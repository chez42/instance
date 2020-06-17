<?php

require_once('libraries/reports/cTransactions.php');
require_once("libraries/reports/Portfolios.php");

global $adb;
$id = $_REQUEST['accountid'];
if(!$id)
    $id = $_REQUEST['record'];
$acct = $_REQUEST['acct_number'];
$accountname = getAccountName($id);
if(!$accountname)
    $accountname = getContactName ($id);
$ids = GetPortfolioIDsFromHHID($id);
$pids = SeparateArrayWithCommasAndSingleQuotes($ids);

class cPholdingsInfo
{   
    /**
     * Calculate the grand totals for all accounts in the temporary table, or just the specified account number
     * @param type $account_number
     */
    public function CalculateGrandTotals($account_number=null)
    {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        if($account_number)
            $where = " WHERE account_number = ? ";
        
        $query = "SELECT code_description, SUM(latest_value) AS grand_total
                  FROM t_summary_table_{$current_user->get('id')}
                  {$where}";
        if($where)
            $grand_total_result = $adb->pquery($query, array($account_number));
        else
            $grand_total_result = $adb->pquery($query, array());
        
        $grand_total = $adb->query_result($grand_total_result, 0, "grand_total");

        $query = "SELECT SUM(a.latest_value) AS sub_total,
                         SUM(a.cost_basis_adjustment) AS cba,
                         SUM(ugl) AS ugl,
                         SUM(ugl) / SUM(a.cost_basis_adjustment) * 100 AS gl,
                         SUM(a.latest_value) / {$grand_total} * 100 AS weight
                  FROM t_summary_table_{$current_user->get('id')} a
                  {$where}";

        if($where)
            $result = $adb->pquery($query, array($account_number));
        else
            $result = $adb->pquery($query, array());

        if($result)
        foreach($result AS $k => $v)
        {
            $gt['value'] = $v['sub_total'];
            $gt['cba'] = $v['cba'];
            $gt['ugl'] = $v['ugl'];
            $gt['gl'] = $v['gl'];
            $gt['weight'] = $v['weight'];
        }
        return $gt;
    }
    
    /**
     * Calculate the shorts values, returning their current value (total) and their total cba (total_cba)
     * @global type $adb
     * @param type $account_numbers
     * @return type
     */
    public static function CalculateShorts($account_numbers=null){
        global $adb;
        $questions = generateQuestionMarks($account_numbers);
        $current_user = Users_Record_Model::getCurrentUserModel();
        
        $query = "SELECT SUM(latest_value) AS total_short_value,
                         SUM(cost_basis_adjustment) AS total_short_cost_basis_adjustment,
                         (SUM(cost_basis_adjustment) - SUM(latest_value)) AS short_difference
                         FROM t_summary_table_{$current_user->get('id')}
                         WHERE account_number IN ({$questions})
                         AND activity_id = 80";
        $result = $adb->pquery($query, array($account_numbers));
        $shorts = array();
        
        if($adb->num_rows($result) > 0){
            $shorts['total'] = $adb->query_result($result, 0, 'total_short_value');
            $shorts['total_cba'] = $adb->query_result($result, 0, 'total_short_cost_basis_adjustment');
            $shorts['difference'] = $adb->query_result($result, 0, 'short_difference');
        }
        
        return $shorts;
    }
    
    /**
     * Calculate the sub totals for all accounts in the temporary table, or just the specified account number
     * @param type $account_number
     */
    public function CalculateSubTotals($account_number=null)
    {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        
        if($account_number)
            $where = " WHERE account_number = ? ";
        
        $query = "SELECT code_description, SUM(latest_value) AS grand_total
                  FROM t_summary_table_{$current_user->get('id')}
                  {$where}";
        if($where)
            $grand_total_result = $adb->pquery($query, array($account_number));
        else
            $grand_total_result = $adb->pquery($query, array());
        
        $grand_total = $adb->query_result($grand_total_result, 0, "grand_total");

        $query = "SELECT code_description, SUM(a.latest_value) AS sub_total,
                            SUM(a.cost_basis_adjustment) AS cba,
                            SUM(ugl) AS ugl,
                            SUM(gl) AS gl,
                            SUM(a.latest_value) / {$grand_total} * 100 AS weight
                  FROM t_summary_table_{$current_user->get('id')} a
                  {$where}
                  GROUP BY code_description";//Subtotals
        if($where)
            $subtotal_result = $adb->pquery($query, array($account_number));
        else
            $subtotal_result = $adb->pquery($query, array());

            
       /* ====  START : Felipe 2016-07-25 MyChanges ===== */
       
       $sub = array();
       
       // foreach($subtotal_result AS $k => $v)
       if($adb->num_rows($subtotal_result)){
	       while($v = $adb->fetchByAssoc($subtotal_result))
	        {            
	            $sub[$v['code_description']]['sub_total']['value'] = $v['sub_total'];
	            $sub[$v['code_description']]['sub_total']['cba'] = $v['cba'];
	            $sub[$v['code_description']]['sub_total']['ugl'] = $v['ugl'];
	            $sub[$v['code_description']]['sub_total']['gl'] = $v['gl'];
	            $sub[$v['code_description']]['sub_total']['weight'] = $v['weight'];
	        }
       }
            
       /* ====  END : Felipe 2016-07-25 MyChanges ===== */
       
       return $sub;
    }
    /**
     * Returns all holdings without categories
     * @global type $adb
     * @param type $account_number
     * @return type
     */
    public function GetSecurities($account_number)
    {
        $current_user = Users_Record_Model::getCurrentUserModel();
        $direction = $_SESSION['direction'];
        if(!$direction)
        {
            $direction = "DESC";
            $_SESSION['direction'] = "DESC";

        }
        $reverse = $_REQUEST['reverse'];

        if($reverse == "1")
        {
            if($direction == "ASC")
            {
                $direction = "DESC";
                $_SESSION['direction'] = "DESC";
            }
            else
            {
                $direction = "ASC";
                $_SESSION['direction'] = "ASC";
            }
        }

        $orderby = $_REQUEST["order_by"];
        if(!$orderby)
            $orderby = "security_symbol";
        
        global $adb;
        if($account_number)
            $filter = " AND account_number = ? ";
        else
            $filter = "";
        $query = "DROP FUNCTION IF EXISTS GET_TOTAL";
        $adb->pquery($query, array());

        $query = "SELECT code_description, SUM(latest_value) AS grand_total
                  FROM t_summary_table_{$current_user->get('id')}";
        $grand_total_result = $adb->pquery($query, array());
        $grand_total = $adb->query_result($grand_total_result, 0, "grand_total");
        
        $query = "SELECT a.*, SUM(a.latest_value) AS total_value,
                            SUM(ugl) AS ugl,
                            SUM(gl) AS gl,
                            a.latest_value / ? * 100 AS weight
                            FROM t_summary_table_{$current_user->get('id')} a
                            WHERE quantity != 0
                            {$filter}
                            GROUP BY a.account_number, a.security_type_id, a.symbol_id
                            ORDER BY {$orderby} {$direction}";
        if($account_number)
            $security_result = $adb->pquery($query, array($grand_total, $account_number));
        else
            $security_result = $adb->pquery($query, array($grand_total));        

        return $security_result;
    }
    
    /**
     * Calculate the main categories based on the passed in code description and account number
     * @global type $adb
     * @param type $code_description
     * @param type $account_number
     * @return type
     */
    public function CalculateMainCategory($code_description, $account_number=null){
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        
        $where = " WHERE code_description = ? ";
        if($account_number)
            $where .= " AND account_number = ? ";

        $query = "SELECT code_description, SUM(a.latest_value) AS sub_total,
                        SUM(a.cost_basis_adjustment) AS cba,
                        SUM(ugl) AS ugl,
                        SUM(ugl) / SUM(a.cost_basis_adjustment) * 100 AS gl,
                        SUM(a.latest_value) / (SELECT SUM(latest_value) AS grand_total
                                              FROM t_summary_table_{$current_user->get('id')}) * 100 AS weight
                  FROM t_summary_table_{$current_user->get('id')} a
                  {$where}
                  GROUP BY code_description";
        if($account_number)
            $category_result = $adb->pquery($query, array($code_description, $account_number));
        else
            $category_result = $adb->pquery($query, array($code_description));
        
        $cat = array();
        
             
       	/* ====  START : Felipe 2016-07-25 MyChanges ===== */
       
        if($adb->num_rows($category_result) > 0){
        	while($v = $adb->fetchByAssoc($category_result)){
        		$cat[$code_description] = $v;
        	}
        }
	     
       /* ====  END : Felipe 2016-07-25 MyChanges ===== */
       
        return $cat;
    }

    public function CalculateSubSubCategories($account_number=null){
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        
        if($account_number)
            $where .= " WHERE account_number = ? ";

        $query = "SELECT code_description, sub_sub_category, SUM(a.latest_value) AS sub_total,
                        SUM(a.cost_basis_adjustment) AS cba,
                        SUM(ugl) AS ugl,
                        SUM(ugl) / SUM(a.cost_basis_adjustment) * 100 AS gl,
                        SUM(a.latest_value) / (SELECT SUM(latest_value) AS grand_total
                                              FROM t_summary_table_{$current_user->get('id')}) * 100 AS weight
                  FROM t_summary_table_{$current_user->get('id')} a
                  {$where}
                  GROUP BY sub_sub_category";
        if($account_number)
            $category_result = $adb->pquery($query, array($account_number));
        else
            $category_result = $adb->pquery($query, array());
        
        $cat = array();
        
    	     
       	/* ====  START : Felipe 2016-07-25 MyChanges ===== */
       
        if($adb->num_rows($category_result) > 0){
        	while($v = $adb->fetchByAssoc($category_result)){
        		$cat[$v['sub_sub_category']] = $v;
        	}
        }
	     
       	/* ====  END : Felipe 2016-07-25 MyChanges ===== */
       
        return $cat;
    }

    /**
     * Returns a categorized version of the holdings
     * @global type $adb
     * @param type $account_number
     * @return type
     */
    public function GetCategories($account_number=null)
    {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        if($account_number)
            $filter = " AND account_number = ? ";
        else
            $filter = "";
        $query = "DROP FUNCTION IF EXISTS GET_TOTAL";
        $adb->pquery($query, array());

        $query = "SELECT code_description, SUM(latest_value) AS grand_total
                  FROM t_summary_table_{$current_user->get('id')}";
        $grand_total_result = $adb->pquery($query, array());
        $grand_total = $adb->query_result($grand_total_result, 0, "grand_total");

        $query = "SELECT a.*, SUM(a.cost_basis_adjustment) AS cost_basis_adjustment, 
                            SUM(a.latest_value) AS total_value,
                            SUM(ugl) AS ugl,
                            SUM(gl) AS gl,
                            a.latest_value / ? * 100 AS weight
                            FROM t_summary_table_{$current_user->get('id')} a
                            WHERE quantity != 0
                            {$filter}
                            GROUP BY a.account_number, a.security_type_id, a.symbol_id
                            ORDER BY code_description ASC";
        if($account_number)
            $category_result = $adb->pquery($query, array($grand_total, $account_number));
        else
            $category_result = $adb->pquery($query, array($grand_total));
        
        $categories = array();

     
       /* ====  START : Felipe 2016-07-25 MyChanges ===== */
       
        //if($category_result)
        
        if($adb->num_rows($category_result)){
        	
        	while($category = $adb->fetchByAssoc($category_result)){
        		$categories[$category['code_description']][$category['sub_sub_category']][] = $category;
	        	
        	}
	        
	        $subtotals = $this->CalculateSubTotals($account_number);
	        
	        if(!empty($subtotals)){
		        foreach($subtotals AS $k => $v){
		            $categories[$k]['sub_total'] = $v;
		        }
	        }
        }
     
       /* ====  END : Felipe 2016-07-25 MyChanges ===== */
       
        return $categories;
    }
    
    /**
     * Determine the sub-sub Category by the Security id
     * @param type $security_id
     */
    public function DetermineSubSubCategoryById($security_id){
        global $adb;
        $query = "SELECT csub.code_description AS sub_sub_category
                  FROM vtiger_pc_codes csub 
                  JOIN vtiger_securities s ON s.security_id = ?
                  WHERE csub.code_id = 
                     (SELECT code_id FROM vtiger_pc_security_codes WHERE security_id = s.security_id AND code_type_id = 10)
                  AND s.security_data_set_id IN(1, 27)
                  LIMIT 1";
        $result = $adb->pquery($query, array($security_id));
        if($adb->num_rows($result) > 0){
            $sub_sub_category = $adb->query_result($result, 0, "sub_sub_category");
            return $sub_sub_category;
        }
        else
            return "undefined";
    }
    
    /**
     * Determine the sub-sub category by Security Symbol
     * @param type $security_symbol
     */
    public function DetermineSubSubCategoryBySymbol($security_symbol){
        global $adb;
        $query = "SELECT csub.code_description AS sub_sub_category
                  FROM vtiger_pc_codes csub 
                  JOIN vtiger_securities s ON s.security_symbol = ?
                  WHERE csub.code_id = 
                     (SELECT code_id FROM vtiger_pc_security_codes WHERE security_id = s.security_id AND code_type_id = 10)
                  AND s.security_data_set_id IN(1, 27)
                  LIMIT 1";
        $result = $adb->pquery($query, array($security_symbol));
        if($adb->num_rows($result) > 0){
            $sub_sub_category = $adb->query_result($result, 0, "sub_sub_category");
            return $sub_sub_category;
        }
        else
            return "undefined";
    }
}

?>

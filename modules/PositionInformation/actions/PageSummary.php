<?php
require_once("include/utils/omniscientCustom.php");

class PositionInformation_PageSummary_Action extends Vtiger_Action_Model{

    public function GetSummaryFromSymbols($symbols){
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $db = PearDatabase::getInstance();
        global $current_user;
        
        if($currentUserModel->isAdminUser()){
            $symbol_questions = generateQuestionMarks($symbols);

            $query = "SELECT vpi.security_symbol, vpi.description, SUM(current_value) AS total_value, 
			SUM(quantity) AS total_quantity, vtiger_modsecurities.security_price AS last_price
            FROM vtiger_positioninformation vpi
			JOIN vtiger_crmentity e ON e.crmid = vpi.positioninformationid 
			LEFT JOIN vtiger_modsecurities ON vtiger_modsecurities.security_symbol = vpi.security_symbol
			WHERE vpi.security_symbol IN ({$symbol_questions}) AND e.deleted = 0 GROUP BY vpi.security_symbol";
            $result = $db->pquery($query, array($symbols), true);
        }
        else
        {
            require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

            foreach($PositionInformation_share_read_permission['GROUP'] AS $groups => $users){
                foreach($users AS $k => $v)
                    $related_ids[] = $v;
                $related_ids[] = $groups;
            }
            $questions = generateQuestionMarks($related_ids);
            $symbol_questions = generateQuestionMarks($symbols);
            $query = "SELECT vpi.security_symbol, vpi.description, SUM(current_value) AS total_value, 
			SUM(quantity) AS total_quantity, vtiger_modsecurities.security_price AS last_price 
            FROM vtiger_positioninformation vpi
            JOIN vtiger_crmentity e ON e.crmid = vpi.positioninformationid 
			LEFT JOIN vtiger_modsecurities ON vtiger_modsecurities.security_symbol = vpi.security_symbol
			WHERE e.smownerid IN ({$questions}) AND vpi.security_symbol IN ({$symbol_questions}) AND e.deleted = 0 
			GROUP BY vpi.security_symbol";

            $result = $db->pquery($query, array($related_ids, $symbols));
        }
        
        if (is_object($result))
            foreach($result AS $k => $v){  
                $values[$k]['security_symbol'] = $v['security_symbol'];
                $values[$k]['description'] = $v['description'];
                $values[$k]['total_value'] = $v['total_value'];
                $values[$k]['quantity'] = $v['total_quantity'];
                $values[$k]['last_price'] = $v['last_price'];
            }
        return $values;        
    }
}

?>
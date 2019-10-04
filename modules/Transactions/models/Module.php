<?php

class Transactions_Module_Model extends Vtiger_Module_Model {
    public static function GetSecurityIdBySymbol($symbol){
        global $adb;
        $query = "SELECT security_id FROM vtiger_modsecurities WHERE security_symbol = ?";
        $result = $adb->pquery($query, array($symbol));
        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, 'security_id');
        } else
            return 0;
    }

    public static function ConvertPCTransactionsTableToModule(){
        global $adb;
        $query = "SELECT * FROM vtiger_pc_transactions WHERE data_set_id IN (1,28)";
        echo 'here';exit;
        $result = $adb->pquery($query, array());
        foreach($result AS $k => $v){
            print_r($v);exit;
        }
    }
/*
    public static function GetSymbolPriceForDate($symbol, $date){
        global $adb;
        $query = "SELECT price FROM vtiger_custodian_prices WHERE symbol = ? AND trade_date = ?";
        echo "PASSED IN: {$symbol}, {$date}<br />";
        $result = $adb->pquery($query, array($symbol, $date));
        if($adb->num_rows($result) > 0) {
            echo "RETURNING: " . $adb->query_result($result, 0, 'price') . " -- {$symbol}, {$date}<br />";
            return $adb->query_result($result, 0, 'price');
        }
        else {
            echo "RETURNING 0 -- {$tsymbol}, {$date}<br />";
            return 0;
        }
    }
*/
    public static function GetSymbolPriceForDate($symbol, $date){
        global $adb;
        $query = "SELECT price FROM vtiger_pc_security_prices WHERE symbol = ? AND price_date = ? AND data_set_id IN (1,28) LIMIT 1";

#        echo "PASSED IN: {$symbol}, {$date}<br />";
        $result = $adb->pquery($query, array($symbol, $date));
        if($adb->num_rows($result) > 0) {
#            echo "RETURNING: " . $adb->query_result($result, 0, 'price') . " -- {$symbol}, {$date}<br />";
            return $adb->query_result($result, 0, 'price');
        }
        else {
#            echo "RETURNING 0 -- {$symbol}, {$date}<br />";
            return 0;
        }
    }

    private static function CreatePortfolioOwnersTable($field = "account_number"){
        global $adb;

        $query = "DROP TABLE IF EXISTS PortfolioOwners";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE PortfolioOwners
        SELECT p.{$field} AS account_number, e.smownerid AS portfolio_owner
        FROM vtiger_portfolioinformation p
        JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid";

        $adb->pquery($query, array());
    }

    private static function UpdateTransactionOwnersFromOwnerTable(){
        global $adb;

        $query = "UPDATE vtiger_transactions t
        JOIN vtiger_crmentity e ON e.crmid = t.transactionsid
        JOIN PortfolioOwners o ON t.account_number = o.account_number
        SET e.smownerid = o.portfolio_owner
        WHERE e.setype = 'Transactions' AND e.smownerid=1";

        $adb->pquery($query, array());
    }

    public static function AssignOwnerBasedOnAccountNumber(){
        self::CreatePortfolioOwnersTable("account_number");
        self::UpdateTransactionOwnersFromOwnerTable();
        self::CreatePortfolioOwnersTable("dashless");
        self::UpdateTransactionOwnersFromOwnerTable();
    }

    public static function GetPCPriceForDate($symbol, $date){
        global $adb;
        $query = "SELECT price FROM vtiger_pc_security_prices WHERE security_id = (SELECT security_id FROM vtiger_securities WHERE security_symbol = ? AND security_data_set_id IN (1,28) LIMIT 1) AND price_date = ? LIMIT 1";

#        echo "PASSED IN: {$symbol}, {$date}<br />";
        $result = $adb->pquery($query, array($symbol, $date));
        if($adb->num_rows($result) > 0) {
#            echo "RETURNING: " . $adb->query_result($result, 0, 'price') . " -- {$symbol}, {$date}<br />";
            return $adb->query_result($result, 0, 'price');
        }
        else {
#            echo "RETURNING 0 -- {$symbol}, {$date}<br />";
            return 0;
        }
    }
	
	
	function getWidgetTransactions($headerColumns, $pagingModel, $tradeDates, $transaction_activity){
	
		$db = PearDatabase::getInstance();

		$moduleName = $this->getName();
		
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		
		$queryGenerator = new QueryGenerator($moduleName, $currentUserModel);
	
		$headerColumns = array_merge($headerColumns, array("id", "transaction_type"));
		
		$queryGenerator->setFields( $headerColumns );

		$listviewController = new ListViewController($db, $currentUserModel, $queryGenerator);

		$query = $queryGenerator->getQuery();
		$query .= " AND ( ";
		$activityCount = count($transaction_activity);
		$act = 0;
		
		foreach($transaction_activity as $transactionActivity){
		    if($act > 0 && $act < $activityCount)
		        $query .= " OR ";
		    if($transactionActivity == 'Buy' || $transactionActivity == 'Sell'){
		        $query .= " ( vtiger_transactionscf.transaction_type = 'Trade' AND transaction_activity = '".$transactionActivity."' ) ";
		    }else{
		        $query .= " ( vtiger_transactionscf.transaction_type = 'Flow' AND transaction_activity = '".$transactionActivity."' ) ";
		    }
		    $act++;
		}
		$query .= ' ) ';	
		$startDate = (isset($tradeDates['start_date']))?$tradeDates['start_date']:"";
		
		if($startDate)
			$query .= " AND vtiger_transactions.trade_date >= '" . $startDate . "'";
		
		
		$endDate = (isset($tradeDates['end_date']))?$tradeDates['end_date']:"";
		
		if($endDate)
			$query .= " AND vtiger_transactions.trade_date <= '".$endDate."' ";
		
		$yesterdayTransactions = (isset($tradeDates['yesterday_transactions']))?$tradeDates['yesterday_transactions']:"";
		
		if($yesterdayTransactions)
			$query .= " AND vtiger_transactions.trade_date = '".$yesterdayTransactions."' ";
				
		$query = str_replace(" FROM ", ",vtiger_crmentity.crmid as id FROM ", $query);
		
		$query  .= "ORDER BY vtiger_transactionscf.net_amount DESC LIMIT ". $pagingModel->getStartIndex() .", ". ($pagingModel->getPageLimit()+1);
		
		$result = $db->pquery($query, array());
		
		$numOfRows = $db->num_rows($result);

		$moduleFocus= CRMEntity::getInstance($moduleName);

		$entries = $listviewController->getListViewRecords($moduleFocus,$moduleName,$result);

		$pagingModel->calculatePageRange($activities);
		
		if($numOfRows > $pagingModel->getPageLimit()){
			array_pop($entries);
			$pagingModel->set('nextPageExists', true);
		} else {
			$pagingModel->set('nextPageExists', false);
		}
		
		$listviewRecords = array();
		$index = 0;
		foreach ($entries as $id => $record) {
			$rawData = $db->query_result_rowdata($result, $index++);
			$record['id'] = $id;
			$listviewRecords[$id] = $this->getRecordFromArray($record, $rawData);
		}

		return $listviewRecords;
	}
	
	function getWidgetLinkURL($trade_dates, $transaction_activity){
		
		$listSearchParams = array();
		
		$listSearchParams[0][0] = array('transaction_type', 'e', 'Flow');
		$listSearchParams[0][1] = array('transaction_activity', 'e', $transaction_activity);
		$listSearchParams[0][2] = array('trade_date', 'bw', $trade_dates);
			
		$baseModuleListLink = $this->getListViewUrlWithAllFilter();
		$baseModuleListLink = str_replace("&view=List", "&view=GraphFilterList", $baseModuleListLink);
		return $baseModuleListLink.'&search_params='. json_encode($listSearchParams);
	}

	static public function GetGeneratedTransactionID($account_number, $symbol){
        global $adb;

        $query = "SELECT transactionsid 
                  FROM vtiger_transactions t 
                  JOIN vtiger_transactionscf cf USING (transactionsid) 
                  WHERE account_number = ? AND security_symbol = ? AND system_generated = 1";
        $result = $adb->pquery($query, array($account_number, $symbol));
        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, 'transactionsid');
        }
        return 0;
    }

    static public function GetTDAccountsMissingNetAmountsReceiptOfSecurities(){
        global $adb;

        $query = "SELECT account_number 
                  FROM vtiger_transactions t 
                  JOIN vtiger_transactionscf cf USING(transactionsid)
                  WHERE key_mnemonic_description = 'REC'
                  AND (net_amount IS NULL OR net_amount = 0)
                  AND origination = 'TD'
                  GROUP BY account_number";
        $result = $adb->pquery($query, array());
        $account_numbers = array();
        if($adb->num_rows($result) > 0){
            while($x = $adb->fetch_array($result)) {
                $account_numbers[] = $x['account_number'];
            }
            return $account_numbers;
        }
        return 0;
    }
}

?>
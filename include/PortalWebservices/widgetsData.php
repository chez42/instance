<?php
require_once("include/utils/omniscientCustom.php");

$account_numbers = GetAccountNumbersFromRecord($contactid);
	
function vtws_widgetData($element,$user){
    
    global $adb,$site_URL;
    
    $element = json_decode($element,true);
    
    $widgetsPosition = array();
    
    $posSizeQuery = $adb->pquery("SELECT portal_widget_position FROM vtiger_contactdetails 
	WHERE contactid = ?", array($element['ID']));
    
    if($adb->num_rows($posSizeQuery)){
        
        $widgetsPosition = Zend_Json::decode(decode_html($adb->query_result($posSizeQuery, 0, 'portal_widget_position')));
        
        $data['widgetsPosition'] = $widgetsPosition;
    }
	
	$account_numbers = GetAccountNumbersFromRecord($element['ID']);
    
    $balances = PortfolioInformation_HistoricalInformation_Model::GetConsolidatedBalances($account_numbers, '1900-01-01', date("Y-m-d"));
    $data['balances'] = $balances;
    
    $margin_balance = PortfolioInformation_HoldingsReport_Model::GetMarginBalanceTotal($account_numbers);
    $data['margin_balance'] = $margin_balance;
    
	$net_credit_debit = PortfolioInformation_HoldingsReport_Model::GetNetCreditDebitTotal($account_numbers);
    $data['net_credit_debit'] = $net_credit_debit;
    
	$unsettled_cash = PortfolioInformation_HoldingsReport_Model::GetDynamicFieldTotal($account_numbers, "unsettled_cash");
    $data['unsettled_cash'] = $unsettled_cash;
    
	$data['assetclasstables'] = PortfolioInformation_HoldingsReport_Model::GenerateAssetClassTables($account_numbers);
    
	$pie = PortfolioInformation_Reports_Model::GetPieFromTable();
    $data['pie'] = $pie;
    
    return $data;

}


function GetAccountNumbersFromRecord($crmid){
    global $adb;
    $query = "SELECT 
                CASE (SELECT setype FROM vtiger_crmentity WHERE crmid=?)
                        WHEN 'Contacts' THEN (SELECT ssn FROM vtiger_contactscf WHERE contactid = ?)
                        WHEN 'PortfolioInformation' THEN (SELECT tax_id FROM vtiger_portfolioinformationcf WHERE portfolioinformationid = ?)
                        WHEN 'Accounts' THEN (SELECT GROUP_CONCAT(ssn) FROM vtiger_contactscf WHERE contactid IN 
                                                                        (SELECT contactid FROM vtiger_contactdetails WHERE accountid=?))
                        END AS ssn";
    $result = $adb->pquery($query, array($crmid, $crmid, $crmid, $crmid));
    if($adb->num_rows($result) > 0){
        $ssn_result = $adb->query_result($result, 0, "ssn");
        $ssn = explode(",", $ssn_result);
        $questions = generateQuestionMarks($ssn);//The original query
        $query = "SELECT account_number FROM vtiger_portfolioinformation p
        JOIN vtiger_portfolioinformationcf cf ON (p.portfolioinformationid = cf.portfolioinformationid)
        WHERE tax_id IN ({$questions}) AND tax_id != '' AND accountclosed = 0";
#        $query = "SELECT portfolio_account_number FROM vtiger_portfolios WHERE portfolio_tax_id IN ({$questions}) AND portfolio_tax_id != '' AND account_closed = 0";
        $res = $adb->pquery($query, array($ssn));
        if($adb->num_rows($res) > 0){
            foreach($res AS $k => $v){
                $accounts[] = $v['account_number'];
            }
        }

        $extra = GetPortfolioAccountNumbersFromSSN($ssn);
		if(is_array($extra))
			$accounts = array_merge($accounts, $extra);
    }

    $query = "SELECT account_number 
              FROM vtiger_portfolioinformation p
              JOIN vtiger_portfolioinformationcf cf ON (p.portfolioinformationid = cf.portfolioinformationid)
              JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
              WHERE contact_link = ? AND p.accountclosed = 0 AND e.deleted = 0";
    $result = $adb->pquery($query, array($crmid));
    if($adb->num_rows($result) > 0){
        while($v = $adb->fetch_array($result))
            $accounts[] = $v['account_number'];
    }

    $focus = CRMEntity::getInstance('Accounts');
    $entityIds = $focus->getRelatedContactsIds($crmid);

    $account_numbers_household = GetPortfolioAccountNumbersFromContactID($entityIds);
    if(!is_array($accounts))
        $accounts = array();
    if(is_array($account_numbers_household))
        $accounts = array_merge($accounts, $account_numbers_household);

    return $accounts;
}
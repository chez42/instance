<?php
require_once("include/utils/omniscientCustom.php");

$account_numbers = GetAccountNumbersFromRecord($contactid);
	
function vtws_widgetData($element,$user){
    
    global $adb,$site_URL;
    
    $widgetsPosition = array();
    
    $posSizeQuery = $adb->pquery("SELECT portal_widget_position FROM vtiger_contactdetails 
	WHERE contactid = ?", array($element['ID']));
    
    if($adb->num_rows($posSizeQuery)){
        
        $widgetsPosition = Zend_Json::decode(decode_html($adb->query_result($posSizeQuery, 0, 'portal_widget_position')));
        
        $data['widgetsPosition'] = $widgetsPosition;
    }
	
    $ticketData = getTicketData($element['ID']);
    $data['ticketWidget'] = $ticketData;
    
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

function getTicketData($contactId){
    global $adb;
    
    $moduleModel = Vtiger_Module_Model::getInstance('HelpDesk');
    
    $statusQuery = $adb->pquery("SELECT vtiger_troubletickets.status, count(*) as statuscount FROM vtiger_troubletickets 
    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_troubletickets.ticketid
    WHERE vtiger_crmentity.deleted =0 AND vtiger_troubletickets.parent_id = ? 
    GROUP BY vtiger_troubletickets.status",array($contactId));
    $statusData = array();
    if($adb->num_rows($statusQuery)){
        $statusResult = array();
        for($s=0;$s<$adb->num_rows($statusQuery);$s++){
            $statusResult[$adb->query_result($statusQuery, $s, 'status')] = $adb->query_result($statusQuery, $s, 'statuscount');
//             if(!$adb->query_result($statusQuery, $s, 'status')){
//                 $tmp['title'] = '';
//                 $tmp['value'] = $adb->query_result($statusQuery, $s, 'statuscount');
//                 $statusData[] = $tmp;
//             }
        }
        $statusField = Vtiger_Field_Model::getInstance('ticketstatus',$moduleModel);
        $statusPickList = $statusField->getPicklistValues();
        
        foreach($statusPickList as $statusValue){
            //$statusData[$statusValue] = $statusResult[$statusValue] ? $statusResult[$statusValue]:0;
            if( $statusValue != '----------'){
                $tmp['title'] = $statusValue;
                $tmp['value'] = $statusResult[$statusValue] ? $statusResult[$statusValue]:0;
                $tmp['url'] = 'helpdesk.php?status='.$statusValue;
                $statusData[] = $tmp;
            }
        }
        
    }
   
    $timeQuery = $adb->pquery("SELECT vtiger_troubletickets.total_time_spent, count(*) as ticketcount FROM vtiger_troubletickets 
    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_troubletickets.ticketid
    WHERE vtiger_crmentity.deleted =0 AND vtiger_troubletickets.parent_id = ?
    GROUP BY vtiger_troubletickets.total_time_spent",array($contactId));
    $timeData = array();
    if($adb->num_rows($timeQuery)){
        $timeResult = array();
        for($s=0;$s<$adb->num_rows($timeQuery);$s++){
            $timeResult[$adb->query_result($timeQuery, $s, 'total_time_spent')] = $adb->query_result($timeQuery, $s, 'ticketcount');
            
        }
        foreach($timeResult as $time=>$count){
           $hour = date('H:i',strtotime($time));
           if($hour < '01:00' && $hour >= '00:00'){
               $timeData['<1hrs'] += $count;
           }elseif($hour < '02:00' && $hour >= '01:00'){
               $timeData['<2hrs'] += $count;
           }elseif($hour < '03:00' && $hour >= '02:00'){
               $timeData['<3hrs'] += $count;
           }elseif($hour < '04:00' && $hour >= '03:00'){
               $timeData['<4hrs'] += $count;
           }elseif($hour < '05:00' && $hour >= '04:00'){
               $timeData['<5hrs'] += $count;
           }elseif($hour >= '05:00'){
               $timeData['>5hrs'] += $count;
               
           }
        }
        $finalData = array();
        foreach($timeData as $timeKey => $timeVal){
            $tmp['title'] = $timeKey;
            $tmp['value'] = $timeVal;
            $finalData[] = $tmp;
        }
        
    }
    
    $catQuery = $adb->pquery("SELECT vtiger_troubletickets.category, count(*) as ticketcount FROM vtiger_troubletickets 
    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_troubletickets.ticketid
    WHERE vtiger_crmentity.deleted =0 AND vtiger_troubletickets.parent_id = ?
    GROUP BY vtiger_troubletickets.category",array($contactId));
    $catData = array();
    if($adb->num_rows($catQuery)){
        $catResult = array();
        for($s=0;$s<$adb->num_rows($catQuery);$s++){
            $catResult[$adb->query_result($catQuery, $s, 'category')] = $adb->query_result($catQuery, $s, 'ticketcount');
        }
        $catField = Vtiger_Field_Model::getInstance('ticketcategories',$moduleModel);
        $catPickList = $catField->getPicklistValues();
        
        foreach($catPickList as $catValue){
            if($catResult[$catValue] > 0){
                $tmp['title'] = $catValue;
                $tmp['value'] = $catResult[$catValue] ? $catResult[$catValue]:0;
                $tmp['url'] = 'helpdesk.php?category='.$catValue;
                $catData[] = $tmp;
            }
        }
        
    }
    
    
    return array('ticketStatus'=>$statusData, 'timeResult'=>$finalData, 'catData'=>$catData);
}


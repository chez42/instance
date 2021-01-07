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
    
    $recDocs = getRecentDocuments($element['ID']);
    $data['recentWidget'] = $recDocs;
    
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
    
    $permission_result = $adb->pquery("SELECT * FROM `vtiger_contact_portal_permissions` inner join
	vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_contact_portal_permissions.crmid
	where crmid = ?", array($contactId));
    
    $ticket_across_org = 0;
    
    $contact_ids = array();
    
    $contact_ids[] = $contactId;
    
    if($adb->num_rows($permission_result)){
        $ticket_across_org = $adb->query_result($permission_result, 0, "tickets_record_across_org");
        $account_id = $adb->query_result($permission_result, 0, "accountid");
        if($account_id && $ticket_across_org){
            $contact_ids[] = $account_id;
            $contact_result = $adb->pquery("SELECT * FROM `vtiger_contactdetails`
			inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.contactid
			where accountid = ? and deleted = 0", array($account_id));
            for($i = 0; $i < $adb->num_rows($contact_result); $i++){
                $contact_ids[] = $adb->query_result($contact_result, $i, "contactid");
            }
        }
    }
    
    $moduleModel = Vtiger_Module_Model::getInstance('HelpDesk');
    
    $statusQuery = $adb->pquery("SELECT vtiger_troubletickets.status, count(*) as statuscount FROM vtiger_troubletickets
    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_troubletickets.ticketid
    WHERE vtiger_crmentity.deleted =0 AND vtiger_troubletickets.parent_id IN ( ". generateQuestionMarks($contact_ids) ." )
    GROUP BY vtiger_troubletickets.status",array($contact_ids));
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
    WHERE vtiger_crmentity.deleted =0 AND vtiger_troubletickets.parent_id IN ( ". generateQuestionMarks($contact_ids) ." )
    GROUP BY vtiger_troubletickets.total_time_spent",array($contact_ids));
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
            $tmp['url'] = 'helpdesk.php?tickettime='.$timeKey;
            $finalData[] = $tmp;
        }
        
    }
    
    $catQuery = $adb->pquery("SELECT vtiger_troubletickets.category, count(*) as ticketcount FROM vtiger_troubletickets
    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_troubletickets.ticketid
    WHERE vtiger_crmentity.deleted =0 AND vtiger_troubletickets.parent_id IN ( ". generateQuestionMarks($contact_ids) ." )
    GROUP BY vtiger_troubletickets.category",array($contact_ids));
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

function getRecentDocuments($conId){
    
    global $adb;
    $end = date('Y-m-d');
    $start = date('Y-m-d', strtotime('-7 days'));
    
    $doc = $adb->pquery("SELECT  * FROM vtiger_notes
    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_notes.notesid
    INNER JOIN vtiger_senotesrel ON vtiger_senotesrel.notesid = vtiger_notes.notesid
    INNER JOIN vtiger_documentfolder ON vtiger_documentfolder.documentfolderid = vtiger_notes.doc_folder_id
    LEFT JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.crmid = vtiger_notes.notesid
    WHERE vtiger_crmentity.deleted = 0 AND vtiger_crmentity.createdtime BETWEEN ? AND ?
    AND vtiger_senotesrel.crmid = ? ORDER BY vtiger_crmentity.crmid DESC",
        array($start, $end, $conId));
    $recDocument = array();
    if($adb->num_rows($doc)){
        for($i=0;$i<$adb->num_rows($doc);$i++){
            $documents = $adb->query_result_rowdata($doc, $i);
            $recDocument[] = array(
                'filename' => $documents['filename'],
                'foldername'=> $documents['folder_name'],
                'docid' => $documents['notesid'],
                'attid' => $documents['attachmentsid']
            );
        }
    }
    return $recDocument;
}

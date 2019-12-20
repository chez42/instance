<?php
require_once("include/utils/omniscientCustom.php");
require_once('vtlib/Vtiger/Module.php');

function vtws_portalfunctions($element,$user){
    global $adb;
 
    $value = $element['function_name']($element['input_array']);
    
    return $value;
}

function getReportTypesData(){
    
    return array(
        'holdings'		=> array('function_name' => 'LoadHoldingsReport', 'filepath' => "modules/Reports/HoldingsReport.php"),
        'income' 		=> array('function_name' => 'LoadMonthlyIncomeReport', 'filepath' => "modules/Reports/MonthlyIncomeReport.php"),
        'incomelastyear'=> array('function_name' => 'LoadIncomeLastYearReport', 'filepath' => "modules/Reports/IncomeLastYearReport.php"),
        'overview' 		=> array('function_name' => 'LoadOmniOverviewReport', 'filepath' => "modules/Reports/OmniOverviewReport.php"),
        'omniprojected' => array('function_name' => 'LoadOmniProjectedReport', 'filepath' => "modules/Reports/OmniProjected.php"),
        'ghreport' 		=> array('function_name' => 'LoadGHReport', 'filepath' => "modules/Reports/GHReport.php"),
        'gh2report' 	=> array('function_name' => 'LoadGH2Report', 'filepath' => "modules/Reports/GH2Report.php"),
        'monthovermonth'=> array('function_name' => 'LoadMonthOverMonth', 'filepath' => "modules/Reports/MonthOverMonth.php"),
        'omniincome' 	=> array('function_name' => 'LoadOmniIncome', 'filepath' => "modules/Reports/OmniIncome.php"),
        'assetclassreport'=> array('function_name' => 'LoadAssetClassReport', 'filepath' => "modules/Reports/AssetClassReport.php"),
        'gainloss'      => array('function_name' => 'LoadGainLoss', 'filepath' => "modules/Reports/GainLoss.php"),
        'omniintervals' => array('function_name' => 'LoadOmniIntervals', 'filepath' => "modules/Reports/OmniIntervals.php"),
        'omniintervalsdaily' => array('function_name' => 'LoadOmniIntervalsDaily', 'filepath' => "modules/Reports/OmniIntervalsDaily.php"),
    );
}

function get_reports($input_array){
    
    $contactid = $input_array['contactid'];
    
    global $adb,$log;
    
    $contactid = (int) vtlib_purify($contactid);
    
    $accountid = ($input_array['accountid'])?$input_array['accountid']:0;
    
    if(!$accountid){
        $account_result = $adb->pquery("select accountid from vtiger_contactdetails where contactid = ?",array($contactid));
        if($adb->num_rows($account_result))
            $accountid = $adb->query_result($account_result, "0","accountid");
    }
    
    require_once("libraries/reports/new/nExpense.php");
    
    $show_report = $input_array['show_report'];
    
    $account_numbers = $sanitizedSSN = array();
    
    if($accountid && $show_report == "Accounts"){
        
        $account_numbers = getContactAccessibleAccounts($accountid);
        
    } else {
        
        $account_numbers = getContactAccessibleAccounts($contactid);
        
    }
    
    $totals = $summary_info = $reportData = array();
    
    $pageLimit = ($input_array['page_limit']) ? $input_array['page_limit'] : 10;
    
    $tableColumnOrder = array(
        "pos_0" => "vtiger_portfolioinformation.account_number",
        "pos_1" => "vtiger_portfolioinformation.contact_link",
        "pos_2" => "vtiger_portfolioinformation.account_type",
        "pos_3" => "vtiger_portfolioinformation.total_value",
        "pos_4" => "vtiger_portfolioinformationcf.securities",
        "pos_5" => "vtiger_portfolioinformationcf.cash",
        "pos_6" => "vtiger_pc_account_custom.nickname"
    );
    
    $order = $input_array['order'];
    $orderBy = $input_array['orderBy'];
    
    $order_by = "";
    
    if(isset($tableColumnOrder['pos_'.$order]))
        $order_by = "ORDER By ".$tableColumnOrder['pos_'.$order]." ".$orderBy;
        
        if(!empty($account_numbers)) {
            
        $query = "SELECT *, vtiger_portfolioinformation.account_number as account_number FROM vtiger_portfolioinformation INNER JOIN vtiger_portfolioinformationcf ON
		vtiger_portfolioinformationcf.portfolioinformationid = vtiger_portfolioinformation.portfolioinformationid
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_portfolioinformation.portfolioinformationid
		LEFT JOIN vtiger_pc_account_custom on vtiger_pc_account_custom.account_number = vtiger_portfolioinformation.account_number
		WHERE vtiger_portfolioinformation.account_number IN (".generateQuestionMarks($account_numbers).") AND vtiger_crmentity.deleted = 0
		AND (vtiger_portfolioinformation.accountclosed = 0 OR vtiger_portfolioinformation.accountclosed IS NULL)
		GROUP BY vtiger_portfolioinformation.account_number $order_by  ";
            
            if($input_array['page_limit'])
                $query.=" LIMIT ".$pageLimit;
                
                $result = $adb->pquery($query, array($account_numbers));
                
                if($adb->num_rows($result) > 0){
                    while($v = $adb->fetchByAssoc($result)){
                        
                        $v['record'] = $v['crmid'];
                        
                        $tmp = new nExpense($v['account_number']);
                        
                        $v['management_fee'] = abs($tmp->CalculateAmount('DATE_SUB(NOW(),INTERVAL 1 YEAR)', 'NOW()'));
                        
                        if(isset($v['contact_link']) && $v['contact_link'] > 0)
                            $v['contact_link'] = getContactName($v['contact_link']);
                            
                            $summary_info[] = $v;
                            
                            $totals["total_value"] += $v['total_value'];
                            $totals["market_value"] += $v['securities'];
                            $totals["cash_value"] += $v['cash'];
                            $totals['management_fee'] += $v['management_fee'];
                    }
                }
                
                // Total Records
                $query = "SELECT count(vtiger_portfolioinformation.account_number) as total_portfolios FROM vtiger_portfolioinformation
		INNER JOIN vtiger_portfolioinformationcf ON vtiger_portfolioinformation.portfolioinformationid = vtiger_portfolioinformationcf.portfolioinformationid
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_portfolioinformation.portfolioinformationid
		WHERE vtiger_portfolioinformation.account_number IN (".generateQuestionMarks($account_numbers).") AND vtiger_crmentity.deleted = 0
		AND (vtiger_portfolioinformation.accountclosed = 0 OR vtiger_portfolioinformation.accountclosed IS NULL)";
                
                $result = $adb->pquery($query,array($account_numbers));
                
                if($adb->num_rows($result))
                    $reportData["recordsTotal"] = $adb->query_result($result,0,'total_portfolios');
        }
        
        if(!empty($totals)){
            $totals['total_value'] = number_format($totals['total_value'], "2");
            $totals['market_value'] = number_format($totals['market_value'], "2");
            $totals['cash_value'] = number_format($totals['cash_value'], "2");
        }
        
        $reportData["grandTotals"] = $totals;
        $reportData["summary_info"] = $summary_info;
        
        $dataReports = $reportData;
        
        return $dataReports;
}

function getAccountsRelatedContactsSSN($accountid){
    
    $adb = PearDatabase::getInstance();
    
    $ssn = array();
    
    $query = "SELECT vtiger_contactscf.ssn AS ssn, REPLACE(ssn, '-', '') AS sanitized_ssn FROM vtiger_contactdetails
    INNER JOIN vtiger_contactscf on vtiger_contactscf.contactid = vtiger_contactdetails.contactid
	INNER JOIN vtiger_crmentity on  vtiger_crmentity.crmid = vtiger_contactdetails.contactid
    WHERE vtiger_crmentity.deleted = 0 AND vtiger_contactdetails.accountid = ? AND
	(vtiger_contactscf.ssn != '' AND vtiger_contactscf.ssn IS NOT NULL)";
    
    $result = $adb->pquery($query, array($accountid));
    
    if($adb->num_rows($result)){
        while($row = $adb->fetchByAssoc($result)){
            $ssn['sanitized_ssn'][] = $row['sanitized_ssn'];
            $ssn['ssn'][] = $row['ssn'];
        }
    }
    return $ssn;
}

function checkModuleActive($module){
    global $adb,$log;
    
    $isactive = false;
    $modules = get_modules(true);
    
    foreach($modules as $key => $value){
        if(strcmp($module,$value) == 0){
            $isactive = true;
            break;
        }
    }
    return $isactive;
}


function get_modules()
{
    global $adb,$log;
    $log->debug("Entering customer portal Function get_modules");
    
    // Check if information is available in cache?
    $modules = array();
    
    $query = $adb->pquery("SELECT vtiger_customerportal_tabs.* FROM vtiger_customerportal_tabs
		INNER JOIN vtiger_tab ON vtiger_tab.tabid = vtiger_customerportal_tabs.tabid
		WHERE vtiger_tab.presence = 0 AND vtiger_customerportal_tabs.visible = 1", array());
    $norows = $adb->num_rows($query);
    if($norows) {
        while($resultrow = $adb->fetch_array($query)) {
            $modules[(int)$resultrow['sequence']] = getTabModuleName($resultrow['tabid']);
        }
        ksort($modules); // Order via SQL might cost us, so handling it ourselves in this case
    }
    
    $log->debug("Exiting customerportal function get_modules");
    return $modules;
}

function getPortalUserid() {
    global $adb,$log;
    $log->debug("Entering customer portal function getPortalUserid");
    
    // Look the value from cache first
    $res = $adb->pquery("SELECT prefvalue FROM vtiger_customerportal_prefs WHERE prefkey = 'userid' AND tabid = 0", array());
    $norows = $adb->num_rows($res);
    if($norows > 0) {
        $userid = $adb->query_result($res,0,'prefvalue');
        // Update the cache information now.
    }
    return $userid;
    $log->debug("Exiting customerportal function getPortalUserid");
}

function show_all_permission($module, $contactid){
    
    global $adb,$log;
    
    $log->debug("Entering customer portal Function show_all_permission");
    
    $tabid = getTabid($module);
    
    if($module=='Tickets'){
        $tabid = getTabid('HelpDesk');
    }
    
    $permissions = get_contact_portal_modules($contactid, true);
    
    if(!empty($permissions) && isset($permissions[$tabid]) && !empty($permissions[$tabid])){
        
        $module_permission = $permissions[$tabid];
        
        if($module_permission['record_across_org'] == '1')
            return 'true';
            else
                return 'false';
    }else {
        return 'false';
    }
    $log->debug("Exiting customerportal function show_all_permission");
    
}

function get_contact_portal_modules($id, $all_info = false){
    
    // Check if information is available in cache?
    
    $selectedModules = array();
    
    $selectedPortalModulesInfo = getSingleFieldValue("vtiger_contact_portal_permissions", "permissions", "crmid", $id);
    
    $selectedPortalModulesInfo = stripslashes(html_entity_decode($selectedPortalModulesInfo));
    
    $selectedPortalModulesInfo = json_decode($selectedPortalModulesInfo, true);
    
    $selectedModules = array();
    
    foreach($selectedPortalModulesInfo as $tabid => $module_permission){
        
        if(isset($module_permission['visible']) && $module_permission['visible'] == '1'){
            
            $moduleName = getTabModuleName($tabid);
            
            $selectedModules[$tabid] = array(
                "module" => $moduleName,
                "edit_record" => ($module_permission['edit_records'])?$module_permission['edit_records']:0,
                "record_across_org" => ($module_permission['record_across_org'])?$module_permission['record_across_org']:0
            );
        }
    }
    
    $reportModuleTabid = getTabid("Reports");
    
    if(isset($selectedPortalModulesInfo[$reportModuleTabid]) && !empty($selectedPortalModulesInfo[$reportModuleTabid])){
        
        $portalReports = $selectedPortalModulesInfo[$reportModuleTabid];
        
        if(isset($portalReports['allowed_reports']) && !empty($portalReports['allowed_reports'])){
            
            foreach($portalReports['allowed_reports'] as $report){
                
                if($report['visible'] == 1){
                    
                    $selectedModules[$reportModuleTabid]['allowed_reports'] = $portalReports['allowed_reports'];
                    
                    break;
                }
            }
        }
    }
    
    if(!$all_info){
        
        $modules = array();
        
        if(!empty($selectedModules)){
            
            foreach($selectedModules as $allowedModule){
                $modules[] = $allowedModule['module'];
            }
        }
        
        return $modules;
    }
    
    return $selectedModules;
}


function get_module_list_values($Basicdata){
    $id = $Basicdata['id'];
    $module = $Basicdata['block'];
    $only_mine = $Basicdata['only_mine'];
    //$page_limit = 10;
    
    checkFileAccessForInclusion('modules/'.$module.'/'.$module.'.php');
    
    require_once('modules/'.$module.'/'.$module.'.php');
    require_once('include/utils/UserInfoUtil.php');
    global $adb,$log,$current_user;
    
    $log->debug("Entering customer portal function get_module_list_values");
    
    $check = checkModuleActive($module, $id);
    
    if($check == false){
        return array("#MODULE INACTIVE#");
    }
    
    
    //To avoid SQL injection we are type casting as well as bound the id variable.
    $id = (int) vtlib_purify($id);
    
    $user = new Users();
    $userid = getPortalUserid();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    
    $focus = new $module();
    $focus->filterInactiveFields($module);
    
    foreach ($focus->list_fields as $fieldlabel => $values){
        foreach($values as $table => $fieldname){
            $fields_list[$fieldlabel] = $fieldname;
        }
    }
    
    $entity_ids_list = array();
    $show_all=show_all_permission($module, $id);//show_all($module);
    
    if($only_mine == 'true' || $show_all == 'false'){
        array_push($entity_ids_list,$id);
    } else {
        
        $contactquery = "SELECT contactid, accountid FROM vtiger_contactdetails " .
            " INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid" .
            " AND vtiger_crmentity.deleted = 0 " .
            " WHERE (accountid = (SELECT accountid FROM vtiger_contactdetails WHERE contactid = ?)  AND accountid != 0) OR contactid = ?";
        $contactres = $adb->pquery($contactquery, array($id,$id));
        $no_of_cont = $adb->num_rows($contactres);
        for($i=0;$i<$no_of_cont;$i++)
        {
            $cont_id = $adb->query_result($contactres,$i,'contactid');
            $acc_id = $adb->query_result($contactres,$i,'accountid');
            if(!in_array($cont_id, $entity_ids_list))
                $entity_ids_list[] = $cont_id;
                if(!in_array($acc_id, $entity_ids_list) && $acc_id != '0')
                    $entity_ids_list[] = $acc_id;
        }
    }
    
    if($module == 'Documents'){
        
        global $current_user;
        
        $moduleName = "DocumentFolder";
        
        $currentUserModel = Users_Record_Model::getInstanceFromPreferenceFile($current_user->id);
        
        $queryGenerator = new QueryGenerator($moduleName, $currentUserModel);
        
        $queryGenerator->setFields( array('folder_name','id', 'parent_id') );
        
        $listviewController = new ListViewController($adb, $currentUserModel, $queryGenerator);
        
        $docquery = $queryGenerator->getQuery();
        
        $docquery .= " AND vtiger_documentfolder.hide_from_portal != 1 ";
        
        $pos = strpos($docquery, "SELECT");
        if ($pos !== false) {
            $docquery = substr_replace($docquery, "SELECT DISTINCT vtiger_documentfolder.documentfolderid, ", $pos, strlen("SELECT"));
        }
        $docFolId ='';
        $docRes = $adb->pquery($docquery);
        if($adb->num_rows($docRes)){
            for($doc=0;$doc<$adb->num_rows($docRes);$doc++){
                $docFolId[] = $adb->query_result($docRes,$doc,'documentfolderid');
            }
        }
        
        $query = "SELECT vtiger_notes.*, vtiger_crmentity.*
        FROM vtiger_notes
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_notes.notesid AND vtiger_crmentity.deleted = 0
        WHERE (vtiger_notes.is_private != 1 OR vtiger_notes.is_private IS NULL)
        AND (vtiger_crmentity.smcreatorid = ".$_SESSION['ownerId']." OR vtiger_crmentity.smownerid = ".$_SESSION['ownerId'].") ";
        
        if(!empty($docFolId))
            $query.=" AND vtiger_notes.doc_folder_id IN (".implode(',',$docFolId).") ";
            
            $fields_list['actions'] = 'module_actions';
            
            $total_records_query = explode("from", strtolower($query));
            
            $total_records_query = "select count(vtiger_notes.notesid) as total_records from ".$total_records_query[1];
    } else if ($module == "Contacts"){
        
        $query = "select vtiger_contactdetails.*,vtiger_crmentity.smownerid from vtiger_contactdetails
		 inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_contactdetails.contactid
		 where vtiger_crmentity.deleted = 0 and contactid IN (".generateQuestionMarks($entity_ids_list).")
		 and (vtiger_contactdetails.portal_visibility IS NULL OR vtiger_contactdetails.portal_visibility = '' OR
		 vtiger_contactdetails.portal_visibility = 'Public')";
        
        $params = array($entity_ids_list);
        
        $total_records_query = explode("from", strtolower($query));
        
        $total_records_query = "select count(vtiger_contactdetails.contactid) as total_records from ".$total_records_query[1];
    }
    
    $total_records_result = $adb->pquery($total_records_query, $params);
    
    $query = $query. " order by vtiger_crmentity.crmid DESC ";
    
    $res = $adb->pquery($query,$params);
    $noofdata = $adb->num_rows($res);
    $columnVisibilityByFieldnameInfo = array();
    
    if($noofdata) {
        foreach($fields_list as $fieldlabel =>$fieldname ) {
            $columnVisibilityByFieldnameInfo[$fieldname] = getColumnVisibilityPermission($current_user->id,$fieldname,$module);
        }
    }
    
    for( $j= 0;$j < $noofdata; $j++){
        $i=0;
        foreach($fields_list as $fieldlabel =>$fieldname){
            $fieldper = $columnVisibilityByFieldnameInfo[$fieldname];
            if($fieldper == '1' && $fieldname != 'entityid' && $fieldname != 'module_actions'){
                continue;
            }
            
            $fieldlabel = getTranslatedString($fieldlabel,$module);
            
            $output[0][$module]['head'][0][$i]['fielddata'] = $fieldlabel;
            $fieldvalue = $adb->query_result($res,$j,$fieldname);
            
            if($module == 'Contacts'){
                if($fieldname == 'lastname' || $fieldname == 'firstname'){
                    $fieldid = $adb->query_result($res,$j,'contactid');
                    $fieldvalue ='<a href="contacts.php?module=Contacts&action=detail&id='.$fieldid.'">'.$fieldvalue.'</a>';
                }
            }
            
            if($module == 'Documents'){
                if($fieldname == 'title'){
                    $fieldid = $adb->query_result($res,$j,'notesid');
                    $fieldvalue = '<a href="documents.php?&module=Documents&action=detail&id='.$fieldid.'">'.$fieldvalue.'</a>';
                }
                if( $fieldname == 'filename'){
                    $fieldid = $adb->query_result($res,$j,'notesid');
                    $filename = $fieldvalue;
                    $folderid = $adb->query_result($res,$j,'folderid');
                    $filename = $adb->query_result($res,$j,'filename');
                    $fileactive = $adb->query_result($res,$j,'filestatus');
                    $filetype = $adb->query_result($res,$j,'filelocationtype');
                    
                    if($fileactive == 1){
                        if($filetype == 'I'){
                            $fieldvalue = '<a href="download.php?downloadfile=true&folderid='.$folderid.'&filename='.$filename.'&module=Documents&id='.$fieldid.'">'.$fieldvalue.'</a>';
                        }
                        elseif($filetype == 'E'){
                            $fieldvalue = '<a target="_blank" href="'.$filename.'">'.$filename.'</a>';
                        }
                    }else{
                        $fieldvalue = $filename;
                    }
                }
                if($fieldname == 'doc_folder_id'){
                    $fieldvalue = $adb->query_result($res,$j,'foldername');
                }
                
                if($fieldname == "module_actions"){
                    $fieldid = $adb->query_result($res,$j,'notesid');
                    $filename = $fieldvalue;
                    $folderid = $adb->query_result($res,$j,'doc_folder_id');
                    $filename = $adb->query_result($res,$j,'filename');
                    $fileactive = $adb->query_result($res,$j,'filestatus');
                    $filetype = $adb->query_result($res,$j,'filelocationtype');
                    
                    if($filetype == 'I'){
                        $fieldvalue = '<a href="download.php?downloadfile=true&folderid='.$folderid.'&filename='.$filename.'&module=Documents&id='.$fieldid.'" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--pill m-btn--air"><span><i class="fa flaticon-download"></i><span>Download</span></span></a>';
                    } else if($filetype == 'E'){
                        $fieldvalue = '<a target="_blank" href="'.$filename.'" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--pill m-btn--air">Open URL</a>';
                    }
                }
            }
            if($fieldname == 'entityid' || $fieldname == 'contactid' || $fieldname == 'accountid' || $fieldname == 'potentialid' || $fieldname == 'account' || $fieldname == 'linktoaccountscontacts') {
                $crmid = $fieldvalue;
                $modulename = getSalesEntityType($crmid);
                if ($crmid != '' && $modulename != '') {
                    $fieldvalues = getEntityName($modulename, array($crmid));
                    if($modulename == 'Contacts')
                        $fieldvalue = '<a href="contacts.php?module=Contacts&action=detail&id='.$crmid.'"*/>'.$fieldvalues[$crmid].'</a>';
                        elseif($modulename == 'Accounts')
                        $fieldvalue = '<a href="accounts.php?module=Accounts&action=detail&id='.$crmid.'">'.$fieldvalues[$crmid].'</a>';
                        elseif($modulename == 'Potentials'){
                            $fieldvalue = $adb->query_result($res,$j,'potentialname');
                        }
                } else {
                    $fieldvalue = '';
                }
            }
            
            if($fieldname == 'smownerid'){
                $fieldvalue = getOwnerName($fieldvalue);
            }
            $output[1][$module]['data'][$j][$i]['fielddata'] = $fieldvalue;
            $i++;
        }
        
        if($adb->num_rows($total_records_result))
            $output[2][$module]['totalRecords'] = $adb->query_result($total_records_result,0, 'total_records');
    }
    $log->debug("Exiting customer portal function get_module_list_values");
    
    return $output;
}


function get_filecontent_detail($data)
{
    $id = $data['id'];
    $folderid = $data['folderid'];
    $module = $data['block'];
    $customerid= $data['contactid'];
    
    global $adb,$log;
    global $site_URL;
    $log->debug("Entering customer portal function get_filecontent_detail ");
    $isPermitted = check_permission($customerid,$module,$id);
    if($isPermitted == false) {
        return array("#NOT AUTHORIZED#");
    }
    
    if($module == 'Documents')
    {
        $query="SELECT filetype FROM vtiger_notes WHERE notesid =?";
        $res = $adb->pquery($query, array($id));
        $filetype = $adb->query_result($res, 0, "filetype");
        updateDownloadCount($id);
        
        $fileidQuery = 'select attachmentsid from vtiger_seattachmentsrel where crmid = ?';
        $fileres = $adb->pquery($fileidQuery,array($id));
        $fileid = $adb->query_result($fileres,0,'attachmentsid');
        
        $filepathQuery = 'select path,name from vtiger_attachments where attachmentsid = ?';
        $fileres = $adb->pquery($filepathQuery,array($fileid));
        $filepath = $adb->query_result($fileres,0,'path');
        $filename = $adb->query_result($fileres,0,'name');
        $filename= decode_html($filename);
        
        $saved_filename =  $fileid."_".$filename;
        $filenamewithpath = $filepath.$saved_filename;
        $filesize = filesize($filenamewithpath );
        
    }
    else
    {
        $query ='select vtiger_attachments.*,vtiger_seattachmentsrel.* from vtiger_attachments inner join vtiger_seattachmentsrel on vtiger_seattachmentsrel.attachmentsid=vtiger_attachments.attachmentsid where vtiger_seattachmentsrel.crmid =?';
        
        $res = $adb->pquery($query, array($id));
        
        $filename = $adb->query_result($res,0,'name');
        $filename = decode_html($filename);
        $filepath = $adb->query_result($res,0,'path');
        $fileid = $adb->query_result($res,0,'attachmentsid');
        $filesize = filesize($filepath.$fileid."_".$filename);
        $filetype = $adb->query_result($res,0,'type');
        $filenamewithpath=$filepath.$fileid.'_'.$filename;
        
    }
    $output[0]['fileid'] = $fileid;
    $output[0]['filename'] = $filename;
    $output[0]['filetype'] = $filetype;
    $output[0]['filesize'] = $filesize;
    $output[0]['filecontents'] = $filenamewithpath;
    $log->debug("Exiting customer portal function get_filecontent_detail ");
    
    return $output;
}

function updateDownloadCount($id){
    global $adb,$log;
    $log->debug("Entering customer portal function updateDownloadCount");
    $updateDownloadCount = "UPDATE vtiger_notes SET filedownloadcount = filedownloadcount+1 WHERE notesid = ?";
    $countres = $adb->pquery($updateDownloadCount,array($id));
    $log->debug("Entering customer portal function updateDownloadCount");
    return true;
}


function check_permission($customerid, $module, $entityid) {
    
    global $adb,$log;
    $log->debug("Entering customer portal function check_permission ..");
    $show_all= show_all($module);
    $allowed_contacts_and_accounts = array();
    $check = checkModuleActive($module);
    
    if($check == false){
        return false;
    }
    
    if($show_all == 'false')
        $allowed_contacts_and_accounts[] = $customerid;
        else {
            
            $contactquery = "SELECT contactid, accountid FROM vtiger_contactdetails " .
                " INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid" .
                " AND vtiger_crmentity.deleted = 0 " .
                " WHERE (accountid = (SELECT accountid FROM vtiger_contactdetails WHERE contactid = ?) AND accountid != 0) OR contactid = ?";
            $contactres = $adb->pquery($contactquery, array($customerid,$customerid));
            $no_of_cont = $adb->num_rows($contactres);
            for($i=0;$i<$no_of_cont;$i++){
                $cont_id = $adb->query_result($contactres,$i,'contactid');
                $acc_id = $adb->query_result($contactres,$i,'accountid');
                if(!in_array($cont_id, $allowed_contacts_and_accounts))
                    $allowed_contacts_and_accounts[] = $cont_id;
                    if(!in_array($acc_id, $allowed_contacts_and_accounts) && $acc_id != '0')
                        $allowed_contacts_and_accounts[] = $acc_id;
            }
        }
        if(in_array($entityid, $allowed_contacts_and_accounts)) { //for contact's,if they are present in the allowed list then send true
            return true;
        }
        $faqquery = "select id from vtiger_faq";
        $faqids = $adb->pquery($faqquery,array());
        $no_of_faq = $adb->num_rows($faqids);
        for($i=0;$i<$no_of_faq;$i++){
            $faq_id[] = $adb->query_result($faqids,$i,'id');
        }
        switch($module) {
            
            case 'Documents'	: 	$query = "SELECT vtiger_senotesrel.notesid FROM vtiger_senotesrel
								INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_senotesrel.notesid AND vtiger_crmentity.deleted = 0
								WHERE vtiger_senotesrel.crmid IN (". generateQuestionMarks($allowed_contacts_and_accounts) .")
								AND vtiger_senotesrel.notesid = ?";
            $res = $adb->pquery($query, array($allowed_contacts_and_accounts, $entityid));
            if ($adb->num_rows($res) > 0) {
                return true;
            }
            if(checkModuleActive('Project')) {
                $query = "SELECT vtiger_senotesrel.notesid FROM vtiger_senotesrel
					INNER JOIN vtiger_project ON vtiger_project.projectid = vtiger_senotesrel.crmid
					WHERE vtiger_project.linktoaccountscontacts IN (". generateQuestionMarks($allowed_contacts_and_accounts) .")
					AND vtiger_senotesrel.notesid = ?";
                $res = $adb->pquery($query, array($allowed_contacts_and_accounts, $entityid));
                if ($adb->num_rows($res) > 0) {
                    return true;
                }
            }
            
            $query = "SELECT vtiger_senotesrel.notesid FROM vtiger_senotesrel
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_senotesrel.notesid AND vtiger_crmentity.deleted = 0
				WHERE vtiger_senotesrel.crmid IN (". generateQuestionMarks($faq_id) .")
				AND vtiger_senotesrel.notesid = ?";
            $res = $adb->pquery($query, array($faq_id,$entityid));
            if ($adb->num_rows($res) > 0) {
                return true;
            }
            break;
            
            case 'HelpDesk'	:	if($acc_id) $accCondition = "OR vtiger_troubletickets.parent_id = $acc_id";
            $query = "SELECT vtiger_troubletickets.ticketid FROM vtiger_troubletickets
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_troubletickets.ticketid AND vtiger_crmentity.deleted = 0
				WHERE (vtiger_troubletickets.contact_id IN (". generateQuestionMarks($allowed_contacts_and_accounts) .") $accCondition )
				AND vtiger_troubletickets.ticketid = ?";
            $res = $adb->pquery($query, array($allowed_contacts_and_accounts, $entityid));
            if ($adb->num_rows($res) > 0) {
                return true;
            }
            
            $query = "SELECT vtiger_troubletickets.ticketid FROM vtiger_troubletickets
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_troubletickets.ticketid
				INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = vtiger_crmentity.crmid OR vtiger_crmentityrel.crmid = vtiger_crmentity.crmid)
				WHERE vtiger_crmentity.deleted = 0 AND
				(vtiger_crmentityrel.crmid IN
					(SELECT projectid FROM vtiger_project WHERE linktoaccountscontacts
						IN (". generateQuestionMarks($allowed_contacts_and_accounts) .") AND vtiger_crmentityrel.relcrmid = $entityid)
				OR vtiger_crmentityrel.relcrmid IN
					(SELECT projectid FROM vtiger_project WHERE linktoaccountscontacts
						IN (". generateQuestionMarks($allowed_contacts_and_accounts) .") AND vtiger_crmentityrel.crmid = $entityid)
				AND vtiger_troubletickets.ticketid = ?)";
            
            $res = $adb->pquery($query, array($allowed_contacts_and_accounts, $allowed_contacts_and_accounts, $entityid));
            if ($adb->num_rows($res) > 0) {
                return true;
            }
            
            break;
            
            case 'Accounts' : 	$query = "SELECT vtiger_account.accountid FROM vtiger_account " .
                "INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_account.accountid " .
                "INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.accountid = vtiger_account.accountid " .
                "WHERE vtiger_crmentity.deleted = 0 and vtiger_contactdetails.contactid = ? and vtiger_contactdetails.accountid = ?";
            $res = $adb->pquery($query,array($customerid,$entityid));
            if ($adb->num_rows($res) > 0) {
                return true;
            }
            break;
            
            
        }
        return false;
        $log->debug("Exiting customerportal function check_permission ..");
}

function show_all($module){
    
    global $adb,$log;
    $log->debug("Entering customer portal Function show_all");
    $tabid = getTabid($module);
    if($module=='Tickets'){
        $tabid = getTabid('HelpDesk');
    }
    $query = $adb->pquery("SELECT prefvalue from vtiger_customerportal_prefs where tabid = ?", array($tabid));
    $norows = $adb->num_rows($query);
    if($norows > 0){
        if($adb->query_result($query,0,'prefvalue') == 1){
            return 'true';
        }else {
            return 'false';
        }
    }else {
        return 'false';
    }
    $log->debug("Exiting customerportal function show_all");
}

function get_details($data)
{
    $id = $data['id'];
    $module = $data['block'];
    $customerid = $data['contactid'];
    
    global $adb,$log,$current_language,$default_language,$current_user;
    require_once('include/utils/utils.php');
    require_once('include/utils/UserInfoUtil.php');
    $log->debug("Entering customer portal function get_details ..");
    
    $user = new Users();
    $userid = getPortalUserid();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    
    $current_language = $default_language;
    $isPermitted = check_permission($customerid,$module,$id);
    if($isPermitted == false) {
        return array("#NOT AUTHORIZED#");
    }
    
    if($module == 'Documents'){
        $query =  "SELECT
		vtiger_notes.*,vtiger_crmentity.*,vtiger_attachmentsfolder.foldername,vtiger_notescf.*
		FROM vtiger_notes
		INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_notes.notesid
		LEFT JOIN vtiger_attachmentsfolder
			ON vtiger_notes.folderid = vtiger_attachmentsfolder.folderid
		LEFT JOIN vtiger_notescf ON vtiger_notescf.notesid = vtiger_notes.notesid
		where vtiger_notes.notesid=(". generateQuestionMarks($id) .") AND vtiger_crmentity.deleted=0";
    }
    else if($module == 'HelpDesk'){
        $query ="SELECT
		vtiger_troubletickets.*,vtiger_crmentity.smownerid,vtiger_crmentity.createdtime,vtiger_crmentity.modifiedtime,
		vtiger_ticketcf.*,vtiger_crmentity.description  FROM vtiger_troubletickets
		INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_troubletickets.ticketid
		INNER JOIN vtiger_ticketcf
			ON vtiger_ticketcf.ticketid = vtiger_troubletickets.ticketid
		WHERE (vtiger_troubletickets.ticketid=(". generateQuestionMarks($id) .") AND vtiger_crmentity.deleted = 0)";
    }
    
    else if($module == 'Contacts'){
        $query = "SELECT vtiger_contactdetails.*,vtiger_contactaddress.*,vtiger_contactsubdetails.*,vtiger_contactscf.*" .
            " ,vtiger_crmentity.*,vtiger_customerdetails.*
		FROM vtiger_contactdetails
		INNER JOIN vtiger_crmentity
			ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid
		INNER JOIN vtiger_contactaddress
			ON vtiger_contactaddress.contactaddressid = vtiger_contactdetails.contactid
		INNER JOIN vtiger_contactsubdetails
			ON vtiger_contactsubdetails.contactsubscriptionid = vtiger_contactdetails.contactid
		INNER JOIN vtiger_contactscf
			ON vtiger_contactscf.contactid = vtiger_contactdetails.contactid
		LEFT JOIN vtiger_customerdetails
			ON vtiger_customerdetails.customerid = vtiger_contactdetails.contactid
		WHERE vtiger_contactdetails.contactid = (". generateQuestionMarks($id) .") AND vtiger_crmentity.deleted = 0";
    }
    else if($module == 'Accounts'){
        $query = "SELECT vtiger_account.*,vtiger_accountbillads.*,vtiger_accountshipads.*,vtiger_accountscf.*,
		vtiger_crmentity.* FROM vtiger_account
		INNER JOIN vtiger_crmentity
			ON vtiger_crmentity.crmid = vtiger_account.accountid
		INNER JOIN vtiger_accountbillads
			ON vtiger_account.accountid = vtiger_accountbillads.accountaddressid
		INNER JOIN vtiger_accountshipads
			ON vtiger_account.accountid = vtiger_accountshipads.accountaddressid
		INNER JOIN vtiger_accountscf
			ON vtiger_account.accountid = vtiger_accountscf.accountid" .
			" WHERE vtiger_account.accountid = (". generateQuestionMarks($id) .") AND vtiger_crmentity.deleted = 0";
    }
    
    $params = array($id);
    $res = $adb->pquery($query,$params);
    
    $fieldquery = "SELECT fieldname,columnname,fieldlabel,blocklabel,uitype FROM vtiger_field
		INNER JOIN  vtiger_blocks on vtiger_blocks.blockid=vtiger_field.block WHERE vtiger_field.tabid = ? AND displaytype in (1,2,4)
		ORDER BY vtiger_field.block,vtiger_field.sequence";
    
    $fieldres = $adb->pquery($fieldquery,array(getTabid($module)));
    $nooffields = $adb->num_rows($fieldres);
    
    // Dummy instance to make sure column fields are initialized for futher processing
    $focus = CRMEntity::getInstance($module);
    
    for($i=0;$i<$nooffields;$i++)
    {
        $columnname = $adb->query_result($fieldres,$i,'columnname');
        $fieldname = $adb->query_result($fieldres,$i,'fieldname');
        $fieldid = $adb->query_result($fieldres,$i,'fieldid');
        $blockid = $adb->query_result($fieldres,$i,'block');
        $uitype = $adb->query_result($fieldres,$i,'uitype');
        
        $blocklabel = $adb->query_result($fieldres,$i,'blocklabel');
        $blockname = getTranslatedString($blocklabel,$module);
        
        /* === START : 2016-09-01 Changes For Contacts Blocks  === */
        $allowedBlocks = array("LBL_CONTACT_INFORMATION", "LBL_DESCRIPTION_INFORMATION");
        if($module == "Contacts" && !in_array($blocklabel, $allowedBlocks))continue;
        /* === END : 2016-09-01 Changes For Contacts Blocks  === */
        
        
        if($blocklabel == 'LBL_COMMENTS' || $blocklabel == 'LBL_IMAGE_INFORMATION'){ // the comments block of tickets is hardcoded in customer portal,get_ticket_comments is used for it
            continue;
        }
        if($uitype == 83){ //for taxclass in products and services
            continue;
        }
        $fieldper = getFieldVisibilityPermission($module,$current_user->id,$fieldname);
        if($fieldper == '1'){
            continue;
        }
        
        $fieldlabel = getTranslatedString($adb->query_result($fieldres,$i,'fieldlabel'));
        $fieldvalue = $adb->query_result($res,0,$columnname);
        
        $output[0][$module][$i]['fieldlabel'] = $fieldlabel ;
        $output[0][$module][$i]['blockname'] = $blockname;
        if($columnname == 'title' || $columnname == 'description') {
            $fieldvalue = decode_html($fieldvalue);
        }
        if($uitype == 71 || $uitype == 72){
            $fieldvalue = number_format($fieldvalue, 5, '.', '');
        }
        if($columnname == 'parent_id' || $columnname == 'contactid' || $columnname == 'accountid' || $columnname == 'potentialid'
            || $fieldname == 'account_id' || $fieldname == 'contact_id' || $columnname == 'linktoaccountscontacts')
        {
            $crmid = $fieldvalue;
            $modulename = getSalesEntityType($crmid);
            if ($crmid != '' && $modulename != '') {
                $fieldvalues = getEntityName($modulename, array($crmid));
                if($modulename == 'Contacts')
                    $fieldvalue = '<a href="contacts.php?module=Contacts&action=detail&id='.$crmid.'">'.$fieldvalues[$crmid].'</a>';
                    elseif($modulename == 'Accounts')
                    $fieldvalue = '<a href="accounts.php?module=Accounts&action=detail&id='.$crmid.'">'.$fieldvalues[$crmid].'</a>';
                    else
                        $fieldvalue = $fieldvalues[$crmid];
            } else {
                $fieldvalue = '';
            }
        }
        
        
        if($module == 'Documents')
        {
            $fieldid = $adb->query_result($res,0,'notesid');
            $filename = $fieldvalue;
            $folderid = $adb->query_result($res,0,'folderid');
            $filestatus = $adb->query_result($res,0,'filestatus');
            $filetype = $adb->query_result($res,0,'filelocationtype');
            if($fieldname == 'filename'){
                if($filestatus == 1){
                    if($filetype == 'I'){
                        $fieldvalue = '<a href="download.php?downloadfile=true&folderid='.$folderid.'&filename='.$filename.'&module=Documents&action=index&id='.$fieldid.'" >'.$fieldvalue.'</a>';
                    }
                    elseif($filetype == 'E'){
                        $fieldvalue = '<a target="_blank" href="'.$filename.'" onclick = "updateCount('.$fieldid.');">'.$filename.'</a>';
                    }
                }
            }
            if($fieldname == 'folderid'){
                $fieldvalue = $adb->query_result($res,0,'foldername');
            }
            if($fieldname == 'filesize'){
                if($filetype == 'I'){
                    $fieldvalue = $fieldvalue .' B';
                }
                elseif($filetype == 'E'){
                    $fieldvalue = '--';
                }
            }
            if($fieldname == 'filelocationtype'){
                if($fieldvalue == 'I'){
                    $fieldvalue = getTranslatedString('LBL_INTERNAL',$module);
                }elseif($fieldvalue == 'E'){
                    $fieldvalue = getTranslatedString('LBL_EXTERNAL',$module);
                }else{
                    $fieldvalue = '---';
                }
            }
        }
        
        if($fieldname == 'assigned_user_id' || $fieldname == 'assigned_user_id1'){
            $fieldvalue = getOwnerName($fieldvalue);
        }
        if($uitype == 56){
            if($fieldvalue == 1){
                $fieldvalue = 'Yes';
            }else{
                $fieldvalue = 'No';
            }
        }
        if($module == 'HelpDesk' && $fieldname == 'ticketstatus'){
            $parentid = $adb->query_result($res,0,'contact_id');
            $status = $adb->query_result($res,0,'status');
            if($customerid != $parentid ){ //allow only the owner to close the ticket
                $fieldvalue = '';
            }else{
                $fieldvalue = $status;
            }
        }
        
        if($fieldname == "view_permission"){
            $viewpermission = explode(" |##| ", $fieldvalue);
            $fieldvalue = array();
            foreach($viewpermission as $ownerid){
                $fieldvalue[] = getOwnerName($ownerid);
            }
            $fieldvalue = implode(", ",$fieldvalue);
        }
        $output[0][$module][$i]['fieldvalue'] = $fieldvalue;
    }
    
    if($module == 'HelpDesk'){
        $ticketid = $adb->query_result($res,0,'ticketid');
        $sc_info = getRelatedServiceContracts($ticketid);
        if (!empty($sc_info)) {
            $modulename = 'ServiceContracts';
            $blocklable = getTranslatedString('LBL_SERVICE_CONTRACT_INFORMATION',$modulename);
            $j=$i;
            for($k=0;$k<count($sc_info);$k++){
                foreach ($sc_info[$k] as $label => $value) {
                    $output[0][$module][$j]['fieldlabel']= getTranslatedString($label,$modulename);
                    $output[0][$module][$j]['fieldvalue']= $value;
                    $output[0][$module][$j]['blockname'] = $blocklable;
                    $j++;
                }
            }
        }
    }
    $log->debug("Existing customer portal function get_details ..");
    return $output;
}

function get_documents($data)
{
    $id = $data['id'];
    $module = $data['module'];
    $customerid = $data['contactid'];
    
    global $adb,$log;
    $log->debug("Entering customer portal function get_documents ..");
    $check = checkModuleActive($module,$customerid);
    if($check == false){
        return array("#MODULE INACTIVE#");
    }
    $fields_list = array(
        'title' => 'Title',
        'filename' => 'FileName',
        'createdtime' => 'Created Time');
    
    $query ="select vtiger_notes.title,'Documents' ActivityType, vtiger_notes.filename,
		crm2.createdtime,vtiger_notes.notesid,vtiger_notes.folderid,
		vtiger_notes.notecontent description, vtiger_users.user_name, vtiger_notes.filelocationtype
		from vtiger_notes
		LEFT join vtiger_senotesrel on vtiger_senotesrel.notesid= vtiger_notes.notesid
		INNER join vtiger_crmentity on vtiger_crmentity.crmid= vtiger_senotesrel.crmid
		LEFT join vtiger_crmentity crm2 on crm2.crmid=vtiger_notes.notesid and crm2.deleted=0
		LEFT JOIN vtiger_groups
		ON vtiger_groups.groupid = vtiger_crmentity.smownerid
		LEFT join vtiger_users on crm2.smownerid= vtiger_users.id
		where vtiger_crmentity.crmid=?";
    $res = $adb->pquery($query,array($id));
    $noofdata = $adb->num_rows($res);
    for( $j= 0;$j < $noofdata; $j++)
    {
        $i=0;
        foreach($fields_list as $fieldname => $fieldlabel) {
            $output[0][$module]['head'][0][$i]['fielddata'] = $fieldlabel; //$adb->query_result($fieldres,$i,'fieldlabel');
            $fieldvalue = $adb->query_result($res,$j,$fieldname);
            if($fieldname =='title') {
                $fieldid = $adb->query_result($res,$j,'notesid');
                $filename = $fieldvalue;
                $fieldvalue = '<a href="documents?&module=Documents&action=detail&id='.$fieldid.'">'.$fieldvalue.'</a>';
            }
            if($fieldname == 'filename'){
                $fieldid = $adb->query_result($res,$j,'notesid');
                $filename = $fieldvalue;
                $folderid = $adb->query_result($res,$j,'folderid');
                $filetype = $adb->query_result($res,$j,'filelocationtype');
                if($filetype == 'I'){
                    $fieldvalue = '<a href="download.php?&downloadfile=true&folderid='.$folderid.'&filename='.$filename.'&module=Documents&action=index&id='.$fieldid.'">'.$fieldvalue.'</a>';
                }else{
                    $fieldvalue = '<a target="_blank" href="'.$filename.'">'.$filename.'</a>';
                }
            }
            $output[1][$module]['data'][$j][$i]['fielddata'] = $fieldvalue;
            $i++;
        }
    }
    $log->debug("Exiting customerportal function  get_faq_document ..");
    return $output;
}

function get_record_entity_name_fields($input_array){
    
    $id = $input_array['id'];
    $contactid = (int) $input_array['contactid'];
    
    $modulename = getSalesEntityType($id);
    
    $moduleModel = Vtiger_Module_Model::getInstance($modulename);
    
    $recordModel = Vtiger_Record_Model::getInstanceById($id, $modulename);
    
    $entityNameFields = $moduleModel->getNameFields();
    
    $record_name_fields = array();
    
    if(!empty($entityNameFields)){
        foreach($entityNameFields as $enityname){
            $record_name_fields[] = $recordModel->get($enityname);
        }
    }
    return $record_name_fields;
}

function add_document_attachment($input_array){
    global $adb,$log;
    global $root_directory, $upload_badext;
    
    $log->debug("Entering customer portal function add_document_attachment");
    
    $adb->println("INPUT ARRAY for the function add_document_attachment");
    $adb->println($input_array);
    
    $id = $input_array['id'];
    
    $title = $input_array['title'];
    $note_desc = $input_array['description'];
    $filelocationtype = $input_array['filelocationtype'];
    $filename = $input_array['filename'];
    
    $filetype = '';
    $filesize = 0;
    
    if($filelocationtype == "I"){
        $filetype = $input_array['filetype'];
        $filesize = $input_array['filesize'];
        $filecontents = $input_array['filecontents'];
        
        if($filesize > 0 && $filecontents != ''){
            
            $upload_filepath = decideFilePath();
            
            $attachmentid = $adb->getUniqueID("vtiger_crmentity");
            
            $filename = sanitizeUploadFileName($filename, $upload_badext);
            $new_filename = $attachmentid.'_'.$filename;
            
            $data = base64_decode($filecontents);
            $description = 'CustomerPortal Document Attachment';
            
            $handle = @fopen($upload_filepath.$new_filename,'w');
            fputs($handle, $data);
            fclose($handle);
            
            $date_var = $adb->formatDate(date('Y-m-d H:i:s'), true);
            
            $crmquery = "insert into vtiger_crmentity (crmid,setype,description,createdtime) values(?,?,?,?)";
            $crmresult = $adb->pquery($crmquery, array($attachmentid, 'Documents Attachment', $description, $date_var));
            
            $attachmentquery = "insert into vtiger_attachments(attachmentsid,name,description,type,path) values(?,?,?,?,?)";
            $attachmentreulst = $adb->pquery($attachmentquery, array($attachmentid, $filename, $description, $filetype, $upload_filepath));
        }
    }
    
    $ownerResult = $adb->pquery("SELECT smownerid FROM vtiger_crmentity WHERE crmid = ?", array($id));
    $user_id = $adb->query_result($ownerResult, 0, 'smownerid');
    
    if(!$user_id)
        $user_id = getDefaultAssigneeId();
        
        //require_once('modules/Documents/Documents.php');
        
        $query = "SELECT * FROM vtiger_documentfolder inner join vtiger_crmentity on
	vtiger_crmentity.crmid = vtiger_documentfolder.documentfolderid
	WHERE is_default=1 and deleted=0";
        
        $result = $adb->pquery($query, array());
        
        if($input_array['doc_folder_id']){
            $doc_fol_id = $input_array['doc_folder_id'];
        }elseif($adb->num_rows($result)){
            $doc_fol_id = $adb->query_result($result,0,'documentfolderid');
        }
        
        $focus = CRMEntity::getInstance('Documents');
        $focus->column_fields['notes_title'] = $title;
        $focus->column_fields['filename'] = $filename;
        $focus->column_fields['filetype'] = $filetype;
        $focus->column_fields['filesize'] = $filesize;
        $focus->column_fields['filelocationtype'] = $filelocationtype;
        $focus->column_fields['filedownloadcount']= 0;
        $focus->column_fields['filestatus'] = 1;
        $focus->column_fields['assigned_user_id'] = $user_id;
        $focus->column_fields['folderid'] = 1;
        $focus->column_fields['notecontent'] = $note_desc;
        $focus->column_fields['from_portal'] = 1;
        $focus->column_fields['contactid'] = $id;
    $focus->column_fields['related_to'] = $id;
        
        if($doc_fol_id)
            $focus->column_fields['doc_folder_id'] = $doc_fol_id;
            
            $focus->save('Documents');
            
            if($filelocationtype == "I" && $attachmentid > 0){
                $related_doc = 'insert into vtiger_seattachmentsrel values (?,?)';
                $res = $adb->pquery($related_doc,array($focus->id,$attachmentid));
            }
            
            $doc = 'insert into vtiger_senotesrel values(?,?)';
            $res = $adb->pquery($doc,array($id,$focus->id));
            
            $log->debug("Exiting customer portal function add_document_attachment");
            
            return array("new_document" => array("document_id" => $focus->id));
}

function getDefaultAssigneeId() {
    global $adb;
    $adb->println("Entering customer portal function getPortalUserid");
    
    // Look the value from cache first
    $res = $adb->pquery("SELECT prefvalue FROM vtiger_customerportal_prefs WHERE prefkey = 'defaultassignee' AND tabid = 0", array());
    $norows = $adb->num_rows($res);
    if($norows > 0) {
        $defaultassignee = $adb->query_result($res,0,'prefvalue');
        // Update the cache information now.
    }
    return $defaultassignee;
}

function get_module_details($input_array){
    $id = $input_array['id'];
    $module = $input_array['block'];
    $customerid = $input_array['contactid'];
    
    $view = $input_array['view'];
    
    global $adb,$log,$current_language,$default_language,$current_user;
    require_once('include/utils/utils.php');
    require_once('include/utils/UserInfoUtil.php');
    $log->debug("Entering customer portal function get_details ..");
    
    $user = new Users();
    $userid = getPortalUserid();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    
    $current_language = $default_language;
    $isPermitted = check_permission($customerid,$module,$id);
    if($isPermitted == false) {
        return array("#NOT AUTHORIZED#");
    }
    
    if($module == 'Documents'){
        
        $query =  "SELECT vtiger_notes.*,vtiger_crmentity.*,vtiger_documentfolder.folder_name as foldername,vtiger_notescf.*, vtiger_senotesrel.crmid as contactid
		FROM vtiger_notes INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_notes.notesid
		INNER JOIN vtiger_documentfolder ON vtiger_notes.doc_folder_id = vtiger_documentfolder.documentfolderid
		INNER JOIN vtiger_notescf ON vtiger_notescf.notesid = vtiger_notes.notesid
		inner join vtiger_senotesrel on vtiger_senotesrel.notesid= vtiger_notes.notesid
		WHERE vtiger_notes.notesid=(". generateQuestionMarks($id) .") AND vtiger_crmentity.deleted=0";
        
    } else if($module == 'Contacts'){
        
        $query = "SELECT vtiger_contactdetails.*,vtiger_contactaddress.*,vtiger_contactsubdetails.*,vtiger_contactscf.*, " .
            "vtiger_crmentity.*,vtiger_customerdetails.* FROM vtiger_contactdetails
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid
		INNER JOIN vtiger_contactaddress ON vtiger_contactaddress.contactaddressid = vtiger_contactdetails.contactid
		INNER JOIN vtiger_contactsubdetails ON vtiger_contactsubdetails.contactsubscriptionid = vtiger_contactdetails.contactid
		INNER JOIN vtiger_contactscf ON vtiger_contactscf.contactid = vtiger_contactdetails.contactid
		LEFT JOIN vtiger_customerdetails ON vtiger_customerdetails.customerid = vtiger_contactdetails.contactid
		WHERE vtiger_contactdetails.contactid = (". generateQuestionMarks($id) .") AND vtiger_crmentity.deleted = 0";
    }
    
    $params = array($id);
    $res = $adb->pquery($query,$params);
    
    
    $fieldquery = "SELECT fieldname,columnname,fieldlabel,blocklabel,uitype FROM vtiger_field
		INNER JOIN  vtiger_blocks on vtiger_blocks.blockid=vtiger_field.block WHERE vtiger_field.tabid = ? AND displaytype in (1,2,4)
		ORDER BY vtiger_field.block,vtiger_field.sequence";
    
    
    $fieldres = $adb->pquery($fieldquery,array(getTabid($module)));
    //$fieldres = $adb->pquery($fieldquery,array(getTabid($module), $roleid));
    $nooffields = $adb->num_rows($fieldres);
    
    $focus = CRMEntity::getInstance($module);
    
    for($i=0;$i<$nooffields;$i++)
    {
        $columnname = $adb->query_result($fieldres,$i,'columnname');
        $fieldname = $adb->query_result($fieldres,$i,'fieldname');
        $fieldid = $adb->query_result($fieldres,$i,'fieldid');
        $blockid = $adb->query_result($fieldres,$i,'block');
        $uitype = $adb->query_result($fieldres,$i,'uitype');
        
        $blocklabel = $adb->query_result($fieldres,$i,'blocklabel');
        $blockname = getTranslatedString($blocklabel,$module);
        if($blocklabel == 'LBL_COMMENTS' || $blocklabel == "LBL_CUSTOMER_PORTAL_INFORMATION"){
            continue;
        }
        if($uitype == 83){
            continue;
        }
        $fieldper = getFieldVisibilityPermission($module,$current_user->id,$fieldname);
        if($fieldper == '1'){
            continue;
        }
        
        $fieldlabel = getTranslatedString($adb->query_result($fieldres,$i,'fieldlabel'));
        $fieldvalue = $adb->query_result($res,0,$columnname);
        
        $output[0][$module][$i]['fieldlabel'] = $fieldlabel ;
        $output[0][$module][$i]['blockname'] = $blockname;
        $output[0][$module][$i]['fieldname'] = $fieldname;
        
        if($columnname == 'title' || $columnname == 'description') {
            $fieldvalue = decode_html($fieldvalue);
        }
        if($uitype == 71 || $uitype == 72){
            $fieldvalue = number_format($fieldvalue, 5, '.', '');
        }
        
        if($uitype == 52)
            $fieldvalue = getOwnerName($fieldvalue);
            
            if($columnname == 'parent_id' || $columnname == 'contactid' || $columnname == 'accountid' || $columnname == 'potentialid'
                || $fieldname == 'account_id' || $fieldname == 'contact_id' || $columnname == 'linktoaccountscontacts' || $uitype == 10)
            {
                $crmid = $fieldvalue;
                $modulename = getSalesEntityType($crmid);
                if ($crmid != '' && $modulename != '') {
                    $fieldvalues = getEntityName($modulename, array($crmid));
                    if($modulename == 'Contacts')
                        $fieldvalue = '<a href="contacts.php?module=Contacts&action=detail&id='.$crmid.'">'.$fieldvalues[$crmid].'</a>';
                        elseif($modulename == 'Accounts' && $view != 'edit')
                        $fieldvalue = '<a href="accounts.php?module=Accounts&action=index&id='.$crmid.'">'.$fieldvalues[$crmid].'</a>';
                        else
                            $fieldvalue = $fieldvalues[$crmid];
                } else {
                    $fieldvalue = '';
                }
            }
            
            if($fieldname == 'assigned_user_id' || $fieldname == 'assigned_user_id1'){
                $fieldvalue = getOwnerName($fieldvalue);
            }
            if($uitype == 56){
                if($fieldvalue == 1){
                    $fieldvalue = 'Yes';
                }else{
                    $fieldvalue = 'No';
                }
            }
            
            if($uitype == "33" && $fieldvalue){
                $fieldvalue = explode(" |##| ", $fieldvalue);
                $fieldvalue = implode(", ", $fieldvalue);
            }
            
            if($fieldname == 'imagename' && $module == 'Contacts'){
                global $site_URL;
                
                $query = "SELECT vtiger_attachments.attachmentsid, vtiger_attachments.name, vtiger_attachments.path FROM vtiger_contactdetails
			INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.crmid = vtiger_contactdetails.contactid
			INNER JOIN vtiger_attachments ON vtiger_attachments.attachmentsid = vtiger_seattachmentsrel.attachmentsid
			WHERE vtiger_seattachmentsrel.crmid = ?";
                
                $result = $adb->pquery($query,array($id));
                
                if($adb->num_rows($result))
                    $fieldvalue = $site_URL.$adb->query_result($result, "0","path").$adb->query_result($result, "0","attachmentsid")."_".$adb->query_result($result, "0","name");
            }
            
            if($module == 'Documents'){
                $fieldid = $adb->query_result($res,0,'notesid');
                $filename = $fieldvalue;
                $folderid = $adb->query_result($res,0,'doc_folder_id');
                $filestatus = $adb->query_result($res,0,'filestatus');
                $filetype = $adb->query_result($res,0,'filelocationtype');
                if($fieldname == 'filename'){
                    if($filestatus == 1){
                        
                        $mode = $input_array['mode'];
                        
                        if($filetype == 'I'){
                            $fieldvalue = '<a href="download.php?downloadfile=true&folderid='.$folderid.'&filename='.$filename.'&module=Documents&action=index&id='.$fieldid.'" >'.$fieldvalue.'</a>';
                        } else if($filetype == 'E'){
                            if($mode == 'edit')
                                $fieldvalue = $filename;
                                else
                                    $fieldvalue = '<a target="_blank" href="'.$filename.'">'.$filename.'</a>';
                        }
                    }
                }
                if($fieldname == 'doc_folder_id'){
                    $fieldvalue = $adb->query_result($res,0,'foldername');
                }
                if($fieldname == 'filesize'){
                    if($filetype == 'I'){
                        $fieldvalue = $fieldvalue .' B';
                    }
                    elseif($filetype == 'E'){
                        $fieldvalue = '--';
                    }
                }
                if($fieldname == 'filelocationtype'){
                    if($fieldvalue == 'I'){
                        $fieldvalue = getTranslatedString('LBL_INTERNAL',$module);
                    }elseif($fieldvalue == 'E'){
                        $fieldvalue = getTranslatedString('LBL_EXTERNAL',$module);
                    }else{
                        $fieldvalue = '---';
                    }
                }
            }
            
            $output[0][$module][$i]['fieldvalue'] = $fieldvalue;
            
            if($module == 'Documents'){
                $output[1][$module]['contactid'] = $adb->query_result($res, "0","contactid");
            }
    }
    
    return $output;
}

function update_document($input_array){
    
    global $adb,$log;
    global $root_directory, $upload_badext;
    
    $save_doc = false;
    
    $log->debug("Entering customer portal function update_document");
    $adb->println("INPUT ARRAY for the function update_document");
    $adb->println($input_array);
    
    $id = $input_array['id'];
    
    $title = $input_array['title'];
    $note_desc = $input_array['description'];
    $filelocationtype = $input_array['filelocationtype'];
    $filename = $input_array['filename'];
    
    $notesid = $input_array['notesid'];
    
    $filetype = '';
    $filesize = 0;
    
    //require_once('modules/Documents/Documents.php');
    $focus = CRMEntity::getInstance('Documents');
    $focus->id = $notesid;
    $focus->retrieve_entity_info($focus->id,'Documents');
    $focus->mode = "edit";
    
    if($filelocationtype == "E")
        $focus->column_fields['filename'] = $filename;
        else
            $filedownloadcount = $focus->column_fields['filedownloadcount'];
            
            $delete_previous_attachment = $input_array['delete_previous'];
            
            if($delete_previous_attachment){
                
                $att_result = $adb->pquery("select vtiger_seattachmentsrel.attachmentsid, vtiger_attachments.name,
		vtiger_attachments.path from vtiger_seattachmentsrel inner join vtiger_attachments on
		vtiger_attachments.attachmentsid = vtiger_seattachmentsrel.attachmentsid
		where vtiger_seattachmentsrel.crmid = ?",array($notesid));
                
                if($adb->num_rows($att_result) > 0 ){
                    
                    $attachment_id = $adb->query_result($att_result,"0","attachmentsid");
                    $path = $adb->query_result($att_result,"0","path");
                    $name = $adb->query_result($att_result,"0","name");
                    
                    $adb->pquery("delete from vtiger_seattachmentsrel where crmid = ? and attachmentsid = ?", array($notesid, $attachment_id));
                    $adb->pquery("delete from vtiger_attachments where attachmentsid = ?", array($attachment_id));
                    unlink($path.$attachment_id."_".$name);
                }
            }
            
            
            if($filename != '' && $filelocationtype == "I") {
                
                $filetype = $input_array['filetype'];
                $filesize = $input_array['filesize'];
                $filecontents = $input_array['filecontents'];
                $filedownloadcount = '0';
                
                if($filesize > 0 && $filecontents != ''){
                    
                    $save_doc = true;
                    
                    $upload_filepath = decideFilePath();
                    
                    $attachmentid = $adb->getUniqueID("vtiger_crmentity");
                    
                    $filename = sanitizeUploadFileName($filename, $upload_badext);
                    $new_filename = $attachmentid.'_'.$filename;
                    
                    $data = base64_decode($filecontents);
                    $description = 'CustomerPortal Document Attachment';
                    
                    $handle = @fopen($upload_filepath.$new_filename,'w');
                    fputs($handle, $data);
                    fclose($handle);
                    
                    $date_var = $adb->formatDate(date('Y-m-d H:i:s'), true);
                    
                    $crmquery = "insert into vtiger_crmentity (crmid,setype,description,createdtime) values(?,?,?,?)";
                    $crmresult = $adb->pquery($crmquery, array($attachmentid, 'Documents Attachment', $description, $date_var));
                    
                    $attachmentquery = "insert into vtiger_attachments(attachmentsid,name,description,type,path) values(?,?,?,?,?)";
                    $attachmentreulst = $adb->pquery($attachmentquery, array($attachmentid, $filename, $description, $filetype, $upload_filepath));
                    
                    $focus->column_fields['filename'] = $filename;
                    $focus->column_fields['filetype'] = $filetype;
                    $focus->column_fields['filesize'] = $filesize;
                    $focus->column_fields['filelocationtype'] = 'I';
                    $focus->column_fields['filedownloadcount']= '0';
                    $focus->column_fields['filestatus'] = 1;
                    $focus->column_fields['folderid'] = 1;
                }
            }
            
            $focus->column_fields['notes_title'] = $title;
            $focus->column_fields['notecontent'] = $note_desc;
            $focus->column_fields['filelocationtype'] = $filelocationtype;
            
            $focus->save('Documents');
            
            if($save_doc && $attachmentid > 0){
                
                $related_doc = 'insert into vtiger_seattachmentsrel values (?,?)';
                $res = $adb->pquery($related_doc,array($focus->id,$attachmentid));
            }
            
            if($filelocationtype == "E"){
                
                if($filename != '' && !preg_match('/^\w{1,5}:\/\/|^\w{0,3}:?\\\\\\\\/', trim($filename), $match)) {
                    $filename = "http://$filename";
                }
                
                $filetype = '';
                $filesize = 0;
                $filedownloadcount = null;
            }
            
            if($filename){
                $query = "UPDATE vtiger_notes SET filename = ? ,filesize = ?, filetype = ? , filelocationtype = ?, filedownloadcount = ? WHERE notesid = ?";
                $adb->pquery($query,array(decode_html($filename),$filesize,$filetype,$filelocationtype,$filedownloadcount,$focus->id));
            }
            
            $log->debug("Exiting customer portal function update_document");
            
            return array("document_id" => $focus->id);
}

function getContactAccessibleAccounts($contactid){
    
    require_once("libraries/reports/new/nCommon.php");
    $account_numbers = GetAccountNumbersFromRecord($contactid);
    
    return $account_numbers;
}


function LoadIncomeLastYearReport($input_array){
    
    require_once("libraries/Reporting/ReportCommonFunctions.php");
    require_once("libraries/Reporting/ReportIncome.php");
    
    
    $account = getContactAccessibleAccounts($input_array['ID']);
    $accountIdNo = array();
    if($input_array['show_reports'] == 'Accounts')
        $accountIdNo = getContactAccessibleAccounts($input_array['accountid']);
        
        $account_number = array_merge($account,$accountIdNo);
        $account_number = array_unique($account_number);
        
        
        $accounts = $account_number;
        
        $income = new Income_Model($accounts);
        $individual = $income->GetIndividualIncomeForDates(GetFirstDayLastYear(), GetLastDayLastYear());
        $monthly = $income->GetMonthlyTotalForDates(GetFirstDayLastYear(), GetLastDayLastYear());
        $graph = $income->GenerateGraphForDates(GetFirstDayLastYear(), GetLastDayLastYear());
        $combined = $income->GetCombinedSymbolsForDates(GetFirstDayLastYear(), GetLastDayLastYear());
        
        $year_end_totals = $income->CalculateCombineSymbolsYearEndToal(GetFirstDayLastYear(), GetLastDayLastMonth());
        $grand_total = $income->CalculateGrandTotal(GetFirstDayLastYear(), GetLastDayLastMonth());
        
        $start_month = date("F, Y", strtotime(GetFirstDayLastYear()));
        $end_month = date("F, Y", strtotime(GetLastDayLastYear()));
        
        $output = array();
        
        $output["start_month"] = $start_month;
        $output["end_month"] = $end_month;
        $output['monthly_totals'] = $monthly;
        $output['combined_symbols'] = $combined;
        $output['year_end_totals'] = $year_end_totals;
        $output['grand_total'] = $grand_total;
        $output['dynamic_graph'] = json_encode($graph);
        
        $account_totals = PortfolioInformation_Module_Model::GetAccountSumTotals($accounts);
        $output['global_total'] = $account_totals['total'];
        
        if(is_array($account_number)){
            $portfolios = array();
            foreach($account_number AS $k => $v) {
                $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($v);
                if($crmid) {
                    $p = PortfolioInformation_Record_Model::getInstanceById($crmid);
                    $portfolios[] = $p->getData();
                }
            }
            $output["portfolio_data"] = $portfolios;
        }
        
        return $output;
}


function LoadHoldingsReport($account_number){
    
    global $current_user,$adb,$log;
    
    $holdingsReportParams = array();
    
    $holdingsReportParams['account_number'] = $account_number;
    $holdingsReportParams['module'] = "PortfolioInformation";
    
    $request = new Vtiger_Request($holdingsReportParams, $holdingsReportParams);
    
    $account_number = $request->get("account_number");
    
    $total_weight = 0;
    
    $accounts = array();
    
    if(!is_array($account_number))
        $accounts = array($account_number);
        else
            $accounts = $account_number;
            
            $accounts = array_unique($accounts);
            
            if (!empty($accounts)) {
                
                PortfolioInformation_HoldingsReport_Model::GenerateReportFromAccounts($accounts);
                
                holdingsReportReformatSecondaryTableData();
                
                $global_total = cHoldingsReport::GetGlobalTotal();
                
                $primary = cHoldingsReport::GetGroupedPrimary();
                $secondary = cHoldingsReport::GetGroupedSecondary();
                $positions = cHoldingsReport::GetWeightedPositions();
                $positions = cHoldingsReport::CategorizePositions($positions);
                
                foreach($positions AS $k => $v)
                    $symbols[] = $v['security_symbol'];
                    
                    if(sizeof($symbols) > 0)
                        $position_information = ModSecurities_Module_Model::GetSecurityInformationFromSymbols($symbols);
                        
                        $grouped = cHoldingsReport::GetWeightedPositions(true);
                        $grouped = cHoldingsReport::CategorizePositions($grouped);
                        
                        $categories = cHoldingsReport::TotalCategories($positions, $total_weight);
                        
                        $ac = cHoldingsReport::TotalAssetClass($positions);
                        $ac_weight = cHoldingsReport::GetACWeights($ac, $global_total);
                        $individual_ac = cHoldingsReport::TotalIndividualizedAssetClass($positions);
                        $individual_weight = cHoldingsReport::GetACWeights($individual_ac, $global_total);
            };
            
            $portfolios = array();
            
            $contact_instance = null;
            if(is_array($accounts)){
                $portfolios = array();
                $unsettled_cash = 0;
                foreach($accounts AS $k => $v) {
                    $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($v);
                    if($crmid) {
                        $p = PortfolioInformation_Record_Model::getInstanceById($crmid);
                        
                        $portfolios[] = $p->getData();
                        $unsettled_cash += $p->get('unsettled_cash');
                        
                    }
                }
            }
            
            $trailing_aum = PortfolioInformation_HistoricalInformation_Model::GetTrailing12AUM($accounts);
            $trailing_revenue = PortfolioInformation_HistoricalInformation_Model::GetTrailing12Revenue($accounts);
            
            $monthly_values = getAccountValueOverLast12Months(array($request->get("account_number")));
            
            $output["date"] = date("F d, Y");
            $output["num_accounts_used"] = sizeof($accounts);
            $output["portfolio_data"] = $portfolios;
            $output["unsettled_cash"] = $unsettled_cash;
            $output["global_total"] = $global_total;
            $output["asset_class"] = $ac;
            $output["total_weight"] = $total_weight;
            $output["primary"] = $primary;
            $output["secondary"] = $secondary;
            $output["categories"] = $categories;
            $output["individual"] = $positions;
            $output["positions"] = $position_information;
            $output["grouped"] = $grouped;
            $output["monthly_totals"] = json_encode($monthly_values);
            $output["asset_class_weight"] = $ac_weight;
            $output["individual_ac"] = $individual_ac;
            $output["individual_weight"] = $individual_weight;
            
            $output["asset_allocation_report"] = getAssetAllocationReport($request->get("account_number"));
            
            return $output;
}


function getAccountValueOverLast12Months($accounts){
    
    require_once("libraries/reports/cReturn.php");
    
    $transaction_handler = new cReturn();
    
    $pids = GetPortfolioIDsFromPortfolioAccountNumbers($accounts);
    $pids = SeparateArrayWithCommas($pids);
    
    $m = date('m');
    $d = date('d');
    $Y = date('Y');
    
    $date = date('Y-m-d 00:00:00',mktime(0,0,0,$m,$d,$Y));
    
    $accts_back = $accounts;
    
    $months = array();
    $months[] = date('Y-m-d 00:00:00',mktime(0,0,0,$m-11,0,$Y));
    $months[] = date('Y-m-d 00:00:00',mktime(0,0,0,$m-10,0,$Y));
    $months[] = date('Y-m-d 00:00:00',mktime(0,0,0,$m-9,0,$Y));
    $months[] = date('Y-m-d 00:00:00',mktime(0,0,0,$m-8,0,$Y));
    $months[] = date('Y-m-d 00:00:00',mktime(0,0,0,$m-7,0,$Y));
    $months[] = date('Y-m-d 00:00:00',mktime(0,0,0,$m-6,0,$Y));
    $months[] = date('Y-m-d 00:00:00',mktime(0,0,0,$m-5,0,$Y));
    $months[] = date('Y-m-d 00:00:00',mktime(0,0,0,$m-4,0,$Y));
    $months[] = date('Y-m-d 00:00:00',mktime(0,0,0,$m-3,0,$Y));
    $months[] = date('Y-m-d 00:00:00',mktime(0,0,0,$m-2,0,$Y));
    $months[] = date('Y-m-d 00:00:00',mktime(0,0,0,$m-1,0,$Y));
    $months[] = date('Y-m-d 00:00:00',mktime(0,0,0,$m-0,0,$Y));
    
    $history = array();
    $abreviated_months = array();
    $currentMonth = (int)date('m');
    $count = 0;
    for($x = $currentMonth; $x < $currentMonth+12; $x++) {
        $abreviated_months[$count] = substr(date('F', mktime(0, 0, 0, $x, 1)), 0, 3);
        $count++;
    }
    
    $portfolio_transactions = $transaction_handler->GetAllPortfolioTransactions($pids, null);
    $transaction_handler->FillTransactionTable($portfolio_transactions);
    
    foreach($months AS $k => $v){
        $t = $transaction_handler->GetSymbolTotals($v);
        $val = $transaction_handler->AddAllSymbolTotals($t);
        $history[$abreviated_months[$k]] = $val;
    }
    
    $value_history = array();
    $count = 0;
    foreach($history AS $k => $v){
        $value = number_format($v,2,".","");
        $value_history[] = array("date"=>$k, "value"=>$value, "open:"=>"$", "date_time"=>$months[$count]);
        $count++;
    }
    
    return $value_history;
}

function holdingsReportReformatSecondaryTableData(){
    
    $adb = PearDatabase::getInstance();
    
    $adb->pquery("DROP TABLE IF EXISTS holdings_grouped_secondary", array());
    
    $query = "CREATE TEMPORARY TABLE holdings_grouped_secondary
	SELECT SUM(current_value) AS group_total, SUM(weight) AS group_weight, s.securitytype, cf.aclass
	FROM holdings_report_positions r
	LEFT JOIN vtiger_modsecurities s ON s.security_symbol = r.security_symbol
	LEFT JOIN vtiger_modsecuritiescf cf ON s.modsecuritiesid = cf.modsecuritiesid
	GROUP BY securitytype";
    $adb->pquery($query, array());
}

function getAssetAllocationReport($account_number){
    
    if(!is_array($account_number))
        $account_number = array($account_number);
        
        PortfolioInformation_HoldingsReport_Model::GenerateEstimateTables($account_number);
        
        $categories = array("estimatedtype");
        $fields = array("security_symbol", "account_number", "cusip", "description", "quantity", "last_price", "weight", "current_value");
        $totals = array("current_value", "weight");
        
        $estimateTable = PortfolioInformation_Reports_Model::GetTable("Holdings", "Estimator", $fields, $categories);
        
        $estimatePie = PortfolioInformation_Reports_Model::GetPieFromTable();
        
        $estimateTable['TableTotals'] = PortfolioInformation_Reports_Model::GetTableTotals("Estimator", $totals);
        
        $category_totals = PortfolioInformation_Reports_Model::GetTableCategoryTotals("Estimator", $categories, $totals);
        
        PortfolioInformation_reports_model::MergeTotalsIntoCategoryRows($categories, $estimateTable, $category_totals);
        
        $report_data = array("estimate_table" => $estimateTable, "estimate_pie" => json_encode($estimatePie));
        
        return $report_data;
}


function LoadMonthlyIncomeReport($input_array){
    
    $requestParams = array();
    
    $requestParams['account_number'] = $input_array['account_number'];
    
    $request = new Vtiger_Request($requestParams, $requestParams);
    
    $monthlyReportData = array();
    
    if(strlen($request->get("account_number") > 0)){
        
        $monthly = new PortfolioInformation_MonthlyIncome_Model();
        
        $monthly->GenerateReport($request);
        
        $account = "";
        
        if(is_array($monthly->account))
            foreach($monthly->account AS $k => $v)
                $account .= "account_number[]={$v}&";
                else
                    $account = "account_number[]={$monthly->account}&";
                    
                    
                    $monthlyReportData["account"] = $account;
                    $monthlyReportData["main_categories_previous"] = $monthly->main_categories_previous;
                    $monthlyReportData["main_categories_projected"] = $monthly->main_categories_projected;
                    $monthlyReportData["sub_sub_categories_previous"] = $monthly->sub_sub_categories_previous;
                    $monthlyReportData["sub_sub_categories_projected"] = $monthly->sub_sub_categories_projected;
                    $monthlyReportData["projected_symbols"] = $monthly->individual_projected_symbols;
                    $monthlyReportData["previous_symbols"] = $monthly->individual_previous_symbols;
                    $monthlyReportData["previous_symbols_values"] = $monthly->previous_symbols;
                    $monthlyReportData["projected_symbols_values"] = $monthly->projected_symbols;
                    $monthlyReportData["previous_monthly_totals"] = $monthly->previous_monthly_totals;
                    $monthlyReportData["projected_monthly_totals"] = $monthly->projected_monthly_totals;
                    $monthlyReportData["display_months"] = $monthly->display_months;
                    $monthlyReportData["display_years_current"] = $monthly->display_years_current;
                    $monthlyReportData["display_years_projected"] = $monthly->display_years_projected;
                    $monthlyReportData["monthly_values"] = $monthly->monthly_values;
                    $monthlyReportData["monthly_totals"] = $monthly->monthly_totals;
                    $monthlyReportData["grand_total"] = $monthly->grand_total;
                    $monthlyReportData["estimate_payout"] = $monthly->estimate_payout;
                    $monthlyReportData["estimated_monthly_totals"] = $monthly->estimated_monthly_totals;
                    $monthlyReportData["estimated_grand_total"] = $monthly->estimated_grand_total;
                    $monthlyReportData["history_data"] = json_encode($monthly->history);
                    $monthlyReportData["future_data"] = json_encode($monthly->estimated_income);
                    
                    $monthlyReportData['account_number'] = $input_array['account_number'];
                    
                    $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($request->get("account_number"));
                    
                    if($crmid){
                        
                        $portfolioInformationModel = PortfolioInformation_Record_Model::getInstanceById($crmid);
                        
                        $portfolios = $portfolioInformationModel->getData();
                        
                        $monthlyReportData["portfolio_data"] = $portfolios;
                    }
                    
                    return $monthlyReportData;
    }
}

function LoadOmniOverviewReport($input_array){
    
    $account = getContactAccessibleAccounts($input_array['ID']);
    $accountIdNo = array();
    if($input_array['show_reports'] == 'Accounts')
        $accountIdNo = getContactAccessibleAccounts($input_array['accountid']);
        
        $account_number = array_merge($account,$accountIdNo);
        $account_number = array_unique($account_number);
        
        require_once("libraries/Reporting/ReportCommonFunctions.php");
        require_once("libraries/Reporting/ReportPerformance.php");
        require_once("libraries/Reporting/ReportHistorical.php");
        require_once("libraries/reports/new/holdings_report.php");
        
        global $current_user,$adb,$log;
        
        $overviewReportParams = array();
        
        $overviewReportParams['account_number'] = $account_number;
        $overviewReportParams['module'] = "PortfolioInformation";
        
        $request = new Vtiger_Request($overviewReportParams, $overviewReportParams);
        
        $accounts = $request->get("account_number");
        $accounts = array_unique($accounts);
        
        $start = date('Y-m-d', strtotime('-7 days'));
        $end = date('Y-m-d');
        
        PortfolioInformation_Module_Model::CalculateMonthlyIntervalsForAccounts($accounts);
        // 	PortfolioInformation_Module_Model::CalculateDailyIntervalsForAccounts($accounts, $start, $end);
        
        // 	$t3_performance		= new Performance_Model($accounts, GetDateMinusMonths(TRAILING_3), date("Y-m-d"));
        //     $t6_performance 	= new Performance_Model($accounts, GetDateStartOfYear(), date("Y-m-d"));
        // 	$t12_performance 	= new Performance_Model($accounts, GetDateMinusMonths(TRAILING_12), date("Y-m-d"));
        $end_date = DetermineIntervalEndDate($accounts, date('Y-m-d'));
        $t3_performance = new Performance_Model($accounts, DetermineIntervalStartDate($accounts, GetDateMinusMonths(TRAILING_3)), $end_date);
        $t6_performance = new Performance_Model($accounts, DetermineIntervalStartDate($accounts, GetDateStartOfYear()), $end_date);
        $t12_performance = new Performance_Model($accounts, DetermineIntervalStartDate($accounts, GetDateMinusMonths(TRAILING_12)), $end_date);
        
        $historical = new Historical_Model($accounts);
        $last_month = date('Y-m-d', strtotime('last day of previous month'));
        $last_year = date('Y-m-d', strtotime("{$last_month} - 1 year"));
        $t12_balances = $historical->GetEndValues($last_year, $last_month);
        
        $performance_summary = array();
        
        $performance_summary['t3']['performance_summed'] = $t3_performance->GetPerformanceSummed();
        $performance_summary['t3']['performance'] = $t3_performance->GetPerformance();
        $performance_summary['t3']['start_date'] = $t3_performance->GetStartDate();
        $performance_summary['t3']['end_date'] = $t3_performance->GetEndDate();
        $performance_summary['t3']['beginning_values'] = $t3_performance->GetBeginningValuesSummed()->value;
        $performance_summary['t3']['ending_values'] = $t3_performance->GetEndingValuesSummed()->value;
        $performance_summary['t3']['capital_appreciation'] = $t3_performance->GetCapitalAppreciation();
        $performance_summary['t3']['interval_end_date'] = $t3_performance->GetIntervalEndDate();
        $performance_summary['t3']['twr'] = $t3_performance->GetTWR();
        $performance_summary['t3']['sp500'] = $t3_performance->GetIndex("S&P 500");
        $performance_summary['t3']['agg'] = $t3_performance->GetIndex("AGG");
        
        $performance_summary['t6']['performance_summed'] = $t6_performance->GetPerformanceSummed();
        $performance_summary['t6']['performance'] = $t6_performance->GetPerformance();
        $performance_summary['t6']['start_date'] = $t6_performance->GetStartDate();
        $performance_summary['t6']['end_date'] = $t6_performance->GetEndDate();
        $performance_summary['t6']['beginning_values'] = $t6_performance->GetBeginningValuesSummed()->value;
        $performance_summary['t6']['ending_values'] = $t6_performance->GetEndingValuesSummed()->value;
        $performance_summary['t6']['capital_appreciation'] = $t6_performance->GetCapitalAppreciation();
        $performance_summary['t6']['interval_end_date'] = $t6_performance->GetIntervalEndDate();
        $performance_summary['t6']['twr'] = $t6_performance->GetTWR();
        $performance_summary['t6']['sp500'] = $t6_performance->GetIndex("S&P 500");
        $performance_summary['t6']['agg'] = $t6_performance->GetIndex("AGG");
        
        $performance_summary['t12']['performance_summed'] = $t12_performance->GetPerformanceSummed();
        $performance_summary['t12']['performance'] = $t12_performance->GetPerformance();
        $performance_summary['t12']['start_date'] = $t12_performance->GetStartDate();
        $performance_summary['t12']['end_date'] = $t12_performance->GetEndDate();
        $performance_summary['t12']['beginning_values'] = $t12_performance->GetBeginningValuesSummed()->value;
        $performance_summary['t12']['ending_values'] = $t12_performance->GetEndingValuesSummed()->value;
        $performance_summary['t12']['capital_appreciation'] = $t12_performance->GetCapitalAppreciation();
        $performance_summary['t12']['interval_end_date'] = $t12_performance->GetIntervalEndDate();
        $performance_summary['t12']['twr'] = $t12_performance->GetTWR();
        $performance_summary['t12']['sp500'] = $t12_performance->GetIndex("S&P 500");
        $performance_summary['t12']['agg'] = $t12_performance->GetIndex("AGG");
        
        $individual_summary = array();
        $individual_summary['t3']['individual_performance_summed'] = $t3_performance->GetIndividualSummedBalance();
        $individual_summary['t6']['individual_performance_summed'] = $t6_performance->GetIndividualSummedBalance();
        $individual_summary['t12']['individual_performance_summed'] = $t12_performance->GetIndividualSummedBalance();
        
        $individual_summary['t3']['begin_values'] = $t3_performance->GetIndividualBeginValues();
        $individual_summary['t6']['begin_values'] = $t6_performance->GetIndividualBeginValues();
        $individual_summary['t12']['begin_values'] = $t12_performance->GetIndividualBeginValues();
        
        $individual_summary['t3']['end_values'] = $t3_performance->GetIndividualEndValues();
        $individual_summary['t6']['end_values'] = $t6_performance->GetIndividualEndValues();
        $individual_summary['t12']['end_values'] = $t12_performance->GetIndividualEndValues();
        
        $individual_summary['t3']['appreciation'] = $t3_performance->GetIndividualCapitalAppreciation();
        $individual_summary['t6']['appreciation'] = $t6_performance->GetIndividualCapitalAppreciation();
        $individual_summary['t12']['appreciation'] = $t12_performance->GetIndividualCapitalAppreciation();
        
        $individual_summary['t3']['appreciation_percent'] = $t3_performance->GetIndividualCapitalAppreciationPercent();
        $individual_summary['t6']['appreciation_percent'] = $t6_performance->GetIndividualCapitalAppreciationPercent();
        $individual_summary['t12']['appreciation_percent'] = $t12_performance->GetIndividualCapitalAppreciationPercent();
        
        $individual_summary['t3']['twr'] = $t3_performance->GetIndividualTWR();
        $individual_summary['t6']['twr'] = $t6_performance->GetIndividualTWR();
        $individual_summary['t12']['twr'] = $t12_performance->GetIndividualTWR();
        
        $tmp = array_merge_recursive($t3_performance->GetTransactionTypes(), $t6_performance->GetTransactionTypes(), $t12_performance->GetTransactionTypes());
        
        $table = array();
        foreach($tmp AS $k => $v){
            $vals = array_unique($v);
            $table[$k] = $vals;
        }
        $tmp_end_date = date("Y-m-d", strtotime($end_date));
        if (sizeof($accounts) > 0) {
            // 		PortfolioInformation_HoldingsReport_Model::GenerateEstimateTables($accounts);
            // 		$categories = array("estimatedtype");
            // 		$fields = array("security_symbol", "account_number", "cusip", "description", "quantity", "last_price", "weight", "current_value");
            // 		$totals = array("current_value", "weight");
            // 		$estimateTable = PortfolioInformation_Reports_Model::GetTable("Holdings", "Estimator", $fields, $categories);
            // 		$estimateTable['TableTotals'] = PortfolioInformation_Reports_Model::GetTableTotals("Estimator", $totals);
            // 		$holdings_pie = PortfolioInformation_Reports_Model::GetPieFromTable();
            // 		$category_totals = PortfolioInformation_Reports_Model::GetTableCategoryTotals("Estimator", $categories, $totals);
            // 		PortfolioInformation_reports_model::MergeTotalsIntoCategoryRows($categories, $estimateTable, $category_totals);
            
            // 		global $adb;
            // 		$query = "SELECT @global_total as global_total";
            // 		$result = $adb->pquery($query, array());
            // 		if($adb->num_rows($result) > 0){
            // 			$global_total = $adb->query_result($result, 0, 'global_total');
            // 		}
            
            $unsettled_cash = PortfolioInformation_HoldingsReport_Model::GetUnsettledCashTotal($accounts);
            $margin_balance = PortfolioInformation_HoldingsReport_Model::GetMarginBalanceTotal($accounts);
            $net_credit_debit = PortfolioInformation_HoldingsReport_Model::GetNetCreditDebitTotal($accounts);
            
            PortfolioInformation_Reports_Model::GeneratePositionsValuesTable($accounts, $tmp_end_date);
            $categories = array("aclass");
            $fields = array("symbol", "security_type", "account_number", "cusip", "description", "quantity", "price", "market_value");//, "weight", "current_value");
            $totals = array("market_value");
            $estimateTable = PortfolioInformation_Reports_Model::GetTable("Holdings", "PositionValues", $fields, $categories);
            $holdings_pie = PortfolioInformation_Reports_Model::GetPieFromTable("PositionValuesPie");//"PositionValuesPie"
            $estimateTable['TableTotals'] = PortfolioInformation_Reports_Model::GetTableTotals("PositionValues", $totals);
            
            $category_totals = PortfolioInformation_Reports_Model::GetTableCategoryTotals("PositionValues", $categories, $totals);
            PortfolioInformation_reports_model::MergeTotalsIntoCategoryRows($categories, $estimateTable, $category_totals);
            
            global $adb;
            $query = "SELECT @global_total as global_total";
            $result = $adb->pquery($query, array());
            if($adb->num_rows($result) > 0) {
                $global_total = $adb->query_result($result, 0, 'global_total');
            }
        }
        
        $output = array();
        
        if(is_array($accounts)){
            $portfolios = array();
            foreach($accounts AS $k => $v) {
                $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($v);
                if($crmid) {
                    $p = PortfolioInformation_Record_Model::getInstanceById($crmid);
                    $portfolios[] = $p->getData();
                }
            }
            $output["portfolio_data"] = $portfolios;
        }
        $output["unsettled_cash"] = $unsettled_cash;
        $output["margin_balance"] = $margin_balance;
        $output["net_credit_debit"] = $net_credit_debit;
        $output["settled_total"] = $global_total+$unsettled_cash+$margin_balance+$net_credit_debit;
        
        $output["t3performance"] = $t3_performance;
        $output["t6performance"] = $t6_performance;
        $output["t12performance"] = $t12_performance;
        $output["tablecategories"] = $table;
        $output["holdingspievalues"] = json_encode($holdings_pie);
        $output["t12balances"] = json_encode($t12_balances);
        $output["account_number"] = $request->get("account_number");
        $output["performance_summary"] = $performance_summary;
        $output["individual_summary"] = $individual_summary;
        
        return $output;
}

function LoadOmniProjectedReport($input_array){
    
    $account = getContactAccessibleAccounts($input_array['ID']);
    $accountIdNo = array();
    if($input_array['show_reports'] == 'Accounts')
        $accountIdNo = getContactAccessibleAccounts($input_array['accountid']);
        
        $account_number = array_merge($account,$accountIdNo);
        $account_number = array_unique($account_number);
        
        require_once("libraries/Reporting/ReportCommonFunctions.php");
        require_once("libraries/Reporting/ProjectedIncomeModel.php");
        
        $start_date = GetDateFirstOfThisMonth();
        
        $end_date = GetDateLastOfPreviousMonthPlusOneYear();
        
        $positions = PositionInformation_Module_Model::GetPositionsForAccountNumber($account_number);
        
        foreach($positions AS $k => $v) {
            
            $crmid = ModSecurities_Module_Model::GetCrmidFromSymbol($v['security_symbol']);
            
            if($crmid > 0) {
                
                $instance = ModSecurities_Record_Model::getInstanceById($crmid);
                
                $data = $instance->getData();
                
                $returned = Date("Y-m-d", strtotime($data['last_eod']));
                
                $compared = Date("Y-m-d", strtotime("-3 months"));
                
                if ($returned <= $compared)
                    ModSecurities_ConvertCustodian_Model::UpdateSecurityFromEOD($v['security_symbol'], "US");
            }
        }
        
        $projected = new ProjectedIncome_Model($account_number);
        
        $calendar = CreateMonthlyCalendar($start_date, $end_date);
        
        $projected->CalculateMonthlyTotals($calendar);
        
        $graph = $projected->GetMonthlyIncomeGraph();
        
        $output = array();
        
        $output["individual_projected"] = $projected->GetGroupedAccounts();
        $output["monthly_total"] = $projected->GetMonthlyTotals();
        $output["projected_graph"] = json_encode($graph);
        $output["grand_total"] = $projected->GetGrandTotal();
        $output["calendar"] = $calendar;
        
        if(is_array($account_number)){
            $portfolios = array();
            foreach($account_number AS $k => $v) {
                $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($v);
                if($crmid) {
                    $p = PortfolioInformation_Record_Model::getInstanceById($crmid);
                    $portfolios[] = $p->getData();
                }
            }
            $output["portfolio_data"] = $portfolios;
        }
        return $output;
}

function LoadGHReport($input_array){
    
    $account = getContactAccessibleAccounts($input_array['ID']);
    $accountIdNo = array();
    if($input_array['show_reports'] == 'Accounts')
        $accountIdNo = getContactAccessibleAccounts($input_array['accountid']);
        
        $account_number = array_merge($account,$accountIdNo);
        $account_number = array_unique($account_number);
        
        require_once("libraries/Reporting/ReportCommonFunctions.php");
        require_once("libraries/Reporting/ReportPerformance.php");
        require_once("libraries/Reporting/ReportHistorical.php");
        require_once("libraries/reports/new/holdings_report.php");
        
        if(isset($input_array['report_start_date'])) {
            $start_date = $input_array['report_start_date'];
        } else {
            $start_date = PortfolioInformation_Module_Model::ReportValueToDate("2017", false)['start'];
        }
        
        if(isset($input_array['report_end_date'])){
            $end_date = $input_array['report_end_date'];
        } else {
            $end_date = PortfolioInformation_Module_Model::ReportValueToDate("2017", false)['end'];
        }
        
        if(!is_array($account_number))
            $accounts = explode(",",$account_number);
            else {
                $accounts = $account_number;
            }
            $accounts = array_unique($accounts);
            
            $calling_record = $input_array['calling_record'];
            
            $tmp_start_date = date("Y-m-d", strtotime("first day of " . $start_date));
            $tmp_end_date = date("Y-m-d", strtotime("last day of " . $end_date));
            
            $start_date = date("F Y", strtotime($start_date));
            $end_date = date("F Y", strtotime($end_date));
            
            PortfolioInformation_Module_Model::CalculateMonthlyIntervalsForAccounts($accounts);
            
            $ytd_performance = new Performance_Model($accounts, $tmp_start_date, $tmp_end_date);
            
            if (sizeof($accounts) > 0) {
                PortfolioInformation_HoldingsReport_Model::GenerateEstimateTables($accounts);
                $categories = array("estimatedtype");
                $fields = array("security_symbol", "account_number", "cusip", "description", "quantity", "last_price", "weight", "current_value");
                $totals = array("current_value", "weight");
                $estimateTable = PortfolioInformation_Reports_Model::GetTable("Holdings", "Estimator", $fields, $categories);
                $estimateTable['TableTotals'] = PortfolioInformation_Reports_Model::GetTableTotals("Estimator", $totals);
                $holdings_pie = PortfolioInformation_Reports_Model::GetPieFromTable();
                
                PortfolioInformation_Reports_Model::GeneratePositionsValuesTable($accounts, $tmp_end_date);
                $new_pie = PortfolioInformation_Reports_Model::GetPositionValuesPie();
                $category_totals = PortfolioInformation_Reports_Model::GetTableCategoryTotals("Estimator", $categories, $totals);
                PortfolioInformation_reports_model::MergeTotalsIntoCategoryRows($categories, $estimateTable, $category_totals);
                
                global $adb;
                $query = "SELECT @global_total as global_total";
                $result = $adb->pquery($query, array());
                if($adb->num_rows($result) > 0){
                    $global_total = $adb->query_result($result, 0, 'global_total');
                }
            }
            
            $unsettled_cash = PortfolioInformation_HoldingsReport_Model::GetFidelityFieldTotalAsOfDate($accounts, "unsettled_cash", $tmp_end_date);
            $margin_balance = PortfolioInformation_HoldingsReport_Model::GetFidelityFieldTotalAsOfDate($accounts, "margin_balance", $tmp_end_date);
            $net_credit_debit = PortfolioInformation_HoldingsReport_Model::GetFidelityFieldTotalAsOfDate($accounts, "net_credit_debit", $tmp_end_date);
            
            $options = PortfolioInformation_Module_Model::GetReportSelectionOptions("gh_report");
            
            $tmp = $ytd_performance->ConvertPieToBenchmark($new_pie);
            $ytd_performance->SetBenchmark($tmp['Stocks'], $tmp['Cash'], $tmp['Bonds']);
            
            $output = array();
            
            if(is_array($accounts)){
                $portfolios = array();
                foreach($accounts AS $k => $v) {
                    $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($v);
                    if($crmid) {
                        $p = PortfolioInformation_Record_Model::getInstanceById($crmid);
                        $portfolios[] = $p->getData();
                    }
                }
                $output["portfolio_data"] = $portfolios;
            }
            
            if($calling_record) {
                $prepared_for = PortfolioInformation_Module_Model::GetPreparedForNameByRecordID($calling_record);
                $record = VTiger_Record_Model::getInstanceById($calling_record);
                $data = $record->getData();
                $module = $record->getModule();
                if($module->getName() == "Accounts") {
                    $policy = $data['cf_2525'];
                    $output["policy"] = $policy;
                }
            }
            
            $output["ytdperformance"] = $ytd_performance;
            $output["holdingspievalues"] = json_encode($new_pie);
            $output["holdingspiearray"] = $new_pie;
            $output["globaltotal"] = $global_total;
            $output["unsettled_cash"] = $unsettled_cash;
            $output["settled_total"] = $global_total+$unsettled_cash;
            $output["date_options"] = $options;
            $output["show_start_date"] = 1;
            $output["show_end_date"] = 1;
            $output["start_date"] = $start_date;
            $output["end_date"] = $end_date;
            $output['ytd_individual_performance_summed'] = $ytd_performance->GetIndividualSummedBalance();
            $output['ytd_begin_values'] = $ytd_performance->GetIndividualBeginValues();
            $output['ytd_end_values'] = $ytd_performance->GetIndividualEndValues();
            $output['ytd_appreciation'] = $ytd_performance->GetIndividualCapitalAppreciation();
            $output['ytd_appreciation_percent'] = $ytd_performance->GetIndividualCapitalAppreciationPercent();
            $output['ytd_twr'] = $ytd_performance->GetIndividualTWR();
            $output['ytd_performance_summed'] = $ytd_performance->GetPerformanceSummed();
            $output['GetDividendAccrualAmount'] = $ytd_performance->GetDividendAccrualAmount();
            $output['GetStartDate'] = $ytd_performance->GetStartDate();
            $output['GetEndDate'] = $ytd_performance->GetEndDate();
            $output['GetBeginningValuesSummed'] = $ytd_performance->GetBeginningValuesSummed();
            $output['GetEndingValuesSummed'] = $ytd_performance->GetEndingValuesSummed();
            $output['GetBenchmark'] = $ytd_performance->GetBenchmark();
            $output['GetIndexSP'] = $ytd_performance->GetIndex("S&P 500");
            $output['GetIndexAGG'] = $ytd_performance->GetIndex("AGG");
            $output['GetIndexEEM'] = $ytd_performance->GetIndex("EEM");
            $output['GetIndexMSCI_EAFE'] = $ytd_performance->GetIndex("MSCI_EAFE");
            $output['GetTWR'] = $ytd_performance->GetTWR();
            
            if(isset($input_array['selectedDate']))
                $output['selectedDate'] = $input_array['selectedDate'];
                
                return $output;
}

function LoadGH2Report($input_array){
    
    $account = getContactAccessibleAccounts($input_array['ID']);
    $accountIdNo = array();
    if($input_array['show_reports'] == 'Accounts')
        $accountIdNo = getContactAccessibleAccounts($input_array['accountid']);
        
        $account_number = array_merge($account,$accountIdNo);
        $account_number = array_unique($account_number);
        
        require_once("libraries/Reporting/ReportCommonFunctions.php");
        require_once("libraries/Reporting/ReportPerformance.php");
        require_once("libraries/Reporting/ReportHistorical.php");
        require_once("libraries/reports/new/holdings_report.php");
        
        if(!is_array($account_number))
            $accounts = explode(",", $account_number);
            else {
                $accounts = $account_number;
            }
            $accounts = array_unique($accounts);
            
            if(isset($input_array['report_start_date'])) {
                $start_date = $input_array['report_start_date'];
            }
            else {
                $start_date = PortfolioInformation_Module_Model::ReportValueToDate("2018", false)['start'];
            }
            
            if(isset($input_array['report_end_date'])) {
                $end_date = $input_array['report_end_date'];
            }
            else {
                $end_date = PortfolioInformation_Module_Model::ReportValueToDate("2018", false)['end'];
            }
            
            $tmp_start_date = date("Y-m-d", strtotime("first day of " . $start_date));
            $tmp_end_date = date("Y-m-d", strtotime("last day of " . $end_date));
            
            $start_date = date("F Y", strtotime($start_date));
            $end_date = date("F Y", strtotime($end_date));
            
            PortfolioInformation_Module_Model::CalculateMonthlyIntervalsForAccounts($accounts);
            
            $ytd_performance = new Performance_Model($accounts, $tmp_start_date, $tmp_end_date);
            
            if (sizeof($accounts) > 0) {
                PortfolioInformation_Reports_Model::GeneratePositionsValuesTable($accounts, $tmp_end_date);
                $new_pie = PortfolioInformation_Reports_Model::GetPositionValuesPie();
                $sector_pie = PortfolioInformation_Reports_Model::GetPositionSectorsPie();
                
                global $adb;
                $query = "SELECT @global_total as global_total";
                $result = $adb->pquery($query, array());
                if($adb->num_rows($result) > 0){
                    $global_total = $adb->query_result($result, 0, 'global_total');
                }
            };
            
            $unsettled_cash = PortfolioInformation_HoldingsReport_Model::GetFidelityFieldTotalAsOfDate($accounts, "unsettled_cash", $tmp_end_date);
            $margin_balance = PortfolioInformation_HoldingsReport_Model::GetFidelityFieldTotalAsOfDate($accounts, "margin_balance", $tmp_end_date);
            $net_credit_debit = PortfolioInformation_HoldingsReport_Model::GetFidelityFieldTotalAsOfDate($accounts, "net_credit_debit", $tmp_end_date);
            $options = PortfolioInformation_Module_Model::GetReportSelectionOptions("gh2_report");
            
            $tmp = $ytd_performance->ConvertPieToBenchmark($new_pie);
            $ytd_performance->SetBenchmark($tmp['Stocks'], $tmp['Cash'], $tmp['Bonds']);
            
            $output["today"] = date("M d, Y");
            $output["ytdperformance"] = $ytd_performance;
            $output["holdingspievalues"] = json_encode($new_pie);
            $output["holdingssectorpiestring"] = json_encode($sector_pie);
            $output["holdingssectorpiearray"] = $sector_pie;
            $output["holdingspiearray"] = $new_pie;
            $output["positions"] = $positions;
            $output["globaltotal"] = $global_total;
            $output["unsettled_cash"] = $unsettled_cash;
            $output["settled_total"] = $global_total+$unsettled_cash;
            $output["date_options"] = $options;
            $output["show_start_date"] = 1;
            $output["show_end_date"] = 1;
            $output["start_date"] = $start_date;
            $output["end_date"] = $end_date;
            $output['ytd_individual_performance_summed'] = $ytd_performance->GetIndividualSummedBalance();
            $output['ytd_begin_values'] = $ytd_performance->GetIndividualBeginValues();
            $output['ytd_end_values'] = $ytd_performance->GetIndividualEndValues();
            $output['ytd_appreciation'] = $ytd_performance->GetIndividualCapitalAppreciation();
            $output['ytd_appreciation_percent'] = $ytd_performance->GetIndividualCapitalAppreciationPercent();
            $output['ytd_twr'] = $ytd_performance->GetIndividualTWR();
            $output['ytd_performance_summed'] = $ytd_performance->GetPerformanceSummed();
            $output['dividendAmount'] = $ytd_performance->GetDividendAccrualAmount();
            $output['GetStartDate'] = $ytd_performance->GetStartDate();
            $output['GetEndDate'] = $ytd_performance->GetEndDate();
            $output['GetBeginningValuesSummed'] = $ytd_performance->GetBeginningValuesSummed();
            $output['GetEndingValuesSummed'] = $ytd_performance->GetEndingValuesSummed();
            $output['GetCapitalAppreciation'] = $ytd_performance->GetCapitalAppreciation();
            $output['GetTWR'] = $ytd_performance->GetTWR();
            $output['GetIndexSP'] = $ytd_performance->GetIndex("S&P 500");
            $output['GetIndexAGG'] = $ytd_performance->GetIndex("AGG");
            $output['GetIndexEEM'] = $ytd_performance->GetIndex("EEM");
            $output['GetIndexMSCI_EAFE'] = $ytd_performance->GetIndex("MSCI_EAFE");
            $output['GetBenchmark'] = $ytd_performance->GetBenchmark();
            
            if(isset($input_array['selectedDate']))
                $output['selectedDate'] = $input_array['selectedDate'];
                
                return $output;
}

function getDocumentFolderWithParentList($elementId,$folderId,$index,$emptyFolder ) {
    
    $db = PearDatabase::getInstance();
    
    global $current_user;
    
    $foldersQuery = $db->pquery("SELECT DISTINCT vtiger_documentfolder.documentfolderid, vtiger_documentfolder.folder_name,
    vtiger_documentfolder.parent_id
    FROM vtiger_notes
        
	INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_notes.notesid
    INNER JOIN vtiger_documentfolder ON vtiger_documentfolder.documentfolderid = vtiger_notes.doc_folder_id
    INNER JOIN vtiger_senotesrel ON vtiger_senotesrel.notesid = vtiger_notes.notesid
        
	WHERE vtiger_crmentity.deleted = 0
    AND (vtiger_notes.is_private != 1 OR vtiger_notes.is_private IS NULL)
    AND (vtiger_documentfolder.hide_from_portal != 1 OR vtiger_documentfolder.hide_from_portal IS NULL )
    AND vtiger_senotesrel.crmid = ? AND vtiger_documentfolder.parent_id = ? ", array($elementId,$folderId));
    
    $folders = array();
    
    $folderIds = array();
    
    $foldersData = array();
    
    if($db->num_rows($foldersQuery)){
        
        for($i=0;$i<$db->num_rows($foldersQuery);$i++){
            
            $folderIds[] = $db->query_result($foldersQuery,$i,'documentfolderid');
            
            $folder_id = $db->query_result($foldersQuery, $i, 'documentfolderid');
            
            $folderName = $db->query_result($foldersQuery, $i, 'folder_name');
            
            $parent_id = $db->query_result($foldersQuery, $i, 'parent_id');
            
            $folders[$folder_id] =  array(
                "id"=>$folder_id,
                "parent_id"=>$parent_id,
                "text"=>$folderName,
                "type"=>"folder",
            );
            
        }
        
    }
    
    if($emptyFolder == 'true'){
        $moduleName = "DocumentFolder";
        
        $currentUserModel = Users_Record_Model::getInstanceFromPreferenceFile($current_user->id);
        
        $queryGenerator = new QueryGenerator($moduleName, $currentUserModel);
        
        $queryGenerator->setFields( array('folder_name','id', 'parent_id') );
        
        $listviewController = new ListViewController($db, $currentUserModel, $queryGenerator);
        
        $query = $queryGenerator->getQuery();
        
        $query .= " AND vtiger_documentfolder.hide_from_portal != 1 AND vtiger_documentfolder.parent_id = ?
         AND vtiger_documentfolder.documentfolderid  NOT IN (".implode(',',$folderIds).") ";
        
        $pos = strpos($query, "SELECT");
        if ($pos !== false) {
            $query = substr_replace($query, "SELECT DISTINCT vtiger_documentfolder.documentfolderid, ", $pos, strlen("SELECT"));
        }
        
        $result = $db->pquery($query,array($folderId));
        
        $rows = $db->num_rows($result);
        
        for($i=0; $i<$rows; $i++){
            $folder_id = $db->query_result($result, $i, 'documentfolderid');
            $folderName = $db->query_result($result, $i, 'folder_name');
            $parent_id = $db->query_result($result, $i, 'parent_id');
            if($parent_id){
                $folders[$folder_id] =  array(
                    "id"=>$folder_id,
                    "parent_id"=>$parent_id,
                    "text"=>$folderName,
                    "type"=>"folder",
                );
            }
        }
    }
    
    $folderFiles = folderFiles($elementId, $folderId, $index);
    
    $folders = array_merge($folders,$folderFiles);
    
    return $folders;
}

function folderFiles($elementId, $folderId, $startIndex){
    
    $folders = array();
    $db = PearDatabase::getInstance();
    
    $query = "select * from vtiger_notes
            inner JOIN vtiger_crmentity on vtiger_crmentity.crmid=vtiger_notes.notesid
			inner join vtiger_senotesrel on vtiger_senotesrel.notesid = vtiger_notes.notesid
			";
    $query.= "where vtiger_crmentity.deleted=0 ";
    $query.=" AND (vtiger_notes.is_private != 1 OR vtiger_notes.is_private IS NULL) ";
    //     $query.="  AND (vtiger_crmentity.smcreatorid = ? OR vtiger_crmentity.smownerid = ?) ";
    $query.= " AND vtiger_notes.doc_folder_id = ? and vtiger_senotesrel.crmid = ? LIMIT ".$startIndex.",50";
    $result = $db->pquery($query,array($folderId, $elementId));
    //     $_SESSION['ownerId'],$_SESSION['ownerId'],
    
    $rows = $db->num_rows($result);
    for($i=0; $i<$rows; $i++){
        $docId = $db->query_result($result, $i, 'notesid');
        $docName = $db->query_result($result, $i, 'title');
        $loctype = $db->query_result($result, $i, 'filelocationtype');
        $fileName = $db->query_result($result, $i, 'filename');
        $file = explode('/',$db->query_result($result, $i, 'filetype'));
        
        if($file[0] == 'image'){
            $icon = 'img.jpg';
            $fileType = 'image File';
        }else if($file[0] == 'video'){
            $icon = 'video.jpg';
            $fileType = 'video File';
        }else if($file[0] == 'text'){
            $icon = 'docx.jpg';
            $fileType = 'text File';
        }else if($file[1] == 'pdf'){
            $icon = 'pdf.jpg';
            $fileType = 'pdf File';
        }else if($file[1] == 'zip'){
            $icon = 'zip.jpg';
            $fileType = 'zip File';
        }else if(strpos($file[1], 'ms')!== false || strpos($file[1], 'vnd') !== false){
            $icon = 'office.jpg';
            $fileType = 'office File';
        }else {
            $icon = 'txt.jpg';
            $fileType = 'doc File';
            if($loctype == 'E')
                $fileType = 'external File';
        }
        
        $folders[$docId] =  array(
            "id"=>$docId,
            "parent_id"=>$folderId,
            "text"=>$docName,
            "type"=>"file",
            "icon"=> $icon,
            "fileType"=>$fileType,
            "fileLocation"=>$loctype,
            "fileName"=>$fileName,
        );
    }
    
    return $folders;
}

function LoadMonthOverMonth($input_array){
    
    $account = getContactAccessibleAccounts($input_array['ID']);
    $accountIdNo = array();
    if($input_array['show_reports'] == 'Accounts')
        $accountIdNo = getContactAccessibleAccounts($input_array['accountid']);
        
        $account_number = array_merge($account,$accountIdNo);
        $account_number = array_unique($account_number);
        
        $overviewReportParams = array();
        
        $overviewReportParams['account_number'] = $account_number;
        $overviewReportParams['module'] = "PortfolioInformation";
        $overviewReportParams['calling_record'] = $input_array['ID'];
        
        $request = new Vtiger_Request($overviewReportParams, $overviewReportParams);
        
        $calling_record = $request->get('calling_record');
        
        if($request->get('calling_record')) {
            $prepared_for = PortfolioInformation_Module_Model::GetPreparedForNameByRecordID($calling_record);
            $prepared_by = PortfolioInformation_Module_Model::GetPreparedByNameByRecordID($calling_record);
            $calling_instance = Vtiger_Record_Model::getInstanceById($request->get('calling_record'));
            $advisor_instance = Users_Record_Model::getInstanceById($calling_instance->get('assigned_user_id'), "Users");
            $assigned_to = getGroupName($calling_instance->get('assigned_user_id'));
            if(sizeof($assigned_to) == 0)
                $assigned_to = GetUserFirstLastNameByID($calling_instance->get('assigned_user_id'), true);
        }
        
        if(is_array($assigned_to))
            $assigned_to = $assigned_to[0];
            
            $moduleName = $request->getModule();
            $account_number = $request->get("account_number");
            
            $total_weight = 0;
            if(!is_array($account_number))
                $accounts = explode(",", $request->get("account_number"));
                else {
                    $accounts = $account_number;
                }
                $accounts = array_unique($accounts);
                if (sizeof($accounts) > 0) {
                    $mom_table = PortfolioInformation_MonthOverMonth_Model::GenerateMonthOverMonthTable($accounts, "Income");
                    $dow_prices = PortfolioInformation_MonthOverMonth_Model::GetMonthEndPrices("DJI");
                    $years = PortfolioInformation_MonthOverMonth_Model::GetMonthOverMonthYears();
                };
                
                $contact_instance = null;
                if(is_array($accounts)){
                    $portfolios = array();
                    $unsettled_cash = 0;
                    foreach($accounts AS $k => $v) {
                        $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($v);
                        if($crmid) {
                            $p = PortfolioInformation_Record_Model::getInstanceById($crmid);
                            $contact_id = $p->get('contact_link');
                            if ($contact_id)
                                $contact_instance[$p->get('account_number')] = Contacts_Record_Model::getInstanceById($contact_id);
                                
                                $portfolios[] = $p->getData();
                                $unsettled_cash += $p->get('unsettled_cash');
                                if (!$advisor_instance) {
                                    echo "NO INSTANCE!";
                                    $advisor_instance = Users_Record_Model::getInstanceById($p->get('assigned_user_id'), "Users");
                                }
                        }
                    }
                }
                
                if($contact_instance) {//If there is a contact instance to do anything with
                    if(!$advisor_instance)
                        $advisor_instance = Users_Record_Model::getInstanceById(reset($contact_instance)->get('assigned_user_id'), "Users");
                        
                        $household_instance = null;
                        if (reset($contact_instance)->get('account_id'))
                            $household_instance = Users_Record_Model::getInstanceById(reset($contact_instance)->get('account_id'));
                }
                
                $current_user = Users_Record_Model::getCurrentUserModel();
                $data = $advisor_instance->getData();
                $has_advisor = 0;
                
                if(strlen($data['user_name']) > 0)
                    $has_advisor = 1;
                    
                    $toc = array();
                    $output = array();
                    $toc[] = array("title" => "#1", "name" => "Accounts Overview");
                    $toc[] = array("title" => "#2", "name" => "Month Over Month");
                    
                    $output["DATE"] = date("F d, Y");
                    $output["ASSIGNED_TO"] = $assigned_to;
                    $output["HAS_ADVISOR"] = $has_advisor;
                    $output["CONTACTS"] = $contact_instance;
                    $output["REPORT_TYPE"] = "Client Statement";
                    $output["CURRENT_USER"] = $current_user;
                    $output["ADVISOR"] = $advisor_instance;
                    $output["HOUSEHOLD"] = $household_instance;
                    $output["USER_DATA"] = $current_user->getData();
                    $output["NUM_ACCOUNTS_USED"] = sizeof($accounts);
                    $output["PORTFOLIO_DATA"] = $portfolios;
                    $output["UNSETTLED_CASH"] = $unsettled_cash;
                    $output["TOTAL_WEIGHT"] = $total_weight;
                    $output["CALLING_RECORD"] = $request->get('calling_record');
                    $output["TOC"] = $toc;
                    $output["ACCOUNT_NUMBER"] = json_encode($accounts);
                    $output["MOM_TABLE"] = $mom_table;
                    $output["DOW_PRICES"] = $dow_prices;
                    $output["YEARS"] = $years;
                    $output["PREPARED_FOR"] = $prepared_for;
                    $output["PREPARED_BY"] = $prepared_by;
                    $output["MODULE"] = "PortfolioInformation";
                    
                    $output["RANDOM"] = rand(1,100000);
                    
                    return $output;
}

function LoadOmniIncome($input_array){
    
    require_once("libraries/Reporting/ReportCommonFunctions.php");
    require_once("libraries/Reporting/ReportIncome.php");
    
    $account = getContactAccessibleAccounts($input_array['ID']);
    $accountIdNo = array();
    if($input_array['show_reports'] == 'Accounts')
        $accountIdNo = getContactAccessibleAccounts($input_array['accountid']);
        
        $account_number = array_merge($account,$accountIdNo);
        $account_number = array_unique($account_number);
        
        
        $overviewReportParams = array();
        
        $overviewReportParams['account_number'] = $account_number;
        $overviewReportParams['calling_module'] = "PortfolioInformation";
        $overviewReportParams['calling_record'] = $input_array['ID'];
        
        $request = new Vtiger_Request($overviewReportParams, $overviewReportParams);
        
        $calling_module = $request->get('calling_module');
        $calling_record = $request->get('calling_record');
        $output = array();
        if(strlen($request->get("account_number") > 0) || strlen($calling_module) >= 0){
            
            $accounts = array_unique($account_number);
            
            $income = new Income_Model($accounts);
            $individual = $income->GetIndividualIncomeForDates(GetFirstDayThisMonthLastYear(), GetLastDayLastMonth());
            $monthly = $income->GetMonthlyTotalForDates(GetFirstDayThisMonthLastYear(), GetLastDayLastMonth());
            $graph = $income->GenerateGraphForDates(GetFirstDayThisMonthLastYear(), GetLastDayLastMonth());
            $combined = $income->GetCombinedSymbolsForDates(GetFirstDayThisMonthLastYear(), GetLastDayLastMonth());
            $year_end_totals = $income->CalculateCombineSymbolsYearEndToal(GetFirstDayThisMonthLastYear(), GetLastDayLastMonth());
            $grand_total = $income->CalculateGrandTotal(GetFirstDayThisMonthLastYear(), GetLastDayLastMonth());
            
            $start_month = date("F, Y", strtotime(GetFirstDayThisMonthLastYear()));
            $end_month = date("F, Y", strtotime(GetLastDayLastMonth()));
            
            $output["START_MONTH"] = $start_month;
            $output["END_MONTH"] = $end_month;
            $output["MONTHLY_TOTALS"] = $monthly;
            $output["COMBINED_SYMBOLS"] = $combined;
            $output["YEAR_END_TOTALS"] = $year_end_totals;
            $output["GRAND_TOTAL"] = $grand_total;
            $output["DYNAMIC_GRAPH"] = json_encode($graph);
            
            
        }
        return $output;
}

function LoadAssetClassReport($input_array){
    require_once("libraries/Reporting/ReportCommonFunctions.php");
    
    $account = getContactAccessibleAccounts($input_array['ID']);
    $accountIdNo = array();
    if($input_array['show_reports'] == 'Accounts')
        $accountIdNo = getContactAccessibleAccounts($input_array['accountid']);
        
        $account_number = array_merge($account,$accountIdNo);
        $account_number = array_unique($account_number);
        
        $overviewReportParams = array();
        
        $overviewReportParams['account_number'] = $account_number;
        $overviewReportParams['calling_module'] = "PortfolioInformation";
        $overviewReportParams['calling_record'] = $input_array['ID'];
        $overviewReportParams['report_end_date'] = $input_array['report_end_date'];
        
        $request = new Vtiger_Request($overviewReportParams, $overviewReportParams);
        
        $calling_record = $request->get('calling_record');
        if($request->get('calling_record')) {
            $calling_instance = Vtiger_Record_Model::getInstanceById($request->get('calling_record'));
            $advisor_instance = Users_Record_Model::getInstanceById($calling_instance->get('assigned_user_id'), "Users");
            $assigned_to = getGroupName($calling_instance->get('assigned_user_id'));
            if(sizeof($assigned_to) == 0)
                $assigned_to = GetUserFirstLastNameByID($calling_instance->get('assigned_user_id'), true);
        }
        
        if(is_array($assigned_to))
            $assigned_to = $assigned_to[0];
            
            $account_number = $request->get("account_number");
            
            $total_weight = 0;
            if(!is_array($account_number))
                $accounts = explode(",", $request->get("account_number"));
                else {
                    $accounts = $account_number;
                }
                $accounts = array_unique($accounts);
                
                if(strlen($request->get('report_end_date')) > 1) {
                    $end_date = $request->get("report_end_date");
                }
                else {
                    $end_date = PortfolioInformation_Module_Model::ReportValueToDate("current")['end'];
                }
                
                $tmp_end_date = date("Y-m-d", strtotime($end_date));
                if (sizeof($accounts) > 0) {
                    PortfolioInformation_Reports_Model::GeneratePositionsValuesTable($accounts, $tmp_end_date);
                    $categories = array("aclass");
                    $fields = array("symbol", "security_type", "account_number", "cusip", "description", "quantity", "price", "market_value");//, "weight", "current_value");
                    $totals = array("market_value");
                    $estimateTable = PortfolioInformation_Reports_Model::GetTable("Holdings", "PositionValues", $fields, $categories);
                    $estimatePie = PortfolioInformation_Reports_Model::GetPieFromTable("PositionValuesPie");
                    $estimateTable['TableTotals'] = PortfolioInformation_Reports_Model::GetTableTotals("PositionValues", $totals);
                    
                    $category_totals = PortfolioInformation_Reports_Model::GetTableCategoryTotals("PositionValues", $categories, $totals);
                    PortfolioInformation_reports_model::MergeTotalsIntoCategoryRows($categories, $estimateTable, $category_totals);
                    
                    global $adb;
                    $query = "SELECT @global_total as global_total";
                    $result = $adb->pquery($query, array());
                    if($adb->num_rows($result) > 0) {
                        $global_total = $adb->query_result($result, 0, 'global_total');
                    }
                    /*
                     PortfolioInformation_HoldingsReport_Model::GenerateAssetClassTables($accounts);
                     $categories = array("aclass");
                     $fields = array("security_symbol", "account_number", "cusip", "description", "quantity", "last_price", "weight", "current_value");
                     $totals = array("current_value", "weight");
                     $estimateTable = PortfolioInformation_Reports_Model::GetTable("Holdings", "Estimator", $fields, $categories);
                     $estimatePie = PortfolioInformation_Reports_Model::GetPieFromTable();
                     $estimateTable['TableTotals'] = PortfolioInformation_Reports_Model::GetTableTotals("Estimator", $totals);
                     #            print_r($estimateTable['table_categories']);
                     #            echo "<br /><br />";
                     $category_totals = PortfolioInformation_Reports_Model::GetTableCategoryTotals("Estimator", $categories, $totals);
                     PortfolioInformation_reports_model::MergeTotalsIntoCategoryRows($categories, $estimateTable, $category_totals);
                     
                     global $adb;
                     $query = "SELECT @global_total as global_total";
                     $result = $adb->pquery($query, array());
                     if($adb->num_rows($result) > 0){
                     $global_total = $adb->query_result($result, 0, 'global_total');
                     }*/
                };
                
                $contact_instance = null;
                $custodian = null;
                if(is_array($accounts)){
                    $portfolios = array();
                    $unsettled_cash = 0;
                    foreach($accounts AS $k => $v) {
                        $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($v);
                        if($crmid) {
                            $p = PortfolioInformation_Record_Model::getInstanceById($crmid);
                            $contact_id = $p->get('contact_link');
                            if ($contact_id)
                                $contact_instance[$p->get('account_number')] = Contacts_Record_Model::getInstanceById($contact_id);
                                
                                $portfolios[] = $p->getData();
                                $unsettled_cash += $p->get('unsettled_cash');
                                if (!$advisor_instance) {
                                    echo "NO INSTANCE!";
                                    $advisor_instance = Users_Record_Model::getInstanceById($p->get('assigned_user_id'), "Users");
                                }
                        }
                        
                        $custodian = $p->get('origination');
                    }
                }
                
                if($contact_instance) {//If there is a contact instance to do anything with
                    if(!$advisor_instance)
                        $advisor_instance = Users_Record_Model::getInstanceById(reset($contact_instance)->get('assigned_user_id'), "Users");
                        
                        $household_instance = null;
                        if (reset($contact_instance)->get('account_id'))
                            $household_instance = Users_Record_Model::getInstanceById(reset($contact_instance)->get('account_id'));
                }
                
                $account_info = PortfolioInformation_Module_Model::GetAccountIndividualTotals($accounts);
                $account_info_total = PortfolioInformation_module_Model::GetAccountSumTotals($accounts);
                
                $mailing_info = PortfolioInformation_Reports_Model::GetMailingInformationForAccount($moduleName, $accounts);
                
                $colors = PortfolioInformation_Module_Model::GetAllChartColors();
                $current_user = Users_Record_Model::getCurrentUserModel();
                $trailing_aum = PortfolioInformation_HistoricalInformation_Model::GetTrailing12AUM($accounts);
                $trailing_revenue = PortfolioInformation_HistoricalInformation_Model::GetTrailing12Revenue($accounts);
                
                $options = PortfolioInformation_Module_Model::GetReportSelectionOptions("asset_allocation");
                
                $data = $advisor_instance->getData();
                $has_advisor = 0;
                if(strlen($data['user_name']) > 0)
                    $has_advisor = 1;
                    
                    $unsettled_cash = PortfolioInformation_HoldingsReport_Model::GetCustodianTotalAsOfDate($custodian, $accounts, "unsettled_cash", $tmp_end_date);
                    $margin_balance = PortfolioInformation_HoldingsReport_Model::GetCustodianTotalAsOfDate($custodian, $accounts, "margin_balance", $tmp_end_date);
                    $net_credit_debit = PortfolioInformation_HoldingsReport_Model::GetCustodianTotalAsOfDate($custodian, $accounts, "net_credit_debit", $tmp_end_date);
                    
                    $toc = array();
                    $toc[] = array("title" => "#1", "name" => "Accounts Overview");
                    $toc[] = array("title" => "#2", "name" => "Asset Allocation");
                    
                    $output["UNSETTLED_CASH"] = $unsettled_cash;
                    $output["MARGIN_BALANCE"] = $margin_balance;
                    $output["NET_CREDIT_DEBIT"] = $net_credit_debit;
                    
                    $output["DATE"] = date("F d, Y");
                    $output["DATE_OPTIONS"] = $options;
                    $output["SHOW_END_DATE"] = 1;;
                    $output["END_DATE"] = $end_date;
                    $output["CATEGORY_TOTALS"] = $category_totals;
                    $output["ESTIMATE_TABLE"] = $estimateTable;
                    $output["DYNAMIC_PIE"] = json_encode($estimatePie);
                    $output["GLOBAL_TOTAL"] = array("global_total" => $global_total);
                    $output["TRAILING_AUM"] = json_encode($trailing_aum);
                    $output["TRAILING_REVENUE"] = json_encode($trailing_revenue);
                    $output["RANDOM"] = rand(1,100000);
                    
                    return $output;
}
function LoadGainLoss($input_array){
    
    require_once("libraries/Reporting/ReportCommonFunctions.php");
    $overviewReportParams = array();
    
    $account = getContactAccessibleAccounts($input_array['ID']);
    $accountIdNo = array();
    if($input_array['show_reports'] == 'Accounts')
        $accountIdNo = getContactAccessibleAccounts($input_array['accountid']);
        
        $account_number = array_merge($account,$accountIdNo);
        $account_number = array_unique($account_number);
        
        $overviewReportParams['account_number'] = $account_number;
        $overviewReportParams['calling_module'] = "PortfolioInformation";
        $overviewReportParams['calling_record'] = $input_array['ID'];
        
        $request = new Vtiger_Request($overviewReportParams, $overviewReportParams);
        
        $calling_module = $request->get('calling_module');
        $calling_record = $request->get('calling_record');
        $output = array();
        
        if(strlen($request->get("account_number") > 0)){
            
            $accounts = $request->get("account_number");
            $accounts = array_unique($accounts);
            
            foreach($accounts AS $k => $v){
                PortfolioInformation_Module_Model::AutoGenerateTransactionsForGainLossReport($v);
            }
            PortfolioInformation_GainLoss_Model::CreateGainLossTables($accounts);
            
            $categories = array("security_symbol");
            $fields = array('description', 'trade_date', "quantity", 'position_current_value', 'net_amount', 'ugl', 'ugl_percent', 'days_held');//, 'system_generated');//, "weight", "current_value");
            $totals = array("quantity", "net_amount", "position_current_value", "ugl");//Totals needs to have the same names as the fields to show up properly!!!
            $hidden_row_fields = array("description");//We don't want description showing on every row, just the category row
            $comparison_table = PortfolioInformation_Reports_Model::GetTable("Positions", "TEMPORARY_TRANSACTIONS", $fields, $categories, $hidden_row_fields);
            
            $comparison_table['TableTotals'] = PortfolioInformation_Reports_Model::GetTableTotals("COMPARISON", $totals);
            
            $add_on_fields = array("description", "ugl", "ugl_percent");
            $category_totals = PortfolioInformation_Reports_Model::GetTableCategoryTotals("COMPARISON", $categories, $totals, $add_on_fields);
            
            PortfolioInformation_reports_model::MergeTotalsIntoCategoryRows($categories, $comparison_table, $category_totals);
            
            $output["COMPARISON_TABLE"] = $comparison_table;
            $output["ACCOUNT_NUMBER"] = $request->get("account_number");
            $output["CALLING_RECORD"] = $calling_record;
            
        }
        return $output;
}

function LoadOmniIntervals($account_number){
    
    $overviewReportParams = array();
    
    $overviewReportParams['account_number'] = $account_number;
    $overviewReportParams['calling_module'] = "PortfolioInformation";
    $overviewReportParams['calling_record'] = $_SESSION['ID'];
    
    $request = new Vtiger_Request($overviewReportParams, $overviewReportParams);
    
    $module = $request->get('calling_module');
    $calling_record = $request->get('calling_record');
    $account_numbers = $request->get('account_number');
    
    $accounts = explode(",", $account_numbers);
    
    $accounts = PortfolioInformation_Module_Model::ReturnValidAccountsFromArray($accounts);
    PortfolioInformation_Module_Model::CalculateMonthlyIntervalsForAccounts($accounts);
    $intervals = PortfolioInformation_Module_Model::GetIntervalsForAccounts($accounts);
    
    $output['INTERVALS'] = $intervals;
    $output["ACCOUNT_NUMBERS"] = implode(",", $accounts);
    $output["SOURCE_RECORD"] = $calling_record;
    $output["SOURCE_MODULE"] = $module;
    
    return $output;
    
}

function LoadOmniIntervalsDaily($account_number){
    
    $overviewReportParams = array();
    
    $overviewReportParams['account_number'] = $account_number;
    $overviewReportParams['calling_module'] = "PortfolioInformation";
    $overviewReportParams['calling_record'] = $_SESSION['ID'];
    
    $request = new Vtiger_Request($overviewReportParams, $overviewReportParams);
    
    $module = $request->get('calling_module');
    $calling_record = $request->get('calling_record');
    $account_numbers = $request->get('account_number');
    $accounts = explode(",", $account_numbers);
    $accounts = PortfolioInformation_Module_Model::ReturnValidAccountsFromArray($accounts);
    $intervals = PortfolioInformation_Module_Model::GetDailyIntervalsForAccounts($accounts);
    
    $output['INTERVALS'] = $intervals;
    $output["ACCOUNT_NUMBERS"] = implode(",", $accounts);
    $output["SOURCE_RECORD"] = $calling_record;
    $output["SOURCE_MODULE"] = $module;
    
    return $output;
    
}
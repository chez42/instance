<?php
include_once("includes/config.php");
class Language{
    
    public static function translate($term,$lang=false) {
        
        require_once(__DIR__."/../languages/en_us.php");
        
        if(isset($app_strings[$term]))
            return $app_strings[$term];
            else
                return $term;
    }
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
        'ghreportactual' => array('function_name' => 'LoadGHReportActual', 'filepath' => "modules/Reports/GHReportActual.php"),
    );
}

function get_reports($input_array){
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $element = array('function_name'=>'get_reports','input_array'=>$input_array);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
}

function getAccountsRelatedContactsSSN($accountid){
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $element = array('function_name'=>'getAccountsRelatedContactsSSN','input_array'=>$accountid);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
}

function checkModuleActive($module){
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $element = array('function_name'=>'checkModuleActive','input_array'=>$module);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
}


function get_modules()
{
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $element = array('function_name'=>'get_modules','input_array'=>false);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
}

function getPortalUserid() {
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $element = array('function_name'=>'getPortalUserid','input_array'=>false);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
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
    
    global $api_username, $api_accesskey, $api_url, $user_basic_details, $avmod;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $element = array('function_name'=>'get_module_list_values','input_array'=>$Basicdata);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
}


function get_filecontent_detail($data)
{
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $element = array('function_name'=>'get_filecontent_detail','input_array'=>$data);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
}

function updateDownloadCount($id){
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $element = array('function_name'=>'updateDownloadCount','input_array'=>$id);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
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
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $element = array('function_name'=>'show_all','input_array'=>$module);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
}

function get_details($data)
{
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $element = array('function_name'=>'get_details','input_array'=>$data);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
}

function get_documents($data)
{
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $element = array('function_name'=>'get_documents','input_array'=>$data);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
}

function get_record_entity_name_fields($input_array){
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $element = array('function_name'=>'get_record_entity_name_fields','input_array'=>$input_array);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
}

function add_document_attachment($input_array){
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $element = array('function_name'=>'add_document_attachment','input_array'=>$input_array);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
}

function getDefaultAssigneeId() {
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $element = array('function_name'=>'getDefaultAssigneeId','input_array'=>false);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
}

function get_module_details($input_array){
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $element = array('function_name'=>'get_module_details','input_array'=>$input_array);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
}

function update_document($input_array){
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $element = array('function_name'=>'update_document','input_array'=>$input_array);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
}

function getContactAccessibleAccounts($contactid){
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $element = array('function_name'=>'getContactAccessibleAccounts','input_array'=>$contactid);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
}


function LoadIncomeLastYearReport($input_array){
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $input_array['ID'] = $_SESSION['ID'];
    $input_array['accountid'] = $_SESSION['accountid'];
    
    $element = array('function_name'=>'LoadIncomeLastYearReport','input_array'=>$input_array);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
}


function LoadHoldingsReport($account_number){
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $element = array('function_name'=>'LoadHoldingsReport','input_array'=>$account_number);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
}


function getAccountValueOverLast12Months($accounts){
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $element = array('function_name'=>'getAccountValueOverLast12Months','input_array'=>$accounts);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
}

function holdingsReportReformatSecondaryTableData(){
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $element = array('function_name'=>'holdingsReportReformatSecondaryTableData','input_array'=>false);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
}

function getAssetAllocationReport($account_number){
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $element = array('function_name'=>'getAssetAllocationReport','input_array'=>$account_number);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
}


function LoadMonthlyIncomeReport($input_array){
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $element = array('function_name'=>'LoadMonthlyIncomeReport','input_array'=>$input_array);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
}

function LoadOmniOverviewReport($input_array){
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $input_array['ID'] = $_SESSION['ID'];
    $input_array['accountid'] = $_SESSION['accountid'];
    
    $element = array('function_name'=>'LoadOmniOverviewReport','input_array'=>$input_array);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
}

function LoadOmniProjectedReport($input_array){
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $input_array['ID'] = $_SESSION['ID'];
    $input_array['accountid'] = $_SESSION['accountid'];
    
    $element = array('function_name'=>'LoadOmniProjectedReport','input_array'=>$input_array);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
}

function LoadGHReport($input_array){
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $input_array['ID'] = $_SESSION['ID'];
    $input_array['accountid'] = $_SESSION['accountid'];
    
    $element = array('function_name'=>'LoadGHReport','input_array'=>$input_array);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
}

function LoadGH2Report($input_array){
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $input_array['ID'] = $_SESSION['ID'];
    $input_array['accountid'] = $_SESSION['accountid'];
    
    $element = array('function_name'=>'LoadGH2Report','input_array'=>$input_array);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
}

function getDocumentFolderWithParentList($folderId,$index,$emptyFolder ) {
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
    AND vtiger_senotesrel.crmid = ? AND vtiger_documentfolder.parent_id = ? ",
        array($_SESSION['ID'],$folderId));
    
    //     AND (vtiger_crmentity.smcreatorid = ? OR vtiger_crmentity.smownerid = ?)$_SESSION['ownerId'],$_SESSION['ownerId'],
    
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
    
    $folderFiles = folderFiles($folderId, $index);
    
    $folders = array_merge($folders,$folderFiles);
    
    return $folders;
}

function folderFiles($folderId, $startIndex){
    
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
    $result = $db->pquery($query,array($folderId, $_SESSION["ID"]));
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
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $input_array['ID'] = $_SESSION['ID'];
    $input_array['accountid'] = $_SESSION['accountid'];
    
    $element = array('function_name'=>'LoadMonthOverMonth','input_array'=>$input_array);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
}

function LoadOmniIncome($input_array){
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $input_array['ID'] = $_SESSION['ID'];
    $input_array['accountid'] = $_SESSION['accountid'];
    
    $element = array('function_name'=>'LoadOmniIncome','input_array'=>$input_array);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
}

function LoadAssetClassReport($input_array){
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $input_array['ID'] = $_SESSION['ID'];
    $input_array['accountid'] = $_SESSION['accountid'];
    
    $element = array('function_name'=>'LoadAssetClassReport','input_array'=>$input_array);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
}
function LoadGainLoss($input_array){
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $input_array['ID'] = $_SESSION['ID'];
    $input_array['accountid'] = $_SESSION['accountid'];
    
    $element = array('function_name'=>'LoadGainLoss','input_array'=>$input_array);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
}

function LoadOmniIntervals($account_number){
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $element = array('function_name'=>'LoadOmniIntervals','input_array'=>$account_number);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
}

function LoadOmniIntervalsDaily($account_number){
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $element = array('function_name'=>'LoadOmniIntervalsDaily','input_array'=>$account_number);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
}

function LoadGHReportActual($input_array){
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $input_array['ID'] = $_SESSION['ID'];
    $input_array['accountid'] = $_SESSION['accountid'];
    
    $element = array('function_name'=>'LoadGHReportActual','input_array'=>$input_array);
    
    $postParams = array(
        'operation'=>'portal_function',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    return $response['result'];
    
}
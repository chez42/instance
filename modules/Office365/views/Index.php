<?php

class Office365_Index_View extends Vtiger_ExtensionViews_View {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('settings');
	}
	
	function process(Vtiger_Request $request) {
	    
	   $moduleName = $request->get('extensionModule');
	    
       parent::process($request);
	    
	}
	
	function showLogs(Vtiger_Request $request) {
	    $viewer = $this->getViewer($request);
	    $sourceModule = $request->getModule();
	    $moduleName = $request->get('extensionModule');
	    $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
	    $page = $request->get('page');
	    $syncReady = Office365_Utils_Helper::hasStoredToken($request);
	    $viewType = $request->get('viewType');
	    
	    $pagingModel = new Vtiger_Paging_Model();
	    if(!$page || $page == 1) {
	        $page = 1;
	        $pagingModel->set('prevPageExists', false);
	    }
	    $pagingModel->set('page', $page);
	    
	    $forModule = $sourceModule;
	    
	    if($forModule == 'Calendar'){
	        $forModule = "Events";
	    }
	    
	    $logData = Office365_Utils_Helper::getSyncCounts($pagingModel, $moduleName, $forModule);
	    $logsCount = count($logData);
	    
	    $currentUser = Users_Record_Model::getCurrentUserModel();
	    
	    // if user has not authenticated the extension redirect to settings page
	    if(!$syncReady && $viewType != 'modal' && $logsCount == 0) {
	        if(!$request->isAjax()){
	            $this->invokeExposedMethod('settings', $request);
	            return;
	        }
	    }
	    
	    $pagingModel->calculatePageRange($logData);
	    if(count($logData) > $pagingModel->getPageLimit()){
	        array_pop($logData);
	        $logsCount = $logsCount - 1;
	        $pagingModel->set('nextPageExists', true);
	    }else{
	        $pagingModel->set('nextPageExists', false);
	    }
	    
	    $data = $this->convertDataToUserFormat($logData);
	    
	    $totalCount = Office365_Utils_Helper::getTotalSyncCount($moduleName, $forModule);
	    $pageLimit = $pagingModel->getPageLimit();
	    $pageCount = ceil((int) $totalCount / (int) $pageLimit);
	    
	    if($pageCount == 0){
	        $pageCount = 1;
	    }
	    
	    $viewer->assign('PAGE_COUNT', $pageCount);
	    $viewer->assign('TOTAL_RECORD_COUNT', $totalCount);
	    $viewer->assign('LISTVIEW_ENTRIES_COUNT', $logsCount);
	    $viewer->assign('IS_SYNC_READY', $syncReady);
	    $viewer->assign('MODULE_MODEL', $moduleModel);
	    $viewer->assign('MODULE', $moduleName);
	    $viewer->assign('SOURCE_MODULE', $sourceModule);
	    $viewer->assign('CURRENT_USER_MODEL', $currentUser);
	    $viewer->assign('DATA', $data);
	    $viewer->assign('PAGING_MODEL', $pagingModel);
	    
	    if ($viewType == 'modal') {
	        $viewer->assign('MODAL', true);
	        echo $viewer->view('ExtensionListImportLog.tpl',$moduleName);
	    } else {
	        $viewer->view('ExtensionListLog.tpl', $moduleName);
	    }
	}
	
	function settings(Vtiger_Request $request) {
	    
	    $user = Users_Record_Model::getCurrentUserModel();
	    
	    $viewer = $this->getViewer($request);
	    
	    $moduleName = $request->get('extensionModule');
	    
	    $isSyncReady = Office365_Utils_Helper::hasStoredToken($request);
	    
	    $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
	    
	    $sourceModule = $request->getModule();
	    
        $clientId = MailManager_Office365Config_Connector::$clientId;
        
        $redriectUri = MailManager_Office365Config_Connector::$redirect_url;
        
        global $site_URL;
        
        $auth_url = "https://login.microsoftonline.com/common/oauth2/v2.0/authorize?response_type=code&redirect_uri=".urlencode($redriectUri)."&client_id=".urlencode($clientId);
        $auth_url .= '&state=' . base64_encode(implode('||', array($site_URL, $user->id, "Office365", "Office365Calendar")));
        $auth_url .= '&scope=' . urlencode('Contacts.ReadWrite Calendars.ReadWrite offline_access User.Read.All');
        
        $viewer->assign('AUTH_URL', $auth_url);
        //  header('Location: ' . $auth_url);
        
	    $viewer->assign('MODULE_MODEL', $moduleModel);
	    $viewer->assign('MODULE', $moduleName);
	    $viewer->assign('SOURCEMODULE', $request->getModule());
	    $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
	    $viewer->assign('CONTACTS_SYNC_DIRECTION', Office365_Utils_Helper::getSyncDirectionForUser('Contacts'));
	    $viewer->assign('CALENDAR_SYNC_DIRECTION', Office365_Utils_Helper::getSyncDirectionForUser('Calendar'));
	    $viewer->assign('CONTACTS_ENABLED', Office365_Utils_Helper::checkCronEnabled('Contacts'));
	    $viewer->assign('CALENDAR_ENABLED', Office365_Utils_Helper::checkCronEnabled('Calendar'));
	    
	    $syncStartDate = Office365_Utils_Helper::getCalendarSyncStartDate();
	    if(!$syncStartDate)
	        $syncStartDate = date('Y-m-d', strtotime('-2 days'));
	    
        $viewer->assign('CALENDAR_SYNC_START', $syncStartDate);
        $viewer->assign('SYNC_STATE',Office365_Utils_Helper::getSyncState('Calendar'));
	    $viewer->assign('IS_SYNC_READY', $isSyncReady);
	    $viewer->assign('PARENT', $request->get('parent'));
	    
	    if($request->get("returnToLogs"))
	        $viewer->assign("RETURNTOLOGS", $request->get("returnToLogs"));
	        
        $viewer->view('ExtensionSettings.tpl', $moduleName);
	}
	
	function getHeaderScripts(Vtiger_Request $request){
	    
	    $mode = $request->getMode();
	    
	    $headerScriptInstances = parent::getHeaderScripts($request);
	    
	    if($mode == 'GlobalSettings'){
    	    $jsFileNames = array(
    	        '~layouts/v7/modules/Office365/resources/Office365.js',
    	    );
    	    
    	    $headerScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
	    } 
	    
	    return $headerScriptInstances;
	}
	
	
	
	
}
<?php

class MSExchange_Index_View extends Vtiger_ExtensionViews_View {

	function __construct() {
		parent::__construct();
		$this->exposeMethod("showLicenseSettings");
		$this->exposeMethod("GlobalSettings");
		$this->exposeMethod('settings');
	}
	
	function process(Vtiger_Request $request) {
	    
	    $moduleName = $request->get('extensionModule');
	    
	    $exchangeLicense = new MSExchange_License_Model();
	    
	    $mode = $request->get('mode');
	    if (!$exchangeLicense->validate() && $mode != 'showLicenseSettings') {
	        if($request->get("parent") == 'Settings'){
	            header("Location: index.php?module=MSExchange&parent=Settings&view=Extension&extensionModule=MSExchange&extensionView=Index&mode=showLicenseSettings");
	        }
	        $viewer = $this->getViewer($request);
	        $viewer->assign('MODULE', $moduleName);
	        $viewer->view('InvalidLicense.tpl', $moduleName);
	    } else {
	       parent::process($request);
	    }
	}
	
	function showLogs(Vtiger_Request $request) {
	    $viewer = $this->getViewer($request);
	    $sourceModule = $request->getModule();
	    $moduleName = $request->get('extensionModule');
	    $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
	    $page = $request->get('page');
	    $syncReady = $this->checkIsSyncReady($sourceModule);
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
	    
	    $logData = MSExchange_Utils_Helper::getSyncCounts($pagingModel, $moduleName, $forModule);
	    $logsCount = count($logData);
	    
	    $currentUser = Users_Record_Model::getCurrentUserModel();
	    
	    // if user has not authenticated the extension redirect to settings page
	    if(!$syncReady && $viewType != 'modal' && $logsCount == 0) {
	        if(!$request->isAjax()){
	            $settingsUrl = "index.php?module=Users&parent=Settings&view=MsExchangeSettings&mode=Edit&record=".$currentUser->id;
	            header("Location: $settingsUrl");
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
	    
	    $totalCount = MSExchange_Utils_Helper::getTotalSyncCount($moduleName, $forModule);
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
	
	function showLicenseSettings(Vtiger_Request $request){
	    
	    global $site_URL;
	    
	    $moduleName = $request->getModule();
    	
	    $exchangeLicense = new MSExchange_License_Model();
	    $exchangeLicense->validate();
	    $viewer = $this->getViewer($request);
	    $viewer->assign('EXCHANGELICENSE', $exchangeLicense);
    	$viewer->assign('SITE_URL', $site_URL);
    	$viewer->assign('QUALIFIED_MODULE', $moduleName);
    	$viewer->view('LicenseSetting.tpl', $moduleName);
    }
	
	/**
	 * Function to check if sync is ready
	 * @return <boolean> true/false
	 */
	function checkIsSyncReady($sourceModule = 'Contacts') {

	    $exchangeLicense = new MSExchange_License_Model();
	    
	    if($exchangeLicense->validate()){
	    
    	    $user = Users_Record_Model::getCurrentUserModel();
    	    
    	    $userId = $user->getId();
    	    
    	    $isSyncReady = MSExchange_Utils_Helper::getImpersonationIdentifierForUser($userId, $sourceModule);
    	   
    	    return $isSyncReady;
	    }
	}

	function GlobalSettings(Vtiger_Request $request){
	    
	    $exchangeLicense = new MSExchange_License_Model();
	    
	    if($exchangeLicense->validate()){
	        
	        $adb = PearDatabase::getInstance();
	        
	        $user = Users_Record_Model::getCurrentUserModel();
	        
	        $viewer = $this->getViewer($request);
	        
	        $moduleName = $request->get('extensionModule');
	        
	        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
	        
    	    $viewer->assign('MODULE_MODEL', $moduleModel);
    	    
    	    $viewer->assign('MODULE', $moduleName);
    	    
    	    $exchangeGlobalSettings = $moduleModel->getExchangeGlobalSettings();
    	    
    	    $viewer->assign("MSEXCHANGE_SETTINGS", $exchangeGlobalSettings);
    	    
    	    $viewer->assign('EXCHANGELICENSE', $exchangeLicense);
    	    
    	    $viewer->view('ExtensionGlobalSettings.tpl', $moduleName);
	    
	    } else {
	        
	        $this->showLicenseSettings($request);
	    }
	}
	
	function settings(Vtiger_Request $request) {
	    
	    $user = Users_Record_Model::getCurrentUserModel();
	    
	    $viewer = $this->getViewer($request);
	    
	    $moduleName = $request->get('extensionModule');
	    
	    $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
	    
	    $sourceModule = $request->getModule();
	    
	    //if($sourceModule == 'Calendar'){
	        
	        $syncStart = MSExchange_Utils_Helper::getCalendarSyncStartDate();
	        
	        if(!$syncStart){
	            $date = new DateTimeField(date("Y-m-d", strtotime("-1 month")));
	            $syncStart = $date->getDisplayDate($user);
	        } else{
	            $syncStart = getValidDisplayDate($syncStart);
	        }
	            
	        $viewer->assign("SYNC_START_FROM", $syncStart);
	        $viewer->assign('AUTOMATIC_SYNC', MSExchange_Utils_Helper::checkCronEnabled('Calendar'));
	    //}
	    
	    //if($sourceModule == 'Task'){
	        
	        $syncStart = MSExchange_Utils_Helper::getTaskSyncStartDate();
	        
	        if(!$syncStart){
	            $date = new DateTimeField(date("Y-m-d", strtotime("-1 month")));
	            $syncStart = $date->getDisplayDate($user);
	        } else{
	            $syncStart = getValidDisplayDate($syncStart);
	        }
	        
	        $viewer->assign("SYNC_TASK_START_FROM", $syncStart);
	        $viewer->assign('TASK_AUTOMATIC_SYNC', MSExchange_Utils_Helper::checkCronEnabled('Task'));
	        
	    //}
	    
	    $viewer->assign('MODULE_MODEL', $moduleModel);
	    $viewer->assign('MODULE', $moduleName);
	    $viewer->assign('SOURCEMODULE', $request->getModule());
	    $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
	    $viewer->assign('CONTACTS_SYNC_DIRECTION', MSExchange_Utils_Helper::getSyncDirectionForUser('Contacts'));
	    $viewer->assign('CALENDAR_SYNC_DIRECTION', MSExchange_Utils_Helper::getSyncDirectionForUser('Calendar'));
	    $viewer->assign('TASK_SYNC_DIRECTION', MSExchange_Utils_Helper::getSyncDirectionForUser('Task'));
	    $viewer->assign("USER_IMPERSONATION", MSExchange_Utils_Helper::getCurrentUserImpersonation($sourceModule));
	    $viewer->assign('GLOBAL_SETTINGS', $moduleModel->getExchangeGlobalSettings());
	    $viewer->assign('PARENT', $request->get('parent'));
	    
	    if($request->get("returnToLogs"))
	        $viewer->assign("RETURNTOLOGS", $request->get("returnToLogs"));
	        
        $viewer->view('ExtensionSettings.tpl', $moduleName);
	}
	
	function getHeaderScripts(Vtiger_Request $request){
	    
	    $mode = $request->getMode();
	    
	    $headerScriptInstances = parent::getHeaderScripts($request);
	    
	    if($mode == 'GlobalSettings' || $mode == 'showLicenseSettings'){
    	    $jsFileNames = array(
    	        '~layouts/v7/modules/MSExchange/resources/MSExchange.js',
    	    );
    	    
    	    $headerScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
	    } 
	    
	    return $headerScriptInstances;
	}
}
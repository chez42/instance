<?php

class MSExchange_Sync_View extends MSExchange_List_View {

	function process(Vtiger_Request $request) {
	    
	    $exchangeLicense = new MSExchange_License_Model();
	    
	    if (!$exchangeLicense->validate()) {
	    
	        $syncRecordList = array("success" => false, "error" => vtranslate("Access denied. Please check your license settings.", $request->getModule()));
	    
	    } else {
	    
    	    $module = $request->getModule();
    	    
    	    $moduleModel = Vtiger_Module_Model::getInstance($module);
    	    
    	    $globalSettings = $moduleModel->getExchangeGlobalSettings();
    	    
    		$modules = array('Contacts', 'Calendar');
    		
    		if($request->get("source_module", false))
    		    $modules = array($request->get("source_module"));
    		
    		$syncRecordList = array();
    		
    		$user = Users_Record_Model::getCurrentUserModel();
    		
    		foreach ($modules as $sourceModule) {
    			
    		    $request->set('sourcemodule', $sourceModule);
    
    			if (!empty($globalSettings) && isset($globalSettings['url']) && $globalSettings['url'] != '') {
    			    
    			    if($sourceModule == 'Contacts'){
    			        $controller = new MSExchange_Contacts_Controller($user);
    			    } else if($sourceModule == 'Calendar'){
    			        $controller = new MSExchange_Calendar_Controller($user);
    			    }
    			    
    			    $isSyncReady = $controller->getMSExchangeModel()->isValidCredentials();
    			    
    			    if($isSyncReady){
                        $syncRecords = $this->sync($request, $sourceModule);
                        $syncRecordList['success'] = true;
    				    $syncRecordList[$sourceModule] = $syncRecords;
    			    } else {
    			        $syncRecordList = array("success" => false, "error" => vtranslate("Invalid Credentials. Please check your configuration settings.", $request->getModule()));
    			    }
    			}
    		}
	    }
   		$response = new Vtiger_Response();
		$response->setResult($syncRecordList);
		$response->emit();
	}

	function sync(Vtiger_Request $request, $sourceModule) {
		try {
		    $records = $this->invokeExposedMethod($sourceModule);
		    return $records;
		} catch (Zend_Gdata_App_HttpException $e) {
			$errorCode = $e->getResponse()->getStatus();
			if($errorCode == 401) {
				$this->removeSynchronization($request);
				$response = new Vtiger_Response();
				$response->setError(401);
				$response->emit();
				return array();
			}
		}
	}
}

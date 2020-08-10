<?php

class Office365_Sync_View extends Office365_List_View {

	function process(Vtiger_Request $request) {
	    
	    $module = $request->getModule();
	    
		$modules = array('Contacts', 'Calendar');
		
		if($request->get("source_module", false))
		    $modules = array($request->get("source_module"));
		
		$syncRecordList = array();
		
		$user = Users_Record_Model::getCurrentUserModel();
		
		foreach ($modules as $sourceModule) {
			
		    if($sourceModule != 'Contacts'){
    		    $request->set('sourcemodule', $sourceModule);
    		    
    		    if (Office365_Utils_Helper::hasStoredToken($request)) {
    			    
    			    if($sourceModule == 'Contacts'){
    			        //$controller = new Office365_Contacts_Controller($user);
    			    } else if($sourceModule == 'Calendar'){
    			        $controller = new Office365_Calendar_Controller($user);
    			    } 
    			    
                    $syncRecords = $this->sync($request, $sourceModule);
                    $syncRecordList['success'] = true;
    			    $syncRecordList[$sourceModule] = $syncRecords;
    			    
    		    }else{
    		        $syncRecordList = array("success" => false, "error" => vtranslate("Invalid Credentials. Please check your configuration settings.", $request->getModule()));
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

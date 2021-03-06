<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_MailConverter_SaveMailBox_Action extends Settings_Vtiger_Index_Action {

    function checkPermission(Vtiger_Request $request) {
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        if(!$currentUserModel->isAdminUser() && $request->getModule() != 'MailConverter') {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED', 'Vtiger'));
        }
    }
    
	public function process(Vtiger_Request $request) {
		$recordId = $request->get('record');
		$qualifiedModuleName = $request->getModule(false);

		$duplicate = false;
		
		if(!$recordId){
	        
		    global $adb;
    		$scanner = $adb->pquery("SELECT * FROM vtiger_mailscanner WHERE username = ? AND userid = ?",
    		    array($request->get('username'), $request->get('userid')));
    		
    		if($adb->num_rows($scanner))
    		    $duplicate = true;
		
		}
		
		if(!$duplicate){
    	
		    if ($recordId) {
    			$recordModel = Settings_MailConverter_Record_Model::getInstanceById($recordId);
    		} else {
    			$recordModel = Settings_MailConverter_Record_Model::getCleanInstance();
    			
    		}
    
    		$recordModel->set('scannerOldName', $request->get('scannerOldName'));
            $recordModel->set('userid', $request->get('userid'));
    		
    		$fieldsList = $recordModel->getModule()->getFields();
    		foreach ($fieldsList as $fieldName=>$fieldModel) {
    			$recordModel->set($fieldName, $request->get($fieldName));
    		}
    
    		$status = $recordModel->save();
	
		}
		
		$response = new Vtiger_Response();
		if ($status) {
			$result = array('message' => vtranslate('LBL_SAVED_SUCCESSFULLY', $qualifiedModuleName));
			$result['id'] = $recordModel->getId();
			$result['listViewUrl'] = $recordModel->getListUrl();
			$response->setResult($result);
		}else if($duplicate) {
		    $response->setError(vtranslate('Scanner is already available. Please try with another user.', $qualifiedModuleName));
		}else {
			$response->setError(vtranslate('LBL_CONNECTION_TO_MAILBOX_FAILED', $qualifiedModuleName));
		}
		$response->emit();
	}
        
        public function validateRequest(Vtiger_Request $request) { 
            $request->validateWriteAccess(); 
        }
}
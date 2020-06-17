<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class MSExchange_SaveSyncSettings_Action extends Vtiger_BasicAjax_Action {

	function checkPermission(Vtiger_Request $request) {
		return true;
	}
	
    public function process(Vtiger_Request $request) {
        
        $contactsSettings = $request->get('Contacts');
        $calendarSettings = $request->get('Calendar');
        $taskSettings = $request->get('Task');
        
        $impersonation_identifier = $request->get('impersonation_identifier');
        
        $sourceModule = $request->get('sourceModule');
        
        if(!empty($contactsSettings)){
            $contactRequest = new Vtiger_Request($contactsSettings);
            $contactRequest->set('sourcemodule', 'Contacts');
            $contactRequest->set('impersonation_identifier', $impersonation_identifier);
            MSExchange_Utils_Helper::saveSyncSettings($contactRequest);
        }
        
        if(!empty($calendarSettings)){
            $calendarRequest = new Vtiger_Request($calendarSettings);
            $calendarRequest->set('sourcemodule', 'Calendar');
            $calendarRequest->set('impersonation_identifier', $impersonation_identifier);
            MSExchange_Utils_Helper::saveSyncSettings($calendarRequest);
        }
        
        if(!empty($taskSettings)){
            $taskRequest = new Vtiger_Request($taskSettings);
            $taskRequest->set('sourcemodule', 'Task');
            $taskRequest->set('impersonation_identifier', $impersonation_identifier);
            MSExchange_Utils_Helper::saveSyncSettings($taskRequest);
        }
        
        $moduleModel = Vtiger_Module_Model::getInstance('MSExchange');
        
        $returnUrl = $moduleModel->getBaseExtensionUrl($sourceModule);
        
        header('Location: '.$returnUrl);
    }
    
}

?>
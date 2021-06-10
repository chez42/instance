<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Notifications_Delete_Action extends Vtiger_Delete_Action {

	function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$record = $request->get('record');

		$currentUserPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		if(!$currentUserPrivilegesModel->isPermitted($moduleName, 'Delete', $record)) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}

		if ($record) {
		    if ('Notifications' !== $moduleName) {
				throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
			}
		}
	}
	
	public function process(Vtiger_Request $request) {
	    $moduleName = $request->getModule();
	    $recordId = $request->get('record');
	    
	    global $adb;
	    
	    if($recordId){
            
	        $adb->pquery("DELETE FROM vtiger_notifications WHERE 
            vtiger_notifications.notificationsid = ?", array($recordId));
            
			$adb->pquery("DELETE FROM `vtiger_notificationscf` 
			where notificationsid = ?", array($recordId));
			
	        $result = array('success' => true);
	    }else{
	        $result = array('success' => false);
	    }
	    
        $response = new Vtiger_Response();
        $response->setResult($result);
        return $response;
	    
	}

}

<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Contacts_Save_Action extends Vtiger_Save_Action {

	public function process(Vtiger_Request $request) {
		$adb = PearDatabase::getInstance();
	
		//To stop saveing the value of salutation as '--None--'
		$salutationType = $request->get('salutationtype');
		if ($salutationType === '--None--') {
			$request->set('salutationtype', '');
		}
		
		$recordModel = $this->saveRecord($request);
		if($request->get('relationOperation')) {
			$parentModuleName = $request->get('sourceModule');
			$parentRecordId = $request->get('sourceRecord');
			$parentRecordModel = Vtiger_Record_Model::getInstanceById($parentRecordId, $parentModuleName);
			$loadUrl = $parentRecordModel->getDetailViewUrl();
		} else if ($request->get('returnToList')) {
			$loadUrl = $recordModel->getModule()->getListViewUrl();
		} else {
			$loadUrl = $recordModel->getDetailViewUrl();
		}

		$portal_module_permission = $request->get("portalModulesInfo");
		
		if(!empty($portal_module_permission) && $recordModel->getId()){
			
			$portal_permission_result = $adb->pquery("select * from vtiger_contact_portal_permissions where crmid = ?",array($recordModel->getId()));
			
			if($adb->num_rows($portal_permission_result)){
				$adb->pquery("update vtiger_contact_portal_permissions set permissions = ? where crmid = ?", 
				array(json_encode($portal_module_permission), $recordModel->getId()));
			} else {
				$adb->pquery("insert into vtiger_contact_portal_permissions (crmid, permissions) values (?, ?)",array($recordModel->getId(), json_encode($portal_module_permission)));
			}
		}
		
		$appName = $request->get('appName');
		if(strlen($appName) > 0){
		    $loadUrl = $loadUrl.$appName;
		}
		
		header("Location: $loadUrl");
	}
}

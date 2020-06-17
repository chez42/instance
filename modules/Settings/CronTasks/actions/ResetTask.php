<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_CronTasks_ResetTask_Action extends Settings_Vtiger_Index_Action {

	public function checkPermission(Vtiger_Request $request) {
		parent::checkPermission($request);

		$recordId = $request->get('record');
		if(!$recordId) {
			throw new AppException('LBL_PERMISSION_DENIED');
		}
	}

	public function process(Vtiger_Request $request) {
        global $adb;
		$recordId = $request->get('record');
		if($request->get('resettask')) {
			$qualifiedModuleName = $request->getModule(false);

			$recordModel = Settings_CronTasks_Record_Model::getInstanceById($recordId, $qualifiedModuleName);

			if($recordId == 25){
			    $query = "UPDATE vtiger_custodian_status SET active = 0";
			    $adb->pquery($query, array());
            }

			$query = "UPDATE vtiger_cron_task SET laststart=0, lastend=0, status=1 WHERE id=?";
			$adb->pquery($query, array($recordId));
			$data = $recordModel->getData();//These lines don't seem to actually do anything due to laststart and lastend not really being editable, but it seems to at least reset the times when save is hit without a refresh
			$data['laststart'] = 0;
			$data['lastend'] = 0;
			$recordModel->setData($data);

			$recordModel->save();

			$response = new Vtiger_Response();
			$response->setResult(array(true));
			$response->emit();
		}
	}

	public function validateRequest(Vtiger_Request $request) {
		$request->validateWriteAccess();
	}
}
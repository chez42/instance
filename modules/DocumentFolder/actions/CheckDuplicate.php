<?php

class DocumentFolder_CheckDuplicate_Action extends Vtiger_Action_Controller {

	function checkPermission(Vtiger_Request $request) {
		return;
	}

	public function process(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$folderName = $request->get('foldername');
		$record = $request->get('record');

		if ($record) {
			$recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
		} else {
			$recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
		}

		$recordModel->set('folder_name', $folderName);
			
		if (!$recordModel->checkDuplicate()) {
			$result = array('success'=>false);
		} else {
			$result = array('success'=>true, 'message'=>vtranslate('LBL_FOLDER_EXISTS', $moduleName));
		}
		$response = new Vtiger_Response();
		$response->setResult($result);
		$response->emit();
	}
}

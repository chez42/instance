<?php

class Documents_DocumentsReminder_Action extends Vtiger_Action_Controller{

	function __construct() {
		$this->exposeMethod('getReminders');
		$this->exposeMethod("Snooze");
		$this->exposeMethod("Acknowledge");
	}

	public function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);

		$userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());

		if(!$permission) {
			throw new AppException('LBL_PERMISSION_DENIED');
		}
	}

	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode) && $this->isMethodExposed($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}

	}

	function getReminders(Vtiger_Request $request) {
		
		$recordModels = Documents_Module_Model::getDocumentReminder();
		
		$contactRecords = $docRecords = array();
		
		foreach($recordModels as $contactid => $docRecordModels){
			$contactRecordModel = Vtiger_Record_Model::getInstanceById($contactid, "Contacts");
			$contactLabel = $contactRecordModel->get("firstname")." ".$contactRecordModel->get("lastname");
			$contactRecords[$contactid] = "<a href='".$contactRecordModel->getDetailViewUrl()."' target='_blank'>".trim($contactLabel)."</a>";
			foreach($docRecordModels as $record) {
				$docRecords[$contactid][$record->getId()] = "<a href='".$record->getDetailViewUrl()."' target='_blank'>".$record->getDisplayName()."</a>";
			}
		}

		$response = new Vtiger_Response();
		$response->setResult(array("Contacts" => $contactRecords, "Documents" => $docRecords));
		$response->emit();
	}
	
	function Acknowledge(Vtiger_Request $request){
		$record = $request->get("record");
		$recordModel = Vtiger_Record_Model::getInstanceById($record);
		$recordModel->deleteReminderNotification();	
		$response = new Vtiger_Response();
		$response->setResult(true);
		$response->emit();
	}
	
	function Snooze(Vtiger_Request $request){
		
		$record = $request->get("record");
		
		if(!isset($_SESSION['snooze_notifications']))
			$_SESSION['snooze_notifications'] = array();

		if(!in_array($record, $_SESSION['snooze_notifications']))
			array_push($_SESSION['snooze_notifications'], $record);
		
		$response = new Vtiger_Response();
		$response->setResult(true);
		$response->emit();
	}
}
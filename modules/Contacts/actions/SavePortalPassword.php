<?php
require_once("modules/Emails/mail.php");
class Contacts_SavePortalPassword_action extends Vtiger_Action_Controller {
	
	function __construct() {
		parent::__construct();
		$this->exposeMethod('isPortalEnableAndActive');
	}
	
	function checkPermission(Vtiger_Request $request) {
		return true;
	}
	
	function process(Vtiger_Request $request){
		
		$mode = $request->getMode();
		
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}
		
		global $current_user;
			
		$adb = PearDatabase::getInstance();
		
		$record = $request->get("record");
		
		$new_password = $request->get("new_password");
		
		$isactive = $adb->query_result($adb->pquery("select isactive from vtiger_portalinfo where id = ?", array($record)),"0", "isactive");
		
		$result = array();
		
		if($isactive){
		    
			$adb->pquery("update vtiger_portalinfo set user_password = ? where id = ?",array($new_password, $record));
			
			$recordModel = Vtiger_Record_Model::getInstanceById($record, "Contacts");
			$recordModel->set('mode', 'edit');
			$recordModel->set('portal_password',$new_password);
			$recordModel->save();
			
			$result['success'] = true;$result['message'] = "Password reset successfully";
			
		} else {
			$result['error'] = true;$result['message'] = "Your portal is inactive.";
		}
		$response = new Vtiger_Response();
		$response->setResult($result);
		$response->emit();
	}
	
	function isPortalEnableAndActive(Vtiger_Request $request){
		
		$adb = PearDatabase::getInstance();
		
		$record = $request->get("record");
		
		$portal_result = $adb->pquery("select isactive from vtiger_portalinfo where id = ?",array($record));
		
		if($adb->num_rows($portal_result))
			$result = array("isenable" => "1", "isactive" => $adb->query_result($portal_result, "0", "isactive"));
		else
			$result = array("isenable" => "0", "isactive" => "0");
	
		$response = new Vtiger_Response();
		$response->setResult($result);
		$response->emit();
	}
}
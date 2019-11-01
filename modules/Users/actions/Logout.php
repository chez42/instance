<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Users_Logout_Action extends Vtiger_Action_Controller {
	
	function checkPermission(Vtiger_Request $request) {
		return true;
	}

	function process(Vtiger_Request $request) {
		//Redirect into the referer page
		global $adb,$current_user;
		
		//Fetch and Close all the Timers of Existing Users
		$timecontrol_result = $adb->pquery("SELECT vtiger_timecontrol.timecontrolid FROM vtiger_timecontrol
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_timecontrol.timecontrolid
        WHERE vtiger_crmentity.deleted = 0 AND vtiger_timecontrol.timecontrolstatus = 'run'
        AND vtiger_crmentity.smcreatorid = ?",array($current_user->id));
		
		if($adb->num_rows($timecontrol_result)){
		    for($i=0;$i<$adb->num_rows($timecontrol_result);$i++){
		        
		        $recordId = $adb->query_result($timecontrol_result, $i, 'timecontrolid');
		        
		        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'Timecontrol');
		        
		        $datetimefield = new DateTimeField('');
				
		        $nowDate = $datetimefield->convertToUserTimeZone(date('Y-m-d H:i:s'));
		        
				$finishDateTS = strtotime($nowDate->format('Y-m-d H:i:s'));
		        
		        $recordModel->set('date_end', date('Y-m-d', $finishDateTS));
		        $recordModel->set('time_end', date('H:i:s', $finishDateTS));
		        $recordModel->set('mode', 'edit');
		        $recordModel->set('timecontrolstatus', 'finish');
		        $recordModel->save();
		    }
		}
		
		
		$logoutURL = $this->getLogoutURL();
        session_regenerate_id(true);
		Vtiger_Session::destroy();
		
		//Track the logout History
		$moduleName = $request->getModule();
		$moduleModel = Users_Module_Model::getInstance($moduleName);
		$moduleModel->saveLogoutHistory();
		//End

		if(!empty($logoutURL)) {
			header('Location: '.$logoutURL);
			exit();
		} else {
			header ('Location: index.php');
		}
	}
	
	protected function getLogoutURL() {
		$logoutUrl = Vtiger_Session::get('LOGOUT_URL');
		if (isset($logoutUrl) && !empty($logoutUrl)) {
			return $logoutUrl;
		}
		return VtigerConfig::getOD('LOGIN_URL');
	}
}
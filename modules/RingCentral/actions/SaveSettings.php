<?php

class RingCentral_SaveSettings_Action extends Vtiger_Action_Controller{
	
    public function checkPermission(Vtiger_Request $request){}
	
	public function process(Vtiger_Request $request){
		
	    global $adb,$current_user;
	    $success = array('success'=>false,'message'=>'Something went wrong try again');
		$from_no = $request->get("from_no");
		
		$result = $adb->pquery('SELECT * FROM vtiger_ringcentral_settings 
		WHERE userid = ?',array($current_user->id));
	
		if($adb->num_rows($result)){
			$adb->pquery('UPDATE vtiger_ringcentral_settings SET from_no = ? 
            WHERE userid =?',array($from_no,$current_user->id));
			$success = array('success'=>true,'message'=>'Number saved successfully');
		}
	
		$response = new Vtiger_Response();
		$response->setResult($success);
		$response->emit();
		
	}
}
?>

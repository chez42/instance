<?php

class RingCentral_SaveAuthSettings_Action extends Vtiger_Action_Controller{
    
    public function checkPermission(Vtiger_Request $request){}
    
    public function process(Vtiger_Request $request){
        
        global $adb,$current_user;
        $success = array('success'=>false,'message'=>'Something went wrong try again');
        
        $client_id = $request->get("client_id");
        $client_secret = $request->get("client_secret");
        $redirect_url = $request->get("redirect_url");
        
        $result = $adb->pquery('SELECT * FROM vtiger_ringcentral_oauth_settings
		WHERE user_id = ?',array($current_user->id));
        
        if($adb->num_rows($result)){
            $adb->pquery('UPDATE vtiger_ringcentral_oauth_settings SET clientid = ?, clientsecret = ?
             WHERE user_id =?',array($client_id,$client_secret,$current_user->id));
            $success = array('success'=>true,'message'=>'Credentials saved successfully');
        }else{
            $adb->pquery("INSERT INTO vtiger_ringcentral_oauth_settings(user_id, clientid, clientsecret)
             VALUES (?,?,?)",array($current_user->id, $client_id, $client_secret));
            $success = array('success'=>true,'message'=>'Credentials saved successfully');
        }
        
        $response = new Vtiger_Response();
        $response->setResult($success);
        $response->emit();
        
    }
}
?>

<?php

class PandaDoc_SaveAuthSettings_Action extends Vtiger_Action_Controller{
    
    public function checkPermission(Vtiger_Request $request){}
    
    public function process(Vtiger_Request $request){
        
        global $adb,$current_user;
        $success = array('success'=>false,'message'=>'Something went wrong try again');
        
        $client_id = $request->get("client_id");
        $client_secret = $request->get("client_secret");
        $redirect_url = $request->get("redirect_url");
       
        $result = $adb->pquery('SELECT * FROM vtiger_oauth_configuration WHERE type=?',array('PandaDoc'));
        
        if($adb->num_rows($result)){
            $adb->pquery('UPDATE vtiger_oauth_configuration SET client_id = ?, client_secret = ?, redirect_url = ? WHERE type = ?',
                array($client_id, $client_secret, $redirect_url, 'PandaDoc'));
            $success = array('success'=>true,'message'=>'Credentials saved successfully');
        }else{
            $adb->pquery("INSERT INTO vtiger_oauth_configuration(client_id, client_secret, redirect_url, type) VALUES (?, ?, ?, ?)",
                array($client_id, $client_secret, $redirect_url, 'PandaDoc'));
            $success = array('success'=>true,'message'=>'Credentials saved successfully');
        }
        
        $response = new Vtiger_Response();
        $response->setResult($success);
        $response->emit();
        
    }
}
?>

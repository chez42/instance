<?php

class RingCentral_SaveAuthSettings_Action extends Vtiger_Action_Controller{
    
    public function checkPermission(Vtiger_Request $request){}
    
    public function process(Vtiger_Request $request){
        
        global $adb,$current_user;
        $success = array('success'=>false,'message'=>'Something went wrong try again');
        
        $client_id = $request->get("client_id");
        $client_secret = $request->get("client_secret");
        $server = $request->get("server");
        
        
        $result = $adb->pquery('SELECT * FROM vtiger_ringcentral_configuration_settings',array());
        
        if($adb->num_rows($result)){
            $adb->pquery('UPDATE vtiger_ringcentral_configuration_settings SET clientid = ?, clientsecret = ?,
            server = ?',
                array($client_id, $client_secret, $server));
            $success = array('success'=>true,'message'=>'Credentials saved successfully');
        }else{
            $adb->pquery("INSERT INTO vtiger_ringcentral_configuration_settings(clientid, clientsecret, server)
             VALUES (?,?,?)",array($client_id, $client_secret,$server));
            $success = array('success'=>true,'message'=>'Credentials saved successfully');
        }
        
        $response = new Vtiger_Response();
        $response->setResult($success);
        $response->emit();
        
    }
}
?>

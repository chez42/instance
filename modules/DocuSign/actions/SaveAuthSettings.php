<?php

class DocuSign_SaveAuthSettings_Action extends Vtiger_Action_Controller{
    
    public function checkPermission(Vtiger_Request $request){}
    
    public function process(Vtiger_Request $request){
        
        global $adb,$current_user;
        $success = array('success'=>false,'message'=>'Something went wrong try again');
        
        $client_id = $request->get("client_id");
        $client_secret = $request->get("client_secret");
        $server = $request->get("server");
        $redirect_url = $request->get("redirect_url");
        
        
        $result = $adb->pquery('SELECT * FROM vtiger_document_designer_auth_settings',array());
        
        if($adb->num_rows($result)){
            $adb->pquery('UPDATE vtiger_document_designer_auth_settings SET clientid = ?, clientsecret = ?,
            server = ?, redirect_url = ?',
                array($client_id, $client_secret, $server, $redirect_url));
            $success = array('success'=>true,'message'=>'Credentials saved successfully');
        }else{
            $adb->pquery("INSERT INTO vtiger_document_designer_auth_settings(clientid, clientsecret, server, redirect_url)
             VALUES (?,?,?,?)",array($client_id, $client_secret, $server, $redirect_url));
            $success = array('success'=>true,'message'=>'Credentials saved successfully');
        }
        
        $response = new Vtiger_Response();
        $response->setResult($success);
        $response->emit();
        
    }
}
?>

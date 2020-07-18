<?php
class Settings_Vtiger_SaveOauthConfiguration_Action extends Settings_Vtiger_Basic_Action {
 
    public function process(Vtiger_Request $request) {
        
        global $adb;
        
        $officeId = $request->get('office_client_id');
        $officeSecret = $request->get('office_client_secret');
        $officeUri = $request->get('office_redirect_uri');
        
        $googleId = $request->get('google_client_id');
        $googleSecret = $request->get('google_client_secret');
        $googleUri = $request->get('google_redirect_uri');
        
        if($officeId){
            
            $checkOffice = $adb->pquery("SELECT * FROM vtiger_oauth_configuration WHERE type = ?",
            array('Office365'));
            
            if($adb->num_rows($checkOffice)){
                $adb->pquery("UPDATE vtiger_oauth_configuration SET client_id=?, client_secret=?, 
                redirect_url = ? WHERE type=?", array($officeId, $officeSecret, $officeUri, 'Office365'));
            }else{
                $adb->pquery("INSERT INTO vtiger_oauth_configuration(client_id, client_secret, redirect_url, type) VALUES (?,?,?,?)",
                array($officeId, $officeSecret, $officeUri, 'Office365'));
            }
            
        }
        
        if($googleId){
            
            $checkOffice = $adb->pquery("SELECT * FROM vtiger_oauth_configuration WHERE type = ?",
                array('Google'));
            
            if($adb->num_rows($checkOffice)){
                $adb->pquery("UPDATE vtiger_oauth_configuration SET client_id=?, client_secret=?, redirect_url=? WHERE type=?",
                    array($googleId, $googleSecret, $googleUri, 'Google'));
            }else{
                $adb->pquery("INSERT INTO vtiger_oauth_configuration(client_id, client_secret, redirect_url, type) VALUES (?,?,?,?)",
                    array($googleId, $googleSecret, $googleUri, 'Google'));
            }
            
        }
        
        $response = new Vtiger_Response();
        $response->setResult($success);
        $response->emit();
        
    }
    
}
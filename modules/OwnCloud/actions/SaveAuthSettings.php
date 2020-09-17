<?php

class OwnCloud_SaveAuthSettings_Action extends Vtiger_Action_Controller{
    
    public function checkPermission(Vtiger_Request $request){}
    
    public function process(Vtiger_Request $request){
        
        global $adb,$current_user;
        $success = array('success'=>false,'message'=>'Something went wrong try again');
        
        $url = $request->get("url");
        $username = $request->get("username");
        $password = $request->get("password");
       
        $result = $adb->pquery('SELECT * FROM vtiger_owncloud_credentials WHERE userid=?',array($current_user->id));
        
        if($adb->num_rows($result)){
            $adb->pquery('UPDATE vtiger_owncloud_credentials SET username = ?, password = ?, url = ? WHERE userid = ?',
                array($username, $password, $url, $current_user->id));
            $success = array('success'=>true,'message'=>'Credentials saved successfully');
        }else{
            $adb->pquery("INSERT INTO vtiger_owncloud_credentials(userid, username, password, url) VALUES (?, ?, ?, ?)",
                array($current_user->id, $username, $password, $url));
            $success = array('success'=>true,'message'=>'Credentials saved successfully');
        }
        
        $response = new Vtiger_Response();
        $response->setResult($success);
        $response->emit();
        
    }
}
?>

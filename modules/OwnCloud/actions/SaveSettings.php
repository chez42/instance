<?php
class OwnCloud_SaveSettings_Action extends Vtiger_Action_Controller{
    
    public function checkPermission(Vtiger_Request $request){return true;}
    
    function __construct() {
        parent::__construct();
        $this->exposeMethod('OwncloudCredentials');
    }
    
    public function process(Vtiger_Request $request){
        
        $mode = $request->get('mode');
        if(!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
        
        global $adb, $current_user;
        
        $url = $request->get("url");
       
        $result = $adb->pquery('SELECT * FROM vtiger_owncloud_configration',array());
        
        if($adb->num_rows($result)){
            $adb->pquery('UPDATE vtiger_owncloud_configration 
            SET url = ?', array($url));
            $success = array('success'=>true,'message'=>'Settings saved successfully');
        } else{
            $adb->pquery("INSERT INTO vtiger_owncloud_configration(url) 
            VALUES (?)", array($url));
            $success = array('success' => true,'message' => 'Settings saved successfully');
        }
        
        $response = new Vtiger_Response();
        
        $response->setResult($success);
        
        $response->emit();
    }
    
    public function OwncloudCredentials(Vtiger_Request $request){
        
        global $adb;
        $success = array('success'=>false,'message'=>'Something went wrong try again');
        
        $record = $request->get("record");
        $username = $request->get("username");
        $password = $request->get("password");
        
        $result = $adb->pquery('SELECT * FROM vtiger_owncloud_credentials WHERE userid=?',array($record));
        
        if($adb->num_rows($result)){
            $adb->pquery('UPDATE vtiger_owncloud_credentials SET username = ?, password = ? WHERE userid = ?',
                array($username, $password, $record));
            $success = array('success'=>true,'message'=>'Credentials saved successfully');
        }else{
            $adb->pquery("INSERT INTO vtiger_owncloud_credentials(userid, username, password) VALUES (?, ?, ?)",
                array($record, $username, $password));
            $success = array('success'=>true,'message'=>'Credentials saved successfully');
        }
        
        $response = new Vtiger_Response();
        $response->setResult($success);
        $response->emit();
        
    }
}
?>

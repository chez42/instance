<?php
class PandaDoc_GetUserActions_Action extends Vtiger_Mass_Action {
    
    function __construct() {
        parent::__construct();
        $this->exposeMethod('checkConnection');
        $this->exposeMethod('revokeToken');
    }
    
    function checkPermission(Vtiger_Request $request) {
        return true;
    }
    
    public function process(Vtiger_Request $request) {
        
        $mode = $request->getMode();
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
        }
        
    }
    
    public function checkConnection(Vtiger_Request $request){
        
        global $adb ;
        
        $userId = $request->get('record');
        
        $result = array('success'=> false);
        
        $check = $adb->pquery("SELECT * FROM vtiger_pandadoc_oauth WHERE userid = ?",array($userId));
        
        if($adb->num_rows($check)){
            $result = array('success'=> true);
        }
        
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
        
    }
    
    public function revokeToken(Vtiger_Request $request){
        
        global $adb;
        
        $userId = $request->get('record');
        
        $result = array('success'=> false);
       
        $adb->pquery("DELETE FROM vtiger_pandadoc_oauth WHERE userid = ?",array($userId));
        $result = array('success'=> true);
        
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
                
    }
}
?>
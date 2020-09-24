<?php
class Settings_Vtiger_SavePortalConfiguration_Action extends Settings_Vtiger_Basic_Action {
    
    public function __construct() {
        parent::__construct();
        $this->exposeMethod('chatWidget');
    }
    
    public function process(Vtiger_Request $request) {
        
        $mode = $request->get('mode');
        if(!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
        
        global $adb;
        
        $fields = $request->get('fieldIdsList');
        
        if(!empty($fields)){
            
            $checkFields = $adb->pquery("SELECT * FROM 
            vtiger_portal_configuration", array());
            
            if($adb->num_rows($checkFields)){
                $adb->pquery("UPDATE vtiger_portal_configuration 
                SET portal_fields = ?", array(json_encode($fields)));
            } else {
                $adb->pquery("INSERT INTO vtiger_portal_configuration 
                (portal_fields) VALUES (?)", array(json_encode($fields)));
            }
            
        }
            
        $response = new Vtiger_Response();
        $response->setResult(array('success' => true));
        $response->emit();
    }
    
    public function chatWidget(Vtiger_Request $request){
        
        global $adb;
        
        $widgetid = $request->get('tawk_widget_id');
        
        if(!empty($widgetid)){
            
            $checkFields = $adb->pquery("SELECT * FROM
            vtiger_portal_configuration", array());
            
            if($adb->num_rows($checkFields)){
                $adb->pquery("UPDATE vtiger_portal_configuration
                SET portal_chat_widget_code = ?", array($widgetid));
            } else {
                $adb->pquery("INSERT INTO vtiger_portal_configuration
                (portal_chat_widget_code) VALUES (?)", array($widgetid));
            }
            
        }
        
        $response = new Vtiger_Response();
        $response->setResult(array('success' => true));
        $response->emit();
        
    }
}
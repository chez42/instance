<?php
class Settings_Vtiger_SaveStratifiConfiguration_Action extends Settings_Vtiger_Basic_Action {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request) {
        
        global $adb;
        
        $rep_codes = $request->get('rep_codes');
        
        if(!empty($rep_codes)){
            
            $result = $adb->pquery("SELECT * FROM 
            vtiger_stratifi_configuration", array());
            
            if($adb->num_rows($result)){
                $adb->pquery("UPDATE vtiger_stratifi_configuration 
                SET rep_codes = ?", array($rep_codes));
            } else {
                $adb->pquery("INSERT INTO vtiger_stratifi_configuration 
                (rep_codes) VALUES (?)", array($rep_codes));
            }
        }
            
        $response = new Vtiger_Response();
        $response->setResult(array('success' => true));
        $response->emit();
    }
}
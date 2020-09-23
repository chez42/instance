<?php
class Settings_Vtiger_SaveEditablePortalProfileFields_Action extends Settings_Vtiger_Basic_Action {
    
    public function process(Vtiger_Request $request) {
        
        global $adb;
        
        $fields = $request->get('fieldIdsList');
        
        if(!empty($fields)){
            
            $checkFields = $adb->pquery("SELECT * FROM 
            vtiger_portal_editable_profile_fields", array());
            
            if($adb->num_rows($checkFields)){
                $adb->pquery("UPDATE vtiger_portal_editable_profile_fields 
                SET portal_fields = ?", array(json_encode($fields)));
            } else {
                $adb->pquery("INSERT INTO vtiger_portal_editable_profile_fields 
                (portal_fields) VALUES (?)", array(json_encode($fields)));
            }
            
        }
            
        $response = new Vtiger_Response();
        $response->setResult(array('success' => true));
        $response->emit();
    }
}
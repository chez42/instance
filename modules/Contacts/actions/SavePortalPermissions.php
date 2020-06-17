<?php
class Contacts_SavePortalPermissions_Action extends Vtiger_Action_Controller {
    
    function checkPermission(Vtiger_Request $request) {
        return true;
    }
    
    function process(Vtiger_Request $request){
        
        global $current_user;
        
        $adb = PearDatabase::getInstance();
        
        $record = $request->get("record");
        
        $field = $request->get("field");
        
        $value = $request->get("value");
        
        $perQuery = $adb->pquery("SELECT * FROM vtiger_contact_portal_permissions WHERE crmid = ?", array($record));
        
        $result = array();
        
        if($adb->num_rows($perQuery)){
            $fieldArray =  explode('[',str_replace(']','',$field));
            $valueField = end($fieldArray);
            
            $adb->pquery("UPDATE vtiger_contact_portal_permissions SET ".$valueField." = ? WHERE crmid = ?",
                array($value,$record));
            
            $result['success'] = true;$result['value'] = $value;
            
        } else {
            
            $fieldArray =  explode('[',str_replace(']','',$field));
            $valueField = end($fieldArray);
            
            $adb->pquery("insert into vtiger_contact_portal_permissions
			(crmid, " . $valueField . ") values (" . $record. "," . $value . ")",array());
            
            $result['success'] = true;
            $result['value'] = $value;
            
            
        }
        
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
    
    
}
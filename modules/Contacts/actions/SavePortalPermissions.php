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
            $valueField = array(end($fieldArray)=>$value);
            
            if(count($fieldArray) > 3){
                $field_Array[$fieldArray[1]]['allowed_reports'][$fieldArray[3]][$fieldArray[4]] = $valueField;
            }else{
                $field_Array[$fieldArray[1]] = $valueField;
            }
           
            $permissions = $adb->query_result($perQuery, 0, 'permissions');
            $prePerVal = json_decode(html_entity_decode($permissions),true);
            $newValue = array_replace_recursive($prePerVal, $field_Array);
            
            $adb->pquery("UPDATE vtiger_contact_portal_permissions SET permissions = ? WHERE crmid = ?",
                array(json_encode($newValue),$record));
            
            $result['success'] = true;$result['value'] = $value;
            
        } else {
            
            $result['error'] = true;
            
        }
        
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
    
    
}
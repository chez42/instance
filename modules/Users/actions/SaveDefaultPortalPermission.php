<?php
class Users_SaveDefaultPortalPermission_Action extends Vtiger_Action_Controller {
    
    function checkPermission(Vtiger_Request $request) {
        return true;
    }
    
    function process(Vtiger_Request $request){
        
        global $current_user;
        
        $result = array();
        
        $record = $request->get('record');
        if($request->get('from'))
            $record = 0;
        
        $adb = PearDatabase::getInstance();
        
        $portal_module_permission = $request->get("portalModulesInfo");
            
        if(!empty($portal_module_permission) ){
            
            $portal_permission_result = $adb->pquery("select * from vtiger_default_portal_permissions where userid = ?",array($record));
            
            $queryFields = '';
            
            $totalCount = count($portal_module_permission);
            $fieldCount = 1;
            
            if($adb->num_rows($portal_permission_result)){
                
                foreach($portal_module_permission as $field_name => $field_value){
                    if($field_value != ''){
                        $queryFields .= $field_name .' = '. $field_value;
                        if($fieldCount < $totalCount)
                            $queryFields .= ', ';
                        $fieldCount++;
                    }
                }
                $queryFields = rtrim($queryFields,', ');
                $adb->pquery("update vtiger_default_portal_permissions set ".$queryFields." where userid = ?",
                    array($record));
                
            } else {
                
                $queryValues = '';
                foreach($portal_module_permission as $field_name => $field_value){
                    if($field_value != ''){
                        $queryFields .= $field_name ;
                        $queryValues .= $field_value;
                        if($fieldCount < $totalCount){
                            $queryFields .= ', ';
                            $queryValues .= ', ';
                        }
                        $fieldCount++;
                    }
                }
                $queryFields = rtrim($queryFields,', ');
                $queryValues = rtrim($queryValues,', ');
                $adb->pquery("insert into vtiger_default_portal_permissions (userid, ".$queryFields.") values (?, ".$queryValues.")",array($record));
                
            }
            
            $result = array('success'=>true);
        }
        
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
    
    
}
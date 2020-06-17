<?php
class Contacts_UpdatePortalPermission_Action extends Vtiger_Action_Controller {
    
    function checkPermission(Vtiger_Request $request) {
        return true;
    }
    
    function process(Vtiger_Request $request){
        
        global $current_user;
        
        $result = array();
        
        $records = $this->getRecordsListFromRequest($request);
        
        $adb = PearDatabase::getInstance();
        
        $portal_module_permission = $request->get("portalModulesInfo");
        
        foreach($records as $record){
            
            if(!empty($portal_module_permission) && $record){
                
                $portal_permission_result = $adb->pquery("select * from vtiger_contact_portal_permissions where crmid = ?",array($record));
                
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
                    $adb->pquery("update vtiger_contact_portal_permissions set ".$queryFields." where crmid = ?",
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
                    $adb->pquery("insert into vtiger_contact_portal_permissions (crmid, ".$queryFields.") values (?, ".$queryValues.")",array($record));
                    
                }
            }
        }
        
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
    
    
    public function getRecordsListFromRequest(Vtiger_Request $request) {
        $cvId = $request->get('viewname');
        $module = $request->get('module');
        if(!empty($cvId) && $cvId=="undefined"){
            $sourceModule = $request->get('sourceModule');
            $cvId = CustomView_Record_Model::getAllFilterByModule($sourceModule)->getId();
        }
        $selectedIds = $request->get('selected_ids');
        $excludedIds = $request->get('excluded_ids');
        
        if(!empty($selectedIds) && $selectedIds != 'all') {
            if(!empty($selectedIds) && count($selectedIds) > 0) {
                return $selectedIds;
            }
        }
        
        $customViewModel = CustomView_Record_Model::getInstanceById($cvId);
        if($customViewModel) {
            $searchKey = $request->get('search_key');
            $searchValue = $request->get('search_value');
            $operator = $request->get('operator');
            if(!empty($operator)) {
                $customViewModel->set('operator', $operator);
                $customViewModel->set('search_key', $searchKey);
                $customViewModel->set('search_value', $searchValue);
            }
            
            /**
             *  Mass action on Documents if we select particular folder is applying on all records irrespective of
             *  seleted folder
             */
            if ($module == 'Documents') {
                $customViewModel->set('folder_id', $request->get('folder_id'));
                $customViewModel->set('folder_value', $request->get('folder_value'));
            }
            
            $customViewModel->set('search_params',$request->get('search_params'));
            return $customViewModel->getRecordIds($excludedIds,$module);
        }
    }
    
}
<?php

class DocumentFolder_ViewPermissions_Action extends Vtiger_Action_Controller {
    
    function checkPermission(Vtiger_Request $request) {
        return;
    }
    
    public function process(Vtiger_Request $request) {
        
        global $adb,$current_user;
        
        $moduleName = $request->getModule();
        $record = $request->get('record');
       
        $result = array('success'=>false);
        
        if ($record) {
            
            $docFolderModel = Vtiger_Record_Model::getInstanceById($record, 'DocumentFolder');
            $docFolderModel->set('folder_name', $request->get('foderName'));
            
            if (!$docFolderModel->checkDuplicate()) {
                
                $view_permission = $request->get('view_permissions');
                
                $recordModel = CRMEntity::getInstance($moduleName);
                $recordModel->id = $record;
                $recordModel->retrieve_entity_info($record,$moduleName);
                $recordModel->mode = 'edit';
                $recordModel->column_fields['view_permission'] = implode(' |##| ',$view_permission);
                $recordModel->column_fields["hide_from_portal"] = $request->get('portalvalue');
                $recordModel->column_fields["default_for_all_users"] = $request->get('default_for_all_users');
                $recordModel->column_fields["folder_name"] = $request->get('foderName');
                $recordModel->save($moduleName);
                
                
                $user_name = '';
                $adb->pquery("DELETE FROM vtiger_documentfolder_view_permissions WHERE documentfolderid = ?",array($record));
                
                if(!in_array($current_user->id, $view_permission))
                    $view_permission[] = $current_user->id;
                
                    foreach($view_permission as $key=>$user_ids){
                    
                    $adb->pquery("INSERT INTO vtiger_documentfolder_view_permissions(documentfolderid, share_permission_id) VALUES (?,?)",
                        array($record,$user_ids));
                    
                    if($user_ids != $current_user->id){
                        if($key > 0){
                            $user_name .= ', ';
                        }
                                
                        $user_name .= getUserFullName($user_ids);
                    }
                
                }
                
                $result = array('success'=>true,'folderName'=>$request->get('foderName'), 'users'=>$user_name, 'portal'=>$request->get('portalvalue')?'Yes':'No', 
                    'defaultForUsers'=>$request->get('default_for_all_users')?'Yes':'No' );
            }else{
                
                $result = array('success'=>false,'message'=>vtranslate('LBL_FOLDER_EXISTS', 'DocumentFolder'));
                
            }
        } 
        
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
}

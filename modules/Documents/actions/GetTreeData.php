<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Documents_GetTreeData_Action extends Vtiger_Action_Controller {
    
    function __construct() {
        parent::__construct();
        $this->exposeMethod('get_data');
        $this->exposeMethod('create_node');
        $this->exposeMethod('rename_node');
        $this->exposeMethod('delete_node');
        $this->exposeMethod('move_node');
        $this->exposeMethod('updateShowfolder');
        $this->exposeMethod('delete_folder');
        $this->exposeMethod('create_folder');
        $this->exposeMethod('edit_folder');
    }
    
    public function checkPermission(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        
        if(!Users_Privileges_Model::isPermitted($moduleName, 'DetailView', $request->get('record'))) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED', $moduleName));
        }
    }
    
    public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
        }
    }
    
    public function get_data($request) {
        
        $moduleName = $request->getModule();
        
        $relatedModuleName = 'Documents';
        
        $parentId = $request->get('record') ;
        
        $moduleModel = Vtiger_Module_Model::getInstance($relatedModuleName);
        
        $fieldList = $moduleModel->getFields();
        
        $docFolderModel = ($fieldList['doc_folder_id'])?$fieldList['doc_folder_id']:array();
        
        $accessible_folders = $this->getDocumentFolderWithParentList($parentId);
        
        $tree = $moduleModel->buildTree($accessible_folders);
        
        $result = array();
        
        
        if (!empty ($parentId)) {
            
            echo json_encode($tree);
            
        }
    }
    
    
    function getDocumentFolderWithParentList($contactid) {
        
        $db = PearDatabase::getInstance();
        
        $moduleName = "DocumentFolder";
        
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        
        $queryGenerator = new QueryGenerator($moduleName, $currentUserModel);
        
        $queryGenerator->setFields( array('folder_name','id', 'parent_id','is_default') );
        
        $listviewController = new ListViewController($db, $currentUserModel, $queryGenerator);
        
        $query = $queryGenerator->getQuery();
        
        $result = $db->pquery($query,array());
        
        $rows = $db->num_rows($result);
        
        $folders = array();
        
        $folders['xxx'] =  array(
            "id"=>'xxx',
            "parent_id"=>'',
            "text"=>'/',
            'is_default'=>1,
            "type"=>"folder") ;
        
        for($i=0; $i<$rows; $i++){
            
            $folderId = $db->query_result($result, $i, 'documentfolderid');
            
            $folderName = $db->query_result($result, $i, 'folder_name');
            
            $parent_id = $db->query_result($result, $i, 'parent_id');
            
            if($parent_id){
              
                $folders[$folderId] =  array(
                    
                    "id"=>$folderId,
                    "parent_id"=>$parent_id,
                    "text"=>$folderName,
                    "type"=>"folder",
                    "icon"=>"jstree-folder",
                    
                );
                
            }else{
            
                $folders[$folderId] =  array(
                    
                    "id"=>$folderId,
                    "parent_id"=>$parent_id,
                    "text"=>$folderName,
                    "type"=>"folder",
                    "icon"=>"jstree-folder",
                    
                );
                
            }
        }
        
        foreach($folders as $key=>$value){
            
            if($key!='') {
                
                $parentRecordModel = Vtiger_Record_Model::getInstanceById($contactid, getSalesEntityType($contactid));
                $relationListView = Vtiger_RelationListView_Model::getInstance($parentRecordModel, 'Documents', 'Documents');
                $query = $relationListView->getRelationQuery();
                $query.= " AND vtiger_notes.doc_folder_id = ?";
                
                $result = $db->pquery($query,array($key));
               
                $rows = $db->num_rows($result);
                
                for($i=0; $i<$rows; $i++){
                    
                    $folderId = $db->query_result($result, $i, 'crmid');
                    
                    $documentRecordModel = Vtiger_Record_Model::getInstanceById($folderId,'Documents');
                    
                    $record_id = vtws_getWebserviceEntityId('Documents',$folderId);
                    
                    $folderName = $db->query_result($result, $i, 'title');
                    
                    $parent_id = $key ;
                    
                    $child ='';
                    
                    $locationType = $db->query_result($result, $i, 'filelocationtype');
                    
                    $href_url = '';
                    
                    if($locationType == 'I'){
                        
                        $href_url = $documentRecordModel->getDownloadFileURL();
                        
                    }elseif($locationType == 'E'){
                        
                        $href_url = $documentRecordModel->getDetailViewUrl();
                        
                    }
                    
                    if($parent_id){
                        
                        $folders[$record_id] =  array(
                            
                            "id"=>$record_id,
                            "parent_id"=>$parent_id,
                            "icon"=>"jstree-file",
                            "text"=>$folderName,
                            "children"=>true,
                            "type"=>"file",
                            "a_attr"=>array("href"=>$href_url,'target'=>'_blank')
                            
                        );
                        
                    }else{
                        
                        $folders[$record_id] =  array(
                            
                            "id"=>$record_id,
                            "parent_id"=>$parent_id,
                            "icon"=>"jstree-file",
                            "text"=>$folderName,
                            "type"=>"file",
                            "a_attr"=>array('href'=>$href_url,'target'=>'_blank')
                            
                        );
                        
                    }
                }
                
            }
        }
        
        return $folders;
    }
    
   
    public function create_node($request) {
        
        if(Users_Privileges_Model::isPermitted('DocumentFolder', 'CreateView')) {
            
            $recordModel = Vtiger_Record_Model::getCleanInstance('DocumentFolder');
            $recordModel->set('folder_name', $request->get('text'));
            if (!$recordModel->checkDuplicate()) {
                $record = $request->get('id');
                
                $docFolder_obj = CRMEntity::getInstance('DocumentFolder');
                
                $docFolder_obj->column_fields['folder_name'] = $request->get('text');
                
                if($record != 'xxx' && $record != ''){
                    
                    $docFolder_obj->column_fields['parent_id'] = $record;
                    
                }
                
                $docFolder_obj->save('DocumentFolder');
                
                if($docFolder_obj->id){
                    
                    $result = array("success"=>true,'id'=>$docFolder_obj->id,'message'=>'Folder create successfully');
                    
                }else{
                    
                    $result = array("success"=>false,'message'=>'Something wrong!');
                    
                }
            }else{
                
                $result = array("success"=>false,'message'=>vtranslate('LBL_FOLDER_EXISTS', 'DocumentFolder'));
                
            }
        }else{
            $result = array("success"=>false, 'message'=>'Permission Denied!');
        }
            
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
        
    }
    
    public function rename_node($request) {
        
        global $adb;
        
        $record = $request->get('id');
        
        if(Users_Privileges_Model::isPermitted('DocumentFolder', 'EditView', $record)) {
            
            $recordModel = Vtiger_Record_Model::getInstanceById($record, 'DocumentFolder');
            $recordModel->set('folder_name', $request->get('text'));
            if (!$recordModel->checkDuplicate()) {
                
                $result ='';
                
                $docFolder = $adb->pquery("SELECT * FROM vtiger_documentfolder
                INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_documentfolder.documentfolderid
                WHERE vtiger_crmentity.deleted = 0 AND vtiger_documentfolder.documentfolderid = ?",array($record));
                
                if($adb->num_rows($docFolder)){
                    
                    $docFolder_id = $adb->query_result($docFolder,0,'documentfolderid');
                    
                    $docFolder_obj = CRMEntity::getInstance('DocumentFolder');
                    
                    $docFolder_obj->id = $docFolder_id;
                    $docFolder_obj->mode = 'edit';
                    $docFolder_obj->retrieve_entity_info($docFolder_id,"DocumentFolder");
                    
                    $docFolder_obj->column_fields['folder_name'] = $request->get('text');
                    
                    $docFolder_obj->save('DocumentFolder');
                    
                    if($docFolder_obj->id){
                        $result = array("success"=>true,'id'=>$docFolder_obj->id, 'message'=>'Folder update Successfully');
                    }
                    
                }else{
                    
                    $result = array("success"=>false,'message'=>'Something wrong!');
                    
                }
            }else{
                
                $result = array("success"=>false,'message'=>vtranslate('LBL_FOLDER_EXISTS', 'DocumentFolder'));
                
            }
        }else{
            $result = array("success"=>false, 'message'=>'Permission Denied!');
        }
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
        
    }
    
    public function delete_node($request) {
       
        global $adb;
        $moduleModel = Vtiger_Module_Model::getInstance('Documents');
        
        $currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        if($currentUserPriviligesModel->hasModuleActionPermission($moduleModel->getId(), 'Delete')) {
            
            $record = $request->get('id');
            
            if($record){
                
                $documents = $adb->pquery("SELECT * FROM vtiger_notes
                    WHERE vtiger_notes.doc_folder_id = ?",array($record));
                
                if($adb->num_rows($documents)){
                    
                    $result = array("success"=>false);
                    
                }else{
                    
                    $adb->pquery("UPDATE vtiger_crmentity SET deleted = 1 WHERE crmid = ?",array($record));
                    
                    $result = array("success"=>true);
                }
            }
        }else{
            $result = array("success"=>false, 'message'=>'Permission Denied!');
        }
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
        
    }
    
    public function move_node(Vtiger_Request $request) {
        
        $node = explode('x',$request->get('id'));
        
        $parent = $request->get('parent');
        
        $record = $node[1];
        if(Users_Privileges_Model::isPermitted('Documents', 'EditView', $record)) {
            global $adb;
            
            if($record){
                
                $documents = $adb->pquery("SELECT * FROM vtiger_notes
                INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_notes.notesid
                WHERE vtiger_crmentity.deleted = 0 AND vtiger_notes.notesid = ?",array($record));
                
                if($adb->num_rows($documents)){
                   
                    $adb->pquery("UPDATE vtiger_notes SET doc_folder_id = ? WHERE vtiger_notes.notesid = ?",array($parent,$record));
                    
                    $result = array("success"=>true);
                    
                }else{
                    
                    $result = array("success"=>false);
                }
            }
        }else{
            $result = array("success"=>false, 'message'=>'Permission Denied!');
        }
        
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
        
    }
    
    /**
     * Function to update TurnOfConfirmation
     */
    public function updateShowfolder(Vtiger_Request $request) {
        $response = new Vtiger_Response();
        
        $userId = $request->get('userid');
        global $adb;
        
        if($request->get("folder_value")){
            
            $adb->pquery("UPDATE vtiger_users SET show_hidden_folders = 1 WHERE id = ?",array($userId));
            
            require_once('modules/Users/CreateUserPrivilegeFile.php');
            createUserPrivilegesfile($userId);
            
            $response->setResult('Success');
        }else{
            
            $adb->pquery("UPDATE vtiger_users SET show_hidden_folders = 0 WHERE id = ?",array($userId));
            
            require_once('modules/Users/CreateUserPrivilegeFile.php');
            createUserPrivilegesfile($userId);
            
            $response->setError('Success');
        }
        $response->emit();
    }
    
    public function delete_folder($request) {
        
        global $adb;
        $moduleModel = Vtiger_Module_Model::getInstance('DocumentFolder');
        
        $currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        if($currentUserPriviligesModel->hasModuleActionPermission($moduleModel->getId(), 'Delete')) {
            
            $record = $request->get('id');
            $recordModel = Vtiger_Record_Model::getInstanceById($record, 'DocumentFolder');
            
            $subFolder = $adb->pquery("SELECT * FROM vtiger_documentfolder 
            INNER  JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_documentfolder.documentfolderid
            WHERE vtiger_crmentity.deleted = 0 AND vtiger_documentfolder.parent_id = ?",array($record));
            
           
            if(!$recordModel->hasDocuments() && !$adb->num_rows($subFolder)){
               
                $recordModel = Vtiger_Record_Model::getInstanceById($record, 'DocumentFolder');
                $recordModel->delete();
                //$adb->pquery("UPDATE vtiger_crmentity SET deleted = 1 WHERE crmid = ?",array($record));
                
                $result = array("success"=>true, 'message'=>'Folder deleted successfully');
               
            }else{
                
                $result = array("success"=>false, 'message'=>'Please move documents and subfolders from folder before deleting');
                
            }
        }else{
            $result = array("success"=>false, 'message'=>'Permission Denied!');
        }
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
        
    }
    
    public function create_folder($request) {
       
        global $adb,$current_user;
        
        if(Users_Privileges_Model::isPermitted('DocumentFolder', 'CreateView')) {
            
            $recordModel = Vtiger_Record_Model::getCleanInstance('DocumentFolder');
            $recordModel->set('folder_name', $request->get('text'));
            if (!$recordModel->checkDuplicate()) {
                $record = $request->get('id');
                
                $docFolder_obj = CRMEntity::getInstance('DocumentFolder');
                
                $docFolder_obj->column_fields['folder_name'] = $request->get('text');
                
                if($record != 'xxx' && $record != ''){
                    
                    $docFolder_obj->column_fields['parent_id'] = $record;
                    
                }
                if($request->get('default_for_all_users')){
                    $docFolder_obj->column_fields['default_for_all_users'] = 1;
                }else{
                    $docFolder_obj->column_fields['default_for_all_users'] = 0;
                }
                
                if($request->get('view_permissions')){
                    $docFolder_obj->column_fields['view_permission'] = implode(' |##| ',$request->get('view_permissions'));
                }else{
                    $docFolder_obj->column_fields['view_permission'] = '';
                }
                
                $docFolder_obj->save('DocumentFolder');
                
                if($docFolder_obj->id){
                    $view_permission =array();
                    if($request->get('view_permissions')){
                        $view_permission = $request->get('view_permissions');
                        
                        foreach($view_permission as $key=>$user_ids){
                            $adb->pquery("INSERT INTO vtiger_documentfolder_view_permissions(documentfolderid, share_permission_id) VALUES (?,?)",
                                array($docFolder_obj->id,$user_ids));
                        }
                    }
                   
                    if($request->get('is_default')){
                        
                        $adb->pquery("UPDATE vtiger_users SET default_documents_folder_id = ? WHERE id = ?",array($docFolder_obj->id,$current_user->id));
                        require_once('modules/Users/CreateUserPrivilegeFile.php');
                        createUserPrivilegesfile($current_user->id);
                    }
                    
                    $result = array("success"=>true,'id'=>$docFolder_obj->id,'message'=>'Folder create successfully','info'=>array('folderid'=>$docFolder_obj->id,'folderName'=>$request->get('text')));
                    
                }else{
                    
                    $result = array("success"=>false,'message'=>'Something wrong!');
                    
                }
            }else{
                
                $result = array("success"=>false,'message'=>vtranslate('LBL_FOLDER_EXISTS', 'DocumentFolder'));
                
            }
        }else{
            $result = array("success"=>false, 'message'=>'Permission Denied!');
        }
        
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
        
    }
    
    public function edit_folder($request) {
        
        global $adb,$current_user;
        
        $record = $request->get('id');
        
        if(Users_Privileges_Model::isPermitted('DocumentFolder', 'EditView', $record)) {
            
            $recordModel = Vtiger_Record_Model::getInstanceById($record, 'DocumentFolder');
            $recordModel->set('folder_name', $request->get('text'));
            if (!$recordModel->checkDuplicate()) {
                
                $result ='';
                
                $docFolder = $adb->pquery("SELECT * FROM vtiger_documentfolder
                INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_documentfolder.documentfolderid
                WHERE vtiger_crmentity.deleted = 0 AND vtiger_documentfolder.documentfolderid = ?",array($record));
                
                if($adb->num_rows($docFolder)){
                    
                    $docFolder_id = $adb->query_result($docFolder,0,'documentfolderid');
                    
                    $docFolder_obj = CRMEntity::getInstance('DocumentFolder');
                    
                    $docFolder_obj->id = $docFolder_id;
                    $docFolder_obj->mode = 'edit';
                    $docFolder_obj->retrieve_entity_info($docFolder_id,"DocumentFolder");
                    
                    $docFolder_obj->column_fields['folder_name'] = $request->get('text');
                    
                    if($request->get('default_for_all_users')){
                        $docFolder_obj->column_fields['default_for_all_users'] = 1;
                    }else{
                        $docFolder_obj->column_fields['default_for_all_users'] = 0;
                    }
                    if($request->get('hide_from_portal')){
                        $docFolder_obj->column_fields['hide_from_portal'] = 1;
                    }else{
                        $docFolder_obj->column_fields['hide_from_portal'] = 0;
                    }
                    
                    if($request->get('view_permissions')){
                        $docFolder_obj->column_fields['view_permission'] = implode(' |##| ',$request->get('view_permissions'));
                    }else{
                        $docFolder_obj->column_fields['view_permission'] = '';
                    }
                    
                    $docFolder_obj->save('DocumentFolder');
                    
                    if($docFolder_obj->id){
                       
                        if($request->get('view_permissions')){
                            $view_permission = $request->get('view_permissions');
                            $adb->pquery("DELETE FROM vtiger_documentfolder_view_permissions WHERE documentfolderid = ?",array($record));
                            
                            if(!in_array($current_user->id, $view_permission))
                                $view_permission[] = $current_user->id;
                            
                            foreach($view_permission as $key=>$user_ids){
                                $adb->pquery("INSERT INTO vtiger_documentfolder_view_permissions(documentfolderid, share_permission_id) VALUES (?,?)",
                                    array($record,$user_ids));
                            }
                        }else{
                            $adb->pquery("DELETE FROM vtiger_documentfolder_view_permissions WHERE documentfolderid = ? and share_permission_id !=?",array($record,$current_user->id));
                        }
                        
                        if($request->get('is_default')){
                            
                            $adb->pquery("UPDATE vtiger_users SET default_documents_folder_id = ? WHERE id = ?",array($docFolder_obj->id,$current_user->id));
                            require_once('modules/Users/CreateUserPrivilegeFile.php');
                            createUserPrivilegesfile($current_user->id);
                        }
                        
                        $result = array("success"=>true,'id'=>$docFolder_obj->id, 'message'=>'Folder update Successfully','info'=>array('folderid'=>$docFolder_obj->id,'folderName'=>$request->get('text')));
                    }
                    
                }else{
                    
                    $result = array("success"=>false,'message'=>'Something wrong!');
                    
                }
            }else{
                
                $result = array("success"=>false,'message'=>vtranslate('LBL_FOLDER_EXISTS', 'DocumentFolder'));
                
            }
        }else{
            $result = array("success"=>false, 'message'=>'Permission Denied!');
        }
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
        
    }
}


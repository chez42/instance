<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vtiger_Folder_View extends Vtiger_RelatedList_View {
    
    function __construct() {
        parent::__construct();
        $this->exposeMethod('openFolderFiles');
        $this->exposeMethod('loadMoreFiles');
        $this->exposeMethod('sidbarEssentials');
    }
    
    function process (Vtiger_Request $request) {
        //echo"<pre>";print_r($request);echo"</pre>";exit;
        $mode = $request->get('submode');
        if(!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
       
        $viewer = $this->getViewer ($request);
        $moduleName = 'Documents';
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $moduleFields = $moduleModel->getFields();
        
        $fieldsInfo = array();
        foreach($moduleFields as $fieldName => $fieldModel){
            $fieldsInfo[$fieldName] = $fieldModel->getFieldInfo();
        }
        $viewer->assign('FIELDS_INFO', json_encode($fieldsInfo));
        
        global $adb,$current_user;
        
        $parentId = $request->get('record');
        $viewer->assign('PARENT_ID',$parentId);
        $entity_id = VtigerWebserviceObject::fromName($adb,$moduleName);
        $doc_entity_id = $entity_id->getEntityId();
        $viewer->assign('DOC_ENTITY_ID',$doc_entity_id);
        $relatedlink = $adb->pquery("SELECT relation_id,name FROM vtiger_relatedlists WHERE tabid = ? AND related_tabid = ?",
            array(getTabid($request->getModule()),getTabid($moduleName)));
        $linkId = '';
        $function= '';
        if($adb->num_rows($relatedlink)){
            $linkId = $adb->query_result($relatedlink,0,'relation_id');
            $function = $adb->query_result($relatedlink,0,'name');
        }
        
        if($request->get('show_empty_folders')){
            $showFolder = $request->get('show_empty_folders');
            if($showFolder == 'Yes')
                $emptyFolders = 1;
                else
                    $emptyFolders = 0;
        }else{
            $emptyFolders = $current_user->show_hidden_folders;
        }
        
        if($emptyFolders){
            $folderList = Documents_Module_Model::getAllDocumentFolders();
        }else{
            
            $relQuery = $moduleModel->getRelationQuery($parentId,$function,$moduleModel,$linkId);
            $newQuery = preg_split('/FROM/i', $relQuery);
            $selectColumnSql = 'SELECT DISTINCT vtiger_notes.doc_folder_id ';
            $query = $selectColumnSql.' FROM '.$newQuery[1];
            $result = $adb->pquery($query,array());
            $rows = $adb->num_rows($result);
            $folders = array();
           
            for($i=0; $i<$rows; $i++){
                
                $folderId = $adb->query_result($result, $i, 'doc_folder_id');
                
                $docQuery = $adb->pquery("SELECT vtiger_documentfolder.documentfolderid FROM vtiger_documentfolder 
                INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_documentfolder.documentfolderid
                WHERE vtiger_crmentity.deleted = 0 AND vtiger_documentfolder.documentfolderid =?",array($folderId));
                
                if($adb->num_rows($docQuery))
                    $folders[$folderId] = Vtiger_Record_Model::getInstanceById($folderId, 'DocumentFolder');
                
            }
            
            $folderList = $folders;
        }
        
        
        
       
        $viewer->assign('VIEW', $request->get('view'));
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('FOLDERS', $folderList);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('SHOWFOLDER', $emptyFolders);
        
        $viewer->assign('LINKID', $linkId);
        $relatedmoduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $viewer->assign('RELATED_MODULE',$relatedmoduleModel);
        $viewer->assign('IS_CREATE_PERMITTED', $relatedmoduleModel->isPermitted('CreateView'));
        $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentId, $request->getModule());
        $relationListView = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $moduleName, '');
        $links = $relationListView->getLinks();
        $relationModel = $relationListView->getRelationModel();
        $relationField = $relationModel->getRelationField();
        $viewer->assign('RELATED_LIST_LINKS', $links);
        $viewer->assign('RELATION_FIELD', $relationField);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('SCRIPTS',$this->getHeaderScripts($request));
        $viewer->assign('STYLES',$this->getHeaderCss($request));
        $viewer->view('FolderViewRelatedContents.tpl', $moduleName);
    }
    
    function sidbarEssentials (Vtiger_Request $request) {
        
        $viewer = $this->getViewer ($request);
        $moduleName = 'Documents';
        
        $folderList = Documents_Module_Model::getAllDocumentFolders();
        
        $viewer->assign('FOLDERS', $folderList);
        $viewer->assign('MODULE', $moduleName);
        $viewer->view('partials/FolderSidebarEssentials.tpl', $moduleName);
    }
    
    
    function openFolderFiles(Vtiger_Request $request){
        
        $moduleName = 'Documents';
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $folderId = $request->get('record');
        
        $viewer = $this->getViewer ($request);
       
        if(!$folderId){
            global $adb,$current_user;
            
            $parentId = $request->get('parentid');
            $entity_id = VtigerWebserviceObject::fromName($adb,$moduleName);
            $doc_entity_id = $entity_id->getEntityId();
            $relatedlink = $adb->pquery("SELECT relation_id,name FROM vtiger_relatedlists WHERE tabid = ? AND related_tabid = ?",
                array(getTabid($request->getModule()),getTabid($moduleName)));
            $linkId = '';
            $function= '';
            if($adb->num_rows($relatedlink)){
                $linkId = $adb->query_result($relatedlink,0,'relation_id');
                $function = $adb->query_result($relatedlink,0,'name');
            }
            
            if($current_user->show_hidden_folders){
                
                $folderData = Documents_Module_Model::getAllDocumentFolders();
                
            }else{
                
                $relQuery = $moduleModel->getRelationQuery($parentId,$function,$moduleModel,$linkId);
                $newQuery = preg_split('/FROM/i', $relQuery);
                $selectColumnSql = 'SELECT DISTINCT vtiger_notes.doc_folder_id ';
                $query = $selectColumnSql.' FROM '.$newQuery[1];
                $result = $adb->pquery($query,array());
                $rows = $adb->num_rows($result);
                $folders = array();
                
                for($i=0; $i<$rows; $i++){
                    
                    $folder_Id = $adb->query_result($result, $i, 'doc_folder_id');
                    
                    $docQuery = $adb->pquery("SELECT vtiger_documentfolder.documentfolderid FROM vtiger_documentfolder
                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_documentfolder.documentfolderid
                    WHERE vtiger_crmentity.deleted = 0 AND vtiger_documentfolder.documentfolderid =?",array($folder_Id));
                    
                    if($adb->num_rows($docQuery))
                        $folders[$folder_Id] = Vtiger_Record_Model::getInstanceById($folder_Id, 'DocumentFolder');
                        
                }
                
                $folderData = $folders;
            }
            
        }
        
        if($folderId){
            
            $folderData = $this->getDocumentFolderWithParentList($folderId,$request->get('parentid'),$request->getModule());
            
            $docFolder = DocumentFolder_Record_Model::getInstanceById($folderId);
            $folderName = $docFolder->getName();
            
            $viewer->assign('FOLDERNAME',$folderName);
            $viewer->assign('MODE', $request->get('submode'));
            
        }
        
        $viewer->assign('INDEX', '50');
        $viewer->assign('FOLDERID', $folderId);
        $viewer->assign('VIEW', $request->get('view'));
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('FOLDERS', $folderData);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->view('FolderContent.tpl', $moduleName);
        
    }
    
    function loadMoreFiles(Vtiger_Request $request){
        
        $viewer = $this->getViewer ($request);
        $moduleName = 'Documents';
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        
        $folderId = $request->get('record');
        $index = $request->get('index');
        
        $folderFiles = $this->folderFiles($folderId, $index, $request->get('parentid'));
        
        $count = count($folderFiles);
        
        if(!$count) $count = 0;
        else $count = 1;
        
        $viewer->assign('COUNT', $count);
        $viewer->assign('FOLDERS', $folderFiles);
        $viewer->view('FolderFilesContent.tpl', $moduleName);
        
    }
    
    function getDocumentFolderWithParentList($folderId, $parentId,$module ) {
        $db = PearDatabase::getInstance();
        
        global $current_user;
        
        if($current_user->show_hidden_folders){
            
            $moduleName = "DocumentFolder";
            $currentUserModel = Users_Record_Model::getCurrentUserModel();
            $queryGenerator = new QueryGenerator($moduleName, $currentUserModel);
            $queryGenerator->setFields( array('folder_name','id', 'parent_id','is_default') );
            $listviewController = new ListViewController($db, $currentUserModel, $queryGenerator);
            $query = $queryGenerator->getQuery();
            $query .= " AND vtiger_documentfolder.parent_id = ?";
            $result = $db->pquery($query,array($folderId));
            $rows = $db->num_rows($result);
            $folders = array();
            for($i=0; $i<$rows; $i++){
                $folder_id = $db->query_result($result, $i, 'documentfolderid');
                $folderName = $db->query_result($result, $i, 'folder_name');
                $parent_id = $db->query_result($result, $i, 'parent_id');
                if($parent_id){
                    $folders[$folder_id] =  array(
                        "id"=>$folder_id,
                        "parent_id"=>$parent_id,
                        "text"=>$folderName,
                        "type"=>"folder",
                    );
                }
            }
            
        }else{
            
            $moduleModel = Vtiger_Module_Model::getInstance('Documents');
            $entity_id = VtigerWebserviceObject::fromName($db,'Documents');
            $doc_entity_id = $entity_id->getEntityId();
            $relatedlink = $db->pquery("SELECT relation_id,name FROM vtiger_relatedlists WHERE tabid = ? AND related_tabid = ?",
                array(getTabid($module),getTabid('Documents')));
            $linkId = '';
            $function= '';
            if($db->num_rows($relatedlink)){
                $linkId = $db->query_result($relatedlink,0,'relation_id');
                $function = $db->query_result($relatedlink,0,'name');
            }
            
            $relQuery = $moduleModel->getRelationQuery($parentId,$function,$moduleModel,$linkId);
            $newQuery = preg_split('/FROM/i', $relQuery);
            $selectColumnSql = 'SELECT DISTINCT vtiger_notes.doc_folder_id ';
            $query = $selectColumnSql.' FROM '.$newQuery[1];
            $newQuery1 = preg_split('/WHERE/i', $query);
            $query = $newQuery1[0];
            $query .= ' INNER JOIN vtiger_documentfolder ON vtiger_documentfolder.documentfolderid = vtiger_notes.doc_folder_id ';
            $query .= ' WHERE '.$newQuery1[1].' AND vtiger_documentfolder.parent_id ='.$folderId;
           
            $result = $db->pquery($query,array());
            $rows = $db->num_rows($result);
            $folders = array();
            
            for($i=0; $i<$rows; $i++){
                
                $folder_Id = $db->query_result($result, $i, 'doc_folder_id');
                
                $docQuery = $db->pquery("SELECT vtiger_documentfolder.documentfolderid,vtiger_documentfolder.folder_name,
                    vtiger_documentfolder.parent_id FROM vtiger_documentfolder
                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_documentfolder.documentfolderid
                    WHERE vtiger_crmentity.deleted = 0 AND vtiger_documentfolder.documentfolderid =?  ",
                    array($folder_Id));
                
                if($db->num_rows($docQuery)){
                    
                    $folder_id = $db->query_result($docQuery, $i, 'documentfolderid');
                    $folderName = $db->query_result($docQuery, $i, 'folder_name');
                   
                    $parent_id = $db->query_result($docQuery, $i, 'parent_id');
                    if($parent_id){
                        $folders[$folder_id] =  array(
                            "id"=>$folder_id,
                            "parent_id"=>$parent_id,
                            "text"=>$folderName,
                            "type"=>"folder",
                        );
                    }
                }
            }
            
        }
        
        $folderFiles = $this->folderFiles($folderId, 0, $parentId);
        
        $folders = array_merge($folders,$folderFiles);
        
        return $folders;
    }
    
    function folderFiles($folderId, $startIndex, $parentId){
        
        $folders = array();
        $db = PearDatabase::getInstance();
        
        $query = "select * from vtiger_notes
            inner JOIN vtiger_crmentity on vtiger_crmentity.crmid=vtiger_notes.notesid 
            INNER JOIN vtiger_senotesrel ON vtiger_senotesrel.notesid = vtiger_notes.notesid ";
        global $current_user;
        $query .= getNonAdminAccessControlQuery('Documents',$current_user);
        $query.= "where vtiger_crmentity.deleted=0";
        if($parentId)
            $query.= " AND vtiger_senotesrel.crmid = ".$parentId;
        $query.= " AND vtiger_notes.doc_folder_id = ? LIMIT ".$startIndex.",50";
        $result = $db->pquery($query,array($folderId));
        
        $rows = $db->num_rows($result);
        for($i=0; $i<$rows; $i++){
            $docId = $db->query_result($result, $i, 'notesid');
            $docName = $db->query_result($result, $i, 'title');
            $loctype = $db->query_result($result, $i, 'filelocationtype');
            $fileName = $db->query_result($result, $i, 'filename');
            $file = explode('/',$db->query_result($result, $i, 'filetype'));
            
            if($file[0] == 'image'){
                $icon = 'img.jpg';
                $fileType = 'image File';
            }else if($file[0] == 'video'){
                $icon = 'video.jpg';
                $fileType = 'video File';
            }else if($file[0] == 'text'){
                $icon = 'docx.jpg';
                $fileType = 'text File';
            }else if($file[1] == 'pdf'){
                $icon = 'pdf.jpg';
                $fileType = 'pdf File';
            }else if($file[1] == 'zip'){
                $icon = 'zip.jpg';
                $fileType = 'zip File';
            }else if(strpos($file[1], 'ms')!== false || strpos($file[1], 'vnd') !== false){
                $icon = 'office.jpg';
                $fileType = 'office File';
            }else {
                $icon = 'txt.jpg';
                $fileType = 'doc File';
                if($loctype == 'E')
                    $fileType = 'external File';
            }
            
            $folders[$docId] =  array(
                "id"=>$docId,
                "parent_id"=>$folderId,
                "text"=>$docName,
                "type"=>"file",
                "icon"=> $icon,
                "fileType"=>$fileType,
                "fileLocation"=>$loctype,
                "fileName"=>$fileName,
            );
        }
        
        return $folders;
    }
    
    function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = 'Documents';
        
        $jsFileNames = array(
            "modules.$moduleName.resources.FolderView",
            "~layouts/".Vtiger_Viewer::getDefaultLayoutName()."/lib/jquery/contextMenu/jquery.contextMenu.min.js"
        );
        
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
    
    public function getHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
            "~layouts/".Vtiger_Viewer::getDefaultLayoutName()."/lib/jquery/contextMenu/jquery.contextMenu.min.css",
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);
        return $headerCssInstances;
    }
    
    
}

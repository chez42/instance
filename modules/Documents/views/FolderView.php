<?php
class Documents_FolderView_View extends Vtiger_List_View {
    
    function __construct() {
        parent::__construct();
        $this->exposeMethod('openFolderFiles');
        $this->exposeMethod('loadMoreFiles');
        $this->exposeMethod('sidbarEssentials');
    }
    
    function process (Vtiger_Request $request) {
        
        $mode = $request->get('mode');
        if(!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
       
        $viewer = $this->getViewer ($request);
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $moduleFields = $moduleModel->getFields();
        
        $fieldsInfo = array();
        foreach($moduleFields as $fieldName => $fieldModel){
            $fieldsInfo[$fieldName] = $fieldModel->getFieldInfo();
        }
        $viewer->assign('FIELDS_INFO', json_encode($fieldsInfo));
        
        $folderList = Documents_Module_Model::getAllDocumentFolders();
        
        $viewer->assign('VIEW', $request->get('view'));
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('FOLDERS', $folderList);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->view('FolderViewContents.tpl', $moduleName);
    }
    
    function sidbarEssentials (Vtiger_Request $request) {
        
        $viewer = $this->getViewer ($request);
        $moduleName = $request->getModule();
        
        $folderList = Documents_Module_Model::getAllDocumentFolders();
        
        $viewer->assign('FOLDERS', $folderList);
        $viewer->assign('MODULE', $moduleName);
        $viewer->view('partials/FolderSidebarEssentials.tpl', $moduleName);
    }
    
    function preProcessTplName(Vtiger_Request $request) {
        return '';
    }

    function openFolderFiles(Vtiger_Request $request){
        
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $folderId = $request->get('record');
        
        $viewer = $this->getViewer ($request);
        
        if(!$folderId){
            $folderData = Documents_Module_Model::getAllDocumentFolders();
        }
        
        if($folderId){
           
            
            $folderData = $this->getDocumentFolderWithParentList($folderId);
            
            $docFolder = DocumentFolder_Record_Model::getInstanceById($folderId);
            $folderName = $docFolder->getName();
            
            $viewer->assign('FOLDERNAME',$folderName);
            $viewer->assign('MODE', $request->get('mode'));
            
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
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        
        $folderId = $request->get('record');
        $index = $request->get('index');
        
        $folderFiles = $this->folderFiles($folderId, $index);
        
        $count = count($folderFiles);
        
        if(!$count) $count = 0;
        else $count = 1;
        
        $viewer->assign('COUNT', $count);
        $viewer->assign('FOLDERS', $folderFiles);
        $viewer->view('FolderFilesContent.tpl', $moduleName);
        
    }
    
    function getDocumentFolderWithParentList($folderId ) {
        $db = PearDatabase::getInstance();
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
        
        $folderFiles = $this->folderFiles($folderId, 0);
        
        $folders = array_merge($folders,$folderFiles);
        
        return $folders;
    }
    
    function folderFiles($folderId, $startIndex){
        
        $folders = array();
        $db = PearDatabase::getInstance();
        
        $query = "select * from vtiger_notes
            inner JOIN vtiger_crmentity on vtiger_crmentity.crmid=vtiger_notes.notesid ";
        global $current_user;
        $query .= getNonAdminAccessControlQuery('Documents',$current_user);
        $query.= "where vtiger_crmentity.deleted=0";
        $query.= " AND vtiger_notes.doc_folder_id = ? LIMIT ".$startIndex.",50";
        $result = $db->pquery($query,array($folderId));
       
        $rows = $db->num_rows($result);
        for($i=0; $i<$rows; $i++){
            $docId = $db->query_result($result, $i, 'crmid');
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
        $moduleName = $request->getModule();
        
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
<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Documents_ListAjax_View extends Documents_List_View {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('getRecordsCount');
		$this->exposeMethod('getPageCount');
		$this->exposeMethod('showSearchResults');
		$this->exposeMethod('ShowListColumnsEdit');
		$this->exposeMethod('settings');
	}

	function preProcess(Vtiger_Request $request) {
		return true;
	}

	function postProcess(Vtiger_Request $request) {
		return true;
	}

	function process(Vtiger_Request $request) {
		$mode = $request->get('mode');
		if(!empty($mode)) {
		    
		    if($mode == 'settings'){
		        return $this->getDocumentsSettings($request);
		    }
		    
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	/**
	 * Extending Vtiger List Ajax API to show Advance Search results
	 * @param Vtiger_Request $request
	 */
	public function showSearchResults(Vtiger_Request $request) {
		$vtigerListAjaxInstance = new Vtiger_ListAjax_View();
		$vtigerListAjaxInstance->showSearchResults($request);
	}

	/**
	 * Extending Vtiger List Ajax API to show List Columns Edit view
	 * @param Vtiger_Request $request
	 */
	public function ShowListColumnsEdit(Vtiger_Request $request){
		$vtigerListAjaxInstance = new Vtiger_ListAjax_View();
		$vtigerListAjaxInstance->ShowListColumnsEdit($request);
	}
	
	/**
	 * Extending Vtiger List Ajax API to show documents settings view
	 * @param Vtiger_Request $request
	 */
	public function getDocumentsSettings(Vtiger_Request $request){
	    
	    global $adb;
	    
	    $viewer = $this->getViewer($request);
	    $currentUserModel = Users_Record_Model::getCurrentUserModel();
	    $module = $request->getModule();
	    
	    $documentFolderPicklistValue = Documents_Module_Model::getAllDocumentFolders();
	    
	    $folder = $adb->pquery("SELECT default_documents_folder_id FROM vtiger_users  
        WHERE vtiger_users.id = ?",array($currentUserModel->id));
	    
	    $folderId = '';
	    if($adb->num_rows($folder)){
	        $folderId = $adb->query_result($folder, 0, 'default_documents_folder_id');
	    }
	    
	    $viewId = '';
	    $viewQuery = $adb->pquery("SELECT cvid FROM vtiger_customview WHERE entitytype = 'DocumentFolder' AND viewname = 'All'");
	    
	    if($adb->num_rows($viewQuery)){
	        $viewId = $adb->query_result($viewQuery,0,'cvid');
	    }
	    
	    $pageNumber = $request->get('page');
	    
	    if(empty ($pageNumber)){
	        $pageNumber = '1';
	    }
	    
        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        $pagingModel->set('viewid', $viewId);
        
	    $viewer->assign('PAGING_MODEL', $pagingModel);
	    
	    $viewer->assign('PAGE_NUMBER',$pageNumber);
        
        $folderRecords = DocumentFolder_ListView_Model::getInstance('DocumentFolder',$viewId);
        $entries = $folderRecords->getListViewEntries($pagingModel);
        $totalCount = $folderRecords->getListViewCount();
        
        $pageLimit = $pagingModel->getPageLimit();
        $pageCount = ceil((int) $totalCount / (int) $pageLimit);
        
        if($pageCount == 0){
            $pageCount = 1;
        }
        $viewer->assign('PAGE_COUNT', $pageCount);
        $viewer->assign('LISTVIEW_COUNT', $totalCount);
        
        $viewer->assign('FOLDERID',$folderId);
        $viewer->assign('MODULE',$module);
        $viewer->assign('RECORD', $currentUserModel->id);
        $viewer->assign('FOLDERS',$documentFolderPicklistValue);
        $viewer->assign('FOLDER_ENTRIES',$entries);
        $viewer->assign('VIEWID',$viewId);
        $viewer->assign('FOLDERS_ENTRIES_COUNT',count($documentFolderPicklistValue));
        $viewer->assign('USER_MODEL', $currentUserModel);
        $viewer->assign('SCRIPTS', $this->getHeaderScripts($request));
        $viewer->view('DocumentSettings.tpl', $request->getModule());
        
    }
    
    function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = array();
        $moduleName = $request->getModule();
        
        $jsFileNames = array(
            
            "modules.$moduleName.resources.FolderSettings",
            
        );
        
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
    
    
    
}
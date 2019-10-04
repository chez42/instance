<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Documents_AddFolder_View extends Vtiger_IndexAjax_View {


	public function process (Vtiger_Request $request) {
	    
	     
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
		$parent = $request->get('parent');
		
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		
		if ($request->has('folderid')) {
		    $parent = $request->get('folderid');
		    $folderModel = DocumentFolder_Record_Model::getInstanceById($parent);
		   
		    $viewPermission = $folderModel->folderViewPermissions($parent);
		   
		    $viewer->assign('FOLDER_ID', $parent);
			$viewer->assign('FOLDER_NAME', $folderModel->getName());
			$viewer->assign('GLOBAL',$folderModel->get('default_for_all_users'));
			$viewer->assign('HIDE',$folderModel->get('hide_from_portal'));
			
			if($currentUserModel->default_documents_folder_id == $parent)
			    $viewer->assign('IS_DEFAULT',true);
			
			$viewer->assign('VIEWPERMISSIONS',$viewPermission);
		}
    		
		if(!$parent)
		    $parent = 'xxx';
		
	    if($request->get('src')){
	        $documentParentFolder = $this->getFolderList();
	        $viewer->assign('PARENT_FOLDERS',$documentParentFolder);
	        $viewer->assign('SRC', $request->get('src'));
	        
	    }
		    
	    $viewer->assign('USER_MODEL', $currentUserModel);
		$viewer->assign('SAVE_MODE', $request->get('mode'));
		$viewer->assign('PARENT', $parent);
		$viewer->assign('MODULE',$moduleName);
		$viewer->view('AddFolder.tpl', $moduleName);
	}
	
	function getFolderList(){
	    
	    $db = PearDatabase::getInstance();
	    
	    $moduleName = "DocumentFolder";
	    
	    $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
	    
	    if($currentUserModel->hasModulePermission(getTabid($moduleName))) {
	        
	        $queryGenerator = new QueryGenerator($moduleName, $currentUserModel);
	        
	        $queryGenerator->setFields( array('folder_name','id', 'parent_id') );
	        
	        $listviewController = new ListViewController($db, $currentUserModel, $queryGenerator);
	        
	        $query = $queryGenerator->getQuery();
	        
	        $query.= ' AND vtiger_crmentity.smcreatorid=?';
	        
	        $result = $db->pquery($query,array($currentUserModel->id));
	        
	        $rows = $db->num_rows($result);
	        
	        $folders = array();
	        
	        for($i=0; $i<$rows; $i++){
	            
	            $folderId = $db->query_result($result, $i, 'documentfolderid');
	            $folders[$folderId] = Vtiger_Record_Model::getInstanceById($folderId, 'DocumentFolder');
	            
	        }
	        
	        return $folders;
	    }
	    return array();
	}
	
}
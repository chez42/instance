<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Accounts_Detail_View extends Vtiger_Detail_View {

    function __construct(){
        parent::__construct();
        $this->exposeMethod('showRecentTasks');
    }
    
    
    function showModuleSummaryView($request) {
        $recordId = $request->get('record');
        $moduleName = $request->getModule();
        
        if(!$this->record){
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel = $this->record->getRecord();
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_SUMMARY);
        
        $moduleModel = $recordModel->getModule();
        $viewer = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('SUMMARY_RECORD_STRUCTURE', $recordStrucure->getStructure());
        $viewer->assign('RELATED_ACTIVITIES', $this->getActivities($request));
        $viewer->assign('RELATED_TASKS', $this->showRecentTasks($request));
        
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $pagingModel = new Vtiger_Paging_Model();
        $viewer->assign('PAGING_MODEL', $pagingModel);
        
        $picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
        $viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', Vtiger_Functions::jsonEncode($picklistDependencyDatasource));
        
        return $viewer->view('ModuleSummaryView.tpl', $moduleName, true);
    }
    
	/**
	 * Function to get activities
	 * @param Vtiger_Request $request
	 * @return <List of activity models>
	 */
	public function getActivities(Vtiger_Request $request) {
		$moduleName = 'Calendar';
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);

		$currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		if($currentUserPriviligesModel->hasModulePermission($moduleModel->getId())) {
			$moduleName = $request->getModule();
			$recordId = $request->get('record');

			$pageNumber = $request->get('page');
			if(empty ($pageNumber)) {
				$pageNumber = 1;
			}
			$pagingModel = new Vtiger_Paging_Model();
			$pagingModel->set('page', $pageNumber);
			$pagingModel->set('limit', 10);

			if(!$this->record) {
				$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
			}
			$recordModel = $this->record->getRecord();
			$moduleModel = $recordModel->getModule();

			$relatedActivities = $moduleModel->getCalendarActivities('', $pagingModel, 'all', $recordId);

			$viewer = $this->getViewer($request);
			$viewer->assign('RECORD', $recordModel);
			$viewer->assign('MODULE_NAME', $moduleName);
			$viewer->assign('PAGING_MODEL', $pagingModel);
			$viewer->assign('PAGE_NUMBER', $pageNumber);
			$viewer->assign('ACTIVITIES', $relatedActivities);

			return $viewer->view('RelatedActivities.tpl', $moduleName, true);
		}
	}

	public function showModuleDetailView(Vtiger_Request $request) {
		$recordId = $request->get('record');
		$moduleName = $request->getModule();

		// Getting model to reuse it in parent 
		if (!$this->record) {
			$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}
		$recordModel = $this->record->getRecord();

		$viewer = $this->getViewer($request);
		$viewer->assign('IMAGE_DETAILS', $recordModel->getImageDetails());

		return parent::showModuleDetailView($request);
	}
	/*17-Oct-2018*/
	function showRecentTasks(Vtiger_Request $request){
	    $moduleName = 'Task';
	    $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
	    
	    $currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
	    if($currentUserPriviligesModel->hasModulePermission($moduleModel->getId())) {
	        $moduleName = $request->getModule();
	        $recordId = $request->get('record');
	        
	        $limit = $request->get('limit');
	        
	        $pageNumber = $request->get('page');
	        
	        if(empty ($pageNumber)) {
	            $pageNumber = 1;
	        }
	        
	        if(empty($limit))
	            $limit = 5;
	            
	            $pagingModel = new Vtiger_Paging_Model();
	            
	            $pagingModel->set('page', $pageNumber);
	            
	            if(!empty($limit)) {
	                $pagingModel->set('limit', $limit);
	            }
	            
	            if(!$this->record) {
	                $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
	            }
	            $recordModel = $this->record->getRecord();
	            $moduleModel = $recordModel->getModule();
	            
	            $relatedTasks = $moduleModel->getRecentTasks('', $pagingModel, 'all', $recordId);
	            
	            $viewer = $this->getViewer($request);
	            $viewer->assign('RECORD', $recordModel);
	            $viewer->assign('MODULE_NAME', $moduleName);
	            $viewer->assign('PAGING_MODEL', $pagingModel);
	            $viewer->assign('PAGE_NUMBER', $pageNumber);
	            $viewer->assign('TASKS', $relatedTasks);
	            
	            return $viewer->view('RelatedTasks.tpl', $moduleName, true);
	    }
	}

}

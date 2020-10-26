<?php
/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ***********************************************************************************/

class Vtiger_RelatedRecordQuickPreview_View extends Vtiger_Index_View {

	protected $record = false;

	function __construct() {
		parent::__construct();
	}

	function process(Vtiger_Request $request) {

	    
		$moduleName = $request->getModule();
		$viewer = $this->getViewer($request);
		$recordId = $request->get('record');

		if (!$this->record) {
			$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}
		if ($request->get('navigation') == 'true') {
		    $this->assignNavigationRecordIds($viewer,$request);
		}

		$recordModel = $this->record->getRecord();
		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_SUMMARY);
		$moduleModel = $recordModel->getModule();
		
		$quickPreviewFields = $moduleModel->getQuickPreviewFields($recordModel);

		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('MODULE_MODEL', $moduleModel);
		$viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('SUMMARY_RECORD_STRUCTURE', $recordStrucure->getStructure());
		$viewer->assign('QUICK_PREVIEW_FIELDS', $quickPreviewFields);
		$viewer->assign('$SOCIAL_ENABLED', false);
		$viewer->assign('LIST_PREVIEW', true);
		$appName = $request->get('app');
		$viewer->assign('SELECTED_MENU_CATEGORY', $appName);
		$pageNumber = 1;
		$limit = 4;

		$pagingModel = new Vtiger_Paging_Model();
		$pagingModel->set('page', $pageNumber);
		$pagingModel->set('limit', $limit);

		if ($moduleModel->isCommentEnabled()) {
			//Show Top 5
			$recentComments = ModComments_Record_Model::getRecentComments($recordId, $pagingModel);
			$viewer->assign('COMMENTS', $recentComments);
			$modCommentsModel = Vtiger_Module_Model::getInstance('ModComments');
			$viewer->assign('COMMENTS_MODULE_MODEL', $modCommentsModel);
			$currentUserModel = Users_Record_Model::getCurrentUserModel();
			$viewer->assign('CURRENTUSER', $currentUserModel);
		}

		$viewer->assign('SHOW_ENGAGEMENTS', 'false');
		$recentActivities = ModTracker_Record_Model::getJournalUpdates($recordId, $pagingModel, $moduleName, array());
		//To show more button for updates if there are more than 5 records
		if (count($recentActivities) >= 5) {
			$pagingModel->set('nextPageExists', true);
		} else {
			$pagingModel->set('nextPageExists', false);
		}
		$viewer->assign('PAGING_MODEL', $pagingModel);
		$viewer->assign('RECENT_ACTIVITIES', $recentActivities);
		$viewer->view('RelatedListViewQuickPreview.tpl', $moduleName);
	}

	public function assignNavigationRecordIds($viewer,$request) {
	    
	    $orderBy = $request->get('orderBy');
	    $sortOrder = $request->get('sortOrder');

	    $relatedModuleName = $request->getModule();
	    $recordId = $request->get('record');
	    
	    $parent_module = $request->get('parent_module');
	    $parentId = $request->get('parent_recordId');
	    
	    $pagingModel = new Vtiger_Paging_Model();
	    $pagingModel->set('page',1);
	    $pagingModel->set('view','quickpreview');
	    
	    $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentId, $parent_module);
	    $relationListView = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $relatedModuleName);
		    
	    if(!empty($orderBy)) {
	        $relationListView->set('orderby', $orderBy);
	        $relationListView->set('sortorder',$sortOrder);
	    }
	    
	    $models = $relationListView->getEntries($pagingModel);
	    $i = 0;
	    $navigationInfo = array();
	    foreach ($models as $record_id=>$record_model){
	        
	        $navigationInfo[$i] = $record_id;
	        $i++;
	    }
		$prevRecordId = null;
		$nextRecordId = null;
		$found = false;
		if ($navigationInfo) {
		    foreach ($navigationInfo as $index => $record) {
				if ($found) {
					$nextRecordId = $record;
					break;
				}
				if ($record == $recordId) {
					$found = true;
				}
				if (!$found) {
					$prevRecordId = $record;
				}
			}
		}
		$viewer->assign('PREVIOUS_RECORD_ID', $prevRecordId);
		$viewer->assign('NEXT_RECORD_ID', $nextRecordId);
		$viewer->assign('NAVIGATION', true);
	}

	public function validateRequest(Vtiger_Request $request) {
		$request->validateReadAccess();
	}

}

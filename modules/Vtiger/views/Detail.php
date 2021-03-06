<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vtiger_Detail_View extends Vtiger_Index_View {
	protected $record = false;
	protected $isAjaxEnabled = null;

	function __construct() {
		parent::__construct();
		$this->exposeMethod('showDetailViewByMode');
		$this->exposeMethod('showModuleDetailView');
		$this->exposeMethod('showModuleSummaryView');
		$this->exposeMethod('showModuleBasicView');
		$this->exposeMethod('showRecentActivities');
		$this->exposeMethod('showRecentComments');
		$this->exposeMethod('showRelatedList');
		$this->exposeMethod('showChildComments');
		$this->exposeMethod('getActivities');
		$this->exposeMethod('showRelatedRecords');
		$this->exposeMethod('treeViewForDocs');
		$this->exposeMethod('folderViewForDocs');
	}

	function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$recordId = $request->get('record');

		$recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'DetailView', $recordId);
		if(!$recordPermission) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}

		if ($recordId) {
			$recordEntityName = getSalesEntityType($recordId);
			if ($recordEntityName !== $moduleName) {
				throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
			}
		}
		return true;
	}

	function preProcess(Vtiger_Request $request, $display=true) {
		parent::preProcess($request, false);

		$recordId = $request->get('record');
		$moduleName = $request->getModule();
		if(!$this->record){
			$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}
		$recordModel = $this->record->getRecord();
		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
		$summaryInfo = array();
		// Take first block information as summary information
		$stucturedValues = $recordStrucure->getStructure();
		foreach($stucturedValues as $blockLabel=>$fieldList) {
			$summaryInfo[$blockLabel] = $fieldList;
			break;
		}

		$detailViewLinkParams = array('MODULE'=>$moduleName,'RECORD'=>$recordId);

		$detailViewLinks = $this->record->getDetailViewLinks($detailViewLinkParams);
		$navigationInfo = ListViewSession::getListViewNavigation($recordId);

		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('NAVIGATION', $navigationInfo);

		//Intially make the prev and next records as null
		$prevRecordId = null;
		$nextRecordId = null;
		$found = false;
		if ($navigationInfo) {
			foreach($navigationInfo as $page=>$pageInfo) {
				foreach($pageInfo as $index=>$record) {
					//If record found then next record in the interation
					//will be next record
					if($found) {
						$nextRecordId = $record;
						break;
					}
					if($record == $recordId) {
						$found = true;
					}
					//If record not found then we are assiging previousRecordId
					//assuming next record will get matched
					if(!$found) {
						$prevRecordId = $record;
					}
				}
				//if record is found and next record is not calculated we need to perform iteration
				if($found && !empty($nextRecordId)) {
					break;
				}
			}
		}

		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		if(!empty($prevRecordId)) {
			$viewer->assign('PREVIOUS_RECORD_URL', $moduleModel->getDetailViewUrl($prevRecordId));
		}
		if(!empty($nextRecordId)) {
			$viewer->assign('NEXT_RECORD_URL', $moduleModel->getDetailViewUrl($nextRecordId));
		}

		$viewer->assign('MODULE_MODEL', $this->record->getModule());
		$viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);

		$viewer->assign('IS_EDITABLE', $this->record->getRecord()->isEditable($moduleName));
		$viewer->assign('IS_DELETABLE', $this->record->getRecord()->isDeletable($moduleName));

		$linkParams = array('MODULE'=>$moduleName, 'ACTION'=>$request->get('view'));
		$linkModels = $this->record->getSideBarLinks($linkParams);
		$viewer->assign('QUICK_LINKS', $linkModels);
		$viewer->assign('MODULE_NAME', $moduleName);

		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$viewer->assign('DEFAULT_RECORD_VIEW', $currentUserModel->get('default_record_view'));

		$picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
		$viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', Vtiger_Functions::jsonEncode($picklistDependencyDatasource));

		$tagsList = Vtiger_Tag_Model::getAllAccessible($currentUserModel->getId(), $moduleName, $recordId);
		$allUserTags = Vtiger_Tag_Model::getAllUserTags($currentUserModel->getId());
		$viewer->assign('TAGS_LIST', $tagsList);
		$viewer->assign('ALL_USER_TAGS', $allUserTags);

		$appName = $request->get('app');
		if(!empty($appName)){
			$viewer->assign('SELECTED_MENU_CATEGORY',$appName);
		}

		$selectedTabLabel = $request->get('tab_label');
		$relationId = $request->get('relationId');

		if(empty($selectedTabLabel)) {
			if($currentUserModel->get('default_record_view') === 'Detail') {
				$selectedTabLabel = vtranslate('SINGLE_'.$moduleName, $moduleName).' '. vtranslate('LBL_DETAILS', $moduleName);
			} else{
				if($moduleModel->isSummaryViewSupported()) {
					$selectedTabLabel = vtranslate('SINGLE_'.$moduleName, $moduleName).' '. vtranslate('LBL_SUMMARY', $moduleName);
				} else {
					$selectedTabLabel = vtranslate('SINGLE_'.$moduleName, $moduleName).' '. vtranslate('LBL_DETAILS', $moduleName);
				}
			}
		}

		$viewer->assign('SELECTED_TAB_LABEL', $selectedTabLabel);
		$viewer->assign('SELECTED_RELATION_ID',$relationId);

		//Vtiger7 - TO show custom view name in Module Header
		$viewer->assign('CUSTOM_VIEWS', CustomView_Record_Model::getAllByGroup($moduleName));
		
		global $adb;
		$sql = "SELECT * FROM vte_progressbar_settings WHERE module='" . $moduleName . "' AND active = 1";
		$results = $adb->pquery($sql, array());
		$status_array = array();
		$field_value = "";
		$field_name = "";
		
		if (0 < $adb->getRowCount($results)) {
		    // 		    $current_record_model = Vtiger_Record_Model::getInstanceById($recordId);
		    $field_name = $adb->query_result($results, 0, "field_name");
		    $module_model = Vtiger_Module_Model::getInstance($moduleName);
		    $field_model = Vtiger_Field_Model::getInstance($field_name, $module_model);
		    $field_label = $field_model->get("label");
		    $field_value = $recordModel->getDisplayValue($field_name, $record_id);
		    $sql = "SELECT * FROM vtiger_" . $field_name . "  ORDER BY sortorderid";
		    $results = $adb->pquery($sql, array());
		    $status_array = array();
		    while ($row = $adb->fetchByAssoc($results)) {
		        $status_array[] = $row[$field_name];
		    }
		    if (0 < count($status_array)) {
		        $viewer->assign("CURRENT_STATUS", $field_value);
		        $viewer->assign("FIELD_NAME", $field_name);
		        $viewer->assign("FIELD_LABEL", $field_label);
		        $viewer->assign("PROGRESSBARS", $status_array);
		        $viewer->assign('MODULE_NAME', $moduleName);
		        $viewer->assign("IS_PROGRESS", true);
		    }
		}

		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
		
		
		
		$instance_permissions = $adb->pquery("select * from vtiger_instance_permissions");
		$portfolio_reports = 0;
		if($adb->num_rows($instance_permissions)){
			$portfolio_reports = $adb->query_result($instance_permissions, 0, "portfolio_reports");
		}
		$viewer->assign("PORTFOLIO_REPORTS", $portfolio_reports);
		
		
		
		$this->getTimecontrolWidgetData($request);
		if($display) {
			$this->preProcessDisplay($request);
		}
	}

	function preProcessTplName(Vtiger_Request $request) {
		return 'DetailViewPreProcess.tpl';
	}

	function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}

		$currentUserModel = Users_Record_Model::getCurrentUserModel();

		if ($currentUserModel->get('default_record_view') === 'Summary') {
			echo $this->showModuleBasicView($request);
		} else {
			echo $this->showModuleDetailView($request);
		}
	}

	public function postProcess(Vtiger_Request $request) {
		$recordId = $request->get('record');
		$moduleName = $request->getModule();
		if($moduleName=="Calendar"){
			$recordModel = Vtiger_Record_Model::getInstanceById($recordId);
			$activityType = $recordModel->getType();
			if($activityType=="Events"){
				$moduleName="Events";
			}
		}
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		if(!$this->record){
			$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}
		$detailViewLinkParams = array('MODULE'=>$moduleName,'RECORD'=>$recordId);
		$detailViewLinks = $this->record->getDetailViewLinks($detailViewLinkParams);

		$selectedTabLabel = $request->get('tab_label');
		$relationId = $request->get('relationId');

		if(empty($selectedTabLabel)) {
			if($currentUserModel->get('default_record_view') === 'Detail') {
				$selectedTabLabel = vtranslate('SINGLE_'.$moduleName, $moduleName).' '. vtranslate('LBL_DETAILS', $moduleName);
			} else{
				if($moduleModel->isSummaryViewSupported()) {
					$selectedTabLabel = vtranslate('SINGLE_'.$moduleName, $moduleName).' '. vtranslate('LBL_SUMMARY', $moduleName);
				} else {
					$selectedTabLabel = vtranslate('SINGLE_'.$moduleName, $moduleName).' '. vtranslate('LBL_DETAILS', $moduleName);
				}
			}
		}

		$viewer = $this->getViewer($request);

		$viewer->assign('SELECTED_TAB_LABEL', $selectedTabLabel);
		$viewer->assign('SELECTED_RELATION_ID',$relationId);
		$viewer->assign('MODULE_MODEL', $this->record->getModule());
		$viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);

		$viewer->view('DetailViewPostProcess.tpl', $moduleName);

		parent::postProcess($request);
	}


	public function getHeaderScripts(Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();

		$jsFileNames = array(
			'modules.Vtiger.resources.Detail',
			"modules.$moduleName.resources.Detail",
			'modules.Vtiger.resources.RelatedList',
			"modules.$moduleName.resources.RelatedList",
			'libraries.jquery.jquery_windowmsg',
			"libraries.jquery.ckeditor.ckeditor",
			"libraries.jquery.ckeditor.adapters.jquery",
			"modules.Emails.resources.MassEdit",
			"modules.Vtiger.resources.CkEditor",
			"~/libraries/jquery/twitter-text-js/twitter-text.js",
			"libraries.jquery.multiplefileupload.jquery_MultiFile",
			'~/libraries/jquery/bootstrapswitch/js/bootstrap-switch.min.js',
			'~/libraries/jquery.bxslider/jquery.bxslider.min.js',
			"~layouts/v7/lib/jquery/Lightweight-jQuery-In-page-Filtering-Plugin-instaFilta/instafilta.js",
			'modules.Vtiger.resources.Tag',
			'modules.Google.resources.Map',
		    '~libraries/tree/jstree.min.js',
		    'modules.Vtiger.resources.Journal',
		    "modules.Vtiger.resources.FolderViewRelated",
		    "~layouts/".Vtiger_Viewer::getDefaultLayoutName()."/lib/jquery/contextMenu/jquery.contextMenu.min.js",
            "~layouts/".Vtiger_Viewer::getDefaultLayoutName()."/lib/jquery/perfect-scrollbar/js/perfect-scrollbar.jquery.js"
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

	function showDetailViewByMode($request) {
		$requestMode = $request->get('requestMode');
		if($requestMode == 'full') {
			return $this->showModuleDetailView($request);
		}
		return $this->showModuleBasicView($request);
	}

	/**
	 * Function shows the entire detail for the record
	 * @param Vtiger_Request $request
	 * @return <type>
	 */
	function showModuleDetailView(Vtiger_Request $request) {
		$recordId = $request->get('record');
		$moduleName = $request->getModule();

		if(!$this->record){
		$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}
		$recordModel = $this->record->getRecord();
		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
		$structuredValues = $recordStrucure->getStructure();

		$moduleModel = $recordModel->getModule();

		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('RECORD_STRUCTURE', $structuredValues);
		$viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
		$viewer->assign('MODULE', $moduleName);

		$picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
		$viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', Vtiger_Functions::jsonEncode($picklistDependencyDatasource));

		global $adb;
		
		$tab_view = $adb->pquery("SELECT * FROM vtiger_module_tab_view WHERE module_name = ?",
		    array($moduleName));
	   
		$tabView = '';
		
		if($adb->num_rows($tab_view)){
		    $tabView = $adb->query_result($tab_view,0,'is_tab');
		}
		
		$viewer->assign('TABVIEW', $tabView);
		
		if($tabView){
		    $combineTab = $adb->pquery("SELECT * FROM vtiger_module_tab
            INNER JOIN vtiger_module_tab_blocks ON vtiger_module_tab_blocks.tabid = vtiger_module_tab.id
            WHERE module_name = ? ORDER BY vtiger_module_tab.sequence,vtiger_module_tab_blocks.blocksequence ASC",
		        array($moduleName));
		    $combineTabs = array();
		    if($adb->num_rows($combineTab)){
		        for($c=0;$c<$adb->num_rows($combineTab);$c++){
		            $combineTabs[$adb->query_result($combineTab,$c,'tab_name')][] = $adb->query_result($combineTab,$c,'blockid');
		            $combineTabIds[] = $adb->query_result($combineTab,$c,'blockid');
		            $noOfColumns[$adb->query_result($combineTab,$c,'blockid')] = $adb->query_result($combineTab,$c,'columns');
		        }
		    }
		   
		    $viewer->assign('NUMOFCOLUMNS', $noOfColumns);
		    $viewer->assign('COMBINETABIDS', $combineTabIds);
		    $viewer->assign('COMBINETABS', $combineTabs);
		}
		
		if ($request->get('displayMode') == 'overlay') {
			$viewer->assign('MODULE_MODEL', $moduleModel);
			$this->setModuleInfo($request, $moduleModel);
			$viewer->assign('SCRIPTS',$this->getOverlayHeaderScripts($request));

			$detailViewLinkParams = array('MODULE'=>$moduleName, 'RECORD'=>$recordId);
			$detailViewLinks = $this->record->getDetailViewLinks($detailViewLinkParams);
			$viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);
			return $viewer->view('OverlayDetailView.tpl', $moduleName);
		} else {
			return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
		}
	}
	public function getOverlayHeaderScripts(Vtiger_Request $request){
		$moduleName = $request->getModule();
		$jsFileNames = array(
			"modules.$moduleName.resources.Detail",
		);
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		return $jsScriptInstances;	
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

		$viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$pagingModel = new Vtiger_Paging_Model();
		$viewer->assign('PAGING_MODEL', $pagingModel);

		$picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
		$viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', Vtiger_Functions::jsonEncode($picklistDependencyDatasource));

		return $viewer->view('ModuleSummaryView.tpl', $moduleName, true);
	}

	/**
	 * Function shows basic detail for the record
	 * @param <type> $request
	 */
	function showModuleBasicView($request) {

		$recordId = $request->get('record');
		$moduleName = $request->getModule();

		if(!$this->record){
			$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}
		$recordModel = $this->record->getRecord();

		$detailViewLinkParams = array('MODULE'=>$moduleName,'RECORD'=>$recordId);
		$detailViewLinks = $this->record->getDetailViewLinks($detailViewLinkParams);

		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('MODULE_SUMMARY', $this->showModuleSummaryView($request));

		$viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
		$viewer->assign('MODULE_NAME', $moduleName);

		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
		$structuredValues = $recordStrucure->getStructure();

		$moduleModel = $recordModel->getModule();
		$viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('RECORD_STRUCTURE', $structuredValues);
		$viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
		global $adb;
		
		$tab_view = $adb->pquery("SELECT * FROM vtiger_module_tab_view WHERE module_name = ?",
		    array($moduleName));
		
		$tabView = '';
		
		if($adb->num_rows($tab_view)){
		    $tabView = $adb->query_result($tab_view,0,'is_tab');
		}
		
		$viewer->assign('TABVIEW', $tabView);
		if($tabView){
    		$combineTab = $adb->pquery("SELECT * FROM vtiger_module_tab
            INNER JOIN vtiger_module_tab_blocks ON vtiger_module_tab_blocks.tabid = vtiger_module_tab.id
            WHERE module_name = ? ORDER BY vtiger_module_tab.sequence ASC",
    		    array($moduleName));
    		$combineTabs = array();
    		if($adb->num_rows($combineTab)){
    		    for($c=0;$c<$adb->num_rows($combineTab);$c++){
    		        $combineTabs[$adb->query_result($combineTab,$c,'tab_name')][] = $adb->query_result($combineTab,$c,'block_id');
    		        $combineTabIds[] = $adb->query_result($combineTab,$c,'blockid');
    		        $noOfColumns[$adb->query_result($combineTab,$c,'blockid')] = $adb->query_result($combineTab,$c,'columns');
    		    }
    		}
    		
    		$viewer->assign('NUMOFCOLUMNS', $noOfColumns);
    		$viewer->assign('COMBINETABIDS', $combineTabIds);
    		$viewer->assign('COMBINETABS', $combineTabs);
		}
		echo $viewer->view('DetailViewSummaryContents.tpl', $moduleName, true);
	}

	/**
	 * Added to support Engagements view in Vtiger7
	 * @param Vtiger_Request $request
	 */
	function _showRecentActivities(Vtiger_Request $request){
		$parentRecordId = $request->get('record');
		$pageNumber = $request->get('page');
		$limit = $request->get('limit');
		$moduleName = $request->getModule();

		if(empty($pageNumber)) {
			$pageNumber = 1;
		}

		$pagingModel = new Vtiger_Paging_Model();
		$pagingModel->set('page', $pageNumber);
		if(!empty($limit)) {
			$pagingModel->set('limit', $limit);
		}

		$recentActivities = ModTracker_Record_Model::getUpdates($parentRecordId, $pagingModel,$moduleName);
		$pagingModel->calculatePageRange($recentActivities);

		if($pagingModel->getCurrentPage() == ModTracker_Record_Model::getTotalRecordCount($parentRecordId)/$pagingModel->getPageLimit()) {
			$pagingModel->set('nextPageExists', false);
		}
		$recordModel = Vtiger_Record_Model::getInstanceById($parentRecordId);
		$viewer = $this->getViewer($request);
		$viewer->assign('SOURCE',$recordModel->get('source'));
		$viewer->assign('RECENT_ACTIVITIES', $recentActivities);
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('PAGING_MODEL', $pagingModel);
		$viewer->assign('RECORD_ID',$parentRecordId);
	}

	/**
	 * Function returns recent changes made on the record
	 * @param Vtiger_Request $request
	 */
	function showRecentActivities (Vtiger_Request $request){
		$moduleName = $request->getModule();
		$this->_showRecentActivities($request);

		$viewer = $this->getViewer($request);
		echo $viewer->view('RecentActivities.tpl', $moduleName, true);
	}

	/**
	 * Function returns latest comments
	 * @param Vtiger_Request $request
	 * @return <type>
	 */
	function showRecentComments(Vtiger_Request $request) {
		$parentId = $request->get('record');
		$pageNumber = $request->get('page');
		$limit = $request->get('limit');
		$moduleName = $request->getModule();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();

		if(empty($pageNumber)) {
			$pageNumber = 1;
		}

		$pagingModel = new Vtiger_Paging_Model();
		$pagingModel->set('page', $pageNumber);
		if(!empty($limit)) {
			$pagingModel->set('limit', $limit);
		}

		if($request->get('rollup-toggle')) {
			$rollupsettings = ModComments_Module_Model::storeRollupSettingsForUser($currentUserModel, $request);
		} else {
			$rollupsettings = ModComments_Module_Model::getRollupSettingsForUser($currentUserModel, $moduleName);
		}
		
		if($moduleName =='Contacts' || $moduleName == 'Accounts'){
		    $rollupsettings['rollup_status'] = 1;
		}
		
		if($rollupsettings['rollup_status']) {
			$parentRecordModel = Vtiger_Record_Model::getInstanceById($parentId, $moduleName);
			$recentComments = $parentRecordModel->getRollupCommentsForModule(0, 6);
		}else {
			$recentComments = ModComments_Record_Model::getRecentComments($parentId, $pagingModel);
		}

		$pagingModel->calculatePageRange($recentComments);
		if ($pagingModel->get('limit') < count($recentComments)) {
			array_pop($recentComments);
		}

		$modCommentsModel = Vtiger_Module_Model::getInstance('ModComments');
		$fileNameFieldModel = Vtiger_Field::getInstance("filename", $modCommentsModel);
		$fileFieldModel = Vtiger_Field_Model::getInstanceFromFieldObject($fileNameFieldModel);

		$viewer = $this->getViewer($request);
		$viewer->assign('COMMENTS', $recentComments);
		$viewer->assign('CURRENTUSER', $currentUserModel);
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('PAGING_MODEL', $pagingModel);
		$viewer->assign('FIELD_MODEL', $fileFieldModel);
		$viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
		$viewer->assign('MAX_UPLOAD_LIMIT_BYTES', Vtiger_Util_Helper::getMaxUploadSizeInBytes());
		$viewer->assign('COMMENTS_MODULE_MODEL', $modCommentsModel);
		$viewer->assign('ROLLUP_STATUS', $rollupsettings['rollup_status']);
		$viewer->assign('ROLLUPID', $rollupsettings['rollupid']);
		$viewer->assign('PARENT_RECORD', $parentId);

		return $viewer->view('RecentComments.tpl', $moduleName, 'true');
	}

	/**
	 * Function returns related records
	 * @param Vtiger_Request $request
	 * @return <type>
	 */
	function showRelatedList(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$relatedModuleName = $request->get('relatedModule');
		$targetControllerClass = null;

		if($relatedModuleName == 'ModComments') {
			$currentUserModel = Users_Record_Model::getCurrentUserModel();
			$rollupSettings = ModComments_Module_Model::getRollupSettingsForUser($currentUserModel, $moduleName);
			$request->set('rollup_settings', $rollupSettings);
		}

		// Added to support related list view from the related module, rather than the base module.
		try {
			$targetControllerClass = Vtiger_Loader::getComponentClassName('View', 'In'.$moduleName.'Relation', $relatedModuleName);
		}catch(AppException $e) {
			try {
				// If any module wants to have same view for all the relation, then invoke this.
				$targetControllerClass = Vtiger_Loader::getComponentClassName('View', 'InRelation', $relatedModuleName);
			}catch(AppException $e) {
				// Default related list
				$targetControllerClass = Vtiger_Loader::getComponentClassName('View', 'RelatedList', $moduleName);
			}
		}
		if($targetControllerClass) {
			$targetController = new $targetControllerClass();
			return $targetController->process($request);
		}
	}

	/**
	 * Function sends the child comments for a comment
	 * @param Vtiger_Request $request
	 * @return <type>
	 */
	function showChildComments(Vtiger_Request $request) {
		$parentCommentId = $request->get('commentid');
		$parentCommentModel = ModComments_Record_Model::getInstanceById($parentCommentId);
		$childComments = $parentCommentModel->getChildComments();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$modCommentsModel = Vtiger_Module_Model::getInstance('ModComments');

		$viewer = $this->getViewer($request);
		$viewer->assign('PARENT_COMMENTS', $childComments);
		$viewer->assign('CURRENTUSER', $currentUserModel);
		$viewer->assign('COMMENTS_MODULE_MODEL', $modCommentsModel);

		return $viewer->view('CommentsList.tpl', $moduleName, 'true');
	}

	/**
	 * Function to get Ajax is enabled or not
	 * @param Vtiger_Record_Model record model
	 * @return <boolean> true/false
	 */
	function isAjaxEnabled($recordModel) {
		if(is_null($this->isAjaxEnabled)){
			$this->isAjaxEnabled = $recordModel->isEditable();
		}
		return $this->isAjaxEnabled;
	}

	/**
	 * Function to get activities
	 * @param Vtiger_Request $request
	 * @return <List of activity models>
	 */
	public function getActivities(Vtiger_Request $request) {
		return '';
	}


	/**
	 * Function returns related records based on related moduleName
	 * @param Vtiger_Request $request
	 * @return <type>
	 */
	function showRelatedRecords(Vtiger_Request $request) {
		$parentId = $request->get('record');
		$pageNumber = $request->get('page');
		$limit = $request->get('limit');
		$relatedModuleName = $request->get('relatedModule');
		$moduleName = $request->getModule();

		if(empty($pageNumber)) {
			$pageNumber = 1;
		}

		$pagingModel = new Vtiger_Paging_Model();
		$pagingModel->set('page', $pageNumber);
		if(!empty($limit)) {
			$pagingModel->set('limit', $limit);
		}

		$parentRecordModel = Vtiger_Record_Model::getInstanceById($parentId, $moduleName);
		$relationListView = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $relatedModuleName);
		$models = $relationListView->getEntries($pagingModel);
		$header = $relationListView->getHeaders();

		$viewer = $this->getViewer($request);
		$viewer->assign('MODULE' , $moduleName);
		$viewer->assign('RELATED_RECORDS' , $models);
		$viewer->assign('RELATED_HEADERS', $header);
		$viewer->assign('RELATED_MODULE' , $relatedModuleName);
		$viewer->assign('PAGING_MODEL', $pagingModel);

		return $viewer->view('SummaryWidgets.tpl', $moduleName, 'true');
	}

	public function getHeaderCss(Vtiger_Request $request) {
		$headerCssInstances = parent::getHeaderCss($request);
		$cssFileNames = array(
			'~/libraries/jquery/bootstrapswitch/css/bootstrap2/bootstrap-switch.min.css',
		    '~libraries/tree/style.min.css',
		    "~layouts/".Vtiger_Viewer::getDefaultLayoutName()."/lib/jquery/contextMenu/jquery.contextMenu.min.css",
		    "~layouts/".Vtiger_Viewer::getDefaultLayoutName()."/lib/jquery/perfect-scrollbar/css/perfect-scrollbar.css",
		);
		$cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
		$headerCssInstances = array_merge($headerCssInstances, $cssInstances);
		return $headerCssInstances;
	}
	
	
	function treeViewForDocs(Vtiger_Request $request) {
	    
	    $moduleName = $request->getModule();
	    
	    $viewer = $this->getViewer($request);
	    
	    $parentId = $request->get('record');
	    
	    $relatedModuleName = 'Documents';
	    
	    $relatedmoduleModel = Vtiger_Module_Model::getInstance($relatedModuleName);
	    
	    global $adb, $current_user;
	    
	    $entity_id = VtigerWebserviceObject::fromName($adb,$relatedModuleName);
	    
	    $doc_entity_id = $entity_id->getEntityId();
	    
	    $viewer->assign('DOC_ENTITY_ID',$doc_entity_id);
	    
	    $viewer->assign('MODULE',$moduleName);
	    
	    $viewer->assign('PARENT_ID',$parentId);
	    
	    $viewer->assign('RELATED_MODULE',$relatedmoduleModel);
	    
	    $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentId, $moduleName);
	    
	    $relationListView = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $relatedModuleName, '');
	    
	    $links = $relationListView->getLinks();
	    
	    $relatedlink = $adb->pquery("SELECT relation_id FROM vtiger_relatedlists WHERE tabid = ? AND related_tabid = ?",
	        array(getTabid($moduleName),getTabid($relatedModuleName)));
	    
	    $linkId = '';
	    
	    if($adb->num_rows($relatedlink)){
	        $linkId = $adb->query_result($relatedlink,0,'relation_id');
	    }
	    
	    $relationModel = $relationListView->getRelationModel();
	    
	    $relationField = $relationModel->getRelationField();
	    
	    $viewer->assign('IS_CREATE_PERMITTED', $relatedmoduleModel->isPermitted('CreateView'));
	    
	    $viewer->assign('RELATED_LIST_LINKS', $links);
	    
	    $viewer->assign('RELATION_FIELD', $relationField);
	    
	    $viewer->assign('LINKID', $linkId);
	   
	    $viewer->assign('USER_MODEL', $current_user);
	    
	    return $viewer->view('DocumentsRelatedListForTree.tpl', $relatedModuleName, 'true');
	    
	}
	
	function folderViewForDocs(Vtiger_Request $request){
	    
	    $moduleName = $request->getModule();
	    $relatedModuleName = $request->get('relatedModule');
	    $targetControllerClass = null;
	    
	    if($relatedModuleName == 'Documents')
	       $targetControllerClass = Vtiger_Loader::getComponentClassName('View', 'Folder', $moduleName);
       
        if($targetControllerClass) {
            $targetController = new $targetControllerClass();
            return $targetController->process($request);
        }
	    
	}
	
	
	public function getTimecontrolWidgetData(Vtiger_Request $request){
	    
	    $moduleName = $request->getModule();
	    $viewer = $this->getViewer($request);
	    
	    $moduleIns = Vtiger_Module_Model::getInstance('Timecontrol');
	    $fieldInstance =  Vtiger_Field_Model::getInstance('relatedto', $moduleIns);
	    $referenceList = $fieldInstance->getReferenceList();
	    
	    if(in_array($moduleName,$referenceList)){
	        
    	    global $adb, $current_user;
    	    
    	    $timeControl = $adb->pquery("SELECT * FROM vtiger_timecontrol
            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_timecontrol.timecontrolid
            WHERE vtiger_crmentity.deleted = 0 AND vtiger_timecontrol.timecontrolstatus='run'
            AND vtiger_crmentity.smcreatorid = ?
            AND vtiger_timecontrol.relatedto = ?",array($current_user->id, $request->get('record')));
    	    
    	    if($adb->num_rows($timeControl)){
    	        
    	        $timeControlId = $adb->query_result($timeControl, 0, 'timecontrolid');
    	        
    	        $recordModel = Vtiger_Record_Model::getInstanceById($timeControlId);
    	        
    	        $viewer->assign('TIMECONTROLID', $timeControlId);
    	        if ($recordModel->get('timecontrolstatus')=='run') {
    	            $date = $recordModel->get('date_start');
    	            $time = $recordModel->get('time_start');
    	            list($year, $month, $day) = explode('-', $date);
    	            list($hour, $minute) = explode(':', $time);
    	            
    	            $starttime = mktime($hour, $minute, 0, $month, $day, $year);
    	            
    	            $datetimefield = new DateTimeField('');
    	            $nowDate = $datetimefield->convertToUserTimeZone(date('Y-m-d H:i:s'));
    	            
    	            $counter = strtotime($nowDate->format('Y-m-d H:i:s'))-$starttime;
    	            $viewer->assign('SHOW_WATCH', 'started');
    	            $viewer->assign('WATCH_COUNTER', $counter);
    	        }
    	    }else {
    	        $timeControl = $adb->pquery("SELECT * FROM vtiger_timecontrol
                INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_timecontrol.timecontrolid
                WHERE vtiger_crmentity.deleted = 0 AND vtiger_timecontrol.relatedto = ?
                ORDER BY timecontrolid DESC",array($request->get('record')));
    	        
    	        if($adb->num_rows($timeControl)){
    	            $timeControlId = $adb->query_result($timeControl, 0, 'timecontrolid');
    	            $viewer->assign('TIMECONTROLID', $timeControlId);
    	        }
    	        $viewer->assign('WATCH_DISPLAY', '00:00');
    	        $viewer->assign('SHOW_WATCH', 'halted');
    	    }
    	    $viewer->assign('TIMECONTROL_ENABLE', Users_Privileges_Model::isPermitted('Timecontrol', 'EditView'));
	    }
	    
	}

}

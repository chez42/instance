<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class EmailTemplates_ListView_Model extends Vtiger_ListView_Model {

	private $querySelectColumns = array('templatename, foldername, subject', 'systemtemplate', 'module', 'description');
	private $listViewColumns = array('templatename', 'subject', 'description', 'module');

	public function addColumnToSelectClause($columName) {
		if (!is_array($columName))
			$columNameList = array($columName);
		else
			$columNameList = $columName;

		$this->querySelectColumns = array_merge($this->querySelectColumns, $columNameList);
		return $this; 
	}

	/**
	 * Function to get the list of Mass actions for the module
	 * @param <Array> $linkParams
	 * @return <Array> - Associative array of Link type to List of  Vtiger_Link_Model instances for Mass Actions
	 */
	public function getListViewMassActions($linkParams) {
		$moduleModel = $this->getModule();
		$linkTypes = array('LISTVIEWMASSACTION');
		$links = array();

		$massActionLinks[] = array(
			'linktype' => 'LISTVIEWMASSACTION',
			'linklabel' => 'LBL_DELETE',
			'linkurl' => 'javascript:EmailTemplates_List_Js.massDeleteRecords("index.php?module='.$moduleModel->get('name').'&action=MassDelete");',
			'linkicon' => ''
		);

		foreach($massActionLinks as $massActionLink) {
			$links['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
		}

		return $links;
	}

	/**
	 * Static Function to get the Instance of Vtiger ListView model for a given module and custom view
	 * @param <String> $moduleName - Module Name
	 * @param <Number> $viewId - Custom View Id
	 * @return Vtiger_ListView_Model instance
	 */
	public static function getInstance($moduleName, $viewId = 0) {
		$db = PearDatabase::getInstance();
		$currentUser = vglobal('current_user');

		$modelClassName = Vtiger_Loader::getComponentClassName('Model', 'ListView', $moduleName);
		$instance = new $modelClassName();

		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		$queryGenerator = new QueryGenerator($moduleModel->get('name'), $currentUser);
		$customView = new CustomView();

		// === START : Changes for custom view filter sorting 2016-23-11 === //

		if(!empty($viewId) && $viewId != "0")
			$cv_id = $viewId;
		else
			$cv_id = $customView->getViewId($moduleName);

		if($cv_id){
			$CVRecordModel = CustomView_Record_Model::getInstanceById($cv_id);
			$querySorting = $CVRecordModel->getSelectedSortingFields();
		}

		if (!empty($viewId) && $viewId != "0") {
			$queryGenerator->initForCustomViewById($viewId);
			//Used to set the viewid into the session which will be used to load the same filter when you refresh the page
			$viewId = $customView->getViewId($moduleName);
		} else {
			$viewId = $customView->getViewId($moduleName);
			if(!empty($viewId) && $viewId != 0) {
				$queryGenerator->initForDefaultCustomView();
				$listFields = $queryGenerator->getFields();
				$listFields[] = 'body';
				$queryGenerator->setFields($listFields);
			} else {
				$entityInstance = CRMEntity::getInstance($moduleName);
				$listFields = $entityInstance->list_fields_name;
				$listFields[] = 'id';
				$queryGenerator->setFields($listFields);
			}
		}
		if(!empty($querySorting)){

			$sortPermission = false;

			if(isset($querySorting['columnname']) && $querySorting['columnname'] != 'none'){
				$orderbyFieldname = explode(":",$querySorting['columnname']);
				$fieldModel = Vtiger_Field_Model::getInstance($orderbyFieldname[1], $moduleModel);
				if($moduleName != "Documents")
				if($fieldModel->getPermissions()){
					$instance->set('cv_orderby', $orderbyFieldname[0].".".$orderbyFieldname[1]);
					$sortPermission = true;
				}
			}

			if(isset($querySorting['sortorder']) && $querySorting['sortorder'] != '' && $sortPermission)
				$instance->set('cv_sortorder', $querySorting['sortorder']);
		}


		// === END : Changes for custom view filter sorting 2016-23-11 === //

		$controller = new ListViewController($db, $currentUser, $queryGenerator);

		return $instance->set('module', $moduleModel)->set('query_generator', $queryGenerator)->set('listview_controller', $controller);
		//return $instance->set('module', $moduleModel);
	}

	/**
	 * Function to get the list view entries
	 * @param Vtiger_Paging_Model $pagingModel
	 * @return <Array> - Associative array of record id mapped to Vtiger_Record_Model instance.
	 */

	public function getListViewEntries($pagingModel) {
		$db = PearDatabase::getInstance();
		$moduleName = $this->getModule()->get('name');
		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();
		$orderBy = $this->getForSql('orderby');
		$sortOrder = $this->getForSql('sortorder');

		$queryGenerator = $this->get('query_generator');
		$listViewContoller = $this->get('listview_controller');
		
		$searchParams = $this->get('search_params');
		if(empty($searchParams)) {
		    $searchParams = array();
		}
		$glue = "";
		if(count($queryGenerator->getWhereFields()) > 0 && (count($searchParams)) > 0) {
		    $glue = QueryGenerator::$AND;
		}
		$queryGenerator->parseAdvFilterList($searchParams, $glue);
		
		$listQuery = $this->getQuery();
		$sourceModule = $this->get('sourceModule');
		$searchKey = $this->get('search_key');
		$searchValue = $this->get('search_value');

		$whereQuery .= ' AND ';
		if(!empty($searchKey) && !empty($searchValue)) {
			$whereQuery .= " vtiger_emailtemplates.$searchKey LIKE '$searchValue%' AND ";
		}

		//module should be enabled or module should be empty then allow
		$moduleActiveCheck = '(vtiger_tab.presence IN (0,2) OR vtiger_emailtemplates.module IS null OR vtiger_emailtemplates.module = "")';
		$listQuery .= $whereQuery. $moduleActiveCheck;
		//To retrieve only selected module records
		if ($sourceModule) {
			$listQuery .= " AND vtiger_emailtemplates.module = '".$sourceModule."'";
		}

		if ($orderBy) {
			$listQuery .= " ORDER BY $orderBy $sortOrder";
		} else {
			$listQuery .= " ORDER BY templateid DESC";
		}
		$viewid = $pagingModel->get('viewid');
		if(empty($viewid)) {
			$viewid = ListViewSession::getCurrentView($moduleName);
		}
		
		$_SESSION['lvs'][$moduleName][$viewid]['start'] = $pagingModel->get('page');

		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);
		
		$listQuery .= " LIMIT $startIndex,".($pageLimit+1);
		$result = $db->pquery($listQuery, array());
		$num_rows = $db->num_rows($result);

		$listViewRecordModels = array();
		for ($i = 0; $i < $num_rows; $i++) {
			$recordModel = new EmailTemplates_Record_Model();
			$recordModel->setModule('EmailTemplates');
			$row = $db->query_result_rowdata($result, $i);
			$recordModel->setRawData($row);
			foreach ($row as $key => $value) {
				if($key=="module"){
					$value = vtranslate($value,$value);
				}
				if(in_array($key,$this->listViewColumns)){
					$value = textlength_check($value);
				}
				$row[$key] = $value;
			}
			$listViewRecordModels[$row['templateid']] = $recordModel->setData($row);
		}

		$pagingModel->calculatePageRange($listViewRecordModels);

		if($num_rows > $pageLimit){
			array_pop($listViewRecordModels);
			$pagingModel->set('nextPageExists', true);
		}else{
			$pagingModel->set('nextPageExists', false);
		}

		return $listViewRecordModels;
	}

	/**
	 * Function to get the list of listview links for the module
	 * @param <Array> $linkParams
	 * @return <Array> - Associate array of Link Type to List of Vtiger_Link_Model instances
	 */
	public function getListViewLinks($linkParams) {
		$moduleModel = $this->getModule();

		$linkTypes = array('LISTVIEWBASIC', 'LISTVIEW', 'LISTVIEWSETTING');
		$links = Vtiger_Link_Model::getAllByType($moduleModel->getId(), $linkTypes, $linkParams);

		$basicLinks = array(
				array(
						'linktype' => 'LISTVIEWBASIC',
						'linklabel' => 'LBL_ADD_RECORD',
						'linkurl' => $moduleModel->getCreateRecordUrl(),
						'linkicon' => ''
				)
		);
		foreach($basicLinks as $basicLink) {
			$links['LISTVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($basicLink);
		}

		return $links;
	}

	function getQuery() {
		
		
		$listQuery = parent::getQuery();
		
		$listQueryComponents = explode("INNER JOIN vtiger_crmentity ON vtiger_emailtemplates.templateid = vtiger_crmentity.crmid ", $listQuery);
		
		
		$listQuery = implode('LEFT JOIN vtiger_users ON vtiger_emailtemplates.creatorid = vtiger_users.id  LEFT JOIN vtiger_tab ON vtiger_tab.name = vtiger_emailtemplates.module
						AND (vtiger_tab.isentitytype=1 or vtiger_tab.name = "Users") ', $listQueryComponents);
		
		
		
		$listQuerycom = explode("WHERE vtiger_crmentity.deleted=0 AND",$listQuery);
		
		$listQuery = implode('WHERE', $listQuerycom);
		
		
		
		return $listQuery;
	}

	/**
	 * Function to get the list view entries
	 * @param Vtiger_Paging_Model $pagingModel
	 * @return <Array> - Associative array of record id mapped to Vtiger_Record_Model instance.
	 */
	public function getListViewCount() {
		$db = PearDatabase::getInstance();

		$queryGenerator = $this->get('query_generator');
		$listViewContoller = $this->get('listview_controller');
		
		$searchParams = $this->get('search_params');
		if(empty($searchParams)) {
		    $searchParams = array();
		}
		$glue = "";
		if(count($queryGenerator->getWhereFields()) > 0 && (count($searchParams)) > 0) {
		    $glue = QueryGenerator::$AND;
		}
		$queryGenerator->parseAdvFilterList($searchParams, $glue);
		
		$listQuery = $this->getQuery();

		$position = stripos($listQuery, 'from');
		if ($position) {
			$split = preg_split('/from/i', $listQuery);
			$splitCount = count($split);
			$listQuery = 'SELECT count(*) AS count ';
			for ($i=1; $i<$splitCount; $i++) {
				$listQuery = $listQuery. ' FROM ' .$split[$i];
			}
		}
		$searchKey = $this->get('search_key');
		$searchValue = $this->get('search_value');

		$whereQuery .= " AND ";
		if(!empty($searchKey) && !empty($searchValue)) {
			$whereQuery .= " vtiger_emailtemplates.$searchKey LIKE '$searchValue%' AND ";
		}

		//module should be enabled or module should be empty then allow
		$moduleActiveCheck = '(vtiger_tab.presence IN (0,2) OR vtiger_emailtemplates.module IS null OR vtiger_emailtemplates.module = "")';
		$listQuery .= $whereQuery. $moduleActiveCheck;

		$sourceModule = $this->get('sourceModule');
		if ($sourceModule) {
			$listQuery .= ' AND vtiger_emailtemplates.module= "' . $sourceModule . '" ';
		}

		$listResult = $db->pquery($listQuery, array());
		return $db->query_result($listResult, 0, 'count');
	}

} 
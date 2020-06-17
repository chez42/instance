<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class Project_Module_Model extends Vtiger_Module_Model {

	public function getSideBarLinks($linkParams) {
		$userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$linkTypes = array('SIDEBARLINK', 'SIDEBARWIDGET');
		$links = parent::getSideBarLinks($linkParams);
		$quickLinks = array();

		$projectTaskInstance = Vtiger_Module_Model::getInstance('ProjectTask');
		if($userPrivilegesModel->hasModulePermission($projectTaskInstance->getId())) {
			$quickLinks[] = array(
								'linktype' => 'SIDEBARLINK',
								'linklabel' => 'LBL_TASKS_LIST',
								'linkurl' => $this->getTasksListUrl(),
								'linkicon' => '',
							);
		}

		$projectMileStoneInstance = Vtiger_Module_Model::getInstance('ProjectMilestone');
		if($userPrivilegesModel->hasModulePermission($projectMileStoneInstance->getId())) {
			$quickLinks[] = array(
							'linktype' => 'SIDEBARLINK',
							'linklabel' => 'LBL_MILESTONES_LIST',
							'linkurl' => $this->getMilestonesListUrl(),
							'linkicon' => '',
						  );
		}

		foreach($quickLinks as $quickLink) {
			$links['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
		}

		return $links;
	}

	public function getTasksListUrl() {
		$taskModel = Vtiger_Module_Model::getInstance('ProjectTask');
		return $taskModel->getListViewUrl();
	}
	public function getMilestonesListUrl() {
		$milestoneModel = Vtiger_Module_Model::getInstance('ProjectMilestone');
		return $milestoneModel->getListViewUrl();
	}

	/*
	 * Function to get supported utility actions for a module
	 */
	function getUtilityActionsNames() {
		return array('Import', 'Export', 'DuplicatesHandling');
	}

	/**
	 * Function to get relation query for particular module with function name
	 * @param <record> $recordId
	 * @param <String> $functionName
	 * @param Vtiger_Module_Model $relatedModule
	 * @return <String>
	 */
	public function getRelationQuery($recordId, $functionName, $relatedModule, $relationId) {
		$relatedModuleName = $relatedModule->getName();
		$query = parent::getRelationQuery($recordId, $functionName, $relatedModule, $relationId);
		return $query;
	}

	/**
	 * Function to get list view query for popup window
	 * @param <String> $sourceModule Parent module
	 * @param <String> $field parent fieldname
	 * @param <Integer> $record parent id
	 * @param <String> $listQuery
	 * @return <String> Listview Query
	 */
	public function getQueryByModuleField($sourceModule, $field, $record, $listQuery) {
		if ($sourceModule === 'HelpDesk') {
			$condition = " vtiger_project.projectid NOT IN (SELECT relcrmid FROM vtiger_crmentityrel WHERE crmid = '$record' UNION SELECT crmid FROM vtiger_crmentityrel WHERE relcrmid = '$record') ";

			$pos = stripos($listQuery, 'where');
			if ($pos) {
				$split = preg_split('/where/i', $listQuery);
				$overRideQuery = $split[0].' WHERE '.$split[1].' AND '.$condition;
			} else {
				$overRideQuery = $listQuery.' WHERE '.$condition;
			}
			return $overRideQuery;
		}
	}
	
	/*17-Oct-2018*/
	public function getRecentTasks($mode, $pagingModel, $user, $recordId = false){
	    
	    $currentUser = Users_Record_Model::getCurrentUserModel();
	    
	    $db = PearDatabase::getInstance();
	    
	    if (!$user) {
	        $user = $currentUser->getId();
	    }
	    
	    $nowInUserFormat = Vtiger_Datetime_UIType::getDisplayDateValue(date('Y-m-d H:i:s'));
	    $nowInDBFormat = Vtiger_Datetime_UIType::getDBDateTimeValue($nowInUserFormat);
	    list($currentDate, $currentTime) = explode(' ', $nowInDBFormat);
	    
	    $query = "SELECT vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.setype, vtiger_task.* FROM vtiger_task
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_task.taskid
		INNER JOIN vtiger_project ON vtiger_project.projectid = vtiger_task.parent_id
		LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
	    
	    $query .= Users_Privileges_Model::getNonAdminAccessControlQuery('Task');
	    
	    $query .= " WHERE vtiger_crmentity.deleted = 0 AND vtiger_task.task_status != 'Completed' ";
	    
	    $params = array();
	    
	    if ($recordId) {
	        $query .= " AND vtiger_task.parent_id = ?";
	        array_push($params, $recordId);
	    }
	    
	    if($user != 'all' && $user != '') {
	        if($user === $currentUser->id) {
	            $query .= " AND vtiger_crmentity.smownerid = ?";
	            array_push($params, $user);
	        }
	    }
	    
	    $query .= " ORDER BY due_date LIMIT ". $pagingModel->getStartIndex() .", ". ($pagingModel->getPageLimit()+1);
	    
	    $result = $db->pquery($query, $params);
	    $numOfRows = $db->num_rows($result);
	    
	    $groupsIds = Vtiger_Util_Helper::getGroupsIdsForUsers($currentUser->getId());
	    $tasks = array();
	    for($i=0; $i<$numOfRows; $i++) {
	        $newRow = $db->query_result_rowdata($result, $i);
	        $model = Vtiger_Record_Model::getCleanInstance('Task');
	        $model->setData($newRow);
	        $model->setId($newRow['crmid']);
	        $tasks[] = $model;
	    }
	    
	    $pagingModel->calculatePageRange($tasks);
	    if($numOfRows > $pagingModel->getPageLimit()){
	        array_pop($tasks);
	        $pagingModel->set('nextPageExists', true);
	    } else {
	        $pagingModel->set('nextPageExists', false);
	    }
	    
	    return $tasks;
	}

}
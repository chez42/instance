<?php

/**
 * Users CustomView Model Class
 */
class Users_CustomView_Model extends CustomView_Record_Model {

	/**
	 * Function to get the instance of Custom View module, given custom view id
	 * @param <Integer> $cvId
	 * @return CustomView_Record_Model instance, if exists. Null otherwise
	 */
	public static function getInstanceById($cvId) {
		$db = PearDatabase::getInstance();

		$sql = 'SELECT * FROM vtiger_customview WHERE cvid = ?';
		$params = array($cvId);
		$result = $db->pquery($sql, $params);
		if($db->num_rows($result) > 0) {
			$row = $db->query_result_rowdata($result, 0);
			$customView = new self();
			return $customView->setData($row)->setModule($row['entitytype']);
		}
		return null;
	}
	
	public static function getNameBycvId($cvId) {
	    $db = PearDatabase::getInstance();
	    
	    $sql = 'SELECT * FROM vtiger_customview WHERE cvid = ?';
	    $params = array($cvId);
	    $result = $db->pquery($sql, $params);
	    if($db->num_rows($result) > 0) {
	        $row = $db->query_result_rowdata($result, 0);
	        return $row['viewname'];
	    }
	    return null;
	}
	/**
	 * Function which provides the records for the current view
	 * @param <Boolean> $skipRecords - List of the RecordIds to be skipped
	 * @return <Array> List of RecordsIds
	 */
	public function getRecordIds($skipRecords=false, $module= false) {
		$db = PearDatabase::getInstance();
		$cvId = $this->getId();
		$moduleModel = $this->getModule();
		$moduleName = $moduleModel->get('name');
		$baseTableName = $moduleModel->get('basetable');
		$baseTableId = $moduleModel->get('basetableid');

		$listViewModel = Vtiger_ListView_Model::getInstance($moduleName, $cvId);
		$queryGenerator = $listViewModel->get('query_generator');

        $searchKey = $this->get('search_key');
		$searchValue = $this->get('search_value');
		$operator = $this->get('operator');
		if(!empty($searchValue)) {
			$queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator));
		}
        
        $searchParams = $this->get('search_params');
        if(empty($searchParams)) {
            $searchParams = array();
        }
        
		$transformedSearchParams = Vtiger_Util_Helper::transferListSearchParamsToFilterCondition($searchParams, $moduleModel);
        
		$glue = "";
        if(count($queryGenerator->getWhereFields()) > 0 && (count($transformedSearchParams)) > 0) {
            $glue = QueryGenerator::$AND;
        }
        
		$queryGenerator->parseAdvFilterList($transformedSearchParams, $glue);
		
		$listQuery = $queryGenerator->getQuery();
		if($module == 'RecycleBin'){
			$listQuery = preg_replace("/vtiger_crmentity.deleted\s*=\s*0/i", 'vtiger_crmentity.deleted = 1', $listQuery);
		}

		if($module == 'Users'){
			$listQueryComponents = explode(" WHERE vtiger_users.status='Active' AND", $listQuery);
			if(strpos($listQueryComponents['1'], "vtiger_users.status = 'Inactive'") === false)
				$listQuery = implode(' WHERE vtiger_users.deleted = 0 AND ', $listQueryComponents);
			else {
				$listQuery = implode(' WHERE ', $listQueryComponents);
			}
		}
		
		if($skipRecords && !empty($skipRecords) && is_array($skipRecords) && count($skipRecords) > 0) {
			$listQuery .= ' AND '.$baseTableName.'.'.$baseTableId.' NOT IN ('. implode(',', $skipRecords) .')';
		}
		$result = $db->query($listQuery);
		$noOfRecords = $db->num_rows($result);
		$recordIds = array();
		for($i=0; $i<$noOfRecords; ++$i) {
			$recordIds[] = $db->query_result($result, $i, $baseTableId);
		}
		return $recordIds;
	}

	public static function getAllByGroup($moduleName='', $listMode = true) {
	   
	    $customViews = self::getAll($moduleName);
	    $groupedCustomViews = array();
	    $groupedCustomViews['Mine'] = array();
	    $groupedCustomViews['Shared'] = array();
	    foreach ($customViews as $index => $customView) {
	        if($customView->isMine() && ($customView->get('viewname') != 'All' && $customView->get('viewname') != 'LBL_INACTIVE_USERS' && $customView->get('viewname') != 'LBL_ACTIVE_USERS' || !$listMode)) {
	            $groupedCustomViews['Mine'][] = $customView;
	        } elseif($customView->isPublic()) {
	            $groupedCustomViews['Public'][] = $customView;
	            $groupedCustomViews['Shared'][] = $customView;
	        } elseif($customView->isPending()) {
	            $groupedCustomViews['Pending'][] = $customView;
	            $groupedCustomViews['Shared'][] = $customView;
	        } else {
	            $groupedCustomViews['Others'][] = $customView;
	            $groupedCustomViews['Shared'][] = $customView;
	        }
	    }
	    if(empty($groupedCustomViews['Shared'])) {
	        unset($groupedCustomViews['Shared']);
	    }
	    return $groupedCustomViews;
	}
}
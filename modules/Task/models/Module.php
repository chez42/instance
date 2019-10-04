<?php
class Task_Module_Model extends Vtiger_Module_Model {

	/**
	 *  Function returns the url for Calendar view
	 * @return <String>
	 */
	public function getCalendarViewUrl() {
		return 'index.php?module='.$this->get('name').'&view='.$this->getCalendarViewName();
	}
	
	/**
	 * Function returns the calendar view name
	 * @return <String>
	 */
	public function getCalendarViewName() {
		return 'Calendar';
	}
	
	public function getSideBarLinks($linkParams) {
		$linkTypes = array('SIDEBARLINK', 'SIDEBARWIDGET');
		$links = Vtiger_Link_Model::getAllByType($this->getId(), $linkTypes, $linkParams);

		$quickLinks = array(
			array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => 'LBL_CALENDAR_VIEW',
				'linkurl' => $this->getCalendarViewUrl(),
				'linkicon' => '',
			),
			array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => 'LBL_RECORDS_LIST',
				'linkurl' => $this->getListViewUrl(),
				'linkicon' => '',
			),
		);
		foreach($quickLinks as $quickLink) {
			$links['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
		}

		$quickWidgets = array();

		if ($linkParams['ACTION'] == 'Calendar') {
			
			$sidebarWidgets = $links['SIDEBARWIDGET'];
		
			$calendarNavigation = array(
				'linktype' => 'SIDEBARWIDGET',
				'linklabel' => 'Date Picker',
				'linkurl' => 'module='.$this->get('name').'&view=CalendarNavigation',
				'linkicon' => ''
			);
			
			$links['SIDEBARWIDGET'] = array(Vtiger_Link_Model::getInstanceFromValues($calendarNavigation));
			
			if(!empty($sidebarWidgets))
				$links['SIDEBARWIDGET'] = array_merge($links['SIDEBARWIDGET'],$sidebarWidgets);
		}
		
		$quickWidgets[] = array(
			'linktype' => 'SIDEBARWIDGET',
			'linklabel' => 'LBL_RECENTLY_MODIFIED',
			'linkurl' => 'module='.$this->get('name').'&view=IndexAjax&mode=showActiveRecords',
			'linkicon' => ''
		);
		
		foreach($quickWidgets as $quickWidget) {
			$links['SIDEBARWIDGET'][] = Vtiger_Link_Model::getInstanceFromValues($quickWidget);
		}

		return $links;
	}
	public function getAllTasksbyPriority($conditions = false, $pagingModel) {
	    global $current_user;
	    
	    $db = PearDatabase::getInstance();
	    
	    $viewId = $pagingModel->get('viewid');
	    
	    $queryGenerator = new QueryGenerator("Task",$current_user);
	    
	    $queryGenerator->initForCustomViewById($viewId);
	    
	    $moduleModel = Vtiger_Module_Model::getInstance("Task");
	    $quickCreateFields = $moduleModel->getQuickCreateFields();
	    $mandatoryFields = array("id","task_priority","parent_id","contact_id");
	    $fields = array_unique(array_merge($mandatoryFields,array_keys($quickCreateFields)));
	    $queryGenerator->setFields($fields);
	    //$queryGenerator->addCondition("activitytype","Task","e","AND");
	    if($conditions){
	        foreach($conditions as $condition){
	            if($condition["comparator"] === 'bw'){
	                $condition['fieldValue'] = implode(",",$condition['fieldValue']);
	            }
	            $queryGenerator->addCondition($condition['fieldName'],$condition['fieldValue'],$condition['comparator'],"AND");
	        }
	    }
	    $query = $queryGenerator->getQuery();
	    
	    $startIndex = $pagingModel->getStartIndex();
	    $pageLimit = $pagingModel->getPageLimit();
	    
	    $query .= " LIMIT $startIndex,".($pageLimit+1);
	   
	    $result = $db->pquery($query,array());
	    $noOfRows = $db->num_rows($result);
	    
	    $mandatoryReferenceFields = array("parent_id","contact_id");
	    $tasks = array();
	    for($i=0;$i<$noOfRows;$i++){
	        $newRow = $db->query_result_rowdata($result, $i);
	        $model = Vtiger_Record_Model::getCleanInstance('Task');
	        $model->setData($newRow);
	        $model->setId($newRow['taskid']);
	        $basicInfo = array();
	        foreach($quickCreateFields as $fieldName => $fieldModel){
	            if(in_array($fieldName,$mandatoryReferenceFields)){
	                continue;
	            }
	            $columnName = $fieldModel->get("column");
	            $fieldType = $fieldModel->getFieldDataType();
	            $value = $model->get($columnName);
	            switch($fieldType){
	                case "reference":	if(!empty($value)){
	                    $value = array("id"=>$value,"display_value"=>Vtiger_Functions::getCRMRecordLabel($value),"module"=>Vtiger_Functions::getCRMRecordType($value));
	                    
	                }
	                break;
	                case "date":	$value = Vtiger_Date_UIType::getDisplayDateValue($value);
	                break;
	            }
	            $basicInfo[$fieldName] = $value;
	        }
	        
	        foreach($mandatoryReferenceFields as $fieldName){
	            if($fieldName == "parent_id"){
	                $value = $model->get("crmid");
	            } else {
	                $value = $model->get("contactid");
	            }
	            if(!empty($value)){
	                $value = array("id"=>$value,"display_value"=>Vtiger_Functions::getCRMRecordLabel($value),"module"=>Vtiger_Functions::getCRMRecordType($value));
	                
	            }
	            $basicInfo[$fieldName] = $value;
	        }
	        
	        $model->set("basicInfo",  $basicInfo);
	        
	        $priority = $model->get('task_priority');
	        if($priority){
	            $tasks[$priority][$model->getId()] = $model;
	        }
	    }
	    
	    if(count($tasks[$priority]) > $pageLimit){
	        array_pop($tasks[$priority]);
	        $pagingModel->set('nextPageExists', true);
	    }else{
	        $pagingModel->set('nextPageExists', false);
	    }
	    
	    return $tasks;
	}
	
	public function isSummaryViewSupported() {
	    return false;
	}
	
}
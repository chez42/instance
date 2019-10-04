<?php

class Task_MassSave_Action extends Vtiger_MassSave_Action {

	/**
	 * Function to get the record model based on the request parameters
	 * @param Vtiger_Request $request
	 * @return Vtiger_Record_Model or Module specific Record Model instance
	 */
	function getRecordModelsFromRequest(Vtiger_Request $request) {

		$moduleName = $request->getModule();
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		$recordIds = $this->getRecordsListFromRequest($request);
		$recordModels = array();

		$fieldModelList = $moduleModel->getFields();
		foreach($recordIds as $recordId) {
			$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleModel);
			$recordModel->set('id', $recordId);
			$recordModel->set('mode', 'edit');
			
			foreach ($fieldModelList as $fieldName => $fieldModel) {
				
				if($fieldName == 'date_start' || $fieldName == 'time_start' || $fieldName == 'due_date')continue;
				
				$fieldValue = $request->get($fieldName, null);
				$fieldDataType = $fieldModel->getFieldDataType();
				if($fieldDataType == 'time'){
					$fieldValue = Vtiger_Time_UIType::getTimeValueWithSeconds($fieldValue);
				}
				if(isset($fieldValue) && $fieldValue != null) {
					if(!is_array($fieldValue)) {
						$fieldValue = trim($fieldValue);
					}
					$recordModel->set($fieldName, $fieldValue);
				} else {
                    $uiType = $fieldModel->get('uitype');
                    if($uiType == 70) {
                        $recordModel->set($fieldName, $recordModel->get($fieldName));
                    }  else {
                        $uiTypeModel = $fieldModel->getUITypeModel();
                        $recordModel->set($fieldName, $uiTypeModel->getUserRequestValue($recordModel->get($fieldName)));
                    }
				}
			}
			
			//Start Date and Time values
			if($request->get('date_start')){
				$startTime = Vtiger_Time_UIType::getTimeValueWithSeconds($request->get('time_start'));
				$startDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($request->get('date_start')." ".$startTime);
				list($startDate, $startTime) = explode(' ', $startDateTime);
			} else {
				$startDate = $recordModel->get("date_start");
				$startTime = $recordModel->get("time_start");
			}
			
			//End Date and Time values
			if($request->get('due_date')){
				$endDate = Vtiger_Date_UIType::getDBInsertedValue($request->get('due_date'));
			} else {
				$endDate = $recordModel->get("due_date");
			}
		
			if($endDate >= $startDate){
			
				$recordModel->set('date_start', $startDate);
				$recordModel->set('time_start', $startTime);
				$recordModel->set('due_date', $endDate);

				$time_start = new DateTimeField($recordModel->get("date_start")." ".$recordModel->get("time_start"));
				$_REQUEST['time_start'] = Vtiger_Time_UIType::getTimeValueWithSeconds($time_start->getDisplayTime());
					
			}
			
			$task_exchange_itemid = $recordModel->get('task_exchange_item_id');
			
			$task_exchange_change_key = $recordModel->get('task_exchange_change_key');
			
			// if task item_id and change key exists then update task info in Exchange.
           	if($task_exchange_itemid && $task_exchange_change_key)
            	$recordModel->set("update_exchange", 1);
			else
				$recordModel->set("update_exchange", 0);
	
			$recordModels[$recordId] = $recordModel;
		}
		return $recordModels;
	}
}

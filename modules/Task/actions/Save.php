<?php

class Task_Save_Action extends Vtiger_Save_Action {

	public function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$record = $request->get('record');

		$currentUserModel = Users_Record_Model::getCurrentUserModel();
					
		if(!Users_Privileges_Model::isPermitted($moduleName, 'Save', $record)) {
			if(Vtiger_Util_Helper::getCreator($record) == $currentUserModel->getId())
				return true;
			throw new AppException('LBL_PERMISSION_DENIED');
		}
	}

	/**
	 * Function to get the record model based on the request parameters
	 * @param Vtiger_Request $request
	 * @return Vtiger_Record_Model or Module specific Record Model instance
	 */
	protected function getRecordModelFromRequest(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$recordId = $request->get('record');

		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);

		if(!empty($recordId)) {
			$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
			$modelData = $recordModel->getData();
			$recordModel->set('id', $recordId);
			$recordModel->set('mode', 'edit');
            //Due to dependencies on the activity_reminder api in Activity.php(5.x)
            $_REQUEST['mode'] = 'edit';
		} else {
			$recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
			$modelData = $recordModel->getData();
			$recordModel->set('mode', '');
		}

		$fieldModelList = $moduleModel->getFields();
		foreach ($fieldModelList as $fieldName => $fieldModel) {
			$fieldValue = $request->get($fieldName, null);
            // For custom time fields in Calendar, it was not converting to db insert format(sending as 10:00 AM/PM)
            $fieldDataType = $fieldModel->getFieldDataType();
            if($fieldDataType == 'time'){
				$fieldValue = Vtiger_Time_UIType::getTimeValueWithSeconds($fieldValue);
            }
            // End
			if($fieldValue !== null) {
				if(!is_array($fieldValue)) {
					$fieldValue = trim($fieldValue);
				}
				$recordModel->set($fieldName, $fieldValue);
			}
		}

		//Start Date and Time values
		$startTime = Vtiger_Time_UIType::getTimeValueWithSeconds($request->get('time_start'));
		$startDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($request->get('date_start')." ".$startTime);
		list($startDate, $startTime) = explode(' ', $startDateTime);

		$recordModel->set('date_start', $startDate);
		$recordModel->set('time_start', $startTime);

		//End Date and Time values
		$endTime = $request->get('time_end');
		$endDate = Vtiger_Date_UIType::getDBInsertedValue($request->get('due_date'));

		if ($endTime) {
			$endTime = Vtiger_Time_UIType::getTimeValueWithSeconds($endTime);
			$endDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($request->get('due_date')." ".$endTime);
			list($endDate, $endTime) = explode(' ', $endDateTime);
		}

		//$recordModel->set('time_end', $endTime);
		$recordModel->set('due_date', $endDate);

		/*
		if($recordId){
			
			$recordModel->set("update_exchange", 1);
			
			$task_exchange_itemid = $recordModel->get('task_exchange_item_id');
			
			$task_exchange_change_key = $recordModel->get('task_exchange_change_key');
			
			// if task item_id and change key exists then update task info in Exchange.
           	if($task_exchange_itemid && $task_exchange_change_key)
            	$recordModel->set("update_exchange", 1);
			else
				$recordModel->set("update_exchange", 0);
			
		} else {
			$recordModel->set("update_exchange", 1);
		}
		*/
		
		$recordModel->set("update_exchange", 1);
		
		return $recordModel;
	}
}

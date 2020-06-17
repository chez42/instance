<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Task_SaveAjaxBackup_Action extends Vtiger_SaveAjax_Action {

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
	
	public function process(Vtiger_Request $request) {
        
		$user = Users_Record_Model::getCurrentUserModel();

        $recordId = $request->get('record');
        
		$recordModel = $this->saveRecord($request);

		$fieldModelList = $recordModel->getModule()->getFields();
		
		$result = array();
		
		foreach ($fieldModelList as $fieldName => $fieldModel) {
			
			$fieldValue =  Vtiger_Util_Helper::toSafeHTML($recordModel->get($fieldName));
            
			$result[$fieldName] = array();
			
			if($fieldName == 'date_start') {
				
				$timeStart = $recordModel->get('time_start');
					
				if($recordId){
					
					if($user->get('date_format') == 'mm-dd-yyyy')
						$createdFormat = 'm-d-Y';
					else if($user->get('date_format') == 'dd-mm-yyyy')
						$createdFormat = 'd-m-Y';
					else
						$createdFormat = 'Y-m-d';
						
					if(preg_match('#^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$#', $timeStart))
						$timeFormat = "H:i:s";
					else
						$timeFormat = "h:i:s";
						
					$startDateTime = DateTime::createFromFormat($createdFormat." ".$timeFormat, $fieldValue . ' ' . $timeStart); 
	                
					$dateTimeFieldInstance = new DateTimeField($startDateTime->format("Y-m-d ".$timeFormat));
	
					$fieldValue = $startDateTime->format("Y-m-d ".$timeFormat);
				} else {
					
					$dateTimeFieldInstance = new DateTimeField($fieldValue . ' ' . $timeStart);

					$fieldValue = $fieldValue.' '.$timeStart;
				}
                $userDateTimeString = $dateTimeFieldInstance->getDisplayDateTimeValue();
                
                $dateTimeComponents = explode(' ',$userDateTimeString);
                $dateComponent = $dateTimeComponents[0];
                //Conveting the date format in to Y-m-d . since full calendar expects in the same format
                $dataBaseDateFormatedString = DateTimeField::__convertToDBFormat($dateComponent, $user->get('date_format'));
                $result[$fieldName]['calendar_display_value'] = $dataBaseDateFormatedString.' '. $dateTimeComponents[1];
			} else if($fieldName == 'due_date') {
				
				$timeEnd = $recordModel->get('time_end');
                
				if($recordId){
					
					if($user->get('date_format') == 'mm-dd-yyyy')
						$createdFormat = 'm-d-Y';
					else if($user->get('date_format') == 'dd-mm-yyyy')
						$createdFormat = 'd-m-Y';
					else
						$createdFormat = 'Y-m-d';

					if(!$timeEnd)
						$timeEnd = $recordModel->get('time_start');
						
					if(preg_match('#^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$#', $timeEnd))
						$timeFormat = "H:i:s";
					else
						$timeFormat = "h:i:s";
					
					$endDateTime = DateTime::createFromFormat($createdFormat." ".$timeFormat, $fieldValue . ' ' . $timeEnd); 
	                
					$dateTimeFieldInstance = new DateTimeField($endDateTime->format("Y-m-d ".$timeFormat));
			
					$fieldValue = $endDateTime->format("Y-m-d ".$timeFormat);
				
				} else {
					
					$dateTimeFieldInstance = new DateTimeField($fieldValue . ' ' . $timeEnd);

					$fieldValue = $fieldValue.' '.$timeEnd;
				}
                
				$userDateTimeString = $dateTimeFieldInstance->getDisplayDateTimeValue();
                $dateTimeComponents = explode(' ',$userDateTimeString);
                $dateComponent = $dateTimeComponents[0];
                //Conveting the date format in to Y-m-d . since full calendar expects in the same format
                $dataBaseDateFormatedString = DateTimeField::__convertToDBFormat($dateComponent, $user->get('date_format'));
                $result[$fieldName]['calendar_display_value']   =  $dataBaseDateFormatedString.' '. $dateTimeComponents[1];
			}
			$result[$fieldName]['value'] = $fieldValue;
            $result[$fieldName]['display_value'] = decode_html($fieldModel->getDisplayValue($fieldValue));
		}

		$result['_recordLabel'] = $recordModel->getName();
		$result['_recordId'] = $recordModel->getId();

		$response = new Vtiger_Response();
		$response->setEmitType(Vtiger_Response::$EMIT_JSON);
		$response->setResult($result);
		$response->emit();
	}

	/**
	 * Function to get the record model based on the request parameters
	 * @param Vtiger_Request $request
	 * @return Vtiger_Record_Model or Module specific Record Model instance
	 */
	public function getRecordModelFromRequest(Vtiger_Request $request) {
		
		$recordModel = parent::getRecordModelFromRequest($request);
		
		$recordId = $request->get('record');
		
		$startDate = $request->get('date_start');
		
		if(!empty($startDate)) {
			//Start Date and Time values
			$startTime = Vtiger_Time_UIType::getTimeValueWithSeconds($request->get('time_start'));
			$startDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($request->get('date_start')." ".$startTime);
			list($startDate, $startTime) = explode(' ', $startDateTime);

			$recordModel->set('date_start', $startDate);
			$recordModel->set('time_start', $startTime);
		}

		$endDate = $request->get('due_date');
		if(!empty($endDate)) {
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
		}

		if($recordId){
			
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
		
		return $recordModel;
	}
}

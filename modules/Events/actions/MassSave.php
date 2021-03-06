<?php

class Events_MassSave_Action extends Vtiger_MassSave_Action {

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
		
		$skipFields = array('date_start','time_start','due_date','time_end');
		
		foreach($recordIds as $recordId) {
			
			$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleModel);
			$recordModel->set('id', $recordId);
			$recordModel->set('mode', 'edit');

			foreach ($fieldModelList as $fieldName => $fieldModel) {
				
				if(in_array($fieldName,$skipFields))continue;
				
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
				
				$endTime = $request->get('time_end');
				
				$endDate = Vtiger_Date_UIType::getDBInsertedValue($request->get('due_date'));
		
				if ($endTime) {
					$endTime = Vtiger_Time_UIType::getTimeValueWithSeconds($endTime);
					$endDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($request->get('due_date')." ".$endTime);
					list($endDate, $endTime) = explode(' ', $endDateTime);
				}
			} else {
				$endDate = $recordModel->get("due_date");
				$endTime = $recordModel->get("time_end");
			}
			
			$datetime1 = strtotime($startDate . ' ' . $startTime);
			$datetime2 = strtotime($endDate . ' ' . $endTime);
			
			if($datetime1 <= $datetime2){
						
				$recordModel->set('date_start', $startDate);
				$recordModel->set('time_start', $startTime);
		
				$recordModel->set('time_end', $endTime);
				$recordModel->set('due_date', $endDate);
			}
			
			
	        /*$exchange_info = OmniCal_CRMExchangeHandler_Model::GetActivityIdAndChangeKey($recordId);
			
			if(!empty($exchange_info))
            	$recordModel->set("update_exchange", 1);
			else
				$recordModel->set("update_exchange", 0);
			*/
			
			$recordModels[$recordId] = $recordModel;
		}	
		
		return $recordModels;
	}	
	
	protected function getRecordsListFromRequest(Vtiger_Request $request) {
	    $cvId = $request->get('viewname');
	    $module = $request->get('module');
	    if(!empty($cvId) && $cvId=="undefined"){
	        $sourceModule = $request->get('sourceModule');
	        $cvId = CustomView_Record_Model::getAllFilterByModule($sourceModule)->getId();
	    }
	    $selectedIds = $request->get('selected_ids');
	    $excludedIds = $request->get('excluded_ids');
	    
	    if(!empty($selectedIds) && $selectedIds != 'all') {
	        if(!empty($selectedIds) && count($selectedIds) > 0) {
	            return $selectedIds;
	        }
	    }
	    
	    $customViewModel = CustomView_Record_Model::getInstanceById($cvId);
	    if($customViewModel) {
	        $searchKey = $request->get('search_key');
	        $searchValue = $request->get('search_value');
	        $operator = $request->get('operator');
	        if(!empty($operator)) {
	            $customViewModel->set('operator', $operator);
	            $customViewModel->set('search_key', $searchKey);
	            $customViewModel->set('search_value', $searchValue);
	        }
	        
	        /**
	         *  Mass action on Documents if we select particular folder is applying on all records irrespective of
	         *  seleted folder
	         */
	        if ($module == 'Documents') {
	            $customViewModel->set('folder_id', $request->get('folder_id'));
	            $customViewModel->set('folder_value', $request->get('folder_value'));
	        }
	        
	        $customViewModel->set('search_params',$request->get('search_params'));
	        return $customViewModel->getEventsRecordIds($excludedIds,$module);
	    }
	}
}
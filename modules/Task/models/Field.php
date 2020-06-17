<?php

class Task_Field_Model extends Vtiger_Field_Model {

	/**
	 * Function returns special validator for fields
	 * @return <Array>
	 */
	function getValidator() {
		
		$validator = array();
		
		$fieldName = $this->getName();

		switch($fieldName) {
			case 'due_date': $funcName = array('name' => 'greaterThanDependentField', 'params' => array('date_start'));
							array_push($validator, $funcName);
							break;
            default : $validator = parent::getValidator();
						break;
		}
		
		return $validator;
	}

	/**
	 * Function to get the Webservice Field data type
	 * @return <String> Data type of the field
	*/
	/*public function getFieldDataType() {
		if($this->getName() == 'date_start' || $this->getName() == 'due_date') {
			return 'datetime';
		}
		$webserviceField = $this->getWebserviceFieldObject();
		return $webserviceField->getFieldDataType();
	}*/

	/**
	 * Customize the display value for detail view.
	 */
	public function getDisplayValue($value, $record = false, $recordInstance = false) {
		if ($recordInstance) {
			if ($this->getName() == 'date_start') {
				$dateTimeValue = $value . ' '. $recordInstance->get('time_start');
				$value = $this->getUITypeModel()->getDisplayValue($dateTimeValue);
				list($startDate, $startTime,$meridiem) = explode(' ', $value);
                 return $startDate . ' ' . $startTime.' '. $meridiem;
			} else if ($this->getName() == 'due_date') {
				$dateTimeValue = $value . ' '. $recordInstance->get('time_end');
				$value = $this->getUITypeModel()->getDisplayValue($dateTimeValue);
				list($startDate, $startTime,$meridiem) = explode(' ', $value);
				return $startDate;
                //return $startDate . ' ' . $startTime.' '. $meridiem;
			}
		}
		return parent::getDisplayValue($value, $record, $recordInstance);
	}

	/**
	 * Function to get Edit view display value
	 * @param <String> Data base value
	 * @return <String> value
	 */
	public function getEditViewDisplayValue($value, $blockfields = FALSE, $mode = false) {
	    
		$fieldName = $this->getName();

		if($mode == "massedit"){
		    
		    $emptyFieldList = array('date_start', 'due_date');
		    
		    if(in_array($fieldName, $emptyFieldList)){
		        return ;
		    } else if($fieldName == 'time_start' || $fieldName == 'time_end')
		        return;
		        
		}
		if ($fieldName == 'time_start' || $fieldName == 'time_end') {
		    if($blockfields && !empty($value)) {
		        $dateField = ($fieldName == 'time_start' ? $blockfields['date_start'] : $blockfields['due_date']);
		        $value = $dateField->get('fieldvalue')." ".$value;
		    }
		    return $this->getUITypeModel()->getDisplayTimeDifferenceValue($fieldName, $value);
		}
		
		//Set the start date and end date
		if(empty($value)) {
			if ($fieldName === 'date_start') {
				return DateTimeField::convertToUserFormat(date('Y-m-d'));
			} elseif ($fieldName === 'due_date') {
				$currentUser = Users_Record_Model::getCurrentUserModel();
				$minutes = $currentUser->get('callduration');
				return DateTimeField::convertToUserFormat(date('Y-m-d', strtotime("+$minutes minutes")));
			}
		}
		return parent::getEditViewDisplayValue($value);
	}
	
	/**
	 * Function to get the advanced filter option names by Field type
	 * @return <Array>
	 */
	public static function getAdvancedFilterOpsByFieldType() {
		
		$filterOpsByFieldType = parent::getAdvancedFilterOpsByFieldType();
		$filterOpsByFieldType['O'] = array('e','n');
		
		return $filterOpsByFieldType;
	}
	
	public function getDBFormatDisplayValue($value, $record = false, $recordInstance = false){
		if ($this->getName() == 'date_start') {
			$dateTimeValue = $value . ' '. $recordInstance->get('time_start');
			return $dateTimeValue;
		} 
		return $value;
	}
	
	/**
	 * Function to check whether field is ajax editable'
	 * @return <Boolean>
	 */
	public function isAjaxEditable() {
		if($this->getName() == 'date_start') {
			return false;
		}
		
		return parent::isAjaxEditable();
	}
}

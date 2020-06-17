<?php

class Task_Datetime_UIType extends Vtiger_Datetime_UIType {
	
	public function getDisplayValue($value) {
		
		if($this->hasTimeComponent($value)) {
			return self::getDisplayDateTimeValue($value);
		}else{
			return $this->getDisplayDateValue($value);
		}
	}

	public function hasTimeComponent($value) {
		$component = explode(' ', $value);
		if(!empty($component[1])) {
			return true;
		}
		return false;
	}
}



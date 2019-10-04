<?php

class Task_Record_Model extends Vtiger_Record_Model {

	function save() {
		$_REQUEST['time_start'] = Vtiger_Time_UIType::getTimeValueWithSeconds($_REQUEST['time_start']);
		$_REQUEST['time_end'] = Vtiger_Time_UIType::getTimeValueWithSeconds($_REQUEST['time_end']);
		parent::save();
	}
}
<?php

class Task extends CRMEntity {
	var $table_name = 'vtiger_task';
	var $table_index= 'taskid';

	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_taskcf', 'taskid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('vtiger_crmentity', 'vtiger_task', 'vtiger_taskcf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_task' => 'taskid',
		'vtiger_taskcf'=>'taskid');

	var $list_fields = Array(
       	'Subject'=>Array('task'=>'subject'),
       	'Start Date'=>Array('task'=>'date_start'),
       	'Start Time'=>Array('task','time_start'),
       	'End Date'=>Array('task'=>'due_date'),
       	'Assigned To'=>Array('task'=>'smownerid'),
		'Status'=>Array('task'=>'task_status'),
    );

	var $list_fields_name = Array(
    	'Subject'=>'subject',
       	'Start Date'=>'date_start',
       	'Start Time'=>'time_start',
       	'End Date'=>'due_date',
    	'Assigned To'=>'assigned_user_id',
		'Status'=>'task_status',
    );

	// Make the field link to detail view
	var $list_link_field = 'subject';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'Subject' => Array('task', 'subject'),
		'Assigned To' => Array('vtiger_crmentity','assigned_user_id'),
	);
	var $search_fields_name = Array (
		/* Format: Field Label => fieldname */
		'Subject' => 'subject',
		'Assigned To' => 'assigned_user_id',
	);

	// For Popup window record selection
	var $popup_fields = Array ('subject');

	// For Alphabetical search
	var $def_basicsearch_col = 'subject';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'subject';

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('subject','assigned_user_id','due_date','task_status');

	// Used when enabling/disabling the related tab view fields for the module.
	var $related_tab_fields = Array('subject','due_date','task_status');
	
	var $default_order_by = 'due_date';
	var $default_sort_order = 'ASC';
	
	
	function Task() {
		$this->log = LoggerManager::getLogger('Task');
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('Task');
	}
	
	function save_module($module){
	}
	
	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/
	function vtlib_handler($moduleName, $eventType) {
		global $adb;
 		if($eventType == 'module.postinstall') {
			// TODO Handle actions after this module is installed.
		} else if($eventType == 'module.disabled') {
			// TODO Handle actions before this module is being uninstalled.
		} else if($eventType == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} else if($eventType == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} else if($eventType == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
		}
 	}
}
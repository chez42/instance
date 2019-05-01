<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

include_once 'modules/Vtiger/CRMEntity.php';

class __ModuleName__ extends Vtiger_CRMEntity {
	var $table_name = 'vtiger_<modulename>';
	var $table_index= '<modulename>id';
	var $related_tables = Array ('vtiger_<modulename>cf' => Array ( '<modulename>id', 'vtiger_<modulename>', '<modulename>id' ),);


	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_<modulename>cf', '<modulename>id');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('vtiger_crmentity', 'vtiger_<modulename>', 'vtiger_<modulename>cf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_<modulename>' => '<modulename>id',
		'vtiger_<modulename>cf'=>'<modulename>id');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'<entityfieldlabel>' => Array('<modulename>', '<entitycolumn>'),
		'Assigned To' => Array('crmentity','smownerid')
	);
	var $list_fields_name = Array (
		/* Format: Field Label => fieldname */
		'<entityfieldlabel>' => '<entityfieldname>',
		'Assigned To' => 'assigned_user_id',
	);

	// Make the field link to detail view
	var $list_link_field = '<entityfieldname>';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'<entityfieldlabel>' => Array('<modulename>', '<entitycolumn>'),
		'Assigned To' => Array('vtiger_crmentity','assigned_user_id'),
	);
	var $search_fields_name = Array (
		/* Format: Field Label => fieldname */
		'<entityfieldlabel>' => '<entityfieldname>',
		'Assigned To' => 'assigned_user_id',
	);

	// For Popup window record selection
	var $popup_fields = Array ('<entityfieldname>');

	// For Alphabetical search
	var $def_basicsearch_col = '<entityfieldname>';

	// Column value to use on detail view record text display
	var $def_detailview_recname = '<entityfieldname>';

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('<entityfieldname>','assigned_user_id');

	var $default_order_by = '<entityfieldname>';
	var $default_sort_order='ASC';

	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/
	function vtlib_handler($moduleName, $eventType) {
		global $adb;
 		if($eventType == 'module.postinstall') {
			// TODO Handle actions after this module is installed.
			$this->init($moduleName);
			$this->createHandle($moduleName);
		} else if($eventType == 'module.disabled') {
			$this->removeHandle($moduleName);
			// TODO Handle actions before this module is being uninstalled.
		} else if($eventType == 'module.enabled') {
			$this->createHandle($moduleName);
			// TODO Handle actions before this module is being uninstalled.
		} else if($eventType == 'module.preuninstall') {
			$this->removeHandle($moduleName);
			// TODO Handle actions when this module is about to be deleted.
		} else if($eventType == 'module.preupdate') {
			$this->createHandle($moduleName);
			// TODO Handle actions before this module is updated.
		} else if($eventType == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
		}
 	}

	/**
	 * When install module
	 * @param $moduleName
	 */
	public function init($moduleName) {
		$module = Vtiger_Module::getInstance($moduleName);

		// Enable Activities
		$activityFieldTypeId = 34;
		$this->addModuleRelatedToForEvents($module->name, $activityFieldTypeId);

		// Enable ModTracker
		require_once 'modules/ModTracker/ModTracker.php';
		ModTracker::enableTrackingForModule($module->id);

		// Enable Comments
		$commentInstance = Vtiger_Module::getInstance('ModComments');
		$commentRelatedToFieldInstance = Vtiger_Field::getInstance('related_to', $commentInstance);
		$commentRelatedToFieldInstance->setRelatedModules(array($module->name));

		// Customize Record Numbering
		$prefix = 'NO';
		if (strlen($module->name) >= 2) {
			$prefix = substr($module->name, 0, 2);
			$prefix = strtoupper($prefix);
		}
		$this->customizeRecordNumbering($module->name, $prefix, 1);

	}

	/**
	 * @param string $moduleName
	 * @param int $fieldTypeId
	 */
	public function addModuleRelatedToForEvents($moduleName, $fieldTypeId)
	{
		global $adb;

		$sqlCheckProject = "SELECT * FROM `vtiger_ws_referencetype` WHERE fieldtypeid = ? AND type = ?";
		$rsCheckProject = $adb->pquery($sqlCheckProject, array($fieldTypeId, $moduleName));
		if ($adb->num_rows($rsCheckProject) < 1) {
			$adb->pquery("INSERT INTO `vtiger_ws_referencetype` (`fieldtypeid`, `type`) VALUES (?, ?)",
				array($fieldTypeId, $moduleName));
		}
	}

	/**
	 * @param string $sourceModule
	 * @param string $prefix
	 * @param int $sequenceNumber
	 * @return array
	 */
	public function customizeRecordNumbering($sourceModule, $prefix = 'NO', $sequenceNumber = 1)
	{
		$moduleModel = Settings_Vtiger_CustomRecordNumberingModule_Model::getInstance($sourceModule);
		$moduleModel->set('prefix', $prefix);
		$moduleModel->set('sequenceNumber', $sequenceNumber);

		$result = $moduleModel->setModuleSequence();

		return $result;
	}

	private function createHandle($moduleName)
	{
		include_once 'include/events/VTEventsManager.inc';
		global $adb;
		$em = new VTEventsManager($adb);
		$em->setModuleForHandler($moduleName, "{$moduleName}Handler.php");
		$em->registerHandler("vtiger.entity.aftersave", "modules/{$moduleName}/{$moduleName}Handler.php", "{$moduleName}Handler");
	}

	/**
	 * @param string $moduleName
	 */
	private function removeHandle($moduleName)
	{
		include_once 'include/events/VTEventsManager.inc';
		global $adb;
		$em = new VTEventsManager($adb);
		$em->unregisterHandler("{$moduleName}Handler");
	}

}
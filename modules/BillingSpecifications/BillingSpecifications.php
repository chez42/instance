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

class BillingSpecifications extends Vtiger_CRMEntity {
	var $table_name = 'vtiger_billingspecifications';
	var $table_index= 'billingspecificationsid';

	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_billingspecificationscf', 'billingspecificationsid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('vtiger_crmentity', 'vtiger_billingspecifications', 'vtiger_billingspecificationscf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_billingspecifications' => 'billingspecificationsid',
		'vtiger_billingspecificationscf'=>'billingspecificationsid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'Name' => Array('billingspecifications', 'name'),
		'Assigned To' => Array('crmentity','smownerid')
	);
	var $list_fields_name = Array (
		/* Format: Field Label => fieldname */
		'Name' => 'name',
		'Assigned To' => 'assigned_user_id',
	);

	// Make the field link to detail view
	var $list_link_field = 'name';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'Name' => Array('billingspecifications', 'name'),
		'Assigned To' => Array('vtiger_crmentity','assigned_user_id'),
	);
	var $search_fields_name = Array (
		/* Format: Field Label => fieldname */
		'Name' => 'name',
		'Assigned To' => 'assigned_user_id',
	);

	// For Popup window record selection
	var $popup_fields = Array ('name');

	// For Alphabetical search
	var $def_basicsearch_col = 'name';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'name';

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('name','assigned_user_id');

	var $default_order_by = 'name';
	var $default_sort_order='ASC';

	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/
	function vtlib_handler($moduleName, $eventType) {
		global $adb;
		
 		if($eventType == 'module.postinstall') {

 		    $adb->pquery("CREATE TABLE IF NOT EXISTS vtiger_billing_range (
            `rangeid` INT(11) NOT NULL AUTO_INCREMENT,
            `billingid` INT(11) NULL,
            `from` VARCHAR(255) NULL,
            `to` VARCHAR(255) NULL,
            `type` VARCHAR(255) NULL,
            `value` VARCHAR(255) NULL ,
            PRIMARY KEY (`rangeid`));");
			
 		} else if($eventType == 'module.enabled') {
 		    
 		    $adb->pquery("CREATE TABLE IF NOT EXISTS vtiger_billing_range (
            `rangeid` INT(11) NOT NULL AUTO_INCREMENT,
            `billingid` INT(11) NULL,
            `from` VARCHAR(255) NULL,
            `to` VARCHAR(255) NULL,
            `type` VARCHAR(255) NULL,
            `value` VARCHAR(255) NULL ,
            PRIMARY KEY (`rangeid`));");
 		    
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
 	
 	function save_module($module) {
 	    $this->saveScheduleDetails();
 	}
 	
 	
 	function saveScheduleDetails(){
 	    
 	    $adb = PearDatabase::getInstance();
 	    
 	    $id = $this->id;
 	    
 	    if(isset($_REQUEST['totalscheduleCount']) && $_REQUEST['totalscheduleCount'] >0){
 	        
 	        if($this->mode == 'edit'){
 	            $adb->pquery("delete from vtiger_billing_range where billingid = ?",array($id));
 	        }
 	        
 	        $totalScheduleCount = $_REQUEST['totalscheduleCount'];
 	        
 	        for($i=1; $i<=$totalScheduleCount; $i++){
 	            
 	            $scheduleItems = array();
 	            
 	            $from= vtlib_purify($_REQUEST['from'.$i]);
 	            
 	            $to = vtlib_purify($_REQUEST['to'.$i]);
 	            
 	            $type = vtlib_purify($_REQUEST['type'.$i]);
 	            
 	            $value = vtlib_purify($_REQUEST['value'.$i]);
 	                
                $scheduleItems = array($id, $from, $to, $type, $value);
                
                $query = "INSERT INTO vtiger_billing_range(`billingid`, `from`, `to`, `type`, `value`) 
                    VALUES (?, ?, ?, ?, ?)";
                
                $adb->pquery($query, $scheduleItems);
 	                
 	        }
 	        
 	    }
 	}
 	
 	
}
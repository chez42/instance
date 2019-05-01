<?php
/************************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.2
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class EmailTemplates extends CRMEntity {
	var $table_name = 'vtiger_emailtemplates';
	var $table_index= 'templateid';

	/**
	 * Mandatory table for supporting custom fields.
	 */
	//var $customFieldTable = Array('vtiger_emailtemplatescf', 'templateid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('vtiger_crmentity', 'vtiger_emailtemplates', 'vtiger_emailtemplates_view_permission');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_emailtemplates' => 'templateid',
		'vtiger_emailtemplates_view_permission'=>'template_id');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'LBL_TEMPLATE_NAME' => Array('emailtemplates', 'templatename'),
		'Assigned To' => Array('crmentity','smownerid')
	);
	var $list_fields_name = Array (
		/* Format: Field Label => fieldname */
		'LBL_TEMPLATE_NAME' => 'templatename',
		'Assigned To' => 'assigned_user_id',
	);

	// Make the field link to detail view
	var $list_link_field = 'templatename';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'LBL_TEMPLATE_NAME' => Array('emailtemplates', 'templatename'),
		'Assigned To' => Array('vtiger_crmentity','assigned_user_id'),
	);
	var $search_fields_name = Array (
		/* Format: Field Label => fieldname */
		'LBL_TEMPLATE_NAME' => 'templatename',
		'Assigned To' => 'assigned_user_id',
	);

	// For Popup window record selection
	var $popup_fields = Array ('templatename');

	// For Alphabetical search
	var $def_basicsearch_col = 'templatename';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'templatename';

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('templatename','assigned_user_id');

	var $default_order_by = 'templatename';
	var $default_sort_order='ASC';
	
	
 
    public function getNonAdminAccessControlQuery($module, $user,$scope='') {
       
        $query = " ";
        
        $currentUser = Users_Record_Model::getCurrentUserModel();
        
        if(!$currentUser->isAdminUser()) {
            $permission_ids = array();
           
            array_push($permission_ids, $user->id);
            
            $groups = new GetUserGroups();
            
            $groups->getAllUserGroups($user->id);
            
            $groups = $groups->user_groups;
            
            foreach($groups as $group){
                array_push($permission_ids, $group);
            }
            
            $view_permission_ids = implode(',',$permission_ids);
           
            $query = " INNER JOIN vtiger_emailtemplates_view_permission on vtiger_emailtemplates_view_permission.template_id = vtiger_emailtemplates.templateid
			AND vtiger_emailtemplates_view_permission.view_permission_id IN ($view_permission_ids)";
        }
        
        return $query;
    }
    
}
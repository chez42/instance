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

class DocumentFolder extends Vtiger_CRMEntity {
	var $table_name = 'vtiger_documentfolder';
	var $table_index= 'documentfolderid';

	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_documentfoldercf', 'documentfolderid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('vtiger_crmentity', 'vtiger_documentfolder', 'vtiger_documentfoldercf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_documentfolder' => 'documentfolderid',
		'vtiger_documentfoldercf'=>'documentfolderid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'Folder Name' => Array('documentfolder', 'folder_name'),
		'Assigned To' => Array('crmentity','smownerid')
	);
	var $list_fields_name = Array (
		/* Format: Field Label => fieldname */
		'Folder Name' => 'folder_name',
		'Assigned To' => 'assigned_user_id',
	);

	// Make the field link to detail view
	var $list_link_field = 'folder_name';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'Folder Name' => Array('documentfolder', 'folder_name'),
		'Assigned To' => Array('vtiger_crmentity','assigned_user_id'),
	);
	var $search_fields_name = Array (
		/* Format: Field Label => fieldname */
		'Folder Name' => 'folder_name',
		'Assigned To' => 'assigned_user_id',
	);

	// For Popup window record selection
	var $popup_fields = Array ('folder_name');

	// For Alphabetical search
	var $def_basicsearch_col = 'folder_name';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'folder_name';

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('folder_name','assigned_user_id');

	var $default_order_by = 'crmid';
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
	
	function DocumentFolder() {
		$this->log = LoggerManager::getLogger('DocumentFolder');
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('DocumentFolder');
	}

	
	function save_module($module)
	{
	    global $adb,$current_user;
	    if($mode == ''){
	        
	        $selectFolder = $adb->pquery("SELECT * FROM vtiger_documentfolder_view_permissions WHERE documentfolderid = ? AND share_permission_id = ?",
	            array($this->id,$current_user->id));
	        
	        if(!$adb->num_rows($selectFolder)){
	           $adb->pquery("INSERT INTO vtiger_documentfolder_view_permissions(documentfolderid, share_permission_id) VALUES (?,?)",
	            array($this->id,$current_user->id));
	        }
	    }
	}
	
	
	/**
	 *
	 * @param String $module - module name for which query needs to be generated.
	 * @param Users $user - user for which query needs to be generated.
	 * @return String Access control Query for the user.
	 */
	function getNonAdminAccessControlQuery($module, $user, $scope = '') {
	    
	    require('user_privileges/user_privileges_' . $user->id . '.php');
	    
	    require('user_privileges/sharing_privileges_' . $user->id . '.php');
	    
	    $query = ' ';
	    $tabId = getTabid($module);
	    if ($is_admin == false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2]
	        == 1 && $defaultOrgSharingPermission[$tabId] == 3) {
	            
	            $tableName = 'vt_tmp_u' . $user->id;
	            
	            $sharingRuleInfoVariable = $module . '_share_read_permission';
	            
	            $sharingRuleInfo = $sharingRuleInfoVariable;
	            
	            $sharedTabId = null;
	            
	            if (!empty($sharingRuleInfo) && (count($sharingRuleInfo['ROLE']) > 0 ||
	                count($sharingRuleInfo['GROUP']) > 0)) {
	                    
	                    $tableName = $tableName . '_t' . $tabId;
	                    $sharedTabId = $tabId;
	                    
	                }
	                
	                $this->setupTemporaryTable($tableName, $sharedTabId, $user, $current_user_parent_role_seq, $current_user_groups);
	                
	                $permission_ids = array();
	                
	                array_push($permission_ids,$user->id);
	                
	                $groups = new GetUserGroups();
	                
	                $groups->getAllUserGroups($user->id);
	                
	                $groups = $groups->user_groups;
	                
	                foreach($groups as $group){
	                    array_push($permission_ids, $group);
	                }
	                
	                $view_permission_ids = implode(',',$permission_ids);
	                
	                if($scope == ''){
	                    
                        $query = " INNER JOIN vtiger_documentfolder_view_permissions on vtiger_documentfolder_view_permissions.documentfolderid = vtiger_documentfolder.documentfolderid
                            OR vtiger_documentfolder.default_for_all_users = 1 ".
   	                        " INNER JOIN $tableName $tableName$scope ON $tableName$scope.id = vtiger_crmentity$scope.smownerid ".
   	                        " OR $tableName$scope.id = vtiger_crmentity$scope.smcreatorid OR vtiger_documentfolder_view_permissions.share_permission_id = $tableName$scope.id ".
   	                        " AND (vtiger_documentfolder_view_permissions.share_permission_id IN('". $view_permission_ids ."')  OR vtiger_crmentity.smcreatorid = '".$user->id."') ".
   	                        " ";
	                    
	                }else{
	                    
	                    $query = " INNER JOIN $tableName $tableName$scope ON $tableName$scope.id = " .
	                    "vtiger_crmentity$scope.smownerid OR vtiger_crmentity$scope.smownerid IS NULL";
	                    
	                }
	                
	                
	                /* ===  END : 28-Aug-2018 Changes For View Permission === */
	        }
	        
	        return $query;
	}

}
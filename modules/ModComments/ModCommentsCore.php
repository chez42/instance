<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');
require_once 'vtlib/Vtiger/Module.php';

class ModCommentsCore extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

	var $table_name = 'vtiger_modcomments';
	var $table_index= 'modcommentsid';

	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_modcommentscf', 'modcommentsid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('vtiger_crmentity', 'vtiger_modcomments', 'vtiger_modcommentscf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_modcomments' => 'modcommentsid',
		'vtiger_modcommentscf'=>'modcommentsid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'Comment' => Array('modcomments', 'commentcontent'),
		'Assigned To' => Array('crmentity','smownerid')
	);
	var $list_fields_name = Array (
		/* Format: Field Label => fieldname */
		'Comment' => 'commentcontent',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view
	var $list_link_field = 'commentcontent';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'Comment' => Array('modcomments', 'commentcontent')
	);
	var $search_fields_name = Array (
		/* Format: Field Label => fieldname */
		'Comment' => 'commentcontent'
	);

	// For Popup window record selection
	var $popup_fields = Array ('commentcontent');

	// Allow sorting on the following (field column names)
	var $sortby_fields = Array ('commentcontent');

	// Should contain field labels
	//var $detailview_links = Array ('Comment');

	// For Alphabetical search
	var $def_basicsearch_col = 'commentcontent';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'commentcontent';

	// Required Information for enabling Import feature
	var $required_fields = Array ('assigned_user_id'=>1);

	// Callback function list during Importing
	var $special_functions = Array('set_import_assigned_user');

	var $default_order_by = 'modcommentsid';
	var $default_sort_order='DESC';

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('createdtime', 'modifiedtime', 'commentcontent');

	function __construct() {
		global $log, $currentModule;
		$this->column_fields = getColumnFields('ModComments');
		$this->db = PearDatabase::getInstance();
		$this->log = $log;
	}

	function getSortOrder() {
		global $currentModule;

		$sortorder = $this->default_sort_order;
		if($_REQUEST['sorder']) $sortorder = $this->db->sql_escape_string($_REQUEST['sorder']);
		else if($_SESSION[$currentModule.'_Sort_Order'])
			$sortorder = $_SESSION[$currentModule.'_Sort_Order'];

		return $sortorder;
	}

	function getOrderBy() {
		global $currentModule;

		$use_default_order_by = '';
		if(PerformancePrefs::getBoolean('LISTVIEW_DEFAULT_SORTING', true)) {
			$use_default_order_by = $this->default_order_by;
		}

		$orderby = $use_default_order_by;
		if($_REQUEST['order_by']) $orderby = $this->db->sql_escape_string($_REQUEST['order_by']);
		else if($_SESSION[$currentModule.'_Order_By'])
			$orderby = $_SESSION[$currentModule.'_Order_By'];
		return $orderby;
	}

	function save_module($module) {
	    $related_to = $this->column_fields['related_to'];
	    
	    $se_type = getSalesEntityType($related_to);
	    
	    if($se_type == 'Contacts'){
	        
	        $customer = $this->column_fields['customer'];
	        if($customer){
	            $from_portal = 1;
	        } else {
	            $from_portal = 0;
	        }
	        
	        $ch = curl_init('http://dev.omnisrv.com:3000');
	        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	        $jsonData = json_encode([
	            'contactid' => $related_to,
	            'fromportal' => $from_portal
	        ]);
	        $query = http_build_query(['data' => $jsonData]);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        curl_exec($ch);
	        curl_close($ch);
	    }
	}

	/**
	 * Return query to use based on given modulename, fieldname
	 * Useful to handle specific case handling for Popup
	 */
	function getQueryByModuleField($module, $fieldname, $srcrecord) {
		// $srcrecord could be empty
	}

	/**
	 * Get list view query (send more WHERE clause condition if required)
	 */
	function getListQuery($module, $usewhere=false) {
		$query = "SELECT vtiger_crmentity.*, $this->table_name.*";

		// Keep track of tables joined to avoid duplicates
		$joinedTables = array();

		// Select Custom Field Table Columns if present
		if(!empty($this->customFieldTable)) $query .= ", " . $this->customFieldTable[0] . ".* ";

		$query .= " FROM $this->table_name";

		$query .= "	INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index";

		$joinedTables[] = $this->table_name;
		$joinedTables[] = 'vtiger_crmentity';

		// Consider custom table join as well.
		if(!empty($this->customFieldTable)) {
			$query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
					  " = $this->table_name.$this->table_index";
			$joinedTables[] = $this->customFieldTable[0];
		}
		$query .= " LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid";
		$query .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

		$joinedTables[] = 'vtiger_users';
		$joinedTables[] = 'vtiger_groups';

		$linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM vtiger_field" .
				" INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid" .
				" WHERE uitype='10' AND vtiger_fieldmodulerel.module=?", array($module));
		$linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);

		for($i=0; $i<$linkedFieldsCount; $i++) {
			$related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
			$fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
			$columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');

			$other =  CRMEntity::getInstance($related_module);
			vtlib_setup_modulevars($related_module, $other);

			if(!in_array($other->table_name, $joinedTables)) {
				$query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index = $this->table_name.$columnname";
				$joinedTables[] = $other->table_name;
			}
		}

		$query .= "	WHERE vtiger_crmentity.deleted = 0 ";
		if($usewhere) {
			$query .= $usewhere;
		}
		$query .= $this->getListViewSecurityParameter($module);
		return $query;
	}

	/**
	 * Apply security restriction (sharing privilege) query part for List view.
	 */
	function getListViewSecurityParameter($module) {
		global $current_user;
		require('user_privileges/user_privileges_'.$current_user->id.'.php');
		require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

		$sec_query = '';
		$tabid = getTabid($module);

		if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1
			&& $defaultOrgSharingPermission[$tabid] == 3) {

				$sec_query .= " AND (vtiger_crmentity.smownerid in($current_user->id) OR vtiger_crmentity.smownerid IN
					(
						SELECT vtiger_user2role.userid FROM vtiger_user2role
						INNER JOIN vtiger_users ON vtiger_users.id=vtiger_user2role.userid
						INNER JOIN vtiger_role ON vtiger_role.roleid=vtiger_user2role.roleid
						WHERE vtiger_role.parentrole LIKE '".$current_user_parent_role_seq."::%'
					)
					OR vtiger_crmentity.smownerid IN
					(
						SELECT shareduserid FROM vtiger_tmp_read_user_sharing_per
						WHERE userid=".$current_user->id." AND tabid=".$tabid."
					)
					OR
						(";

					// Build the query based on the group association of current user.
					if(sizeof($current_user_groups) > 0) {
						$sec_query .= " vtiger_groups.groupid IN (". implode(",", $current_user_groups) .") OR ";
					}
					$sec_query .= " vtiger_groups.groupid IN
						(
							SELECT vtiger_tmp_read_group_sharing_per.sharedgroupid
							FROM vtiger_tmp_read_group_sharing_per
							WHERE userid=".$current_user->id." and tabid=".$tabid."
						)";
				$sec_query .= ")
				)";
		}
		return $sec_query;
	}

	/**
	 * Create query to export the records.
	 */
	function create_export_query($where)
	{
		global $current_user;

		include("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery('ModComments', "detail_view");

		$fields_list = getFieldsListFromQuery($sql);

		$query = "SELECT $fields_list, vtiger_users.user_name AS user_name
					FROM vtiger_crmentity INNER JOIN $this->table_name ON vtiger_crmentity.crmid=$this->table_name.$this->table_index";

		if(!empty($this->customFieldTable)) {
			$query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
					  " = $this->table_name.$this->table_index";
		}

		$query .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
		$query .= " LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id and vtiger_users.status='Active'";

		$linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM vtiger_field" .
				" INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid" .
				" WHERE uitype='10' AND vtiger_fieldmodulerel.module=?", array($thismodule));
		$linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);

		for($i=0; $i<$linkedFieldsCount; $i++) {
			$related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
			$fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
			$columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');

			$other = CRMEntity::getInstance($related_module);
			vtlib_setup_modulevars($related_module, $other);

			$query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index = $this->table_name.$columnname";
		}

		$where_auto = " vtiger_crmentity.deleted=0";

		if($where != '') $query .= " WHERE ($where) AND $where_auto";
		else $query .= " WHERE $where_auto";

		require('user_privileges/user_privileges_'.$current_user->id.'.php');
		require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

		// Security Check for Field Access
		if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1 && $defaultOrgSharingPermission[7] == 3)
		{
			//Added security check to get the permitted records only
			$query = $query." ".getListViewSecurityParameter($thismodule);
		}
		return $query;
	}

	/**
	 * Transform the value while exporting (if required)
	 */
	function transform_export_value($key, $value) {
		return parent::transform_export_value($key, $value);
	}

	/**
	 * Function which will give the basic query to find duplicates
	 */
	function getDuplicatesQuery($module,$table_cols,$field_values,$ui_type_arr,$select_cols='') {
		$select_clause = "SELECT ". $this->table_name .".".$this->table_index ." AS recordid, vtiger_users_last_import.deleted,".$table_cols;

		// Select Custom Field Table Columns if present
		if(isset($this->customFieldTable)) $query .= ", " . $this->customFieldTable[0] . ".* ";

		$from_clause = " FROM $this->table_name";

		$from_clause .= "	INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index";

		// Consider custom table join as well.
		if(isset($this->customFieldTable)) {
			$from_clause .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
					  " = $this->table_name.$this->table_index";
		}
		$from_clause .= " LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
						LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

		$where_clause = "	WHERE vtiger_crmentity.deleted = 0";
		$where_clause .= $this->getListViewSecurityParameter($module);

		if (isset($select_cols) && trim($select_cols) != '') {
			$sub_query = "SELECT $select_cols FROM  $this->table_name AS t " .
				" INNER JOIN vtiger_crmentity AS crm ON crm.crmid = t.".$this->table_index;
			// Consider custom table join as well.
			if(isset($this->customFieldTable)) {
				$sub_query .= " LEFT JOIN ".$this->customFieldTable[0]." tcf ON tcf.".$this->customFieldTable[1]." = t.$this->table_index";
			}
			$sub_query .= " WHERE crm.deleted=0 GROUP BY $select_cols HAVING COUNT(*)>1";
		} else {
			$sub_query = "SELECT $table_cols $from_clause $where_clause GROUP BY $table_cols HAVING COUNT(*)>1";
		}


		$query = $select_clause . $from_clause .
					" LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id=" . $this->table_name .".".$this->table_index .
					" INNER JOIN (" . $sub_query . ") AS temp ON ".get_on_clause($field_values,$ui_type_arr,$module) .
					$where_clause .
					" ORDER BY $table_cols,". $this->table_name .".".$this->table_index ." ASC";

		return $query;
	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	function generateReportsSecQuery($module,$secmodule,$queryplanner){
		$matrix = $queryplanner->newDependencyMatrix();

		$matrix->setDependency('vtiger_crmentityModComments',array('vtiger_groupsModComments','vtiger_usersModComments', 'vtiger_contactdetailsRelModComments', 'vtiger_modcommentsRelModComments'));

		if (!$queryplanner->requireTable("vtiger_modcomments",$matrix)){
			return '';
		}
		$matrix->setDependency('vtiger_modcomments', array('vtiger_crmentityModComments'));

		$query = $this->getRelationQuery($module,$secmodule,"vtiger_modcomments","modcommentsid", $queryplanner);

		if ($queryplanner->requireTable("vtiger_crmentityModComments",$matrix)){
			$query .= " left join vtiger_crmentity as vtiger_crmentityModComments on vtiger_crmentityModComments.crmid=vtiger_modcomments.modcommentsid and vtiger_crmentityModComments.deleted=0";
		}
		if ($queryplanner->requireTable("vtiger_groupsModComments")){
			$query .= " left join vtiger_groups vtiger_groupsModComments on vtiger_groupsModComments.groupid = vtiger_crmentityModComments.smownerid";
		}
		if ($queryplanner->requireTable("vtiger_usersModComments")){
			$query .= " left join vtiger_users as vtiger_usersModComments on vtiger_usersModComments.id = vtiger_crmentityModComments.smownerid";
		}
		if ($queryplanner->requireTable("vtiger_contactdetailsRelModComments")){
			$query .= " left join vtiger_contactdetails as vtiger_contactdetailsRelModComments on vtiger_contactdetailsRelModComments.contactid = vtiger_crmentityModComments.crmid";
		}
		if ($queryplanner->requireTable("vtiger_modcommentsRelModComments")){
			$query .= " left join vtiger_modcomments as vtiger_modcommentsRelModComments on vtiger_modcommentsRelModComments.modcommentsid = vtiger_crmentityModComments.crmid";
		}

		//if secondary modules custom reference field is selected
        $query .= parent::getReportsUiType10Query($secmodule, $queryplanner);

		return $query;
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	function vtlib_handler($modulename, $event_type) {
		if($event_type == 'module.postinstall') {
			// TODO Handle post installation actions
		} else if($event_type == 'module.disabled') {
			// TODO Handle actions when this module is disabled.
		} else if($event_type == 'module.enabled') {
			// TODO Handle actions when this module is enabled.
		} else if($event_type == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} else if($event_type == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} else if($event_type == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
		}
	}
	
	function uploadAndSaveFile($id, $module, $file_details, $attachmentType='Attachment') {
	    
	    global $log;
	    $log->debug("Entering into uploadAndSaveFile($id,$module,$file_details) method.");
	    
	    global $adb, $current_user;
	    global $upload_badext;
	    
	    $date_var = date("Y-m-d H:i:s");
	    
	    //to get the owner id
	    $ownerid = $this->column_fields['assigned_user_id'];
	    if (!isset($ownerid) || $ownerid == '')
	        $ownerid = $current_user->id;
	        
	        if (isset($file_details['original_name']) && $file_details['original_name'] != null) {
	            $file_name = $file_details['original_name'];
	        } else {
	            $file_name = $file_details['name'];
	        }
	        
	        // Check 1
	        $save_file = 'true';
	        //only images are allowed for Image Attachmenttype
	        $mimeType = vtlib_mime_content_type($file_details['tmp_name']);
	        $mimeTypeContents = explode('/', $mimeType);
	        // For contacts and products we are sending attachmentType as value
	        if ($attachmentType == 'Image' || ($file_details['size'] && $mimeTypeContents[0] == 'image')) {
	            $save_file = validateImageFile($file_details);
	        }
	        if ($save_file == 'false') {
	            return false;
	        }
	        
	        // Check 2
	        $save_file = 'true';
	        //only images are allowed for these modules
	        if ($module == 'Contacts' || $module == 'Products') {
	            $save_file = validateImageFile($file_details);
	        }
	        
	        $binFile = sanitizeUploadFileName($file_name, $upload_badext);
	        
	        $current_id = $adb->getUniqueID("vtiger_crmentity");
	        
	        $filename = ltrim(basename(" " . $binFile)); //allowed filename like UTF-8 characters
	        $filetype = $file_details['type'];
	        $filetmp_name = $file_details['tmp_name'];
	        
	        //get the file path inwhich folder we want to upload the file
	        $upload_file_path = decideFilePath();
	        
	        // upload the file in server
	        $upload_status = copy($filetmp_name, $upload_file_path . $current_id . "_" . $binFile);
	        // temporary file will be deleted at the end of request
	        
	        if ($save_file == 'true' && $upload_status == 'true') {
	            if($attachmentType != 'Image' && $this->mode == 'edit') {
	                //Only one Attachment per entity delete previous attachments
	                $res = $adb->pquery('SELECT vtiger_seattachmentsrel.attachmentsid FROM vtiger_seattachmentsrel
									INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_seattachmentsrel.attachmentsid AND vtiger_crmentity.setype = ?
									WHERE vtiger_seattachmentsrel.crmid = ?',array($module.' Attachment',$id));
	                $oldAttachmentIds = array();
	                for($attachItr = 0;$attachItr < $adb->num_rows($res);$attachItr++) {
	                    $oldAttachmentIds[] = $adb->query_result($res,$attachItr,'attachmentsid');
	                }
	                if(count($oldAttachmentIds)) {
	                    $adb->pquery('DELETE FROM vtiger_seattachmentsrel WHERE attachmentsid IN ('.generateQuestionMarks($oldAttachmentIds).')',$oldAttachmentIds);
	                    //TODO : revisit to delete actual file and attachment entry,as we need to see the deleted file in the history when its changed
	                    //$adb->pquery('DELETE FROM vtiger_attachments WHERE attachmentsid IN ('.generateQuestionMarks($oldAttachmentIds).')',$oldAttachmentIds);
	                    //$adb->pquery('DELETE FROM vtiger_crmentity WHERE crmid IN ('.generateQuestionMarks($oldAttachmentIds).')',$oldAttachmentIds);
	                }
	            }
	            //Add entry to crmentity
	            $sql1 = "INSERT INTO vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) VALUES (?, ?, ?, ?, ?, ?, ?)";
	            $params1 = array($current_id, $current_user->id, $ownerid, $module." ".$attachmentType, $this->column_fields['description'], $adb->formatDate($date_var, true), $adb->formatDate($date_var, true));
	            $adb->pquery($sql1, $params1);
	            //Add entry to attachments
	            $sql2 = "INSERT INTO vtiger_attachments(attachmentsid, name, description, type, path) values(?, ?, ?, ?, ?)";
	            $params2 = array($current_id, $filename, $this->column_fields['description'], $filetype, $upload_file_path);
	            $adb->pquery($sql2, $params2);
	            //Add relation
	            $sql3 = 'INSERT INTO vtiger_seattachmentsrel VALUES(?,?)';
	            $params3 = array($id, $current_id);
	            $adb->pquery($sql3, $params3);
	            
	            $crmid = $this->column_fields['related_to'];
	            
	            if($crmid && $current_id){
	                
	                $query = "SELECT * FROM vtiger_documentfolder inner join vtiger_crmentity on
                	vtiger_crmentity.crmid = vtiger_documentfolder.documentfolderid
                	WHERE is_default=1 and deleted=0";
	                
	                $result = $adb->pquery($query, array());
	                
	                if($adb->num_rows($result)){
	                    $doc_fol_id = $adb->query_result($result,0,'documentfolderid');
	                }
	                
	                $focus = CRMEntity::getInstance('Documents');
	                $focus->column_fields['notes_title'] = $filename;
	                $focus->column_fields['filename'] = $filename;
	                $focus->column_fields['filetype'] = $filetype;
	                $focus->column_fields['filelocationtype'] = 'I';
	                $focus->column_fields['filestatus'] = 1;
	                $focus->column_fields['assigned_user_id'] = $ownerid;
	                $focus->column_fields['related_to'] = $crmid;
	                
	                if($doc_fol_id)
	                    $focus->column_fields['doc_folder_id'] = $doc_fol_id;
	                    
                    $focus->save('Documents');
                    
                    if($current_id > 0){
                        $related_doc = 'insert into vtiger_seattachmentsrel values (?,?)';
                        $res = $adb->pquery($related_doc,array($focus->id,$current_id));
                    }
                    
                    $doc = 'insert into vtiger_senotesrel values(?,?)';
                    $res = $adb->pquery($doc,array($crmid,$focus->id));
	                
	            }
	            
	            return $current_id;
	        } else {
	            //failed to upload file
	            return false;
	        }
	}

	/**
	 * Handle saving related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	// function save_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle deleting related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function delete_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle getting related list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	/**
	 * Handle getting dependents list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }
}
?>

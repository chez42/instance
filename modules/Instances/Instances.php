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

class Instances extends Vtiger_CRMEntity {
    var $table_name = 'vtiger_instances';
    var $table_index= 'instancesid';
    
    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array('vtiger_instancescf', 'instancesid');
    
    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    var $tab_name = Array('vtiger_crmentity', 'vtiger_instances', 'vtiger_instancescf');
    
    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_instances' => 'instancesid',
        'vtiger_instancescf'=>'instancesid');
    
    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array (
        'Assigned To' => Array('crmentity','smownerid')
    );
    
    var $list_fields_name = Array (
        'Assigned To' => 'assigned_user_id',
    );
    
    // Make the field link to detail view
    var $list_link_field = '';
    
    // For Popup listview and UI type support
    var $search_fields = Array(
        'Assigned To' => Array('vtiger_crmentity','assigned_user_id'),
    );
    
    var $search_fields_name = Array (
        'Assigned To' => 'assigned_user_id',
    );
    
    // For Popup window record selection
    var $popup_fields = Array ();
    
    // For Alphabetical search
    var $def_basicsearch_col = '';
    
    // Column value to use on detail view record text display
    var $def_detailview_recname = '';
    
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('assigned_user_id');
    
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
    
    function save_module($module)
    {
        
        $this->insertIntoAttachment($this->id,$module);
        
    }
    
    
    function insertIntoAttachment($id,$module) {
        global $adb,$log;
        $log->debug("Entering into insertIntoAttachment($id,$module) method.");
        
        $imageFile = $_FILES['imagename'];
        
        $portalIcon = $_FILES['portalfavicon'];
        
        foreach($imageFile as $fileindex => $files) {
            if($files['name'] != '' && $files['size'] > 0) {
                $res = $adb->pquery('SELECT vtiger_seattachmentsrel.attachmentsid FROM vtiger_seattachmentsrel
									INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_seattachmentsrel.attachmentsid AND vtiger_crmentity.setype = ?
									WHERE vtiger_seattachmentsrel.crmid = ?',array('Instances Image File' ,$id));
                $oldAttachmentIds = array();
                for($attachItr = 0;$attachItr < $adb->num_rows($res);$attachItr++) {
                    $oldAttachmentIds[] = $adb->query_result($res,$attachItr,'attachmentsid');
                }
                if(count($oldAttachmentIds)) {
                    $adb->pquery('DELETE FROM vtiger_seattachmentsrel WHERE attachmentsid IN ('.generateQuestionMarks($oldAttachmentIds).')',$oldAttachmentIds);
                    //TODO : revisit to delete actual file and attachment entry,as we need to see the deleted file in the history when its changed
                    $adb->pquery('DELETE FROM vtiger_attachments WHERE attachmentsid IN ('.generateQuestionMarks($oldAttachmentIds).')',$oldAttachmentIds);
                    $adb->pquery('DELETE FROM vtiger_crmentity WHERE crmid IN ('.generateQuestionMarks($oldAttachmentIds).')',$oldAttachmentIds);
                }
                $files['original_name'] = vtlib_purify($_REQUEST[$fileindex.'_hidden']);
                $this->uploadAndSaveFile($id,$module,$files,'Image File');
            }
        }
        
        foreach($portalIcon as $fileindex => $files) {
            if($files['name'] != '' && $files['size'] > 0) {
                $res = $adb->pquery('SELECT vtiger_seattachmentsrel.attachmentsid FROM vtiger_seattachmentsrel
									INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_seattachmentsrel.attachmentsid AND vtiger_crmentity.setype = ?
									WHERE vtiger_seattachmentsrel.crmid = ?',array('Instances Portal Icon' ,$id));
                $oldAttachmentIds = array();
                for($attachItr = 0;$attachItr < $adb->num_rows($res);$attachItr++) {
                    $oldAttachmentIds[] = $adb->query_result($res,$attachItr,'attachmentsid');
                }
                if(count($oldAttachmentIds)) {
                    $adb->pquery('DELETE FROM vtiger_seattachmentsrel WHERE attachmentsid IN ('.generateQuestionMarks($oldAttachmentIds).')',$oldAttachmentIds);
                    //TODO : revisit to delete actual file and attachment entry,as we need to see the deleted file in the history when its changed
                    $adb->pquery('DELETE FROM vtiger_attachments WHERE attachmentsid IN ('.generateQuestionMarks($oldAttachmentIds).')',$oldAttachmentIds);
                    $adb->pquery('DELETE FROM vtiger_crmentity WHERE crmid IN ('.generateQuestionMarks($oldAttachmentIds).')',$oldAttachmentIds);
                }
                $files['original_name'] = vtlib_purify($_REQUEST[$fileindex.'_hidden']);
                $this->uploadAndSaveFile($id,$module,$files,'Portal Icon');
            }
        }
        
        $instanceLogo = $_FILES['instance_logo'];
        
        foreach($instanceLogo as $fileindex => $files) {
            if($files['name'] != '' && $files['size'] > 0) {
                $res = $adb->pquery('SELECT vtiger_seattachmentsrel.attachmentsid FROM vtiger_seattachmentsrel
									INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_seattachmentsrel.attachmentsid AND vtiger_crmentity.setype = ?
									WHERE vtiger_seattachmentsrel.crmid = ?',array('Instances Logo' ,$id));
                $oldAttachmentIds = array();
                for($attachItr = 0;$attachItr < $adb->num_rows($res);$attachItr++) {
                    $oldAttachmentIds[] = $adb->query_result($res,$attachItr,'attachmentsid');
                }
                if(count($oldAttachmentIds)) {
                    $adb->pquery('DELETE FROM vtiger_seattachmentsrel WHERE attachmentsid IN ('.generateQuestionMarks($oldAttachmentIds).')',$oldAttachmentIds);
                    //TODO : revisit to delete actual file and attachment entry,as we need to see the deleted file in the history when its changed
                    $adb->pquery('DELETE FROM vtiger_attachments WHERE attachmentsid IN ('.generateQuestionMarks($oldAttachmentIds).')',$oldAttachmentIds);
                    $adb->pquery('DELETE FROM vtiger_crmentity WHERE crmid IN ('.generateQuestionMarks($oldAttachmentIds).')',$oldAttachmentIds);
                }
                $files['original_name'] = vtlib_purify($_REQUEST[$fileindex.'_hidden']);
                $this->uploadAndSaveFile($id,$module,$files, 'Logo');
            }
        }
        
        $instanceBackground = $_FILES['instance_background'];
        
        foreach($instanceBackground as $fileindex => $files) {
            if($files['name'] != '' && $files['size'] > 0) {
                $res = $adb->pquery('SELECT vtiger_seattachmentsrel.attachmentsid FROM vtiger_seattachmentsrel
									INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_seattachmentsrel.attachmentsid AND vtiger_crmentity.setype = ?
									WHERE vtiger_seattachmentsrel.crmid = ?',array('Instances Background' ,$id));
                $oldAttachmentIds = array();
                for($attachItr = 0;$attachItr < $adb->num_rows($res);$attachItr++) {
                    $oldAttachmentIds[] = $adb->query_result($res,$attachItr,'attachmentsid');
                }
                if(count($oldAttachmentIds)) {
                    $adb->pquery('DELETE FROM vtiger_seattachmentsrel WHERE attachmentsid IN ('.generateQuestionMarks($oldAttachmentIds).')',$oldAttachmentIds);
                    //TODO : revisit to delete actual file and attachment entry,as we need to see the deleted file in the history when its changed
                    $adb->pquery('DELETE FROM vtiger_attachments WHERE attachmentsid IN ('.generateQuestionMarks($oldAttachmentIds).')',$oldAttachmentIds);
                    $adb->pquery('DELETE FROM vtiger_crmentity WHERE crmid IN ('.generateQuestionMarks($oldAttachmentIds).')',$oldAttachmentIds);
                }
                $files['original_name'] = vtlib_purify($_REQUEST[$fileindex.'_hidden']);
                $this->uploadAndSaveFile($id,$module,$files, 'Background');
            }
        }
            
        $log->debug("Exiting from insertIntoAttachment($id,$module) method.");
    }
    
    function uploadAndSaveFile($id, $module, $file_details, $attachmentType='Attachment') {
        global $log;
        $log->debug("Entering into uploadAndSaveFile($id,$module,$file_details) method.");
        
        if($attachmentType != 'Image File' && $attachmentType != 'Portal Icon' && $attachmentType != 'Logo' && $attachmentType != 'Background')
            return;
        
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
            return $current_id;
        } else {
            //failed to upload file
            return false;
        }
    }
    
}
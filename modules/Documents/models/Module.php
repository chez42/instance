<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Documents_Module_Model extends Vtiger_Module_Model {

	/**
	 * Functions tells if the module supports workflow
	 * @return boolean
	 */
	public function isWorkflowSupported() {
		return true;
	}

	/**
	 * Function to check whether the module is summary view supported
	 * @return <Boolean> - true/false
	 */
	public function isSummaryViewSupported() {
		return false;
	}
	
	/**
	 * Function returns the url which gives Documents that have Internal file upload
	 * @return string
	 */
	public function getInternalDocumentsURL() {
		return 'view=Popup&module=Documents&src_module=Emails&src_field=composeEmail';
	}

	/**
	 * Function returns list of folders
	 * @return <Array> folder list
	 */
	public static function getAllFolders() {
		$db = PearDatabase::getInstance();
		$result = $db->pquery('SELECT * FROM vtiger_attachmentsfolder ORDER BY sequence', array());

		$folderList = array();
		for($i=0; $i<$db->num_rows($result); $i++) {
			$row = $db->query_result_rowdata($result, $i);
			$folderList[] = Documents_Folder_Model::getInstanceByArray($row);
		}
		return $folderList;
	}

	/**
	 * Function to get list view query for popup window
	 * @param <String> $sourceModule Parent module
	 * @param <String> $field parent fieldname
	 * @param <Integer> $record parent id
	 * @param <String> $listQuery
	 * @return <String> Listview Query
	 */
	public function getQueryByModuleField($sourceModule, $field, $record, $listQuery) {
		if($sourceModule === 'Emails' && $field === 'composeEmail') {
			$condition = ' (( vtiger_notes.filelocationtype LIKE "%I%")) AND vtiger_notes.filename != "" AND vtiger_notes.filestatus = 1';
		} else {
			$condition = " vtiger_notes.notesid NOT IN (SELECT notesid FROM vtiger_senotesrel WHERE crmid = '$record') AND vtiger_notes.filestatus = 1";
		}
		$pos = stripos($listQuery, 'where');
		if($pos) {
			$split = preg_split('/where/i', $listQuery);
			$overRideQuery = $split[0] . ' WHERE ' . $split[1] . ' AND ' . $condition;
		} else {
			$overRideQuery = $listQuery. ' WHERE ' . $condition;
		}
		return $overRideQuery;
	}

	/**
	 * Funtion that returns fields that will be showed in the record selection popup
	 * @return <Array of fields>
	 */
	public function getPopupViewFieldsList() { 
		$popupFileds = $this->getSummaryViewFieldsList();
		$reqPopUpFields = array(/*'File Status' => 'filestatus', 
								'File Size' => 'filesize', 
								'File Location Type' => 'filelocationtype'*/); 
		foreach ($reqPopUpFields as $fieldLabel => $fieldName) {
			$fieldModel = Vtiger_Field_Model::getInstance($fieldName,$this); 
			if ($fieldModel->getPermissions('readonly')) { 
				$popupFileds[$fieldName] = $fieldModel; 
			}
		}
		return array_keys($popupFileds); 
	}

	/**
	 * Function to get the url for add folder from list view of the module
	 * @return <string> - url
	 */
	public function getAddFolderUrl() {
		return 'index.php?module='.$this->getName().'&view=AddFolder';
	}
	
	/**
	 * Function to get Alphabet Search Field 
	 */
	public function getAlphabetSearchField(){
		return 'notes_title';
	}
	
	/**
     * Function that returns related list header fields that will be showed in the Related List View
     * @return <Array> returns related fields list.
     */
	public function getRelatedListFields() {
		$relatedListFields = parent::getRelatedListFields();
		
		//Adding filestatus, filelocationtype in the related list to be used for file download
		$relatedListFields['filestatus'] = 'filestatus';
		$relatedListFields['filelocationtype'] = 'filelocationtype';
		
		return $relatedListFields;
	}
    
    /**
	* Function is used to give links in the All menu bar
	*/
	public function getQuickMenuModels() {
		if($this->isEntityModule()) {
			$moduleName = $this->getName();
            
			$createPermission = Users_Privileges_Model::isPermitted($moduleName, 'CreateView');
            if($createPermission) {
                $basicListViewLinks[] = array(
					'linktype' => 'LISTVIEW',
					'linklabel' => 'LBL_INTERNAL_DOCUMENT_TYPE',
					'linkurl' => 'javascript:Vtiger_Header_Js.getQuickCreateFormForModule("index.php?module=Documents&view=EditAjax&type=I","Documents")',
					'linkicon' => ''
				);
                $basicListViewLinks[] = array(
					'linktype' => 'LISTVIEW',
					'linklabel' => 'LBL_EXTERNAL_DOCUMENT_TYPE',
					'linkurl' => 'javascript:Vtiger_Header_Js.getQuickCreateFormForModule("index.php?module=Documents&view=EditAjax&type=E")',
					'linkicon' => ''
				);
                $basicListViewLinks[] = array(
					'linktype' => 'LISTVIEW',
					'linklabel' => 'LBL_WEBDOCUMENT_TYPE',
					'linkurl' => 'javascript:Vtiger_Header_Js.getQuickCreateFormForModule("index.php?module=Documents&view=EditAjax&type=W")',
					'linkicon' => ''
				);
            }
           
		}
		if($basicListViewLinks) {
			foreach($basicListViewLinks as $basicListViewLink) {
				if(is_array($basicListViewLink)) {
					$links[] = Vtiger_Link_Model::getInstanceFromValues($basicListViewLink);
				} else if(is_a($basicListViewLink, 'Vtiger_Link_Model')) {
					$links[] = $basicListViewLink;
				}
			}
		}
		return $links;
	}
    
    /*
     * Function to get supported utility actions for a module
     */
    function getUtilityActionsNames() {
        return array('Export');
    }

	public function getConfigureRelatedListFields() {
		$showRelatedFieldModel = $this->getHeaderAndSummaryViewFieldsList();
		$relatedListFields = array();
        $defaultFields = array();
		if(count($showRelatedFieldModel) > 0) {
			foreach ($showRelatedFieldModel as $key => $field) {
				$relatedListFields[$field->get('column')] = $field->get('name');
			}
            $defaultFields = array(
                'title' => 'notes_title',
                'filename' => 'filename'
            );
		}

		foreach($defaultFields as $columnName => $fieldName) {
			if(!array_key_exists($columnName, $relatedListFields)) {
				$relatedListFields[$columnName] = $fieldName;
			}
		}
		return $relatedListFields;
	}

	public function isFieldsDuplicateCheckAllowed() {
		return false;
	}
	
	public function buildTree(array $elements, $parentId = 0) {
	    $branch = array();
	    foreach ($elements as $element) {
	        if ($element['parent_id'] == $parentId) {
	            $children =  $this->buildTree($elements, $element['id']);
	            if($children) {
	                $element['children'] = $children;
	            }
	            $branch[] = $element;
	        }
	    }
	    
	    return $branch;
	}
	
	public static function getAllDocumentFolders(){
	    
	    $db = PearDatabase::getInstance();
	    
	    $moduleName = "DocumentFolder";
	    
	    $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
	    
	    if($currentUserModel->hasModulePermission(getTabid($moduleName))) {
	        
	        $queryGenerator = new QueryGenerator($moduleName, $currentUserModel);
	        
	        $queryGenerator->setFields( array('folder_name','id', 'parent_id') );
	        
	        $listviewController = new ListViewController($db, $currentUserModel, $queryGenerator);
	        
	        $query = $queryGenerator->getQuery();
	        
	        $query .= "  ORDER BY vtiger_documentfolder.folder_name ASC ";
	        
	        $result = $db->pquery($query,array());
	        
	        $rows = $db->num_rows($result);
	        
	        $folders = array();
	        
	        for($i=0; $i<$rows; $i++){
	            
	            $folderId = $db->query_result($result, $i, 'documentfolderid');
	            $folders[$folderId] = Vtiger_Record_Model::getInstanceById($folderId, 'DocumentFolder');
	       
	        }
	        
	        return $folders;
	    }
	    return array();
	}
	
	
	public function nested2ul($data) {
	    
	    $result = array();
	    
	    if (sizeof($data) > 0) {
	        $result[] = '<ul>';
	        foreach ($data as $entry) {
	           
	            $result[] = sprintf(
	                '<li class="Folder filterName documentFolder" data-filter-id='.$entry['id'].' id='.$entry['id'].' data-folder-name="'.$entry['text'].'" title="'.$entry['text'].'"> 
                            %s %s   
                    </li>',
	                $entry['text'],
	                $this->nested2ul($entry['children'])
	                );
	        }
	        $result[] = '</ul>';
	    }
	    
	    return implode($result);
	}
	
	
	
	
	/**
	 * Function returns Related Records
	 * @return Array
	 */
	public function getRelatedRecords($notes_id) {
	    
	    global $adb;

	    $relatedRecords = array();
	    
	    if($notes_id){
	        
	        $relatedIds = $adb->pquery("SELECT vtiger_senotesrel.crmid FROM vtiger_senotesrel
            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_senotesrel.notesid AND vtiger_crmentity.deleted = 0
            INNER JOIN vtiger_crmentity as crm ON crm.crmid = vtiger_senotesrel.crmid AND crm.deleted = 0
            WHERE vtiger_senotesrel.notesid = ?",array($notes_id));
	        
	        if($adb->num_rows($relatedIds)){
	            
	            for($r=0;$r<$adb->num_rows($relatedIds);$r++){
	                
	                $related_record = $adb->query_result($relatedIds,$r,'crmid');
	                
	                $recordPermission = Users_Privileges_Model::isPermitted(getSalesEntityType($related_record), 'DetailView', $related_record);
	                
	                if(getSalesEntityType($related_record) == 'HelpDesk')
	                    $recordPermission = HelpDesk_Record_Model::checkPermission('Detail',$related_record);
	                    
	                if($recordPermission){
    	                $recordInstance = Vtiger_Record_Model::getInstanceById($related_record);
    	                
    	                $relatedRecords[] = "<a href='".$recordInstance->getDetailViewUrl()."' title='".$recordInstance->getModuleName()."'>".
    	                Vtiger_Functions::getCRMRecordLabel($related_record)."</a>";
	                }
	            }
	            
	        }
	        
	    }
	    
	    return implode(',',$relatedRecords);
	}
	
	
	public static function getDocumentReminder(){
	    $db = PearDatabase::getInstance();
	    $currentUserModel = Users_Record_Model::getCurrentUserModel();
	    
	    $recordModels = array();
	    
	    $reminderDocsResult = "SELECT vtiger_documents_reminder_popup.reminderid, vtiger_documents_reminder_popup.recordid,
		vtiger_documents_reminder_popup.creatorid FROM vtiger_documents_reminder_popup
		INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_documents_reminder_popup.creatorid
		INNER JOIN vtiger_crmentity ON vtiger_contactdetails.contactid = vtiger_crmentity.crmid
		INNER JOIN vtiger_notes ON vtiger_notes.notesid = vtiger_documents_reminder_popup.recordid
		INNER JOIN vtiger_crmentity AS crm2 ON crm2.crmid = vtiger_notes.notesid AND crm2.deleted =0
		INNER JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
		WHERE vtiger_documents_reminder_popup.status = 0 AND vtiger_crmentity.smownerid = ? AND vtiger_crmentity.deleted =0";
	    $result = $db->pquery($reminderDocsResult, array($currentUserModel->getId()));
	    $rows = $db->num_rows($result);
	    for($i=0; $i<$rows; $i++) {
	        $creator = $db->query_result($result,$i, "creatorid");
	        $recordId = $db->query_result($result, $i, 'recordid');
	        if(isset($_SESSION['snooze_notifications']) && in_array($recordId, $_SESSION['snooze_notifications']))continue;
	        $recordModels[$creator][$recordId] = Vtiger_Record_Model::getInstanceById($recordId, 'Documents');
	    }
	    return $recordModels;
	}
	
	public function getDefaultViewName() {
	    $current_user = Users_Privileges_Model::getCurrentUserPrivilegesModel();
	    if($current_user->documents_default_view)
	        return $current_user->documents_default_view;
	    else
	       return 'List';
	}
}

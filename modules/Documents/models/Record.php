<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Documents_Record_Model extends Vtiger_Record_Model {

	/**
	 * Function to get the Display Name for the record
	 * @return <String> - Entity Display Name for the record
	 */
	function getDisplayName() {
		return Vtiger_Util_Helper::getRecordName($this->getId());
	}

	function getDownloadFileURL() {
		if ($this->get('filelocationtype') == 'I') {
			$fileDetails = $this->getFileDetails();
			return 'index.php?module='. $this->getModuleName() .'&action=DownloadFile&record='. $this->getId() .'&fileid='. $fileDetails['attachmentsid'];
		} else {
			return $this->get('filename');
		}
	}

	function checkFileIntegrityURL() {
		return "javascript:Documents_Detail_Js.checkFileIntegrity('index.php?module=".$this->getModuleName()."&action=CheckFileIntegrity&record=".$this->getId()."')";
	}

	function checkFileIntegrity() {
		$recordId = $this->get('id');
		$downloadType = $this->get('filelocationtype');
		$returnValue = false;

		if ($downloadType == 'I') {
			$fileDetails = $this->getFileDetails();
			if (!empty ($fileDetails)) {
				$filePath = $fileDetails['path'];

				$savedFile = $fileDetails['attachmentsid']."_".decode_html($this->get('filename'));

				if(fopen($filePath.$savedFile, "r")) {
					$returnValue = true;
				}
			}
		}
		return $returnValue;
	}

	function getFileDetails() {
		$db = PearDatabase::getInstance();
		$fileDetails = array();

		$result = $db->pquery("SELECT * FROM vtiger_attachments
							INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
							WHERE crmid = ?", array($this->get('id')));

		if($db->num_rows($result)) {
			$fileDetails = $db->query_result_rowdata($result);
		}
		return $fileDetails;
	}

	function downloadFile() {
		$fileDetails = $this->getFileDetails();
		$fileContent = false;

		if (!empty ($fileDetails)) {
			$filePath = $fileDetails['path'];
			$fileName = $fileDetails['name'];

			if ($this->get('filelocationtype') == 'I') {
				$fileName = html_entity_decode($fileName, ENT_QUOTES, vglobal('default_charset'));
				$savedFile = $fileDetails['attachmentsid']."_".$fileName;

				while(ob_get_level()) {
					ob_end_clean();
				}
				$fileSize = filesize($filePath.$savedFile);
				$fileSize = $fileSize + ($fileSize % 1024);

				if (fopen($filePath.$savedFile, "r")) {
					$fileContent = fread(fopen($filePath.$savedFile, "r"), $fileSize);

					header("Content-type: ".$fileDetails['type']);
					header("Pragma: public");
					header("Cache-Control: private");
					header("Content-Disposition: attachment; filename=\"$fileName\"");
					header("Content-Description: PHP Generated Data");
                    header("Content-Encoding: none");
				}
			}
		}
		echo $fileContent;
	}

	function updateFileStatus() {
		$db = PearDatabase::getInstance();

		$db->pquery("UPDATE vtiger_notes SET filestatus = 0 WHERE notesid= ?", array($this->get('id')));
	}

	function updateDownloadCount() {
		$db = PearDatabase::getInstance();
		$notesId = $this->get('id');

		$result = $db->pquery("SELECT filedownloadcount FROM vtiger_notes WHERE notesid = ?", array($notesId));
		$downloadCount = $db->query_result($result, 0, 'filedownloadcount') + 1;

		$db->pquery("UPDATE vtiger_notes SET filedownloadcount = ? WHERE notesid = ?", array($downloadCount, $notesId));
	}

	function getDownloadCountUpdateUrl() {
		return "index.php?module=Documents&action=UpdateDownloadCount&record=".$this->getId();
	}
	
	function get($key) {
		$value = parent::get($key);
		if ($key === 'notecontent') {
			return decode_html($value);
		}
		return $value;
	}
	
	function deleteReminderNotification(){
	    $db = PearDatabase::getInstance();
	    $db->pquery("delete from vtiger_documents_reminder_popup where recordid = ?", array($this->getId()));
	}
	
	public function checkPermission($view='',$record=''){
	    
	    
	    $current_user = Users_Record_Model::getCurrentUserModel();
	    $moduleName = 'Documents';
	    $recordId = $record;
	    
	    if($view == 'Edit'){
	        
	        $recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'EditView', $recordId);
	        
	        if(!$recordPermission) {
	            $recordModel = Vtiger_Record_Model::getInstanceById( $recordId);
	            $creatorId = $recordModel->get('creator');
	            
	            $ownerId = $recordModel->get('assigned_user_id');
	            
	            if($creatorId == $current_user->id  && $ownerId != $current_user->id)
	                return true;
	                
	        }else if($recordPermission){
	            
	            return true;
	            
	        }
	        
	        
	    }elseif($view == 'Detail'){
	        
	        $recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'DetailView', $recordId);
	        
	        if(!$recordPermission) {
	            
                $recordModel = Vtiger_Record_Model::getInstanceById( $recordId);
                
                $creatorId = $recordModel->get('creator');
                
                if($creatorId == $current_user->id && $ownerId != $current_user->id)
                    return true;
	                        
	                        
	        }else if($recordPermission){
	            return true;
	        }
	        
	    }elseif($view == 'Save' || $view == 'SaveAjax'){
	        
	        $recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'Save', $recordId);
	        
	        if(!$recordPermission) {
	            
	            $recordModel = Vtiger_Record_Model::getInstanceById( $recordId);
	            
	            $creatorId = $recordModel->get('creator');
	            
	            $ownerId = $recordModel->get('assigned_user_id');
	            
	            if($creatorId == $current_user->id && $ownerId != $current_user->id)
	                return true;
	                
	        }else if($recordPermission) {
	            return true;
	        }
	    }
	}
	
    
}
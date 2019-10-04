<?php

class MassDocumentUploader_Upload_Action extends Vtiger_Action_Controller {
	
	function checkPermission(Vtiger_Request $request) {
		$moduleName = "Documents";
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		$currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();

		if(!$currentUserPriviligesModel->hasModuleActionPermission($moduleModel->getId(), 'Save')) {
			throw new AppException(vtranslate($moduleName).' '.vtranslate('LBL_NOT_ACCESSIBLE'));
		}
	}

	public function process(Vtiger_Request $request) {
		
		global $adb,$upload_badext;

		foreach($_FILES as $fileindex => $fileDetails) {
			if($fileDetails['name'] != '' && $fileDetails['size'] > 0) {
				$file_saved = $this->uploadDocument($request,$fileDetails);
			}
		}
		$response = new Vtiger_Response();
		if ($file_saved) {
			$response->setResult(true);
		} else {
			$response->setResult(false);
		}
		$response->emit();	
	}
	
	public function uploadDocument(Vtiger_Request $request, $file_details) {
		
		global $adb,$current_user;

		$sourceRecord = $request->get('sourceRecord');
	    $sourceModule = $request->get('sourceModule');
	   
	    $doc_fol_id = $request->get('doc_fol_id');
	    
	        if(!$doc_fol_id || $doc_fol_id == 'xxx') {
		  	
	    	   $query = "SELECT * FROM `vtiger_documentfolder` inner join vtiger_crmentity on
	    			 	vtiger_crmentity.crmid = vtiger_documentfolder.documentfolderid 
	    			 	WHERE is_default=1 and deleted=0";
	    	  $result = $adb->pquery($query, array());
	    	if($adb->num_rows($result)){
	    		$doc_fol_id = $adb->query_result($result,0,'documentfolderid');
	    	}
		}
	    
	  
		if($sourceRecord){ 
			
			$parent_obj = CRMEntity::getInstance($sourceModule);
			
			$parent_obj->id = $sourceRecord;
			
			$parent_obj->retrieve_entity_info($sourceRecord, $sourceModule);
			
			$userid = $parent_obj->column_fields['assigned_user_id'];
			
		} else
			$userid = $current_user->id;
			
		require_once('modules/Users/Users.php');
		require_once('modules/Documents/Documents.php');

		if(empty($file_details)) return;
			
		$date_var = $adb->formatDate(date('YmdHis'), true);

		if (isset($file_details['original_name']) && $file_details['original_name'] != null) {
			$filename = $file_details['original_name'];
		} else {
			$filename = $file_details['name'];
		}
		
		$notesTitle = $filename;
		
		global $upload_badext;
		$filename = from_html(preg_replace('/\s+/', '_', $filename));
		$binFile = sanitizeUploadFileName($filename, $upload_badext);
		$filename = ltrim(basename(" ".$binFile)); //allowed filename like UTF-8 characters
							
		$filetype = $file_details['type'];
						
		$attachid = $adb->getUniqueId('vtiger_crmentity');

		$issaved = $this->saveAttachmentFile($attachid, $file_details, $userid, $sourceModule);
		
		$folderId = '1';
		
       if($issaved) {

			$document = new Documents();

			$document->column_fields['notes_title']		 = $notesTitle;
			$document->column_fields['filestatus']		 = 1;
			
			/*$document->column_fields['filename']		 = $filename;
			$document->column_fields['filesize']		 = $file_details['size'];
			$document->column_fields['filelocationtype'] = '';
			$document->column_fields['folderid']         = $folderId;
			*/
			if($doc_fol_id) {    //06.03.2018
				$document->column_fields['doc_folder_id'] = $doc_fol_id;
			}
			$document->column_fields['assigned_user_id'] = $userid;
			$document->saveentity('Documents');

			
			$query = "UPDATE vtiger_notes SET filename = ? ,filesize = ?, filetype = ? , filelocationtype = ? , filedownloadcount = ? WHERE notesid = ?";
 			$re=$adb->pquery($query, array(decode_html($filename), $file_details['size'], $filetype, 'I', '0', $document->id));
			
			$sql3 = 'insert into vtiger_seattachmentsrel(crmid, attachmentsid) values(?,?)';
			$adb->pquery($sql3, array($document->id, $attachid));
			
			// Link document to base record
			$dbQuery = "insert into vtiger_senotesrel(crmid, notesid) values ( ?, ? )";
			$dbresult = $adb->pquery($dbQuery,array($sourceRecord,$document->id));	
		
       }
	   return $issaved;
	}
	
	public function saveAttachmentFile($attachid, $file_details, $ownerid, $sourceModule) {
		global $adb;
		global $upload_badext;
		global $current_user;
		
		//$date_var = date("Y-m-d H:i:s");
		
		if (isset($file_details['original_name']) && $file_details['original_name'] != null) {
			$filename = $file_details['original_name'];
		} else {
			$filename = $file_details['name'];
		}
		
		$description = $filename;
		
		$filename = from_html(preg_replace('/\s+/', '_', $filename));		
		$binFile = sanitizeUploadFileName($filename, $upload_badext);
		$filename = ltrim(basename(" ".$binFile)); //allowed filename like UTF-8 characters
		
		$filetype = $file_details['type'];
		$filesize = $file_details['size'];
		$filetmp_name = $file_details['tmp_name'];

		//get the file path inwhich folder we want to upload the file
		$upload_file_path = decideFilePath();

		$save_file = 'true';
			
		$setype = $sourceModule . " Attachment";
		
		
		//upload the file in server
		$upload_status = move_uploaded_file($filetmp_name, $upload_file_path . $attachid . "_" . $binFile);

		$description = $filename;
		
		if( $upload_status == 'true' ) {
			
			$date_var = date("Y-m-d H:i:s");		
			$createdTime = $adb->formatDate($date_var, true);
	
			$sql1 = "insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) values(?, ?, ?, ?, ?, ?, ?)";
			$params1 = array($attachid, $current_user->id, $ownerid, $setype, $description, $createdTime, $createdTime);
			$adb->pquery($sql1, $params1);

			$sql2 = "insert into vtiger_attachments(attachmentsid, name, description, type, path) values(?, ?, ?, ?, ?)";
			$params2 = array($attachid, $filename, $description, $filetype, $upload_file_path);
			$result = $adb->pquery($sql2, $params2);
			
			return true;
		}
		return false;
	}
	
}

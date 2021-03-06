<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Emails_MassSaveAjax_View extends Vtiger_Footer_View {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('massSave');
		$this->exposeMethod('resendEmails');
	}

	public function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();

		if (!Users_Privileges_Model::isPermitted($moduleName, 'Save')) {
			throw new AppException(vtranslate($moduleName, $moduleName).' '.vtranslate('LBL_NOT_ACCESSIBLE'));
		}
	}

	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	/**
	 * Function Sends/Saves mass emails
	 * @param <Vtiger_Request> $request
	 */
	public function massSave(Vtiger_Request $request) {
		global $upload_badext;
		$adb = PearDatabase::getInstance();

		$moduleName = $request->getModule();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$recordIds = $this->getRecordsListFromRequest($request);
		$documentIds = $request->get('documentids');
        if(!$documentIds)
            $documentIds = array();
		$signature = $request->get('signature');
		// This is either SENT or SAVED
		$flag = $request->get('flag');

		$result = Vtiger_Util_Helper::transformUploadedFiles($_FILES, true);
		$_FILES = $result['file'];

		$recordId = $request->get('record');

		if(!empty($recordId)) {
			$recordModel = Vtiger_Record_Model::getInstanceById($recordId,$moduleName);
			$recordModel->set('mode', 'edit');
		}else{
			$recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
			$recordModel->set('mode', '');
		}

		$parentEmailId = $request->get('parent_id',null);
		$attachmentsWithParentEmail = array();
		if(!empty($parentEmailId) && !empty ($recordId)) {
			$parentEmailModel = Vtiger_Record_Model::getInstanceById($parentEmailId);
			$attachmentsWithParentEmail = $parentEmailModel->getAttachmentDetails();
		}
		$existingAttachments = $request->get('attachments',array());
		if(empty($recordId)) {
			if(is_array($existingAttachments)) {
				foreach ($existingAttachments as $index =>  $existingAttachInfo) {
					$existingAttachInfo['tmp_name'] = $existingAttachInfo['name'];
					$existingAttachments[$index] = $existingAttachInfo;
					if(array_key_exists('docid',$existingAttachInfo)) {
						$documentIds[] = $existingAttachInfo['docid'];
						unset($existingAttachments[$index]);
					}

				}
			}
		}else{
			//If it is edit view unset the exising attachments
			//remove the exising attachments if it is in edit view

			$attachmentsToUnlink = array();
			$documentsToUnlink = array();


			foreach($attachmentsWithParentEmail as $i => $attachInfo) {
				$found = false;
				foreach ($existingAttachments as $index =>  $existingAttachInfo) {
					if($attachInfo['fileid'] == $existingAttachInfo['fileid']) {
						$found = true;
						break;
					}
				}
				//Means attachment is deleted
				if(!$found) {
					if(array_key_exists('docid',$attachInfo)) {
						$documentsToUnlink[] = $attachInfo['docid'];
					}else{
						$attachmentsToUnlink[] = $attachInfo;
					}
				}
				unset($attachmentsWithParentEmail[$i]);
			}
			//Make the attachments as empty for edit view since all the attachments will already be there
			$existingAttachments = array();
			if(!empty($documentsToUnlink)) {
				$recordModel->deleteDocumentLink($documentsToUnlink);
			}

			if(!empty($attachmentsToUnlink)){
				$recordModel->deleteAttachment($attachmentsToUnlink);
			}

		}

		// This will be used for sending mails to each individual
		$toMailInfo = $request->get('toemailinfo');

		$to = $request->get('to');
		if(is_array($to)) {
			$to = implode(',',$to);
		}

		$content = $request->getRaw('description');
		$processedContent = Emails_Mailer_Model::getProcessedContent($content); // To remove script tags
		$mailerInstance = Emails_Mailer_Model::getInstance();
		$processedContentWithURLS = decode_html($mailerInstance->convertToValidURL($processedContent));
		$recordModel->set('description', $processedContentWithURLS);
		$recordModel->set('subject', $request->get('subject'));
		$recordModel->set('toMailNamesList',$request->get('toMailNamesList'));
		$recordModel->set('saved_toid', $to);
		$recordModel->set('ccmail', $request->get('cc'));
		$recordModel->set('bccmail', $request->get('bcc'));
		$recordModel->set('assigned_user_id', $currentUserModel->getId());
		$recordModel->set('email_flag', $flag);
		$recordModel->set('documentids', $documentIds);
		$recordModel->set('signature',$signature);

		$recordModel->set('toemailinfo', $toMailInfo);
		foreach($toMailInfo as $recordId=>$emailValueList) {
			if($recordModel->getEntityType($recordId) == 'Users'){
				$parentIds .= $recordId.'@-1|';
			}else{
				$parentIds .= $recordId.'@1|';
			}
		}
		$recordModel->set('parent_id', $parentIds);

		//save_module still depends on the $_REQUEST, need to clean it up
		$_REQUEST['parent_id'] = $parentIds;

		if($request->get("from_serveremailid")){
		    $recordModel->set('from_serveremailid', $request->get("from_serveremailid"));
		}
		
		$success = false;
		$viewer = $this->getViewer($request);
		if ($recordModel->checkUploadSize($documentIds)) {
			// Fix content format acceptable to be preserved in table.
			$decodedHtmlDescriptionToSend = $recordModel->get('description');
			$recordModel->set('description', to_html($decodedHtmlDescriptionToSend));
			$recordModel->save();

			// Restore content to be dispatched through HTML mailer.
			$recordModel->set('description', $decodedHtmlDescriptionToSend);

			// To add entry in ModTracker for email relation
			$emailRecordId = $recordModel->getId();
			foreach ($toMailInfo as $recordId => $emailValueList) {
				$relatedModule = $recordModel->getEntityType($recordId);
				if (!empty($relatedModule) && $relatedModule != 'Users') {
					$relatedModuleModel = Vtiger_Module_Model::getInstance($relatedModule);
					$relationModel = Vtiger_Relation_Model::getInstance($relatedModuleModel, $recordModel->getModule());
					if ($relationModel) {
						$relationModel->addRelation($recordId, $emailRecordId);
					}
				}
			}
			// End

			//To Handle existing attachments
			$current_user = Users_Record_Model::getCurrentUserModel();
			$ownerId = $recordModel->get('assigned_user_id');
			$date_var = date("Y-m-d H:i:s");
			if(is_array($existingAttachments)) {
				foreach ($existingAttachments as $index =>  $existingAttachInfo) {
					$file_name = $existingAttachInfo['attachment'];
					$path = $existingAttachInfo['path'];
					$fileId = $existingAttachInfo['fileid'];

					$oldFileName = $file_name;
					//SEND PDF mail will not be having file id
					if(!empty ($fileId)) {
						$oldFileName = $existingAttachInfo['fileid'].'_'.$file_name;
					}
					$oldFilePath = $path.'/'.$oldFileName;

					$binFile = sanitizeUploadFileName($file_name, $upload_badext);

					$current_id = $adb->getUniqueID("vtiger_crmentity");

					$filename = ltrim(basename(" " . $binFile)); //allowed filename like UTF-8 characters
					$filetype = $existingAttachInfo['type'];
					$filesize = $existingAttachInfo['size'];

					//get the file path inwhich folder we want to upload the file
					$upload_file_path = decideFilePath();
					$newFilePath = $upload_file_path . $current_id . "_" . $binFile;

					copy($oldFilePath, $newFilePath);

					if($request->get('source_module') == 'Reports')
					    unlink($oldFilePath);
					
					$sql1 = "insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) values(?, ?, ?, ?, ?, ?, ?)";
					$params1 = array($current_id, $current_user->getId(), $ownerId, $moduleName . " Attachment", $recordModel->get('description'), $adb->formatDate($date_var, true), $adb->formatDate($date_var, true));
					$adb->pquery($sql1, $params1);

					$sql2 = "insert into vtiger_attachments(attachmentsid, name, description, type, path) values(?, ?, ?, ?, ?)";
					$params2 = array($current_id, $filename, $recordModel->get('description'), $filetype, $upload_file_path);
					$result = $adb->pquery($sql2, $params2);

					$sql3 = 'insert into vtiger_seattachmentsrel values(?,?)';
					$adb->pquery($sql3, array($recordModel->getId(), $current_id));
				}
			}
			$success = true;
			if($flag == 'SENT') {
			    if(count($recordIds) > 20){
			        
			        $selectedFields = is_array($request->get('selectedfields')) ? json_encode($request->get('selectedfields')) : $request->get('selectedfields');
			        $cvId = $request->get('viewname');
			        $selectedIds = is_array($request->get('selected_ids')) ? json_encode($request->get('selected_ids')) : $request->get('selected_ids');
			        $excludedIds = is_array($request->get('excluded_ids')) ? json_encode($request->get('excluded_ids')) : $request->get('excluded_ids');
			        $searchParams = is_array($request->get('search_params')) ? json_encode($request->get('search_params')) : $request->get('search_params');
			        $otherData = json_encode(array(
			             'searchKey' => $request->get('search_key'),
			             'searchValue' => $request->get('search_value'),
			             'operator' => $request->get('operator')
			        ));
			        $adb->pquery("INSERT INTO vtiger_email_queue(emailid, from_serveremailid, selected_fields, cvid, serch_params, 
                    selected_ids, excluded_ids, other_data, source_module) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
			            array($recordModel->getId(), $request->get("from_serveremailid"), $selectedFields, $cvId, 
			                $searchParams, $selectedIds, $excludedIds, $otherData, $request->get('source_module')));
			        
			        $viewer->assign('SCHEDULED', true);
			        
			    }else{
			        
    				$status = $recordModel->send();
    				if ($status === true) {
    					// This is needed to set vtiger_email_track table as it is used in email reporting
    					$recordModel->setAccessCountValue();
    				} else {
    					$success = false;
    					$message = $status;
    				}
    				
			    }
			}

		} else {
			$message = vtranslate('LBL_MAX_UPLOAD_SIZE', $moduleName).' '.vtranslate('LBL_EXCEEDED', $moduleName);
		}
		$viewer->assign('SUCCESS', $success);
		$viewer->assign('MESSAGE', $message);
		$viewer->assign('FLAG', $flag);
		$viewer->assign('MODULE',$moduleName);
		$loadRelatedList = $request->get('related_load');
		if(!empty($loadRelatedList)){
			$viewer->assign('RELATED_LOAD',true);
		}
		$viewer->view('SendEmailResult.tpl', $moduleName);
	}

	/**
	 * Function returns the record Ids selected in the current filter
	 * @param Vtiger_Request $request
	 * @return integer
	 */
	public function getRecordsListFromRequest(Vtiger_Request $request) {
		$cvId = $request->get('viewname');
		$selectedIds = $request->get('selected_ids');
		$excludedIds = $request->get('excluded_ids');

		if(!empty($selectedIds) && $selectedIds != 'all') {
			if(!empty($selectedIds) && count($selectedIds) > 0) {
				return $selectedIds;
			}
		}

		if($selectedIds == 'all'){
			$sourceRecord = $request->get('sourceRecord');
			$sourceModule = $request->get('sourceModule');
			if ($sourceRecord && $sourceModule) {
				$sourceRecordModel = Vtiger_Record_Model::getInstanceById($sourceRecord, $sourceModule);
				return $sourceRecordModel->getSelectedIdsList($request->get('parentModule'), $excludedIds);
			}

			$customViewModel = CustomView_Record_Model::getInstanceById($cvId);
			if($customViewModel) {
				$searchKey = $request->get('search_key');
				$searchValue = $request->get('search_value');
				$operator = $request->get('operator');
				if(!empty($operator)) {
					$customViewModel->set('operator', $operator);
					$customViewModel->set('search_key', $searchKey);
					$customViewModel->set('search_value', $searchValue);
				}
				
				$customViewModel->set('search_params',$request->get('search_params'));
				
				return $customViewModel->getRecordIds($excludedIds);
			}
		}
		return array();
	}

	public function validateRequest(Vtiger_Request $request) {
		$request->validateWriteAccess();
	}
	
	/**
	 * Function Sends/Saves mass emails
	 * @param <Vtiger_Request> $request
	 */
	public function resendEmails(Vtiger_Request $request) {
	    global $upload_badext;
	    $adb = PearDatabase::getInstance();
	    
	    $moduleName = $request->getModule();
	    $currentUserModel = Users_Record_Model::getCurrentUserModel();
	    $recordIds = $this->getRecordsListFromRequest($request);
	    $viewer = $this->getViewer($request);
	    
	    foreach($recordIds as $recordId){
    	    
    	    $documentIds = array();
    	    
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId,$moduleName);
            $recordModel->set('mode', 'edit');
    	   
    	    $attachmentsWithParentEmail = array();
    	    if(!empty ($recordId)) {
    	        $parentEmailModel = Vtiger_Record_Model::getInstanceById($recordId);
    	        $attachmentsWithParentEmail = $parentEmailModel->getAttachmentDetails();
    	    }
    	    
	        //If it is edit view unset the exising attachments
	        //remove the exising attachments if it is in edit view
	        
	        $attachmentsToUnlink = array();
	        $documentsToUnlink = array();
	        
	        
	        foreach($attachmentsWithParentEmail as $i => $attachInfo) {
                $documentIds[] =  $attachInfo['fileid'] ;
	        }
	       
	        
	        $to = Zend_Json::decode(html_entity_decode($recordModel->get('saved_toid')));
    	    if(is_array($to)) {
    	        $to = implode(',',$to);
    	    }
    	    $flag = $recordModel->get('email_flag');
	        $content = $recordModel->get('description');
    	    $processedContent = Emails_Mailer_Model::getProcessedContent($content); // To remove script tags
    	    $mailerInstance = Emails_Mailer_Model::getInstance();
    	    $processedContentWithURLS = decode_html($mailerInstance->convertToValidURL($processedContent));
    	    $recordModel->set('description', $processedContentWithURLS);
    	    $recordModel->set('subject', $recordModel->get('subject'));
    	    $recordModel->set('toMailNamesList',$recordModel->get('toMailNamesList'));
    	    $recordModel->set('saved_toid', $to);
    	    $recordModel->set('ccmail', $recordModel->get('cc'));
    	    $recordModel->set('bccmail', $recordModel->get('bcc'));
    	    $recordModel->set('assigned_user_id', $currentUserModel->getId());
    	    $recordModel->set('email_flag', $flag);
    	    $recordModel->set('documentids', $documentIds);
    	    
    	    if($request->get("from_serveremailid")){
    	        $recordModel->set('from_serveremailid', $request->get("from_serveremailid"));
    	    }
    	    
    	    $success = false;
    	   
    	    if ($recordModel->checkUploadSize($documentIds)) {
    	        // Fix content format acceptable to be preserved in table.
    	        $decodedHtmlDescriptionToSend = $recordModel->get('description');
    	        $recordModel->set('description', to_html($decodedHtmlDescriptionToSend));
    	        $recordModel->save();
    	        
    	        // Restore content to be dispatched through HTML mailer.
    	        $recordModel->set('description', $decodedHtmlDescriptionToSend);
    	        
    	        // To add entry in ModTracker for email relation
    	        $emailRecordId = $recordModel->getId();
    	        
    	        $success = true;
    	        if($flag == 'SENT') {
    	            $status = $recordModel->send();
    	            if ($status === true) {
    	                // This is needed to set vtiger_email_track table as it is used in email reporting
    	                $recordModel->setAccessCountValue();
    	            } else {
    	                $success = false;
    	                $message = $status;
    	            }
    	        }
    	        
    	    } else {
    	        $message = vtranslate('LBL_MAX_UPLOAD_SIZE', $moduleName).' '.vtranslate('LBL_EXCEEDED', $moduleName);
    	    }
    	    $viewer->assign('SUCCESS', $success);
    	    $viewer->assign('MESSAGE', $message);
    	    $viewer->assign('FLAG', $flag);
    	    $viewer->assign('MODULE',$moduleName);
    	    
	    }
	    $viewer->view('SendEmailResult.tpl', $moduleName);
	}
}

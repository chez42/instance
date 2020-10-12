<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Emails_DownloadFile_Action extends Vtiger_Action_Controller {

    public function __construct() {
        parent::__construct();
        $this->exposeMethod('saveAsDocument');
        $this->exposeMethod('previewDocument');
    }
    
	public function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();

		if(!Users_Privileges_Model::isPermitted($moduleName, 'DetailView', $request->get('record'))) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED', $moduleName));
		}
	}

	public function process(Vtiger_Request $request) {
	    
	    $mode = $request->getMode();
	    if(!empty($mode)) {
	        echo $this->invokeExposedMethod($mode, $request);
	        return;
	    }
	    
        $db = PearDatabase::getInstance();

        $attachmentId = $request->get('attachment_id');
        $query = "SELECT * FROM vtiger_attachments WHERE attachmentsid = ?" ;
        $result = $db->pquery($query, array($attachmentId));

        if($db->num_rows($result) == 1)
        {
            $row = $db->fetchByAssoc($result, 0);
            $fileType = $row["type"];
            $name = $row["name"];
            $filepath = $row["path"];
            $name = decode_html($name);
            $saved_filename = $attachmentId."_".$name;
            $disk_file_size = filesize($filepath.$saved_filename);
            $filesize = $disk_file_size + ($disk_file_size % 1024);
            $fileContent = fread(fopen($filepath.$saved_filename, "r"), $filesize);

            header("Content-type: $fileType");
            header("Pragma: public");
            header("Cache-Control: private");
            header("Content-Disposition: attachment; filename=$name");
            header("Content-Description: PHP Generated Data");
            echo $fileContent;
        }
    }
    
    public function saveAsDocument(Vtiger_Request $request){
        
        global $current_user;
        
        $docId = '';
        $db = PearDatabase::getInstance();
        
        $user_id = $current_user->id;
        $attachmentId = $request->get('attid');
       
        $docQuery = $db->pquery("SELECT * FROM vtiger_notes
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_notes.notesid
        INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.crmid = vtiger_notes.notesid
        WHERE vtiger_crmentity.deleted = 0 AND vtiger_seattachmentsrel.attachmentsid = ?",array($attachmentId));
        if(!$db->num_rows($docQuery)){
            $query = "SELECT * FROM vtiger_attachments WHERE attachmentsid = ?" ;
            $result = $db->pquery($query, array($attachmentId));
            
            if($db->num_rows($result) == 1)
            {
                $row = $db->fetchByAssoc($result, 0);
                $fileType = $row["type"];
                $name = $row["name"];
                $filepath = $row["path"];
                $name = decode_html($name);
                $saved_filename = $attachmentId."_".$name;
                $disk_file_size = filesize($filepath.$saved_filename);
                $filesize = $disk_file_size + ($disk_file_size % 1024);
                
                $query = "SELECT * FROM vtiger_documentfolder inner join vtiger_crmentity on
            	vtiger_crmentity.crmid = vtiger_documentfolder.documentfolderid
            	WHERE  vtiger_documentfolder.is_default=1 AND vtiger_crmentity.deleted = 0
                AND vtiger_crmentity.smownerid = ?";
                
                $result = $db->pquery($query, array($user_id));
                
                if(!$db->num_rows($result)){
                    $query = "SELECT * FROM vtiger_documentfolder
                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_documentfolder.documentfolderid
                    WHERE  vtiger_documentfolder.default_for_all_users=1 AND vtiger_crmentity.deleted = 0";
                    
                    $result = $db->pquery($query, array());
                }
                if(!$db->num_rows($result)){
                    $query = "SELECT * FROM vtiger_documentfolder
                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_documentfolder.documentfolderid
                    WHERE vtiger_crmentity.deleted = 0 AND vtiger_documentfolder.folder_name = BINARY 'Default'";
                    
                    $result = $db->pquery($query, array());
                }
                
                if($input_array['doc_folder_id']){
                    $doc_fol_id = $input_array['doc_folder_id'];
                }elseif($db->num_rows($result)){
                    $doc_fol_id = $db->query_result($result,0,'documentfolderid');
                }
                
                $focus = CRMEntity::getInstance('Documents');
                $focus->column_fields['notes_title'] = $name;
                $focus->column_fields['filename'] = $name;
                $focus->column_fields['filetype'] = $fileType;
                $focus->column_fields['filesize'] = $filesize;
                $focus->column_fields['filelocationtype'] = 'I';
                $focus->column_fields['filedownloadcount']= 0;
                $focus->column_fields['filestatus'] = 1;
                
                if($doc_fol_id)
                    $focus->column_fields['doc_folder_id'] = $doc_fol_id;
                    
                $focus->save('Documents');
                if($focus->id){
                    $related_doc = 'insert into vtiger_seattachmentsrel values (?,?)';
                    $res = $db->pquery($related_doc,array($focus->id, $attachmentId));
                    $docId = $focus->id;
                }
            }
        }else{
            $docId = $db->query_result($docQuery, 0, 'notesid');
        }
        
        if($docId){
            $result = array('success' => true);
        }else{
            $result = array('success' => false);
        }
        
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
    
    public function previewDocument(Vtiger_Request $request){
        
        global $adb;
        $moduleName = $request->getModule();
        $recordId = $request->get('attid');
        
        $basicFileTypes = array('txt','csv','ics');
        $imageFileTypes = array('image/gif','image/png','image/jpeg');
        //supported by video js
        $videoFileTypes = array('video/mp4','video/ogg','audio/ogg','video/webm');
        $audioFileTypes = array('audio/mp3','audio/mpeg','audio/wav');
        //supported by viewer js
        $opendocumentFileTypes = array('odt','ods','odp','fodt');
        
        $result = $adb->pquery("SELECT * FROM vtiger_attachments
				WHERE attachmentsid = ?", array($recordId));
        $fileDetails = array();
        if($adb->num_rows($result)) {
            $fileDetails = $adb->query_result_rowdata($result);
        }
        
        $fileContent = false;
        if (!empty ($fileDetails)) {
            $filePath = $fileDetails['path'];
            $fileName = $fileDetails['name'];
            
            $fileName = html_entity_decode($fileName, ENT_QUOTES, vglobal('default_charset'));
            $savedFile = $fileDetails['attachmentsid']."_".$fileName;
            
            $fileSize = filesize($filePath.$savedFile);
            $fileSize = $fileSize + ($fileSize % 1024);
            
            if (fopen($filePath.$savedFile, "r")) {
                $fileContent = fread(fopen($filePath.$savedFile, "r"), $fileSize);
            }
        }
        
        $path = $fileDetails['path'].$fileDetails['attachmentsid'].'_'.$fileDetails['name'];
        $type = $fileDetails['type'];
        $contents = $fileContent;
        $filename = $fileDetails['name'];
        $parts = explode('.',$filename);
       
        $downloadUrl = 'index.php?module=Emails&action=DownloadFile&attachment_id='.$fileDetails['attachmentsid'];
        
        //support for plain/text document
        $extn = 'txt';
        if(count($parts) > 1){
            $extn = end($parts);
        }
        global $vtiger_current_version, $vtiger_display_version, $onlyV7Instance;
        $viewer = new Vtiger_Viewer();
        $viewer->assign('APPTITLE', getTranslatedString('APPTITLE'));
        $viewer->assign('VTIGER_VERSION', $vtiger_current_version);
        $viewer->assign('VTIGER_DISPLAY_VERSION', $vtiger_display_version);
        $viewer->assign('ONLY_V7_INSTANCE', $onlyV7Instance);
        
        $viewer->assign('MODULE_NAME',$moduleName);
        if(in_array($extn,$basicFileTypes))
            $viewer->assign('BASIC_FILE_TYPE','yes');
        else if(in_array($type,$videoFileTypes))
            $viewer->assign('VIDEO_FILE_TYPE','yes');
        else if(in_array($type,$imageFileTypes))
            $viewer->assign('IMAGE_FILE_TYPE','yes');
        else if(in_array($type,$audioFileTypes))
            $viewer->assign('AUDIO_FILE_TYPE','yes');
        else if (in_array($extn, $opendocumentFileTypes)) {
            $viewer->assign('OPENDOCUMENT_FILE_TYPE', 'yes');
            $downloadUrl .= "&type=$extn";
        } else if ($extn == 'pdf') {
            $viewer->assign('PDF_FILE_TYPE', 'yes');
        } else {
            $viewer->assign('FILE_PREVIEW_NOT_SUPPORTED','yes');
        }
        
        $viewer->assign('DOWNLOAD_URL',$downloadUrl);
        $viewer->assign('FILE_PATH',$path);
        $viewer->assign('FILE_NAME',$filename);
        $viewer->assign('FILE_EXTN',$extn);
        $viewer->assign('FILE_TYPE',$type);
        $viewer->assign('FILE_CONTENTS',$contents);
        global $site_URL;
        $viewer->assign('SITE_URL',$site_URL);
        
        echo $viewer->view('FilePreview.tpl','Documents',true);
    }
}

?>

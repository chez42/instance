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
}

?>

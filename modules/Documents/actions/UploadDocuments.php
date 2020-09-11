<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Documents_UploadDocuments_Action extends Vtiger_Action_Controller {
    
    
    public function checkPermission(Vtiger_Request $request) {
        $moduleName = $request->get('module');
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        
        if(!Users_Privileges_Model::isPermitted($moduleName, 'Save')) {
                
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
                
        }
    }
    
    public function process(Vtiger_Request $request) {
        $moduleName = $request->get('module');
        global $adb, $upload_badext, $current_user;
        
        $folderId = $request->get('doc_folder_id');
        
        if(!$folderId){
            
            $folQuery = $adb->pquery("SELECT * FROM vtiger_users WHERE id = ?",array($current_user->id));
            if($adb->num_rows($folQuery)){
                $folderId = $adb->query_result($folQuery, 0, 'default_documents_folder_id');
            }
            
            if(!$folderId){
                $adminFolQuery = $adb->pquery("SELECT * FROM vtiger_users WHERE user_name = ?",array('admin'));
                if($adb->num_rows($adminFolQuery)){
                    $folderId = $adb->query_result($adminFolQuery, 0, 'default_documents_folder_id');
                }
            }
            
            if(!$folderId){
                $result = $adb->pquery("SELECT * FROM vtiger_documentfolder
                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_documentfolder.documentfolderid
                    WHERE vtiger_crmentity.deleted = 0 AND
    				vtiger_documentfolder.folder_name = BINARY 'Default'", array());
                if($adb->num_rows($result)) {
                    $folderId = $adb->query_result($result,0,'documentfolderid');
                }
            }
            
        }
        
       
        $_FILES = Vtiger_Util_Helper::transformUploadedFiles($_FILES, true);
        
        foreach($_FILES['files'] as $file){
            if(!$file['error']){
                
                $filename = $file['name'];
                $filename = from_html(preg_replace('/\s+/', '_', $filename));
                $filetype = $file['type'];
                $filesize = $file['size'];
                $filelocationtype = 'I';
                $binFile = sanitizeUploadFileName($filename, $upload_badext);
                $filename = ltrim(basename(" ".$binFile)); 
                
            }
        }
        
        $docModel = CRMEntity::getInstance($moduleName);
        $docModel->column_fields['notes_title'] = $filename;
        $docModel->column_fields['filename'] = $filename;
        $docModel->column_fields['filesize'] = $filesize;
        $docModel->column_fields['filetype'] = $filetype;
        $docModel->column_fields['filedownloadcount'] = 0;
        $docModel->column_fields['related_to'] = $request->get('record');
        $docModel->column_fields['filelocationtype'] = $filelocationtype;
        $docModel->column_fields['doc_folder_id'] = $folderId;
        $docModel->column_fields['filestatus'] = 1;
        $docModel->save('Documents');
        
        if($docModel->id){
            $docModel->insertintonotesrel($request->get('record'),$docModel->id);
            $this->insertIntoAttachment($docModel->id,'Documents');
        }
       
        $result = array('success'=>true, 'message'=>vtranslate('Document Saved Successfully', $moduleName));
        
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
        
        
    }
    
    function insertIntoAttachment($id,$module){
        global $log, $adb;
        $log->debug("Entering into insertIntoAttachment($id,$module) method.");
        
        $file_saved = false;
        
        foreach($_FILES['files'] as $fileindex => $files){
           
            if($files['name'] != '' && $files['size'] > 0)
            {
                $files['original_name'] = vtlib_purify($files['name']);
                $model = CRMEntity::getInstance($module);
                $file_saved = $model->uploadAndSaveFile($id,$module,$files);
            }
        }
        
        $log->debug("Exiting from insertIntoAttachment($id,$module) method.");
    }
    
   
}

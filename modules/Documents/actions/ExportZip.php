<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Documents_ExportZip_Action extends Vtiger_Mass_Action {
    
    public function checkPermission(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        
        $currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        if(!$currentUserPriviligesModel->hasModuleActionPermission($moduleModel->getId(), 'Export')) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }
    }
    
    public function process(Vtiger_Request $request) {
        
        $moduleName = $request->getModule();
        
        $documentIdsList = $this->getRecordsListFromRequest($request);
        
        global $adb;
        
        $notes = $adb->pquery("SELECT vtiger_attachments.* FROM vtiger_notes
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_notes.notesid
        INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.crmid = vtiger_notes.notesid
        INNER JOIN vtiger_attachments ON vtiger_attachments.attachmentsid = vtiger_seattachmentsrel.attachmentsid
        WHERE vtiger_crmentity.deleted = 0 AND vtiger_notes.notesid IN (".implode(',',$documentIdsList).")");
        
        $files = array();
        
        if($adb->num_rows($notes)){
            for($i=0;$i<$adb->num_rows($notes);$i++){
                $path = $adb->query_result($notes, $i, 'path');
                $fileId = $adb->query_result($notes, $i, 'attachmentsid');
                $fileName = $adb->query_result($notes, $i, 'name');
                $files[] =  $path.$fileId.'_'.$fileName;
            }
        }
        
        $zipname = 'storage/'.$request->get('filename').'.zip';
        $zip = new ZipArchive;
        $zip->open($zipname, ZipArchive::CREATE);
        foreach ($files as $i=>$file) {
            if(filetype($file) == 'file') {
                if(file_exists($file)) {
                    $zip->addFile( $file, pathinfo( $file, PATHINFO_BASENAME ) );
                }
            }
        }
        $zip->close();
        while(ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename='.basename($zipname));
        readfile($zipname);
        unlink($zipname);
    }
    
    public function validateRequest(Vtiger_Request $request) {
        return true;
    }
}
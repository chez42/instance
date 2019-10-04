<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vtiger_ExternalDownloadLink_Action extends Vtiger_Action_Controller {
    
    function loginRequired() {
        return false;
    }
    
    public function process(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $this->downloadFile($request->get('record'));
        
    }
    
    public function getFileDetails($attachmentId = false) {
        $db = PearDatabase::getInstance();
        $fileDetails = array();
        $query = "SELECT * FROM vtiger_attachments
				INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
				WHERE crmid = ? ";
        $params = array($attachmentId);
       
        $result = $db->pquery($query, $params);
        
        while($row = $db->fetch_array($result)){
            if(!empty($row)){
                $fileDetails[] = $row;
            }
        }
        return $fileDetails;
    }
    
    public function downloadFile($attachmentId = false) {
        $attachments = $this->getFileDetails($attachmentId);
        if(is_array($attachments[0])) {
            $fileDetails = $attachments[0];
        } else {
            $fileDetails = $attachments;
        }
        $fileContent = false;
        if (!empty ($fileDetails)) {
            $filePath = $fileDetails['path'];
            $fileName = $fileDetails['name'];
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
        echo $fileContent;
    }
}
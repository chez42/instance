<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Documents_DocumentViewer_Handler {
    
    public function documentview($data){
        global $adb,$site_URL;
        $request = new Vtiger_Request($data);
        $viewer = Vtiger_Viewer::getInstance();
        $moduleName = 'Documents';
        $docId = $request->get('documentId');
        $recordId = $request->get('documentId');
        
        $basicFileTypes = array('txt','ics');
        $imageFileTypes = array('image/gif','image/png','image/jpeg');
        $videoFileTypes = array('video/mp4','video/ogg','audio/ogg','video/webm');
        $audioFileTypes = array('audio/mp3','audio/mpeg','audio/wav');
        $opendocumentFileTypes = array('odt','ods','odp','fodt');
        
        $fileDetail = Vtiger_ExternalDownloadLink_Action::getFileDetails($recordId);
        foreach($fileDetail as $fileData){
            $fileDetails = $fileData;
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
        
        $downloadUrl =  "index.php?module=Vtiger&action=ExternalDownloadLink&record=".$recordId;
        
        $extn = 'txt';
        if(count($parts) > 1){
            $extn = end($parts);
        }
        
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
        
        $viewer->view('DocumentView.tpl',$moduleName);
    }
    
}
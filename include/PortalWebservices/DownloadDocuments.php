<?php
function vtws_downloaddocuments($element,$user){
    
    global $adb,$site_URL;
    
    $element = json_decode($element,true);
   
    $moduleName = 'Documents';
    
    $recordId = $element['file_id'];
   
    $data = array();
    
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
            $fileContent = base64_encode(fread(fopen($filePath.$savedFile, "r"), $fileSize));
        }
    }
    
    $data['type'] = $fileDetails['type'];
    $data['contents'] = $fileContent;
    $data['filename'] = $fileDetails['name'];
    $data['parts'] = explode('.',$data['filename']);
    
    $data['downloadUrl'] =  "index.php?module=Vtiger&action=ExternalDownloadLink&record=".$recordId;
    $data['site_URL'] = $site_URL;
    
    return $data;
    
    
}
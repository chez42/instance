<?php

function vtws_get_ticket_documents($element,$user){
    
    global $adb,$site_URL;
    
    $element = json_decode($element,true);
    
    $doc_data = array();
   
    $docs = $adb->pquery("SELECT vtiger_notes.*, vtiger_crmentity.createdtime FROM vtiger_notes
    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_notes.notesid
    INNER JOIN vtiger_senotesrel ON vtiger_senotesrel.notesid = vtiger_notes.notesid
    WHERE vtiger_crmentity.deleted = 0 AND vtiger_senotesrel.crmid = ? ",
        array($element['ticket_id']));
    
    if($adb->num_rows($docs)){
        
        for($j = 0; $j < $adb->num_rows($docs); $j++){
            
            $file = explode('/',$adb->query_result($docs, $j, 'filetype'));
            
            if($file[0] == 'image'){
                $icon = 'img.jpg';
                $fileType = 'image File';
            }else if($file[0] == 'video'){
                $icon = 'video.jpg';
                $fileType = 'video File';
            }else if($file[0] == 'text'){
                $icon = 'docx.jpg';
                $fileType = 'text File';
            }else if($file[1] == 'pdf'){
                $icon = 'pdf.jpg';
                $fileType = 'pdf File';
            }else if($file[1] == 'zip'){
                $icon = 'zip.jpg';
                $fileType = 'zip File';
            }else if(strpos($file[1], 'ms')!== false || strpos($file[1], 'vnd') !== false){
                $icon = 'office.jpg';
                $fileType = 'office File';
            }else {
                $icon = 'txt.jpg';
                $fileType = 'doc File';
                if($loctype == 'E')
                    $fileType = 'external File';
            }
            $doc_data[]= array(
                "notesid" => $adb->query_result($docs, $j, 'notesid'),
                "docname" => $adb->query_result($docs, $j, 'filename'),
                //"url" => $site_URL.'/index.php?module=Vtiger&action=ExternalDownloadLink&record='.$adb->query_result($docs, $j, 'notesid'),
                "icon" => $icon,
                "type" => $fileType,
                "filelocationtype" => $adb->query_result($docs, $j, 'filelocationtype'),
                "title" => $adb->query_result($docs, $j, 'title'),
                "createdtime" => $adb->query_result($docs, $j, 'createdtime')
            );
            
        }
    }
    
    return $doc_data;
}
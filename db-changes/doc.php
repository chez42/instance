<?php

chdir('../');

include("includes/main/WebUI.php");

global $adb;

$docQuery = $adb->pquery("SELECT vtiger_notes.notesid, vtiger_attachments.name, vtiger_notes.title, 
vtiger_notes.note_no, vtiger_attachments.path, vtiger_attachments.attachmentsid, createdtime FROM vtiger_notes 
INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_notes.notesid
INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.crmid = vtiger_notes.notesid
INNER JOIN vtiger_attachments ON vtiger_attachments.attachmentsid = vtiger_seattachmentsrel.attachmentsid
WHERE vtiger_crmentity.deleted = 0");
$entries = array();
if($adb->num_rows($docQuery)){
    for($d=0;$d<$adb->num_rows($docQuery);$d++){
        $path = $adb->query_result($docQuery, $d, 'path');
        $orgName = $adb->query_result($docQuery, $d, 'name');
        
        $attachmentsid = $adb->query_result($docQuery, $d, 'attachmentsid');
        
        $fileName = html_entity_decode($orgName, ENT_QUOTES, vglobal('default_charset'));
        $savedFile = $attachmentsid."_".$fileName;
        if(!file_exists($path.$savedFile)){
            $entries[] = array(
                'no' => $adb->query_result($docQuery, $d, 'note_no'),
                'title' => $adb->query_result($docQuery, $d, 'title'),
                'name' => $orgName,
                'id'  => $adb->query_result($docQuery, $d, 'notesid'),
				'createdtime'  => $adb->query_result($docQuery, $d, 'createdtime'),
			);
        }
        
    }
}

$headerFields = array('Document No', 'Title', 'File Name', 'Record ID', "Created Time");

$headers = array_map('decode_html', $headerFields);

header("Content-Disposition:attachment;filename=docFiles.csv");
header("Content-Type:text/csv;charset=UTF-8");

$header = implode("\", \"", $headers);
$header = "\"" .$header;
$header .= "\"\r\n";
echo str_replace('"','',$header);

foreach($entries as $row) {
    foreach ($row as $key => $value) {
        $row[$key] = str_replace('"', '""', $value);
    }
    $line = implode("\",\"",$row);
    $line = "\"" .$line;
    $line .= "\"\r\n";
    echo str_replace('"','',$line);
}

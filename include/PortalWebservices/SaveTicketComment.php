<?php

function vtws_save_ticket_comment($element,$user){
    
    global $adb,$site_URL;

    $element = json_decode($element,true);
    
    $result = array();
    
    $modComments = CRMEntity::getInstance('ModComments');
    
    $filename = $element['filename'];
    
    if($filename != '') {
        
        $filetype = $element['filetype'];
        $filesize = $element['filesize'];
        $filecontents = $element['filecontents'];
        
        if($filesize > 0 && $filecontents != ''){
            
            $save_doc = true;
            
            $upload_filepath = decideFilePath();
            
            $attachmentid = $adb->getUniqueID("vtiger_crmentity");
            
            $filename = sanitizeUploadFileName($filename, $upload_badext);
            $new_filename = $attachmentid.'_'.$filename;
            
            $data = base64_decode($filecontents);
            $description = 'CustomerPortal ModComments Attachment';
            
            $handle = @fopen($upload_filepath.$new_filename,'w');
            fputs($handle, $data);
            fclose($handle);
            
            $date_var = $adb->formatDate(date('Y-m-d H:i:s'), true);
            
            $crmquery = "insert into vtiger_crmentity (crmid,setype,description,createdtime) values(?,?,?,?)";
            $crmresult = $adb->pquery($crmquery, array($attachmentid, 'ModComments Attachment', $description, $date_var));
            
            $attachmentquery = "insert into vtiger_attachments(attachmentsid,name,description,type,path) values(?,?,?,?,?)";
            $attachmentreulst = $adb->pquery($attachmentquery, array($attachmentid, $filename, $description, $filetype, $upload_filepath));
            
            $modComments->column_fields['filename'] = $attachmentid;
        }
    }
    
    $modComments->column_fields['commentcontent'] = $element['commentcontent'];
    $modComments->column_fields['customer'] = $element['customer'];
    $modComments->column_fields['assigned_user_id'] = $element['assigned_user_id'];
    $modComments->column_fields['related_to'] = $element['related_to'];
    $modComments->column_fields['userid'] = $element['userid'];
    $modComments->column_fields['from_portal'] = true;
    $modComments->column_fields['parent_comments'] = $element['parent_comments'];
    
    $modComments->save('ModComments');
    
    if($modComments->id)
        $result = array('success'=>true,'modcommentid'=>$modComments);
    else
        $result = array('success'=>false);
            
    return $result;
            
}

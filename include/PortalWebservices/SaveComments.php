<?php

    function vtws_savecomments($element,$user){
        
        global $adb,$site_URL;
        
        $result = array();
        $save_doc = false;
        
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
                $description = 'CustomerPortal Document Attachment';
                
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
        
        
        $modComments->column_fields['commentcontent'] = $element['textvalue'];
        $modComments->column_fields['customer'] = $element['ID'];
        $modComments->column_fields['related_to'] = $element['ID'];
        $modComments->column_fields['from_portal'] = true;
        $modComments->save('ModComments');
        $fileUrl = '';
        if($save_doc && $attachmentid > 0 && $modComments->id){
            
            $related_doc = 'insert into vtiger_seattachmentsrel values (?,?)';
            $res = $adb->pquery($related_doc,array($modComments->id,$attachmentid));
            
            $fileUrl = $site_URL.'/index.php?module=Vtiger&action=ExternalDownloadLink&record='.$modComments->id;
        }
        
        if($modComments->id)
            $result = array('success'=>true,'modcommentid'=>$modComments->id,'fileurl'=>$fileUrl);
        else
            $result = array('success'=>false);
        
        return $result;
        
    }
    
<?php

function vtws_get_ticket_comments($element,$user){
    
    global $adb,$site_URL;
    
    $comment_data = array();
    
    $comments = $adb->pquery("SELECT * FROM vtiger_modcomments
	INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_modcomments.modcommentsid
	WHERE vtiger_crmentity.deleted = 0 AND vtiger_modcomments.related_to = ?
	AND (vtiger_modcomments.is_private IS NULL OR vtiger_modcomments.is_private != 1)
	AND vtiger_modcomments.modcommentsid > 0 ORDER BY vtiger_crmentity.createdtime ASC",
        array($element['ID']));
    
    if($adb->num_rows($comments)){
        
        for($j = 0; $j < $adb->num_rows($comments); $j++){
            
            /*$child_comments = $adb->pquery("SELECT vtiger_modcomments.modcommentsid FROM vtiger_modcomments
             INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_modcomments.modcommentsid
             WHERE vtiger_crmentity.deleted = 0 AND vtiger_modcomments.related_to = ?
             AND (vtiger_modcomments.is_private IS NULL OR vtiger_modcomments.is_private != 1)
             AND vtiger_crmentity.createdtime > '2017-12-06' ORDER BY vtiger_crmentity.createdtime DESC",
             array($element['ID']));
            
             $comment_ids =array();
             if($adb->num_rows($child_comments)){
             for($a=0;$a<$adb->num_rows($child_comments);$a++){
             array_push($comment_ids,$adb->query_result($child_comments,$a,'modcommentsid'));
             }
             }*/
            
            $parent_id = $adb->query_result($comments,$j,'parent_comments');
            
            $parent = '';
            
            if($parent_id){
                $parent = $parent_id;
            } else {
                $parent = null;
            }
            
            $modified = date_create($adb->query_result($comments,$j,'modifiedtime'));
            
            $created = date_create($adb->query_result($comments,$j,'createdtime'));
            
            $fullname = '';
            
            $createduser = false;
            
            $imagepath = '';
            
            if($adb->query_result($comments,$j,'customer')){
                
                $contact_model = Vtiger_Record_Model::getInstanceById($adb->query_result($comments,$j,'customer'), 'Contacts');
                
                $contact_image_details = $contact_model->getImageDetails();
                
                if($contact_image_details){
                    
                    foreach($contact_image_details as $contact_image_detail){
                        
                        if(!empty($contact_image_detail['path']) && !empty($contact_image_detail['orgname'])){
                            $imagepath = $site_URL . $contact_image_detail['path'] . "_" . $contact_image_detail['orgname'];
                        }
                        
                    }
                    
                }
                
                if(
                    $adb->query_result($comments,$j,'customer') == $element['contact_id']
                    ){
                        
                        $createduser = true;
                        
                } else {
                    
                    $fullname = getContactName($adb->query_result($comments,$j,'customer'));
                    
                }
                
            } else {
                
                $userModel = Vtiger_Record_Model::getInstanceById($adb->query_result($comments,$j,'userid'), 'Users');
                
                $userimagedetails = $userModel->getImageDetails();
                
                if($userimagedetails){
                    foreach($userimagedetails['imagename'] as $userimagedetail){
                        if(!empty($userimagedetail['path']) && !empty($userimagedetail['orgname'])){
                            $imagepath = $site_URL.$userimagedetail['path']."_".$userimagedetail['orgname'];
                        }
                    }
                }
                $fullname = getUserFullName($adb->query_result($comments,$j,'userid'));
            }
            
            $attachment = $adb->query_result($comments,$j,'filename');
            $att_Path = '';
            $att_type = '';
            $data = array();
            if($attachment){
                $result = $adb->pquery("SELECT * FROM vtiger_attachments
                INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_attachments.attachmentsid
                WHERE vtiger_crmentity.deleted = 0 AND vtiger_attachments.attachmentsid = ?
                and vtiger_crmentity.setype = ?", array($attachment, "ModComments Attachment"));
                
                if($adb->num_rows($result) == 1){
                    
                    $attPath = $site_URL;
                    $attPath .= "/".$adb->query_result($result, "0", "path");
                    $attPath .= $adb->query_result($result, "0", "attachmentsid");
                    $attPath .= "_".decode_html($adb->query_result($result, "0", "name"));
                    $att_Path = ($attPath);
                    $att_type = $adb->query_result($result, "0", "type");
                    $att_name = decode_html($adb->query_result($result, "0", "name"));
                }
            }
            
            $data['id'] = $adb->query_result($comments,$j,'modcommentsid');
            $data['parent'] = $parent;
            $data['content'] = html_entity_decode($adb->query_result($comments,$j,'commentcontent'));
            $data['created']=date_format($created,"Y-m-d H:i:s");
            $data['created_by_current_user']= $createduser;
            $data['profile_picture_url']=$imagepath;
            $data['fullname']=html_entity_decode($fullname);
            if($att_Path){
                if(strpos($att_type,'image') == false){
                    $att_Path = $site_URL.'/index.php?module=Vtiger&action=ExternalDownloadLink&record='.$data['id'];
                }
                $data['file'] = $att_name;
                $data['file_url'] = $att_Path;
                $data['file_mime_type'] = $att_type;
            }
            
            $comment_data[] = $data;
            
        }
    }
    return $comment_data;
}
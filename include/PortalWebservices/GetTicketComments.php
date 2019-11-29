<?php

function vtws_getTicketComment($element,$user){
    
    global $adb,$site_URL;
    
//     $element = json_decode($element,true);
    
    if(isset($element['index'])){
        $startIndex = $element['index'];
    } else {
        $startIndex = 0;
    }
    
    $comments = $adb->pquery("SELECT * FROM vtiger_modcomments
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_modcomments.modcommentsid
		WHERE vtiger_crmentity.deleted = 0 AND vtiger_modcomments.related_to = ?
        AND vtiger_modcomments.modcommentsid > 0 ORDER BY vtiger_crmentity.createdtime DESC
        LIMIT ".$startIndex.",10",array($element['ID']));
    
    
    if($adb->num_rows($comments)){
        for($j=0;$j<$adb->num_rows($comments);$j++){
            $child_comments = $adb->pquery("SELECT vtiger_modcomments.modcommentsid FROM vtiger_modcomments
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_modcomments.modcommentsid
				WHERE vtiger_crmentity.deleted = 0 AND vtiger_modcomments.related_to = ?
		      	AND (vtiger_modcomments.is_private IS NULL OR vtiger_modcomments.is_private != 1)
		        AND vtiger_crmentity.createdtime > '2017-12-06' ORDER BY vtiger_crmentity.createdtime DESC
		        LIMIT ".$startIndex.",10",array($element['ID']));
            
            $comment_ids =array();
            if($adb->num_rows($child_comments)){
                for($a=0;$a<$adb->num_rows($child_comments);$a++){
                    array_push($comment_ids,$adb->query_result($child_comments,$a,'modcommentsid'));
                }
            }
            
            $parent_id = $adb->query_result($comments,$j,'parent_comments');
            if(in_array($parent_id, $comment_ids)){
                $parent = $parent_id;
            }else{
                $parent = null;
            }
            
            $modified = date_create($adb->query_result($comments,$j,'modifiedtime'));
            $created = date_create($adb->query_result($comments,$j,'createdtime'));
            
            $fullname = '';
            $createduser = false;
            $imagepath = '';
            if($adb->query_result($comments,$j,'customer')){
                
                $accountModel = Vtiger_Record_Model::getInstanceById($adb->query_result($comments,$j,'customer'), 'Contacts');
                $Accountimagedetails = $accountModel->getImageDetails();
                if($Accountimagedetails){
                    foreach($Accountimagedetails as $accountimagedetail){
                        if(!empty($accountimagedetail['path']) && !empty($accountimagedetail['orgname'])){
                            $imagepath = $site_URL.$accountimagedetail['path']."_".$accountimagedetail['orgname'];
                        }
                    }
                }
                
                if($adb->query_result($comments,$j,'userid') == $_SESSION['ID']){
                    $createduser = true;
                }else{
                    $fullname = getAccountName($adb->query_result($comments,$j,'customer'));
                }
            }else{
                
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
            
            $comment_data[] = array('id'=>$adb->query_result($comments,$j,'modcommentsid'),
                'parent'=> $parent,
                'content'=>html_entity_decode($adb->query_result($comments,$j,'commentcontent')),
                'modified'=>date_format($modified,"Y-m-d"),
                'created'=>date_format($created,"Y-m-d H:i:s"),
                'created_by_current_user'=>$createduser,
                'profile_picture_url'=>$imagepath,
                'fullname'=>html_entity_decode($fullname));
            
        }
    }
    
//     if($adb->num_rows($comments) >= 10){
//         $comment_data['scroll_event'] = 1;
//     }
    
    return $comment_data;
    
}
<?php

function vtws_save_ticket_comment($element,$user){
    
    global $adb,$site_URL;

//     $element = json_decode($element,true);
    
    $result = array();
    
    $modComments = CRMEntity::getInstance('ModComments');
    
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

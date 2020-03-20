<?php

class NotificationsHandler extends VTEventHandler {
    
    function handleEvent($eventName, $data) {
        global $adb, $webSocket_url;
        $moduleName = $data->getModuleName(); 
        
        $acceptedModule = array('Notifications');
        if(!in_array($moduleName, $acceptedModule))
            return;
        
        if($eventName == 'vtiger.entity.aftersave') {
            //return true;
            if($data->isNew()){
               
                $assigned_user_id = $data->get('assigned_user_id');
                
                if($assigned_user_id){
                    if($webSocket_url){
                        
                        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
                        $ch = curl_init($protocol.$webSocket_url);
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

                        if($data->get('source') == 'PORTAL'){
                            $jsonData = json_encode([
                                'assigned_user_id' => $assigned_user_id,
                            ]);
                        }else{
                            $contact_id = '';
                            if(getSalesEntityType($data->get('related_to')) == 'Contacts'){
                                $contact_id = $data->get('related_to');
                            }else if(getSalesEntityType($data->get('related_to')) == 'HelpDesk'){
                                $tickets = $adb->pquery("SELECT parent_id FROM vtiger_troubletickets WHERE vtiger_troubletickets.ticketid = ?",
                                array($data->get('related_to')));
                                if($adb->num_rows($tickets)){
                                    $relatedTo = $adb->query_result($tickets, 0, 'parent_id');
                                    if(getSalesEntityType($relatedTo) == 'Contacts')
                                        $contact_id = $relatedTo;
                                }
                            }
                            $jsonData = json_encode([
                                'contactid' => $contact_id,
                                'fromportal' => false
                            ]);
                        }
                        $query = http_build_query(['data' => $jsonData]);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_exec($ch);
                        curl_close($ch);
                        
                    }
                }
               
            }
            
        }
    }
}


function createNotificationForPortalComments($entityData){
    
    global $current_user;
    $adb = PearDatabase::getInstance();
    $moduleName = $entityData->getModuleName();
    $wsId = $entityData->getId();
    $parts = explode('x', $wsId);
    $entityId = $parts[1];
    
    $parent = explode('x', $entityData->get('related_to'));
    $parentRecord = $parent[1];
    
    if(getSalesEntityType($parentRecord) == 'Contacts' || getSalesEntityType($parentRecord) == 'HelpDesk'){
      
        $wsAssignedUserId = $entityData->get('assigned_user_id');
        $userIdParts = explode('x', $wsAssignedUserId);
        $ownerId = $userIdParts[1];
        
        if($entityData->get('customer')){
            
            if(getSalesEntityType($parentRecord) == 'Contacts'){
                $fullName = Vtiger_Functions::getCRMRecordLabel($parentRecord);
                $title = '<div class="pull-left" style="margin: 7px 0px 0px 0px !important;"><i class="vicon-chat" title="comment" style="font-size: 1.5rem !important;"></i></div><div><span class="notification_full_name" title="'.$fullName.'"> ' .$fullName. ' send a new message.&nbsp;</span>
                <span class="notification_description" title="'. $entityData->get('commentcontent') .'">' .$entityData->get('commentcontent'). '&nbsp;</span>' ;
            }else if(getSalesEntityType($parentRecord) == 'HelpDesk'){
                $fullName = Vtiger_Functions::getCRMRecordLabel($entityData->get('customer'));
                $ticketName = Vtiger_Functions::getCRMRecordLabel($parentRecord);
                $title = '<div class="pull-left"  style="margin: 7px 0px 0px 0px !important;"><i class="vicon-chat" title="comment" style="font-size: 1.5rem !important;"></i></div><div><span class="notification_full_name" title="'.$ticketName.'"> ' .$fullName. ' commented on ticket '.$ticketName.'.&nbsp;</span>
                <span class="notification_description" title="'. $entityData->get('commentcontent') .'">' .$entityData->get('commentcontent'). '&nbsp;</span>' ;
            }
        }else{
            $fullName = getUserFullName($current_user->id);
            if(getSalesEntityType($parentRecord) == 'Contacts'){
                $title = '<div class="pull-left" style="margin: 7px 0px 0px 0px !important;"><i class="vicon-chat" title="comment" style="font-size: 1.5rem !important;"></i></div><div><span class="notification_full_name" title="'.$fullName.'"> ' .$fullName. ' send a new message.&nbsp;</span>
                <span class="notification_description" title="'. $entityData->get('commentcontent') .'">' .$entityData->get('commentcontent'). '&nbsp;</span>' ;
            }else if(getSalesEntityType($parentRecord) == 'HelpDesk'){
                $ticketName = Vtiger_Functions::getCRMRecordLabel($parentRecord);
                $title = '<div class="pull-left" style="margin: 7px 0px 0px 0px !important;"><i class="vicon-chat" title="comment" style="font-size: 1.5rem !important;"></i></div><div><span class="notification_full_name" title="'.$ticketName.'"> ' .$fullName. ' commented on ticket '.$ticketName.'.&nbsp;</span>
                <span class="notification_description" title="'. $entityData->get('commentcontent') .'">' .$entityData->get('commentcontent'). '&nbsp;</span>' ;
            }
        }
        
        
        $notifications = CRMEntity::getInstance('Notifications');
        $notifications->column_fields['assigned_user_id'] = $ownerId;
        $notifications->column_fields['related_to'] = $parentRecord;
        
        $notifications->column_fields['description'] = $title;
        if($entityData->get('customer'))
            $notifications->column_fields['source'] = 'PORTAL';
        $notifications->column_fields['related_record'] = $entityId;
        $notifications->save('Notifications');
        
    }
    
}

function createNotificationForPortalDocuments($entityData){
    global $current_user;
    $adb = PearDatabase::getInstance();
    $moduleName = $entityData->getModuleName();
    $wsId = $entityData->getId();
    $parts = explode('x', $wsId);
    $entityId = $parts[1];
    if($entityData->get('from_portal')){
        $parentRecord = $entityData->get('related_to');
    }else{
        $parentRecord = $_REQUEST['sourceRecord']; 
    }
    
    if(getSalesEntityType($parentRecord) == 'Contacts' || getSalesEntityType($parentRecord) == 'HelpDesk'){
        
        $wsAssignedUserId = $entityData->get('assigned_user_id');
        $userIdParts = explode('x', $wsAssignedUserId);
        $ownerId = $userIdParts[1];
        
        if($entityData->get('from_portal')){
            
            if(getSalesEntityType($parentRecord) == 'Contacts'){
                $fullName = Vtiger_Functions::getCRMRecordLabel($parentRecord).' added new document ';
                $title = '<div class="pull-left" style="margin: 7px 0px 0px 0px !important;"><i class="vicon-documents" title="document" style="font-size: 1.5rem !important;"></i></div><div><span class="notification_full_name" title="'.$fullName.'"> ' .$fullName. '.&nbsp;</span>
                <span class="notification_description" title="'. $entityData->get('notes_title') .'">' .$entityData->get('filename'). '&nbsp;</span>' ;
            }else if(getSalesEntityType($parentRecord) == 'HelpDesk'){
                $conIdParts = explode('x', $entityData->get('contactid'));
                $fullName = Vtiger_Functions::getCRMRecordLabel($conIdParts[1]);
                $ticketName = Vtiger_Functions::getCRMRecordLabel($parentRecord);
                $title = '<div class="pull-left" style="margin: 7px 0px 0px 0px !important;"><i class="vicon-documents" title="document" style="font-size: 1.5rem !important;"></i></div><div><span class="notification_full_name" title="'.$ticketName.'"> ' .$fullName. ' added new document for '.$ticketName.'.&nbsp;</span>
                <span class="notification_description" title="'. $entityData->get('notes_title') .'">' .$entityData->get('filename'). '&nbsp;</span>' ;
            }
        }else{
            $fullName = getUserFullName($current_user->id);
            if(getSalesEntityType($parentRecord) == 'Contacts'){
                $title = '<div class="pull-left" style="margin: 7px 0px 0px 0px !important;"><i class="vicon-documents" title="document" style="font-size: 1.5rem !important;"></i></div><div><span class="notification_full_name" title="'.$fullName.'"> ' .$fullName. ' added new document.&nbsp;</span>
                <span class="notification_description" title="'. $entityData->get('notes_title') .'">' .$entityData->get('filename'). '&nbsp;</span>' ;
            }else if(getSalesEntityType($parentRecord) == 'HelpDesk'){
                $ticketName = Vtiger_Functions::getCRMRecordLabel($parentRecord);
                $title = '<div class="pull-left" style="margin: 7px 0px 0px 0px !important;"><i class="vicon-documents" title="document" style="font-size: 1.5rem !important;"></i></div><div><span class="notification_full_name" title="'.$ticketName.'"> ' .$fullName. ' added new document for '.$ticketName.'.&nbsp;</span>
                <span class="notification_description" title="'. $entityData->get('notes_title') .'">' .$entityData->get('filename'). '&nbsp;</span>' ;
            }
        }
       
        $notifications = CRMEntity::getInstance('Notifications');
        $notifications->column_fields['assigned_user_id'] = $ownerId;
        $notifications->column_fields['related_to'] = $parentRecord;
        $notifications->column_fields['description'] = $title;
        if($entityData->get('from_portal'))
            $notifications->column_fields['source'] = 'PORTAL';
        $notifications->column_fields['related_record'] = $entityId;
        $notifications->save('Notifications');
        
    }
        
}

?>
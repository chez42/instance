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
            if(!$data->isNew()){
               
                $assigned_user_id = $data->get('assigned_user_id');
                
                if($assigned_user_id){
                    if($webSocket_url){
                        
                        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
                        $ch = curl_init($protocol.$webSocket_url);
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                        
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
    
    $adb = PearDatabase::getInstance();
    $moduleName = $entityData->getModuleName();
    $wsId = $entityData->getId();
    $parts = explode('x', $wsId);
    $entityId = $parts[1];
    
    $parent = explode('x', $entityData->get('related_to'));
    $parentRecord = $parent[1];
    
    $wsAssignedUserId = $entityData->get('assigned_user_id');
    $userIdParts = explode('x', $wsAssignedUserId);
    $ownerId = $userIdParts[1];
    
    $notifications = CRMEntity::getInstance('Notifications');
    $notifications->column_fields['assigned_user_id'] = $ownerId;
    $notifications->column_fields['related_to'] = $parentRecord;
    $notifications->column_fields['description'] = 'Create Comment From Portal';
    if($entityData->get('customer'))
        $notifications->column_fields['source'] = 'PORTAL';
    $notifications->column_fields['related_record'] = $entityId;
    $notifications->save('Notifications');
    
}

function createNotificationForPortalDocuments($entityData){
   
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
    
    $wsAssignedUserId = $entityData->get('assigned_user_id');
    $userIdParts = explode('x', $wsAssignedUserId);
    $ownerId = $userIdParts[1];
    
    $notifications = CRMEntity::getInstance('Notifications');
    $notifications->column_fields['assigned_user_id'] = $ownerId;
    $notifications->column_fields['related_to'] = $parentRecord;
    $notifications->column_fields['description'] = 'Create Document From Portal';
    if($entityData->get('from_portal'))
        $notifications->column_fields['source'] = 'PORTAL';
    $notifications->column_fields['related_record'] = $entityId;
    $notifications->save('Notifications');
    
}

?>
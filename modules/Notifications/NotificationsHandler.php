<?php

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
    $notifications->column_fields['related_record'] = $entityId;
    $notifications->save('Notifications');
    
}

function createNotificationForPortalDocuments($entityData){
   
    $adb = PearDatabase::getInstance();
    $moduleName = $entityData->getModuleName();
    $wsId = $entityData->getId();
    $parts = explode('x', $wsId);
    $entityId = $parts[1];
    
    $parentRecord = $entityData->get('related_to');
    
    $wsAssignedUserId = $entityData->get('assigned_user_id');
    $userIdParts = explode('x', $wsAssignedUserId);
    $ownerId = $userIdParts[1];
    
    $notifications = CRMEntity::getInstance('Notifications');
    $notifications->column_fields['assigned_user_id'] = $ownerId;
    $notifications->column_fields['related_to'] = $parentRecord;
    $notifications->column_fields['description'] = 'Create Document From Portal';
    $notifications->column_fields['related_record'] = $entityId;
    $notifications->save('Notifications');
    
}

?>
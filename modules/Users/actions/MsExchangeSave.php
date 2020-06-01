<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Users_MsExchangeSave_Action extends Vtiger_Save_Action {
    
    public function checkPermission(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $record = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        if(!Users_Privileges_Model::isPermitted($moduleName, 'Save', $record) || ($recordModel->isAccountOwner() &&
            $currentUserModel->get('id') != $recordModel->getId() && !$currentUserModel->isAdminUser())) {
                throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
            }
    }
    
    public function process(Vtiger_Request $request) {
       
        $moduleName = $request->getModule();
        $record = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
        
        $loadUrl = $recordModel->getMsSettingsDetailViewUrl();
        
        global $adb;
        
        $params = array();
        
        if($request->get('user_principal_name')){
            
            if($request->get('sync_direction')){
                
                $check = $adb->pquery("SELECT * FROM vtiger_msexchange_sync_settings WHERE user = ? and module = ?",
                    array($request->get('record'), 'Calendar'));
                
                if($adb->num_rows($check)){
                    
                    $query = "UPDATE vtiger_msexchange_sync_settings SET impersonation_identifier = ?";
                    $params[] = $request->get('user_principal_name');
                    
                    if($request->get('sync_direction')){
                        $query .= ', direction = ?';
                        $params[] = $request->get('sync_direction');
                    }
                    if($request->get('automatic_calendar_sync')){
                        $query .= ', enable_cron = ?';
                        $params[] = ($request->get('automatic_calendar_sync') == 'on') ? 1 : 0;
                    }
                    if($request->get('calendar_sync_start_from')){
                        $query .= ', sync_start_from = ?';
                        $params[] = getValidDBInsertDateValue($request->get('calendar_sync_start_from'));
                    }
                    $query .= ' WHERE user = ? ';
                    $params[] = $request->get('record');
                    
                }else{
                    
                    $query = "INSERT INTO vtiger_msexchange_sync_settings(id, user, module, direction, impersonation_identifier, sync_start_from, enable_cron) VALUES (?,?,?,?,?,?,?)";
                    
                    $syncId = $adb->getUniqueID("vtiger_msexchange_sync_settings");
                    $enable = ($request->get('automatic_calendar_sync') == 'on') ? 1 : 0;
                    $params = array($syncId, $request->get('record'), 'Calendar', $request->get('sync_direction'), $request->get('user_principal_name'),
                        getValidDBInsertDateValue($request->get('calendar_sync_start_from')), $enable);
                    
                }
                
                $adb->pquery($query,$params);
                
            }
            if($request->get('task_sync_direction')){
                $check = $adb->pquery("SELECT * FROM vtiger_msexchange_sync_settings WHERE user = ? and module = ?",
                    array($request->get('record'), 'Task'));
                
                if($adb->num_rows($check)){
                    
                    $query = "UPDATE vtiger_msexchange_sync_settings SET impersonation_identifier = ?";
                    $params[] = $request->get('user_principal_name');
                    
                    if($request->get('task_sync_direction')){
                        $query .= ', direction = ?';
                        $params[] = $request->get('task_sync_direction');
                    }
                    if($request->get('automatic_task_sync')){
                        $query .= ', enable_cron = ?';
                        $params[] = ($request->get('automatic_task_sync') == 'on') ? 1 : 0;
                    }
                    if($request->get('task_sync_start_from')){
                        $query .= ', sync_start_from = ?';
                        $params[] = getValidDBInsertDateValue($request->get('task_sync_start_from'));
                    }
                    $query .= ' WHERE user = ? ';
                    $params[] = $request->get('record');
                    
                }else{
                    
                    $query = "INSERT INTO vtiger_msexchange_sync_settings(id, user, module, direction, impersonation_identifier, sync_start_from, enable_cron) VALUES (?,?,?,?,?,?,?)";
                    
                    $syncId = $adb->getUniqueID("vtiger_msexchange_sync_settings");
                    $enable = ($request->get('automatic_task_sync') == 'on') ? 1 : 0;
                    $params = array($syncId, $request->get('record'), 'Task', $request->get('task_sync_direction'), $request->get('user_principal_name'),
                        getValidDBInsertDateValue($request->get('task_sync_start_from')), $enable);
                    
                }
                
                $adb->pquery($query,$params);
            }
            if($request->get('contact_sync_direction')){
                $check = $adb->pquery("SELECT * FROM vtiger_msexchange_sync_settings WHERE user = ? and module = ?",
                    array($request->get('record'), 'Contacts'));
                
                if($adb->num_rows($check)){
                    
                    $query = "UPDATE vtiger_msexchange_sync_settings SET impersonation_identifier = ?";
                    $params[] = $request->get('user_principal_name');
                    
                    if($request->get('contact_sync_direction')){
                        $query .= ', direction = ?';
                        $params[] = $request->get('contact_sync_direction');
                    }
                    /*  if($request->get('contact_sync_start_from')){
                     $query .= ', sync_start_from = ?';
                     $params[] = $request->get('contact_sync_start_from');
                     }*/
                    $query .= ' WHERE user = ? ';
                    $params[] = $request->get('record');
                    
                }else{
                    
                    $query = "INSERT INTO vtiger_msexchange_sync_settings(id, user, module, direction, impersonation_identifier, enable_cron) VALUES (?,?,?,?,?,?)";
                    
                    $syncId = $adb->getUniqueID("vtiger_msexchange_sync_settings");
                    $enable =  0;
                    $params = array($syncId, $request->get('record'), 'Contacts', $request->get('contact_sync_direction'), $request->get('user_principal_name'),
                        $enable);
                    
                }
                
                $adb->pquery($query,$params);
            }
        }
        
        header("Location: $loadUrl");
    }
}

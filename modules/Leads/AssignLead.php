<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

function AssignLead($entityData){
    
    $adb = PearDatabase::getInstance();
    $moduleName = $entityData->getModuleName();
    $wsId = $entityData->getId();
    $parts = explode('x', $wsId);
    $entityId = $parts[1];
    
    $roundrobin_logic=1;
    
    $allUsers = getAllUserName();
    
    $result = $adb->pquery("SELECT * FROM `workflow_roundrobin_logic`");
    
    $numOfRow = $adb->num_rows($result);
    
    if($numOfRow){
        $roundrobin_logic = $adb->query_result($result, 0, 'roundrobin_logic');
    }
    
    if($roundrobin_logic >= max(array_keys($allUsers)))
        $roundrobin_logic=1;
        
    $nextRoundrobinLogic = getNextRoundrobinLogin($roundrobin_logic, $allUsers);
    
    if($numOfRow){
        $adb->pquery("UPDATE workflow_roundrobin_logic SET roundrobin_logic = ?", array($nextRoundrobinLogic));
    }else{
        $adb->pquery("insert into workflow_roundrobin_logic VALUES(?)", array($nextRoundrobinLogic));
    }
    
    $focus = CRMEntity::getInstance($moduleName);
    
    $focus->column_fields['lastname'] = 'wkflow test';
    
    $focus->column_fields['assigned_user_id'] = $roundrobin_logic;
    
    $focus->saveentity($moduleName);
    
}

function getNextRoundrobinLogin($roundrobin_logic, $allUsers){
    
    $roundrobinlogic = ($roundrobin_logic+1)%max(array_keys($allUsers));
    
    if(!array_key_exists($roundrobinlogic, $allUsers)){
        return getNextRoundrobinLogin($roundrobinlogic,$allUsers);
    }else{
        return $roundrobinlogic;
    }
    
}

?>
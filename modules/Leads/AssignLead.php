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
    
    $wsId = $entityData->getId();
    
    $parts = explode('x', $wsId);
    
    $entityId = $parts[1];
    
    $roundrobin_logic=1;
    
    $allUsers = getAllUserName();
    
    $user_list = array_keys($allUsers);
    
    $result = $adb->pquery("SELECT * FROM `workflow_roundrobin_logic`");
    
    $numOfRow = $adb->num_rows($result);
    
    if($numOfRow){
        $roundrobin_logic = $adb->query_result($result, 0, 'roundrobin_logic');
    }
    
    if($roundrobin_logic >= count($user_list))
        $roundrobin_logic = 0;
        
        $roundrobinOwnerId = $user_list[$roundrobin_logic];
        
        $adb->pquery("update vtiger_crmentity set smownerid = ? where crmid = ?",
            array($roundrobinOwnerId, $entityId));
        
        $nextRoundrobinLogic = ($roundrobin_logic+1)%count($user_list);
        
        
        if($numOfRow){
            $adb->pquery("UPDATE workflow_roundrobin_logic SET roundrobin_logic = ?", array($nextRoundrobinLogic));
        }else{
            $adb->pquery("insert into workflow_roundrobin_logic VALUES(?)", array($nextRoundrobinLogic));
        }
        
}
?>
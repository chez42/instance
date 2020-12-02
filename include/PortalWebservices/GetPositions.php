<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

function vtws_get_positions($element, $user){
    
    global $log, $adb;
    
    $id = $element['id'];
    
    $account = $element['accounts'];
    
    $pageLimit = $element['pageLimit'];
    
    $startIndex = $element['startIndex'];
    
    
    if($startIndex == ''){
        $startIndex = 0;
    }
    
    
    $positions = array();
    
    $params = array();
    
    $sql = "SELECT DISTINCT vtiger_positioninformation.*, vtiger_crmentity.*, vtiger_positioninformationcf.*, vtiger_positioninformation.description FROM vtiger_positioninformation
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_positioninformation.positioninformationid
		INNER JOIN vtiger_positioninformationcf ON vtiger_positioninformationcf.positioninformationid = vtiger_positioninformation.positioninformationid
		LEFT JOIN vtiger_modsecurities ON vtiger_positioninformation.security_symbol = vtiger_modsecurities.security_symbol
		WHERE vtiger_crmentity.deleted = 0 AND vtiger_positioninformation.account_number = ?
		AND vtiger_positioninformation.quantity != 0  ";
    
    $result = $adb->pquery($sql, array($account));
    
    $count = $adb->num_rows($result);
    
    $sql .=" ORDER BY vtiger_crmentity.modifiedtime DESC LIMIT {$startIndex},{$pageLimit}";
    
    $result = $adb->pquery($sql, array($account));
    
    
    if($adb->num_rows($result)){
        
        while($row = $adb->fetchByAssoc($result)){
            $positions[] = $row;
        }
        
    }
    
    return array("data" => $positions, "count" => $count);
    
}
?>
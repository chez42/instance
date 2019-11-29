<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

function vtws_get_tickets($element, $user){

    global $log, $adb;
    
    $element = json_decode($element,true);
    
    $id = $element['id'];
	
    $module = $element['module'];
    
	$pageLimit = $element['pageLimit'];
    
	$startIndex = $element['startIndex'];
    
    if($startIndex == ''){
        $startIndex = 0;
    }
    
	$tickets = array();
	
	$count = 0;
	
	$sql = "SELECT * FROM vtiger_troubletickets 
    inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_troubletickets.ticketid 
    left join vtiger_contactdetails on vtiger_contactdetails.contactid=vtiger_troubletickets.parent_id 
    LEFT JOIN vtiger_ticketcf ON vtiger_troubletickets.ticketid = vtiger_ticketcf.ticketid 
    left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid 
    left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
    where vtiger_crmentity.deleted=0 and vtiger_contactdetails.contactid = ? ";
	
	$result = $adb->pquery($sql,array($id));
	
	$count = $adb->num_rows($result);
	
	if($count){
		
		$sql = "SELECT DISTINCT * 
        FROM vtiger_troubletickets 
        inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_troubletickets.ticketid 
        left join vtiger_contactdetails on vtiger_contactdetails.contactid=vtiger_troubletickets.parent_id 
        LEFT JOIN vtiger_ticketcf ON vtiger_troubletickets.ticketid = vtiger_ticketcf.ticketid 
        left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid 
        left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
         
        where vtiger_crmentity.deleted=0 and vtiger_contactdetails.contactid=? 
        ORDER BY vtiger_crmentity.modifiedtime DESC LIMIT {$startIndex},{$pageLimit}";
	
		$result = $adb->pquery($sql, array($id));
	
		if($adb->num_rows($result)){
		
			while($row = $adb->fetchByAssoc($result)){
				$tickets[] = $row;
			}
		
		}
	
	
	}
	
	return array("data" => $tickets, "count" => $count);
	
}
?>
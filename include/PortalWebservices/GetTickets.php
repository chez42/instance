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
    
    $id = $element['id'];
    
    $module = $element['module'];
    
    $pageLimit = $element['pageLimit'];
    
    $startIndex = $element['startIndex'];
    
    $title = $element['title'];
    
    $ticket_no = $element['ticket_no'];
    
    $priority = $element['priority'];
    
    $status = $element['status'];
    
    $open_days = $element['cf_3272'];
    
    $due_date = $element['cf_656'];
    
    $modifiedtime = $element['modifiedtime'];
    
    if($startIndex == ''){
        $startIndex = 0;
    }
    
    $tickets = array();
    $params = array();
    $count = 0;
    
    $sql = "SELECT * FROM vtiger_troubletickets
    inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_troubletickets.ticketid
    left join vtiger_contactdetails on vtiger_contactdetails.contactid=vtiger_troubletickets.parent_id
    LEFT JOIN vtiger_ticketcf ON vtiger_troubletickets.ticketid = vtiger_ticketcf.ticketid
    left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
    left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
    where vtiger_crmentity.deleted=0 and vtiger_contactdetails.contactid = ? ";
    $params[] = $id;
    
    if($title){
        $sql .= " AND vtiger_troubletickets.title LIKE ?";
        $params[] = '%'.$title.'%';
    }
    if($ticket_no){
        $sql .= " AND vtiger_troubletickets.ticket_no LIKE ?";
        $params[] = '%'.$ticket_no.'%';
    }
    if($priority){
        $sql .= " AND vtiger_troubletickets.priority = ?";
        $params[] = $priority;
    }
    if($status){
        $sql .= " AND vtiger_troubletickets.status = ?";
        $params[] = $status;
    }
    if($open_days){
        $sql .= " AND vtiger_ticketcf.cf_3272 = ?";
        $params[] = $open_days;
    }
    if($due_date){
        $sql .= " AND vtiger_ticketcf.cf_656 = ?";
        $params[] = $due_date;
    }
    if($modifiedtime){
        $sql .= " AND vtiger_crmentity.modifiedtime LIKE ?";
        $params[] = '%'.$modifiedtime.'%';
    }
    $sql .=" ORDER BY vtiger_crmentity.modifiedtime DESC ";
    
    $result = $adb->pquery($sql, $params);
   
    $count = $adb->num_rows($result);
    
    $ticketIds = array();
    
    if($count){
        
        for($ti=0;$ti<$adb->num_rows($result);$ti++){
            $ticketIds[] = $adb->query_result($result, $ti, 'ticketid');
        }
        
        $sql = "SELECT DISTINCT vtiger_troubletickets.*, vtiger_crmentity.*, vtiger_ticketcf.*, 
        vtiger_troubletickets.status as ticket_status
        FROM vtiger_troubletickets
        inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_troubletickets.ticketid
        left join vtiger_contactdetails on vtiger_contactdetails.contactid=vtiger_troubletickets.parent_id
        LEFT JOIN vtiger_ticketcf ON vtiger_troubletickets.ticketid = vtiger_ticketcf.ticketid
        left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
        left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
        
        where vtiger_crmentity.deleted=0 and vtiger_contactdetails.contactid=?";
        $params = array();
        $params[] = $id;
        
        if($title){
            $sql .= " AND vtiger_troubletickets.title LIKE ?";
            $params[] = '%'.$title.'%';
        }
        if($ticket_no){
            $sql .= " AND vtiger_troubletickets.ticket_no LIKE ?";
            $params[] = '%'.$ticket_no.'%';
        }
        if($priority){
            $sql .= " AND vtiger_troubletickets.priority = ?";
            $params[] = $priority;
        }
        if($status){
            $sql .= " AND vtiger_troubletickets.status = ?";
            $params[] = $status;
        }
        if($open_days){
            $sql .= " AND vtiger_ticketcf.cf_3272 = ?";
            $params[] = $open_days;
        }
        if($due_date){
            $sql .= " AND vtiger_ticketcf.cf_656 = ?";
            $params[] = $due_date;
        }
        if($modifiedtime){
            $sql .= " AND vtiger_crmentity.modifiedtime LIKE ?";
            $params[] = '%'.$modifiedtime.'%';
        }
        
        $sql .=" ORDER BY vtiger_crmentity.modifiedtime DESC LIMIT {$startIndex},{$pageLimit}";
        
        $result = $adb->pquery($sql, $params);
        
        if($adb->num_rows($result)){
            
            while($row = $adb->fetchByAssoc($result)){
                $tickets[] = $row;
            }
            
        }
        
        
    }
    
    return array("data" => $tickets, "count" => $count, 'ticket_ids'=>$ticketIds);
    
}
?>
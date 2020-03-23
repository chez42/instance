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
    
    $permission_result = $adb->pquery("SELECT * FROM `vtiger_contact_portal_permissions` inner join
	vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_contact_portal_permissions.crmid
	where crmid = ?", array($id));
    
    $ticket_across_org = 0;
    
    //28743010
    $contact_ids = array();
    
    $contact_ids[] = $id;
    
    if($adb->num_rows($permission_result)){
        $ticket_across_org = $adb->query_result($permission_result, 0, "tickets_record_across_org");
        $account_id = $adb->query_result($permission_result, 0, "accountid");
        if($account_id){
			$contact_result = $adb->pquery("SELECT * FROM `vtiger_contactdetails`
			inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.contactid
			where accountid = ? and deleted = 0", array($account_id));
			for($i = 0; $i < $adb->num_rows($contact_result); $i++){
				$contact_ids[] = $adb->query_result($contact_result, $i, "contactid");
			}
		}
    }
    
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
    
    $category = $element['category'];
    
    $tickettime = $element['tickettime'];
    
    if($startIndex == ''){
        $startIndex = 0;
    }
    
    
    $tickets = array();
    
    $params = array();
    
    $count = 0;
    
    $sql = "SELECT * FROM vtiger_troubletickets
    inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_troubletickets.ticketid
    inner JOIN vtiger_ticketcf ON vtiger_troubletickets.ticketid = vtiger_ticketcf.ticketid
    left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
    left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
    where vtiger_crmentity.deleted = 0 and
    vtiger_troubletickets.parent_id in ('" . implode("','", $contact_ids) . "') ";
    
    
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
    if($category){
        $sql .= " AND vtiger_troubletickets.category = ?";    
        $params[] = $category;
    }
   
    if($tickettime){
        if($tickettime == '<1hrs'){
            $sql .= ' AND (vtiger_troubletickets.total_time_spent < "01:00" AND vtiger_troubletickets.total_time_spent >= "00:00")';
        }elseif($tickettime == '<2hrs'){
            $sql .= ' AND (vtiger_troubletickets.total_time_spent < "02:00" AND vtiger_troubletickets.total_time_spent >= "01:010")';
        }elseif($tickettime == '<3hrs'){
            $sql .= ' AND (vtiger_troubletickets.total_time_spent < "03:00" AND vtiger_troubletickets.total_time_spent >= "02:00")';
        }elseif($tickettime == '<4hrs'){
            $sql .= 'AND (vtiger_troubletickets.total_time_spent < "04:00" AND vtiger_troubletickets.total_time_spent >= "03:00")';
        }elseif($tickettime == '<5hrs'){
            $sql .= ' AND (vtiger_troubletickets.total_time_spent < "05:00" AND vtiger_troubletickets.total_time_spent >= "04:00")';
        }elseif($tickettime == '<5hrs'){
            $sql .= ' AND (vtiger_troubletickets.total_time_spent >= "05:00")';
            
        }
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
        inner JOIN vtiger_ticketcf ON vtiger_troubletickets.ticketid = vtiger_ticketcf.ticketid
        left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
        left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
        where vtiger_crmentity.deleted=0 and
		vtiger_troubletickets.parent_id in ('" . implode("','", $contact_ids) . "') ";
        
        $params = array();
        
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
        
        if($category){
            $sql .= " AND vtiger_troubletickets.category = ?";
            $params[] = $category;
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
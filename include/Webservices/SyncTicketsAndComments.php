<?php

function vtws_save_tickets_and_comments($element,$user){
    
    global $adb,$site_URL;
   
    if($element['mode'] == 'tickets'){
        
        $helpDesk = CRMEntity::getInstance('HelpDesk');
        
        $ticketId = $element['ticket_no'];
        
        $ticket = $adb->pquery("SELECT * FROM vtiger_troubletickets
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_troubletickets.ticketid
		WHERE vtiger_crmentity.deleted = 0 AND vtiger_troubletickets.referenceid = ?",array($ticketId));
        
        if($adb->num_rows($ticket)){
            
            $id = $adb->query_result($ticket,0,'ticketid');
            $helpDesk->id = $id;
            $helpDesk->retrieve_entity_info($id, 'HelpDesk');
            $helpDesk->mode = 'edit';
            
        }
        
        $helpDesk->column_fields['ticket_title'] = $element['ticket_title'];
        $helpDesk->column_fields['description'] = $element['description'];
        $helpDesk->column_fields['cf_656'] = $element['cf_656'];
        $helpDesk->column_fields['ticketstatus'] = $element['ticketstatus'];
        $helpDesk->column_fields['ticketpriorities'] = $element['ticketpriorities'];
        $helpDesk->column_fields['cf_658'] = $element['cf_658'];
        $helpDesk->column_fields['cf_646'] = $element['cf_646'];
        
        $helpDesk->column_fields['source'] = $element['source'];
        
        $helpDesk->save('HelpDesk');
        
        
        if($helpDesk->id){
            $adb->pquery("UPDATE vtiger_troubletickets SET referenceid = ?, vtiger_troubletickets.original_assigned_to = ?, vtiger_troubletickets.original_creator = ? WHERE vtiger_troubletickets.ticketid = ?",
            array($ticketId, $element['originalassigneduser'], $element['originalcreatorname'], $helpDesk->id));
        } 
        
        return $helpDesk->id;
        
    }else if($element['mode'] == 'comment'){
        
       
        $ticket = $adb->pquery("SELECT * FROM vtiger_troubletickets
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_troubletickets.ticketid
		WHERE vtiger_crmentity.deleted = 0 AND vtiger_troubletickets.referenceid = ?",array($element['ticket_no']));
        
        if($adb->num_rows($ticket)){
            
            $id = $adb->query_result($ticket,0,'ticketid');
            
            $modComments = CRMEntity::getInstance('ModComments');
            $modComments->column_fields['commentcontent'] = $element['commentcontent'];
            $modComments->column_fields['related_to'] = $id;
            $modComments->column_fields['userid'] = $element['userid'];
            $modComments->save('ModComments');
            
            return $modComments->id;
            
        }
        
    }
    
}


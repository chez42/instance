<?php
function vtws_sync_tickets_and_comments_with_instance($element){
    
    global $adb, $current_user;

    $user = Users::getActiveAdminUser();
    vglobal("current_user", $user);
    
    if($element['mode'] == 'tickets'){
        
        $helpDesk = CRMEntity::getInstance('HelpDesk');
        
        $ticketId = $element['referenceid'];
        
        $ticket = $adb->pquery("SELECT * FROM vtiger_troubletickets
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_troubletickets.ticketid
		WHERE vtiger_crmentity.deleted = 0 AND vtiger_troubletickets.ticket_no = ?",array($ticketId));
        
        if($adb->num_rows($ticket)){
            
            $id = $adb->query_result($ticket,0,'ticketid');
            $helpDesk->id = $id;
            $helpDesk->retrieve_entity_info($id, 'HelpDesk');
            $helpDesk->mode = 'edit';
            
            $helpDesk->column_fields['ticketstatus'] = $element['ticketstatus'];
            $helpDesk->saveentity('HelpDesk');
            
        }
        
        return $helpDesk->id;
        
    }else if($element['mode'] == 'comment'){
        
        
        $ticket = $adb->pquery("SELECT * FROM vtiger_troubletickets
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_troubletickets.ticketid
		WHERE vtiger_crmentity.deleted = 0 AND vtiger_troubletickets.ticket_no = ?",array($element['ticket_no']));
        
        if($adb->num_rows($ticket)){
            
            $id = $adb->query_result($ticket,0,'ticketid');
            
            $modComments = CRMEntity::getInstance('ModComments');
            $modComments->column_fields['commentcontent'] = $element['commentcontent'];
            $modComments->column_fields['related_to'] = $id;
            $modComments->column_fields['userid'] = $element['userid'];
            $modComments->saveentity('ModComments');
            
            return $modComments->id;
            
        }
        
    }
    
}


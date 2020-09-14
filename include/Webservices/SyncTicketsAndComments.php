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
        $helpDesk->column_fields['ticketcategories'] = $element['ticketcategories'];
        $helpDesk->column_fields['cf_3643'] = $element['cf_3643'];
        $helpDesk->column_fields['ticketstatus'] = $element['ticketstatus'];
        $helpDesk->column_fields['ticketpriorities'] = $element['ticketpriorities'];
        $helpDesk->column_fields['cf_656'] = $element['cf_656'];
        $helpDesk->column_fields['description'] = $element['description'];
        
       /*  foreach($element as $key => $value){
            if($key != 'id' && $key != 'label' && $key != 'creator' && $key != 'modifiedby' && $key != 'createdtime' && $key != 'modifiedtime'){
                $helpDesk->column_fields[$key] = $value;
            }
            if($key == 'assigned_user_id' || $key == 'parent_id' || $key == 'financial_advisor' || $key == 'project_id'){
                $value = explode('x',$value);
                $helpDesk->column_fields[$key] = $value[1];
            }
        } */
        
        $helpDesk->column_fields['source'] = 'opt';
        $helpDesk->column_fields['referenceid'] = $ticketId;
        
        $helpDesk->save('HelpDesk');
        
        
       /*  if($helpDesk->id){
            $fin_value = explode('x', $element['financial_advisor']);
            $adb->pquery("UPDATE vtiger_ticketcf SET vtiger_ticketcf.financial_advisor = ? WHERE vtiger_ticketcf.ticketid = ?;",array($fin_value[1], $helpDesk->id));
            $adb->pquery("UPDATE vtiger_troubletickets SET vtiger_troubletickets.ticket_no = ? WHERE vtiger_troubletickets.ticketid > ?",array($element['ticket_no'], $helpDesk->id));
        } */
        
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

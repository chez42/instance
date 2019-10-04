<?php

class Omniscient_TransferComments_Action extends Omniscient_Transfer_Action{
    public function InsertTicketComments($info){
        global $adb;
        //159706 is the last ticketcomment id on v1
        $query = "INSERT INTO vtiger_ticketcomments (ticketid, comments, ownerid, ownertype, createdtime) "
               . "VALUES (?, ?, ?, ?, ?)";
        $touched = "INSERT INTO copied_ids (crmid) VALUES (?)";

        $adb->pquery($query, array($info['related_to'], $info['commentcontent'], $info['userid'], 'user', $info['createdtime']));
        $adb->pquery($touched, array($info['crmid']));
        
/*
ticketid,comments,ownerid,ownertype,createdtime
related_to,commentcontent,userid,'user',createdtime
 */
    }
    /**
     * Transfer households from v2 to v1
     * @global type $adb
     */
    public function TransferToCRM100(Vtiger_Request $request){
        global $adb;
        $date = $request->get('comment_date');
        $type = 'ModComments';
        $copied_ids = $this->GetCopiedIds();
        $copied_ids = SeparateArrayWithCommas($copied_ids);

        $query = "SELECT e.*, m.*, cf.*, e2.setype AS parent_module FROM vtiger_crmentity e " 
               . "JOIN vtiger_modcomments m ON m.modcommentsid = e.crmid "
               . "JOIN vtiger_modcommentscf cf ON cf.modcommentsid = m.modcommentsid "
               . "JOIN vtiger_crmentity e2 ON e2.crmid = m.related_to "
               . "JOIN copied_ids ci ON ci.crmid = m.related_to "
               . "WHERE e.createdtime >= ? AND e.setype=? AND e.crmid NOT IN ({$copied_ids})";

        $result = $adb->pquery($query, array($date, $type));
        if($adb->num_rows($result) > 0)
            foreach($result AS $k => $v){
                $info[] = $v;
            }
            
        foreach($info AS $k => $v){
            if($v['parent_module'] == "HelpDesk"){
                print_r($v);
                echo "<br /><br />";
//                $this->InsertTicketComments($v);
            }
            else{
/*                $new_id = $this->UpdateEntitySequence();
                $this->InsertIntoEntityTable($v, $new_id);            
                $this->InsertComments($v, $new_id);*/
            }
        }

//        $this->FixParents();
        return "Service Tickets Inserted";
    }
    
    /**
     * Insert household accounts into CRM100
     * @global type $adb
     * @param type $info
     * @param type $new_id
     */
    public function InsertTickets($info, $new_id){
        global $adb;
        $tickets = "INSERT INTO advisorviewcrm100.vtiger_troubletickets (ticketid,ticket_no,groupname,parent_id,product_id,priority,severity,status,category,title,solution,update_log,version_id,hours,days)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $adb->pquery($tickets, array($new_id,$info['ticket_no'],$info['groupname'],$info['parent_id'],$info['product_id'],$info['priority'],$info['severity'],$info['status'],$info['category'],$info['title'],$info['solution'],$info['update_log'],$info['version_id'],$info['hours'],$info['days']));
        $cf = "INSERT INTO advisorviewcrm100.vtiger_ticketcf (ticketid,cf_646,cf_647,cf_648,cf_649,cf_650,cf_651,cf_652,cf_653,cf_654,cf_655,cf_656,cf_657,cf_658,old_ucrm_ticket_id,cf_687,cf_688,cf_700,cf_701,cf_707,cf_711,cf_713,cf_714,cf_715,cf_716,cf_717,cf_788, v2_id)
               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $adb->pquery($cf, array($new_id,$info['cf_646'],$info['cf_647'],$info['cf_648'],$info['cf_649'],$info['cf_650'],$info['cf_651'],$info['cf_652'],$info['cf_653'],$info['cf_654'],$info['financial_advisor'],$info['cf_656'],$info['cf_657'],$info['cf_658'],$info['old_ucrm_ticket_id'],$info['cf_687'],$info['cf_688'],$info['cf_700'],$info['cf_701'],$info['cf_707'],$info['cf_711'],$info['cf_713'],$info['cf_714'],$info['cf_715'],$info['cf_716'],$info['cf_717'],$info['cf_788'], $info['crmid']));
        $touched = "INSERT INTO copied_ids (crmid) VALUES (?)";
        $adb->pquery($touched, array($info['crmid']));
    }
    
    /**
     * Connect contacts to households
     */
    public function ConnectTickets(){
        $this->UpdateParent();//Relate to the proper contacts
    }
    
    /**
     * Update the parent ID starting at the starting_crm_id
     * @param type $old_id
     * @param type $new_id
     * @param type $starting_crm_id
     */
    public function UpdateParent($field_name, $table_name, $starting_crm_id){
        global $adb;
        $copied_ids = $this->GetCopiedIdsWithOldId();

        $query = "UPDATE advisorviewcrm100.vtiger_troubletickets t "
               . "JOIN advisorviewcrm100.vtiger_crmentity e ON e.crmid = t.ticketid "
               . "SET t.parent_id = ? "
               . "WHERE t.parent_id = ? AND e.createdtime >= '2014-06-28'";

        foreach($copied_ids AS $old_id => $new_id){
            $adb->pquery($query, array($new_id, $old_id));
        }

/*
        $query = "SELECT {$field_name}, v2_id FROM advisorviewcrm100.{$table_name} cf "
               . "WHERE cf.v2_id IN ({$copied_ids})";

        $result = $adb->pquery($query, array());
        foreach($result AS $k => $v){
            $query = "UPDATE advisorviewcrm100.vtiger_troubletickets t "
                   . "SET t.parent_id = ? "
                   . "WHERE t.parent_id = ? AND t.ticketid >= ?";
            $adb->pquery($query, array($v[$field_name], $v['v2_id'], $starting_crm_id));
        }*/
    }
}

?>
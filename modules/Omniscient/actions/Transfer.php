<?php

class Omniscient_Transfer_Action extends Vtiger_BasicAjax_Action{
    public $entity_seq;
    
    /**
     * Updated the copied ID's to add in the new CRM ID
     * @global type $adb
     */
    public function UpdateCopiedIds(){
        global $adb;
        $ids = array();
        $query = "SELECT contactid, v2_id FROM advisorviewcrm100.vtiger_contactscf cf WHERE cf.v2_id IS NOT NULL";
        $result = $adb->pquery($query, array());
        foreach($result AS $k => $v){
            $ids[$v['contactid']] = $v['v2_id'];
        }
        $query = "SELECT accountid, v2_id FROM advisorviewcrm100.vtiger_accountscf cf WHERE cf.v2_id IS NOT NULL";
        $result = $adb->pquery($query, array());
        foreach($result AS $k => $v){
            $ids[$v['accountid']] = $v['v2_id'];
        }
        $query = "SELECT ticketid,  v2_id FROM advisorviewcrm100.vtiger_ticketcf cf WHERE cf.v2_id IS NOT NULL";
        $result = $adb->pquery($query, array());
        foreach($result AS $k => $v){
            $ids[$v['ticketid']] = $v['v2_id'];
        }
        
        $query = "UPDATE copied_ids SET new_id = ? WHERE crmid = ?";
        foreach($ids AS $new_id => $old_id){
            $adb->pquery($query, array($new_id, $old_id));
        }
    }
    
    /**
     * Returns a simple array containing the crmid only
     * @global type $adb
     * @return type
     */
    public function GetCopiedIds(){
        global $adb;
        $query = "SELECT * FROM copied_ids";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $info[] = $v['crmid'];
            }
        }
        
        return $info;
    }
    
    /**
     * Get a list of ticket ID's from the copied_ids table
     * @global type $adb
     * @return type
     */
    public function GetCopiedTicketIds(){
        global $adb;
        $query = "SELECT ticketid FROM vtiger_troubletickets WHERE ticketid IN (SELECT crmid FROM copied_ids)";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $info[] = $v['crmid'];
            }
        }
        
        return $info;
    }
    
    /**
     * Get non ticket ID's from the copied_ids table.  This returns in old_id=>new_id array format
     * @return type
     */
    public function GetCopiedNonTicketIds(){
        $ids = $this->GetCopiedTicketIds();
        $ids = SeparateArrayWithCommas($ids);
        $query = "SELECT * FROM copied_ids WHERE crmid NOT IN ({$ids})";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $info[$v['crmid']] = $v['new_id'];
            }
        }
        
        return $info;        
    }
    
    public function GetCopiedIdsWithOldId(){
        global $adb;
        $query = "SELECT * FROM copied_ids";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $info[$v['crmid']] = $v['new_id'];
            }
        }
        
        return $info;
    }
    
    public function GetEntityInfo($date, $setype){
        global $adb;
        $query = "SELECT * FROM vtiger_crmentity "
               . "WHERE createdtime >= ? AND setype=?";
        $result = $adb->pquery($query, array($date, $setype));
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $info['entity'][] = $v;
            }
        }
        
        return $info;
    }
    
    public function UpdateEntitySequence(){
        global $adb;
        $update_query = "UPDATE advisorviewcrm100.vtiger_crmentity_seq SET id = id+1";
        $adb->pquery($update_query, array());
        $query = "SELECT id FROM advisorviewcrm100.vtiger_crmentity_seq";
        $result = $adb->pquery($query, array());
        return $adb->query_result($result, 0, 'id');
    }
    
    public function InsertIntoEntityTable($info, $new_id){
        global $adb;
        $insert_query = "INSERT INTO advisorviewcrm100.vtiger_crmentity (crmid,
                                                       smcreatorid,
                                                       smownerid,
                                                       modifiedby,
                                                       setype,
                                                       description,
                                                       createdtime,
                                                       modifiedtime,
                                                       viewedtime,
                                                       status,
                                                       version,
                                                       presence,
                                                       deleted)
                         VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $adb->pquery($insert_query, array($new_id,
                                          $info['smcreatorid'],
                                          $info['smownerid'],
                                          $info['modifiedby'],
                                          $info['setype'],
                                          $info['description'],
                                          $info['createdtime'],
                                          $info['modifiedtime'],
                                          $info['viewedtime'],
                                          $info['status'],
                                          $info['version'],
                                          $info['presence'],
                                          $info['deleted']));
    }
}
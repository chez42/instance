<?php

class OmniCal_CRMExchangeHandler_Model extends Vtiger_Base_Model{
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get the sync state based on user_id and sync type
     * @global type $adb
     * @param type $user_id
     * @param type $sync_type
     * @return int
     */
    static public function GetSyncState($user_id, $sync_type){
        global $adb;
        $query = "SELECT exchange_sync_state FROM exchange_sync WHERE exchange_sync_type = ? AND exchange_sync_user_id = ?";
        $result = $adb->pquery($query, array($sync_type, $user_id));
        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, 'exchange_sync_state');
        }
        else
            return 0;
    }
    
    /**
     * Retrieves exhange_sync_type, exchange_sync_state, exchanged_sync_date
     * @param type $user_id
     */
    static public function GetSyncInfo($user_id, $sync_type = null){
        global $adb;
        if(strlen($sync_type) > 0){
            $and = " AND exchange_sync_type = '{$sync_type}' ";
        }
        $query = "SELECT exchange_sync_type, exchange_sync_state, exchange_sync_date, exchange_sync_enabled, exchange_sync_id
                  FROM exchange_sync
                  WHERE exchange_sync_user_id = ? {$and}";
        
        $result = $adb->pquery($query, array($user_id));
        if($adb->num_rows($result) <= 0){
            return 0;
        }

        $sync_info = array();
        foreach($result AS $k => $v){
            $tmp = array("table_id" => $v['exchange_sync_id'],
                         "type" => $v['exchange_sync_type'],
                         "state" => $v['exchange_sync_state'],
                         "date" => $v['exchange_sync_date'],
                         "enabled" => $v['exchange_sync_enabled']);
            $sync_info[] = $tmp;
        }
        
        return $sync_info;
    }
    
    /**
     * Set the enabled state for the table ID
     * @param type $table_id
     */
    static public function UpdateEnabled($table_id, $enabled){
        global $adb;
        $query = "UPDATE exchange_sync
                  SET exchange_sync_enabled = ?
                  WHERE exchange_sync_id = ?";
        $adb->pquery($query, array($enabled, $table_id));
    }
    
    /**
     * Set the sync state for the table ID
     * @param type $table_id
     */
    static public function UpdateSyncState($table_id, $state){
        global $adb;
        $query = "UPDATE exchange_sync
                  SET exchange_sync_state = ?, exchange_sync_date = NOW()
                  WHERE exchange_sync_id = ?";
        $adb->pquery($query, array($state, $table_id));
    }
    
    /**
     * Create the sync info for the user into the exchange_sync table.  Sync state is completely optional in case we ever have a sync state that we want to start from
     * @param type $user_id
     * @param type $sync_type
     * @param type $sync_state
     */
    static public function CreateSyncInfo($user_id, $sync_type, $sync_state = null){
        global $adb;
        $query = "INSERT INTO exchange_sync (exchange_sync_user_id, exchange_sync_type, exchange_sync_state, exchange_sync_enabled, created_date)
                  VALUES (?, ?, ?, 1, NOW())";
        $adb->pquery($query, array($user_id, $sync_type, $sync_state));
    }
    
    /**
     * Returns the ID and ChangeKey for the specified activity (Events and Tasks use this)
     * @global type $adb
     * @param type $record
     */
    static public function GetActivityIdAndChangeKey($record){
        global $adb;
        $query = "SELECT task_exchange_item_id, task_exchange_change_key
                  FROM vtiger_activitycf
                  WHERE activityid = ?";
        $result = $adb->pquery($query, array($record));
        if($adb->num_rows($result) > 0){
            if(strlen($adb->query_result($result, 0, 'task_exchange_item_id')) > 0)
                return array("id" => $adb->query_result($result, 0, 'task_exchange_item_id'),
                             "changekey" => $adb->query_result($result, 0, 'task_exchange_change_key'));
        }
        return 0;
    }
    
    /**
     * Returns the ID and ChangeKey for the specified contact
     * @global type $adb
     * @param type $record
     */
    static public function GetContactIdAndChangeKey($record){
        global $adb;
        $query = "SELECT contact_exchange_item_id, contact_exchange_change_key
                  FROM vtiger_contactscf
                  WHERE contactid = ?";
        $result = $adb->pquery($query, array($record));
        if($adb->num_rows($result) > 0){
            if(strlen($adb->query_result($result, 0, 'contact_exchange_item_id')) > 0)
                return array("id" => $adb->query_result($result, 0, 'contact_exchange_item_id'),
                             "changekey" => $adb->query_result($result, 0, 'contact_exchange_change_key'));
        }
        return 0;
    }
}
?>

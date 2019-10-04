<?php

class OmniCal_ExchangeBridge_Model extends Calendar_Record_Model{
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * returns true or false depending if exchange is enabled for the contact
     * @param type $contactid
     */
    public static function IsContactExchangeEnabled($contactid){
        global $adb;
        $query = "SELECT sync_outlook FROM vtiger_contactscf WHERE contactid=?";
        $result = $adb->pquery($query, array($contactid));
        if($adb->num_rows($result) > 0){
            $item = $adb->query_result($result, 0, 'sync_outlook');
            if($item == 1 || $item == "on" || $item == "On")
                return true;
        }        
        return false;
    }
    
    /**
     * Returns the activity id if it exists, otherwise it returns false
     * @global type $adb
     * @param type $itemId
     * @return boolean
     */
    public static function DoesContactExist($exchange_id){
        global $adb;
        $query = "SELECT contactid FROM vtiger_contactscf WHERE BINARY contact_exchange_item_id = ?";//Do a binary comparison..turns out these unique ID's can be identical minus some case sensitivity
        $result = $adb->pquery($query, array($exchange_id));
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'contactid');
        else
            return false;
    }

    public static function DoContactChangeKeysMatch($record, $changekey){
        global $adb;
        $query = "SELECT contact_exchange_change_key FROM vtiger_contactscf WHERE contactid = ?";
        $result = $adb->pquery($query, array($record));
        if($adb->num_rows($result) > 0){
            $ck = $adb->query_result($result, 0, 'contact_exchange_change_key');
#            echo "<br />OUR CHANGE KEY: {$ck}.. Their Change Key: {$changekey}<br />";
            if($ck == $changekey)
                return true;
        }
        return false;
    }
    
    /**
     * Returns the activity id if it exists, otherwise it returns false
     * @global type $adb
     * @param type $itemId
     * @return boolean
     */
    public static function DoesItemExist($itemId){
        global $adb;
        $query = "SELECT activityid FROM vtiger_activitycf WHERE BINARY task_exchange_item_id = ?";//Do a binary comparison..turns out these unique ID's can be identical minus some case sensitivity
        $result = $adb->pquery($query, array($itemId));
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'activityid');
        else
            return false;
    }
    
    public static function DoChangeKeysMatch($record, $changekey){
        global $adb;
        $query = "SELECT task_exchange_change_key FROM vtiger_activitycf WHERE activityid = ?";
        $result = $adb->pquery($query, array($record));
        if($adb->num_rows($result) > 0){
            $ck = $adb->query_result($result, 0, 'task_exchange_change_key');
#            echo "<br />OUR CHANGE KEY: {$ck}.. Their Change Key: {$changekey}<br />";
            if($ck == $changekey)
                return true;
        }
        return false;
    }
    
}
?>

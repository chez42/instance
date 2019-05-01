<?php

class Omniscient_EditExchangeUsers_Model extends Vtiger_Module_Model {
    public function __construct() {
        parent::__construct();
    }
    
    public function IsEnabled($record){
        global $adb;
        $query = "SELECT exchange_enabled FROM vtiger_users WHERE id = ?";
        $result = $adb->pquery($query, array($record));
        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, 'exchange_enabled');
        }
        return 0;
    }
    
    
    //type, state, date, 
}

?>
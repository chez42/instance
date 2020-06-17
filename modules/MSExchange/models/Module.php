<?php
class MSExchange_Module_Model extends Vtiger_Module_Model{
    
    function getExchangeGlobalSettings(){
        
        $adb = PearDatabase::getInstance();
        
        $result = $adb->pquery("select * from vtiger_msexchange_global_settings",array());
        
        if($adb->num_rows($result))
            return $adb->fetchByAssoc($result);
        else 
            return array();
    }
    
    /**
     * Function to delete MSExchange synchronization completely. Deletes all mapping information stored.
     * @param <string> $module - Module Name
     * @param <integer> $user - User Id
     */
    public function deleteSync($module, $user) {
        
        $db = PearDatabase::getInstance();
        
        $db->pquery("DELETE FROM vtiger_msexchange_sync_settings WHERE user = ? AND module = ?", array($user,$module));
        
        return;
    }
    
}
<?php
class Office365_Module_Model extends Vtiger_Module_Model{
    
    
    /**
     * Function to delete Office365 synchronization completely. Deletes all mapping information stored.
     * @param <string> $module - Module Name
     * @param <integer> $user - User Id
     */
    public function deleteSync($module, $user) {
        
        $db = PearDatabase::getInstance();
        
        $db->pquery("UPDATE vtiger_office365_sync_settings SET access_token = '', refresh_token = '', delta_token = '' WHERE user = ?", array($user));
        
        return;
    }
    
}
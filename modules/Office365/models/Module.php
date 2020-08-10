<?php
class Office365_Module_Model extends Vtiger_Module_Model{
    
    
    /**
     * Function to delete Office365 synchronization completely. Deletes all mapping information stored.
     * @param <string> $module - Module Name
     * @param <integer> $user - User Id
     */
    public function deleteSync($module, $user) {
        
        $db = PearDatabase::getInstance();
        
        $db->pquery("DELETE FROM vtiger_office365_sync_settings WHERE user = ? AND module = ?", array($user,$module));
        
        return;
    }
    
}
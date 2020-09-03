<?php
class PortfolioInformation_Statements_Model extends Vtiger_Module {
    public function GetPreparedByData($user_id){
        global $adb;
        $query = "SELECT prepared_by FROM vtiger_statement_settings WHERE entity_id = ?";
        $result = $adb->pquery($query, array($user_id));
        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, 'prepared_by');
        }
        return false;
    }

    public function SavePreparedBy($prepared_by, $content){
        global $adb;
        $query = "INSERT INTO vtiger_statement_settings (entity_id, prepared_by) 
                  VALUES (?, ?)
                  ON DUPLICATE KEY UPDATE prepared_by = VALUES(prepared_by)";
        $adb->pquery($query, array($prepared_by, $content), true);
    }
}
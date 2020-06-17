<?php

class Omniscient_SubAdmin_Model extends Vtiger_Module_Model {   
    public function HasSubAdminAccess($moduleName){
        global $adb, $log;
        $log->debug("Entering HasSubAdminAccess function");
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $role = $currentUserModel->getRole();
        $query = "SELECT * FROM vtiger_sub_admin WHERE roleid=? AND module=?";
        $result = $adb->pquery($query, array($role, $moduleName));
        if($adb->num_rows($result) > 0){
            $log->debug("Result is yes");
            $log->debug("Exiting HasSubAdminAccess function");
            return "yes";
        }
        $log->debug("Result is no");
        $log->debug("Exiting HasSubAdminAccess function");
        return "no";
    }
}

?>
<?php

class ReportSettings_Settings_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        $current_user = Users_Record_Model::getCurrentUserModel();
        
        switch($request->get('save_action')){
            case "save_setting":
                $this->SavePrintSectionSetting($current_user->get('id'), $request->get('field'), $request->get('value'));
                break;
            case "change_logo":
                $this->ChangeLogo($current_user->get('id'), $request->get('filename'));
                echo $request->get('filename');
                break;
        }
    }
    
    /**
     * Save the check box setting if we should print a given section or not
     * @global type $adb
     * @param type $user_id
     * @param type $field
     * @param type $setting
     */
    public function SavePrintSectionSetting($user_id, $field, $value){
        global $adb;
        if($value == 'true')
            $value = 1;
        else
            $value = 0;
        
        $query = "UPDATE vtiger_report_settings
                  SET {$field}=?
                  WHERE user_id = ?";
        $adb->pquery($query, array($value, $user_id));
    }

    /**
     * Change the active logo
     * @global type $adb
     * @param type $user_id
     * @param type $filename
     */
    public function ChangeLogo($user_id, $filename)
    {
        global $adb;
        $query = "UPDATE vtiger_report_settings SET logo = ? WHERE user_id = ?";
        $adb->pquery($query, array($filename, $user_id));
    }
    
    /**
     * Delete the file from the database
     * @global type $adb
     * @param type $user_id
     * @param type $filename
     */
    public function DeleteLogo($user_id, $filename)
    {
        global $adb;
        if($filename != "reportLogoConcertWM.png")
        {
            if($user_id == 1){
                $query = "DELETE FROM vtiger_report_uploads WHERE filename=?";
                $adb->pquery($query, array($filename));
            }
            else{
                $query = "DELETE FROM vtiger_report_uploads WHERE userid=? AND filename=?";
                $adb->pquery($query, array($user_id, $filename));
            }
        }
    }    
    
    /**
     * Returns the settings result for the given user
     * @global type $adb
     * @param type $user_id
     * @return type
     */
    public function GetSettings($user_id)
    {
        global $adb;
        $query = "SELECT * FROM vtiger_report_settings WHERE user_id = ?";
        $result = $adb->pquery($query, array($user_id));        

        $user_id = $adb->query_result($result, 0, "user_id");
        $logo = $adb->query_result($result, 0, "logo");
        
        return array("user_id" => $user_id,
                     "logo" => $logo);
    }
    
}

?>

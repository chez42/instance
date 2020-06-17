<?php

class ReportSettings_Settings_Model extends Vtiger_Module{
    var $uploader;
    public $settings;
    public $logo_list;
    
    public function __construct($need_upload = false, $options=null) {
        global $adb;
        $current_user = Users_Privileges_Model::getCurrentUserPrivilegesModel();
//        if($need_upload)
//            $this->uploader = new UploadHandler($options);
        $query = "SELECT * FROM vtiger_report_settings WHERE user_id = ?";
        $result = $adb->pquery($query, array($current_user->get('id')));
        if($adb->num_rows($result) == 0)
        {
            $insert = "INSERT INTO vtiger_report_settings (user_id) VALUES (?)";
            $adb->pquery($insert, array($current_user->get('id')));
        }
        
        $this->settings = $this->GetPrintSectionSetting($current_user->get('id'));
        $this->logo_list = $this->GetLogoList($current_user->get('id'));
    }
    
    public function GetPrintSectionSetting($user_id){
        global $adb;
        $query = "SELECT logo, account_details, pie_chart, other_accounts, holdings, monthly_income, performance, positions
                  FROM vtiger_report_settings
                  WHERE user_id = ?";
        $result = $adb->pquery($query, array($user_id));
        if($adb->num_rows($result) > 0)
        foreach($result AS $k => $v){
            return $v;
        }
        return 0;
    }
    
    /**
     * Get the list of logos the user has access to
     * @global type $adb
     * @param type $user_id
     * @return type
     */
    public function GetLogoList($user_id)
    {
        $current_user = Users_Record_Model::getCurrentUserModel();
        $related_ids = '';
        global $adb;
        
        if($current_user->isAdminUser()){
            $query = "SELECT filename FROM vtiger_report_uploads";
            $result = $adb->pquery($query, array());
        }
        else{
            require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

            foreach($PositionInformation_share_read_permission['GROUP'] AS $groups => $users){
                foreach($users AS $k => $v)
                    $related_ids[] = $v;
                $related_ids[] = $groups;
            }
            $questions = generateQuestionMarks($related_ids);
            $query = "SELECT filename FROM vtiger_report_uploads WHERE userid IN ({$questions})";
            $result = $adb->pquery($query, array($related_ids));
        }
        
        $logos = array();
        if($result)
            foreach($result AS $k => $v){
                $logos[$k] = $v['filename'];
            }

        return $logos;
    }
}

?>

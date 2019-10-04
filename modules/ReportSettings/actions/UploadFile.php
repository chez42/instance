<?php

require_once("libraries/uploader/server/php/UploadHandler.php");

class ReportSettings_UploadFile_Action extends Vtiger_BasicAjax_Action{
    
    public function process(Vtiger_Request $request) {
        $current_user = Users_Record_Model::getCurrentUserModel();

        $prefix = $current_user->get('id') . "_";
        
        $options = array('upload_dir'=>'/var/www/sites/vcrm2/storage/logos/',
                         'prefix'=>$prefix);
        $uploader = new UploadHandler($options);
        
        $filename = $_FILES['files'];//Get the file(s)

        if(is_array($filename['tmp_name']))
        {
            foreach($filename['tmp_name'] as $k => $v)
            {
                $name = $filename['name'][$k];
                $id = $current_user->get('id');
                $this->AddToReportUploadTable($id, "{$id}_{$name}");
            }
        }
        else
        {
            $name = $filename['name'];
            $id = $current_user->get('id');
            $this->AddToReportUploadTable($id, "{$id}_{$name}");
        }
    }
    
    /**
     * Adds the user and filename to the uploads table
     * @global type $adb
     * @param type $user_id
     * @param type $filename
     */
    public function AddToReportUploadTable($user_id, $filename)
    {
        global $adb;
        $query = "INSERT INTO vtiger_report_uploads (userid, filename) VALUES (?, ?)";
        $adb->pquery($query, array($user_id, $filename));
    }
}

?>
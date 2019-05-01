<?php

class OmniCal_WriteTaskToCRM_Action extends Vtiger_BasicAjax_Action{
    public function __construct() {
        parent::__construct();
    }
    
    public function WriteNewTaskToCRM(OmniCal_TaskInfo_Model $task){
        $recordModel = Vtiger_record_Model::getCleanInstance ('Calendar');
        $data = array();
        $data['subject'] = $task->subject;
        $data['taskstatus'] = $task->status;
        $data['sendnotification'] = 0;
        $data['activitytype'] = "Task";
        $data['visibility'] = "Private";
        $data['record_module'] = "Calendar";
        print_r($data);
//        $data['date_start'] = date("m/d/Y");
//        $data['time_start'] = date("h:i A");
//        $recordModel->set('mode', 'create');
//        $recordModel->setData($data);
//        $recordModel->save();
    }
    
    public function WriteTaskToCRM(OmniCal_TaskInfo_Model $task){
    }
}

?>
<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TaskView
 *
 * @author theshado
 */

class OmniCal_TaskView_View extends Vtiger_BasicAjax_View {
    
    public function preProcess(\Vtiger_Request $request) {
        parent::preProcess($request);
        $activity_id = $request->get('activity_id');
        $current_user = Users_Record_Model::getCurrentUserModel();
        $recordModel = Vtiger_Record_Model::getInstanceById($activity_id, 'Calendar');
        $data = $recordModel->getData();
        $task = new OmniCal_ExchangeTasks_Model();
        $task->SetImpersonation($current_user->get('user_name'));
        $response = $task->GetTaskInfo($data['task_exchange_item_id']);
        if($response){
            $updated_data = OmniCal_ExchangeTasks_Model::RequestToData($response);
            if($updated_data){
                if($data['task_exchange_change_key'] == $updated_data['task_exchange_change_key']){//If they are the same, nothing has changed in exchange
    //                echo "NO NEED TO UPDATE";
    //                exit;
                } else{
                    OmniCal_ExchangeTasks_Model::UpdateTaskInCRM($activity_id, $updated_data);
                }
            }
        }
        
//        echo $data['task_exchange_item_id'];exit;
    }
    
    public function process(Vtiger_Request $request) {
        $current_user = Users_Record_Model::getCurrentUserModel();
        $activity = new OmniCal_Activity_Model();
        $data = $activity->GetActivityData($request->get('activity_id'), "Task", $request);
        $record_model = $activity->GetActivityRecordModel($request->get('activity_id'));
        
        $field_model = $record_model->getField('assigned_user_id');
        $status_model = $record_model->getField('taskstatus');
        
        $recordModel = Vtiger_Record_Model::getCleanInstance('Calendar');
        $userRecordStructure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($record_model, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_QUICKCREATE);
        $recordStructure = $userRecordStructure->getStructure();
        $contact_list = $activity->GetActivityContacts($request->get('activity_id'), $request->get('record'));

        $parent = $request->get('record');
        if(!$data['parent_id'])
            $data['parent_info'] = $activity->GetActivityParentInfo($parent);
        else
            $data['parent_info'] = $activity->GetActivityParentInfo($data['parent_id']);
        
        if($request->get('activity_id')){
            $recurring = new OmniCal_Recurring_Model();
            $recurring_info = $recurring->GetSerializedArray($record_model);
        }
        
        $viewer = $this->getViewer($request);
        
        $reminder = $data['set_reminder'];//$activity->HasReminder($request->get('activity_id'));
        $data['description'] = htmlspecialchars_decode($data['description']);
        
        $temp = new Omniscient_SubAdmin_Model();
        $temp->HasSubAdminAccess("Contacts");
        
        if(!$data['assigned_user_id'])
            $data['assigned_user_id'] = $current_user->get('id');
        $viewer->assign("DATA", $data);
        $viewer->assign("SETREMINDER", $reminder);
        $viewer->assign("CONTACT_LIST", $contact_list);
        $viewer->assign("USER_MODEL", $current_user);
        $viewer->assign("FIELD_MODEL", $field_model);
        $viewer->assign("STATUS_MODEL", $status_model);
        $viewer->assign("RECORDSTRUCTURE", $recordStructure);
        $viewer->assign("STYLES", $this->getHeaderCss($request));
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $viewer->assign("RECURRING_INFORMATION", $record_model->getRecurrenceInformation());
        $viewer->assign("RECURRING_MODULE", "Events");
        $viewer->assign("STOP_DATE", $recurring_info['calendar_repeat_limit_date']);
        
//        getcontact
        $output = $viewer->view('TaskView.tpl', "OmniCal", true);//False makes it echo
        return $output;
    }

    public function getHeaderScripts(Vtiger_Request $request) {
            $moduleName = $request->getModule();
            $jsFileNames = array(
                "libraries.jquery.ckeditor.ckeditor",
                "libraries.jquery.ckeditor.adapters.jquery",
                "~/libraries/jquery/cookie/jquery.cookie.js",
                'modules.Vtiger.resources.CkEditor',
                "modules.OmniCal.resources.ActivityInteraction",
                "modules.OmniCal.resources.Recurring", // . = delimiter
                "modules.OmniCal.resources.TaskView",
            );
            $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
            return $jsScriptInstances;
    }
    
    public function getHeaderCss(Vtiger_Request $request) {
            $headerCssInstances = parent::getHeaderCss($request);
            $cssFileNames = array(
                    '~/layouts/vlayout/modules/OmniCal/css/MasterActivities.css',
                    '~/layouts/vlayout/modules/OmniCal/css/TaskView.css',
            );
            $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
            return $cssInstances;
    }
}

?>

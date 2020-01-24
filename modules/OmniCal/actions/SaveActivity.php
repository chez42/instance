<?php
/*
'module':'OmniCal', 'action':'SaveActivity', 'activity_id':activity_id, 'subject':subject, 'status':status, 
'assigned_to_id':assigned_to_id, 'set_reminder':set_reminder, 'date_range_start':date_range_start, 'time_range_start':time_range_start,
'contact_list':contact_list, 'description':description
 */
/*
Array ( 
 * [subject] => Test Task 
 * [assigned_user_id] => 22830 
 * [date_start] => 2014-04-23 
 * [time_start] => 19:08:00 
 * [time_end] => 
 * [due_date] => 2014-04-23 
 * [parent_id] => 
 * [contact_id] => 
 * [taskstatus] => Planned 
 * [eventstatus] => 
 * [taskpriority] => 
 * [sendnotification] => 0 
 * [createdtime] => 2014-04-23 19:08:47 
 * [modifiedtime] => 2014-04-23 21:30:02 
 * [activitytype] => Task 
 * [visibility] => Private 
 * [description] => 
 * [duration_hours] => 
 * [duration_minutes] => 
 * [location] => 
 * [reminder_time] => 
 * [recurringtype] => 
 * [notime] => 0 
 * [modifiedby] => 22830 
 * [record_id] => 1524156 
 * [record_module] => Calendar 
 * [id] => 1524156 )
 */
class OmniCal_SaveActivity_Action extends Vtiger_SaveAjax_Action{
    public function process(Vtiger_Request $request) {
        //Decide what to do with the incoming save request
        switch($request->get('event')){
            case "mark_completed"://Mark the activity_id as completed
                $this->MarkCompleted($request->get('activity_id'));
                break;
            case "delete"://Mark the activity_id as deleted
                $this->DeleteActivity($request->get('activity_id'));
                break;
            case "StatusChange":
                $this->UpdateStatus($request);
            default://By default, we save the entire thing with any updates that may have been made
                switch($request->get('activitytype')){
                    case "Task":
                        $this->SaveTask($request);
                        break;
                    case "Event":
                        $this->SaveEvent($request);
                        break;
                }
                break;
        }
    }
    
    public function UpdateStatus($request){
        global $adb;
        $task_status = $request->get('taskstatus');
        $event_status = $request->get('eventstatus');
        $selected_tasks = $request->get('selected_tasks');
        $selected_events = $request->get('selected_events');
        $query = "UPDATE vtiger_activity SET status = ? WHERE activityid = ?";
        foreach($selected_tasks AS $k => $v){
            $adb->pquery($query, array($task_status, $v));
        }
        
        $query = "UPDATE vtiger_activity SET eventstatus = ? WHERE activityid = ?";
        foreach($selected_events AS $k => $v){
            $adb->pquery($query, array($event_status, $v));
        }
    }
    
    public function MarkCompleted($activity_id){
        global $adb;
        $query = "UPDATE vtiger_activity
                  SET status='Completed'
                  WHERE activityid=?";
        $adb->pquery($query, array($activity_id));
    }
    
    public function DeleteActivity($activity_id){
        global $adb;
        $query = "UPDATE vtiger_crmentity
                  SET deleted='1'
                  WHERE crmid=?";
        $adb->pquery($query, array($activity_id));
        $ids = array();
        $exchange_info = OmniCal_CRMExchangeHandler_Model::GetActivityIdAndChangeKey($activity_id);
        if($exchange_info){
            $ids[] = $exchange_info['id'];
            $tmp = new OmniCal_ExchangeEws_Model();
            $current_user = Users_Record_Model::getCurrentUserModel();
            $tmp->SetImpersonation($current_user->get('user_name'));
            $tmp->DeleteItemsFromExchange($ids);
        }
    }
    
    /**
     * Save a task
     * @param Vtiger_Request $request
     */
    public function SaveTask(Vtiger_Request $request){
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $activity_id = $request->get('activity_id');
        $subject = $request->get('subject');
        $status = $request->get('status');
        $assigned_to_id = $request->get('assigned_to_id');
        $set_reminder = $request->get('set_reminder');
        $date_range_start = $request->get('date_range_start');
        $time_range_start = $request->get('time_range_start');
        $contact_list = $request->get('contact_list');
        $description = $request->get('description');
        $parent_id = $request->get('parent');
        
        $converted_date = date('Y-m-d H:i:s', strtotime($date_range_start . ' ' . $time_range_start));
        $converted_date = new DateTime(gmdate('Y-m-d H:i:s', strtotime($converted_date . $currentUserModel->get('time_zone'))));
        
        $activity = new OmniCal_Activity_Model();

        if($parent_id){
            $tmp_parent = $activity->GetActivityParentInfo($parent_id);
            if($tmp_parent['setype'] == 'Contacts'){
                $contact_list[] = array('id' => $parent_id);
            }
        }

        $record_model = $activity->GetActivityRecordModel($request->get('activity_id'));
        $data = $activity->GetActivityData($request->get('activity_id'), "Task", $request);
        $data['subject'] = $subject;
        $data['assigned_user_id'] = $assigned_to_id;
        $data['taskstatus'] = $status;
        $data['description'] = $description;        
        $data['date_start'] = $converted_date->format("Y-m-d");
        $data['time_start'] = $converted_date->format("H:i:s");
        $data['parent_id'] = $parent_id;
        $data['update_exchange'] = 1;
        
        if($set_reminder == 'Yes')
            $data['set_reminder'] = 1;
        else
            $data['set_reminder'] = 0;
                
        if($activity_id != 0){
            $record_model->setData($data);
            $record_model->set('mode', 'edit');
            $activity->unlinkDependencies($activity_id);
            $record_model->save();
        }
        else{
            $record_model->set('mode', 'create');
            $record_model->setData($data);
            $record_model->save();
        }
        
        $id = $record_model->get('id');
        $activity->SaveActivityContacts($id, $contact_list);
        $serialized = $request->get('recurring_info');
        $serialized .= "&date_start={$date_range_start}&time_start={$time_range_start}";
        $recurring = new OmniCal_Recurring_Model();
        $recurObj = $recurring->getRecurringObjValue($serialized);
        
        if(is_object($recurObj)){
            $recurring->insertIntoRecurringTable($recurObj, $record_model, $serialized);
        }
        
        $set_date = $recurring->GetReminderDate($record_model, $recurObj);
        if($data['set_reminder']){
            $activity->UpdateActivityReminderTime($id, $set_date, $data['time_start'], 0);//Enable the reminder
            $recurring->SaveActivityClones($record_model, $recurObj);
        }
        else{
            $activity->UpdateActivityReminderTime($id, $set_date, $data['time_start'], 1);//Disable the reminder
        }
    }

    /**
     * Save an event
     * @param Vtiger_Request $request
     */
    public function SaveEvent(Vtiger_Request $request){
        global $log;
        $log->debug("SAVING EVENT...");
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $activity_id = $request->get('activity_id');
        $subject = $request->get('subject');
        $location = $request->get('location');
        $activitytype = $request->get('activity_type_option');// Call/Meeting
        $date_range_start = $request->get('date_range_start');
        $time_range_start = $request->get('time_range_start');
        $date_range_end = $request->get('date_range_end');
        $time_range_end = $request->get('time_range_end');
        $status = $request->get('status');
        $priority = $request->get('priority');
        $set_reminder = $request->get('set_reminder');
        $days = $request->get('remdays');
        $hours = $request->get('remhrs');
        $minutes = $request->get('remmin');
        $sendnotification = $request->get('sendnotification') == "Yes" ? 1 : 0;
        $assigned_to_id = $request->get('assigned_to_id');
        $contact_list = $request->get('contact_list');
        $selected_users = $request->get('selected_users');//inviteesid should be the data name?  separate by ;
        $contact_attendees = $request->get('contact_attendees');
        $manual_attendees = $request->get('manual_attendees');
        $description = $request->get('description');
        $parent_id = $request->get('parent');
        $index = $request->get('index');
        $converted_start = date('Y-m-d H:i:s', strtotime($date_range_start . ' ' . $time_range_start));
        $converted_start = new DateTime(gmdate('Y-m-d H:i:s', strtotime($converted_start . $currentUserModel->get('time_zone'))));        
        $converted_end = date('Y-m-d H:i:s', strtotime($date_range_end . ' ' . $time_range_end));
        $converted_end = new DateTime(gmdate('Y-m-d H:i:s', strtotime($converted_end . $currentUserModel->get('time_zone'))));

        $activity = new OmniCal_Activity_Model();
        $record_model = $activity->GetActivityRecordModel($request->get('activity_id'));
        $data = $activity->GetActivityData($request->get('activity_id'), "Event", $request);
        
        if($request->get('single_edit') == 1){//This is a single edit activity from a series, so needs to be an exception
            $master_activity = $activity->GetMasterActivityFromChild($activity_id);
            if(!$master_activity){//This event doesn't have a master activity, so it must be new
                $master_activity = $request->get('activity_id');
                $activity_id = 0;//We are no longer editing an activity, we are creating a new one
            }
            $data['task_exchange_item_id'] = null;
            $data['task_exchange_change_key'] = null;
        }
        
        if(!$master_activity)
            $master_activity = $data['master_activity'];
        
        $data['subject'] = $subject;
        $data['assigned_user_id'] = $assigned_to_id;
        $data['description'] = $description;
        $data['date_start'] = $converted_start->format("Y-m-d");
        $data['time_start'] = $converted_start->format("H:i:s");
        $data['due_date'] = $converted_end->format("Y-m-d");
        $data['time_end'] = $converted_end->format("H:i:s");
        $data['taskstatus'] = $status;
        $data['eventstatus'] = $status;
        $data['taskpriority'] = $priority;
        $data['sendnotification'] = $sendnotification;
        $data['activitytype'] = $activitytype;
        $data['visibility'] = "Private";
        $data['description'] = $description;
        $data['location'] = $location;
        $data['parent_id'] = $parent_id;
        $data['reminder_time'] = $minutes;
        $data['master_activity'] = $master_activity;
        $data['update_exchange'] = 1;

//        if($set_reminder == 'Yes')
        if($minutes != 'Never')
            $data['set_reminder'] = 1;
        else
            $data['set_reminder'] = 0;
        
        $record_model->setData($data);
        if($activity_id != 0){
            $record_model->set('mode', 'edit');
            $record_model->set('id', $activity_id);
            $activity->unlinkDependencies($activity_id);
            $invitees = new Activity();
            $invitees->id = $activity_id;
            $invitees->insertIntoInviteeTable('Calendar', $selected_users);
            $invitees->insertIntoContactInviteeTable($contact_attendees);
            $invitees->insertIntoManualInviteeTable($manual_attendees);
            OmniCal_RepeatActivities_Model::SaveRecurringInfo($activity_id, $request->get('recurring'));
            $record_model->save();
            if($minutes != 'Never')
                $reminder_time = $activity->UpdateActivityReminderTable($days, $hours, $minutes, $activity_id);
            $activity->UpdateActivityReminderTime($activity_id, $data['date_start'], $data['time_start'], 0, $reminder_time);
        }
        else{

            $record_model->set('mode', 'create');
            $invitees = new Activity();
            $invitees->id = GetSequenceNumber()+1;
            $invitees->insertIntoInviteeTable('Calendar', $selected_users);
            $invitees->insertIntoContactInviteeTable($contact_attendees);
            $invitees->insertIntoManualInviteeTable($manual_attendees);
            if($request->get('single_edit') == 1){
                OmniCal_RepeatActivities_Model::SetIgnoreDates($master_activity, $data['date_start']);
            } else{
                OmniCal_RepeatActivities_Model::SaveRecurringInfo($invitees->id, $request->get('recurring'));
            }

            $record_model->save();

            if($master_activity)//If there is a master to save against
                $activity->SaveMasterChildActivity($master_activity, $record_model->get('id'), $index);
        }
        $id = $record_model->get('id');
        $activity->SaveActivityContacts($id, $contact_list);
        $serialized = $request->get('recurring_info');
        $serialized .= "&date_start={$date_range_start}&time_start={$time_range_start}";
        $recurring = new OmniCal_Recurring_Model();
        $recurObj = $recurring->getRecurringObjValue($serialized);

        if(is_object($recurObj)){
            $recurring->insertIntoRecurringTable($recurObj, $record_model, $serialized);
        }
        
        $set_date = $recurring->GetReminderDate($record_model, $recurObj);

//        if($set_reminder == 'Yes'){//Set the reminder time to be what the user selected to be
        if($minutes != 'Never'){
            $reminder_time = $activity->UpdateActivityReminderTable($days, $hours, $minutes, $id);
            $activity->UpdateActivityReminderTime($id, $set_date, $data['time_start'], 0, $reminder_time);
            $recurring->SaveActivityClones($record_model, $recurObj);
        }
        else{
            $activity->UpdateActivityReminderTime($id, $set_date, $data['time_start'], 1, $reminder_time);
            $recurring->SaveActivityClones($record_model, $recurObj);
        }
        $log->debug("DONE SAVING");
        
        $return = array("activity_id" => $id, 
                        "recurring_info" => $request->get('recurring'));
        echo json_encode($return);
    }
}

?>
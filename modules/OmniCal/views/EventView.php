<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EventView
 *
 * @author theshado
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
class OmniCal_EventView_View extends Vtiger_BasicAjax_View {
    
    public function preProcess(\Vtiger_Request $request) {
        parent::preProcess($request);
        $activity_id = $request->get('activity_id');
        $current_user = Users_Record_Model::getCurrentUserModel();
        $recordModel = Vtiger_Record_Model::getInstanceById($activity_id, 'Calendar');
        $data = $recordModel->getData();
        $event = new OmniCal_ExchangeEvent_Model();
        $event->SetImpersonation($current_user->get('user_name'));
        $response = $event->GetEventInfo($data['task_exchange_item_id']);
        $required_attendees = $response->ResponseMessages->GetItemResponseMessage->Items->CalendarItem->RequiredAttendees->Attendee;
        if($required_attendees)
            $request->set('attendees', json_encode($required_attendees));
        if($response){
            $updated_data = OmniCal_ExchangeEvent_Model::RequestToData($response);
            if($updated_data){
                if($data['task_exchange_change_key'] == $updated_data['task_exchange_change_key']){//If they are the same, nothing has changed in exchange
    //                echo "NO NEED TO UPDATE";
    //                exit;
                } else{
                    $event->UpdateEventInCRM($activity_id, $updated_data);
                }
            }
        }
    }
    
    public function process(Vtiger_Request $request) {
        $invitations = $request->get('attendees');
        $attendees = array();
        $attendeelist = array();
        if($invitations){
            if($invitations['ResponseType']){
                $username = GetUsernameFromEmail($invitations['Mailbox']['EmailAddress']);
                $tmp = array("name" => $invitations['Mailbox']['Name'],
                             "email" => $invitations['Mailbox']['EmailAddress'],
                             "status" => $invitations['ResponseType'],
                             "username" => $username);
                $attendees[] = $tmp;           
                $attendeelist[] = $invitations['Mailbox']['EmailAddress'];
            }else{
                foreach($invitations AS $k => $attendee){
                    $username = GetUsernameFromEmail($attendee['Mailbox']['EmailAddress']);
                    $tmp = array("name" => $attendee['Mailbox']['Name'],
                                 "email" => $attendee['Mailbox']['EmailAddress'],
                                 "status" => $attendee['ResponseType'],
                                 "username" => $username);
                    $attendees[] = $tmp;
                    $attendeelist[] = $attendee['Mailbox']['EmailAddress'];
                }
            }
        }
        
        $attendees = OmniCal_Activity_Model::DetermineTypeFromAttendeeArray($attendees);

        $current_user = Users_Record_Model::getCurrentUserModel();
        $activity = new OmniCal_Activity_Model();
        $data = $activity->GetActivityData($request->get('activity_id'), "Event", $request);

        if($request->get('start_date'))
            $data['date_start'] = $request->get('start_date');
        if($request->get('end_date'))
            $data['due_date'] = $request->get('end_date');
        
        $data['single_edit'] = $request->get('single_edit');
        $data['is_recurring'] = $request->get('is_recurring');

        $record_model = $activity->GetActivityRecordModel($request->get('activity_id'));

        $field_model = $record_model->getField('assigned_user_id');
        $status_model = $record_model->getField('eventstatus');
        $type_model = $record_model->getField('activitytype');
        $priority_model = $record_model->getField('taskpriority');
        $user_select_model = $record_model->getField('selectedusers');  
        $reminder_model = $record_model->getField('reminder_time');
        $reminder_time = $record_model->get("reminder_time");
        $parent_model = $record_model->getField('parent_id');

        $recordModel = Vtiger_Record_Model::getCleanInstance('Calendar');
        $userRecordStructure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($record_model, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_QUICKCREATE);
        $recordStructure = $userRecordStructure->getStructure();                
        $contact_list = $activity->GetActivityContacts($request->get('activity_id'), $request->get('record'));
////Original to line below        $accessibleUsers = $current_user->getAccessibleUsers();
        $accessibleUsers = $current_user->getAccessibleUsersForModule('Contacts');
        
        $parent = $request->get('record');
        if(!$data['parent_id'])//The activity doesn't already have a parent, so assume we are getting it via the record passed in
            $data['parent_info'] = $activity->GetActivityParentInfo($parent);
        else
            $data['parent_info'] = $activity->GetActivityParentInfo($data['parent_id']);//The activity has a record already, so use it

        if($request->get('activity_id')){
            $recurring = new OmniCal_Recurring_Model();
            $recurring_info = $recurring->GetSerializedArray($record_model);
        }

        $viewer = $this->getViewer($request);

        $data['recurring_info'] = $activity->GetRecurringInfo($request->get('activity_id'));
        $data['recurring_days'] = $activity->GetSplitDays($request->get('activity_id'));
        if($data['recurring_info'])
            $data['is_recurring'] = 1;
//          echo $parent_model->get('fieldvalue');exit;
        $reminder = $data['set_reminder'];//$activity->HasReminder($request->get('activity_id'));
        $data['description'] = htmlspecialchars_decode($data['description']);
        $invitees = $activity->getInvities($request->get('activity_id'));
        $invitee_list = array();
        foreach($invitees AS $k => $v){
            $invitee_list[$v] = $activity->GetAddedUsersInfo($v);
        }
        $single_edit = $request->get('single_edit');
        if(!$single_edit)
            if($data['master_activity']){
                $single_edit = 1;
                $data['single_edit'] = 1;
            }

/*        $invited_contacts_array = $activity->getContactInvities($request->get('activity_id'));
        $invited_manual_array  = $activity->getManualInvities($request->get('activity_id'));
        $attendee_list_array = $attendees;
        foreach($invited_contacts_array AS $k => $v){
            $attendee_list_array[] = $v;
        }
        
        foreach($invited_manual_array AS $k => $v)
            $attendee_list_array[] = $v;*/

        $invited_contacts = json_encode($activity->getContactInvities($request->get('activity_id')));
        $invited_manual   = json_encode($activity->getManualInvities($request->get('activity_id')));
//print_r($contact_list);exit;
        if(!$data['assigned_user_id'])
            $data['assigned_user_id'] = $current_user->get('id');
        $viewer->assign("SINGLE_EDIT", $single_edit);//A child recurring event can be single edited
        $viewer->assign("INDEX", $request->get('index'));
        $viewer->assign("DATA", $data);
        $viewer->assign("SETREMINDER", $reminder);
        $viewer->assign("CONTACT_LIST", $contact_list);
        $viewer->assign("USER_MODEL", $current_user);
        $viewer->assign("PARENT_MODEL", $parent_model);
        $viewer->assign("FIELD_MODEL", $field_model);
        $viewer->assign("STATUS_MODEL", $status_model);
        $viewer->assign("TYPE_MODEL", $type_model);
        $viewer->assign("PRIORITY_MODEL", $priority_model);
        $viewer->assign("RECORDSTRUCTURE", $recordStructure);
        $viewer->assign("USER_SELECT_MODEL", $user_select_model);
        $viewer->assign("REMINDER_MODEL", $reminder_model);
        $viewer->assign("REMINDER_TIME", $reminder_time);
        $viewer->assign('ACCESSIBLE_USERS', $accessibleUsers);
        $viewer->assign("CURRENT_USER", $current_user);
        $viewer->assign('INVITIES_SELECTED', $activity->getInvities($request->get('activity_id')));
        $viewer->assign("INVITED_CONTACTS", $invited_contacts);
        $viewer->assign("INVITED_MANUAL", $invited_manual);
//        $viewer->assign("RECURRING_INFORMATION", $record_model->getRecurrenceInformation());
        $viewer->assign("RECURRING_MODULE", "Events");
        $viewer->assign("STOP_DATE", $recurring_info['calendar_repeat_limit_date']);
        $viewer->assign("ATTENDEES", $attendees);
        $viewer->assign("ATTENDEE_LIST", json_encode($attendeelist));
        $viewer->assign("ATTENDEE_LIST_ARRAY", $attendees);
        $viewer->assign("STYLES", $this->getHeaderCss($request));
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
//print_r($data);
        $output = $viewer->view('NewEventView.tpl', "OmniCal", true);//False makes it echo
        return $output;
    }

    public function getHeaderScripts(Vtiger_Request $request) {
            $moduleName = $request->getModule();
            $jsFileNames = array(
                "~/libraries/jquery/osx/osx.js",
                "~/libraries/jquery/accordion/multiaccordion.jquery.min.js",
                "~/libraries/jquery/cookie/jquery.cookie.js",
                "modules.OmniCal.resources.Accordion",
                "modules.OmniCal.resources.NewInteraction",// . = delimiter
                "modules.OmniCal.resources.ActivityInteraction",// . = delimiter
                "modules.OmniCal.resources.NewRecurrence",
            );
            $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
            return $jsScriptInstances;
    }
    
    public function getHeaderCss(Vtiger_Request $request) {
            $headerCssInstances = parent::getHeaderCss($request);
            $cssFileNames = array(
                '~/layouts/vlayout/modules/OmniCal/css/NewEventHeader.css',
                '~/layouts/vlayout/modules/OmniCal/css/NewEventView.css',
                '~/layouts/vlayout/modules/OmniCal/css/NewRecurrence.css',
                '~/layouts/vlayout/modules/OmniCal/css/NewUserSelect.css',
                '~/layouts/vlayout/modules/OmniCal/css/NewAppointment.css',
                //'~/libraries/jquery/accordion/multiaccordion.jquery.min.css',
            );
            $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
            return $cssInstances;
    }
}

?> 
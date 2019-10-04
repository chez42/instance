<?php

class OmniCal_ExchangeTasks_Model extends OmniCal_ExchangeEws_Model{
    public $task_info;
    public $task_ids = array();
    
    public function __construct($server = 'lanserver33', $user = 'concertglobal\concertadmin', $password = 'Consec1', $exchange_version = 'Exchange2007_SP1') {
        parent::__construct($server, $user, $password, $exchange_version);
    }
    
    /**
     * Get task info from exchange.  If no task id is specified, it will return all Tasks
     * @param type $contact_id
     * @return string
     */
    public function GetTaskInfoFromExchange($task_id=''){
        if(!isset($this->sid->PrimarySmtpAddress) && !isset($this->sid->PrincipalName))
            return 'Impersonation needs to be set';
        
        if(strlen($task_id) > 0)
            return $this->GetTaskInfo($task_id);
        else
            return $this->GetAllTasksFromExchange();
        
    }
        
    /**
     * Get's all event ID's/ChangeKey info from exchange based on the passed in sync_state of the folder (Tasks for example)
     * @param type $sync_state
     * @return int
     */
    public function GetAllEventIDsFromExchangeBySyncState(){
        $sync_state = OmniCal_CRMExchangeHandler_Model::GetSyncInfo($this->user_id, "Task");
        $request = new EWSType_SyncFolderItemsType;
        
        $request->SyncState = $sync_state[0]['state'];
        $request->MaxChangesReturned = 512;
        $request->ItemShape = new EWSType_ItemResponseShapeType;
        $request->ItemShape->BaseShape = EWSType_DefaultShapeNamesType::ID_ONLY;

        $request->SyncFolderId = new EWSType_NonEmptyArrayOfBaseFolderIdsType;
        $request->SyncFolderId->DistinguishedFolderId = new EWSType_DistinguishedFolderIdType;
        $request->SyncFolderId->DistinguishedFolderId->Id = EWSType_DistinguishedFolderIdNameType::TASKS;
        
        $response = $this->ews->SyncFolderItems($request);
        $new_sync_state = $response->ResponseMessages->SyncFolderItemsResponseMessage->SyncState;
        $changes = $response->ResponseMessages->SyncFolderItemsResponseMessage->Changes;

        OmniCal_CRMExchangeHandler_Model::UpdateSyncState($sync_state[0]['table_id'], $new_sync_state);
        $response = array();
        if(property_exists($changes, 'Create')) {
            foreach($changes->Create as $event) {
                if($event->Task->ItemId){
                    $id = $event->Task->ItemId->Id;
                    $change_key = $event->Task->ItemId->ChangeKey;
                } else{
                    $id = $event->ItemId->Id;
                    $change_key = $event->ItemId->ChangeKey;
                }
                $tmp = array('id'=>$id,
                             'changekey'=>$change_key);
                $response['create'][] = $tmp;
            }
        }
        
        // updated events
        if(property_exists($changes, 'Update')) {
            foreach($changes->Update as $event) {
                if($event->Task->ItemId){
                    $id = $event->Task->ItemId->Id;
                    $change_key = $event->Task->ItemId->ChangeKey;
                } else{
                    $id = $event->ItemId->Id;
                    $change_key = $event->ItemId->ChangeKey;
                }
                $tmp = array('id'=>$id,
                             'changekey'=>$change_key);
                $response['update'][] = $tmp;
            }
        }
        
        // deleted events
        if(property_exists($changes, 'Delete')) {
            foreach($changes->Delete as $event) {
                if($event->ItemId){
                    $id = $event->ItemId->Id;
                    $change_key = $event->Task->ItemId->ChangeKey;
                } else{
                    $id = $event->Id;
                    $change_key = $event->ChangeKey;
                }
                
                $tmp = array('id'=>$id,
                             'changekey'=>$change_key);
                $response['delete'][] = $tmp;
            }
        }
        
        return $response;
    }
    
    /**
     * Get all Tasks from exchange
     * @return type
     */
    private function GetAllTasksFromExchange(){
        return $this->GetAllEventIDsFromExchangeBySyncState();
    }
    
    public function DeleteTaskFromCRM($record){
        global $adb;
        $query = "UPDATE vtiger_crmentity SET deleted = 1 WHERE crmid = ?";
        $adb->pquery($query, array($record));
    }
    
    /**
     * Get the task infrom based on the exchange ID
     * @param type $exchange_id
     * @return int
     */
    public function GetTaskInfo($exchange_id){
        if($exchange_id){
            $request = new EWSType_GetItemType();

            $request->ItemShape = new EWSType_ItemResponseShapeType();
            $request->ItemShape->BaseShape = EWSType_DefaultShapeNamesType::ALL_PROPERTIES;
			$request->ItemShape->BodyType = EWSType_BodyTypeResponseType::TEXT;

            $request->ItemIds = new EWSType_NonEmptyArrayOfBaseItemIdsType();
            $request->ItemIds->ItemId = new EWSType_ItemIdType();
            $request->ItemIds->ItemId->Id = $exchange_id;

            $response = $this->ews->GetItem($request);   
            return $response;
        }
        return 0;
    }
    
    /**
     * Create the event based on the passed in info
     * @param type $info
     */
    public function CreateTaskInExchange($subject, $on_behalf=null, $due=null, $body=null, $reminderdue=null, $reminderStart="0", $importance="NORMAL", $sensitivity="NORMAL", 
                                                $bodytype="HTML", $category="default"){
        // Start building the request.
        $request = new EWSType_CreateItemType();
        $request->Items = new EWSType_NonEmptyArrayOfAllItemsType();
//        $request->Items->Task = new EWSType_TaskType();

        $request->SavedItemFolderId->DistinguishedFolderId->Id =EWSType_DistinguishedFolderIdNameType::TASKS;
        $request->Items->Task->Subject = $subject;
        if($on_behalf)
            $request->SavedItemFolderId->DistinguishedFolderId->Mailbox->EmailAddress = $on_behalf;
        if($body){
            $request->Items->Task->Body = new EWSType_BodyType();
            $request->Items->Task->Body->BodyType = constant("EWSType_BodyTypeResponseType::".$bodytype);
            $request->Items->Task->Body->_ = $body;
        }

        $request->Items->Task->Sensitivity = constant("EWSType_SensitivityChoicesType::".$sensitivity);
        $request->Items->Task->Categories->String = $category;
        $request->Items->Task->Importance = constant("EWSType_ImportanceChoicesType::".$importance);
        if($reminderdue){
            $request->Items->Task->ReminderDueBy = date('c',  $reminderdue);
            $request->Items->Task->ReminderMinutesBeforeStart = $reminderStart;
            $request->Items->Task->ReminderIsSet = true;
        }
        else
            $request->Items->Task->ReminderIsSet = false;
        if($due)
            $request->Items->Task->DueDate = date('c',  $due);

        //make the call
        $response = $this->ews->CreateItem($request);

        return $response;
    }
    
    /** @param string $id  		- event id
     * @param string $ckey  	- event change key
     * @param string $subject  	- event subject
     * @param string $body     	- task body
     * @param string $bodytype	- task body type (TEXT/HTML) 
     * @param int $due 		- task due date timestamp
     * @param int $reminderdue	- reminder due date timestamp
     * @param int $reminderStart	- realtive negative offset for reminder start in nimutes
     * @param string $status	- task status (enumarted in TaskStatusType)
     * @param int $percentComplete	- task complitionprocentage
     * @param string $sensitivity	- task sensitivity (enumarted in SensitivityChoicesType)
     * @param string $importance	- task importance (enumarted in ImportanceChoicesType)
     * @param string $category	- task category
     * 
     * @return object response
     */	
    public function UpdateTaskInExchange($id, $ckey, $subject=null, $body=null, $bodytype="HTML", $due=null, 
                                         $reminderdue=null, $reminderStart=null, $status=null, $percentComplete=null,
                                         $sensitivity=null, $importance=null,  $category=null){
        $status = strtoupper($status);
        $updates = array(
                'task:DueDate'                    => $due ? date('c', $due) : null,
                'task:Status'                     => $status ? constant("EWSType_TaskStatusType::".$status) : null,
                'task:Sensitivity'                => $sensitivity ? constant("EWSType_SensitivityChoicesType::".$sensitivity) : null,
                'item:Importance'                 => $importance ? constant("EWSType_ImportanceChoicesType::".$importance) : null,
                'item:Subject'                    => $subject,
                'task:PercentComplete'            => $percentComplete,
                'item:ReminderDueBy'              => $reminderdue ? date('c',  $reminderdue) : null,
                'item:ReminderMinutesBeforeStart' => $reminderStart,
                'item:ReminderIsSet'              => ($reminderdue || $reminderStart) ? true : false,
        );

        $request = new EWSType_UpdateItemType();
        $request->MessageDisposition = 'SaveOnly';
        $request->ConflictResolution = 'AlwaysOverwrite';
        $request->ItemChanges = new EWSType_NonEmptyArrayOfItemChangesType();

        $request->ItemChanges->ItemChange->ItemId->Id = $id;
        $request->ItemChanges->ItemChange->ItemId->ChangeKey = $ckey;
        $request->ItemChanges->ItemChange->Updates = new EWSType_NonEmptyArrayOfItemChangeDescriptionsType();

        //popoulate update array
        $n = 0;
        $request->ItemChanges->ItemChange->Updates->SetItemField = array();
        foreach($updates as $furi => $update){
            if($update){//To save the repition of doing this for every field, we do it in this loop otherwise.  This is no different than doing the same thing as if($body) below
                $prop = array_pop(explode(':',$furi));
                $request->ItemChanges->ItemChange->Updates->SetItemField[$n]->FieldURI->FieldURI = $furi;
                $request->ItemChanges->ItemChange->Updates->SetItemField[$n]->Task->$prop = $update;
                $n++;
            }
        }
        if($category){
            $request->ItemChanges->ItemChange->Updates->SetItemField[$n]->FieldURI->FieldURI = 'item:Categories';
            $request->ItemChanges->ItemChange->Updates->SetItemField[$n]->Task->Categories->String = $category;
            $n++;
        }
        if($body){
            $request->ItemChanges->ItemChange->Updates->SetItemField[$n]->FieldURI->FieldURI = 'item:Body';
            $request->ItemChanges->ItemChange->Updates->SetItemField[$n]->Task->Body->BodyType = constant("EWSType_BodyTypeResponseType::".$bodytype);
            $request->ItemChanges->ItemChange->Updates->SetItemField[$n]->Task->Body->_ = $body;
            $n++;
        }

        //print_r($request); die();
        $response = $this->ews->UpdateItem($request);

        //$responseCode = $response->ResponseMessages->UpdateItemResponseMessage->ResponseCode;
        //$id = $response->ResponseMessages->UpdateItemResponseMessage->Items->CalendarItem->ItemId->Id;
        //$changeKey = $response->ResponseMessages->UpdateItemResponseMessage->Items->CalendarItem->ItemId->ChangeKey;	

        return $response;	    
    }
        
    public function UpdateCRMExchangeIDAndChangeKey($record, $id, $changeKey){
        global $adb;
        $query = "UPDATE vtiger_activitycf 
                  SET task_exchange_item_id = ?, 
                  task_exchange_change_key = ?
                  WHERE activityid = ?";
        $adb->pquery($query, array($id, $changeKey, $record));
    }
    
    static public function RequestToData($response){
        if($response->ResponseMessages->GetItemResponseMessage->ResponseClass == "Success"){
            $task = $response->ResponseMessages->GetItemResponseMessage->Items->Task;
            $data = array();
            
            $data['task_exchange_item_id'] = $task->ItemId->Id;
            $data['task_exchange_change_key'] = $task->ItemId->ChangeKey;
            $data['subject'] = $task->Subject;
            $data['description'] = $task->Body->_;
            $data['set_reminder'] = $task->ReminderIsSet;
            $data['taskstatus'] = $task->StatusDescription;
            $data['reminder_before_start'] = $task->ReminderMinutesBeforeStart;
            $data['date_start'] = date("Y-m-d", strtotime($task->DateTimeCreated));
            $data['time_start'] = date("H:i:s", strtotime($task->DateTimeCreated));
            if($task->ReminderIsSet){
                $data['due_date'] = date("Y-m-d", strtotime($task->ReminderDueBy));
                $data['time_end'] = date("H:i:s", strtotime($task->ReminderDueBy));
            } else {
                $data['due_date'] = date("Y-m-d", strtotime($task->DateTimeCreated));
                $data['time_end'] = date("H:i:s", strtotime($task->DateTimeCreated));                
            }
            
            return $data;
        }
//        if($response->ResponseMessages->GetItemResponseMessage->ResponseCode == "ErrorItemNotFound")
//            return "ErrorItemNotFound";
        return 0;
    }
    
    public function CreateTaskInCRM($data){        
        $recordModel = Vtiger_record_Model::getCleanInstance ('Calendar');
        $activity = new OmniCal_Activity_Model();
        $record_model = $activity->GetActivityRecordModel(0);

        $data['visibility'] = "Private";
        $data['taskpriority'] = "High";
        $data['record_module'] = "Calendar";
        $data['activitytype'] = "Task";
        $data['assigned_user_id'] = $this->user_id;
        $data['update_exchange'] = 0;
        
        $recordModel->set('mode', 'create');
        $recordModel->setData($data);
        $recordModel->save();
        
        $id = $recordModel->get('id');
        
        if($data['set_reminder']){
            $reminder_time = OmniCal_Activity_Model::UpdateActivityReminderTable(0, 0, $data['reminder_before_start'], $id);
            OmniCal_Activity_Model::UpdateActivityReminderTime($id, $data['due_date'], $data['time_end'], 0, $reminder_time);
        }

        /*
        $id = $record_model->get('id');
        OmniCal_Activity_Model::unlinkDependencies($activity_id);
        $recordModel->set('mode', $mode);
        $recordModel->save();
        if($final_data['set_reminder']){
            $reminder_time = OmniCal_Activity_Model::UpdateActivityReminderTable(0, 0, $data['reminder_before_start'], $activity_id);
            OmniCal_Activity_Model::UpdateActivityReminderTime($activity_id, $final_data['date_start'], $final_data['time_start'], 0, $reminder_time);
        }*/

/*
        $timezone = GetUserTimeZone($user_id);

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
        $converted_date = new DateTime(gmdate('Y-m-d H:i:s', strtotime($converted_date . $timezone)));
        
        $activity = new OmniCal_Activity_Model();
        $record_model = $activity->GetActivityRecordModel($request->get('activity_id'));
        $data = $activity->GetActivityData($request->get('activity_id'), "Task", $request);
        $data['subject'] = $subject;
        $data['assigned_user_id'] = $assigned_to_id;
        $data['taskstatus'] = $status;
        $data['description'] = $description;        
        $data['date_start'] = $converted_date->format("Y-m-d");
        $data['time_start'] = $converted_date->format("H:i:s");
        $data['parent_id'] = $parent_id;
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
            $activity->UpdateActivityReminderTime($activity_id, $set_date, $data['time_start'], 0);//Enable the reminder
            $recurring->SaveActivityClones($record_model, $recurObj);
        }
        else{
            $activity->UpdateActivityReminderTime($activity_id, $set_date, $data['time_start'], 1);//Disable the reminder
        }*/
    }
    
    public static function UpdateTaskInCRM($record, $updated_data){
        if($record){
            try{
                $recordModel = Vtiger_record_Model::getInstanceById($record, 'Calendar');
                $data = $recordModel->getData();
                $final_data = array_replace($data, $updated_data);//Replace data with the new stuff from exchange, but keep the rest the same.
                $final_data['update_exchange'] = 0;
                $recordModel->setData($final_data);
                OmniCal_Activity_Model::unlinkDependencies($record);
                $recordModel->set('mode', 'edit');
                $recordModel->save();
                if($final_data['set_reminder']){
                    $reminder_time = OmniCal_Activity_Model::UpdateActivityReminderTable(0, 0, $final_data['reminder_before_start'], $record);
                    OmniCal_Activity_Model::UpdateActivityReminderTime($record, $final_data['date_start'], $final_data['time_start'], 0, $reminder_time);
                }
            } catch (Exception $ex){
                return;
            }
        }
    }
    
    public function AutoCreateTasksInCrm(){
        global $adb;
        $query = "SELECT a.activityid, task_exchange_change_key "
               . "FROM vtiger_activitycf acf "
               . "LEFT JOIN vtiger_activity a ON a.activityid = acf.activityid "
               . "WHERE task_exchange_item_id IN (?) ";
        $updates = array();
        if ($this->task_info->ResponseMessages->FindItemResponseMessage->RootFolder->TotalItemsInView > 0){
            $events = $this->task_info->ResponseMessages->FindItemResponseMessage->RootFolder->Items->CalendarItem;
            foreach ($events as $k => $v){
                $updates[] = $v->ItemId->Id;
                echo "<br /><br />";
            }
        }
        
        $ids = SeparateArrayWithCommasAndSingleQuotes($updates);
        $result = $adb->pquery($query, array($ids));
        
        echo "ROWS: " . $adb->num_rows($result);
    }
    
    public function UpdateTasks(){
        // Define the event to be updated.
        $event_id = 'AAAdAGVyaWMuaG9ydG9uQG9tbmlzY2llbnRjcm0uY29tAEYAAAAAAC0mpWreNOVOshpigHKlP/YHAHLnKFpr7OlIpzKLtMyKVrEAH72n3uwAAHcqQe0kxRhMoh9Dz+KMw0QAK3wMEncAAA==';
        $event_change_key = 'DwAAABYAAABy5yhaa+zpSKcyi7TMilaxACQmCc5Q';

        $request = new EWSType_UpdateItemType();
        $request->ConflictResolution = 'AlwaysOverwrite';
        $request->SendMeetingInvitationsOrCancellations = 'SendOnlyToAll';
        $request->ItemChanges = array();

        $change = new EWSType_ItemChangeType();
        $change->ItemId = new EWSType_ItemIdType();
        $change->ItemId->Id = $event_id;
        $change->ItemId->ChangeKey = $event_change_key;

        //Update Subject Property
        $field = new EWSType_SetItemFieldType();
        $field->FieldURI = new EWSType_PathToUnindexedFieldType();
        $field->FieldURI->FieldURI = 'item:Subject';
        $field->CalendarItem = new EWSType_CalendarItemType();
        $field->CalendarItem->Subject = 'THIS IS MY NEW TASK RYAN--Updated';
        $change->Updates->SetItemField[] = $field;

        $request->ItemChanges[] = $change;

        $response = $this->ews->UpdateItem($request);
        var_dump($response);
    }
    
    public function AutoCreateUpdateDeleteCRMWithTaskInfo($task_info){
        foreach($task_info AS $action => $action_values){
            switch($action){
                case "create":
                    foreach($action_values AS $k => $v){
                    $tinfo = $this->GetTaskInfo($v['id']);
#                    print_r($tinfo);
                        if(!OmniCal_ExchangeBridge_Model::DoesItemExist($v['id']) && $this->user_id){//Make sure the item doesn't exist already and that a user id exists
                            $task = $this->GetTaskInfo($v['id']);
                            $data = $this->RequestToData($task);
                            if(is_array($data))
                                $this->CreateTaskInCRM($data);
                        }
                    }
                    break;
                case "update":
                    foreach($action_values AS $k => $v){
                        $record = OmniCal_ExchangeBridge_Model::DoesItemExist($v['id']);
                        if($record && !OmniCal_ExchangeBridge_Model::DoChangeKeysMatch($record, $v['changekey'])){//Make sure the item exists and the change key's don't match
                            $task = $this->GetTaskInfo($v['id']);
                            $data = $this->RequestToData($task);
                            $this->UpdateTaskInCRM($record, $data);
                        }                    
                    }
                    break;
                case "delete":
                    foreach($action_values AS $k => $v){
                        $record = OmniCal_ExchangeBridge_Model::DoesItemExist($v['id']);
                            $this->DeleteTaskFromCRM($record);
                    }                    
            }
        }
    }
}

?>
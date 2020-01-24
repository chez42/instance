<?php

include_once("include/utils/omniscientCustom.php");

class OmniCal_ExchangeEvent_Model extends OmniCal_ExchangeEws_Model{
    public $event_ids = array();
    
	var $max_crm_insert_events_entries = "12";
		
    public function __construct($server = 'lanserver33', $user = 'concertglobal\concertadmin', $password = 'Consec1', $exchange_version = 'Exchange2010_SP2') {
        parent::__construct($server, $user, $password, $exchange_version);
    }
    
    /**
     * Get task info from exchange.  If no task id is specified, it will return all Tasks
     * @param type $contact_id
     * @return string
     */
    public function GetEventInfoFromExchange($event_id=''){
        if(!isset($this->sid->PrimarySmtpAddress) && !isset($this->sid->PrincipalName))
            return 'Impersonation needs to be set';

        if(strlen($event_id) > 0)
            return $this->GetEventInfo($event_id);
        else
            return $this->GetAllEventsFromExchange();
    }
    
    public function GetAllEventIDsFromExchangeBySyncState(){
        try{
            $sync_state = OmniCal_CRMExchangeHandler_Model::GetSyncInfo($this->user_id, "CalendarItem");
            $request = new EWSType_SyncFolderItemsType;

            $request->SyncState = $sync_state[0]['state'];
            $request->MaxChangesReturned = 512;
            $request->ItemShape = new EWSType_ItemResponseShapeType;
            $request->ItemShape->BaseShape = EWSType_DefaultShapeNamesType::ID_ONLY;

            $request->SyncFolderId = new EWSType_NonEmptyArrayOfBaseFolderIdsType;
            $request->SyncFolderId->DistinguishedFolderId = new EWSType_DistinguishedFolderIdType;
            $request->SyncFolderId->DistinguishedFolderId->Id = EWSType_DistinguishedFolderIdNameType::CALENDAR;

            $response = $this->ews->SyncFolderItems($request);
            $new_sync_state = $response->ResponseMessages->SyncFolderItemsResponseMessage->SyncState;
            $changes = $response->ResponseMessages->SyncFolderItemsResponseMessage->Changes;

            OmniCal_CRMExchangeHandler_Model::UpdateSyncState($sync_state[0]['table_id'], $new_sync_state);
            $response = array();

            if(property_exists($changes, 'Create')) {
                foreach($changes->Create as $event) {
                    if($event->CalendarItem->ItemId){
                        $id = $event->CalendarItem->ItemId->Id;
                        $change_key = $event->CalendarItem->ItemId->ChangeKey;
                    } else{
                        $id = $event->ItemId->Id;
                        $change_key = $event->ItemId->ChangeKey;
                    }
                    $tmp = array('id'=>$id,
                                 'changekey'=>$change_key);
                    $response['create'][] = $tmp;
                }
            }

            if(property_exists($changes, 'Update')) {
                foreach($changes->Update as $event) {
                    if($event->CalendarItem->ItemId){
                        $id = $event->CalendarItem->ItemId->Id;
                        $change_key = $event->CalendarItem->ItemId->ChangeKey;
                    } else{
                        $id = $event->ItemId->Id;
                        $change_key = $event->ItemId->ChangeKey;
                    }
                    $tmp = array('id'=>$id,
                                 'changekey'=>$change_key);
                    if(OmniCal_ExchangeBridge_Model::DoesItemExist($id))
                        $response['update'][] = $tmp;
                    else
                        $response['create'][] = $tmp;
                }
            }

            if(property_exists($changes, 'Delete')) {
                foreach($changes->Delete as $event) {
                    if($event->CalendarItem->ItemId){
                        $id = $event->CalendarItem->ItemId->Id;
                        $change_key = $event->CalendarItem->ItemId->ChangeKey;
                    } else if($event->ItemId->Id){
                        $id = $event->ItemId->Id;
                        $change_key = $event->ItemId->ChangeKey;
                    }
                    else{
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
        catch (Exception $ex){
            return 0;
        }
    }
    
    /**
     * Get all Tasks from exchange
     * @return type
     */
    private function GetAllEventsFromExchange(){
        return $this->GetAllEventIDsFromExchangeBySyncState();
    }
    
    /**
     * Delete the event from the crm
     * @global type $adb
     * @param type $record
     */
    public function DeleteEventFromCRM($record){
        global $adb;
        $query = "UPDATE vtiger_crmentity SET deleted = 1 WHERE crmid = ?";
        $adb->pquery($query, array($record));
    }

    /**
     * Get the task infrom based on the exchange ID
     * @param type $exchange_id
     * @return int
     */
    public function GetEventInfo($exchange_id){
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
     * Get events created between the start date and now
     * @param type $start_date
     * @return int
     */
    public function GetEventsFromExchangeByDate($start_date){
        if(strlen($start_date) <= 0)
            return 0;
        
/*        if(strlen($start_date) <= 0)
            $start_date = '2014-07-07T00:00:00';
        if(strlen($end_date) <= 0)
            $end_date = '2016-07-07T00:00:00';
*/
        $request = new EWSType_FindItemType();

        $request->Restriction = new EWSType_RestrictionType();
        $request->Restriction->IsGreaterThanOrEqualTo = new EWSType_IsGreaterThanOrEqualToType();

        // Search on the contact's created date and time.
        $request->Restriction->IsGreaterThanOrEqualTo->FieldURI = new EWSType_PathToUnindexedFieldType();
        $request->Restriction->IsGreaterThanOrEqualTo->FieldURI->FieldURI = 'item:DateTimeCreated'; //item:LastModifiedTime

        // We only want contacts created in the last week.
        $date = new DateTime($start_date);
        $request->Restriction->IsGreaterThanOrEqualTo->FieldURIOrConstant = new EWSType_FieldURIOrConstantType();
        $request->Restriction->IsGreaterThanOrEqualTo->FieldURIOrConstant->Constant = new EWSType_ConstantValueType();
        $request->Restriction->IsGreaterThanOrEqualTo->FieldURIOrConstant->Constant->Value = $date->format('c');

        $request->ItemShape = new EWSType_ItemResponseShapeType();
        $request->ItemShape->BaseShape = EWSType_DefaultShapeNamesType::ID_ONLY;

/*        $request->CalendarView = new EWSType_CalendarViewType();
        $request->CalendarView->StartDate = $start_date;// an ISO8601 date e.g. 2012-06-12T15:18:34+03:00
        $request->CalendarView->EndDate = $end_date;// an ISO8601 date later than the above
*/
        $request->ParentFolderIds = new EWSType_NonEmptyArrayOfBaseFolderIdsType();
        $request->ParentFolderIds->DistinguishedFolderId = new EWSType_DistinguishedFolderIdType();
        $request->ParentFolderIds->DistinguishedFolderId->Id = EWSType_DistinguishedFolderIdNameType::CALENDAR;

        $request->Traversal = EWSType_ItemQueryTraversalType::SHALLOW;

        $response = $this->ews->FindItem($request);

        foreach($response->ResponseMessages->FindItemResponseMessage->RootFolder->Items->CalendarItem AS $k => $v){
            $this->event_ids[] = $v->ItemId->Id;
        }

        return $response;
    }

    private function TimeZoneMap($time_zone){
        $dateTime = new DateTime(); 
        $dateTime->setTimeZone(new DateTimeZone($time_zone)); 
        $abbr = $dateTime->format('T');
        
        switch($abbr){
            case "PST":
                return "Pacific Standard Time";
                break;
            case "EST":
                return "Eastern Standard Time";
                break;
            case "CST":
                return "Central Standard Time";
                break;
            default:
                return "Pacific Standard Time";
        }
    }
    
    private function SetTimeZoneInformation(&$request, $time_zone){
        $tz = $this->TimeZoneMap($time_zone);
        
		$request->Items->CalendarItem->StartTimeZone = new EWSType_TimeZoneDefinitionType();
        $request->Items->CalendarItem->StartTimeZone->Id = $tz;
        $request->Items->CalendarItem->StartTimeZone->Periods =  new EWSType_NonEmptyArrayOfPeriodsType();

        $period = new EWSType_PeriodType();
        $period->Bias =  'PT5H';
        $period->Name = 'Standard';
        $period->Id = "trule:Microsoft/Registry/{$tz}/2006-Standard";
        $request->Items->CalendarItem->StartTimeZone->Periods->Period[] = $period;

        $period = new EWSType_PeriodType();
        $period->Bias =  'PT4H';
        $period->Name = 'Daylight';
        $period->Id = "trule:Microsoft/Registry/{$tz}/2006-Daylight";
        $request->Items->CalendarItem->StartTimeZone->Periods->Period[] = $period;

        $period = new EWSType_PeriodType();
        $period->Bias =  'PT5H';
        $period->Name = 'Standard';
        $period->Id = "trule:Microsoft/Registry/{$tz}/2007-Standard";
        $request->Items->CalendarItem->StartTimeZone->Periods->Period[] = $period;

        $period = new EWSType_PeriodType();
        $period->Bias =  'PT4H';
        $period->Name = 'Daylight';
        $period->Id = "trule:Microsoft/Registry/{$tz}/2007-Daylight";
        $request->Items->CalendarItem->StartTimeZone->Periods->Period[] = $period;
        
        $request->Items->CalendarItem->StartTimeZone->TransitionsGroups = new EWSType_ArrayOfTransitionsGroupsType();
        $request->Items->CalendarItem->StartTimeZone->TransitionsGroups->TransitionsGroup = array();

        $group = new EWSType_ArrayOfTransitionsGroupsType();
        $group->Id = 0;

        $transition = new EWSType_RecurringDayTransitionType();
        $transition->To = new EWSType_TransitionTargetType();
        $transition->To->_ = "trule:Microsoft/Registry/{$tz}/2006-Daylight";
        $transition->To->Kind = new EWSType_KindType();
        $transition->To->Kind->_ = EWSType_KindType::PERIOD;
        $transition->TimeOffset = 'PT2H';
        $transition->Month = 4;
        $transition->Occurrence = new EWSType_OccurrenceType();
        $transition->Occurrence->_ = EWSType_OccurrenceType::FIRST_FROM_BEGINNING;
        $transition->DayOfWeek = new EWSType_DayOfWeekType();
        $transition->DayOfWeek->_ = EWSType_DayOfWeekType::SUNDAY;
        $group->RecurringDayTransition[] = $transition;

        $transition = new EWSType_RecurringDayTransitionType();
        $transition->To = new EWSType_TransitionTargetType();
        $transition->To->_ = "trule:Microsoft/Registry/{$tz}/2006-Standard";
        $transition->To->Kind = new EWSType_KindType();
        $transition->To->Kind->_ = EWSType_KindType::PERIOD;
        $transition->TimeOffset = 'PT2H';
        $transition->Month = 10;
        $transition->Occurrence = new EWSType_OccurrenceType();
        $transition->Occurrence->_ = EWSType_OccurrenceType::FIRST_FROM_END;
        $transition->DayOfWeek = new EWSType_DayOfWeekType();
        $transition->DayOfWeek->_ = EWSType_DayOfWeekType::SUNDAY;
        $group->RecurringDayTransition[] = $transition;
        $request->Items->CalendarItem->StartTimeZone->TransitionsGroups->TransitionsGroup[] = $group;

        $group = new EWSType_ArrayOfTransitionsGroupsType();
        $group->Id = 1;

        $transition = new EWSType_RecurringDayTransitionType();
        $transition->To = new EWSType_TransitionTargetType();
        $transition->To->_ = "trule:Microsoft/Registry/{$tz}/2006-Daylight";
        $transition->To->Kind = new EWSType_KindType();
        $transition->To->Kind->_ = EWSType_KindType::PERIOD;
        $transition->TimeOffset = 'PT2H';
        $transition->Month = 3;
        $transition->Occurrence = new EWSType_OccurrenceType();
        $transition->Occurrence->_ = EWSType_OccurrenceType::FIRST_FROM_BEGINNING;
        $transition->DayOfWeek = new EWSType_DayOfWeekType();
        $transition->DayOfWeek->_ = EWSType_DayOfWeekType::SUNDAY;
        $group->RecurringDayTransition[] = $transition;

        $transition = new EWSType_RecurringDayTransitionType();
        $transition->To = new EWSType_TransitionTargetType();
        $transition->To->_ = "trule:Microsoft/Registry/{$tz}/2006-Standard";
        $transition->To->Kind = new EWSType_KindType();
        $transition->To->Kind->_ = EWSType_KindType::PERIOD;
        $transition->TimeOffset = 'PT2H';
        $transition->Month = 11;
        $transition->Occurrence = new EWSType_OccurrenceType();
        $transition->Occurrence->_ = EWSType_OccurrenceType::FIRST_FROM_END;
        $transition->DayOfWeek = new EWSType_DayOfWeekType();
        $transition->DayOfWeek->_ = EWSType_DayOfWeekType::SUNDAY;
        $group->RecurringDayTransition[] = $transition;
        $request->Items->CalendarItem->StartTimeZone->TransitionsGroups->TransitionsGroup[] = $group;


        $request->Items->CalendarItem->StartTimeZone->Transitions = new EWSType_ArrayOfTransitionsType();
        $request->Items->CalendarItem->StartTimeZone->Transitions->Transition = new EWSType_TransitionType();
        $request->Items->CalendarItem->StartTimeZone->Transitions->Transition->To = new EWSType_TransitionTargetType();
        $request->Items->CalendarItem->StartTimeZone->Transitions->Transition->To->_ = 0;
        $request->Items->CalendarItem->StartTimeZone->Transitions->Transition->To->Kind = new EWSType_KindType();
        $request->Items->CalendarItem->StartTimeZone->Transitions->Transition->To->Kind = EWSType_KindType::GROUP;
    }
    
    //======================================
    // Add Calendar Event
    //======================================
    /* @param string $subject  	- event subject
     * @param int $start 		- event start timestamp
     * @param int $end		- event end time
     * @param array $attendees	- array of email addresses of invited poeople
     * @param string $body     	- event body
     * @param string $onbehalf 	- "on behalf" seneder's email
     * @param string $location	- event loaction
     * @param bool $allday		- is it an all-day event?
     * @param string $bodyType	- body format (Text/HTML)
     * @param string $category	- event actegory
     * 
     * @return object response
     */
    public function CreateEventInExchange($subject, $body=null, $start, $end, $reminder_set=false, $minutes_before=0, $attendees=null, $on_behalf=null,
                                          $location=null, $allday = false, $bodyType, $category, $time_zone, $repeat_info=null){

        $request = new EWSType_CreateItemType();
        $request->Items = new EWSType_NonEmptyArrayOfAllItemsType();
        
        $request->SendMeetingInvitations = 'SendToAllAndSaveCopy';
        $request->SavedItemFolderId->DistinguishedFolderId->Id = EWSType_DistinguishedFolderIdNameType::CALENDAR;
        if($on_behalf)
                $request->SavedItemFolderId->DistinguishedFolderId->Mailbox->EmailAddress = $on_behalf;
        $request->Items->CalendarItem->Subject = $subject;
        
        if($start)
                $request->Items->CalendarItem->Start = date('c', $start);
        if($end)
			$request->Items->CalendarItem->End = date('c',  $end);
        
		$this->SetTimeZoneInformation($request, $time_zone);

        $request->Items->CalendarItem->IsAllDayEvent = $allday;
        $request->Items->CalendarItem->LegacyFreeBusyStatus = 'Free';
        $request->Items->CalendarItem->Location = $location;
        $request->Items->CalendarItem->Categories->String = $category;
        $request->Items->CalendarItem->Body->BodyType = constant("EWSType_BodyTypeResponseType::".$bodyType);
        $request->Items->CalendarItem->Body->_ = $body;
        if($reminder_set){
            $request->Items->CalendarItem->ReminderDueBy = date('c',  $start);
            $request->Items->CalendarItem->ReminderMinutesBeforeStart = $minutes_before;
            $request->Items->CalendarItem->ReminderIsSet = true;
        }else{
            $request->Items->CalendarItem->ReminderIsSet = false;
        }
        
        for($i = 0; $i < count($attendees); $i++){
			$request->Items->CalendarItem->RequiredAttendees->Attendee[$i]->Mailbox->EmailAddress = $attendees[$i];
        }
        
        $this->AutoCreateRepeatsInExchange($request, $repeat_info);
        
		$response = $this->ews->CreateItem($request);		

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
            
			$event = $response->ResponseMessages->GetItemResponseMessage->Items->CalendarItem;
            
			$data = array();
            
            $data['task_exchange_item_id'] = $event->ItemId->Id;
            $data['task_exchange_change_key'] = $event->ItemId->ChangeKey;
            $data['subject'] = $event->Subject;
            $data['description'] = $event->Body->_;
            $data['location'] = $event->Location;
            $data['set_reminder'] = $event->ReminderIsSet;
            $data['reminder_before_start'] = $event->ReminderMinutesBeforeStart;
            $data['date_start'] = date("Y-m-d", strtotime($event->Start));
            $data['time_start'] = date("H:i:s", strtotime($event->Start));
            $data['due_date'] = date("Y-m-d", strtotime($event->End));
            $data['time_end'] = date("H:i:s", strtotime($event->End));
            $data['reminder_time'] = $event->ReminderDueBy;
            
			$data['calendar_item_type'] = $event->CalendarItemType;
			
			if($event->RequiredAttendees)
                $data['attendees'] = self::AttendeesToUserIDs($event->RequiredAttendees);
            
			if(is_object($event->Recurrence))
				$data['recurring'] = OmniCal_ExchangeRecurring_Model::SetRecurringData($event->Recurrence);
			
			if(is_object($event->ModifiedOccurrences))
				$data['modifiedOccurrences'] = OmniCal_ExchangeRecurring_Model::GetModifiedOccurrences($event);
            
			return $data;
        }
        return false;
    }

    public function AttendeesToUserIDs($attendees){
        $user_ids = array();
        if(is_array($attendees->Attendee)){
            foreach($attendees->Attendee AS $k => $v){
                $id = GetUserIDFromEmail($v->Mailbox->EmailAddress);
                if($id)
                    $user_ids[] = $id;
            }
        } else{
            $id = GetUserIDFromEmail($attendees->Attendee->Mailbox->EmailAddress);
            if($id)
                $user_ids[] = $id;
        }
        
        return $user_ids;
    }
    
    public function CreateEventInCRM($data, $parent_activity_id = false){     
        $recordModel = Vtiger_Record_Model::getCleanInstance ('Calendar');
        $activity = new OmniCal_Activity_Model();
        $record_model = $activity->GetActivityRecordModel(0);
        
        $data['visibility'] = "Private";
        $data['taskpriority'] = "High";
        $data['record_module'] = "Calendar";
        $data['activitytype'] = "Meeting";
        $data['eventstatus'] = 'Planned';
        $data['assigned_user_id'] = $this->user_id;
        $data['update_exchange'] = 0;
        
		if($parent_activity_id > 0)
			$data['parent_activity_id'] = $parent_activity_id;
		
        $recordModel->set('mode', 'create');        
        $recordModel->setData($data);
        $recordModel->save();
        
        $id = $recordModel->get('id');
        
		if(isset($data['attendees'])){
            $invitees = new Activity();
            $invitees->id = $id;
            $invitees->insertIntoInviteeTable('Calendar', $data['attendees']);
        }
        
        if(isset($data['set_reminder'])){
            $reminder_time = OmniCal_Activity_Model::UpdateActivityReminderTable(0, 0, $data['reminder_before_start'], $id);
            OmniCal_Activity_Model::UpdateActivityReminderTime($id, $data['date_start'], $data['time_start'], 0, $reminder_time);
        }
        
        /*if(isset($data['recurring']) && !empty($data['recurring'])){ All Recurring Events are consider as all day single event.
            OmniCal_ExchangeRecurring_Model::InsertRecurringInfoIntoCRM($id, $data['recurring']);
        }*/
        
        /*if($data['modifiedOccurrences']){ Comment this section bcz all the recurring events have handled in InsertRecurringInfoIntoCRM
            $this->WriteModifiedOccurrencesToCRM($data['modifiedOccurrences']);
        }*/
		
		return $id;
    }
    
    /** @param string $id  		- event id
     * @param string $ckey  	- event change key
     * @param string $subject  	- event subject
     * @param string $notification_type - Who to notify on update
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
    public function UpdateEventInExchange($id, $ckey, $notification_type, $subject=null, $body=null, $bodytype="HTML", $start=null, $end=null, 
                                          $location=null, $attendees=array(), $allday=null, $category=null, $ignoreRepeats=null){
                
        $updates = array(
                'calendar:Start'                  => $start,
                'calendar:End'                    => $end,
                'calendar:Location'               => $location,
                'calendar:IsAllDayEvent'          => $allday,
                'item:Subject'                    => $subject
/*                'task:PercentComplete'            => $percentComplete,
                'item:ReminderDueBy'              => $reminderdue ? date('c',  $reminderdue) : null,
                'item:ReminderMinutesBeforeStart' => $reminderStart,
                'item:ReminderIsSet'              => ($reminderdue || $reminderStart) ? true : false,*/
        );

        $request = new EWSType_UpdateItemType();

        $request->SendMeetingInvitationsOrCancellations = constant("EWSType_CalendarItemUpdateOperationType::$notification_type");
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
                $request->ItemChanges->ItemChange->Updates->SetItemField[$n]->CalendarItem->$prop = $update;
                $n++;
            }
        }
        $i = 0;
        if($attendees){
            $request->ItemChanges->ItemChange->Updates->SetItemField[$n]->FieldURI->FieldURI = 'calendar:RequiredAttendees';
            foreach($attendees AS $k => $v){
                $request->ItemChanges->ItemChange->Updates->SetItemField[$n]->CalendarItem->RequiredAttendees->Attendee[$i]->Mailbox->EmailAddress = $v;
                $i++;
            }
            $n++;	
        }
        if($category){
            $request->ItemChanges->ItemChange->Updates->SetItemField[$n]->FieldURI->FieldURI = 'item:Categories';
            $request->ItemChanges->ItemChange->Updates->SetItemField[$n]->CalendarItem->Categories->String = $category;
            $n++;
        }
        if($body){
            $request->ItemChanges->ItemChange->Updates->SetItemField[$n]->FieldURI->FieldURI = 'item:Body';
            $request->ItemChanges->ItemChange->Updates->SetItemField[$n]->CalendarItem->Body->BodyType = constant("EWSType_BodyTypeResponseType::".$bodytype);
            $request->ItemChanges->ItemChange->Updates->SetItemField[$n]->CalendarItem->Body->_ = $body;
            $n++;
        }

        if(!$ignoreRepeats)
            $this->AutoUpdateRepeatsToExchange($id, $request, $n);
        $n++;

        
        $response = $this->ews->UpdateItem($request);

        return $response;	    
    }
    
    public function CreateExceptionEventInExchange($masterid, $ckey, $subject=null, $body=null, $bodytype="HTML", $start=null, $end=null, 
                                          $location=null, array $attendees=array(), $allday=null, $category=null, $index){
        
        $updates = array(
                'calendar:Start'                  => $start,
                'calendar:End'                    => $end,
                'calendar:Location'               => $location,
                'calendar:IsAllDayEvent'          => $allday,
                'item:Subject'                    => $subject
/*                'task:PercentComplete'            => $percentComplete,
                'item:ReminderDueBy'              => $reminderdue ? date('c',  $reminderdue) : null,
                'item:ReminderMinutesBeforeStart' => $reminderStart,
                'item:ReminderIsSet'              => ($reminderdue || $reminderStart) ? true : false,*/
        );

        $request = new EWSType_UpdateItemType();

        $request->SendMeetingInvitationsOrCancellations = EWSType_CalendarItemUpdateOperationType::SEND_TO_ALL_AND_SAVE_COPY;
        $request->MessageDisposition = 'SaveOnly';
        $request->ConflictResolution = 'AlwaysOverwrite';
        $request->ItemChanges = new EWSType_NonEmptyArrayOfItemChangesType();

        $request->ItemChanges->ItemChange->ItemId->Id = $masterid;
        $request->ItemChanges->ItemChange->ItemId->ChangeKey = $ckey;
        $request->ItemChanges->ItemChange->Updates = new EWSType_NonEmptyArrayOfItemChangeDescriptionsType();

        $n = 0;
        $request->ItemChanges->ItemChange->Updates->SetItemField = array();
        foreach($updates as $furi => $update){
            if($update){
                $prop = array_pop(explode(':',$furi));
                $request->ItemChanges->ItemChange->Updates->SetItemField[$n]->FieldURI->FieldURI = $furi;
                $request->ItemChanges->ItemChange->Updates->SetItemField[$n]->CalendarItem->$prop = $update;
                $n++;
            }
        }
        if($attendees){
            $request->ItemChanges->ItemChange->Updates->SetItemField[$n]->FieldURI->FieldURI = 'calendar:RequiredAttendees';
            for($i = 0; $i < count($attendees); $i++){
                $request->ItemChanges->ItemChange->Updates->SetItemField[$n]->CalendarItem->RequiredAttendees->Attendee[$i]->Mailbox->EmailAddress = $attendees[$i];
            }
            $n++;	
        }
        if($category){
            $request->ItemChanges->ItemChange->Updates->SetItemField[$n]->FieldURI->FieldURI = 'item:Categories';
            $request->ItemChanges->ItemChange->Updates->SetItemField[$n]->CalendarItem->Categories->String = $category;
            $n++;
        }
        if($body){
            $request->ItemChanges->ItemChange->Updates->SetItemField[$n]->FieldURI->FieldURI = 'item:Body';
            $request->ItemChanges->ItemChange->Updates->SetItemField[$n]->CalendarItem->Body->BodyType = constant("EWSType_BodyTypeResponseType::".$bodytype);
            $request->ItemChanges->ItemChange->Updates->SetItemField[$n]->CalendarItem->Body->_ = $body;
            $n++;
        }
        
        $response = $this->ews->UpdateItem($request);

        return $response;	    
    }
    
    private function CreateDaily(&$request, $info){
        
		$request->Items->CalendarItem->Recurrence = new EWSType_RecurrenceType();
        $request->Items->CalendarItem->Recurrence->DailyRecurrence = new EWSType_IntervalRecurrencePatternBaseType(); 
        $request->Items->CalendarItem->Recurrence->DailyRecurrence->Interval = 1;
        
		$request->Items->CalendarItem->Recurrence->EndDateRecurrence = new EWSType_EndDateRecurrenceRangeType(); 
        $request->Items->CalendarItem->Recurrence->EndDateRecurrence->StartDate = $info['recurringdate'];
        $request->Items->CalendarItem->Recurrence->EndDateRecurrence->EndDate = $info['recurringenddate'];
    }
    
    private function CreateWeekly(&$request, $info){
        $request->Items->CalendarItem->Recurrence = new EWSType_RecurrenceType();
        $request->Items->CalendarItem->Recurrence->WeeklyRecurrence = new EWSType_IntervalRecurrencePatternBaseType();
        $request->Items->CalendarItem->Recurrence->WeeklyRecurrence->Interval = $info['recurringfrequency'];
        $request->Items->CalendarItem->Recurrence->WeeklyRecurrence->DaysOfWeek = new EWSType_ArrayOfStringsType();
        
		
		$days_array = explode(" ", $info['recurringinfo']);
		
		array_shift($days_array);
		
		$days = array();
		
		if(!empty($days_array)){
			foreach($days_array AS $k => $v){
				$day = strtoupper($v);
				if(strlen($day) > 3)
					$days[] = constant("EWSType_DayOfWeekType::{$day}");
			}
		}
		
        $request->Items->CalendarItem->Recurrence->WeeklyRecurrence->DaysOfWeek = $days;

        $request->Items->CalendarItem->Recurrence->EndDateRecurrence = new EWSType_EndDateRecurrenceRangeType(); 
        $request->Items->CalendarItem->Recurrence->EndDateRecurrence->StartDate = $info['recurringdate'];
        $request->Items->CalendarItem->Recurrence->EndDateRecurrence->EndDate = $info['recurringenddate'];
    }

    private function CreateRelativeMonthly(&$request, $info){
        $request->Items->CalendarItem->Recurrence = new EWSType_RecurrenceType();
        $request->Items->CalendarItem->Recurrence->RelativeMonthlyRecurrence = new EWSType_IntervalRecurrencePatternBaseType();
        $request->Items->CalendarItem->Recurrence->RelativeMonthlyRecurrence->Interval = $info['recurringfrequency'];
        
		$info_array = explode(" ", $info['recurringinfo']);
        $dayOfWeekIndex = $info_array[0];
        array_shift($info_array);

        foreach($info_array AS $k => $v){
            $day = strtoupper($v);
            if(strlen($day) > 3)
                $day = constant("EWSType_DayOfWeekType::{$day}");
        }

        $request->Items->CalendarItem->Recurrence->RelativeMonthlyRecurrence->DaysOfWeek = $day;
        $request->Items->CalendarItem->Recurrence->RelativeMonthlyRecurrence->DayOfWeekIndex = $dayOfWeekIndex;

        $request->Items->CalendarItem->Recurrence->EndDateRecurrence = new EWSType_EndDateRecurrenceRangeType(); 
        $request->Items->CalendarItem->Recurrence->EndDateRecurrence->StartDate = $info['recurringdate'];
        $request->Items->CalendarItem->Recurrence->EndDateRecurrence->EndDate = $info['recurringenddate'];
    }

    private function CreateAbsoluteMonthly(&$request, $info){
        $request->Items->CalendarItem->Recurrence = new EWSType_RecurrenceType();
        $request->Items->CalendarItem->Recurrence->AbsoluteMonthlyRecurrence = new EWSType_IntervalRecurrencePatternBaseType();
        $request->Items->CalendarItem->Recurrence->AbsoluteMonthlyRecurrence->Interval = $info['recurringfrequency'];
        $request->Items->CalendarItem->Recurrence->AbsoluteMonthlyRecurrence->DayOfMonth = $info['recurringinfo'];

        $request->Items->CalendarItem->Recurrence->EndDateRecurrence = new EWSType_EndDateRecurrenceRangeType(); 
        $request->Items->CalendarItem->Recurrence->EndDateRecurrence->StartDate = $info['recurringdate'];
        $request->Items->CalendarItem->Recurrence->EndDateRecurrence->EndDate = $info['recurringenddate'];
    }
    
    public function AutoCreateRepeatsInExchange(&$request, $info){
        switch($info['recurringtype']){
            case 'Daily':
                $this->CreateDaily($request, $info);
                break;
            case 'Weekly':
                $this->CreateWeekly($request, $info);
                break;
            case 'AbsoluteMonthly':
                $this->CreateAbsoluteMonthly($request, $info);
                break;
            case 'RelativeMonthly':
                $this->CreateRelativeMonthly($request, $info);
                break;
        }
    }
    
    private function UpdateDaily(&$request, $info, $offset){

            $request->ItemChanges->ItemChange->Updates->SetItemField[$offset]->FieldURI->FieldURI = 'calendar:Recurrence';

        $request->ItemChanges->ItemChange->Updates->SetItemField[$offset]->CalendarItem->Recurrence = new EWSType_RecurrenceType();
		$request->ItemChanges->ItemChange->Updates->SetItemField[$offset]->CalendarItem->Recurrence->DailyRecurrence = new EWSType_IntervalRecurrencePatternBaseType();
		$request->ItemChanges->ItemChange->Updates->SetItemField[$offset]->CalendarItem->Recurrence->DailyRecurrence->Interval = 1;
		
		$request->ItemChanges->ItemChange->Updates->SetItemField[$offset]->CalendarItem->Recurrence->EndDateRecurrence = new EWSType_EndDateRecurrenceRangeType();
		$request->ItemChanges->ItemChange->Updates->SetItemField[$offset]->CalendarItem->Recurrence->EndDateRecurrence->StartDate = $info['recurringdate'];
		$request->ItemChanges->ItemChange->Updates->SetItemField[$offset]->CalendarItem->Recurrence->EndDateRecurrence->EndDate = $info['recurringenddate'];

            $offset++;
            return $offset;
    }

    private function AutoUpdateRepeatsToExchange($exchange_id, &$request, $offset){
        $activity_id = OmniCal_ExchangeBridge_Model::DoesItemExist($exchange_id);
        if($activity_id){
            $info = OmniCal_RepeatActivities_Model::GetRecurringInfo($activity_id);
            if($info['recurringtype'] == 'Daily')
               return $this->UpdateDaily($request, $info, $offset);
        }
    }

    
    public function UpdateEventInCRM($record, $updated_data){
        if($record){
            try{
                $recordModel = Vtiger_Record_Model::getInstanceById($record, 'Calendar');
                $data = $recordModel->getData();
                $final_data = array_replace($data, $updated_data);
                $final_data['update_exchange'] = 0;
                OmniCal_Activity_Model::unlinkDependencies($record, 0);
                $recordModel->setData($final_data);
                $recordModel->set('mode', 'edit');
                $recordModel->save();
                if($final_data['attendees']){
                    $invitees = new Activity();
                    $invitees->id = $final_data['record_id'];
                    $invitees->insertIntoInviteeTable('Calendar', $final_data['attendees']);
                }
                if($final_data['set_reminder']){
                    $reminder_time = OmniCal_Activity_Model::UpdateActivityReminderTable(0, 0, $final_data['reminder_before_start'], $record);
                    OmniCal_Activity_Model::UpdateActivityReminderTime($record, $final_data['date_start'], $final_data['time_start'], 0, $reminder_time);
                }
                
				/*if($final_data['recurring']){
                    OmniCal_ExchangeRecurring_Model::InsertRecurringInfoIntoCRM($record, $final_data['recurring']);
                }*/
                /*if($final_data['modifiedOccurrences']){ not required bcz all recurring events are handled before
                    $this->WriteRecurringIgnore($record, $final_data['modifiedOccurrences']);
                    $this->WriteModifiedOccurrencesToCRM($final_data['modifiedOccurrences']);
                }*/
            } catch (Exception $ex){
                return;
            }
        }
    }

    private function WriteRecurringIgnore($record, $data){
        global $adb;
        $query = "DELETE FROM vtiger_recurringignore WHERE parent_id = ?";
        $adb->pquery($query, array($record));
        
        foreach($data AS $k => $v){
           if($v->Start){
                $query = "INSERT INTO vtiger_recurringignore (parent_id, start_date) VALUES (?, ?)";
                $adb->pquery($query, array($record, $v->Start));
           } else{
                $query = "INSERT INTO vtiger_recurringignore (parent_id, start_date) VALUES (?, ?)";
                $adb->pquery($query, array($record, $data[1]));
                return;
           }
        }
    }
    
    private function WriteModifiedOccurrencesToCRM($data){
        foreach($data AS $k => $v){
            $id = $v->ItemId->Id;
            if(!$id)
                $id = $v->Id;
            $event = $this->GetEventInfo($id);
            $tmp = $this->RequestToData($event);
            $activity_id = OmniCal_ExchangeBridge_Model::DoesItemExist($id);
            if(!$activity_id && $this->user_id){
                $this->CreateEventInCRM($tmp);
            } else
            if($activity_id && $this->user_id){
                $this->UpdateEventInCRM($activity_id, $tmp);
            }
        }
    }
    
    public function AutoCreateUpdateDeleteCRMWithEventInfo($event_info){
        foreach($event_info AS $action => $action_values){
            switch($action){
                case "create":
                    foreach($action_values AS $k => $v){
                        if(!OmniCal_ExchangeBridge_Model::DoesItemExist($v['id']) && $this->user_id){
                            $event = $this->GetEventInfo($v['id']);
                            $data = $this->RequestToData($event);
							if(!empty($data)){
                                $activityid = $this->CreateEventInCRM($data);
								if(isset($data['calendar_item_type']) && $data['calendar_item_type'] == "RecurringMaster"){
									$this->fetchAndCreateRecurringEvents($data, $activityid);
								}
							}
                        }
                    }
                    break;
                case "update":
                    foreach($action_values AS $k => $v){
                        $record = OmniCal_ExchangeBridge_Model::DoesItemExist($v['id']);
                        $event = $this->GetEventInfo($v['id']);
                        $data = $this->RequestToData($event);
						if($record && !OmniCal_ExchangeBridge_Model::DoChangeKeysMatch($record, $data['task_exchange_change_key'])){
                            if(isset($data['calendar_item_type']) && $data['calendar_item_type'] == 'RecurringMaster')
								$this->UpdateRecurringEventInCRM($record, $data);
							else	
								$this->UpdateEventInCRM($record, $data);
						}                    
                    }
                    break;
                case "delete":
                    foreach($action_values AS $k => $v){
                        $record = OmniCal_ExchangeBridge_Model::DoesItemExist($v['id']);
						$this->DeleteEventFromCRM($record);
						$this->checkAndDeleteRecurringEvent($record);
                    }
            }
        }
    }
    						
    function fetchAndCreateRecurringEvents($data, $parent_activity_id){
	
		$skipRecurringTypes = array("RelativeYearly", "AbsoluteYearly");
		
		if(in_array($data["recurring"]["type"], $skipRecurringTypes)) return false;
	
		/*else if($data["recurring"]["type"] == "RelativeMonthly"){ //allow only first and last and day should be numeric value
			$_REQUEST['recurringtype'] = "Monthly";
			$_REQUEST['repeatMonth'] = "day";
			
			$relativeMonthlyData = $data["recurring"]['recurring_info'];
			
			$relativeMonthlyData = explode(" ", $relativeMonthlyData);
			
			$_REQUEST['repeatMonth_daytype'] = $relativeMonthlyData['0']; 
			$_REQUEST['repeatMonth_day'] = $relativeMonthlyData[1];
		} else if($data["recurring"]["type"] == "AbsoluteYearly"){
			$_REQUEST['recurringtype'] = "Yearly";
			$end_date = $data["recurring"]['recurring_info']." ".date("Y", strtotime($data["recurring"]['end_date']));
			$end_date = strtotime($end_date);
			$end_date = date("Y-m-d", $end_date);
			$_REQUEST['calendar_repeat_limit_date'] = $end_date;
		} */
		
		if($data["recurring"]["type"] != 'RelativeMonthly'){
		
			$_REQUEST = array();
			
			$_REQUEST['recurringtype'] = $data["recurring"]["type"];
			$_REQUEST['date_start'] = $data["recurring"]["start_date"];
			$_REQUEST['calendar_repeat_limit_date'] = $data["recurring"]["end_date"];
			$_REQUEST['due_date'] = $data["recurring"]["end_date"];
			$_REQUEST['time_start'] = $data['time_start'];
			$_REQUEST['time_end'] = $data["time_end"];
			
			if(isset($data['recurring']['interval']) && $data['recurring']['interval'] != '')
				$_REQUEST['repeat_frequency'] = $data['recurring']['interval'];
			
			if($data["recurring"]["type"] == "Weekly"){
			
				$daysOfWeek = $data["recurring"]['days_of_week'];
				
				if($daysOfWeek != ''){
					$daysOfWeek = explode(" ", $daysOfWeek);
					foreach($daysOfWeek as $weekday){
						if($weekday == "Monday")	
							$_REQUEST['mon_flag'] = true;
						else if($weekday == "Tuesday")
							$_REQUEST['tue_flag'] = true;
						else if($weekday == "Wednesday")
							$_REQUEST['wed_flag'] = true;
						else if($weekday == "Thursday")
							$_REQUEST['thu_flag'] = true;
						else if($weekday == "Friday")
							$_REQUEST['fri_flag'] = true;
						else if($weekday == "Saturday")
							$_REQUEST['sat_flag'] = true;
						else if($weekday == "Sunday")
							$_REQUEST['sun_flag'] = true;
					}
				} 
			} else if($data["recurring"]["type"] == "AbsoluteMonthly"){
				$_REQUEST['recurringtype'] = "Monthly";
				$_REQUEST['repeatMonth'] = "date";
				$_REQUEST['repeatMonth_date'] = $data["recurring"]["days_of_month"];
			} 
			
			$recurObj = getrecurringObjValue();

			if($data["recurring"]["type"] == "AbsoluteMonthly"){
				
				$totalRecurrenceEvents = count($recurObj->recurringdates);
				
				if($totalRecurrenceEvents > $this->max_crm_insert_events_entries){
					$recurObj->recurringdates = array_slice($recurObj->recurringdates, 0, $this->max_crm_insert_events_entries);
				}
			}
			
			$indexes = range(1, count($recurObj->recurringdates));
		
			$data["recurring"]['recurring_info'] = $recurObj->getDBRecurringInfoString();
			$data["recurring"]["type"] = $recurObj->getRecurringType();
		
			$_REQUEST = array(); // In order to show all Recurring Event as single all day event.
			
		} else {
			
			$relativeMonthlyData = $data["recurring"]['recurring_info'];
			
			$relativeMonthlyData = explode(" ", $relativeMonthlyData);
			
			$day = $relativeMonthlyData[1];
			
			$dayType = $relativeMonthlyData[0];
			
			if($data["recurring"]["has_end"] == 1 && $data["recurring"]["end_date"] != '')
				$recurringDates = $this->getRecurringDateRange($data["recurring"]["start_date"], $data["recurring"]["end_date"], $day, $dayType, $data["recurring"]['interval']);
			else
				$recurringDates = $this->getMaxCRMInsertedRecurringDateRange($data["recurring"]["start_date"], $day, $dayType, $data["recurring"]['interval']);
			
			if(count($recurringDates) > $this->max_crm_insert_events_entries){
			
				$recurringDates = array_slice($recurringDates, 0, $this->max_crm_insert_events_entries);
			}
		
			$indexes = range(1, count($recurringDates));
		}
		
		$masterId = $data['task_exchange_item_id'];
		
		$seriesEvents = array();
		
		if(!empty($indexes)){
			foreach($indexes as $InstanceIndex){
				$response = $this->GetExceptionItem($masterId, $InstanceIndex);
				if($response->ResponseMessages->GetItemResponseMessage->ResponseClass == "Success"){
					$event = $response->ResponseMessages->GetItemResponseMessage->Items->CalendarItem;
					$seriesEvents[] = array("item_id" => $event->ItemId->Id, "change_key" => $event->ItemId->ChangeKey);
				}
			}
		}
		
		if(!empty($seriesEvents)){
			foreach($seriesEvents as $child_event){
				$child_event = $this->GetEventInfo($child_event['item_id']);
				$child_event_info = $this->RequestToData($child_event);
				if(is_array($child_event_info) && !empty($child_event_info)){
					if($data['date_start'] != $child_event_info['date_start']){
						if(isset($child_event_info['calendar_item_type']) && ($child_event_info['calendar_item_type'] == "Exception" || $child_event_info['calendar_item_type'] == "Occurrence")){
							$this->CreateEventInCRM($child_event_info, $parent_activity_id);
						}
					}
				}
			}
			if(isset($data["recurring"]["has_end"]) && $data["recurring"]["has_end"] == 0)
				$this->saveEventInRecurringScheduleEvent($parent_activity_id);
		}
	}
	
	function UpdateRecurringEventInCRM($parent_activity_id, $parent_event_data){
		
		if(!$parent_activity_id) return false;
		
		if(!isset($parent_event_data['recurring']) || empty($parent_event_data['recurring']))
			return false;
		
		if(isset($parent_event_data['modifiedOccurrences']))
			unset($parent_event_data['modifiedOccurrences']);
		
		if($parent_event_data["recurring"]["type"] != 'RelativeMonthly'){
		
			$_REQUEST = array();
			
			$_REQUEST['recurringtype'] = $parent_event_data["recurring"]["type"];
			$_REQUEST['date_start'] = $parent_event_data["recurring"]["start_date"];
			$_REQUEST['calendar_repeat_limit_date'] = $parent_event_data["recurring"]["end_date"];
			$_REQUEST['due_date'] = $parent_event_data["recurring"]["end_date"];
			$_REQUEST['time_start'] = $parent_event_data['time_start'];
			$_REQUEST['time_end'] = $parent_event_data["time_end"];
			
			if(isset($parent_event_data['recurring']['interval']) && $parent_event_data['recurring']['interval'] != '')
				$_REQUEST['repeat_frequency'] = $parent_event_data['recurring']['interval'];
			
			if($parent_event_data["recurring"]["type"] == "Weekly"){
			
				$daysOfWeek = $parent_event_data["recurring"]['days_of_week'];
				
				if($daysOfWeek != ''){
					$daysOfWeek = explode(" ", $daysOfWeek);
					foreach($daysOfWeek as $weekday){
						if($weekday == "Monday")	
							$_REQUEST['mon_flag'] = true;
						else if($weekday == "Tuesday")
							$_REQUEST['tue_flag'] = true;
						else if($weekday == "Wednesday")
							$_REQUEST['wed_flag'] = true;
						else if($weekday == "Thursday")
							$_REQUEST['thu_flag'] = true;
						else if($weekday == "Friday")
							$_REQUEST['fri_flag'] = true;
						else if($weekday == "Saturday")
							$_REQUEST['sat_flag'] = true;
						else if($weekday == "Sunday")
							$_REQUEST['sun_flag'] = true;
					}
				}
			} else if($parent_event_data["recurring"]["type"] == "AbsoluteMonthly"){
				$_REQUEST['recurringtype'] = "Monthly";
				$_REQUEST['repeatMonth'] = "date";
				$_REQUEST['repeatMonth_date'] = $parent_event_data["recurring"]["days_of_month"];
			}
			
			$recurObj = getrecurringObjValue();

			if($parent_event_data["recurring"]["type"] == "AbsoluteMonthly"){
				
				$totalRecurrenceEvents = count($recurObj->recurringdates);
				
				if($totalRecurrenceEvents > $this->max_crm_insert_events_entries){
					$recurObj->recurringdates = array_slice($recurObj->recurringdates, 0, $this->max_crm_insert_events_entries);
				}
			}
			
			$indexes = range(1, count($recurObj->recurringdates));
		
			$parent_event_data["recurring"]['recurring_info'] = $recurObj->getDBRecurringInfoString();
			$parent_event_data["recurring"]["type"] = $recurObj->getRecurringType();
		
			$_REQUEST = array(); // In order to show all Recurring Event as single all day event.
			
		} else {
			
			$relativeMonthlyData = $parent_event_data["recurring"]['recurring_info'];
			
			$relativeMonthlyData = explode(" ", $relativeMonthlyData);
			
			$day = $relativeMonthlyData[1];
			
			$dayType = $relativeMonthlyData[0];
			
			if($parent_event_data["recurring"]["has_end"] == 1 && $parent_event_data["recurring"]["end_date"] != '')
				$recurringDates = $this->getRecurringDateRange($parent_event_data["recurring"]["start_date"], $parent_event_data["recurring"]["end_date"], $day, $dayType, $parent_event_data["recurring"]['interval']);
			else
				$recurringDates = $this->getMaxCRMInsertedRecurringDateRange($parent_event_data["recurring"]["start_date"], $day, $dayType, $parent_event_data["recurring"]['interval']);
			
			if(count($recurringDates) > $this->max_crm_insert_events_entries){
			
				$recurringDates = array_slice($recurringDates, 0, $this->max_crm_insert_events_entries);
			}
		
			$indexes = range(1, count($recurringDates));
		}
		
		$childEventsItemIds = $this->getRecurringSeriesEventsIds($parent_activity_id);
		
		if(!empty($indexes)){
			
			foreach($indexes as $InstanceIndex){
			
				$response = $this->GetExceptionItem($parent_event_data['task_exchange_item_id'], $InstanceIndex);
				
				if($response->ResponseMessages->GetItemResponseMessage->ResponseClass == "Success"){
					
					$event = $response->ResponseMessages->GetItemResponseMessage->Items->CalendarItem;
					
					$exchange_itemid = $event->ItemId->Id;
					
					$event = $this->GetEventInfo($exchange_itemid);
				
					$data = $this->RequestToData($event);
					
					if($data['date_start'] != $parent_event_data['date_start']){
						if(isset($data['calendar_item_type']) && ($data['calendar_item_type'] == "Exception" || $data['calendar_item_type'] == "Occurrence")){
							if(in_array($data['task_exchange_item_id'], $childEventsItemIds)){
								$activityid = array_search($data['task_exchange_item_id'], $childEventsItemIds);
								$this->UpdateEventInCRM($activityid, $data);
								unset($childEventsItemIds[$activityid]);
							} else {
								$this->CreateEventInCRM($data, $parent_activity_id);
							}
						}
					} else {
					
						if(isset($data['task_exchange_item_id']))
							unset($data['task_exchange_item_id']);
						
						if(isset($data['task_exchange_change_key']))
							unset($data['task_exchange_change_key']);
						
						if(isset($data['recurring']))
							unset($data['recurring']);
						
						if(isset($data['modifiedOccurrences']))
							unset($data['modifiedOccurrences']);
						
						$final_data = array_replace($parent_event_data, $data);
						
						$this->UpdateEventInCRM($parent_activity_id, $final_data);
					}
				}
			}
		}
		
		if(!empty($childEventsItemIds)){
			foreach($childEventsItemIds as $activityid => $activity_itemid){
				$this->DeleteEventFromCRM($activityid);
			}
		}
	}
	
	function getRecurringSeriesEventsIds($activityid){
		
		$seriesEvents = array();
		
		$adb = PearDatabase::getInstance();
		
		$sql = "select vtiger_activity.activityid, vtiger_activitycf.task_exchange_item_id from vtiger_activity 
		inner join vtiger_activitycf on vtiger_activitycf.activityid = vtiger_activity.activityid
		inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_activitycf.activityid and vtiger_crmentity.deleted = 0
		where vtiger_activity.parent_activity_id = ?";
		
		$result = $adb->pquery($sql, array($activityid));
		
		if($adb->num_rows($result)){
			while($activity = $adb->fetchByAssoc($result)){
				$seriesEvents[$activity['activityid']] = $activity['task_exchange_item_id'];
			}			
		}
		return $seriesEvents;
	}
	
	function checkAndDeleteRecurringEvent($parent_activity_id){
		
		$adb = PearDatabase::getInstance();
		
		$result = $adb->pquery("select vtiger_activity.activityid from vtiger_activity
		inner join vtiger_activitycf on vtiger_activitycf.activityid = vtiger_activity.activityid
		inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_activitycf.activityid and vtiger_crmentity.deleted = 0
		where vtiger_activity.parent_activity_id = ?",array($parent_activity_id));
		
		if($adb->num_rows($result) > 0){
			
			$crmids = array();
			
			while($activity = $adb->fetchByAssoc($result)){
				$crmids[] = $activity['activityid'];
			}		

			if(!empty($crmids))
				$adb->pquery("update vtiger_crmentity set vtiger_crmentity.deleted = 1 where crmid in (".generateQuestionMarks($crmids).")",array($crmids));
		}
	}
	
	function saveEventInRecurringScheduleEvent($master_activity_id){
		
		$adb = PearDatabase::getInstance();
		
		if(!Vtiger_Utils::CheckTable('vtiger_recurring_events_schedule')){
						
			Vtiger_Utils::CreateTable('vtiger_recurring_events_schedule',
			'(`id` INT NOT NULL AUTO_INCREMENT ,
			`activityid` INT NULL DEFAULT NULL ,
			PRIMARY KEY (  `id` ))',
			true);
		}
		
		$result = $adb->pquery("select * from vtiger_recurring_events_schedule where activityid = ?",array($master_activity_id));
		
		if(!$adb->num_rows($result))
			$adb->pquery("insert into vtiger_recurring_events_schedule (activityid) VALUES (?)",array($master_activity_id));
	}
	
	// StartDate = '2017-06-02', $endDate = '2017-08-06', $day = 'saturday', $datType = first, second, third, last
	
	function getRecurringDateRange($startDate, $endDate, $day, $dayType, $recurring_interval = 1){
			
		$start = new DateTime($startDate);
		$start->modify('first day of this month');
		
		$end = new DateTime($endDate);
		$end->modify('first day of next month');
		
		$interval = DateInterval::createFromDateString($recurring_interval.' month');
		
		$period   = new DatePeriod($start, $interval, $end);

		$dateRange = array();
		
		foreach ($period as $dt) {
			
			$dayType = strtolower($dayType);
			
			$dt->modify($dayType. " ". $day." of this month");
			
			$recurringDate = $dt->format("Y-m-d");
	
			if($recurringDate >= $startDate && $recurringDate <= $endDate)
				$dateRange[] = $recurringDate;
		}
		
		return $dateRange;
	}
	
	function getMaxCRMInsertedRecurringDateRange($startDate, $day, $dayType, $recurring_interval = 1){
		
		$start = new DateTime($startDate);
		$start->modify('first day of this month');
		
		$endDate = date('Y-m-d', strtotime("+12 years", strtotime($start->format('Y-m-d'))));
		
		$end = new DateTime($endDate);
		$end->modify('first day of next month');
		
		$interval = DateInterval::createFromDateString($recurring_interval.' month');
		
		$period   = new DatePeriod($start, $interval, $end);

		$dateRange = array();
		
		$index = 0;
		
		foreach ($period as $dt) {
	
			$dayType = strtolower($dayType);
			
			$dt->modify($dayType. " ". $day." of this month");
			
			$recurringDate = $dt->format("Y-m-d");
	
			if($recurringDate >= $startDate && $recurringDate <= $endDate){
				$dateRange[] = $recurringDate;
				$index++;
			}
			
			if($index >= $this->max_crm_insert_events_entries)
				break;
		}
		
		return $dateRange;
	}
}

?>
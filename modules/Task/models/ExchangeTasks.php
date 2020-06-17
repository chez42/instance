<?php

class Task_ExchangeTasks_Model extends OmniCal_ExchangeEws_Model{
    public $task_info;
    public $task_ids = array();
	
	public $timeZone = false;
    
    public function __construct($server = 'lanserver33', $user = 'concertglobal\concertadmin', $password = 'Consec1', $exchange_version = 'Exchange2010_SP2') {
        parent::__construct($server, $user, $password, $exchange_version);
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
        return false;
    }
    
    /**
     * Create the event based on the passed in info
     * @param type $info
     */
    public function CreateTaskInExchange($task_info){
					
		$default_values = array(
			"subject" => null,
			"on_behalf" => null,
			"due" => null,
			"body" => null,
			"reminder_due" => null,
			"reminder_start" => 0,
			"status" => null,
			"importance" => "NORMAL",
			"sensitivity" => "Normal",
			"bodytype" => "TEXT", 
			"category" => "default"
		);
		
		$task_info = array_replace($default_values, $task_info);
		
		$subject = $task_info['subject'];
		
		$on_behalf = $task_info['on_behalf']; 
		
		$due = $task_info['due'];
		
		$body = $task_info['body'];
		
		$reminderdue = $task_info['reminder_due'];
		
		$reminderStart = $task_info['reminder_start'];
		
		$status = $task_info['status'];
		
		$importance = $task_info['importance']; 
		
		$sensitivity = $task_info['sensitivity'];
		
		$bodytype = $task_info['bodytype']; 
		
		$category = $task_info['category'];
		
		$request = new EWSType_CreateItemType();
        $request->Items = new EWSType_NonEmptyArrayOfAllItemsType();

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

		if($status){
			
			$status = strtoupper($status);
			
			if($status)
				$status = str_replace(" ", "", $status);
			
			$request->Items->Task->Status = constant("EWSType_TaskStatusType::".$status);
        }
		
		$time_zone = $this->timeZone;
		
		if(!$time_zone)
			$time_zone = "UTC";
			
		$this->SetTimeZoneInformation($request, $time_zone);

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
    public function UpdateTaskInExchange($id, $ckey, $updated_data){
										 
		$default_values = array(
			"subject" => null,
			"due" => null,
			"body" => null,
			"reminder_due" => null,
			"reminder_start" => 0,
			"status" => null,
			"importance" => "NORMAL",
			"sensitivity" => "Normal",
			"bodytype" => "HTML", 
			"category" => "default",
			"percentComplete" => null,
		);
		
		$updated_data = array_replace($default_values, $updated_data);
		
		$subject = $updated_data['subject']; 
		
		$body = $updated_data['body'];
		
		$bodytype = $updated_data['bodytype']; 
		
		$due = $updated_data['due'];
        
		$reminderdue = $updated_data['reminder_due'];
		
		$reminderStart = $updated_data['reminder_start'];
		
		$status = $updated_data['status'];
		
		$percentComplete = $updated_data['percentComplete'];
		
		$sensitivity = $updated_data['sensitivity'];
		
		$importance = $updated_data['importance']; 
		
		$category = $updated_data['category'];
        
		$status = strtoupper($status);
		
		if($status)
			$status = str_replace(" ", "", $status);
			
        $updates = array(
                'task:DueDate'                    => $due,
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

        $response = $this->ews->UpdateItem($request);

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
            $data['task_status'] = $task->StatusDescription;
            $data['reminder_before_start'] = $task->ReminderMinutesBeforeStart;
            $data['date_start'] = date("Y-m-d", strtotime($task->DateTimeCreated));
            $data['time_start'] = date("H:i:s", strtotime($task->DateTimeCreated));
            if($task->ReminderIsSet){
                $data['due_date'] = date("Y-m-d", strtotime($task->ReminderDueBy));
                $data['time_end'] = date("H:i:s", strtotime($task->ReminderDueBy));
            } else if($task->DueDate){
				$data['due_date'] = date("Y-m-d", strtotime($task->DueDate));
                $data['time_end'] = date("H:i:s", strtotime($task->DueDate));                
            } else {
				$data['due_date'] = date("Y-m-d", strtotime($task->DateTimeCreated));
                $data['time_end'] = date("H:i:s", strtotime($task->DateTimeCreated));                
            }
            
			$endDate = Vtiger_Datetime_UIType::getDisplayDateTimeValue($data['due_date']." ". $data['time_end']);
		
			list($dueDate, $endTime) = explode(' ', $endDate);

			$dueDate = DateTimeField::convertToDBFormat($dueDate);
			$data['due_date'] = $dueDate;
			
            return $data;
        }
        return false;
    }
    
    public function CreateTaskInCRM($data){        
        
		$recordModel = Vtiger_Record_Model::getCleanInstance('Task');

        $data['task_priority'] = "High";
        $data['record_module'] = "Task";
        $data['assigned_user_id'] = $this->user_id;
        $data['update_exchange'] = 0;
        $data['time_end'] = "";
		
        $recordModel->set('mode', 'create');
        $recordModel->setData($data);
        $recordModel->save();
        
        $id = $recordModel->get('id');
        
        if($data['set_reminder']){
            //$reminder_time = OmniCal_Activity_Model::UpdateActivityReminderTable(0, 0, $data['reminder_before_start'], $id);
            //OmniCal_Activity_Model::UpdateActivityReminderTime($id, $data['due_date'], $data['time_end'], 0, $reminder_time);
        }
    }
    
    public static function UpdateTaskInCRM($record, $updated_data){
        
		if($record){
            
			try{
                
				$recordModel = Vtiger_Record_Model::getInstanceById($record, 'Task');
                
				$data = $recordModel->getData();
                
				unset($updated_data['date_start']);
				unset($updated_data['time_start']);
				
				$final_data = array_replace($data, $updated_data);
                
				$final_data['update_exchange'] = 0;
                $final_data['time_end'] = "";
				
				$recordModel->setData($final_data);
                
				$recordModel->set('mode', 'edit');
                
				$recordModel->save();
                
				/*if($final_data['set_reminder']){
                    $reminder_time = OmniCal_Activity_Model::UpdateActivityReminderTable(0, 0, $final_data['reminder_before_start'], $record);
                    OmniCal_Activity_Model::UpdateActivityReminderTime($record, $final_data['date_start'], $final_data['time_start'], 0, $reminder_time);
                }*/
				
            } catch (Exception $ex){
                return;
            }
        }
    }
   
    public function AutoCreateUpdateDeleteCRMWithTaskInfo($task_info){
        foreach($task_info AS $action => $action_values){
            switch($action){
                case "create":
					foreach($action_values AS $k => $v){
						if(!self::DoesTaskExist($v['id']) && $this->user_id){
                            $task = $this->GetTaskInfo($v['id']);
							$data = $this->RequestToData($task);

                            if(is_array($data))
								$this->CreateTaskInCRM($data);
                        }
                    }
                    break;
                case "update":
                    foreach($action_values AS $k => $v){
                        $record = self::DoesTaskExist($v['id']);
						$task = $this->GetTaskInfo($v['id']);
                        $data = $this->RequestToData($task);
						
                        if($record && !self::DoTaskChangeKeysMatch($record, $data['task_exchange_change_key'])){
							$this->UpdateTaskInCRM($record, $data);
                        }                    
                    }
                    break;
                case "delete":
                    foreach($action_values AS $k => $v){
                        $record = self::DoesTaskExist($v['id']);
                        $this->DeleteTaskFromCRM($record);
                    }                    
            }
        }
    }
	 
	public function UpdateTaskExchangeIDAndChangeKey($record, $item_id, $changeKey){
    	global $adb;
        $query = "UPDATE vtiger_task SET task_exchange_item_id = ?, task_exchange_change_key = ? WHERE taskid = ?";
        $adb->pquery($query, array($item_id, $changeKey, $record));
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
        
		$request->Items->Task->StartTimeZone = new EWSType_TimeZoneDefinitionType();
        $request->Items->Task->StartTimeZone->Id = $tz;
        $request->Items->Task->StartTimeZone->Periods =  new EWSType_NonEmptyArrayOfPeriodsType();

        $period = new EWSType_PeriodType();
        $period->Bias =  'PT5H';
        $period->Name = 'Standard';
        $period->Id = "trule:Microsoft/Registry/{$tz}/2006-Standard";
        $request->Items->Task->StartTimeZone->Periods->Period[] = $period;

        $period = new EWSType_PeriodType();
        $period->Bias =  'PT4H';
        $period->Name = 'Daylight';
        $period->Id = "trule:Microsoft/Registry/{$tz}/2006-Daylight";
        $request->Items->Task->StartTimeZone->Periods->Period[] = $period;

        $period = new EWSType_PeriodType();
        $period->Bias =  'PT5H';
        $period->Name = 'Standard';
        $period->Id = "trule:Microsoft/Registry/{$tz}/2007-Standard";
        $request->Items->Task->StartTimeZone->Periods->Period[] = $period;

        $period = new EWSType_PeriodType();
        $period->Bias =  'PT4H';
        $period->Name = 'Daylight';
        $period->Id = "trule:Microsoft/Registry/{$tz}/2007-Daylight";
        $request->Items->Task->StartTimeZone->Periods->Period[] = $period;
        
        $request->Items->Task->StartTimeZone->TransitionsGroups = new EWSType_ArrayOfTransitionsGroupsType();
        $request->Items->Task->StartTimeZone->TransitionsGroups->TransitionsGroup = array();
        
        $request->Items->Task->StartTimeZone->Transitions = new EWSType_ArrayOfTransitionsType();
        $request->Items->Task->StartTimeZone->Transitions->Transition = new EWSType_TransitionType();
        $request->Items->Task->StartTimeZone->Transitions->Transition->To = new EWSType_TransitionTargetType();
        $request->Items->Task->StartTimeZone->Transitions->Transition->To->_ = 0;
        $request->Items->Task->StartTimeZone->Transitions->Transition->To->Kind = new EWSType_KindType();
        $request->Items->Task->StartTimeZone->Transitions->Transition->To->Kind = EWSType_KindType::GROUP;
    }
	
	
	public static function DoesTaskExist($itemId){
		
		global $adb;
        
		$query = "SELECT vtiger_task.taskid FROM vtiger_task 
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_task.taskid
		WHERE vtiger_crmentity.deleted = 0 AND BINARY task_exchange_item_id = ?";
        
		$result = $adb->pquery($query, array($itemId));
        
		if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'taskid');
        else
            return false;
	}
	
	public static function DoTaskChangeKeysMatch($record, $changekey){
		
		global $adb;
        
		$query = "SELECT task_exchange_change_key FROM vtiger_task WHERE taskid = ?";
        
		$result = $adb->pquery($query, array($record));
        
		if($adb->num_rows($result) > 0){
            
			$ck = $adb->query_result($result, 0, 'task_exchange_change_key');
            
			if($ck == $changekey)
                return true;
        }
        return false;
	}
}

?>

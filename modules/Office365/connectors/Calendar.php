<?php
class Office365_Calendar_Connector extends Office365_Base_Connector{
    
    protected $totalRecords;
    
    protected $createdRecords;
    
    protected $maxChildEvents = 500;
	
    protected $indexedPagingOffset = 0;
    
	public function __construct($user) {
        $this->user = $user;
    }
    
	public function getName() {
		return 'Office365Calendar';
	}
    
    /**
     * Pull the events from Office365
     * @return <array> office Records
     */
    public function pull($user = false) {
    	return $this->getCalendar($this->user);
    }


    /**
     * Pull the events from Office365
     * @param <object> $SyncState
     * @return <array> Office365 Records
     */
    public function getCalendar($user = false) {
        
        $lastUpdatedTime = false;
        
       /* if (Office365_Utils_Helper::getSyncTime('Calendar')) {
            $lastUpdatedTime = Office365_Utils_Helper::getSyncTime('Calendar');
        } else {*/
            $lastUpdatedTime = Office365_Utils_Helper::getCalendarSyncStartDate();
        //}
        
        $lastUpdatedTime = date("Y-m-d\TH:i:s.000\Z", strtotime($lastUpdatedTime));
        
        $response = array();
        
        $calendars = $this->getOffice365Events($lastUpdatedTime);
        
        $office365Records = array();
        
        $office365_modified_time = array();
        
        if(is_array($calendars) && !empty($calendars)){
            
            foreach ($calendars as $office365Calendar) {
                
                $recordModel = Office365_Calendar_Model::getInstanceFromValues(array('entity' => $office365Calendar));
                
                $deleted = false;
                
                if ($office365Calendar->getId() && !$office365Calendar->getLastModifiedDateTime()) {
                    $deleted = true;
                }
                
                $type = $office365Calendar->getType()->value();
                if($type == 'occurrence')
                   continue;
                
                if (!$deleted) {
                    $recordModel->setType($this->getSynchronizeController()->getSourceType())->setMode(Office365_SyncRecord_Model::UPDATE_MODE);
                } else {
                    $recordModel->setType($this->getSynchronizeController()->getSourceType())->setMode(Office365_SyncRecord_Model::DELETE_MODE);
                }
               
                $itemId = $office365Calendar->getId();
                if (!$deleted && $type != 'occurrence') 
                    $office365_modified_time[] = $office365Calendar->getLastModifiedDateTime()->format('Y-m-d H:i:s');
                
                // Fetch Recurring Events and Sync Them Too
               
                if (!$deleted && $office365Calendar->getType()->value() == 'seriesMaster') {
                    
                    $masterId = $office365Calendar->getId();
                   
                    $seriesEvents = $this->getChildEvents($masterId);
                    
                    if(!empty($seriesEvents)){
                            
                        foreach($seriesEvents as $childEvent){
                            
                            $recordModel = Office365_Calendar_Model::getInstanceFromValues(array('entity' => $childEvent));
                            
                            $deleted = false;
                                
                            if ($office365Calendar->getIsCancelled()) {
                                $deleted = true;
                            }
                            
                            if (!$deleted) {
                                $recordModel->setType($this->getSynchronizeController()->getSourceType())->setMode(Office365_SyncRecord_Model::UPDATE_MODE);
                            } else {
                                $recordModel->setType($this->getSynchronizeController()->getSourceType())->setMode(Office365_SyncRecord_Model::DELETE_MODE);
                            }
                            
                            $itemId = $childEvent->getId();
                            
                            $recordModel->setSeriesMasterId($masterId);
                            
                            $office365Records[$itemId] = $recordModel;
                        
                        }
                    }
                
                } else {
                    $office365Records[$itemId] = $recordModel;
                }
                
            }
        }
        
        $last_modified_time = date("Y-m-d H:i:s",max(array_map('strtotime',$office365_modified_time)));
        
        $this->createdRecords = count($office365Records);
        
        if (isset($last_modified_time) && !empty($office365_modified_time)) {
            Office365_Utils_Helper::updateSyncTime('Calendar', $last_modified_time, $user);
        } else {
            Office365_Utils_Helper::updateSyncTime('Calendar', false, $user);
        }
     
        return $office365Records;
    }
    
    
   
    public function getOffice365Events($lastUpdatedTime){
    	
		$syncController = $this->getSynchronizeController();
        
        $Office365Model = $syncController->getOffice365Model();
        
        $response = $Office365Model->getEvents($lastUpdatedTime, $this->indexedPagingOffset);
        
        return $response;
    }
    
	/**
     * Transform Office365 Records to Vtiger Records
     * @param <array> $targetRecords 
     * @return <array> tranformed office365 Records
     */
    public function transformToSourceRecord($targetRecords, $user = false) {
        
        $calendarArray = array();
       
        foreach ($targetRecords as $office365Record) {
            
            $entity = array();
           
            if ($office365Record->getMode() != Office365_SyncRecord_Model::DELETE_MODE) {
                
        	    if(!$user)
                    $user = Users_Record_Model::getCurrentUserModel();
                    
                $entity['assigned_user_id'] = vtws_getWebserviceEntityId('Users', $user->id);
                
                $entity['subject'] = $office365Record->getSubject();
                
                $entity['location'] = $office365Record->getWhere();
                
                $startDate = $office365Record->getStartDate();
                
                $entity['date_start'] = date("Y-m-d", strtotime($startDate));

                $entity['time_start'] = date("H:i:s", strtotime($startDate));
                
                $endDate = $office365Record->getEndDate();
                
                $entity['due_date'] = date("Y-m-d", strtotime($endDate));
                
                $entity['time_end'] = date("H:i:s", strtotime($endDate));
                
                $entity['description'] = $office365Record->getDescription();
                
                $entity['all_day_event'] = $office365Record->isAllDay();
                
                if($office365Record->isAllDay()){
                	$entity['duration_hours'] = '24';
                	$entity['duration_minutes'] = '0';	
                } 

                $visibility = $office365Record->get('Sensitivity');
                
                if($visibility !=="Private") $visibility = "Public";

                $entity['visibility'] = $visibility;

                if (empty($entity['subject'])) {
                    $entity['subject'] = 'Office365 Event';
                }
                
                $entity['taskpriority'] = $office365Record->getPriority($user);
                
                $entity['isorganizer'] = $office365Record->isOrganizer();
                
                $attendees = $office365Record->getAttendees();
                
                if(!empty($attendees)){
                    
                    $eventAttendees = $this->emailLookUp($attendees);
                    
                    if(!empty($eventAttendees['Contacts'])){
                        $entity['contactidlist'] = implode(';', $eventAttendees['Contacts']);
                    }
                    
                    if($eventAttendees['Leads'][0] != '')
                        $entity['parent_id'] = vtws_getWebserviceEntityId('Leads', $eventAttendees['Leads'][0]);
                }
                    
            }
            
            $calendar = $this->getSynchronizeController()->getSourceRecordModel($entity);
            
            $calendar = $this->performBasicTransformations($office365Record, $calendar);
            
            $calendar = $this->performBasicTransformationsToSourceRecords($calendar, $office365Record);
            
            $calendarArray[] = $calendar;
        }
        
		return $calendarArray;
    }
    

    public function emailLookUp($emailIds) {
        $db = PearDatabase::getInstance();
        $sql = 'SELECT DISTINCT crmid, setype FROM vtiger_emailslookup WHERE setype IN ("Contacts", "Leads") AND value IN (' .  generateQuestionMarks($emailIds) . ')';
        $result = $db->pquery($sql,$emailIds);
        $crmIds = array();
        for($i=0;$i<$db->num_rows($result);$i++) {
            $crmIds[$db->query_result($result,$i,'setype')][] = $db->query_result($result,$i,'crmid');
        }
        return $crmIds;
    }
    
    /**
     * Push the vtiger records to office365
     * @param <array> $records vtiger records to be pushed to office365
     * @return <array> pushed records
     */
    public function push($allRecords) {
       
        $VTERecords = array();
        
        foreach ($allRecords as $mode => $records) {
           
            if($mode == Office365_SyncRecord_Model::UPDATE_MODE){
                
                $office365Records = array();
                
                $office365Records2 = array();
                
                foreach($records as $record){
                    
                    $entity = $record->getEntityData();
                    $start = new DateTime($entity['Start']);
                    $end = new DateTime($entity['End']);
                    
                    $entity['Start'] = $start->format('c');
                    $entity['End'] = $end->format("c");
                    
                    if(!$entity['SendNotification']){
                        $office365Records2[$record->get("id")] = $entity;
                    } else {
                        $office365Records[$record->get("id")] = $entity;
                    }
                }
                
                $office365Recordss = array_chunk($office365Records, 200, true);
             
                foreach($office365Recordss as $office365Records){
                    
                    $updateResponse = $this->updateOffice365Events($office365Records);
                    
                    if(!empty($updateResponse)){
                        
                        foreach($updateResponse as $updateItemResponse){
                            
                            $itemId = $updateItemResponse->getBody();
                            
                            $records[$itemId['id']]->set("entity", $itemId)->set("office365Response", true);
                        }
                        
                        $VTERecords = $VTERecords + $records;
                    }
                    
                }
                
                $office365Recordss = array_chunk($office365Records2, 200, true);
                
                foreach($office365Recordss as $office365Records){
                    
                    $updateResponse = $this->updateOffice365Events($office365Records);
                    
                    if(!empty($updateResponse)){
                        
                        foreach($updateResponse as $updateItemResponse){
                            
                            if(!empty($updateItemResponse)){
                                
                                $itemId = $updateItemResponse->getBody();
                                
                                $records[$itemId['id']]->set("entity", $itemId)->set("office365Response", true);
                            }
                        }
                        
                        $VTERecords = $VTERecords + $records;
                    }
                    
                }
                
                
            } else if ($mode == Office365_SyncRecord_Model::CREATE_MODE) {
                
                foreach($records as $record){
                    
                    $entity = $record->getEntityData();
                
                    $start = new DateTime($entity['Start']);
                    $end = new DateTime($entity['End']);
                    
                    $entity['Start'] = $start->format('c');
                    $entity['End'] = $end->format("c");
                    
                    $newEntity = $this->addOffice365Event($entity);
                   
                    $record->set('entity', $newEntity->getBody());
                    $record->set("office365Response", true);
                    
                    $VTERecords[] = $record;
                }
                
            } else if($mode == Office365_SyncRecord_Model::DELETE_MODE){
                
                $deleteItems = array();
                
                foreach($records as $record){
                    
                    $record->set("office365Response", true);
                    
                    $deleteItems[] = array("Id" => $record->get("id"));
                }
                
                if(!empty($deleteItems)){
                
                    $response = $this->deleteOffice365Events($deleteItems);
                    
                    if($response){
                        $VTERecords = $VTERecords + $records;
                    }
                }
            }
        }
        
        return $VTERecords;
    }

    /**
     * Tarsform  Vtiger Records to office365 Records
     * @param <array> $vtEvents 
     * @return <array> tranformed vtiger Records
     */
    public function transformToTargetRecord($vtEvents) {
    	
       $records = array();
        
    	$updateEvents = array();
    	
        foreach ($vtEvents as $vtEvent) {
            
            $newEvent = new Office365_OfficeCalendar_Model();

            if($vtEvent->get('_id') != '' && $vtEvent->getMode() == Office365_SyncRecord_Model::UPDATE_MODE){
                $updateEvents[$vtEvent->get('_id')] = $vtEvent;
                continue;
            }
            
            $entityData = $vtEvent->getData();
            if(isset($entityData['_id']) && $entityData['_id'] != ''){
                $newEvent->setId($entityData['_id']);
            }
            
            $newEvent->setSubject($vtEvent->get('subject'));
            
            $newEvent->setLocation($vtEvent->get('location'));
            $newEvent->setDescription($vtEvent->get('description'));
            
            $newEvent->setSensitivity($vtEvent->get('visibility'));
            
            $newEvent->setSendNotification($vtEvent->get('sendnotification'));
            
            $startDate = $vtEvent->get('date_start');
            $startTime = $vtEvent->get('time_start');
            $endDate = $vtEvent->get('due_date');
            $endTime = $vtEvent->get('time_end');
            if (empty($endTime)) {
                $endTime = "00:00:00";
            }
            
            $newEvent->setStart($startDate . ' ' . $startTime);
            $newEvent->setEnd($endDate. ' ' .$endTime); 
            
            if($vtEvent->get('all_day_event'))
                $newEvent->setAllDayEvent(true);
            
               
            $eventAttendees = $vtEvent->get("event_attendees");
            
            if(!empty($eventAttendees)){
                
                $eventAttendees = explode("##", $eventAttendees);
                
                $attendeeEmails = array();
                
                foreach($eventAttendees as $attendee){
                        $attendeeRecordModel = Vtiger_Record_Model::getInstanceById($attendee, getSalesEntityType($attendee));
                    $attendeeRecordData = $attendeeRecordModel->getData();
                    if(isset($attendeeRecordData['email']) && !empty($attendeeRecordData['email']))$attendeeEmails[] = $attendeeRecordData['email'];
                }
                $newEvent->setAttendees($attendeeEmails);
            }
            
            $recordModel = Office365_Calendar_Model::getInstanceFromValues(array('entity' => $newEvent->getData()));
            $recordModel->setType($this->getSynchronizeController()->getSourceType())->setMode($vtEvent->getMode())->setSyncIdentificationKey($vtEvent->get('_syncidentificationkey'));
            $recordModel = $this->performBasicTransformations($vtEvent, $recordModel);
            $recordModel = $this->performBasicTransformationsToTargetRecords($recordModel, $vtEvent);
            $records[$vtEvent->getMode()][] = $recordModel;
        }
        
        if(!empty($updateEvents)){
            
            $office365EventDetails = array();
                
            $syncController = $this->getSynchronizeController();
            
            $Office365Model = $syncController->getOffice365Model();
            
			$office365_event_ids = array_keys($updateEvents);
			
			$office365_event_ids = array_chunk($office365_event_ids, 250);
			
			if(!empty($office365_event_ids)){
				
				foreach($office365_event_ids as $index => $office365_event_chunk_ids){
					
					$office365EventChunkDetails = $Office365Model->getItems($office365_event_chunk_ids);
					
					if(is_object($office365EventChunkDetails)){
						$office365EventChunkDetails = array($office365EventChunkDetails);
					}
					
					$office365EventDetails = array_merge($office365EventDetails, array_filter($office365EventChunkDetails));
				}
			}
			
            if(!empty($office365EventDetails)){
                
                foreach($office365EventDetails as $office365Event){
                    
                    $itemId = $office365Event->getId();
                   
                    $vtEvent = $updateEvents[$itemId];
                    
                    $eventInfo = $this->getOffice365EventData($office365Event);
                    
                    $newEvent = new Office365_OfficeCalendar_Model($eventInfo);
                    
                    $newEvent->setSubject($vtEvent->get('subject'));
                    $newEvent->setLocation($vtEvent->get('location'));
                    $newEvent->setDescription($vtEvent->get('description'));
                    
                    $newEvent->setSensitivity($vtEvent->get('visibility'));
                    $newEvent->setSendNotification($vtEvent->get('sendnotification'));
                    
                    $startDate = $vtEvent->get('date_start');
                    $startTime = $vtEvent->get('time_start');
                    $endDate = $vtEvent->get('due_date');
                    $endTime = $vtEvent->get('time_end');
                    if (empty($endTime)) {
                        $endTime = "00:00:00";
                    }
                    
                    $newEvent->setStart($startDate . ' ' . $startTime);
                    $newEvent->setEnd($endDate. ' ' .$endTime);
                    
                    if($vtEvent->get('all_day_event'))
                        $newEvent->setAllDayEvent(true);
                    
                    $eventAttendees = $vtEvent->get("event_attendees");
                    
                    if(!empty($eventAttendees)){
                        
                        $eventAttendees = explode("##", $eventAttendees);
                        
                        $attendeeEmails = array();
                        
                        foreach($eventAttendees as $attendee){
                                $attendeeRecordModel = Vtiger_Record_Model::getInstanceById($attendee, getSalesEntityType($attendee));
                            $attendeeRecordData = $attendeeRecordModel->getData();
                            if(isset($attendeeRecordData['email']) && !empty($attendeeRecordData['email']))$attendeeEmails[] = $attendeeRecordData['email'];
                        }
					
                        $organizerResponse = $office365Event->getResponseStatus()->getResponse()->value();
					    
						if($organizerResponse == 'organizer'){
							$newEvent->setAttendees($attendeeEmails);
						}
                    }
                    
                    $recordModel = Office365_Calendar_Model::getInstanceFromValues(array('entity' => $newEvent->getData()));
                    $recordModel->setType($this->getSynchronizeController()->getSourceType())->setMode($vtEvent->getMode())->setSyncIdentificationKey($vtEvent->get('_syncidentificationkey'));
                    $recordModel = $this->performBasicTransformations($vtEvent, $recordModel);
                    $recordModel = $this->performBasicTransformationsToTargetRecords($recordModel, $vtEvent);
                    $records[$vtEvent->getMode()][$itemId] = $recordModel;
                    
                }
            }
        }
      
        return $records;
    }
    
    public function addOffice365Event($data){
    	
		$syncController = $this->getSynchronizeController();
		
		$Office365Model = $syncController->getOffice365Model();
		
		$response = $Office365Model->createCalendarItems($data);
		
		return $response;
    }
    
    public function updateOffice365Events($records){
    	
        $syncController = $this->getSynchronizeController();
        
        $Office365Model = $syncController->getOffice365Model();
        
        $response = $Office365Model->updateCalendarItems($records);
        
		return $response;
    }
    

    /**
     * returns if more records exits or not
     * @return <boolean> true or false
     */
    public function moreRecordsExits() {
        return ($this->totalRecords - $this->createdRecords > 0) ? true : false;
    }
    
 	function office365Format($date) {
        $datTime = new DateTime($date);
        $timeZone = new DateTimeZone('UTC');
        $datTime->setTimezone($timeZone);
        $office365format = $datTime->format('Y-m-d\TH:i:s\Z');
        return $office365format;
    }
    
    public function getChildEvents($masterId){
        
        $syncController = $this->getSynchronizeController();
        
        $Office365Model = $syncController->getOffice365Model();
        
        $response = $Office365Model->getRecurringSeriesEvents($masterId);
        
        return $response;
        
    }
    
    function getOffice365EventData($office365Event){
        
        $entity = array();
        
        $office365Record = new Office365_Calendar_Model(array('entity' => $office365Event));
        
        $entity['Subject'] = $office365Record->getSubject();
        
        $entity['Location'] = $office365Record->getWhere();
        
        $startDate = $office365Record->getStartDate();
        
        $entity['Start'] = $startDate;
        
        $entity['End'] = $office365Record->getEndDate();
        
        $entity['Body'] = array('BodyType' => 'TEXT', '_value' => $office365Record->getDescription());
        
        return $entity;
    }
    
    function deleteOffice365Events($records, $options){
        
        $syncController = $this->getSynchronizeController();
        
        $Office365Model = $syncController->getOffice365Model();
        
        $response = $Office365Model->deleteItems($records, $options);
        
        return $response;
    }
    
   
}
?>
<?php
class MSExchange_Calendar_Connector extends MSExchange_Base_Connector{
    
    protected $totalRecords;
    
    protected $createdRecords;
    
    protected $maxChildEvents = 500;
	
    protected $indexedPagingOffset = 0;
    
	private $skipRecurringTypes = array("RelativeYearly", "AbsoluteYearly");
    
    public function __construct($user) {
        $this->user = $user;
    }
    
	public function getName() {
		return 'MSExchangeCalendar';
	}
    
    /**
     * Pull the events from MSEXchange
     * @return <array> exchange Records
     */
    public function pull($user = false) {
    	return $this->getCalendar($this->user);
    }


    /**
     * Pull the events from exchange
     * @param <object> $SyncState
     * @return <array> exchange Records
     */
    public function getCalendar($user = false) {
        
        $lastUpdatedTime = false;
        
        if (MSExchange_Utils_Helper::getSyncTime('Calendar')) {
            $lastUpdatedTime = MSExchange_Utils_Helper::getSyncTime('Calendar');
        } else {
            $lastUpdatedTime = MSExchange_Utils_Helper::getCalendarSyncStartDate();
        }
        
        $lastUpdatedTime = date("Y-m-d\TH:i:s.000\Z", strtotime($lastUpdatedTime));
        
        $response = array();
        
        $calendars = $this->getMSExchangeEvents($lastUpdatedTime);
        
        $exchangeRecords = array();
        
        $exchange_modified_time = array();
        
        if(is_array($calendars) && !empty($calendars)){
            
            foreach ($calendars as $exchangeCalendar) {
                
                $recordModel = MSExchange_Calendar_Model::getInstanceFromValues(array('entity' => $exchangeCalendar));
                
                $deleted = false;
                
                if ($exchangeCalendar->isCancelled()) {
                    $deleted = true;
                }
                
                if (!$deleted) {
                    $recordModel->setType($this->getSynchronizeController()->getSourceType())->setMode(MSExchange_SyncRecord_Model::UPDATE_MODE);
                } else {
                    $recordModel->setType($this->getSynchronizeController()->getSourceType())->setMode(MSExchange_SyncRecord_Model::DELETE_MODE);
                }
                
                $itemId = $exchangeCalendar->getItemId();
                
                $exchange_modified_time[] = $exchangeCalendar->getLastModifiedTime();
                
                // Fetch Recurring Events and Sync Them Too
                
                if ($exchangeCalendar->getCalendarItemType() == 'RecurringMaster') {
                    
                    $masterId = $exchangeCalendar->getItemId()->getId();
                    
                    $seriesEvents = $this->getChildEvents($masterId);
                    
                    if(!empty($seriesEvents)){
                            
                        foreach($seriesEvents as $childEvent){
                            
                            $recordModel = MSExchange_Calendar_Model::getInstanceFromValues(array('entity' => $childEvent));
                            
                            $deleted = false;
                                
                            if ($exchangeCalendar->isCancelled()) {
                                $deleted = true;
                            }
                            
                            if (!$deleted) {
                                $recordModel->setType($this->getSynchronizeController()->getSourceType())->setMode(MSExchange_SyncRecord_Model::UPDATE_MODE);
                            } else {
                                $recordModel->setType($this->getSynchronizeController()->getSourceType())->setMode(MSExchange_SyncRecord_Model::DELETE_MODE);
                            }
                            
                            $itemId = $childEvent->getItemId();
                            
                            $recordModel->setSeriesMasterId($masterId);
                            
                            $exchangeRecords[$itemId->getId()] = $recordModel;
                        
                        }
                    }
                
                } else {
                    $exchangeRecords[$itemId->getId()] = $recordModel;
                }
                
            }
        }
        
        $deletedRecords = $this->getMSExchangeDeletedEvents();
        
        if(!empty($deletedRecords)){
            
            foreach ($deletedRecords as $exchangeCalendar) {
                
                $itemId = $exchangeCalendar->getItemId();
                
                $recordModel = MSExchange_Calendar_Model::getInstanceFromValues(array('entity' => $exchangeCalendar));
                
                $recordModel->setType($this->getSynchronizeController()->getSourceType())->setMode(MSExchange_SyncRecord_Model::DELETE_MODE);
                
                $exchangeRecords[$itemId->getId()] = $recordModel;
            }
        }
       
        $last_modified_time = date("Y-m-d H:i:s",max(array_map('strtotime',$exchange_modified_time)));
        
        $this->createdRecords = count($exchangeRecords);
        
        if (isset($last_modified_time) && !empty($exchange_modified_time)) {
            MSExchange_Utils_Helper::updateSyncTime('Calendar', $last_modified_time, $user);
        } else {
            MSExchange_Utils_Helper::updateSyncTime('Calendar', false, $user);
        }
     
        return $exchangeRecords;
    }
    
    
    public function getLastModifiedExchangeCalendarItems($lastUpdatedTime){
		
		$syncController = $this->getSynchronizeController();
        
        $MSExchangeModel = $syncController->getMSExchangeModel();
        
        $folder = $MSExchangeModel->getFolder('calendar');
        
        $response = $MSExchangeModel->getEvents($folder, $lastUpdatedTime, $this->indexedPagingOffset);
        
		$this->totalRecords = $totalRecords = $response->getTotalItemsInView();
        
		$this->indexedPagingOffset = $response->getIndexedPagingOffset();
		
        $response = $response->getItems();
        
        $exchangeRecords = array();
        
		$exchange_modified_time = array();
		
        if(is_object($response)){
            
            $calendars = $response->getCalendarItem();
            
            if($totalRecords == 1){
                $calendars = array($calendars);
            }
            
            foreach ($calendars as $exchangeCalendar) {
                
                $itemId = $exchangeCalendar->getItemId();
                
                $exchangeRecords[] = $itemId->getId();
				
				$exchange_modified_time[] = $exchangeCalendar->getLastModifiedTime();
            }
        }
        
		$last_modified_time = date("Y-m-d H:i:s",max(array_map('strtotime',$exchange_modified_time)));
    
		$lastUpdatedTime = date("Y-m-d H:i:s", strtotime($lastUpdatedTime));
		
		if($last_modified_time == $lastUpdatedTime && $totalRecords >= 100){
		
			$lastUpdatedTime = date("Y-m-d\TH:i:s.000\Z", strtotime($lastUpdatedTime));
			
			$response = $this->getLastModifiedExchangeCalendarItems($lastUpdatedTime);
			
			$exchangeRecords = array_merge($exchangeRecords, $response);
		}
		
		return $exchangeRecords;
	}
	
    public function getMSExchangeEvents($lastUpdatedTime){
    	
		$syncController = $this->getSynchronizeController();
        
        $MSExchangeModel = $syncController->getMSExchangeModel();
        
        $exchangeRecords = $this->getLastModifiedExchangeCalendarItems($lastUpdatedTime);
		
		$exchangeEventDetails = array();
		
        if(!empty($exchangeRecords)){
            
			$exchange_event_ids = array_chunk($exchangeRecords, 100);
			
			if(!empty($exchange_event_ids)){
				
				foreach($exchange_event_ids as $exchange_event_chunk_ids){
					
					$exchangeEventChunkDetails = $MSExchangeModel->getItems($exchange_event_chunk_ids);
					
					if(is_object($exchangeEventChunkDetails)){
						$exchangeEventChunkDetails = array($exchangeEventChunkDetails);
					}
					
					$exchangeEventDetails = array_merge($exchangeEventDetails, array_filter($exchangeEventChunkDetails));
				}
			}
		}
        
        return $exchangeEventDetails;
    }
    
	/**
     * Transform MS Exchange Records to Vtiger Records
     * @param <array> $targetRecords 
     * @return <array> tranformed exchange Records
     */
    public function transformToSourceRecord($targetRecords, $user = false) {
        
        $calendarArray = array();
        
        foreach ($targetRecords as $exchangeRecord) {
            
            $entity = array();
            
            if ($exchangeRecord->getMode() != MSExchange_SyncRecord_Model::DELETE_MODE) {
                
        	    if(!$user)
                    $user = Users_Record_Model::getCurrentUserModel();
                    
                $entity['assigned_user_id'] = vtws_getWebserviceEntityId('Users', $user->id);
                
                $entity['subject'] = $exchangeRecord->getSubject();
                
                $entity['location'] = $exchangeRecord->getWhere();
                
                $startDate = $exchangeRecord->getStartDate();
                
                $entity['date_start'] = date("Y-m-d", strtotime($startDate));

                $entity['time_start'] = date("H:i:s", strtotime($startDate));
                
                $endDate = $exchangeRecord->getEndDate();
                
                $entity['due_date'] = date("Y-m-d", strtotime($endDate));
                
                $entity['time_end'] = date("H:i:s", strtotime($endDate));
                
                $entity['description'] = $exchangeRecord->getDescription();
                
                $entity['all_day_event'] = $exchangeRecord->isAllDay();
                
                if($exchangeRecord->isAllDay()){
                	$entity['duration_hours'] = '24';
                	$entity['duration_minutes'] = '0';	
                } 

                $visibility = $exchangeRecord->get('Sensitivity');
                
                if($visibility !=="Private") $visibility = "Public";

                $entity['visibility'] = $visibility;

                if (empty($entity['subject'])) {
                    $entity['subject'] = 'MS Exchange Event';
                }
                
                $entity['taskpriority'] = $exchangeRecord->getPriority($user);
                
                $entity['isorganizer'] = $exchangeRecord->isOrganizer();
                
                $attendees = $exchangeRecord->getAttendees();
                
                if(!empty($attendees)){
                    
                    $eventAttendees = $this->emailLookUp($attendees);
                    
                    if(!empty($eventAttendees)){
                        $entity['contactidlist'] = implode(';', $eventAttendees);
                    }
                }
                
                $seriesMasterId = $exchangeRecord->getSeriesMasterId();
                
                if($seriesMasterId != '')
                    $entity['parent_id'] = $seriesMasterId;
            }
            
            $calendar = $this->getSynchronizeController()->getSourceRecordModel($entity);
            
            $calendar = $this->performBasicTransformations($exchangeRecord, $calendar);
            
            $calendar = $this->performBasicTransformationsToSourceRecords($calendar, $exchangeRecord);
            
            $calendarArray[] = $calendar;
        }
        
		return $calendarArray;
    }
    

    public function emailLookUp($emailIds) {
        $db = PearDatabase::getInstance();
        $sql = 'SELECT DISTINCT crmid FROM vtiger_emailslookup WHERE setype = "Contacts" AND value IN (' .  generateQuestionMarks($emailIds) . ')';
        $result = $db->pquery($sql,$emailIds);
        $crmIds = array();
        for($i=0;$i<$db->num_rows($result);$i++) {
            $crmIds[] = $db->query_result($result,$i,'crmid');
        }
        return $crmIds;
    }
    
    /**
     * Push the vtiger records to exchange
     * @param <array> $records vtiger records to be pushed to exchange
     * @return <array> pushed records
     */
    public function push($allRecords) {
    	
        $VTERecords = array();
        
        foreach ($allRecords as $mode => $records) {
    		
            if($mode == MSExchange_SyncRecord_Model::UPDATE_MODE){
        
                
                
                $exchangeRecords = array();
                
                
                $exchangeRecords2 = array();
                
                foreach($records as $record){
                    
                    $entity = $record->getEntityData();
                    $start = new DateTime($entity['Start']);
                    $end = new DateTime($entity['End']);
                    
                    $entity['Start'] = $start->format('c');
                    $entity['End'] = $end->format("c");
                    
                    
                    if(!$entity['SendNotification']){
                        $exchangeRecords2[$record->get("id")] = $entity;
                    } else {
                        $exchangeRecords[$record->get("id")] = $entity;
                    }
                }
                
                $exchangeRecordss = array_chunk($exchangeRecords, 200, true);
             
                foreach($exchangeRecordss as $exchangeRecords){
                    
                    $updateResponse = $this->updateMSExchangeEvents($exchangeRecords);
                    
                    if(!empty($updateResponse)){
                        
                        foreach($updateResponse as $updateItemResponse){
                            
                            $calendarItem = $updateItemResponse->getItems()->getCalendarItem();
                            
                            $itemId = $calendarItem->getItemId();
                            
                            $records[$itemId->getId()]->set("entity", $itemId)->set("exchangeResponse", true);
                        }
                        
                        $VTERecords = $VTERecords + $records;
                    }
                    
                }
                
                
                $exchangeRecordss = array_chunk($exchangeRecords2, 200, true);
                
                foreach($exchangeRecordss as $exchangeRecords){
                    
                    $updateResponse = $this->updateMSExchangeEvents($exchangeRecords);
                    
                    if(!empty($updateResponse)){
                        
                        foreach($updateResponse as $updateItemResponse){
                            
                            $calendarItem = $updateItemResponse->getItems()->getCalendarItem();
                            
                            $itemId = $calendarItem->getItemId();
                            
                            $records[$itemId->getId()]->set("entity", $itemId)->set("exchangeResponse", true);
                        }
                        
                        $VTERecords = $VTERecords + $records;
                    }
                    
                }
                
                
                
                
                
                
                
                
                
            } else if ($mode == MSExchange_SyncRecord_Model::CREATE_MODE) {
                
                foreach($records as $record){
                    
                    $entity = $record->getEntityData();
                
                    $start = new DateTime($entity['Start']);
                    $end = new DateTime($entity['End']);
                    
                    $entity['Start'] = $start->format('c');
                    $entity['End'] = $end->format("c");
                    
                    $newEntity = $this->addMSExchangeEvent($entity);
                
                    $record->set('entity', $newEntity);
                    $record->set("exchangeResponse", true);
                    
                    $VTERecords[] = $record;
                }
            } else if($mode == MSExchange_SyncRecord_Model::DELETE_MODE){
                
                $deleteItems = array();
                
                foreach($records as $record){
                    
                    $record->set("exchangeResponse", true);
                    
                    $deleteItems[] = array("Id" => $record->get("id"));
                }
                
                if(!empty($deleteItems)){
                
                    $defaultOptions = array(
                        'SendMeetingCancellations' => 'SendToNone'
                    );
                    
                    $response = $this->deleteMSExchangeEvents($deleteItems, $defaultOptions);
                    
                    if($response){
                        $VTERecords = $VTERecords + $records;
                    }
                }
            }
        }
        
        return $VTERecords;
    }

    /**
     * Tarsform  Vtiger Records to exchange Records
     * @param <array> $vtEvents 
     * @return <array> tranformed vtiger Records
     */
    public function transformToTargetRecord($vtEvents) {
    	
    	$records = array();
        
    	$updateEvents = array();
    	
        foreach ($vtEvents as $vtEvent) {
            
            $newEvent = new MSExchange_MSExchangeCalendar_Model();

            if($vtEvent->get('_id') != '' && $vtEvent->getMode() == MSExchange_SyncRecord_Model::UPDATE_MODE){
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
                    $attendeeRecordModel = Vtiger_Record_Model::getInstanceById($attendee, 'Contacts');
                    $attendeeRecordData = $attendeeRecordModel->getData();
                    if(isset($attendeeRecordData['email']) && !empty($attendeeRecordData['email']))$attendeeEmails[] = $attendeeRecordData['email'];
                }
                $newEvent->setAttendees($attendeeEmails);
            }
            
            $recordModel = MSExchange_Calendar_Model::getInstanceFromValues(array('entity' => $newEvent->getData()));
            $recordModel->setType($this->getSynchronizeController()->getSourceType())->setMode($vtEvent->getMode())->setSyncIdentificationKey($vtEvent->get('_syncidentificationkey'));
            $recordModel = $this->performBasicTransformations($vtEvent, $recordModel);
            $recordModel = $this->performBasicTransformationsToTargetRecords($recordModel, $vtEvent);
            $records[$vtEvent->getMode()][] = $recordModel;
        }
        
        if(!empty($updateEvents)){
            
            $exchangeEventDetails = array();
                
            $syncController = $this->getSynchronizeController();
            
            $MSExchangeModel = $syncController->getMSExchangeModel();
            
			$exchange_event_ids = array_keys($updateEvents);
			
			$exchange_event_ids = array_chunk($exchange_event_ids, 250);
			
			if(!empty($exchange_event_ids)){
				
				foreach($exchange_event_ids as $index => $exchange_event_chunk_ids){
					
					$exchangeEventChunkDetails = $MSExchangeModel->getItems($exchange_event_chunk_ids);
					
					if(is_object($exchangeEventChunkDetails)){
						$exchangeEventChunkDetails = array($exchangeEventChunkDetails);
					}
					
					$exchangeEventDetails = array_merge($exchangeEventDetails, array_filter($exchangeEventChunkDetails));
				}
			}
			
            if(!empty($exchangeEventDetails)){
                
                foreach($exchangeEventDetails as $exchangeEvent){
                    
                    $itemId = $exchangeEvent->getItemId()->getId();
                    
                    $vtEvent = $updateEvents[$itemId];
                    
                    $eventInfo = $this->getExchangeEventData($exchangeEvent);
                    
                    $newEvent = new MSExchange_MSExchangeCalendar_Model($eventInfo);
                    
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
                            $attendeeRecordModel = Vtiger_Record_Model::getInstanceById($attendee, 'Contacts');
                            $attendeeRecordData = $attendeeRecordModel->getData();
                            if(isset($attendeeRecordData['email']) && !empty($attendeeRecordData['email']))$attendeeEmails[] = $attendeeRecordData['email'];
                        }
					
						$organizerResponse = $exchangeEvent->getMyResponseType();
					
						if($organizerResponse == 'Organizer'){
							$newEvent->setAttendees($attendeeEmails);
						}
                    }
                    
                    $recordModel = MSExchange_Calendar_Model::getInstanceFromValues(array('entity' => $newEvent->getData()));
                    $recordModel->setType($this->getSynchronizeController()->getSourceType())->setMode($vtEvent->getMode())->setSyncIdentificationKey($vtEvent->get('_syncidentificationkey'));
                    $recordModel = $this->performBasicTransformations($vtEvent, $recordModel);
                    $recordModel = $this->performBasicTransformationsToTargetRecords($recordModel, $vtEvent);
                    $records[$vtEvent->getMode()][$itemId] = $recordModel;
                    
                }
            }
        }
        
        return $records;
    }
    
	public function getMSExchangeEventById($itemId){
    	
	    $syncController = $this->getSynchronizeController();
	    
	    $MSExchangeModel = $syncController->getMSExchangeModel();
	    
	    $response = $MSExchangeModel->getEventInfo($itemId);
	    
    	return $response;
    }
    
    public function addMSExchangeEvent($data){
    	
		$syncController = $this->getSynchronizeController();
		
		$MSExchangeModel = $syncController->getMSExchangeModel();
		
		$folder = $MSExchangeModel->getFolder('calendar');
		
		$response = $MSExchangeModel->createCalendarItems($folder, $data);
		
		return $response;
    }
    
    public function updateMSExchangeEvents($records){
    	
        $syncController = $this->getSynchronizeController();
        
        $MSExchangeModel = $syncController->getMSExchangeModel();
        
        $response = $MSExchangeModel->updateCalendarItems($records);
        
		return $response;
    }
    

    /**
     * returns if more records exits or not
     * @return <boolean> true or false
     */
    public function moreRecordsExits() {
        return ($this->totalRecords - $this->createdRecords > 0) ? true : false;
    }
    
 	function exchangeFormat($date) {
        $datTime = new DateTime($date);
        $timeZone = new DateTimeZone('UTC');
        $datTime->setTimezone($timeZone);
        $exchangeformat = $datTime->format('Y-m-d\TH:i:s\Z');
        return $exchangeformat;
    }
    
    public function getChildEvents($masterId){
        
        $syncController = $this->getSynchronizeController();
        
        $MSExchangeModel = $syncController->getMSExchangeModel();
        
        $response = $MSExchangeModel->getRecurringSeriesEvents($masterId);
        
        return $response;
        
    }
    
    function getExchangeEventData($exchangeEvent){
        
        $entity = array();
        
        $exchangeRecord = new MSExchange_Calendar_Model(array('entity' => $exchangeEvent));
        
        $entity['Subject'] = $exchangeRecord->getSubject();
        
        $entity['Location'] = $exchangeRecord->getWhere();
        
        $startDate = $exchangeRecord->getStartDate();
        
        $entity['Start'] = $startDate;
        
        $entity['End'] = $exchangeRecord->getEndDate();
        
        $entity['Body'] = array('BodyType' => 'TEXT', '_value' => $exchangeRecord->getDescription());
        
        return $entity;
    }
    
    function deleteMSExchangeEvents($records, $options){
        
        $syncController = $this->getSynchronizeController();
        
        $MSExchangeModel = $syncController->getMSExchangeModel();
        
        $response = $MSExchangeModel->deleteItems($records, $options);
        
        return $response;
    }
    
    function getMSExchangeDeletedEvents(){
        
        $syncState = MSExchange_Utils_Helper::getSyncState('Calendar');
        
        $syncController = $this->getSynchronizeController();
        
        $MSExchangeModel = $syncController->getMSExchangeModel();
        
        $response = $MSExchangeModel->getDeletedEventsChanges($syncState);
        
        $deletedEvents = array();
        
        if(!empty($response)){
            
            $newSyncState = $response->getSyncState();
            
            $changes = $response->getChanges();
            
            $deletedEvents = $changes->getDelete();
            
            MSExchange_Utils_Helper::updateSyncState($newSyncState, 'Calendar');
        }
        
        if(!empty($deletedEvents))
            $deletedEvents = $MSExchangeModel->ensureIsArray($deletedEvents, true);
       
        return $deletedEvents;
    }
}
?>
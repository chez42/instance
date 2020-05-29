<?php
use garethp\ews\API\Enumeration\TaskStatusType;
class MSExchange_Task_Connector extends MSExchange_Base_Connector{
    
    protected $totalRecords;
    
    protected $createdRecords;
    
    protected $maxChildEvents = 500;
    
    protected $indexedPagingOffset = 0;
    
    private $skipRecurringTypes = array("RelativeYearly", "AbsoluteYearly");
    
    public function __construct($user) {
        $this->user = $user;
    }
    
    public function getName() {
        return 'MSExchangeTask';
    }
    
    /**
     * Pull the events from MSEXchange
     * @return <array> exchange Records
     */
    public function pull($user = false) {
        return $this->getTask($this->user);
    }
    
    
    /**
     * Pull the events from exchange
     * @param <object> $SyncState
     * @return <array> exchange Records
     */
    public function getTask($user = false) {
        
        $lastUpdatedTime = false;
        
        if (MSExchange_Utils_Helper::getSyncTime('Task')) {
            $lastUpdatedTime = MSExchange_Utils_Helper::getSyncTime('Task');
        } else {
            $lastUpdatedTime = MSExchange_Utils_Helper::getTaskSyncStartDate();
        }
       
        $lastUpdatedTime = date("Y-m-d\TH:i:s.000\Z", strtotime($lastUpdatedTime));
        
        $response = array();
        
        $tasks = $this->getMSExchangeEvents($lastUpdatedTime);
        
        $exchangeRecords = array();
        
        $exchange_modified_time = array();
        
        if(is_array($tasks) && !empty($tasks)){
            
            foreach ($tasks as $exchangeTask) {
                
                $recordModel = MSExchange_Task_Model::getInstanceFromValues(array('entity' => $exchangeTask));
                
                $recordModel->setType($this->getSynchronizeController()->getSourceType())->setMode(MSExchange_SyncRecord_Model::UPDATE_MODE);
                
                $itemId = $exchangeTask->getItemId();
                
                $exchangeRecords[$itemId->getId()] = $recordModel;
                
                $exchange_modified_time[] = $exchangeTask->getLastModifiedTime();
                
            }
        }
        
        $deletedRecords = $this->getMSExchangeDeletedEvents();
        
        if(!empty($deletedRecords)){
            
            foreach ($deletedRecords as $exchangeCalendar) {
                
                $itemId = $exchangeCalendar->getItemId();
                
                $recordModel = MSExchange_Task_Model::getInstanceFromValues(array('entity' => $exchangeCalendar));
                
                $recordModel->setType($this->getSynchronizeController()->getSourceType())->setMode(MSExchange_SyncRecord_Model::DELETE_MODE);
                
                $exchangeRecords[$itemId->getId()] = $recordModel;
            }
        }
        
        $last_modified_time = date("Y-m-d H:i:s",max(array_map('strtotime',$exchange_modified_time)));
        
        $this->createdRecords = count($exchangeRecords);
        
        if (isset($last_modified_time) && !empty($exchange_modified_time)) {
            MSExchange_Utils_Helper::updateSyncTime('Task', $last_modified_time, $user);
        } else {
            MSExchange_Utils_Helper::updateSyncTime('Task', false, $user);
        }
        
        return $exchangeRecords;
    }
    
    
    public function getLastModifiedExchangeTaskItems($lastUpdatedTime){
        
        $syncController = $this->getSynchronizeController();
        
        $MSExchangeModel = $syncController->getMSExchangeModel();
        
        $folder = $MSExchangeModel->getFolder('tasks');
       
        $response = $MSExchangeModel->getEvents($folder, $lastUpdatedTime, $this->indexedPagingOffset);
        
        $this->totalRecords = $totalRecords = $response->getTotalItemsInView();
        
        $this->indexedPagingOffset = $response->getIndexedPagingOffset();
        
        $response = $response->getItems();
        
        $exchangeRecords = array();
        
        $exchange_modified_time = array();
        
        if(is_object($response)){
            
            $tasks = $response->getTask();
           
            if($totalRecords == 1){
                $tasks = array($tasks);
            }
            
            foreach ($tasks as $exchangeTask) {
                
                $itemId = $exchangeTask->getItemId();
                
                $exchangeRecords[] = $itemId->getId();
                
                $exchange_modified_time[] = $exchangeTask->getLastModifiedTime();
            }
        }
        
        $last_modified_time = date("Y-m-d H:i:s",max(array_map('strtotime',$exchange_modified_time)));
        
        $lastUpdatedTime = date("Y-m-d H:i:s", strtotime($lastUpdatedTime));
        
        if($last_modified_time == $lastUpdatedTime && $totalRecords >= 100){
            
            $lastUpdatedTime = date("Y-m-d\TH:i:s.000\Z", strtotime($lastUpdatedTime));
            
            $response = $this->getLastModifiedExchangeTaskItems($lastUpdatedTime);
            
            $exchangeRecords = array_merge($exchangeRecords, $response);
        }
        
        return $exchangeRecords;
    }
    
    public function getMSExchangeEvents($lastUpdatedTime){
        
        $syncController = $this->getSynchronizeController();
        
        $MSExchangeModel = $syncController->getMSExchangeModel();
        
        $exchangeRecords = $this->getLastModifiedExchangeTaskItems($lastUpdatedTime);
        
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
        
        $taskArray = array();
       
        foreach ($targetRecords as $exchangeRecord) {
            
            $entity = array();
            
            if ($exchangeRecord->getMode() != MSExchange_SyncRecord_Model::DELETE_MODE) {
                
                if(!$user)
                    $user = Users_Record_Model::getCurrentUserModel();
                
                $entity['assigned_user_id'] = vtws_getWebserviceEntityId('Users', $user->id);
                
                $entity['subject'] = $exchangeRecord->getSubject();
                
                $entity['task_priority'] = $exchangeRecord->getPriority($user);
                
                $entity['task_status'] = $exchangeRecord->getStatus();
               
                $endDate = $exchangeRecord->getEndDate();
                
                $entity['due_date'] = date("Y-m-d", strtotime($endDate));
                
                
                if (empty($entity['subject'])) {
                    $entity['subject'] = 'MS Exchange Task';
                }
                $entity['description'] = $exchangeRecord->getDescription();
                
            }
            
            $task = $this->getSynchronizeController()->getSourceRecordModel($entity);
            
            $task = $this->performBasicTransformations($exchangeRecord, $task);
            
            $task = $this->performBasicTransformationsToSourceRecords($task, $exchangeRecord);
            
            $taskArray[] = $task;
        }
        
        return $taskArray;
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
        
        $reflection = new \ReflectionClass(TaskStatusType::class);
        $constants = $reflection->getConstants();
        
        foreach ($allRecords as $mode => $records) {
            
            if($mode == MSExchange_SyncRecord_Model::UPDATE_MODE){
                
                $exchangeRecords = array();
                
                $exchangeRecords2 = array();
                
                foreach($records as $record){
                    
                    $entity = $record->getEntityData();
                    $end = new DateTime($entity['DueDate']);
                    
                    $entity['DueDate'] = $end->format("c");
                    
                    $status = $entity['Status'];
                    
                    if($status == 'Open')
                        $status = 'In Progress';
                    
                    if($status){
                        $status = strtoupper($status);
                        
                        if($status)
                            $status = str_replace(" ", "_", $status);
                            
                        $status = $constants[$status];
                        
                        if($status)
                            $entity['Status'] = $status;
                            
                    }
                        
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
                            
                            $taskItem = $updateItemResponse->getItems()->getTask();
                            
                            $itemId = $taskItem->getItemId();
                            
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
                            
                            $taskItem = $updateItemResponse->getItems()->getTask();
                            
                            $itemId = $taskItem->getItemId();
                            
                            $records[$itemId->getId()]->set("entity", $itemId)->set("exchangeResponse", true);
                        }
                        
                        $VTERecords = $VTERecords + $records;
                    }
                    
                }
               
            } else if ($mode == MSExchange_SyncRecord_Model::CREATE_MODE) {
                
                foreach($records as $record){
                    
                    $entity = $record->getEntityData();
                    $end = new DateTime($entity['DueDate']);
                    $entity['DueDate'] = $end->format("c");
                    
                    $status = $entity['Status'];
                    
                    if($status == 'Open')
                        $status = 'In Progress';
                    
                    if($status){
                        $status = strtoupper($status);
                        
                        if($status)
                            $status = str_replace(" ", "_", $status);
                        
                        $status = $constants[$status];
                        
                        if($status)
                            $entity['Status'] = $status;
                        
                    }
                    
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
                        'AffectedTaskOccurrences' => 'AllOccurrences'
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
           
            $newEvent = new MSExchange_MSExchangeTask_Model();
            
            if($vtEvent->get('_id') != '' && $vtEvent->getMode() == MSExchange_SyncRecord_Model::UPDATE_MODE){
                $updateEvents[$vtEvent->get('_id')] = $vtEvent;
                continue;
            }
            
            $entityData = $vtEvent->getData();
            if(isset($entityData['_id']) && $entityData['_id'] != ''){
                $newEvent->setId($entityData['_id']);
            }
            
            $newEvent->setSubject($vtEvent->get('subject'));
            
            $newEvent->setDescription($vtEvent->get('description'));
            
            $newEvent->setSensitivity($vtEvent->get('task_priority'));
            
            if($vtEvent->get('task_status') == 'Open')
                $task_status = 'In Progress';
            else
                $task_status = $vtEvent->get('task_status');
           
            $newEvent->setStatus($task_status);
            
            //$newEvent->setSendNotification($vtEvent->get('sendnotification'));
            
            $endDate = $vtEvent->get('due_date');
            $endTime = $vtEvent->get('time_end');
            if (empty($endTime)) {
                $endTime = "00:00:00";
            }
            
            $newEvent->setEndDate($endDate. ' ' .$endTime);
            
            $recordModel = MSExchange_Task_Model::getInstanceFromValues(array('entity' => $newEvent->getData()));
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
                    
                    $newEvent = new MSExchange_MSExchangeTask_Model($eventInfo);
                    
                    $newEvent->setSubject($vtEvent->get('subject'));
                    
                    $newEvent->setDescription($vtEvent->get('description'));
                    
                    $newEvent->setSensitivity($vtEvent->get('task_priority'));
                    
                    if($vtEvent->get('task_status') == 'Open')
                        $task_status = 'In Progress';
                    else
                        $task_status = $vtEvent->get('task_status');
                        
                    $newEvent->setStatus($task_status);
                            
                    //$newEvent->setSendNotification($vtEvent->get('sendnotification'));
                    
                    $endDate = $vtEvent->get('due_date');
                    $endTime = $vtEvent->get('time_end');
                    if (empty($endTime)) {
                        $endTime = "00:00:00";
                    }
                    
                    $newEvent->setEndDate($endDate. ' ' .$endTime);
                    
                    $recordModel = MSExchange_Task_Model::getInstanceFromValues(array('entity' => $newEvent->getData()));
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
        
        $folder = $MSExchangeModel->getFolder('tasks');
        
        $response = $MSExchangeModel->createTaskItems($folder, $data);
        
        return $response;
    }
    
    public function updateMSExchangeEvents($records){
        
        $syncController = $this->getSynchronizeController();
        
        $MSExchangeModel = $syncController->getMSExchangeModel();
        
        $response = $MSExchangeModel->updateTaskItems($records);
        
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
        
        $exchangeRecord = new MSExchange_Task_Model(array('entity' => $exchangeEvent));
        
        $entity['Subject'] = $exchangeRecord->getSubject();
        
        $entity['DueDate'] = strtotime($exchangeRecord->getEndDate());
        
        $entity['Status'] = $exchangeRecord->getStatus();
        
        $entity['Body'] = array('BodyType' => 'TEXT', '_value' => $exchangeRecord->getDescription());
        
        return $entity;
    }
    
    function deleteMSExchangeEvents($records, $options){
        
        $syncController = $this->getSynchronizeController();
        
        $MSExchangeModel = $syncController->getMSExchangeModel();
       
        $response = $MSExchangeModel->deleteItems($records,$options);
        
        return $response;
    }
    
    function getMSExchangeDeletedEvents(){
        
        $syncState = MSExchange_Utils_Helper::getSyncState('Task');
        
        $syncController = $this->getSynchronizeController();
        
        $MSExchangeModel = $syncController->getMSExchangeModel();
        
        $response = $MSExchangeModel->getDeletedEventsChanges($syncState);
        
        $deletedEvents = array();
        
        if(!empty($response)){
            
            $newSyncState = $response->getSyncState();
            
            $changes = $response->getChanges();
            
            $deletedEvents = $changes->getDelete();
            
            MSExchange_Utils_Helper::updateSyncState($newSyncState, 'Task');
        }
        
        if(!empty($deletedEvents))
            $deletedEvents = $MSExchangeModel->ensureIsArray($deletedEvents, true);
            
            return $deletedEvents;
    }
}
?>
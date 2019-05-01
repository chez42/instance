<?php

require_once 'data/CRMEntity.php';

Class MSExchange_Vtiger_Connector extends MSExchange_Base_Connector{
	
	const syncdir = '11';
	
	protected $name;
	
	protected $clientSyncType;
	protected $assignToChangedRecords;
	protected $nextSyncTime;
	protected $sourceModule;
	protected $moreRecords;
	public function __construct($user) {
		$this->user = $user;
		$this->assignToChangedRecords = array();
    }
    
	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	public function getUser() {
		return $this->getSynchronizeController()->user;
	}

	public function getRecordModelFromData($data) {
		return $this->getSynchronizeController()->getSourceRecordModel($data);
	}
	
	public function pull($sourceModule) {
		
		$this->setClientSyncType($this->getSynchronizeController()->getSyncType());
		
		$lastSync = MSExchange_Utils_Helper::getLastVtigerSyncTime($sourceModule);
		
		if ($sourceModule == 'Calendar' && !$lastSync) {
            $lastSync = MSExchange_Utils_Helper::getCalendarSyncStartDate();
            $lastSync = $lastSync . ' 00:00:00';
        }
		
        $this->sourceModule = $sourceModule;
        
        $records = $this->get($lastSync, $sourceModule, $this->getSynchronizeController()->user);
		
		$createdRecords = $records['created'];
		$updatedRecords = $records['updated'];
		$deletedRecords = $records['deleted'];

		if($sourceModule == 'Calendar' && isset($records['recurring_events']) && !empty($records['recurring_events'])){
			$recurring_events = $records['recurring_events'];
		}
		
		foreach ($createdRecords as $record) {
			
			if($sourceModule == 'Calendar' && in_array($record['id'], $recurring_events)){
				$date_start = $record['date_start'];
				$dStart = new DateTime($record['date_start']);
				$dEnd = new DateTime();
				if($dEnd > $dStart){
					$dDiff = $dStart->diff($dEnd);
					$days = $dDiff->days;
					if($days > 15)
						continue;
				}
			}
			
			$model = $this->getRecordModelFromData($record);
			$recordModels[] = $model->setMode(MSExchange_SyncRecord_Model::CREATE_MODE);
		}

		foreach ($updatedRecords as $record) {
			
			if($sourceModule == 'Calendar' && in_array($record['id'], $recurring_events)){
				$date_start = $record['date_start'];
				$dStart = new DateTime($record['date_start']);
				$dEnd  = new DateTime();
				if($dEnd > $dStart){
					$dDiff = $dStart->diff($dEnd);
					$days = $dDiff->days;
					if($days > 15)
						continue;
				}
			}
			
			$model = $this->getRecordModelFromData($record);
			$recordModels[] = $model->setMode(MSExchange_SyncRecord_Model::UPDATE_MODE);
		}

		foreach ($deletedRecords as $record) {
			$model = $this->getRecordModelFromData($record);
			$recordModels[] = $model->setMode(MSExchange_SyncRecord_Model::DELETE_MODE);
		}
		
		$this->nextSyncTime = $records['lastModifiedTime'];
		
		$this->moreRecords = $records['more'];
		
		return $recordModels;
	}

	
	
	public function convertToPushSyncTrackerFormat($recordList) {
		$syncTrackerRecordList = array();
		foreach ($recordList as $record) {
			$syncTrackerRecord = array();
			$syncTrackerRecord['module'] = $record->getType();
			$syncTrackerRecord['mode'] = $record->getMode();
			$syncTrackerRecord['id'] = $record->getId();
			if (!$record->isDeleteMode()) {
				$syncTrackerRecord['values'] = $record->getData();
				$syncTrackerRecord['values']['modifiedtime'] = $record->getModifiedTime();
				$syncTrackerRecord['values']['id'] = $record->getId();
			} else {
               $syncTrackerRecord['_syncidentificationkey'] = $record->get('_syncidentificationkey');
            }
            $syncTrackerRecordList[] = $syncTrackerRecord;
		}
		return $syncTrackerRecordList;
	}
	
	public function get($lastSync, $module, $user){

		$this->user = $user;
        $syncModule = $module;
        $syncType = 'user';
        
        $syncController = $this->getSynchronizeController();
        $syncId = $syncController->syncId;
        
        if (!$this->isClientUserSyncType()) {
            if($this->isClientUserAndGroupSyncType()){
                $syncType = 'userandgroup';
            }else{
                $syncType = 'application';
            }
        }
        
        if($syncModule == 'Calendar'){
			$syncModule = 'Events';
		}
		
		$result = $this->vtigerpull($lastSync, $syncModule, $syncType, $this->user);
        
		$result['updated'] = $this->translateTheReferenceFieldIdsToName($result['updated'], $syncModule, $user);
        
		/* Lookup Ids */
		$updatedIds = array(); $deletedIds = array();
		foreach($result['updated'] as $u){
            $updatedIds[] = $u['id'];
        }
		foreach($result['deleted'] as $d){
            $deletedIds[] = $d;
        }
        
        $syncServerDeleteIds = $this->getQueueDeleteRecord($syncId);
        foreach($syncServerDeleteIds as $deleteServerId){
            $deletedIds[] = $deleteServerId;
        }
        
		$updateDeleteCommonIds = array_values(array_intersect($updatedIds,$deletedIds));
		
		/*if the record exist in both the update and delete , then send record as update		
		and unset the id from deleted list
		*/
		
		$deletedIds = array_diff($deletedIds,$updateDeleteCommonIds);

		$updatedLookupIds = $this->idmap_get_clientmap($syncId, $updatedIds);		
		$deletedLookupIds = $this->idmap_get_clientmap($syncId, $deletedIds);
		
        $filteredCreates = array(); 
        $filteredUpdates = array();
        
        foreach ($result['updated'] as $u) {
        	
            if(in_array($u['id'],$updatedIds)){

            	if ( 
                	isset($updatedLookupIds[$u['id']]) && 
                	($u['modifiedtime'] > $updatedLookupIds[$u['id']]['servermodifiedtime'])
                ) {
                	
					$u['_id'] = $updatedLookupIds[$u['id']]['exchangeid']; // Add exchangeid
                    $u['_modifiedtime'] = $updatedLookupIds[$u['id']]['exchangemodifiedtime'];
                    $u['_syncidentificationkey'] = '';
                    
                    $filteredUpdates[] = $u;
                    
                } else if (empty($updatedLookupIds[$u['id']])){
                	
                	/* add record to create array */
                	
                    $u['id'] = $u['id'];
                    $u['_modifiedtime'] = $u['modifiedtime'];
                    $u['_id'] = '';
                    $u['_syncidentificationkey'] = '';
                    
                    $filteredCreates[] = $u;
                }
            }
		}
	
		$filteredDeletes = array();
		foreach ($deletedIds as $d) {
          	if (isset($deletedLookupIds[$d])) {
				$filteredDeletes[] = array(
					'_id' => $deletedLookupIds[$d]['exchangeid'], /* Add exchangeid;*/
					'_syncidentificationkey' => '',
				);
			}
		}
	
		$result['created'] = $filteredCreates;
		$result['updated'] = $filteredUpdates;
		$result['deleted'] = $filteredDeletes;
		
		return $result;
	}
	
	
	
	
	function vtigerpull($lastSync,$elementType,$syncType,$user){
		
		global $adb, $recordString,$modifiedTimeString;
        
		$numRecordsLimit = $this->maxResults;
		
		$datetime = $lastSync;
		if($datetime == '' || !strtotime($datetime) > 0){
			$datetime = 0;
		}
		
		/*$setypeArray = array();
		$setypeData = array();
		$setypeHandler = array();
		$setypeNoAccessArray = array();*/

		$output = array();
		$output["updated"] = array();
		$output["deleted"] = array();
		
		$applicationSync = false;

		$ownerIds = array($user->id);
		
		/* get accessible modules and entity modules */
		
		$accessableModules = array();
		$entityModules = array();
		
		$modulesDetails = vtws_listtypes(null,$user);
		$moduleTypes = $modulesDetails['types'];
		$modulesInformation = $modulesDetails["information"];

		foreach($modulesInformation as $moduleName=>$entityInformation){
			if($entityInformation["isEntity"])	
				$entityModules[] = $moduleName;
		}
		
		if(!in_array($elementType,$entityModules))	throw new Exception("Permission to perform the operation is denied");
			
		$accessableModules[] = $elementType;
		
		$handler = vtws_getModuleHandlerFromName($elementType, $user);
		$moduleMeta = $handler->getMeta();

		$entityDefaultBaseTables = $moduleMeta->getEntityDefaultTableList();

		/* since there will be only one base table for all entities */
		$baseCRMTable = $entityDefaultBaseTables[0];

		if($elementType=="Calendar" || $elementType=="Events" ){
			$baseCRMTable = $this->getSyncQueryBaseTable($elementType);
		}
		
		$q = "SELECT MAX(modifiedtime) as modifiedtime FROM $baseCRMTable 
		WHERE  modifiedtime>? and setype IN(".generateQuestionMarks($accessableModules).") ";
		
		$params = array($datetime);
		
		foreach($accessableModules as $entityModule){
			if($entityModule == "Events")
				$entityModule = "Calendar";
			$params[] = $entityModule;
		}
		
		if(!$applicationSync){
			$q .= ' and smownerid IN('.generateQuestionMarks($ownerIds).')';
			$params = array_merge($params,$ownerIds);
		}
		
		$q .=" order by modifiedtime limit $numRecordsLimit";
		$result = $adb->pquery($q,$params);
	
		$maxModifiedTime = $adb->query_result($result,0,'modifiedtime');
		
		if( $maxModifiedTime == '' || $maxModifiedTime == '0' ){
			$maxModifiedTime = $datetime;
		}
		
		foreach($accessableModules as $elementType){
			
			$handler = vtws_getModuleHandlerFromName($elementType, $user);
			
			$moduleMeta = $handler->getMeta();
			
			$deletedQueryCondition = $moduleMeta->getEntityDeletedQuery();
			
			preg_match_all("/(?:\s+\w+[ \t\n\r]+)?([^=]+)\s*=([^\s]+|'[^']+')/",$deletedQueryCondition,$deletedFieldDetails);
			$fieldNameDetails = $deletedFieldDetails[1];
			$deleteFieldValues = $deletedFieldDetails[2];
			
			$deleteColumnNames = array();
			foreach($fieldNameDetails as $tableName_fieldName){
				$fieldComp = explode(".",$tableName_fieldName);
				$deleteColumnNames[$tableName_fieldName] = $fieldComp[1];
			}
			
			$params = array($moduleMeta->getTabName(),$datetime,$maxModifiedTime);
			

			$queryGenerator = new QueryGenerator($elementType, $user);
			$fields = array();
			$moduleFields = $moduleMeta->getModuleFields();
            $moduleFieldNames = $this->getSelectClauseFields($elementType,$moduleMeta,$user);
			
            $moduleFieldNames[]='id';
			
            $queryGenerator->setFields($moduleFieldNames);
			$selectClause = "SELECT ".$queryGenerator->getSelectClauseColumnSQL();
			
			/* adding the fieldnames that are present in the delete condition to the select clause
			 since not all fields present in delete condition will be present in the fieldnames of the module
			 */
			
			foreach($deleteColumnNames as $table_fieldName=>$columnName){
				if(!in_array($columnName,$moduleFieldNames)){
					$selectClause .=", ".$table_fieldName;
				}
			}
			if($elementType=="Emails")
				$fromClause = vtws_getEmailFromClause();
			else
				$fromClause = $queryGenerator->getFromClause();

			$fromClause .= " INNER JOIN (select modifiedtime, crmid,deleted,setype FROM $baseCRMTable WHERE setype = ? and modifiedtime > ? and modifiedtime <= ? ";
			
			if(!$applicationSync){
				$fromClause.= ' and smownerid IN('.generateQuestionMarks($ownerIds).')';
				$params = array_merge($params,$ownerIds);
			}
			
			$fromClause.= ' ) vtiger_ws_sync ON (vtiger_crmentity.crmid = vtiger_ws_sync.crmid)';
			$q = $selectClause." ".$fromClause;
			
			$result = $adb->pquery($q, $params);
		
			$recordDetails = array();
			$deleteRecordDetails = array();
			
			$cntactivityids = array();
			
			$forModule = $moduleMeta->getTabName();
			
			$activityids = array();
			
			while($arre = $adb->fetchByAssoc($result)){
				
				$key = $arre[$moduleMeta->getIdColumn()];
				
				if($forModule == 'Calendar' && $arre['contactid'] > 0){
					if(!isset($cntactivityids[$key])) $cntactivityids[$key] = array("data" => DataTransform::sanitizeDataWithColumn($arre,$moduleMeta), "related_contacts" => array());
				    if(!in_array($arre['contactid'], $cntactivityids[$key]['related_contacts'])) $cntactivityids[$key]['related_contacts'][] = $arre['contactid'];
				}
				
				if(vtws_isRecordDeleted($arre,$deleteColumnNames,$deleteFieldValues)){
					if(!$moduleMeta->hasAccess()){
						continue;
					}
					$output["deleted"][] = vtws_getId($moduleMeta->getEntityId(), $key);
				}
				else{
					if(!$moduleMeta->hasAccess() ||!$moduleMeta->hasPermission(EntityMeta::$RETRIEVE,$key)){
						continue;
					}
					try{
					    if($forModule == 'Calendar'){
					        if(in_array($arre['contactid'], $cntactivityids[$key]['related_contacts'])) continue;   	
							$activityids[] = $key;
						}
						$output["updated"][] = DataTransform::sanitizeDataWithColumn($arre,$moduleMeta);
					}catch(Exception $e){
						throw new Exception("Unknown Error while processing request");
					}
				}
			}
		}

		if(!empty($cntactivityids)){
		    
		    foreach($cntactivityids as $activityid => $eventInfo){
		        
		        $updatedEventData = $eventInfo['data'];
		        
		        $updatedEventData['event_attendees'] = implode("##", $eventInfo['related_contacts']);
		        
		        $output["updated"][] = $updatedEventData;
		    }
		}
		
		if(!empty($activityids) && $forModule == 'Calendar'){
			$recurring_result = $adb->pquery("SELECT * FROM `vtiger_activity_recurring_info` where recurrenceid in (".generateQuestionMarks($activityids).")",array($activityids));
			if($adb->num_rows($recurring_result)){
				$recurringIds = array();
				while($rec_row = $adb->fetchByAssoc($recurring_result)){
					$recurringIds[] = vtws_getId($moduleMeta->getEntityId(), $rec_row['recurrenceid']);
				}
				$output['recurring_events'] = $recurringIds;
			}
		}
		
		$q = "SELECT crmid FROM $baseCRMTable WHERE modifiedtime>?  and setype IN(".generateQuestionMarks($accessableModules).")";
		$params = array($maxModifiedTime);

		foreach($accessableModules as $entityModule){
			if($entityModule == "Events")
				$entityModule = "Calendar";
			$params[] = $entityModule;
		}
		if(!$applicationSync){
			$q.='and smownerid IN('.generateQuestionMarks($ownerIds).')';
			$params = array_merge($params,$ownerIds);
		}
		
		$result = $adb->pquery($q,$params);
		if($adb->num_rows($result)>0){
			$output['more'] = true;
		}else{
			$output['more'] = false;
		}
		
		if(!$maxModifiedTime){
			$modifiedtime = $datetime;
		}else{
			$modifiedtime = $maxModifiedTime;
		}
		
		$output['lastModifiedTime'] = $modifiedtime;

		$error = $adb->hasFailedTransaction();
		/*$adb->completeTransaction();*/

		if($error){
			throw new Exception('Database Query Error');
		}

		return $output;
	}
	
	
	
	function getSyncQueryBaseTable($elementType){
		
		if($elementType!="Calendar" && $elementType!="Events"){

			return "vtiger_crmentity";
		
		} else{
			
			$activityCondition = "vtiger_activity.activitytype !='Task' and vtiger_activity.activitytype !='Emails'";
			
			$query = "vtiger_crmentity INNER JOIN vtiger_activity ON (vtiger_crmentity.crmid = vtiger_activity.activityid and $activityCondition)";
			return $query;
		}
	}

    
    function getSelectClauseFields($module,$moduleMeta,$user){
        $moduleFieldNames = $moduleMeta->getModuleFields();
        $inventoryModules = getInventoryModules();
        if(in_array($module, $inventoryModules)){
			
            $fields = vtws_describe('LineItem', $user);
            foreach($fields['fields'] as $field){
                unset($moduleFieldNames[$field['name']]);
            }
			foreach ($moduleFieldNames as $field => $fieldObj){
				if(substr($field, 0, 5) == 'shtax'){
					unset($moduleFieldNames[$field]);
				}
			}
            
        }
        return array_keys($moduleFieldNames);
    }
    
	
	public function push($recordList) {
		
		$this->setClientSyncType($this->getSynchronizeController()->getSyncType());
		
		$pushResult = $this->put($this->convertToPushSyncTrackerFormat($recordList), $this->getSynchronizeController()->user);
		
		$pushResponseRecordList = array();
		foreach ($pushResult as $mode => $records) {
			if ($mode == 'created') {
				$recordMode = MSExchange_SyncRecord_Model::CREATE_MODE;
			} else if ($mode == 'updated') {
				$recordMode = MSExchange_SyncRecord_Model::UPDATE_MODE;
			} else if ($mode == 'deleted') {
    		    $recordMode = MSExchange_SyncRecord_Model::DELETE_MODE;
    		} else {
    		    $recordMode = 'skipped';
    		}
			foreach ($records as $record) {
				$pushResponseRecordList[] = $this->getRecordModelFromData($record)->setMode($recordMode)->setType($this->getSynchronizeController()->getSourceType());
			}
		}
		
		return $pushResponseRecordList;
	}
	
	
	public function put($records, $user) {
	    
	    $syncController = $this->getSynchronizeController();
	    
	    $syncId = $syncController->syncId;
	    
		$db = PearDatabase::getInstance();
        
        $createRecords = array();
        $updateRecords = array();
        $deleteRecords = array();

        $clientModifiedTimeList = array();

        foreach ($records as $record) {
        	
            $clientRecordId = $record['id'];	
		
			if (empty($clientRecordId)) continue;
		
			$lookupRecordId = false;
			
			$lookupResult = $db->pquery("SELECT * FROM vtiger_msexchange_recordmapping 
			WHERE BINARY exchangeid = ?", array($clientRecordId));
			
			if ($db->num_rows($lookupResult)) 
				$lookupRecordId = $db->query_result($lookupResult, 0, 'serverid');
			
			if($record['mode'] == "delete"){
			    
			    if( !(empty($lookupRecordId)) ){
			        
			        $record['id'] = $lookupRecordId;
			        
			        $deleteRecords[$clientRecordId] = $record;
			    
			    } else {
			        
			        if($record['module'] == 'Events'){
			            
			            $lookupResult = $db->pquery("SELECT * FROM vtiger_msexchange_recordmapping
			            WHERE BINARY parent_exchangeid = ?", array($clientRecordId));
			            
			            if ($db->num_rows($lookupResult)){
			                
			                $series = array();
			                
			                while($childRow = $db->fetchByAssoc($lookupResult)){
			                    $series[$childRow['serverid']] = $childRow['exchangeid'];
			                }
			                
			                $deleteRecords[$clientRecordId] = $record;
			                $deleteRecords[$clientRecordId]['master_event'] = true;
			                $deleteRecords[$clientRecordId]['series_ids'] = $series;
			            }
			        }
			    }
			
			} else {
			
    			if ( empty($lookupRecordId) && !empty($record['values']) ) {
                    
    			    if($record['module'] == 'Events'){
    			        $record['values']['eventstatus'] = "Planned";
    			        $record['values']['activitytype'] = "Meeting";
    			    }
    			    
                	$createRecords[$clientRecordId] = $record['values'];
                    $createRecords[$clientRecordId]['module'] = $record['module'];
                    $clientModifiedTimeList[$clientRecordId] = $record['values']['modifiedtime'];
    			
                } else {
    
                	if( !(empty($lookupRecordId)) ){
                		
                		$vtiger_id_components = explode('x',$lookupRecordId);
                		
                		if( isset($vtiger_id_components[1]) ){
                			
                			$deleted_recordResult = $db->pquery("SELECT * FROM vtiger_crmentity 
                			WHERE crmid = ? and deleted = '0'", array($vtiger_id_components[1]));
    						
                			if ($db->num_rows($deleted_recordResult)){
                				unset($vtiger_id_components);
                			} else {
                				continue;
                			}
                		} else {
                			continue;
                		}
    			
    	            	if(!empty($record['values'])) {	
    	            	
    	            		$clientLastModifiedTime = $db->query_result($lookupResult,0,'exchangemodifiedtime');
    						
    	            		if($clientLastModifiedTime >= $record['values']['modifiedtime'])
    							continue;
    						
    						$record['values']['id'] = $lookupRecordId;
    	                    
    	                    $updateRecords[$clientRecordId] = $record['values'];
    	                    $updateRecords[$clientRecordId]['module'] = $record['module'];
    	                    
    	                    $clientModifiedTimeList[$clientRecordId] = $record['values']['modifiedtime'];
    					
    	            	}
                	}
    			}
			}
        }
        
        $recordDetails = array(
        	'created' => $createRecords,
        	'updated' => $updateRecords,
        	'deleted' => $deleteRecords
        );
        
        $vtigerSaveResult = $this->vtigersave($recordDetails,$user);
        
        $response = array(
        	'created' => array(),
        	'updated' => array(),
        	'deleted' => array()
        );
        
        $deleteQueueSyncServerIds = $deleteQueueSyncServerMasterIds = array();
        
        foreach($vtigerSaveResult['created'] as $clientRecordId => $record){
			
            $exchangeData = $createRecords[$clientRecordId];
            
            if($exchangeData['module'] == "Events")
                $parent_id = $exchangeData['parent_id'];
            else 
                $parent_id = false;
            
            $this->idmap_put($syncId, $record['id'], $clientRecordId, $clientModifiedTimeList[$clientRecordId], $record['modifiedtime'], $this->create, $parent_id);
	   			   		
			$responseRecord = $record;					
			$responseRecord['_id'] = $clientRecordId;	
			$responseRecord['_modifiedtime'] = $clientModifiedTimeList[$clientRecordId];
			$responseRecord['_syncidentificationkey'] = $recordDetails['created'][$clientRecordId]['_syncidentificationkey'];

			$response['created'][] = $responseRecord;
		}
       
       
       	foreach($vtigerSaveResult['updated'] as $clientRecordId => $record){
       	    
       	    $exchangeData = $updateRecords[$clientRecordId];
       	    
       	    if($exchangeData['module'] == "Events")
       	        $parent_id = $exchangeData['parent_id'];
   	        else
   	            $parent_id = false;
   	            
   	            $this->idmap_put($syncId,$record['id'], $clientRecordId,$clientModifiedTimeList[$clientRecordId],$record['modifiedtime'], $this->update, $parent_id);
		   	
       		$responseRecord = $record;
		  	$responseRecord['_id'] = $clientRecordId;
		   	$responseRecord['_modifiedtime'] = $clientModifiedTimeList[$clientRecordId];
		   	$responseRecord['_syncidentificationkey'] = $recordDetails['updated'][$clientRecordId]['_syncidentificationkey'];
			
		   	$response['updated'][] = $responseRecord;
       	}
       	
       	foreach($vtigerSaveResult['deleted'] as $clientRecordId => $record){

       	    if($record['module'] == "Events"){
       	        
       	        if(isset($record['master_event']) && $record['master_event'] == 1){
       	            
       	            $deleteQueueSyncServerMasterIds[] = $clientRecordId;
       	            
       	            $responseRecord = array(
       	                '_id' => $clientRecordId,
       	                '_syncidentificationkey' => $recordDetails['deleted'][$clientRecordId]['_syncidentificationkey']
       	            );
       	           
       	            $response['deleted'][] = $responseRecord;
       	            
       	            continue;
       	        }
       	    }
       	        
   	        $syncServerId = $this->getSyncServerId($clientRecordId,$record['id'],$syncId);
       	    
       	    $this->idmap_put($syncId, $record['id'], $clientRecordId,"","",$this->delete);
       	    
       	    if(isset($syncServerId) && $syncServerId != NULL){
       	        $deleteQueueSyncServerIds[] = $syncServerId;
       	    }
           	
           	$responseRecord = array(
           	    '_id' => $clientRecordId,
           	    '_syncidentificationkey' => $recordDetails['deleted'][$clientRecordId]['_syncidentificationkey']
           	);
           	
       		if(is_array($record)){
       		    $responseRecord = array_merge($responseRecord, $record);
       		}
       		
           	$response['deleted'][] = $responseRecord;
       	}
       	
       	if(count($deleteQueueSyncServerIds)>0){
       	    $this->deleteQueueRecords($deleteQueueSyncServerIds);
       	}
       	
       	if(count($deleteQueueSyncServerMasterIds)>0){
       	    $this->deleteQueueMasterRecords($deleteQueueSyncServerMasterIds);
       	}
       	
        return $response;
	}
	
	public function vtigersave($recordDetails, $user){
		
		$this->user = $user;
		$adb = PearDatabase::getInstance();
		
		$recordDetails = $this->syncToNativeVtigerSaveFormat($recordDetails);

        $createdRecords = $recordDetails['created'];
        $updatedRecords = $recordDetails['updated'];
        $deletedRecords = $recordDetails['deleted'];
        
        if (count($createdRecords) > 0) {
            $createdRecords = $this->translateReferenceFieldNamesToIds($createdRecords, $user);
            $createdRecords = $this->fillNonExistingMandatoryPicklistValues($createdRecords);
            $createdRecords = $this->fillMandatoryFields($createdRecords, $user);
            
	        foreach ($createdRecords as $index => $record) {
	            
	            if(isset($_REQUEST['contactidlist'])){ 
	                $_REQUEST['contactidlist'] = "";
	                unset($_REQUEST['contactidlist']);
	            }
	            
	            if($this->getSynchronizeController()->getSourceType() == "Events"){
    	            $_REQUEST['contactidlist'] = "";
    	            if(isset($record['contactidlist']) && !empty($record['contactidlist'])){
	                    $_REQUEST['contactidlist'] = $record['contactidlist'];
    	                unset($record['contactidlist']);
    	            } 
	            }
	            $createdRecords[$index] = vtws_create($record['module'], $record, $this->user);
	        }
        }

        if (count($updatedRecords) > 0) {
        	
            $updatedRecords = $this->translateReferenceFieldNamesToIds($updatedRecords, $user);
            
	        $crmIds = array();
	
	        foreach ($updatedRecords as $index => $record) {
	            $webserviceRecordId = $record["id"];
	            $recordIdComp = vtws_getIdComponents($webserviceRecordId);
	            $crmIds[] = $recordIdComp[1];
	        }
	        
	        $assignedRecordIds = array();
	        
	        if ($this->isClientUserSyncType()|| $this->isClientUserAndGroupSyncType()) {
	
	        	$assignedRecordIds = $this->checkIfRecordsAssignToUser($crmIds, $this->user->id);
	        
	            /* To check if the record assigned to group */
	            if($this->isClientUserAndGroupSyncType()){                
	
	            	$groupIds = $this->getGroupIds($this->user->id);
	                
	            	foreach ($groupIds as $group) {
	                    $groupRecordId = $this->checkIfRecordsAssignToUser($crmIds, $group);
	                    $assignedRecordIds = array_merge($assignedRecordIds, $groupRecordId);
	                }
	            }
	        	/*  End */
	        }
	        
	        
	        foreach ($updatedRecords as $index => $record) {
	            $webserviceRecordId = $record["id"];
	            $recordIdComp = vtws_getIdComponents($webserviceRecordId);
	    
	            try {
	            	
	            	if( $this->getSynchronizeController()->getSourceType() == 'Events' ){			
	            			
	            		$focus = CRMEntity::getInstance('Calendar');
	            		$focus->id = $recordIdComp[1];
	            		$focus->retrieve_entity_info($recordIdComp[1], 'Events');
	            		
	            		$old_assignee = $focus->column_fields['assigned_user_id'];
		            	
	            		//fetch invitees before saving event
	            		
	            		$invitees = array();
		            	
	            		$invitees_result = $adb->pquery("SELECT CONVERT( GROUP_CONCAT( inviteeid ) USING utf8 ) as invitees 
	            		FROM  `vtiger_invitees` WHERE activityid =  ?",array($recordIdComp[1]));
	            		if($adb->num_rows($invitees_result)){
	            			$invites = $adb->query_result($invitees_result, 0, 'invitees');
	            			if($invites != ''){
	            				$invitees = explode(',',$invites);
	            			}	
	            		}
	            		
	            		
	            		foreach( $focus->column_fields as $fieldname => $oldval){
	            			
	            			if( isset($record[$fieldname]) ){
	            				
	            				if( $fieldname == 'assigned_user_id' ){
	            					$focus->column_fields[$fieldname] = $oldval;
	            				} else {
	            					$focus->column_fields[$fieldname] = $record[$fieldname];
	            				}
	            			}
	            		}
	            		
	            		if( $old_assignee != $this->user->id ){
	            		    
	            		    if( $record['isorganizer'] == '1' ){
	            		        
	            		        //it means current user is organizer and
	            		        //old assignee is invitee so add to invitees array
	            		        
	            		        if( empty($invitees) || !in_array($old_assignee, $invitees) ){
	            		            $invitees[] = $old_assignee;
	            		        }
	            		        $focus->column_fields['assigned_user_id'] = $this->user->id;
	            		    } else {
	            		        
	            		        //if current user is not organizer and event is already synced
	            		        //then add user to invitees
	            		        
	            		        if( empty($invitees) || !in_array($this->user->id, $invitees) ){
	            		            $invitees[] = $this->user->id;
	            		        }
	            		    }
	            		}
	            		
	            		$focus->id = $recordIdComp[1];
	            		$focus->mode = 'edit';
	            		
	            		// get Selected Attendess
	            		$query = 'SELECT * from vtiger_cntactivityrel where activityid=?';
	            		$result = $adb->pquery($query, array($recordIdComp[1]));
	            		$num_rows = $adb->num_rows($result);
	            		
	            		$contactIdList = array();
	            		for($i=0; $i<$num_rows; $i++) {
	            		    $row = $adb->fetchByAssoc($result, $i);
	            		    $contactIdList[$i] = $row['contactid'];
	            		}
	            		
	            		$_REQUEST['contactidlist'] = "";
	            		
	            		if(isset($record['contactidlist']) && !empty($record['contactidlist'])){
	            		    $_REQUEST['contactidlist'] = $record['contactidlist'];
	            		    unset($record['contactidlist']);
	            		} else if(!empty($contactIdList)){
	            		    $_REQUEST['contactidlist'] = implode(";", $contactIdList);
	            		}
	            		
	            		$focus->save('Events');
	            		
	            		$focus->retrieve_entity_info($focus->id, 'Events');
	            		
	            		if( !empty($invitees) ){
	            			$sql = "delete from vtiger_invitees where activityid = ?";
							$adb->pquery($sql, array($focus->id));
							foreach($invitees as $inviteeid){
								if($inviteeid != ''){
									//$query="insert into vtiger_invitees values(?,?)";
									//$adb->pquery($query, array($focus->id, $inviteeid));
								}
							}
	            		}
	            		
	            		$updatedRecords[$index] = $focus->column_fields;
	            		$updatedRecords[$index]['id'] = vtws_getWebserviceEntityId('Events', $focus->id);
		            	
	            	} else {
	            		if (in_array($recordIdComp[1], $assignedRecordIds)) {
		                    $updatedRecords[$index] = vtws_revise($record, $this->user);
		                } else if (!$this->isClientUserSyncType()) {
		                    $updatedRecords[$index] = vtws_revise($record, $this->user);
		                } else {
		                    $this->assignToChangedRecords[$index] = $record;
		                }
	            	}
	            	
	                
	            } catch (Exception $e) {
	                continue;
	            }
	        }
	        
        }

        
        $hasDeleteAccess = null;
        $deletedCrmIds = array();
        
        if (count($deletedRecords) > 0) {
        
	        foreach ($deletedRecords as $index => $record) {
	            if(isset($record['master_event']) && $record['master_event'] == 1){
	                $seriesIds = array_keys($record['series_ids']);
	                if(!empty($seriesIds)){
	                    foreach($seriesIds as $seriesId){
	                        $webserviceRecordId = $seriesId;
	                        $recordIdComp = vtws_getIdComponents($webserviceRecordId);
	                        $deletedCrmIds[] = $recordIdComp[1];
	                    }
	                }
	            } else {
    	            $webserviceRecordId = $record['id'];
    	            $recordIdComp = vtws_getIdComponents($webserviceRecordId);
    	            $deletedCrmIds[] = $recordIdComp[1];
	            }
	        }
	    
	        $assignedDeletedRecordIds = $this->checkIfRecordsAssignToUser($deletedCrmIds, $this->user->id);
        
	        /* To get record id's assigned to group of the current user */
	        
	        if($this->isClientUserAndGroupSyncType()){
				foreach ($groupIds as $group) {
					$groupRecordId = $this->checkIfRecordsAssignToUser($deletedCrmIds, $group);
	                $assignedDeletedRecordIds = array_merge($assignedDeletedRecordIds, $groupRecordId);
	           }
	        }
	        
	        /* End */
        
	        foreach ($deletedRecords as $index => $record) {
	            
	            if(isset($record['master_event']) && $record['master_event'] == 1){
	                
	                $seriesIds = array_keys($record['series_ids']);
	                
	                if(!empty($seriesIds)){
	                    
	                    foreach($seriesIds as $seriesId){
	                        
	                        $idComp = vtws_getIdComponents($seriesId);
	                        
	                        if (empty($hasDeleteAccess)) {
	                            $handler = vtws_getModuleHandlerFromId($idComp[0], $this->user);
	                            $meta = $handler->getMeta();
	                            $hasDeleteAccess = $meta->hasDeleteAccess();
	                        }
	                        
	                        if ($hasDeleteAccess) {
	                            
	                            if (in_array($idComp[1], $assignedDeletedRecordIds)) {
	                                
	                                try {
	                                    vtws_delete($record['id'], $this->user);
	                                } catch (Exception $e) {
	                                    continue;
	                                }
	                            }
	                        }
	                    }
	                }
	            } else {
	                
	                $idComp = vtws_getIdComponents($record['id']);
    	            
    	            if (empty($hasDeleteAccess)) {
    	                $handler = vtws_getModuleHandlerFromId($idComp[0], $this->user);
    	                $meta = $handler->getMeta();
    	                $hasDeleteAccess = $meta->hasDeleteAccess();
    	            }
    	            
    	            if ($hasDeleteAccess) {
    	                if (in_array($idComp[1], $assignedDeletedRecordIds)) {
    	                    try {
    	                        vtws_delete($record['id'], $this->user);
    	                    } catch (Exception $e) {
    	                        continue;
    	                    }
    	                }
    	            }
	            }
	        }
        }

        $recordDetails['created'] = $createdRecords;
        $recordDetails['updated'] = $updatedRecords;
        $recordDetails['deleted'] = $deletedRecords;
        return $recordDetails;
        
	}
	
    public function syncToNativeVtigerSaveFormat($element) {
        $syncCreatedRecords = $element['created'];
        $nativeCreatedRecords = array();
        foreach ($syncCreatedRecords as $index => $createRecord) {
            if (empty($createRecord['assigned_user_id'])) {
                $createRecord['assigned_user_id'] = vtws_getWebserviceEntityId("Users", $this->user->id);
            }
            $nativeCreatedRecords[$index] = $createRecord;
        }
        $element['created'] = $nativeCreatedRecords;
        return $element;
    }
    
    public function translateReferenceFieldNamesToIds($entityRecords, $user) {
        $entityRecordList = array();
        foreach ($entityRecords as $index => $record) {
            $entityRecordList[$record['module']][$index] = $record;
        }
        foreach ($entityRecordList as $module => $records) {
            $handler = vtws_getModuleHandlerFromName($module, $user);
            $meta = $handler->getMeta();
            $referenceFieldDetails = $meta->getReferenceFieldDetails();

            foreach ($referenceFieldDetails as $referenceFieldName => $referenceModuleDetails) {
                $recordReferenceFieldNames = array();
                foreach ($records as $index => $recordDetails) {
                    if (!empty($recordDetails[$referenceFieldName])) {
                        $recordReferenceFieldNames[] = $recordDetails[$referenceFieldName];
                    }
                }
                $entityNameIds = $this->getRecordEntityNameIds(array_values($recordReferenceFieldNames), $referenceModuleDetails, $user);
                foreach ($records as $index => $recordInfo) {
                    if(array_key_exists($referenceFieldName, $recordInfo)){
                        $array = explode('x',$recordInfo[$referenceFieldName]); 
						if(is_numeric($array[0]) && is_numeric($array[1])){ 
                            $recordInfo[$referenceFieldName] = $recordInfo[$referenceFieldName]; 
                        }elseif (!empty($entityNameIds[$recordInfo[$referenceFieldName]])) {
                            $recordInfo[$referenceFieldName] = $entityNameIds[$recordInfo[$referenceFieldName]];
                        } else {
                            $recordInfo[$referenceFieldName] = "";
                        }
                    }
                    $records[$index] = $recordInfo;
                }
            }
            $entityRecordList[$module] = $records;
        }

        $crmRecords = array();
        foreach ($entityRecordList as $module => $entityRecords) {
            foreach ($entityRecords as $index => $record) {
                $crmRecords[$index] = $record;
            }
        }
        return $crmRecords;
    }

    public function translateTheReferenceFieldIdsToName($records, $module, $user) {
        $db = PearDatabase::getInstance();
        global $current_user;
        $current_user = $user;
        $handler = vtws_getModuleHandlerFromName($module, $user);
        $meta = $handler->getMeta();
        $referenceFieldDetails = $meta->getReferenceFieldDetails();
        foreach ($referenceFieldDetails as $referenceFieldName => $referenceModuleDetails) {
            $referenceFieldIds = array();
            $referenceModuleIds = array();
            $referenceIdsName = array();
            foreach ($records as $recordDetails) {
                $referenceWsId = $recordDetails[$referenceFieldName];
                if (!empty($referenceWsId)) {
                    $referenceIdComp = vtws_getIdComponents($referenceWsId);
                    $webserviceObject = VtigerWebserviceObject::fromId($db, $referenceIdComp[0]);
                    if ($webserviceObject->getEntityName() == 'Currency') {
                        continue;
                    }
                    $referenceModuleIds[$webserviceObject->getEntityName()][] = $referenceIdComp[1];
                    $referenceFieldIds[] = $referenceIdComp[1];
                }
            }

            foreach ($referenceModuleIds as $referenceModule => $idLists) {
                $nameList = getEntityName($referenceModule, $idLists);
                foreach ($nameList as $key => $value)
                    $referenceIdsName[$key] = $value;
            }
            $recordCount = count($records);
            for ($i = 0; $i < $recordCount; $i++) {
                $record = $records[$i];
                if (!empty($record[$referenceFieldName])) {
                    $wsId = vtws_getIdComponents($record[$referenceFieldName]);
                    $record[$referenceFieldName] = decode_html($referenceIdsName[$wsId[1]]);
                }
                $records[$i] = $record;
            }
        }
        return $records;
    }


	public function fillMandatoryEmptyFields($moduleName, $recordLists, $user) {
		$handler = vtws_getModuleHandlerFromName($moduleName, $user);
		$meta = $handler->getMeta();
		$fields = $meta->getModuleFields();
		$mandatoryFields = $meta->getMandatoryFields();
		$ownerFields = $meta->getOwnerFields();
		$transformedRecords = array();
		foreach ($recordLists as $record) {
			foreach ($mandatoryFields as $fieldName) {
				/* ignore owner fields */
				if (in_array($fieldName, $ownerFields)) {
					continue;
				}

				$fieldInstance = $fields[$fieldName];
				$currentFieldValue = $record->get($fieldName);
				if (!empty($currentFieldValue)) {
					continue;
				}
				/* Dont fill mandatory fields if empty is passed and if the record is in update mode
				Since sync app is using revise to update
				*/
				if($record->getMode() == MSExchange_SyncRecord_Model::UPDATE_MODE) {
					continue;
				}
				$fieldDataType = $fieldInstance->getFieldDataType();
				$defaultValue = $fieldInstance->getDefault();
				$value = '';
				switch ($fieldDataType) {
					case 'date':
						$value = $defaultValue;
						if (empty($defaultValue)) {
							$dateObject = new DateTime();
							$value = $dateObject->format('Y-m-d');
						}
						break;

					case 'text':
						$value = '?????';
						if (!empty($defaultValue)) {
							$value = $defaultValue;
						}
						break;
					case 'phone':
						$value = '?????';
						if (!empty($defaultValue)) {
							$value = $defaultValue;
						}
						break;
				}
				$record->set($fieldName, $value);
			}
			$transformedRecords[] = $record;
		}
		return $transformedRecords;
	}
	
	public function fillNonExistingMandatoryPicklistValues($recordList) {
		
        /* Meta is cached to eliminate overhead of doing the query 
         every time to get the meta details(retrieveMeta) 
         */
        
		$modulesMetaCache = array();
        foreach ($recordList as $index => $recordDetails) {
            if (!array_key_exists($recordDetails['module'], $modulesMetaCache)) {
                $handler = vtws_getModuleHandlerFromName($recordDetails['module'], $this->user);
                $meta = $handler->getMeta();
                $modulesMetaCache[$recordDetails['module']] = $meta;
            }
            $moduleMeta = $modulesMetaCache[$recordDetails['module']];
            $mandatoryFieldsList = $meta->getMandatoryFields();
            $moduleFields = $meta->getModuleFields();
            foreach ($mandatoryFieldsList as $fieldName) {
                $fieldInstance = $moduleFields[$fieldName];
                if (empty($recordDetails[$fieldName]) &&
                        ($fieldInstance->getFieldDataType() == "multipicklist" || $fieldInstance->getFieldDataType() == "picklist")) {
                    $pickListDetails = $fieldInstance->getPicklistDetails($webserviceField);
                    $defaultValue = $pickListDetails[0]['value'];
                    $recordDetails[$fieldName] = $defaultValue;
                }
            }
            $recordList[$index] = $recordDetails;
        }
        return $recordList;
    }

    /**
     * Function to fillMandatory fields in vtiger with given values
     * @param $recordLists
     * @param $user
     * @return array of Transformed Record
     */
    public function fillMandatoryFields($recordLists, $user) {
        $transformedRecords = array();
        foreach ($recordLists as $index => $record) {
            $handler = vtws_getModuleHandlerFromName($record['module'], $user);
            $meta = $handler->getMeta();
            $fields = $meta->getModuleFields();
            $mandatoryFields = $meta->getMandatoryFields();
            $ownerFields = $meta->getOwnerFields();
            foreach ($mandatoryFields as $fieldName) {
                /* ignore owner fields */
                if (in_array($fieldName, $ownerFields)) {
                    continue;
                }

                $fieldInstance = $fields[$fieldName];
                $currentFieldValue = $record[$fieldName];
                if (!empty($currentFieldValue)) {
                    continue;
                }

                $fieldDataType = $fieldInstance->getFieldDataType();
                $defaultValue = $fieldInstance->getDefault();
                $value = '';
                switch ($fieldDataType) {
                    case 'date':
                        $value = $defaultValue;
                        if (empty($defaultValue)) {
                            $dateObject = new DateTime();
                            $value = $dateObject->format('Y-m-d');
                        }
                        break;

                    case 'text':
                        $value = '?????';
                        if (!empty($defaultValue)) {
                            $value = $defaultValue;
                        }
                        break;
                    case 'phone':
                        $value = '?????';
                        if (!empty($defaultValue)) {
                            $value = $defaultValue;
                        }
                        break;
                    case 'boolean':
                        $value = false;
                        if (!empty($defaultValue)) {
                            $value = $defaultValue;
                        }
                        break;
                    case 'email':
                        $value = '?????';
                        if (!empty($defaultValue)) {
                            $value = $defaultValue;
                        }
                        break;
                    case 'string':
                        $value = '?????';
                        if (!empty($defaultValue)) {
                            $value = $defaultValue;
                        }
                        break;
                    case 'url':
                        $value = '?????';
                        if (!empty($defaultValue)) {
                            $value = $defaultValue;
                        }
                        break;
                    case 'integer':
                        $value = 0;
                        if (!empty($defaultValue)) {
                            $value = $defaultValue;
                        }
                        break;
                    case 'double':
                        $value = 00.00;
                        if (!empty($defaultValue)) {
                            $value = $defaultValue;
                        }
                        break;
                    case 'currency':
                        $value = 0.00;
                        if (!empty($defaultValue)) {
                            $value = $defaultValue;
                        }
                        break;
                }
                $record[$fieldName] = $value;
            }
            $transformedRecords[$index] = $record;
        }
        return $transformedRecords;
    }
	
	
	
	
	public function setClientSyncType($syncType = 'user') {
        $this->clientSyncType = $syncType;
        return $this;
    }

    public function isClientUserSyncType() {
        return ($this->clientSyncType == 'user') ? true : false;
    }
    
    public function isClientUserAndGroupSyncType() {
        return ($this->clientSyncType == 'userandgroup') ? true : false;
    }
    
    public function getAssignToChangedRecords() {
        return $this->assignToChangedRecords;
    }
    
    
    
	public function postEvent($type, $synchronizedRecords) {
		if ($type == 'pull') {
			$this->map($synchronizedRecords);
			if( $this->nextSyncTime != '' )
				MSExchange_Utils_Helper::updateLastVtigerSyncTime($this->sourceModule, $this->nextSyncTime);
		}
	}

	public function map($synchronizedRecords) {
		$deleteQueueSyncServerIds = array();
	    $mapFormatedRecords = array();
		$mapFormatedRecords['create'] = array();
		$mapFormatedRecords['update'] = array();
		$mapFormatedRecords['delete'] = array();
		
		$syncController = $this->getSynchronizeController();
		$syncId = $syncController->syncId;
		
		foreach ($synchronizedRecords as $sourceAndTargetRecord) {
			
			$sourceRecord = $sourceAndTargetRecord['source']; 
			
			$destinationRecord = $sourceAndTargetRecord['target'];
			
			if ($destinationRecord->isCreateMode()) {

				$mapFormatedRecords['create'][$destinationRecord->getId()] = array('serverid' => $sourceRecord->getId(),
					'modifiedtime' => $destinationRecord->getModifiedTime(),
					'_modifiedtime' => $sourceRecord->getModifiedTime());
			
			} else if ($destinationRecord->isDeleteMode()) {
				
				$mapFormatedRecords['delete'][] = $destinationRecord->getEntityData()['id'];
			
			} else {
				
				$mapFormatedRecords['update'][$destinationRecord->getId()] = array('serverid' => $sourceRecord->getId(),
					'modifiedtime' => $destinationRecord->getModifiedTime(),
					'_modifiedtime' => $sourceRecord->getModifiedTime());
			}			
		}
				
		/* now save mapping in exchange_recordmapping table */
       
	    $createDetails = $mapFormatedRecords["create"];
        $deleteDetails = $mapFormatedRecords["delete"];
        $updatedDetails = $mapFormatedRecords["update"];
		
        if(count($createDetails)>0){
	        foreach ($createDetails as $clientid => $serverDetails) {
	            $this->idmap_put($syncId, $serverDetails['serverid'], $clientid, $serverDetails['modifiedtime'], $serverDetails['_modifiedtime'], $this->create);
			}
        }
        
        if(count($updatedDetails)>0){
	    	foreach($updatedDetails as $clientid => $serverDetails){
	    	    $this->idmap_put($syncId, $serverDetails['serverid'], $clientid, $serverDetails['modifiedtime'], $serverDetails['_modifiedtime'], $this->update);
	    	}
        }
        
		if(count($deleteDetails)>0){
			
		    $deleteLookUps = $this->idmap_get_clientservermap($syncId, array_values($deleteDetails));
            
			foreach($deleteDetails as $clientid){
            	
                if(isset($deleteLookUps[$clientid])){                	
					$serverId = $deleteLookUps[$clientid];
					$syncServerId = $this->getSyncServerId($clientid,$serverId,$syncId);
					if(isset($syncServerId) && $syncServerId != NULL){
						$deleteQueueSyncServerIds[] = $syncServerId;
					}
					$this->idmap_put($syncId, $serverId, $clientid,"","",$this->delete);
                }
            }
        }
        
        if(count($deleteQueueSyncServerIds)>0){
            $this->deleteQueueRecords($deleteQueueSyncServerIds);
        }	

	}
    
	function moreRecordsExits(){
	    return $this->moreRecords;
	}
	
}
?>
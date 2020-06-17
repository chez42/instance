<?php
use garethp\ews\API;
use garethp\ews\API\Type;
use garethp\ews\API\Enumeration;

class MSExchange_ExchangeCalendar_Model extends MSExchange_MSExchange_Model{
    
    function getEvents($folderId, $lastUpdatedTime, $offset = 0){
        
        $request = array(
            'Traversal' => Enumeration\ItemQueryTraversalType::SHALLOW,
            'ItemShape' => array(
                'BaseShape' => 'IdOnly',
                'BodyType' => 'Text',
				'AdditionalProperties' => array('FieldURI' => array('FieldURI' => API\FieldURIManager::getFieldUriByName('lastModifiedTime'))),
            ),
            'ParentFolderIds' => array(
                'FolderId' => $folderId->toXmlObject()
            ),
			'SortOrder' => array(
                "FieldOrder" => array(
                    "Order" => "Ascending",
                    'FieldURI' => array('FieldURI' => API\FieldURIManager::getFieldUriByName('lastModifiedTime')),
                )
            ),
            'CalendarView' => array('MaxEntriesReturned' => $this->maxEntriesReturned),
            'IndexedPageItemView' => array ('MaxEntriesReturned' => $this->maxEntriesReturned, 'Offset' => $offset, 'BasePoint' => 'Beginning')
        );
        
        if(!$lastUpdatedTime){
            
            $options = array();
            
        }else{
            $options = array('Restriction' =>
                array(
                    'IsGreaterThanOrEqualTo' => array(
                        'FieldURI' => array('FieldURI' => API\FieldURIManager::getFieldUriByName('lastModifiedTime')),
                        'FieldURIOrConstant' => array('Constant' => array('Value' => $lastUpdatedTime))
                    ),
                )
            );
        }
        
        $request = array_replace_recursive($request, $options);
        
        $request = Type::buildFromArray($request);
        
		try{
			return $this->ews->getClient()->FindItem($request);
		} catch (Exception $e){
			$error = $e->getMessage();
			return false;
		}
    }
    
    function getEventInfo($itemId){
        
		$options = array(
            'ItemShape' => array(
                'BaseShape' => 'AllProperties',
                'BodyType' => 'Text'
            )
        );
        
		try{
			return $this->ews->getItem(['Id' => $itemId->getId(), 'ChangeKey' => $itemId->getChangeKey()], $options);
        } catch (Exception $e){
			$error = $e->getMessage();
			return false;
		}
    }
    
    function getEventById($itemId){
        
		$options = array(
            'ItemShape' => array(
                'BaseShape' => 'AllProperties',
                'BodyType' => 'Text'
            )
        );
		
		try{
			return $this->ews->getItem(new Type\ItemIdType($itemId), $options);
        } catch (Exception $e){
			$error = $e->getMessage();
			return false;
		}
    }
    
    function GetExceptionItems($masterId, $indexes){
        
        $seriesEvents = array();
        
        foreach($indexes as $index){
            
            $request = array(
                'ItemShape' => array('BaseShape' => 'AllProperties','BodyType' => 'Text'),
                'ItemIds' => array('OccurrenceItemId' => array("RecurringMasterId" => $masterId, "InstanceIndex" => $index))
            );
            
            $request = array_replace_recursive($request, array());
            
            try{
                
                $response = $this->ews->getClient()->GetItem($request);
                
				if(is_object($response)){
					$response = array($response);
				}

                $seriesEvents = array_merge($seriesEvents, array_filter($response));
				
            } catch (Exception $e){
                $error = $e->getMessage();
            }
        }
            
        return $seriesEvents;
    }
    
    public function createCalendarItems($folderId, $items, $options = array()){
        
        // If Send Notification is Disabled then No Notification
        
        if(!$items['SendNotification']){
            
            $defaultOptions = array(
                'SendMeetingInvitations' => Enumeration\CalendarItemCreateOrDeleteOperationType::SEND_TO_NONE,
                'SavedItemFolderId' => array(
                    'FolderId' => $folderId->toXmlObject()
                )
            );
            
        } else {
            
            $defaultOptions = array(
                'SendMeetingInvitations' => Enumeration\CalendarItemCreateOrDeleteOperationType::SEND_ONLY_TO_ALL,
                'SavedItemFolderId' => array(
                    'FolderId' => $folderId->toXmlObject()
                )
            );
            
        }
        
        $items = $this->ensureIsArray($items, true);
        
        $item = array('CalendarItem' => $items);
        
        /*$defaultOptions = array(
            'SendMeetingInvitations' => Enumeration\CalendarItemCreateOrDeleteOperationType::SEND_ONLY_TO_ALL,
            'SavedItemFolderId' => array(
                'FolderId' => $folderId->toXmlObject()
            )
        );*/
        
        $options = array_replace_recursive($defaultOptions, $options);
        
		try{
		
			$items = $this->ews->createItems($item, $options);
        
		} catch (Exception $e){
			$error = $e->getMessage();
		}
        return $items;
    }
    
    public function updateCalendarItems($updateEvents, $options = array()){
        
        foreach($updateEvents as $itemId => $changes){
            
            if(!$changes['SendNotification']){
                $defaultOptions = [
                    'SendMeetingInvitationsOrCancellations' => 'SendToNone'
                ];
            } else {
                $defaultOptions = [
                    'SendMeetingInvitationsOrCancellations' => 'SendOnlyToChanged'
                ];
            }
            
            unset($changes['SendNotification']);
            
            $itemId = new Type\ItemIdType($itemId);
            
            $request['ItemChange'][] = [
                'ItemId' => $itemId->toArray(),
                'Updates' => API\ItemUpdateBuilder::buildUpdateItemChanges('CalendarItem', 'calendar', $changes)
            ];
            
        }
        
      
        $options = array_replace_recursive($defaultOptions, $options);
        
		try{
			$items = $this->updateItems($request, $options);
			$items = $this->ensureIsArray($items);
        } catch (Exception $e){
			$error = $e->getMessage();
		}
        return $items;
    }
    
    
    public function getRecurringSeriesEvents($masterId){
        
        $seriesEvents = array();
        
        $index = 1;
        
        // Sync Maximum 50 Events
        
        while (!($index > 50))
        {
            
            $request = array(
                'ItemShape' => array('BaseShape' => 'AllProperties','BodyType' => 'Text'),
                'ItemIds' => array('OccurrenceItemId' => array("RecurringMasterId" => $masterId, "InstanceIndex" => $index))
            );
            
            try{
                
                $response = $this->ews->getClient()->GetItem($request);
                
                if( is_object($response) && $response){
                    
                    // If Event is Older than 5 Days then No Need to Sync IT
                    
                    $start_date = date("Y-m-d H:i:s", strtotime($response->getStart()));
                    
                    $dStart = new DateTime($start_date);
                    
                    $dEnd = new DateTime();
                    
                    if($dEnd > $dStart){
                        
                        $dDiff = $dStart->diff($dEnd);
                        
                        $days = $dDiff->days;
                        
                        if($days > 5) {
                            $index++;
                            continue;
                        }
                        
                    }
                    
                    $seriesEvents[] = $response;
                    
                }
                
            } catch (Exception $e){
                break;
            }
            
            $index++;
        }
        
        return $seriesEvents;
        
    }
    
    public function getDeletedEventsChanges($sync_state = ''){
        $request = array();
        $folder = $this->getFolder('calendar');
        $request = array(
            'ItemShape' => array('BaseShape' => 'IdOnly'),
            'SyncFolderId' => array('FolderId' => $folder->toXmlObject()),
            'SyncScope' => 'NormalItems',
            'MaxChangesReturned' => '100',
            'SyncState' => $sync_state
        );
        return $this->ews->getClient()->SyncFolderItems($request);
    }
}
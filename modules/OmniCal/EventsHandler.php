<?php

class EventsHandler extends VTEventHandler{
    function GetExchangeResponseMessage($response){

    }

    function handleEvent($eventName, $entityData) {
        
        return true;
        
        $recordId = $entityData->getId();
        $activity = new OmniCal_Activity_Model();
        $current_user = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $on_behalf = $current_user->get('user_name');
        $time_zone = "UTC";
        if($eventName == 'vtiger.entity.aftersave'){
            $adb = PearDatabase::getInstance();
            $data = $entityData->getData();
            $user_name = getUserName($data['assigned_user_id']);
            $invitees = $activity->getInvities($recordId);
            $contact_invitees = $activity->getContactInvities($recordId);
            $manual_invitees = $activity->getManualInvities($recordId);

            $attendee_list = json_decode($_REQUEST['attendee_list']);
            $attendees = array();
            if($invitees){
                foreach($invitees AS $k => $v){
                    $attendees[] = getUserEmail($v);
                }
            }
            foreach($contact_invitees AS $k => $v){
                $attendees[] = $v['email'];
            }
            foreach($manual_invitees AS $k => $v){
                $attendees[] = $v['email'];
            }

            if(is_array($attendees) && is_array($attendee_list))
                $attendees = array_unique(array_merge($attendees, $attendee_list));
            else
                $attendees = $attendee_list;

			if(isset($_REQUEST['set_reminder']) && $_REQUEST['set_reminder'] == 'Yes'){
				
				$data['set_reminder'] = 1;
				
				$reminderTime = $adb->query_result($adb->pquery("SELECT reminder_time FROM `vtiger_activity_reminder` WHERE `activity_id` = ?",array($recordId)), 0, 'reminder_time');
				
				$data['reminder_time'] = $reminderTime;
			}
			
            /*if($data['activitytype'] == 'Task'){
                $tasks = new OmniCal_ExchangeTasks_Model('lanserver33', 'ConcertAdmin', 'Consec1', 'Exchange2007_SP1' );
                $tasks->SetImpersonation($user_name);
                $exchange_info = OmniCal_CRMExchangeHandler_Model::GetActivityIdAndChangeKey($recordId);
                
				if(isset($data['update_exchange']) && $data['update_exchange'] == 0){
                    return;
                }
				
                if(!empty($exchange_info)){
                    if($data['set_reminder'])
                        $reminder_time = strtotime($data['date_start'] . " " . $data['time_start'] . " " . $time_zone);
                    else
                        $reminder_time = null;
                    $response = $tasks->UpdateTaskInExchange($exchange_info['id'], $exchange_info['changekey'], $data['subject'], $data['description'], "TEXT", null, $reminder_time, null, $data['taskstatus']);
                    if($response->ResponseMessages->UpdateItemResponseMessage->ResponseClass == 'Success'){
                        $id = $response->ResponseMessages->UpdateItemResponseMessage->Items->Task->ItemId->Id;
                        $changeKey = $response->ResponseMessages->UpdateItemResponseMessage->Items->Task->ItemId->ChangeKey;
                        $tasks->UpdateCRMExchangeIDAndChangeKey($recordId, $id, $changeKey);
                        return;
                    }
                    else{
                        return;
                    }
                }
                if($data['set_reminder'])
                    $reminder_time = strtotime($data['date_start'] . " " . $data['time_start'] . " " . $time_zone);
                else
                    $reminder_time = null;
                $response = $tasks->CreateTaskInExchange($data['subject'], null, null, $data['description'], $reminder_time);
                if($response->ResponseMessages->CreateItemResponseMessage->ResponseClass == 'Success'){
                    $id = $response->ResponseMessages->CreateItemResponseMessage->Items->Task->ItemId->Id;
                    $changeKey = $response->ResponseMessages->CreateItemResponseMessage->Items->Task->ItemId->ChangeKey;
                    $tasks->UpdateCRMExchangeIDAndChangeKey($recordId, $id, $changeKey);
                }
                else
                    echo json_encode(array("result"=>"Exchange Creation Failure"));
            } else */
           
			if($data['activitytype'] == 'Call' || $data['activitytype'] == 'Meeting'){
                
				$events = new OmniCal_ExchangeEvent_Model('mail.omnisrv.com', 'concertadmin@concertglobal.com', 'Consec1', 'Exchange2010_SP2' );
                
				$events->SetImpersonation($user_name);
                
				$is_impersonated = false;
				
				try {
					
					$calendar_folder = $events->getExchangeFolderDetail("calendar");
					
					if(!empty($calendar_folder))
						$is_impersonated = true;
					
				} catch (Exception $e) {
					$is_impersonated = false;
				} 
				
				if(!$is_impersonated)
					return true;
				
				$exchange_info = OmniCal_CRMExchangeHandler_Model::GetActivityIdAndChangeKey($recordId);
                
				if(isset($data['update_exchange']) && $data['update_exchange'] == 0){
                    return;
                }
				
				// if itemid and change for the record is set then update event in Exchange
				
				if(!empty($exchange_info) || $data['master_activity']){
				    
                    if($data['set_reminder'])
                        $reminder_time = strtotime($data['date_start'] . " " . $data['time_start'] . " " . $time_zone);
                    else
                        $reminder_time = null;

                    $start = strtotime($data['date_start'] . "T" . $data['time_start'] . "+00:00");
                    $end = strtotime($data['due_date'] . "T" . $data['time_end'] . "+00:00");
                    $notify = $_REQUEST['notify'];
					
					if(!$notify)
						$notify = "SEND_TO_NONE";
					
                    if(!$data['master_activity']){
                        $response = $events->UpdateEventInExchange($exchange_info['id'], $exchange_info['changekey'], $notify, $data['subject'], $data['description'], "HTML", $start,
                                                                   $end, $data['location'], $attendees);
                    }else{
                        if(!$exchange_info['id']){
                            $master_info = $events->GetExchangeIDAndChangeKeyFromActivityId($data['master_activity']);
                            $index = $_REQUEST['index'];
                            if(!$index)
                                $index = $activity->GetIndexFromMasterChildActivity ($data['master_activity'], $recordId);
                            if(!$index)
                                return;
                            $exception_info = $events->GetExceptionItem($master_info['id'], $index);
                            if($exception_info->ResponseMessages->GetItemResponseMessage->ResponseClass == "Success"){
                                $item_id = $exception_info->ResponseMessages->GetItemResponseMessage->Items->CalendarItem->ItemId->Id;
                                $item_ck = $exception_info->ResponseMessages->GetItemResponseMessage->Items->CalendarItem->ItemId->ChangeKey;
                                $response = $events->UpdateEventInExchange($item_id, $item_ck, $notify, $data['subject'], $data['description'], "HTML", $start,
                                                                           $end, $data['location'], $attendees);
                            }
                        } else {
							$response = $events->UpdateEventInExchange($exchange_info['id'], $exchange_info['changekey'], $notify, $data['subject'], $data['description'], "HTML", $start,
                                                                       $end, $data['location'], $attendees, null, null, true);
                        }
                    }
                    if($response->ResponseMessages->UpdateItemResponseMessage->ResponseClass == 'Success'){
                        $id = $response->ResponseMessages->UpdateItemResponseMessage->Items->CalendarItem->ItemId->Id;
                        $changeKey = $response->ResponseMessages->UpdateItemResponseMessage->Items->CalendarItem->ItemId->ChangeKey;
                        $events->UpdateCRMExchangeIDAndChangeKey($recordId, $id, $changeKey);
                        return;
                    }
                    else{
                        return;
                    }
                }
               
                if($data['set_reminder'])
                    $reminder_time = strtotime($data['date_start'] . " " . $data['time_start'] . " " . $time_zone);
                else
                    $reminder_time = null;

				$start = strtotime($data['date_start'] . "T" . $data['time_start'] . "+00:00");
                $end = strtotime($data['due_date'] . "T" . $data['time_end'] . "+00:00");
                $info = OmniCal_RepeatActivities_Model::GetRecurringInfo($recordId);
                
				if($info['recurringtype'] == 'Weekly'){
					$recurring_info = 'Weekly';
					$days_array = explode("::", $info['recurringinfo']);
					foreach($days_array as $key => $weekday){
						if($key == 0) continue;
						$weekday = getTranslatedString('LBL_DAY'.$weekday, 'Events');
						$day = strtoupper($weekday);
						$recurring_info .= ' '.$day;
					}
					$info['recurringinfo'] = $recurring_info;
				} else if($info['recurringtype'] == 'Monthly'){
					$days_array = explode("::", $info['recurringinfo']);
					if($days_array[1] == 'date'){
						$info['recurringtype'] = 'AbsoluteMonthly';
						$info['recurringinfo'] = $days_array[2];
					} else if($days_array[1] == 'day') {
						$info['recurringtype'] = 'RelativeMonthly';
						$info['recurringinfo'] = ucfirst($days_array[2]);
						$weekday = getTranslatedString('LBL_DAY'.$days_array[3], 'Events');
						$day = strtoupper($weekday);
						$info['recurringinfo'] .= ' '.$day;
					}
				}
				
				$datetime =  DateTimeField::convertToUserTimeZone($data['date_start'] . " " . $data['time_start']);
				$actual_start_date = $datetime->format('Y-m-d');
				
				if(strtotime($actual_start_date) < strtotime($info['recurringdate'])){
					$info['recurringdate'] = $actual_start_date;
				}
				
				if($data['parent_activity_id'] != '' && $data['parent_activity_id'] > 0 ) return true;
				
				$response = $events->CreateEventInExchange($data['subject'], $data['description'], $start, $end, $data['set_reminder'], $data['reminder_time'], $attendees, null,
                                                           $data['location'], null, "HTML", "default", $current_user->get('time_zone'), $info);
				
                if($response->ResponseMessages->CreateItemResponseMessage->ResponseClass == 'Success'){
                    $id = $response->ResponseMessages->CreateItemResponseMessage->Items->CalendarItem->ItemId->Id;
                    $changeKey = $response->ResponseMessages->CreateItemResponseMessage->Items->CalendarItem->ItemId->ChangeKey;
                    $events->UpdateCRMExchangeIDAndChangeKey($recordId, $id, $changeKey);
                }
				
            }
        }
		
		/* === START : Delete and Before Save Check Record Owner Change if Chnaged then delete Event from Exchnage === */
		
		if($eventName == 'vtiger.entity.afterdelete'){
		    	
			$moduleName = $entityData->getModuleName();

			if($moduleName == 'Calendar' || $moduleName == 'Events'){
				
				$data = $entityData->getData();
				
				$user_name = getUserName($data['assigned_user_id']);
            
				$events = new OmniCal_ExchangeEvent_Model('mail.omnisrv.com', 'concertadmin@concertglobal.com', 'Consec1', 'Exchange2010_SP2' );
                
				$events->SetImpersonation($user_name);
                
				if(!isset($data['task_exchange_item_id']) || $data['task_exchange_item_id'] == ''){
					
					$exchange_info = OmniCal_CRMExchangeHandler_Model::GetActivityIdAndChangeKey($recordId);
					
					$item_id = $exchange_info['id'];
                } else
					$item_id = $data['task_exchange_item_id'];
				
				if(!$item_id) return true;
				
				$request = new EWSType_DeleteItemType();

				$request->ItemIds = new EWSType_NonEmptyArrayOfBaseItemIdsType();
				$request->ItemIds->ItemId = new EWSType_ItemIdType();
				$request->ItemIds->ItemId->Id = $item_id;
				$request->DeleteType = EWSType_DisposalType::MOVE_TO_DELETED_ITEMS;
				$request->SendMeetingCancellations = constant("EWSType_CalendarItemCreateOrDeleteOperationType::SEND_TO_NONE");
				   
				$response = $events->ews->DeleteItem($request);

			}
		}
		
		if($eventName == 'vtiger.entity.beforesave'){
		    	
			$adb = PearDatabase::getInstance();
			
			$moduleName = $entityData->getModuleName();

			if($moduleName == 'Calendar' || $moduleName == 'Events'){
				
				$data = $entityData->getData();
				
				$assigned_user_id = $data['assigned_user_id'];
				
				$old_assigned_user_id = getRecordOwnerId($recordId);
				
				if(!empty($old_assigned_user_id) && isset($old_assigned_user_id['Users']))
					$old_assigned_user_id = $old_assigned_user_id['Users'];
				else
					$old_assigned_user_id = $assigned_user_id;
					
				if($old_assigned_user_id == $assigned_user_id) return true;
				
				if(!isset($data['task_exchange_item_id']) || $data['task_exchange_item_id'] == ''){
					
					$exchange_info = OmniCal_CRMExchangeHandler_Model::GetActivityIdAndChangeKey($recordId);
					
					$item_id = $exchange_info['id'];
                } else
					$item_id = $data['task_exchange_item_id'];
				
				if(empty($item_id)) return true;
				
				$user_name = getUserName($old_assigned_user_id);
            
				$events = new OmniCal_ExchangeEvent_Model('mail.omnisrv.com', 'concertadmin@concertglobal.com', 'Consec1', 'Exchange2010_SP2' );
                
				$events->SetImpersonation($user_name);
                
				$request = new EWSType_DeleteItemType();

				$request->ItemIds = new EWSType_NonEmptyArrayOfBaseItemIdsType();
				$request->ItemIds->ItemId = new EWSType_ItemIdType();
				$request->ItemIds->ItemId->Id = $item_id;
				$request->DeleteType = EWSType_DisposalType::MOVE_TO_DELETED_ITEMS;
				$request->SendMeetingCancellations = constant("EWSType_CalendarItemCreateOrDeleteOperationType::SEND_TO_NONE");
				   
				$response = $events->ews->DeleteItem($request);

				if($response->ResponseMessages->DeleteItemResponseMessage->ResponseClass == 'Success'){
					
					$entityData->set('task_exchange_item_id', '');
					
					$entityData->set('task_exchange_change_key', '');
					
					$adb->pquery("update vtiger_activitycf set task_exchange_item_id = '', task_exchange_change_key = '' where activityid = ?",array($recordId), true);
				}
			}
		}
		
		/* === END : Delete and Before Save Check Record Owner Change if Chnaged then delete Event from Exchnage === */	
    }
}

?>

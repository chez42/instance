<?php

    function vtws_schedule_appointment($element,$user){
        
        global $adb,$site_URL;
        
        $result = array();
       
        if($element['mode'] == 'getslots'){
                
            $user_id = getUserId_Ol($element['user_id']);
            
            $query = "SELECT time_zone, business_hours FROM vtiger_users WHERE id = ?";
            $result = $adb->pquery($query, array($user_id));
            $time_zone = $adb->query_result($result, 0, "time_zone");
            $business_hours = json_decode(html_entity_decode($adb->query_result($result, 0, 'business_hours')), true);
           
            $date = date('Y-m-d', strtotime($element['curdate']));
            
            $day = strtolower(date('l',strtotime($date)));
            
            $time_start = $business_hours[$day.'_start'];
            $time_end = $business_hours[$day.'_end'];
           
            $available_slots = array();
            
            if($time_start) {
                
                $adb->pquery("create temporary table `possible_slots` (
                `slot` datetime NULL)");
                
                $StartTime = $date." ".$time_start;
                
                $EndTime = $date." ".$time_end;
                
                $slot = $element['slot_time'];
                
                $ReturnArray = array ();
                
                $StartTime    = strtotime ($StartTime);
                $EndTime      = strtotime ($EndTime);
                
                $AddMins  = $slot * 60;
                
                while ($StartTime <= $EndTime){
                    
                    $adb->pquery("insert into `possible_slots` (`slot`) values('".date ("Y-m-d H:i:s", $StartTime)."')");
                    
                    $StartTime += $AddMins;
                    
                }
                
                $now = new DateTime(null, new DateTimeZone($time_zone));
                if($now->getOffset() < 0){
                    $offset = -$now->getOffset();
                    $format = '-' . gmdate('H:i', $offset);
                } else {
                    $format = '+' . gmdate('H:i', $now->getOffset());
                }
                
                $currentTime = $now->format('Y-m-d H:i:s');
                
                $slot_interval = ($slot * 60) - 1;
                
                $result = $adb->pquery("SELECT DISTINCT `slot` FROM possible_slots AS d
            	INNER JOIN vtiger_activity ON
            	(
                        
            		(
            			d.`slot` BETWEEN
                        
                        
            			DATE_FORMAT(
            			            CONVERT_TZ(
            			                CONCAT(vtiger_activity.date_start, ' ', vtiger_activity.time_start)
            			                , '+0:00', '$format') ,'%Y-%m-%d %H:%i:%s')
                        
                        
                        
            			and DATE_SUB(DATE_FORMAT(
            			            CONVERT_TZ(
            			                CONCAT(vtiger_activity.due_date, ' ', vtiger_activity.time_end)
            			                , '+0:00', '$format') ,'%Y-%m-%d %H:%i:%s'), INTERVAL 1 SECOND)
            		) or
            		(
            			DATE_SUB(DATE_FORMAT(
            			            CONVERT_TZ(
            			                CONCAT(vtiger_activity.due_date, ' ', vtiger_activity.time_end)
            			                , '+0:00', '$format') ,'%Y-%m-%d %H:%i:%s') , INTERVAL 1 SECOND)
                        
            			BETWEEN d.`slot` and DATE_ADD(d.`slot`, INTERVAL $slot_interval SECOND)
                        
            		) or
                        
            		(
            			DATE_FORMAT(
            			            CONVERT_TZ(
            			                CONCAT(vtiger_activity.date_start, ' ', vtiger_activity.time_start)
            			                , '+0:00', '$format') ,'%Y-%m-%d %H:%i:%s')
            			BETWEEN
            				d.`slot` and
            				DATE_ADD(d.`slot`, INTERVAL $slot_interval SECOND)
            		) or
            		(
            			DATE_ADD(d.`slot`, INTERVAL $slot_interval SECOND) BETWEEN
                        
            			DATE_FORMAT(
            			            CONVERT_TZ(
            			                CONCAT(vtiger_activity.date_start, ' ', vtiger_activity.time_start)
            			                , '+0:00', '$format') ,'%Y-%m-%d %H:%i:%s')
                        
            			and
                        
            			DATE_FORMAT(
            			            CONVERT_TZ(
            			                CONCAT(vtiger_activity.due_date, ' ', vtiger_activity.time_end)
            			                , '+0:00', '$format') ,'%Y-%m-%d %H:%i:%s')
            		)
                        
                        
            	) INNER JOIN vtiger_crmentity on crmid = activityid where deleted = 0 and smownerid = ? ",array($user_id));
                    
                
                $not_available_slots = array();
                
                for($i = 0; $i < $adb->num_rows($result); $i++){
                    $not_available_slots[] = $adb->query_result($result, $i, "slot");
                }
                
                
                $result = $adb->pquery("SELECT DISTINCT `slot` FROM possible_slots
        	       where `slot` not in('". implode("','", $not_available_slots) . "')");
                
                for($i = 0; $i < $adb->num_rows($result); $i++){
                    $availSlot = $adb->query_result($result, $i, "slot");
                    if(strtotime($availSlot) > strtotime($currentTime))
                        $available_slots[] = array(date('H:i', strtotime($availSlot))); 
                }
                
                sort($available_slots);
            }
            
            $result = array('slots'=> $available_slots,'timezone'=>$time_zone);
        
        }else if($element['mode'] == 'save'){
            
            $user_id = getUserId_Ol($element['domainUserId']);
            
            $email = $element['email'];
            $userName = $element['userName'];
            $phone = $element['phoneNumber'];
            
            $confirmation = $element['confirmation'];
            $notes = $element['notes'];
            $eventName = $element['name'];
			$eventSub = $element['topic'];
            $slot = $element['slot_time'];
            $date = $element['date'];
            $type = $element['meetingType'];
            $time = json_decode($element['selectedSlotsString'],true);
            
            $start_time = $time[0]['start'];
            $end_time = date('H:i', strtotime($start_time) + ($slot * 60));
            
            $contactQuery = $adb->pquery("SELECT * FROM vtiger_contactdetails
            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid
            WHERE vtiger_crmentity.deleted = 0 AND vtiger_contactdetails.email = ?",array($email));
            $contact_id = '';
            
            if(!$adb->num_rows($contactQuery)){
                $contact = CRMEntity::getInstance('Contacts');
                $contact->column_fields['lastname'] = $userName;
                $contact->column_fields['mobile'] = $phone;
                $contact->column_fields['email'] = $email;
                $contact->column_fields['assigned_user_id'] = $user_id;
                $contact->save('Contacts');
                $contact_id = $contact->id;
            }else if($adb->num_rows($contactQuery)){
                $contact_id = $adb->query_result($contactQuery, 0, 'contactid');
            }
            
            $query = "SELECT time_zone FROM vtiger_users WHERE id = ?";
            $result = $adb->pquery($query, array($user_id));
            $time_zone = $adb->query_result($result, 0, "time_zone");
            
            $now = new DateTime(null, new DateTimeZone($time_zone));
            if($now->getOffset() < 0){
                $offset = -$now->getOffset();
                $format = '-' . gmdate('H:i', $offset);
            } else {
                $format = '+' . gmdate('H:i', $now->getOffset());
            }
            
            $start_time = date('H:i', strtotime($format, strtotime($start_time)));
            $end_time = date('H:i', strtotime($format, strtotime($end_time)));
            
            $event = CRMEntity::getInstance('Events');
			$event->column_fields['subject'] = $eventSub;
            $event->column_fields['date_start'] = $date;
            $event->column_fields['time_start'] = $start_time;
            $event->column_fields['assigned_user_id'] = $user_id;
            $event->column_fields['time_end'] = $end_time;
            $event->column_fields['due_date'] = $date;
            $event->column_fields['contact_id'] = $contact_id;
            $event->column_fields['activitytype'] = $type;
            $event->column_fields['description'] = $notes;
            $event->column_fields['sendnotification'] = ($confirmation == 'on') ? 1 : 0;
            $event->column_fields['visibility'] = 'Private';
			$event->column_fields['eventstatus'] = 'Planned';
            $event->save('Events');
            
            if($event->id)
                $result = array('success'=>true, 'event_id'=>$event->id);
            else 
                $result = array('success'=>false);
			
		}else if($element['mode'] == 'logo'){
        
			$user_id = getUserId_Ol($element['user_name']);
			
			$logo = '';
			
			$result = $adb->pquery("SELECT vtiger_attachments.* FROM vtiger_salesmanattachmentsrel
			INNER JOIN vtiger_attachments ON vtiger_salesmanattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
			INNER JOIN vtiger_users ON vtiger_users.id = vtiger_salesmanattachmentsrel.smid
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_salesmanattachmentsrel.attachmentsid
			WHERE vtiger_salesmanattachmentsrel.smid = ? and vtiger_crmentity.setype = ?", array($user_id, "User Logo"));
			
			if($adb->num_rows($result)){
				
				$portalLogo =  $site_URL;
				$portalLogo .= $adb->query_result($result, "0", "path");
				$portalLogo .= $adb->query_result($result, "0", "attachmentsid");
				$portalLogo .= "_". $adb->query_result($result, "0", "name");
				
				$logo = ($portalLogo);
				
			} 
        
			$result = array('success'=>true, 'logo' => $logo);
        
		}
		
        return $result;
        
    }
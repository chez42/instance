<?php

class TaskHandler extends VTEventHandler{

    function handleEvent($eventName, $entityData) {
		
		$recordId = $entityData->getId();
        
		$current_user = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        
		$on_behalf = $current_user->get('user_name');
        
		$time_zone = "UTC";
        
    	$moduleName = $entityData->getModuleName();

        if ($moduleName != 'Task') {
            return;
        }
        
        if($eventName == 'vtiger.entity.aftersave'){
        	
        	$adb = PearDatabase::getInstance();
            
        	$data = $entityData->getData();
            
        	$user_name = getUserName($data['assigned_user_id']);
             
        	// When task is created from Exchange to CRM the value of update_exchange is set to 0.
            if(isset($data['update_exchange']) && $data['update_exchange'] == 0)
            	return;
        	
            $tasks = new Task_ExchangeTasks_Model('mail.omnisrv.com', 'concertadmin@concertglobal.com', 'Consec1', 'Exchange2010_SP2' );
                
            $tasks->SetImpersonation($user_name);

			$is_impersonated = false;
			
			try {
				
				$task_folder = $tasks->getExchangeFolderDetail("task");
				
				if(!empty($task_folder))
					$is_impersonated = true;
				
			} catch (Exception $e) {
				$is_impersonated = false;
			} 
			
			if(!$is_impersonated)
				return true;
				
			$tasks->timeZone = $current_user->get('time_zone');
			
            $task_exchange_itemid = $data['task_exchange_item_id'];
            
            $task_exchange_change_key = $data['task_exchange_change_key'];
            
			$dueDate = new DateTimeField($data['due_date'] . " " . $_REQUEST['time_start']);
			
			$due_date = $dueDate->getDBInsertDateTimeValue();
			
			if(!$due_date)
				$due_date = null;
			 
			if($data['set_reminder'])
				$reminder_time = strtotime($data['date_start'] . " " . $data['time_start'] . " " . $time_zone); 
			else
				$reminder_time = null;
			
			if($due_date){
				
				list($dueDate, $time_end) = explode(' ', $due_date);
				
				$due_date = strtotime($dueDate . "T" . $time_end . "+00:00");
				  
			} else
				$due_date = null;
			
			// if task item_id and change key exists then update task info in Exchange.
           	if($task_exchange_itemid && $task_exchange_change_key){
                
				$task_updated_data = array(
					"subject" => $data['subject'],
					"due" => $due_date,
					"body" => $data['description'],
					"reminder_due" => $reminder_time,
					"reminder_start" => 0,
					"status" => $data['task_status'],
					"bodytype" => "TEXT"
				);
			
           		$response = $tasks->UpdateTaskInExchange($task_exchange_itemid, $task_exchange_change_key,$task_updated_data);
           		
           		if($response->ResponseMessages->UpdateItemResponseMessage->ResponseClass == 'Success'){
                	$id = $response->ResponseMessages->UpdateItemResponseMessage->Items->Task->ItemId->Id;
                	$changeKey = $response->ResponseMessages->UpdateItemResponseMessage->Items->Task->ItemId->ChangeKey;
                  	$tasks->UpdateTaskExchangeIDAndChangeKey($recordId, $id, $changeKey);
            		return;
             	} else {
               		return;
                }
           	}
            
           	// To create Task From CRM to Exchange.
			$task_data = array(
				"subject" => $data['subject'],
				"due" => $due_date,
				"body" => $data['description'],
				"reminder_due" => $reminder_time,
				"reminder_start" => 0,
				"status" => $data['task_status'],
			);
			
           	$response = $tasks->CreateTaskInExchange($task_data);
                
        	if($response->ResponseMessages->CreateItemResponseMessage->ResponseClass == 'Success'){
            	$id = $response->ResponseMessages->CreateItemResponseMessage->Items->Task->ItemId->Id;
               	$changeKey = $response->ResponseMessages->CreateItemResponseMessage->Items->Task->ItemId->ChangeKey;
             	$tasks->UpdateTaskExchangeIDAndChangeKey($recordId, $id, $changeKey);
       		}        
        }
    }
}

?>

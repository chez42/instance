<?php

class Users_ExchangeSubscription_View extends Vtiger_IndexAjax_View {
	
	function __construct() {
        parent::__construct();
        $this->exposeMethod('Subscribe');
		$this->exposeMethod('UnSubscribe');
    }
	
	function process(Vtiger_Request $request) {
		
		$mode = $request->get('mode');
		
		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}
	
	function Subscribe(Vtiger_Request $request){
    	
    	$adb = PearDatabase::getInstance();
    	
		$type = $request->get("type");
		
		if(!$type || !in_array($type, array('Calendar', 'Contacts'))){
			$response = new Vtiger_Response();
			$response->setEmitType(Vtiger_Response::$EMIT_JSON);
			$response->setResult(array("success" => false, "message" => 'You can subscribe only Calendar and Contacts'));
			$response->emit();
		}
			
    	$currentUserModel = Users_Record_Model::getCurrentUserModel();
		
		$is_user_subscribed = $currentUserModel->getCurrentUserSubscription($type);
		
		$result = array();
		
		if(!$is_user_subscribed){
		
			if($type == 'Calendar')
				$result = $this->subscribeExchangeCalendar();
			else if($type == 'Contacts')
				$result = $this->subscribeExchangeContacts();
			
			if(empty($result))
				$result = array("success" => false, "message" => "Oops! Something went wrong while subscribe ".$type);
		
		} else 
			$result = array("success" => false, "message" => "User is already subscribe for ".$type);
		
    	$response = new Vtiger_Response();
		$response->setEmitType(Vtiger_Response::$EMIT_JSON);
		$response->setResult($result);
		$response->emit();
    }
    
    function UnSubscribe(Vtiger_Request $request){
    	
		$viewer = $this->getViewer($request);
					
		$moduleName = $request->getModule();

    	$adb = PearDatabase::getInstance();
    	
    	$currentUserModel = Users_Record_Model::getCurrentUserModel();
		
		$userId = $currentUserModel->getId();
		
		$type = $request->get("type");
		
		if($type == 'Calendar')
			$adb->pquery("delete from vtiger_user_subscription where userid = ?",array($userId));
		else if($type == 'Contacts')
			$adb->pquery("delete from vtiger_user_contacts_subscription where userid = ?",array($userId));
		
		$result = array("success" => true, "message" => $type." Unsubscribe Successfully");
		
		$response = new Vtiger_Response();
		$response->setEmitType(Vtiger_Response::$EMIT_JSON);
		$response->setResult($result);
		$response->emit();
	}
   
	function subscribeExchangeCalendar(){
		
		global $site_URL;
		
		$adb = PearDatabase::getInstance();
		
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		
		$userId = $currentUserModel->getId();
		
		$username = $currentUserModel->get("user_name");
			
		$events = new OmniCal_ExchangeEvent_Model();
			
		$events->SetImpersonation($username);
			
		$task_folder = $events->getExchangeFolderDetail("task");
			
		if(!empty($task_folder) && isset($task_folder['Id']))
			$taskFolderId = $task_folder['Id'];
						
		$calendar_folder = $events->getExchangeFolderDetail("calendar");
						
		if(!empty($calendar_folder) && isset($calendar_folder['Id']))
			$calendarFolderId = $calendar_folder['Id'];
			
		$url = $site_URL."/ews_notification_listener.php";
		
		$keepAliveFrequency = 20;

		$request = new EWSType_SubscribeType();
		$request->PushSubscriptionRequest = new EWSType_PushSubscriptionRequestType();
		$request->PushSubscriptionRequest->FolderIds = new EWSType_ArrayOfFolderIdType();
		
		$calendarFolder = new EWSType_FolderIdType();
		$calendarFolder->Id = $calendarFolderId;
		
		$taskFolder = new EWSType_FolderIdType();
		$taskFolder->Id = $taskFolderId;
		
		$request->PushSubscriptionRequest->FolderIds->FolderId = array($calendarFolder, $taskFolder);//$calendarFolder,

		$request->PushSubscriptionRequest->EventTypes = new EWSType_NonEmptyArrayOfNotificationEventTypesType();
		$request->PushSubscriptionRequest->EventTypes->EventType = array("CreatedEvent", "ModifiedEvent", "MovedEvent");

		$request->PushSubscriptionRequest->StatusFrequency = $keepAliveFrequency;
		$request->PushSubscriptionRequest->URL = $url;

		$response = $events->ews->Subscribe($request);

		$response = json_decode(json_encode($response),TRUE);
		
		$result = array();
		
		if(
			!empty($response) && isset($response['ResponseMessages']) && !empty($response['ResponseMessages']) &&
			isset($response['ResponseMessages']['SubscribeResponseMessage']) && !empty($response['ResponseMessages']['SubscribeResponseMessage'])
		){
			
			$subscribeResponse = $response['ResponseMessages']['SubscribeResponseMessage'];
			
			if(isset($subscribeResponse['ResponseClass']) && $subscribeResponse['ResponseClass'] == 'Success'){
				
				$subscriptionId = $subscribeResponse['SubscriptionId'];
				
				$watermark = $subscribeResponse['Watermark'];
				
				$date_var = date("Y-m-d H:i:s");
				
				$adb->pquery("insert into vtiger_user_subscription(userid, subscriptionid, watermark, subscribe_time) values(?,?,?,?)",
				array($userId, $subscriptionId, $watermark, $adb->formatDate($date_var, true)));
			
				$result = array("success" => true, "message" => "Calendar subscribe Successfully");
				
			} else if(isset($subscribeResponse['ResponseClass']) && $subscribeResponse['ResponseClass'] == 'Error'){
				
				$MessageText = $subscribeResponse['MessageText'];
				
				$result = array("success" => false, "message" => $MessageText);
			}
		}
		return $result;
	}
	
	function subscribeExchangeContacts(){
		
		global $site_URL;
		
    	$adb = PearDatabase::getInstance();
    	
    	$currentUserModel = Users_Record_Model::getCurrentUserModel();
		
		$userId = $currentUserModel->getId();
			
		$username = $currentUserModel->get("user_name");
		
		$contacts = new OmniCal_ExchangeContacts_Model();
		
		$contacts->SetImpersonation($username);

		$url = $site_URL."/ews_contact_notification_listener.php";
		
		$keepAliveFrequency = 20;

		$request = new EWSType_SubscribeType();
		$request->PushSubscriptionRequest = new EWSType_PushSubscriptionRequestType();
		$request->PushSubscriptionRequest->FolderIds = new EWSType_NonEmptyArrayOfBaseFolderIdsType();
		$request->PushSubscriptionRequest->FolderIds->DistinguishedFolderId = new EWSType_DistinguishedFolderIdType();
		$request->PushSubscriptionRequest->FolderIds->DistinguishedFolderId->Id = EWSType_DistinguishedFolderIdNameType::CONTACTS;

		$request->PushSubscriptionRequest->EventTypes = new EWSType_NonEmptyArrayOfNotificationEventTypesType();
		$request->PushSubscriptionRequest->EventTypes->EventType = array("CreatedEvent", "ModifiedEvent");

		$request->PushSubscriptionRequest->StatusFrequency = $keepAliveFrequency;
		$request->PushSubscriptionRequest->URL = $url;

		$response = $contacts->ews->Subscribe($request);

		$response = json_decode(json_encode($response),TRUE);
		
		$result = array();
		
		if(
			!empty($response) && isset($response['ResponseMessages']) && !empty($response['ResponseMessages']) &&
			isset($response['ResponseMessages']['SubscribeResponseMessage']) && !empty($response['ResponseMessages']['SubscribeResponseMessage'])
		){
			
			$subscribeResponse = $response['ResponseMessages']['SubscribeResponseMessage'];
			
			if(isset($subscribeResponse['ResponseClass']) && $subscribeResponse['ResponseClass'] == 'Success'){
				
				$subscriptionId = $subscribeResponse['SubscriptionId'];
				
				$watermark = $subscribeResponse['Watermark'];
				
				$date_var = date("Y-m-d H:i:s");
				
				$adb->pquery("insert into vtiger_user_contacts_subscription(userid, subscriptionid, watermark, subscribe_time) values(?,?,?,?)",
				array($userId, $subscriptionId, $watermark, $adb->formatDate($date_var, true)));

				$result = array("success" => true, "message" => "Contacts subscribe Successfully");
				
			} else if(isset($subscribeResponse['ResponseClass']) && $subscribeResponse['ResponseClass'] == 'Error'){
				
				$MessageText = $subscribeResponse['MessageText'];
				
				$result = array("success" => false, "message" => $MessageText);
			}
		}
		return $result;	
	}
}
<?php
require_once('libraries/exchange_ews/ExchangeWebServices.php');
require_once('libraries/exchange_ews/EWSType.php');

class OmniCal_ExchangeEws_Model extends Vtiger_Module{
    public $server, $user, $password, $exchange_version;
    public $ews, $impersonation, $sid, $user_name, $user_id, $smtp;
    public $folders;
    
    public function __construct($server='lanserver33', $user='concertglobal\concertadmin', $password='Consec1', $exchange_version='Exchange2010_SP2') {
        $this->server           = $server;
        $this->user             = $user;
        $this->password         = $password;
        $this->exchange_version = $exchange_version;
        
        $this->ews = new ExchangeWebServices($this->server, $this->user, $this->password, $this->exchange_version);
    }
    
    /**
     * Impersonates the give user.  Returns the email address of the user (which is what exchange uses for its own user identification)
     * @global type $adb
     * @param type $user_name
     * @return int
     */
    public function SetImpersonation($user_name){
        global $adb;
        $query = "SELECT first_name, last_name, email1, id, upn FROM vtiger_users WHERE user_name=? AND exchange_enabled=1";        
        $result = $adb->pquery($query, array($user_name));
        if($adb->num_rows($result) > 0){
            $email = $adb->query_result($result, 0, 'email1');
            $user_id = $adb->query_result($result, 0, 'id');
            $first_name = $adb->query_result($result, 0, 'first_name');
            $last_name = $adb->query_result($result, 0, 'last_name');
            
			$upn = $adb->query_result($result, 0, 'upn');
			if(!$upn)
				$upn = "{$user_name}@concertglobal.com";
			
            $this->user_name = $user_name;
            $this->user_id = $user_id;
            $this->impersonation = new EWSType_ExchangeImpersonationType();
            $this->sid = new EWSType_ConnectingSIDType();
            $this->sid->PrincipalName = $upn;//"{$user_name}@concertglobal.com";//$user_name;
			
//            $this->sid->PrimarySmtpAddress = $email;
            
            $this->smtp = "concertglobal.com";
            
            $this->impersonation->ConnectingSID = $this->sid;
            $this->ews->setImpersonation($this->impersonation);
        }
        else
            return 0;
    }
    
    public function DoesExchangeItemExist($exchangeID){
        global $adb;
        $query = "SELECT activityid FROM vtiger_activitycf WHERE task_exchange_item_id = ?";
        $result = $adb->pquery($query, array($exchangeID));
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'activityid');
        else{
            $query = "SELECT activityid FROM vtiger_contactscf WHERE task_exchange_item_id = ?";
            $result = $adb->pquery($query, array($exchangeID));
            if($adb->num_rows($result) > 0)
                return $adb->query_result($result, 0, 'contactid');
        }
    }
    
    public function DeleteItemsFromExchange(array $ids){
        $request = new EWSType_DeleteItemType();
        for($i = 0; $i < count($ids); $i++){
            $request->ItemIds->ItemId[$i]->Id = $ids[$i];
        }
        $request->DeleteType = EWSType_DisposalType::MOVE_TO_DELETED_ITEMS;
//        $request->SendMeetingCancellations = EWSType_CalendarItemCreateOrDeleteOperationType::SEND_ONLY_TO_ALL;
        $request->SendMeetingCancellations = EWSType_CalendarItemCreateOrDeleteOperationType::SEND_TO_NONE;
        $request->AffectedTaskOccurrences = EWSType_AffectedTaskOccurrencesType::ALL_OCCURRENCES;
        
        $response = $this->ews->DeleteItem($request);
        return $response;
    }
    
    public function GetExceptionItem($master_id, $index){
        if($master_id){
            $request = new EWSType_GetItemType();

            $request->ItemShape = new EWSType_ItemResponseShapeType();
            $request->ItemShape->BaseShape = EWSType_DefaultShapeNamesType::ID_ONLY;

            $request->ItemIds = new EWSType_NonEmptyArrayOfBaseItemIdsType();
            $request->ItemIds->OccurrenceItemId = new EWSType_OccurrenceItemIdType();
            $request->ItemIds->OccurrenceItemId->RecurringMasterId = $master_id;
            $request->ItemIds->OccurrenceItemId->InstanceIndex = $index;

            $response = $this->ews->GetItem($request);   
            return $response;
        }
    }
    /**
     * Gets the exchange ID and change key from the given activity id
     * @param type $activity_id
     */
    public function GetExchangeIDAndChangeKeyFromActivityId($activity_id){
        global $adb;
        $query = "SELECT task_exchange_item_id, task_exchange_change_key FROM vtiger_activitycf WHERE activityid = ?";
        $result = $adb->pquery($query, array($activity_id));
        if($adb->num_rows($result) > 0){
            return array("id" => $adb->query_result($result, 0, 'task_exchange_item_id'),
                         "change_key" => $adb->query_result($result, 0, 'task_exchange_change_key'));
        }
        return 0;
    }
	
	/*
	 * Function to get Folder Details from Exchange Server
	 * @param  string   $foldername   - foldername like task,calendar
	 * if no folder name is specified then default folder is deleted_items
	 * returns folder id and changeKey in array format
	*/
    
	function getExchangeFolderDetail($folderName = false){
		$request = new EWSType_GetFolderType();
		$request->FolderShape = new EWSType_FolderResponseShapeType();
		$request->FolderShape->BaseShape = EWSType_DefaultShapeNamesType::ID_ONLY;
		$request->FolderIds = new EWSType_NonEmptyArrayOfBaseFolderIdsType();
		$request->FolderIds->DistinguishedFolderId = new EWSType_DistinguishedFolderIdType();
		
		if($folderName == 'task')
			$request->FolderIds->DistinguishedFolderId->Id = EWSType_DistinguishedFolderIdNameType::TASKS;
		else if($folderName == "calendar")
			$request->FolderIds->DistinguishedFolderId->Id = EWSType_DistinguishedFolderIdNameType::CALENDAR;
		else
			$request->FolderIds->DistinguishedFolderId->Id = EWSType_DistinguishedFolderIdNameType::DELETED_ITEMS;
	
		$request->Traversal = EWSType_ItemQueryTraversalType::SHALLOW;

		$response = $this->ews->GetFolder($request);
		
		if($response->ResponseMessages->GetFolderResponseMessage->ResponseClass == 'Success'){
			if($folderName == "task")
				$folderData = $response->ResponseMessages->GetFolderResponseMessage->Folders->TasksFolder->FolderId;
			else if($folderName == "calendar")
				$folderData = $response->ResponseMessages->GetFolderResponseMessage->Folders->CalendarFolder->FolderId;
			else	
				$folderData = $response->ResponseMessages->GetFolderResponseMessage->Folders->Folder->FolderId;
				
			if($folderData)
				return json_decode(json_encode($folderData),TRUE);
		}
		return false;
	}
}

?>
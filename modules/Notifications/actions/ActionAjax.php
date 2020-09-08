<?php

class Notifications_ActionAjax_Action extends Vtiger_Action_Controller
{
    
    public function checkPermission(Vtiger_Request $request)
    {
    }
    
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod("getNotifications");
        $this->exposeMethod("getNotificationNumber");
        $this->exposeMethod("markNotificationRead");
        $this->exposeMethod("replyForComment");
        $this->exposeMethod("discardAllNotifications");
        $this->exposeMethod("eventInvitations");
        $this->exposeMethod('getEventData');
        $this->exposeMethod('getNotificationData');
    }
    
    
    public function process(Vtiger_Request $request)
    {
        $mode = $request->get("mode");
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
        }
    }
    /**
     * @param Vtiger_Request $request
     */
    public function getNotificationNumber(Vtiger_Request $request)
    {
        $response = new Vtiger_Response();
        $data = array();
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $data["count"] = Notifications_Record_Model::countNotificationsByUser($currentUser->getId());
        $response->setResult($data);
        $response->emit();
    }
    
    public function getNotifications(Vtiger_Request $request)
    {
        $response = new Vtiger_Response();
        $data = array();
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $notifications = Notifications_Record_Model::getNotificationsByUser($currentUser->getId());
        $calendarDatetimeUIType = new Calendar_Datetime_UIType();
        $items = array();
        
        foreach ($notifications as $n) {
            $relatedId = $n->get("related_to");
            if (!$relatedId || !isRecordExists($relatedId)) {
                continue;
            }
            $relatedRecordModel = Vtiger_Record_Model::getInstanceById($relatedId);
            
            $createdDateTime = $calendarDatetimeUIType->getDisplayValue($n->get("createdtime"));
            list($createdDate, $createdTime) = explode(" ", $createdDateTime);
            if ($currentUser->get("hour_format") == "12") {
                $createdTime = Vtiger_Time_UIType::getTimeValueInAMorPM($createdTime);
            }
            $relatedModule = '';
            if(getSalesEntityType($n->get('related_record')) == 'Documents'){
                $docRecord = Vtiger_Record_Model::getInstanceById($n->get('related_record'));
                $detailUrl = $docRecord->getDetailViewUrl();
                $relatedModule = 'Documents';
            }else if(getSalesEntityType($n->get('related_record')) == 'ModComments'){
                $detailUrl = $relatedRecordModel->getDetailViewUrl();
                $detailUrl .= '&relatedModule=ModComments&mode=showRelatedList&tab_label=ModComments';
                $relatedModule = 'ModComments';
            }else if($n->get('related_to')){
                $detailUrl = $relatedRecordModel->getDetailViewUrl();
                $relatedModule = getSalesEntityType($n->get('related_to'));
            }
            
            $accepted = false;
            if(getSalesEntityType($relatedId) == 'Contacts'){
                $fullName = $relatedRecordModel->get('firstname').' '.$relatedRecordModel->get('lastname');
                $relatedToModule = 'Contacts';
            }else if(getSalesEntityType($relatedId) == 'HelpDesk'){
                $fullName = $relatedRecordModel->get('ticket_title');
                $relatedToModule = 'HelpDesk';
            }else if(getSalesEntityType($relatedId) == 'Calendar'){
                $fullName = $relatedRecordModel->get('subject');
                $relatedToModule = 'Events';
                $eveRecord = Vtiger_Record_Model::getInstanceById($relatedId);
                $detailUrl = $eveRecord->getDetailViewUrl();
                $relatedModule = 'Events';
                global $adb;
                $eventQuery = $adb->pquery("SELECT * FROM vtiger_invitees WHERE activityid = ? AND inviteeid = ? AND  (status != 'accepted' AND status != 'rejected' )",
                    array($relatedId, $currentUser->id));
                
                if(!$adb->num_rows($eventQuery)){
                    $accepted = true;
                }
            }
            
            $items[] = array("id" => $n->get("notificationsid"), "notificationno" => $n->get("notificationno"), 
                "description" => $n->get('notification_type') != 'Follow Record' ? html_entity_decode($n->get("description")) : 'N/A', 
                "thumbnail" => "layouts/vlayout/skins/images/summary_Leads.png", 
                "createdtime" => $createdDate . " " . $createdTime, "full_name" => $fullName, "link" => $detailUrl, 
                "rel_id" => $relatedId, "relatedModule" => $relatedModule, "relatedRecord"=>$n->get('related_record'), 
                "relatedToModule" => $relatedToModule, "accepted" => $accepted, "title"=>$n->get('title'), "type"=>$n->get('notification_type'));
        }
        
        $data["items"] = $items;
        $data["count"] = count($items);
        $response->setResult($data);
        $response->emit();
    }
   
    public function markNotificationRead(Vtiger_Request $request)
    {
        $response = new Vtiger_Response();
        $record = $request->get("record");
        $module = $request->getModule();
        $updated = Notifications_Record_Model::updateNotificationStatus($record, Notifications_Record_Model::NOTIFICATION_STATUS_YES);
        if (!$updated) {
            $code = 200;
            $response->setError($code, vtranslate("Unable to change notification status", $module));
        }
        $response->emit();
    }
    
    public function replyForComment(Vtiger_Request $request){
        
    }
    
    public function discardAllNotifications(Vtiger_Request $request){
        global $adb;
        $response = new Vtiger_Response();
        $data = array();
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $notifications = Notifications_Record_Model::getNotificationsByUser($currentUser->getId());
    
        if(!empty($notifications)){
            foreach ($notifications as $notification){
                $adb->pquery("UPDATE vtiger_notifications SET notification_status = ? WHERE notificationsid = ?",
                    array('OK', $notification->get('notificationsid')));
                $success = true;
            }
        }
        if (!$success) {
            $code = 200;
            $response->setError($code, vtranslate("Unable to change notification status", $module));
        }else{
            $response->setResult(array('success'=>true));
        }
        $response->emit();
        
    }
    
    public function eventInvitations(Vtiger_Request $request){
        
        $response = new Vtiger_Response();
        
        $eventId = $request->get('event_id');
        $eventStatus = $request->get('status');
        $record = $request->get('record');
        
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $recordModel = Events_Record_Model::getInstanceById($eventId, 'Events');
       
        if($eventStatus == 'accept'){
            $inviteeDetails = $recordModel->getInviteesDetails($userId);
            if ($inviteeDetails[$userId] !== 'accepted') {
                $recordModel->updateInvitationStatus($eventId, $currentUser->id, 'accepted');
                $recordModel->set('assigned_user_id', $currentUser->id);
                $recordModel->set('sendnotification', '0');
                $recordModel->save();
            }
        }else if($eventStatus == 'reject'){
            $recordModel->updateInvitationStatus($eventId, $currentUser->id, 'rejected');
        }
        
        $response->setResult(array('success'=>true));
        $response->emit();
        
    }
    
    public function getEventData(Vtiger_Request $request){
        
        $record = $request->get('record');
        $sourceModule = $request->get('source_module');
        $response = new Vtiger_Response();
        
      
        $recordModel = Vtiger_Record_Model::getInstanceById($record, $sourceModule);
        $data = $recordModel->getData();
        
        $contactLink = '';
        foreach($recordModel->getRelatedToContactIdList($data['id']) as $key => $contacts){
            
            $cntModel = Vtiger_Record_Model::getInstanceById($contacts);
            if($key)
                $contactLink .= ', ';
            $permitted = Users_Privileges_Model::isPermitted("Contacts", 'DetailView', $contacts);
            if($permitted) {
                $contactLink .= '<a target="_blank" style="color:#3b78d4" href="'.$cntModel->getDetailViewUrl().'" title="Contacts">'.$cntModel->get('firstname').' '.$cntModel->get('lastname').'</a>';
            }else{
                $contactLink .= $cntModel->get('firstname').' '.$cntModel->get('lastname');
            }
        }
        
        $parentLink = '';
        if($data['parent_id']){
            $parentModel = Vtiger_Record_Model::getInstanceById($data['parent_id']);
            $permitted = Users_Privileges_Model::isPermitted(getSalesEntityType($data['parent_id']), 'DetailView', $data['parent_id']);
            if($permitted) {
                $parentLink = '<a target="_blank" style="color:#3b78d4" href="'.$parentModel->getDetailViewUrl().'" title="'.getSalesEntityType($data['parent_id']).'">'.Vtiger_Functions::getCRMRecordLabel($data['parent_id']).'</a>';
            }else{
                $parentLink = Vtiger_Functions::getCRMRecordLabel($data['parent_id']);
            }
        }
        $data['contactsLink'] = $contactLink;
        $data['parentLink'] = $parentLink;
        
        $data['startDate'] = Vtiger_Datetime_UIType::getDisplayValue($data['date_start'].' '.$data['time_start']);
        $data['endDate'] = Vtiger_Datetime_UIType::getDisplayValue($data['due_date'].' '.$data['time_end']);
        
        if(!empty($data)){
            $response->setResult(array('success'=>true, 'data'=>array_map('decode_html',$data)));
        } else {
            $response->setResult(array('success'=>false, 'message'=>vtranslate('LBL_PERMISSION_DENIED')));
        }
        $response->emit();
        
    }
    
    public function getNotificationData(Vtiger_Request $request){
        
        $record = $request->get('record');
        $sourceModule = $request->get('source_module');
        $response = new Vtiger_Response();
        
        $recordModel = Vtiger_Record_Model::getInstanceById($record);
        $data = $recordModel->getData();
       
        if(!empty($data)){
            $response->setResult(array('success'=>true, 'data'=>array_map('decode_html',$data)));
        } else {
            $response->setResult(array('success'=>false, 'message'=>vtranslate('LBL_PERMISSION_DENIED')));
        }
        $response->emit();
        
    }
    
}

?>
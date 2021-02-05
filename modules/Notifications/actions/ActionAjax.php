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
        $this->exposeMethod('loadMoreNotifications');
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
            
            $createdDateTime = Vtiger_Util_Helper::formatDateTimeIntoDayString($n->get("createdtime"));
            
            $createdDate = Vtiger_Util_Helper::formatDateDiffInStrings($n->get("createdtime"));
            
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
                "createdtime" => $createdDate , "createdDateTime" => $createdDateTime, "full_name" => $fullName, "link" => $detailUrl, 
                "rel_id" => $relatedId, "relatedModule" => $relatedModule, "relatedRecord"=>$n->get('related_record'), 
                "relatedToModule" => $relatedToModule, "accepted" => $accepted, "title"=>$n->get('title'), "type"=>$n->get('notification_type'));
        }
        
        $data["items"] = $items;
        $data["count"] = Notifications_Record_Model::countNotificationsByUser($currentUser->getId());
        $response->setResult($data);
        $response->emit();
    }
   
    public function markNotificationRead(Vtiger_Request $request)
    {
        $response = new Vtiger_Response();
        
        $module = $request->getModule();
        
        $updated = Notifications_Record_Model::updateNotificationStatus(Notifications_Record_Model::NOTIFICATION_STATUS_YES);
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
        
        global $adb;
        $record = $request->get('record');
        $sourceModule = $request->get('source_module');
        $response = new Vtiger_Response();
        
        $data = array();
        $recordModel =  $adb->pquery("SELECT * FROM vtiger_notifications WHERE notificationsid=?",array($record));
        if($adb->num_rows($recordModel)){
            $data = $adb->query_result_rowdata($recordModel);
        }
        
        if(!empty($data)){
            $response->setResult(array('success'=>true, 'data'=>array_map('decode_html',$data)));
        } else {
            $response->setResult(array('success'=>false, 'message'=>vtranslate('LBL_PERMISSION_DENIED')));
        }
        $response->emit();
        
    }
    
    public function loadMoreNotifications(Vtiger_Request $request){
        
        $moduleName = $request->getModule();
        
        $cvId = $request->get('viewid');
        $listHeaders = $request->get('list_headers', array());
        
        $listViewModel = Vtiger_ListView_Model::getInstance($moduleName, $cvId, $listHeaders);
        
        $listViewModel->set('notificationtype', $request->get('type'));
        
        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $request->get('page'));
        $pagingModel->set('viewid', $request->get('viewname'));
        
        $records = $listViewModel->getListViewEntries($pagingModel);
        
        $nextPage = false;
        $html = '';
        if(!empty($records)){
            if($pagingModel->isNextPageExists())
                $nextPage = true;
            foreach($records as $type => $LIST_DATA){
                $relatedRecord = $LIST_DATA['relatedRecord'];
                $rel_id = $LIST_DATA['rel_id'];
                
                if($LIST_DATA['relatedModule'] == "ModComments"){
                    $moduleIcon = '<i class="vicon-chat" title="comment" style="font-size: 2rem !important;"></i>';
                    $reply = "<button title='reply' data-commentid=$relatedRecord class='btn replyComment' style='padding:5px 12px !important;border-radius:25px !important;border:1px solid #06d79c !important;background-color:transparent !important;color:#06d79c !important;'>Reply now</button>";
                    $toolTipClass = '';
                }else if($LIST_DATA['relatedModule'] == "Documents"){
                    $moduleIcon = '<i class="vicon-documents" title="document" style="font-size: 2rem !important;"></i>';
                    $toolTipClass = '';
                    $reply = '';
                }else if($LIST_DATA['relatedModule'] == "Events"){
                    $moduleIcon = '<i class="vicon-calendar" title="Events" style="font-size: 2rem !important;"></i>';
                    if(!$LIST_DATA['accepted']){
                        $reply = "<button title='Accept' data-event='accept' data-eventid=$rel_id class='btn eventAction' style='padding:5px 12px !important;border-radius:25px !important;border:1px solid #06d79c !important;background-color:transparent !important;color:#06d79c !important;'>Accept</button>&nbsp;
                        <button title='Reject' data-event='reject' data-eventid=$rel_id class='btn eventAction' style='padding:5px 12px !important;border-radius:25px !important;border:1px solid #ef5350 !important;background-color:transparent !important;color:#ef5350 !important;'>Reject</button>";
                    }else{
                        $reply = '';
                    }
                    $toolTipClass = '';
            
                }else if($LIST_DATA['type'] == "Follow Record"){
                    $moduleIcon = '<i title="Follow" class="fa fa-star-o" style="font-size: 2rem !important;"></i>';
                    $toolTipClass = 'followUpClass';
                    $reply = '';
                }
                $title = $LIST_DATA['title']?$LIST_DATA['title']:$LIST_DATA['description'];
               
                $html .='<div class="col-sm-12 col-xs-12 maindiv notification_link '.$toolTipClass.'" data-rel_id='.$rel_id.' data-id="'.$LIST_DATA['id'].'" data-module="'.$LIST_DATA['relatedModule'].'">
                    <div class="col-sm-10 col-xs-10">
                        <div class="pull-left" style="margin:4px!important;">
                            '.$moduleIcon.'
                        </div>
                        <span class="notification_full_name">'.$title.'</span>
                        <span class="notification_description" data-fullComment="'.htmlspecialchars($LIST_DATA['description']).'" data-shortComment="'.htmlspecialchars(mb_substr($LIST_DATA['description'], 0, 150)).'..." data-more="'.vtranslate('LBL_SHOW_MORE',$MODULE).'" data-less="'.vtranslate('LBL_SHOW',$MODULE).' '.vtranslate('LBL_LESS',$MODULE).'">';
                        if( strlen($LIST_DATA['description']) > 150){
                            $html .= mb_substr(trim($LIST_DATA['description']),0,150).'...
								<a class="pull-right toggleNotification showMore" style="color: blue;"><small>'.vtranslate('LBL_SHOW_MORE',$MODULE).'</small></a>';
                        }else{
                            $html .= $LIST_DATA['description'];
						}
						$html .= '</span>
                        <br><br><span>'.$reply.'</span>
                    </div>
                    <div class="col-sm-2 col-xs-2" style="font-size:small !important;">
                        <small class="pull-right notification_createdtime" title="'.Vtiger_Util_Helper::formatDateTimeIntoDayString($LIST_DATA['createdtime']).'">
                            '.Vtiger_Util_Helper::formatDateDiffInStrings($LIST_DATA['createdtime']).'
                        </small>
                        <br>
                        <span aria-hidden="true" style="cursor: pointer;" class="fa fa-times-circle pull-right delete" onclick="NotificationsJS.deleteNotification('.$LIST_DATA['id'].',this)" data-id="'.$LIST_DATA['id'].'"></span>
                    </div>
                    <div class="col-sm-12 col-xs-12">
                        <div class="divider" style="height: 1px;margin: 8px 0;overflow: hidden;background-color: #ebebeb;">&nbsp;</div>
                    </div>
                </div>';
            }
        }
       
        $response = new Vtiger_Response();
        $response->setResult(array('success'=>true, 'nextpage' => $nextPage, 'data'=>$html));
        $response->emit();
    }
    
}

?>
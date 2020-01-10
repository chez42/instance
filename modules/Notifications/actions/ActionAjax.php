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
            if(getSalesEntityType($n->get('related_record')) == 'Documents'){
                $docRecord = Vtiger_Record_Model::getInstanceById($n->get('related_record'));
                $detailUrl = $docRecord->getDetailViewUrl();
            }else if(getSalesEntityType($n->get('related_record')) == 'ModComments'){
                $detailUrl = $relatedRecordModel->getDetailViewUrl();
                $detailUrl .= '&relatedModule=ModComments&mode=showRelatedList&tab_label=ModComments';
            }
            
            if(getSalesEntityType($relatedId) == 'Contacts'){
                $fullName = $relatedRecordModel->get('firstname').' '.$relatedRecordModel->get('lastname');
            }else if(getSalesEntityType($relatedId) == 'HelpDesk'){
                $fullName = $relatedRecordModel->get('ticket_title');
            }
            
            $items[] = array("id" => $n->get("notificationsid"), "notificationno" => $n->get("notificationno"), "description" => $n->get("description"), "thumbnail" => "layouts/vlayout/skins/images/summary_Leads.png", "createdtime" => $createdDate . " " . $createdTime, "full_name" => $fullName, "link" => $detailUrl, "rel_id" => $relatedId);
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
}

?>
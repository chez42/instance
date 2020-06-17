<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class OmniCal_ExchangeEws_Action extends Vtiger_BasicAjax_Action{
    public function __construct() {
    }
    
    public static function HandleTasksFromExchange($username){
        $tasks = new OmniCal_ExchangeTasks_Model();
        $tasks->SetImpersonation($username);
        $response = $tasks->GetTaskInfoFromExchange();
        $tasks->AutoCreateUpdateDeleteCRMWithTaskInfo($response);
 #       print_r($response);
    }
    
    public static function HandleEventsFromExchange($username){
        $events = new OmniCal_ExchangeEvent_Model();
        $events->SetImpersonation($username);
		//$events->createUpdateExchangeEventsFromDateRange(); // Omniver3 : Sync current date Events only 2017-03-02
        $response = $events->GetEventInfoFromExchange();
        $events->AutoCreateUpdateDeleteCRMWithEventInfo($response);
#        print_r($response);
    }
    
    public static function HandleContactsFromExchange($username){
     return;   $contacts = new OmniCal_ExchangeContacts_Model();
        $contacts->SetImpersonation($username);
        $response = $contacts->GetContactInfoFromExchange();
        $contacts->AutoCreateUpdateDeleteCRMWithContactInfo($response);
#        print_r($response);
    }
    
    public static function RecurringException($username){
        $events = new OmniCal_ExchangeEvent_Model();
        $events->SetImpersonation($username);
        $info = $events->GetExchangeIDAndChangeKeyFromActivityId(1675161);
        $exception_info = $events->GetExceptionItem($info['id'], 4);
//        print_r($exception_info);
        if($exception_info->ResponseMessages->GetItemResponseMessage->ResponseClass == "Success"){
            $item_id = $exception_info->ResponseMessages->GetItemResponseMessage->Items->CalendarItem->ItemId->Id;
            $item_ck = $exception_info->ResponseMessages->GetItemResponseMessage->Items->CalendarItem->ItemId->ChangeKey;
        }
        
        $start = '2015-01-18T16:00:00+00:00';
        $end = '2015-01-18T18:00:00+00:00';
        $response = $events->UpdateEventInExchange($item_id, $item_ck, "CUSTOM SUBJECT", "CUSTOM DESCRIPTION", "HTML", $start,
                                                   $end, "CUSTOM LOCATION");

#        echo "AND THE RESPONSE IS!...<br />";
#        print_r($response);
    }
    
    public static function HandleEventsFromExchangeByDate($username, $start_date){
        $events = new OmniCal_ExchangeEvent_Model();
        $events->SetImpersonation($username);
        $response = $events->GetEventsFromExchangeByDate($start_date);
        if($response->ResponseMessages->FindItemResponseMessage->ResponseClass == "Success")
            foreach($response->ResponseMessages->FindItemResponseMessage->RootFolder->Items->CalendarItem AS $k => $v){
                $record = OmniCal_ExchangeBridge_Model::DoesItemExist($v->ItemId->Id);
                if(!$record && $events->user_id){//Make sure the item doesn't exist already and that a user id exists
                    $event = $events->GetEventInfo($v->ItemId->Id);
#                    echo "CREATING RECORD<br />";
#                    echo "<strong>EVENT INFO</strong><br />"; print_r($event); echo "<br />EVENT INFO OVER<br /><br />";
                    $data = $events->RequestToData($event);
                    if(is_array($data))
                        $events->CreateEventInCRM($data);
                }else{
                    if($record && !OmniCal_ExchangeBridge_Model::DoChangeKeysMatch($record, $v->ItemId->ChangeKey)){//Make sure the item exists and the change key's don't match
#                        echo "UPDATING RECORD: {$record}<br />";
                        $event = $events->GetEventInfo($v->ItemId->Id);
#                        echo "<strong>EVENT INFO</strong><br />"; print_r($event); echo "<br /><strong>EVENT INFO OVER</strong><br /><br />";
                        $data = $events->RequestToData($event);
                        $events->UpdateEventInCRM($record, $data);
                    }
                }

            }
/*            foreach($response->ResponseMessages->FindItemResponseMessage->ResponseClass->RootFolder->Items->CalendarItem->ItemId AS $k => $v){
                print_r($v);
            }*/
        $events->AutoCreateUpdateDeleteCRMWithEventInfo($response);
    }
    
    public function process(\Vtiger_Request $request) {
        if($request->get('run_all')){
            global $adb;
            $query = "SELECT user_name FROM vtiger_users WHERE exchange_enabled = 1 ORDER BY id";
            $result = $adb->pquery($query, array());
            $count = 0;
            foreach($result as $k => $v){
                $user_name = $v['user_name'];
                echo "<br /><br /><strong>TRYING FOR {$user_name}<br /><br />";
                try{
                    self::HandleTasksFromExchange($user_name);
                    self::HandleEventsFromExchange($user_name);
                } catch (Exception $ex){
                    echo "<strong>ISSUE with {$user_name}</strong>";
                }
            }
            return;
        } else
        if(!$request->get('user_id')){
            $current_user = Users_Record_Model::getCurrentUserModel();
            $user_name = $current_user->get('user_name');
        } else{
            $user_name = getUserName($request->get('user_id'));
        }

        self::HandleTasksFromExchange($user_name);
        self::HandleEventsFromExchange($user_name);
        self::HandleContactsFromExchange($user_name);
    }
}

?>

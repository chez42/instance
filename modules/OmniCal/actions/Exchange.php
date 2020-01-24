<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once("libraries/exchange/cCalendar.php");
require_once('libraries/exchange/ExchangeServices.php');

class OmniCal_Exchange_Action extends Vtiger_BasicAjax_Action{
    public $fieldMap, $rootFolders, $client, $clientOrgId, $users;
    
    public function __construct() {
        global $adb;
        parent::__construct();
        $this->fieldMap = array(
            'Contact' => array( //ExchangeField => CRM Field
                'GivenName'                             => 'firstname',#contactdetails
                #'MiddleName'                            => 'middle_name',#TAKEN OUT BY ME
                'Surname'                               => 'lastname',#contactdetails
                #'SpouseName'                            => 'spouse',#TAKEN OUT BY ME
                'Birthday'                              => 'birthday',#contactsubdetails
                'JobTitle'                              => 'title',#contactdetails
                'CompanyName'                           => $employerName,//'cf_642',#Now In Contacts, contactscf
                'BusinessHomePage'                      => $employerWebsite,//'cf_643',#Now In Contacts, contactscf
                #'DateTimeCreated'                      => 'created_date',
                #'WeddingAnniversary'                    => 'wedding_anniversary',#TAKEN OUT BY ME
                'EmailAddresses.EmailAddress1'          => 'email',#contactdetails
                'PhoneNumbers.BusinessPhone'            => $employerPhone,//'cf_644',#Now in contacts, contactscf
                'PhoneNumbers.MobilePhone'              => 'mobile',#contactdetails
                'PhoneNumbers.HomePhone'                => 'phone',#contactdetails
                'PhoneNumbers.BusinessFax'              => 'phone_fax',#contactdetails
                'PhysicalAddresses.Home.Street'         => 'street_address',#contactaddress
                //'PhysicalAddresses.Home.Street2'        => 'street_address2',
                'PhysicalAddresses.Home.City'           => 'city',#contactaddress
                'PhysicalAddresses.Home.State'          => 'state_or_province',#contactaddress
                'PhysicalAddresses.Home.PostalCode'     => 'postal_code',#contactaddress
                'PhysicalAddresses.Business.Street'     => $employerStreet1,//'cf_645',#Now In Contacs, contactscf
                'PhysicalAddresses.Business.City'       => $employerCity,//'cf_647',#Now In Contacts, contactscf
                'PhysicalAddresses.Business.State'      => $employerState,//'cf_646',#Now In Contacts, contactscf
                'PhysicalAddresses.Business.PostalCode' => $employerPostal,//'cf_648',#Now In Contacts, contactscf
                #'Body'                                  => 'personal_information_comment',#TAKEN OUT BY ME
                #'PhysicalAddresses.Business.CountryOrRegion' => 'personal_information_country_id',
                #'ItemId.Id' => 'contact_exchange_item_id',
                #'ItemId.ChangeKey' => 'contact_exchange_change_key',
            ),
            'Task' => array(
        #        'CompleteDate'    => 'due_date',
                'DueDate'         => 'due_date',
        #        'PercentComplete' => 'task_completion_level',    #TAKEN OUT BY ME
                'StartDate'       => 'date_start',
                #'Status' => 'task_status_id',
                #'StatusDescription' => 'task_status_description',
                'Subject'         => 'subject',
                'Body'            => 'description', #In crmentity
            ),
            'CalendarItem' => array(
                'Start'           => 'date_start',
                'End'             => 'due_date',
                'Location'        => 'location',
                'Subject'         => 'subject',
                'Body'            => 'description', #In crmentity
        #        'IsAllDayEvent'   => 'appointment_is_all_day',     #TAKEN OUT BY ME
            )
        );
        
        $this->rootFolders = array('Contact'=>'contacts', 'Task'=> 'tasks', 'CalendarItem' => 'calendar');
        $this->client = new ExchangeServices('libraries/exchange/Services.wsdl',array('features'=>SOAP_SINGLE_ELEMENT_ARRAYS,"trace" => TRUE,"exceptions"=>FALSE,"cache_wsdl"=>WSDL_CACHE_NONE),'concertadmin@concertglobal.com','Consec1');
        $this->clientOrgId = 1068;
        $this->users = $adb->pquery("SELECT id, user_exchange_username FROM vtiger_users where deleted = 0 AND user_exchange_username <> ''",null);
    }
    
    public function process(Vtiger_Request $request) {
        $this->RunExchange($request);
    }

    public function RunExchange(Vtiger_Request $request){
        global $adb;
        foreach ($this->users as $u){
            ob_flush();
            flush();
            list ($userId,$exchangeUser) = $u;
            $euser = $exchangeUser;
            if($exchangeUser == "sampleuser" || ($exchangeUser == "ehorton"))
{
                echo("<strong>Impersonating $exchangeUser</strong><br />");
                $this->client->_setImpersonation($exchangeUser);
                $userFolders = $adb->pquery("SELECT * from exchange_sync where exchange_sync_user_id = ? and exchange_sync_type <> 'Email' and exchange_sync_enabled = 1",array($userId));
                echo "SELECT * from exchange_sync where exchange_sync_user_id = {$userId} and exchange_sync_type <> 'Email' and exchange_sync_enabled = 1<br />";
                
                foreach ($userFolders as $f){
                    $folder     = $f['exchange_sync_folder_name'];
                    $folderType = $f['exchange_sync_type'];
                    $syncState  = $f['exchange_sync_state'];
                    $syncDate   = $f['exchange_sync_date'];
                    echo "SYNC DATE: {$syncDate}<br />";

                    echo "<br />Folder Type = " . $folderType . "<br />";
                    
                    $rootFolder = $this->rootFolders[$folderType];
                    if (strlen($folder) > 0) {
                        $folderId = $this->client->_getFolderIdByName($folder,$rootFolder);
                        echo "FOLDER ID: {$folderId}<br />";
                        $distinguished = FALSE;
                    }
                    else {
                        $folderId = $rootFolder; 
                        $distinguished = TRUE;
                    }
                    
                    if (!$folderId) {
                        echo("$exchangeUser $rootFolder $folder NOT FOUND\n");
                        continue 2;
                    }
                    //Get list of updates from Exchange
                    
                    $res = $this->client->_syncFolderItems($folderId,$distinguished,$syncState);
                    $response = $res->ResponseMessages->SyncFolderItemsResponseMessage[0];

                    if (!$res || $response->ResponseClass != 'Success') {
                        echo("SYNC ERROR: " . print_r($res,TRUE));
                        continue;
                    }
                    $touched_records = array();

                    $newSyncState = $response->SyncState;
                    $changes      = $response->Changes;

                    $records = array();

                    $records = array_merge((array)$changes->Create,(array)$changes->Update);
                    foreach ($records as $c){
                        $itemId = $c->$folderType->ItemId;

                        if (!$itemId->Id) {
                            echo("NO ITEM ID: " . print_r($c,TRUE));
                            continue;
                        }
                        $itemResp = $this->client->GetItem(new GetItemType($c->$folderType->ItemId,"AllProperties"));

                        $items = $itemResp->ResponseMessages->GetItemResponseMessage[0]->Items->$folderType;
                        $item = $items[0];
                        if (!is_array($items) || count($items) != 1) {
                            echo("NO ITEM ID: " . print_r($c,TRUE));
                            continue;
                        }
                        switch ($folderType) {
                            case 'Contact':
                                echo "UPDATE CRM CONTACT....<br />";
                                $touchedId = $this->updateCrmContact($item);
                                echo "DONE UPDATING CONTACT....<br />";
            //                    echo "Sync State: {$newSyncState}<br />";
                                break;
                            case 'Task':
                                echo "UPDATE CRM TASK....<br />";
                                $touchedId = $this->updateCrmTask($item);
                                echo "DONE UPDATING TASK....<br />";
            //                    echo "TOUCHED ID = {$touchedId}<br />";
                                break;
                            case 'CalendarItem':
                                echo "EXCHANGE UPDATE, UPDATING CRM MEETING....<br />";
                                $touchedId = $this->updateCrmCalendar($item);
                                echo "DONE UPDATING MEETING....<br />";
                                break;
                        }
                        if ($touchedId > 0)
                            $touched_records[] = $touchedId;
                    }
                    if ($changes->Delete && is_array($changes->Delete) && count($changes->Delete) > 0){ //Deleted items. mark as deleted in the CRM
                        $item_ids = array();
                        foreach ($changes->Delete as $d) 
                        {
                            $r = $adb->pquery("SELECT activityid FROM vtiger_activitycf WHERE task_exchange_item_id=?",array($d->ItemId->Id));
                            $item_ids[] = $adb->query_result($r, 0, "activityid");
                            echo "DELETE: {$adb->query_result($r, 0, 'activityid')}<br />";
                        }
                        switch ($folderType) 
                        {
                            case 'Contact':
            //                    $db->executeParam("UPDATE contacts set marked_for_deletion = 1 WHERE contact_exchange_item_id in (?) and contact_client_organization_id = ?",$item_ids,$clientOrgId);
                                break;
                            case 'Task':
                                echo "DELETING TASK<br />";
                                for($x = 0; $x < sizeof($item_ids); $x++)
                                    $adb->pquery("UPDATE vtiger_crmentity set deleted = 1 WHERE crmid = ?",array($item_ids[$x]));
                                echo "DONE DELETING TASK<br />";
                                break;
                            case 'CalendarItem':
                                echo "DELETING MEETING<br />";
                                echo "SIZE OF ITEM_IDS = " . sizeof($item_ids) . "<br />";
                                for($x = 0; $x < sizeof($item_ids); $x++)
                                    $adb->pquery("UPDATE vtiger_crmentity set deleted = 1 WHERE crmid = ?",array($item_ids[$x]));
                                echo "DONE DELETING MEETING<br />";
                                break;
                        }
                    }
                    // Transfer CRM records to exchange
                    if (count($touched_records) == 0)
                        $touched_records[] = 0;
                    $touched_records = SeparateArrayWithCommasAndSingleQuotes($touched_records);
                    switch ($folderType) 
                    {
                        case 'Contact':
                            echo "UPDATE EXCHANGE CONTACT....VCRM -> Exchange<br />";
                            $res = $adb->pquery("SELECT crmid AS contact_id from vtiger_crmentity 
                                                      LEFT JOIN vtiger_contactscf ccf ON  vtiger_crmentity.crmid = ccf.contactid
                                                      WHERE smownerid = ? and setype='Contacts' and deleted = 0 and (modifiedtime > ? OR contact_exchange_item_id is NULL) 
                                                      and crmid not in (?)",array($userId,$syncDate,$touched_records));

                            echo "SYNC DATE IS {$syncDate}<br />";
                            echo "DONE UPDATING EXCHANGE CONTACT....<br />";

                            $count = $adb->num_rows($res);
                            for($x = 0; $x < $count; $x++)
                            {
                                $contactid = $adb->query_result($res, $x, "contact_id");
                                if($this->IsContactExchangeEnabled($contactid))
                                    $this->updateExchangeContact($adb->query_result($res,$x,"contact_id"),$folderId,$distinguished);
                                else
                                    echo "Exchange Disabled for {$contactid}<br />";
                            }
                            break;

                        case 'Task':
                        {
                            echo "ENTERING TASK SECTION FOR VCRM -> Exchange<br />";
                            $res = $adb->pquery("SELECT crmid AS task_id from vtiger_crmentity 
                                                      LEFT JOIN vtiger_activitycf acf ON vtiger_crmentity.crmid = acf.activityid
                                                      LEFT JOIN vtiger_activity a ON a.activityid = acf.activityid
                                                      WHERE smownerid = ? and setype='Calendar' and vtiger_crmentity.deleted = 0 
                                                      and createdtime > '2012-02-02'
                                                      and activitytype='Task' and (modifiedtime > ? OR task_exchange_item_id is NULL) 
                                                      and crmid not in ({$touched_records})",array($userId,$syncDate));

                            if($adb->num_rows($res) > 0)
                            {
                                echo "NUMBER OF TASKS TO UPDATE: " . $adb->num_rows($res) . "<br />";
                                foreach($res AS $k => $v)
                                {
            //                        echo $v["task_id"] . "<br />";
                                    $this->updateExchangeTask($v["task_id"],$folderId,$distinguished);
                                }
                            }
                            else
                                "NO TASKS NEED UPDATING<br />";
                        }
                            break;
                        case 'CalendarItem':
                        {
                            echo "CHECKING IF EXCHANGE NEEDS UPDATED....VCRM -> Exchange<br />";

                            $res = $adb->pquery("SELECT ent.*, i.inviteeid, act.*, acf.* from vtiger_crmentity ent 
                                                LEFT JOIN vtiger_invitees i on i.activityid = ent.crmid
                                                JOIN vtiger_activity act ON act.activityid = ent.crmid
                                                JOIN vtiger_activitycf acf ON acf.activityid = ent.crmid
                                                WHERE  ent.smownerid = ?
                                                AND (act.activitytype = 'Meeting' OR act.activitytype = 'Call')
                                                AND acf.deleted = 0 
                                                AND (ent.modifiedtime > ? OR acf.task_exchange_change_key is NULL OR acf.task_exchange_change_key = '')
                                                AND ent.createdtime > '2012-01-01'
                                                AND ent.crmid NOT IN ({$touched_records})
                                                ORDER BY ent.crmid", array($userId, $syncDate));

                            $count = 0;
            //                echo $adb->num_rows($res);
                            $count = $adb->num_rows($res);
                            $returnedids = array();
                            $invitee = array();
                            $meetingInfo = array();
                            echo "<br />COUNT IS: {$count}<br />";
                            if($count > 0)//First we insert for the owner of the meeting
                            {
                                for($x = 0; $x < $count; $x++)
                                    $returnedids[$x] = $adb->query_result($res, $x, "crmid");

                                $returnedids = array_unique($returnedids);

                                for($x = 0; $x < sizeof($returnedids); $x++)
                                {
                                    $tmpId = $returnedids[$x];
                                    echo "Meeting is {$tmpId}<br />";
                                    $res = $adb->pquery("SELECT ent.*, i.inviteeid, act.*, acf.*, acf.deleted AS acdeleted, ent.deleted AS entdeleted from vtiger_crmentity ent 
                                                        LEFT JOIN vtiger_invitees i on i.activityid = ent.crmid
                                                        JOIN vtiger_activity act ON act.activityid = ent.crmid
                                                        JOIN vtiger_activitycf acf ON acf.activityid = ent.crmid
                                                        WHERE ent.smownerid = ?
                                                        AND ent.crmid = ?
                                                        AND (act.activitytype = 'Meeting' OR act.activitytype = 'Call')
                                                        AND acf.deleted = 0
                                                        AND ent.createdtime > 2012-02-01", array($userId, $tmpId));
                                    echo "SELECT ent.*, i.inviteeid, act.*, acf.*, acf.deleted AS acdeleted, ent.deleted AS entdeleted from vtiger_crmentity ent 
                                                        LEFT JOIN vtiger_invitees i on i.activityid = ent.crmid
                                                        JOIN vtiger_activity act ON act.activityid = ent.crmid
                                                        JOIN vtiger_activitycf acf ON acf.activityid = ent.crmid
                                                        WHERE ent.smownerid = {$userId}
                                                        AND ent.crmid = {$tmpId}
                                                        AND act.activitytype = 'Meeting'
                                                        AND acf.deleted = 0
                                                        AND ent.createdtime > '2012-02-01'";
                                    $count = $adb->num_rows($res);

                                    echo "COUNT NOW IS {$count}";
                                    $meetingInfo = $this->SetupMeetingInfo($res, 0);
                                    $meetingInfo['invitee'] = $userId;
                                    echo "DESCRIPTION = " . $meetingInfo['description'] . "<br />";
                                    $invitee[0] = $userId;

                                    for($y = 0; $y < $count; $y++)
                                        $invitee[$y+1] = $adb->query_result($res, $y, "inviteeid");

                                    $this->updateExchangeCalendar($meetingInfo, $invitee, $folderId,$distinguished);
                                }
                            }
                            else
                                echo "NO EXCHANGE UPDATE REQUIRED FOR THIS CALENDAR ITEM....<br />";
                            echo "DONE UPDATING EXCHANGE CALENDAR<br />";
                        }
                            break;
                    }
                    echo "UPDATE exchange_sync SET exchange_sync_state = '$newSyncState', exchange_sync_date = UTC_TIMESTAMP() where exchange_sync_id = '{$f['exchange_sync_id']}'<br />";
                    $adb->pquery("UPDATE exchange_sync SET exchange_sync_state = ?, exchange_sync_date = UTC_TIMESTAMP() where exchange_sync_id = ?",array($newSyncState,$f['exchange_sync_id']));
                }
            }
        }
    }

    function SetupMeetingInfo($res, $x)
    {
        global $adb;
        $cal = new cCalendar();
        $crmid = $adb->query_result($res, $x, "crmid");
        $related_to_name = $cal->GetRelatedTo($crmid);
        $related_to_contacts = $cal->GetRelatedContacts($crmid);
        if($related_to_contacts)
            foreach($related_to_contacts AS $k=>$v)
            {
                $contacts .= $v . " ";
            }
        $subject = $adb->query_result($res, $x, "subject");
        if($related_to_name || $contacts)
            $subject .= " ( ";
        if($related_to_name)
            $subject .= $related_to_name;
        if($contacts)
            $subject .= $contacts;
        if($related_to_name || $contacts)
            $subject .= ")";

        $meetingInfo = array("activity_id" => $adb->query_result($res, $x, "crmid"),
                             "invitee" => $adb->query_result($res, $x, "inviteeid"),
                             "creator" => $adb->query_result($res, $x, "smcreatorid"),
                             "owner" => $adb->query_result($res, $x, "smownerid"),
                             "modifiedby" => $adb->query_result($res, $x, "modifiedby"),
                             "description" => html_entity_decode($adb->query_result($res, $x, "description")),
                             "createdtime" => $adb->query_result($res, $x, "createdtime"),
                             "modifiedtime" => $adb->query_result($res, $x, "modifiedtime"),
                             "subject" => $subject,
                             "date_start" => $adb->query_result($res, $x, "date_start"),
                             "due_date" => $adb->query_result($res, $x, "due_date"),
                             "time_start" => $adb->query_result($res, $x, "time_start"),
                             "time_end" => $adb->query_result($res, $x, "time_end"),
                             "duration_hours" => $adb->query_result($res, $x, "duration_hours"),
                             "eventstatus" => $adb->query_result($res, $x, "eventstatus"),
                             "priority" => $adb->query_result($res, $x, "priority"),
                             "location" => $adb->query_result($res, $x, "location"),
                             "deleted" => $adb->query_result($res, $x, "acdeleted"),
                             "entdeleted" => $adb->query_result($res, $x, "entdeleted"),
                             "task_exchange_item_id" => $adb->query_result($res, $x, "task_exchange_item_id"),
                             "task_exchange_change_key" => $adb->query_result($res, $x, "task_exchange_change_key"));
        return $meetingInfo;
    }

    function updateCrmContact($contact)
    {
        global $employerName, $employerWebsite, $employerPhone, $employerStreet1, $employerStreet2, $employerStreet, $employerCity, $employerState, $employerPostal;

        ob_flush();
        flush();
        echo "Contact...<br />";

        global $dao, $adb, $userId;
        $objs = array();

        $res = $adb->pquery("SELECT contactid AS contact_id, contact_exchange_change_key FROM vtiger_contactscf where contact_exchange_item_id = ?",array($contact->getField('ItemId.Id')));
        $res = $adb->fetch_array($res);
        echo "Field ID: " . $contact->getField('ItemId.Id') . "<br />";
        if (is_array($res) && count($res) > 0) 
        {
            list ($contactId,$curChangeKey) = $res;
            $newChangeKey = $contact->getField('ItemId.ChangeKey');

            echo "New Change Key = {$newChangeKey} -- Old Change Key = {$curChangeKey}<br /><br />";
            if ($curChangeKey == $newChangeKey) { //The change key in the database matches the one from exchange.  Skip the update
                echo("[Contact] Skipping update contact_id=$contactId\n");
                #print "$contactId: $curChangeKey == $newChangeKey\n";
                return NULL; //not updated, don't return contact ID
            }

            //Fetch existing objects

            echo "Contact Exists In vTiger, Updating..." . $objs['Contact'] . "<br />";

            //Map field names from the exchange data to the CRM field names using fieldMap
            foreach ($this->fieldMap['Contact'] as $exField => $crmField) 
            {
                    $fieldValue = $contact->getField($exField);
                    echo "Field Value..." . $exField . " = " . $fieldValue . "<br />";

                    $objs['Contact'][$crmField]=$fieldValue;

            }

            echo "EXCHANGE PULL COMPLETE, INFO TO INPUT AS FOLLOWS FOR USER ID {$contactId}...<br />";       

            $firstname = $objs['Contact']['firstname'];
            $lastname = $objs['Contact']['lastname'];
            $birthday = $objs['Contact']['birthday'];
            $title = $objs['Contact']['title'];
    //		$cf_642 = $objs['Contact']['cf_642'];
            $cf_642 = $objs['Contact'][$employerName];
    //		$cf_643 = $objs['Contact']['cf_643'];
            $cf_643 = $objs['Contact'][$employerWebsite];
            $created_date = $objs['Contact']['created_date'];
            $email = $objs['Contact']['email'];
    //		$cf_644 = $objs['Contact']['cf_644'];
            $cf_644 = $objs['Contact'][$employerPhone];
            $mobile = $objs['Contact']['mobile'];
            $phone = $objs['Contact']['phone'];
            $phone_fax = $objs['Contact']['phone_fax'];
            $street_address = $objs['Contact']['street_address'];
            $city = $objs['Contact']['city'];
            $state_or_province = $objs['Contact']['state_or_province'];
            $postal_code = $objs['Contact']['postal_code'];
    /*		$cf_645 = $objs['Contact']['cf_645'];
            $cf_647 = $objs['Contact']['cf_647'];
            $cf_646 = $objs['Contact']['cf_646'];
            $cf_648 = $objs['Contact']['cf_648'];*/
            $cf_645 = $objs['Contact'][$employerStreet1];
            $cf_647 = $objs['Contact'][$employerCity];
            $cf_646 = $objs['Contact'][$employerState];
            $cf_648 = $objs['Contact'][$employerPostal];

            if(!$this->IsContactExchangeEnabled($contactId))
            {
                if($newChangeKey)
                {
                    $query = "UPDATE vtiger_contactscf cf
                                      SET cf.contact_exchange_change_key = '{$newChangeKey}'
                                      WHERE cf.contactid = {$contactId}";
                    echo "HERE IT IS: {$query}<br />";
                    $adb->pquery($query, array());
                }
                echo "Contact Exchange Disabled<br />";
            }
            else
            {
                $query = "UPDATE vtiger_contactdetails cd
                                  LEFT JOIN vtiger_contactaddress a ON a.contactaddressid = cd.contactid
                                  LEFT JOIN vtiger_contactscf cf ON cd.contactid = cf.contactid
                                  LEFT JOIN vtiger_contactsubdetails cs ON cs.contactsubscriptionid = cd.contactid
                                  SET cd.firstname = '{$firstname}', cd.lastname = '{$lastname}', cs.birthday = '{$birthday}', cd.title = '{$title}',
                                  cf.{$employerName} = ?, cf.contact_exchange_change_key = '{$newChangeKey}', cf.contact_exchange_item_id = '{$contact->getField('ItemId.Id')}', 
                                  cf.{$employerWebsite} = ?, cd.email = '{$email}', 
                                  cf.{$employerPhone} = ?, cd.mobile = '{$mobile}', cd.phone = '{$phone}', cd.fax = '{$phone_fax}', a.mailingstreet = '{$street_address}',
                                  a.mailingcity = '{$city}', a.mailingstate = '{$state_or_province}', a.mailingzip = '{$postal_code}', 
                                  cf.{$employerStreet1} = ?, cf.{$employerCity} = ?, cf.{$employerState} = ?, cf.{$employerPostal} = ?
                                  WHERE cd.contactid = {$contactId}";

                echo "QUERY IS: {$query}<br />";
                echo "Change Key Is: {$newChangeKey}<br />";
                
                $adb->pquery($query, array($cf_642, $cf_643, $cf_644, $cf_645, $cf_647, $cf_646, $cf_648));
            }
        }

        else 
        {
            $newChangeKey = $contact->getField('ItemId.ChangeKey');

            echo "Contact DOES NOT Exist In vTiger with a change key, checking email..." . $objs['Contact'] . "<br />";

                    //Map field names from the exchange data to the CRM field names using fieldMap
            //    echo "Test Contact Field First Name = " . $contact->getField("GivenName") . "<br />";
            foreach ($this->fieldMap['Contact'] as $exField => $crmField) 
            {
                $fieldValue = $contact->getField($exField);
    //            echo "Field Value..." . $exField . " = " . $fieldValue . "<br />";

                $objs['Contact'][$crmField]=$fieldValue;
            }

    //    echo "LOOP OVER, first name is " . $objs['Contact']["firstname"];
            echo "EXCHANGE PULL COMPLETE, INFO TO INPUT AS FOLLOWS FOR USER ID {$contactId}...<br />";

            $firstname = $objs['Contact']['firstname'];
            $lastname = $objs['Contact']['lastname'];
            $birthday = $objs['Contact']['birthday'];
            $title = $objs['Contact']['title'];
    //		$cf_642 = $objs['Contact']['cf_642'];
            $cf_642 = $objs['Contact'][$employerName];
    //		$cf_643 = $objs['Contact']['cf_643'];
            $cf_643 = $objs['Contact'][$employerWebsite];
            $created_date = $objs['Contact']['created_date'];
            $email = $objs['Contact']['email'];
    //		$cf_644 = $objs['Contact']['cf_644'];
            $cf_644 = $objs['Contact'][$employerPhone];
            $mobile = $objs['Contact']['mobile'];
            $phone = $objs['Contact']['phone'];
            $phone_fax = $objs['Contact']['phone_fax'];
            $street_address = $objs['Contact']['street_address'];
            $city = $objs['Contact']['city'];
            $state_or_province = $objs['Contact']['state_or_province'];
            $postal_code = $objs['Contact']['postal_code'];
    /*		$cf_645 = $objs['Contact']['cf_645'];
            $cf_647 = $objs['Contact']['cf_647'];
            $cf_646 = $objs['Contact']['cf_646'];
            $cf_648 = $objs['Contact']['cf_648'];*/
            $cf_645 = $objs['Contact'][$employerStreet1];
            $cf_647 = $objs['Contact'][$employerCity];
            $cf_646 = $objs['Contact'][$employerState];
            $cf_648 = $objs['Contact'][$employerPostal];

            $res = $adb->pquery("SELECT contactid FROM vtiger_contactdetails cd
                                 LEFT JOIN vtiger_crmentity e ON e.crmid = cd.contactid
                                 WHERE email=?
                                 AND e.smownerid = ?",array($email, $userId));
    //        $res = $adb->fetch_array($res);
            echo "COUNT RES = " . $adb->num_rows($res) . "<br />SELECT contactid FROM vtiger_contactdetails WHERE email={$email}<br />";
            if ($adb->num_rows($res) > 0)
                foreach ($res as $k => $v) 
                {
                    $contactId = $v['contactid'];
                    if(!$this->IsContactExchangeEnabled($contactId))
                    {
                        echo "<strong>Contact exists, but does not have Sync To Outlook Enabled (contactID: {$contactId}</strong><br />";
                        continue;
                    }

                    echo "<strong>EMAIL EXISTS, INSERTING UP TO DATE INFO AS WELL AS EXCHANGE ID INFO</strong><br />";

                    $query = "UPDATE vtiger_contactdetails cd
                                      LEFT JOIN vtiger_contactaddress a ON a.contactaddressid = cd.contactid
                                      LEFT JOIN vtiger_contactscf cf ON cd.contactid = cf.contactid
                                      LEFT JOIN vtiger_contactsubdetails cs ON cs.contactsubscriptionid = cd.contactid
                                      SET cd.firstname = '{$firstname}', cd.lastname = '{$lastname}', cs.birthday = '{$birthday}', cd.title = '{$title}',
                                      cf.{$employerName} = '{$cf_642}', cf.contact_exchange_change_key = '{$newChangeKey}', cf.contact_exchange_item_id = '{$contact->getField('ItemId.Id')}', 
                                      cf.{$employerWebsite} = '{$cf_643}', cd.email = '{$email}', 
                                      cf.{$employerPhone} = '{$cf_644}', cd.mobile = '{$mobile}', cd.phone = '{$phone}', cd.fax = '{$phone_fax}', a.mailingstreet = '{$street_address}',
                                      a.mailingcity = '{$city}', a.mailingstate = '{$state_or_province}', a.mailingzip = '{$postal_code}', cf.{$employerStreet1} = '{$cf_645}',
                                      cf.{$employerCity} = '{$cf_647}', cf.{$employerState} = '{$cf_646}', cf.{$employerPostal} = '{$cf_648}'
                                      WHERE cd.contactid = {$contactId}";

                    echo "QUERY IS: {$query}<br />";
                    echo "Change Key Is: {$newChangeKey}<br />";

                    $adb->pquery($query, array());


                }
        }
    }

    function updateExchangeContact($contactId,$folderId,$folderIsDistinguished) {
        echo "updateExchangeContact called with contact ID {$contactId}...<br />";

        global $adb;

        $contact = $adb->pquery("SELECT * from vtiger_contactdetails cd
                                      LEFT JOIN vtiger_contactaddress a ON a.contactaddressid = cd.contactid
                                      LEFT JOIN vtiger_contactscf cf ON cd.contactid = cf.contactid
                                      LEFT JOIN vtiger_contactsubdetails cs ON cs.contactsubscriptionid = cd.contactid
                                      WHERE cd.contactid = ?", array($contactId));
        $contact = $adb->fetch_array($contact);

        if (!is_array($contact) && count($contact) > 0)
        {
            echo "[Contact Issue...]<br />";
            return FALSE;
        }

        if (strlen($contact['contact_exchange_item_id']) > 0) { //Already exists in exchange... try update
            $itemId = new ItemIdType();
            $itemId->Id        = $contact['contact_exchange_item_id'];
            $itemId->ChangeKey = $contact['contact_exchange_change_key'];

            $exchangeContact = new ContactItemType();
            foreach ($this->fieldMap['Contact'] as $exField => $crmField) {
                $exchangeContact->setField($exField,$contact[$crmField]);
            }

            $exchangeContact->setField('FileAs',"$contact[firstname] $contact[lastname]"); //default for now

            $UpdateItem = new UpdateItemType();
            $UpdateItem->MessageDisposition = "SaveOnly";
            $UpdateItem->ConflictResolution = "AutoResolve";
            $UpdateItem->addItemChange($itemId,$exchangeContact);

            $res = $this->client->UpdateItem($UpdateItem);

            if ($res && $res->ResponseMessages->UpdateItemResponseMessage[0]->ResponseClass == 'Success')
            {
                echo("[Contacts] updated contactid $contactId in exchange\n");
                $changeKey    = $res->ResponseMessages->UpdateItemResponseMessage[0]->Items->Contact[0]->ItemId->ChangeKey;

                $adb->pquery("UPDATE vtiger_contactscf cf
                                   LEFT JOIN vtiger_crmentity ent ON cf.contactid = ent.crmid
                                   SET cf.contact_exchange_change_key = ?, ent.modifiedtime = if(ent.modifiedtime > UTC_TIMESTAMP(), UTC_TIMESTAMP(), ent.modifiedtime) 
                                   where cf.contactid = ?", array($changeKey,$contact['contactid']));
                echo "UPDATE vtiger_contactscf cf
                                   LEFT JOIN vtiger_crmentity ent ON cf.contactid = ent.crmid
                                   SET cf.contact_exchange_change_key = {$changeKey}, ent.modifiedtime = if(ent.modifiedtime > UTC_TIMESTAMP(), UTC_TIMESTAMP(), ent.modifiedtime) 
                                   where cf.contactid = {$contact['contactid']}";
                echo "<br />Change Key Just Assigned At Update Is: {$changeKey}<br />";
                return $changeKey;
            }
            else {
                echo("UPDATE CONTACT FAIL: " . print_r($res,TRUE));
            }
        }
        else 
        { //add new contact to exchange
            echo "<strong>NOT ADDING CONTACT TO EXCHANGE</strong><br />";
        }
    }


    function updateCrmTask($task)
    {
        global $dao, $userId, $adb;

        $res = $adb->pquery("SELECT activityid, task_exchange_change_key FROM vtiger_activitycf where task_exchange_item_id = ?",array($task->getField('ItemId.Id')));
        echo "SELECT activityid, task_exchange_change_key FROM vtiger_activitycf where task_exchange_item_id = " . $task->getField('ItemId.Id') . "<br />";
        $res = $adb->fetch_array($res);

        foreach ($this->fieldMap['Task'] as $exField => $crmField) 
        {
                $fieldValue = $task->getField($exField);
                echo "Field Value..." . $exField . " = " . $fieldValue . "<br />";

                $objs['Task'][$crmField]=$fieldValue;
        }

        $timeend = $objs['Task']['time_end'];

        $timeInfo = $this->ConvertTimezone($objs['Task']['date_start'], $objs['Task']['due_date']);
        if(!$timeInfo)
        {
            echo "ERROR CONVERTIME TIME ZONE OR RETURNED DATE IS OLDER THAN 1 YEAR...";
            return 0;
        }
        $startdate = $timeInfo['startDate'] . " " . $timeInfo['startTime'];

        if ( is_array($res) && count($res) > 0) 
        {
            $taskId    = $res[0];
            $curChangeKey = $res[1];
            $newChangeKey = $task->getField('ItemId.ChangeKey');

            echo "CUR CHANGE KEY = {$curChangeKey}, NEW CHANGE KEY = {$newChangeKey}<br />";
            if ($curChangeKey == $newChangeKey) { //The change key in the database matches the one from exchange.  Skip the update
                echo("[Task] Skipping update task_id=$taskId -- Exchange_item_id = '{$task->getField('ItemId.Id')}'\n");
                return NULL; //not updated, don't return ID
            }

            //Fetch existing objects
            echo "Task Already Exists In vTiger, Updating..." . $objs['Contact'] . "<br />";

    //        $startdate = $dateStart . " " . $timeStart;
            $exchange_id = $task->getField('ItemId.Id');
            $exchange_changekey = $task->getField('ItemId.ChangeKey');

    //        echo "TIME START PARSED = {$timeStartParsed['minute']}<br />";
            echo "TIME START: {$timeStart} " . $timeInfo['startTime'] . "<br />";
            echo "TIME END: {$timeEnd} " . $timeInfo['endTime'] . "<br />";
            echo "DATE START: ". $timeInfo['startDate'] . "<br />";
            echo "DATE END: {$dateEnd} " . $timeInfo['endDate'] . "<br />";
            echo "EXCHANGE ID: {$exchange_id}<br />";
            echo "EXCHANGE_CHANGEKEY: {$exchange_changekey}<br />";

            $createdBy = $userId;

            $subject = $objs['Task']['subject'];
            $description = $objs['Task']['description'];
            $contactId = $objs['Task']['contact_id'];

            $description = strip_tags($description);
            $subject = strip_tags($subject);

            $adb->pquery("UPDATE vtiger_crmentity 
                               SET description = ?, modifiedtime = UTC_TIMESTAMP()    
                               WHERE crmid=?", array($description, $taskId));
            echo "UPDATE vtiger_crmentity 
                               SET description = {$description}, modifiedtime = UTC_TIMESTAMP()
                               WHERE crmid={$taskId}, CUR CHANGE KEY = {$curChangeKey}<br />";//UTC_TIMESTAMP WAS {$startdate}
            $adb->pquery("UPDATE vtiger_activitycf 
                               SET task_exchange_change_key = ?
                               WHERE activityid = ?", array($exchange_changekey, $taskId));
            $adb->pquery("UPDATE vtiger_activity_reminder_popup 
                               SET date_start = ?, time_start = ? 
                               WHERE recordid = ?", array($timeInfo['startDate'], $timeInfo['startTime'], $taskId));
            $adb->pquery("UPDATE vtiger_activity 
                               SET subject = ?, date_start = ?, due_date = ?, time_start = ?, time_end = ?
                               WHERE activityid = ?", array($subject, $timeInfo['startDate'], $timeInfo['endDate'], 
                                                          $timeInfo['startTime'], $timeInfo['endTime'], $taskId));
        }
        else
        {
            echo "Task does not exist yet in vTiger<br />";

            $subject = $objs['Task']['subject'];
            $description = $objs['Task']['description'];
            $contactId = $objs['Task']['contact_id'];

            $exchange_id = $task->getField('ItemId.Id');
            $exchange_changekey = $task->getField('ItemId.ChangeKey');

            echo "TIME START: {$timeStart}<br />";
            echo "TIME END: {$timeEnd}<br />";
            echo "DATE START: {$dateStart}<br />";
            echo "DATE END: {$dateEnd}<br />";
            echo "EXCHANGE ID: {$exchange_id}<br />";
            echo "EXCHANGE_CHANGEKEY: {$exchange_changekey}<br />";

            $createdBy = $userId;

            $adb->pquery("UPDATE vtiger_crmentity_seq SET id=id+1", null);//Update the entity sequence
            $result = $adb->pquery("SELECT id FROM vtiger_crmentity_seq", null);
            $seq = 0;
            foreach($result as $r)
                list($seq) = $r;

            $description = strip_tags($description);
            $subject = strip_tags($subject);

            $adb->pquery("INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, description, createdtime, modifiedtime, version, presence, deleted)
                                  VALUES ({$seq}, '{$createdBy}', '{$createdBy}', '0', 'Calendar', '{$description}', '{$startdate}', '{$startdate}', 0, 1, 0)", null);
            $adb->pquery("INSERT INTO vtiger_activitycf (activityid, task_exchange_item_id, task_exchange_change_key) 
                                  VALUES ({$seq}, '{$exchange_id}', '{$exchange_changekey}')", null);
            $adb->pquery("INSERT INTO vtiger_activity_reminder_popup (semodule, recordid, date_start, time_start, status)
                                  VALUES ('Calendar', {$seq}, '{$timeInfo['startDate']}', '{$timeInfo['startTime']}', 1)", null);
            $adb->pquery("INSERT INTO vtiger_activity (activityid, subject, activitytype, date_start, due_date, time_start, time_end, status, priority)
                                  VALUES (?, ?, 'Task', ?, ?, ?, ?, 'In Progress', 'Low')", array($seq, $subject, $timeInfo['startDate'], 
                                  $timeInfo['endDate'], $timeInfo['startTime'], $timeInfo['endTime']));//'{$status}', '{$priority}')");

            $taskId = $seq;
        }

        return $taskId;
    }    

    function updateExchangeTask($taskId,$folderId,$folderIsDistinguished) {
        echo "Update Exchange Task Called for taskID {$taskId}...<br />";
        global $adb;

        $task = $adb->pquery("SELECT * from vtiger_activity a
                                      LEFT JOIN vtiger_activitycf acf ON a.activityid = acf.activityid
                                      LEFT JOIN vtiger_crmentity crm ON crm.crmid = acf.activityid
                                      WHERE a.activityid = {$taskId} AND crm.deleted = 0", null);
        if($task)                              
            $task = $adb->fetch_array($task);

        if (!is_array($task) || count($task) <= 0)
            return FALSE;


        list($year,$month,$day) = explode('-',$task['time_end']);
        if ($year && $month && $day) {
            $completed_date = mktime(0,0,0,$month,$day,$year);
            if ($completed_date > time()) //completed date is in the future
                $task['time_end'] = date('Y-m-d');
        }

        if (strlen($task['task_exchange_item_id']) > 0) { //Already exists in exchange... try update
            echo "ALREADY EXISTS IN EXCHANGE...UPDATING EXCHANGE WITH VCRM DATA<br />";

            $itemId = new ItemIdType();
            $itemId->Id        = $task['task_exchange_item_id'];
            $itemId->ChangeKey = $task['task_exchange_change_key'];

            $exchangeTask = new TaskType();
            foreach ($this->fieldMap['Task'] as $exField => $crmField) {
                $exchangeTask->setField($exField,$task[$crmField]);
            }

            $UpdateItem = new UpdateItemType();
            $UpdateItem->MessageDisposition = "SaveOnly";
            $UpdateItem->ConflictResolution = "AutoResolve";
            $UpdateItem->addItemChange($itemId,$exchangeTask);

            $res = $this->client->UpdateItem($UpdateItem);

            if ($res && $res->ResponseMessages->UpdateItemResponseMessage[0]->ResponseClass == 'Success')
            {
                echo("[Task] update task_id $taskId in exchange\n");
                $changeKey    = $res->ResponseMessages->UpdateItemResponseMessage[0]->Items->Task[0]->ItemId->ChangeKey;
                $adb->pquery("UPDATE vtiger_activitycf cf 
                              LEFT JOIN vtiger_crmentity e on cf.activityid = e.crmid
                              SET cf.task_exchange_change_key = '{$changeKey}', e.modified_date = if (e.modified_date > UTC_TIMESTAMP(), UTC_TIMESTAMP(), e.modified_date) where task_id = '{$taskId}'",null);
                return $changeKey;
            }
            else { 
                echo("TASK UPDATE FAIL: " . print_r($res,TRUE));
            }        
        }
        else { //add new task to exchange
            echo "NEW TASK CREATED IN VTIGER, CREATING IN EXCHANGE<br />";
            $CreateItem = new CreateItemType();
            if ($folderIsDistinguished)
                $CreateItem->SavedItemFolderId->DistinguishedFolderId->Id = $folderId;
            else
                $CreateItem->SavedItemFolderId->FolderId->Id = $folderId;
            $CreateItem->Items->Task = new TaskType();

            foreach ($this->fieldMap['Task'] as $exchField => $crmField) {
                    echo "FIELD: {$exchField} - {$task[$crmField]}<br />";
                    $CreateItem->Items->Task->setField($exchField,$task[$crmField]);
            }
            $res = $this->client->CreateItem($CreateItem);

            if ($res && $res->ResponseMessages->CreateItemResponseMessage[0]->ResponseClass == 'Success')
            {
                echo("[Task] created task_id $taskId in exchange\n");
                $itemId       = $res->ResponseMessages->CreateItemResponseMessage[0]->Items->Task[0]->ItemId->Id;
                $changeKey    = $res->ResponseMessages->CreateItemResponseMessage[0]->Items->Task[0]->ItemId->ChangeKey;

                $adb->pquery("UPDATE vtiger_activitycf cf 
                                   JOIN vtiger_crmentity e ON cf.activityid = e.crmid
                                   SET cf.task_exchange_item_id = '{$itemId}', cf.task_exchange_change_key = '{$changeKey}', e.modifiedtime = UTC_TIMESTAMP()
                                   WHERE cf.activityid = {$taskId}", null);
                echo "UPDATE vtiger_activitycf cf 
                                   JOIN vtiger_crmentity e ON cf.activityid = e.crmid
                                   SET cf.task_exchange_item_id = '{$itemId}', cf.task_exchange_change_key = '{$changeKey}', e.modifiedtime = UTC_TIMESTAMP()
                                   WHERE cf.activityid = {$taskId}";
                return $changeKey;
            }
            else {
                echo("TASK ADD FAIL:" . print_r($res,TRUE));
            }
        }  
    }


    function updateCrmCalendar($meeting)
    {
        global $userId, $adb;

        echo "ENTERED VTIGER MEETINGS<br />";

        $type = $meeting->getField("CalendarItemType");
        if ($type == 'Exception' || $type == 'Occurrence') {
            echo("SKIPPING calendar $type\n");
            return FALSE;
        }

        $res = $adb->pquery("SELECT e.smownerid, e.crmid, acf.task_exchange_change_key 
                             FROM vtiger_activitycf acf
                             LEFT JOIN vtiger_crmentity e ON e.crmid = acf.activityid
                             WHERE acf.task_exchange_item_id = ?",array($meeting->getField('ItemId.Id')));

        $objs = array();
        $meetingInfo = array();
        $meetingUserId = 0;
        $meetingId = 0;
        $curChangeKey = 0;
        $newChangeKey = 0;
        if($adb->num_rows($res) > 0)//Meeting exists in vTiger, update it
        {
            $meetingUserId = $adb->query_result($res, 0, "smownerid");
            $meetingId    = $adb->query_result($res, 0, "crmid");
            $curChangeKey = $adb->query_result($res, 0, "task_exchange_change_key");
            $newChangeKey = $meeting->getField("ItemId.ChangeKey");

            echo "CURRENT CHANGE KEY = {$curChangeKey}<br />";
            echo "NEW CHANGE KEY = {$newChangeKey}<br />";

            if ($curChangeKey == $newChangeKey) { //The change key in the database matches the one from exchange.  Skip the update
                echo("[Calendar] Skipping update activity_id={$meetingId}\n<br />");
                return NULL; //not updated, don't return ID
            }
        }

        foreach ($this->fieldMap['CalendarItem'] as $exField => $crmField)
        {
            $fieldValue = $meeting->getField($exField);
    ////        echo "FIELD {$crmField}, VALUE = {$fieldValue}<br />";
            $meetingInfo[$crmField] = $fieldValue;
        }

        $timeInfo = $this->ConvertTimezone($meetingInfo['date_start'], $meetingInfo['due_date']);
        if(!$timeInfo)
        {
            echo "ERROR CONVERTIME TIME ZONE OR RETURNED DATE IS OLDER THAN 1 YEAR...";
            return 0;
        }

        $meetingInfo['description'] = strip_tags($meetingInfo['description']);

        if ($meetingId > 0) 
        { //update
            $adb->pquery("UPDATE vtiger_crmentity e
                          JOIN vtiger_activitycf acf ON acf.activityid = e.crmid
                          JOIN vtiger_activity a ON a.activityid = e.crmid
                          SET acf.task_exchange_change_key = ?, a.subject = ?, e.description = ?, e.modifiedtime = UTC_TIMESTAMP(), a.date_start = ?, a.due_date = ?,
                          a.time_start = ?, a.time_end = ?, a.location = ?
                          WHERE e.crmid = ?", array($newChangeKey, $meetingInfo['subject'], $meetingInfo['description'], $timeInfo["startDate"], $timeInfo["endDate"], 
                                                    $timeInfo["startTime"], $timeInfo["endTime"], $location, $meetingId));//UTC_TIMESTAMP() used to be NOW()

            echo ("[Calendar] Updated activity ID {$meetingId}\n");
        }
        else {

            echo "NEEDS CREATED IN VTIGER<br />";

            $exchange_id = $meeting->getField('ItemId.Id');
            $exchange_changekey = $meeting->getField('ItemId.ChangeKey');

            $desc = $meetingInfo['description'];
            $desc = strip_tags($desc);
            $subject = $meetingInfo['subject'];
            $location = $meetingInfo['location'];

            $meetingUserId = $userId;

            $adb->pquery("UPDATE vtiger_crmentity_seq SET id=id+1", null);//Update the entity sequence
            $result = $adb->pquery("SELECT id FROM vtiger_crmentity_seq", null);
            $seq = $adb->query_result($result, 0, "id");

            $adb->pquery("INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, description, createdtime, modifiedtime, version, presence, deleted)
                                  VALUES (?, ?, ?, ?, 'Calendar', ?, UTC_TIMESTAMP(), UTC_TIMESTAMP(), 0, 1, 0)", array($seq, $meetingUserId, $meetingUserId, $meetingUserId, $desc));
            $adb->pquery("INSERT INTO vtiger_activitycf (activityid, task_exchange_item_id, task_exchange_change_key)
                          VALUES (?, ?, ?)", array($seq, $exchange_id, $exchange_changekey));
            $adb->pquery("INSERT INTO vtiger_activity_reminder_popup (semodule, recordid, date_start, time_start, status)
                          VALUES ('Calendar', ?, ?, ?, 1)", array($seq, $timeInfo['startDate'], $timeInfo['startTime']));
            if(!$subject)
                $subject = "No Subject";
            $adb->pquery("INSERT INTO vtiger_activity (activityid, subject, activitytype, date_start, due_date, time_start, time_end, status, priority, location)
                          VALUES (?, ?, 'Meeting', ?, ?, ?, ?, 'Planned', 'Low', ?)", array($seq, $subject, $timeInfo['startDate'], $timeInfo['endDate'], 
                          $timeInfo['startTime'], $timeInfo['endTime'], $location));

            echo "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, description, createdtime, modifiedtime, version, presence, deleted)
                                      VALUES ('{$seq}', '{$meetingUserId}', '{$meetingUserId}', '{$meetingUserId}', '{$desc}', 'Calendar', ?, UTC_TIMESTAMP(), UTC_TIMESTAMP(), 0, 1, 0)<br />";

            echo "INSERT INTO vtiger_activity (activityid, subject, activitytype, date_start, due_date, time_start, time_end, status, priority)
                          VALUES ('{$seq}', '{$subject}', 'Meeting', '{$timeInfo['startDate']}', '{$timeInfo['endDate']}', 
                                  '{$timeInfo['startTime']}', '{$timeInfo['endTime']}', 'Planned', 'Low')<br />";

            echo "TIME START = {$timeStart}<br />";
            echo "TIME END = {$timeEnd}<br />";

            $meetingId = $seq;
        }

        return $meetingId;
    }

    function updateExchangeCalendar($meeting,$invitee,$folderId,$folderIsDistinguished) {
        global $adb, $euser;

        $start_date = $meeting["date_start"];
        $end_date = $meeting["due_date"];

        $start_time = $meeting["time_start"];
        $end_time = $meeting["time_end"];

        if ($end_date < $start_date)
            $meeting['due_date'] = $meeting['date_start'];

        list($syear,$smonth,$sday) = explode('-',$meeting['date_start']);
        list($eyear,$emonth,$eday) = explode('-',$meeting['due_date']);

        echo "UPDATING EXCHANGE USING VTIGER DATA<br /><br />";
        echo "START DATE: {$start_date}<br />";
        echo "START YEAR {$syear}, START MONTH {$smonth}, START DAY {$sday}<br />";

        echo "END DATE: {$end_date}<br />";
        echo "END YEAR {$eyear}, END MONTH {$emonth}, END DAY {$eday}<br />";

        echo "START TIME: {$start_time}<br />";
        echo "END TIME: {$end_time}<br />";

        echo "DESCRIPTION = {$meeting['description']}<br />";
        echo "LOCATION = {$meeting['location']}<br />";

        $totalStart = $start_date . " " . $start_time;
        $totalEnd = $end_date . " " . $end_time;

        $meeting["date_start"] = $totalStart;
        $meeting["due_date"] = $totalEnd;

        echo "DATE START IS NOW: " . $meeting["date_start"];

        echo "CURRENT MEETING ID EXCHANGE -- " . $meeting["task_exchange_item_id"] . "<br />";

        if(strlen($meeting["task_exchange_item_id"]) > 0)
        {//Already exists in exchange... try update
            echo "MEETING EXISTS IN EXCHANGE ALREADY...UPDATING<br />";

            $itemId = new ItemIdType();
            $itemId->Id        = $meeting['task_exchange_item_id'];
            $itemId->ChangeKey = $meeting['task_exchange_change_key'];
        //        $itemId->getField("")

            echo "Task Exchange Item Id = {$itemId->Id}<br />";
            echo "Task Exchange Change Key = {$itemId->ChangeKey}<br />";

            $exchangeCalendar = new CalendarItemType();

            foreach ($this->fieldMap['CalendarItem'] as $exField => $crmField) {
                $exchangeCalendar->setField($exField,$meeting[$crmField]);
        }
    /*        if ($recurrence)
                $exchangeCalendar->Recurrence = $recurrence;*/

            if($meeting["entdeleted"] == 0)//If the meeting is not deleted, update it in exchange
            {
                $UpdateItem = new UpdateItemType();
                $UpdateItem->MessageDisposition = "SaveOnly";
                $UpdateItem->ConflictResolution = "AutoResolve";
                $UpdateItem->SendMeetingInvitationsOrCancellations = "SendToNone";
                $UpdateItem->addItemChange($itemId,$exchangeCalendar);

                $res = $this->client->UpdateItem($UpdateItem);

                if ($res && $res->ResponseMessages->UpdateItemResponseMessage[0]->ResponseClass == 'Success')
                {
                    echo ("[Calendar] Updated appointment_id {$meeting['activity_id']} in exchange\n");
                    $changeKey    = $res->ResponseMessages->UpdateItemResponseMessage[0]->Items->CalendarItem[0]->ItemId->ChangeKey;
                    $adb->pquery("UPDATE vtiger_activitycf a
                                  LEFT JOIN vtiger_crmentity e ON a.activityid = e.crmid
                                  SET a.task_exchange_change_key = ?, e.modifiedtime = e.modifiedtime 
                                  WHERE e.smownerid = ?
                                  AND e.crmid = ?",array($changeKey,$meeting['owner'],$meeting['activity_id']));
                    echo "UPDATE vtiger_activitycf a
                          LEFT JOIN vtiger_crmentity e on a.activityid = e.crmid
                          SET a.task_exchange_change_key = {$changeKey}, e.modifiedtime = e.modifiedtime
                          WHERE e.smownerid = {$meeting['owner']}
                          AND e.crmid = {$meeting['activity_id']}<br />";
                    return $changeKey;
                }
                else {
                    echo("CALENDAR UPDATE FAIL: " . print_r($res,TRUE));
                }
            }
            else//The meeting was deleted..Kill it in exchange, and mark it as deleted in vTiger to avoid pointless checks in the future
            {
                echo "DELETING MEETING<br />";
                $deleteItem = new DeleteItemType();

                $itemId = new ItemIdType();
                $itemId->Id = $meeting["task_exchange_item_id"];

                $deleteItem->ItemIds->ItemId = $itemId;
                $deleteItem->DeleteType = "HardDelete";
                $deleteItem->SendMeetingCancellations = "SendToNone";

                $res = $this->client->DeleteItem($deleteItem);

                if ($res && $res->ResponseMessages->DeleteItemResponseMessage[0]->ResponseClass == 'Success')
                {
                    echo ("[Calendar] Delete appointment_id {$meeting['activity_id']} in exchange\n");
                    $changeKey    = $res->ResponseMessages->UpdateItemResponseMessage[0]->Items->CalendarItem[0]->ItemId->ChangeKey;
                    $adb->pquery("UPDATE vtiger_activitycf a
                                  LEFT JOIN vtiger_crmentity e ON a.activityid = e.crmid
                                  SET a.task_exchange_change_key = ?, e.modifiedtime = e.modifiedtime, a.deleted='1'
                                  WHERE e.smownerid = ?
                                  AND e.crmid = ?",array($changeKey,$meeting['owner'], $meeting['activity_id']));
                    echo "UPDATE vtiger_activitycf a
                          LEFT JOIN vtiger_crmentity e on a.activityid = e.crmid
                          SET a.task_exchange_change_key = {$changeKey}, e.modifiedtime = e.modifiedtime, a.deleted='1'
                          WHERE e.smownerid = {$meeting['owner']}<br />";
                    return $changeKey;
                }
                else {
                    echo("CALENDAR DELETE FAIL: " . print_r($res,TRUE));
                }
            }
        }
        else//Doesn't exist in exchange, create it
        {
            $CreateItem = new CreateItemType();
            $CreateItem->SendMeetingInvitations = "SendToNone";
            if ($folderIsDistinguished)
                $CreateItem->SavedItemFolderId->DistinguishedFolderId->Id = $folderId;
            else
                $CreateItem->SavedItemFolderId->FolderId->Id = $folderId;
            $CreateItem->Items->CalendarItem = new CalendarItemType();

            foreach ($this->fieldMap['CalendarItem'] as $exchField => $crmField) {
                echo "FIELD: {$exchField} - {$meeting[$crmField]}<br />";
                $CreateItem->Items->CalendarItem->setField($exchField,$meeting[$crmField]);
            }

            $res = $this->client->CreateItem($CreateItem);

            if ($res && $res->ResponseMessages->CreateItemResponseMessage[0]->ResponseClass == 'Success')
            {
                $itemId       = $res->ResponseMessages->CreateItemResponseMessage[0]->Items->CalendarItem[0]->ItemId->Id;
                $changeKey    = $res->ResponseMessages->CreateItemResponseMessage[0]->Items->CalendarItem[0]->ItemId->ChangeKey;
                echo("[Calendar] Created meeting in exchange\n");
                echo "UPDATE vtiger_activitycf a
                              INNER JOIN vtiger_crmentity e ON a.activityid = e.crmid
                              SET a.task_exchange_item_id = {$itemId}, a.task_exchange_change_key = {$changeKey}, e.modifiedtime = e.modifiedtime
                              WHERE a.activityid = {$meeting["activity_id"]}<br />";

                $adb->pquery("UPDATE vtiger_activitycf a
                              INNER JOIN vtiger_crmentity e ON a.activityid = e.crmid
                              SET a.task_exchange_item_id = ?, a.task_exchange_change_key = ?, e.modifiedtime = e.modifiedtime 
                              WHERE a.activityid = ?",array($itemId, $changeKey, $meeting['activity_id']));
                return $changeKey;

                for($x = 0; $x < sizeof($invitee); $x++)
                {
                    echo "ADDING ADDITIONAL USER ({$invitee[$x]}) TO EXCHANGE MEETING<br />";
                    $res = $adb->pquery("SELECT user_exchange_username FROM vtiger_users WHERE id={$invitee[$x]}", null);
                    $exchange_name = $adb->query_result($res, 0, "user_exchange_username");
                    if($exchange_name)
                    {
                        $this->client->_setImpersonation($exchange_name);
                        $res = $this->client->CreateItem($CreateItem);
                        if ($res && $res->ResponseMessages->CreateItemResponseMessage[0]->ResponseClass == 'Success')
                        {
                            echo "SUCCESSFULLY ADDED {$exchange_name} TO THE MEETING<br />";
                        }
                        else
                            echo("FAILED TO ADD {$exchange_name} TO THE MEETING: " . print_r($this->client->__getLastRequest(),TRUE) . print_r($res, TRUE));
                    }
                }
                $this->client->_setImpersonation($euser);
                return $changeKey;

            }
            else {
                echo("CALENDAR ADD FAIL ($appointmentUserId):" . print_r($this->client->__getLastRequest(),TRUE) .  print_r($res,TRUE));
            }   
        }
    }

    function ConvertTimezone($start, $due)
    {
    ////        echo "CONVERTIME TIME ZONE...";
            $duedate = str_replace("T", " ", $due);
            $duedate = str_replace("Z", "", $duedate);
            $startdate = str_replace("T", " ", $start);
            $startdate = str_replace("Z", "", $startdate);

            $format = "Y-m-d H:i:s";
            if(!$startdate || !$duedate)
                return 0;
            $lastYear = date('Y-m-d',strtotime(date("Y-m-d H:i:s", mktime()) . " - 365 day"));

            $ly = strtotime($lastYear);
            $cmp = strtotime($startdate);

            if($cmp < $ly)
                return 0;

            $start = DateTime::createFromFormat($format, $startdate);  //date($startdate;////DateTimeField::convertTimeZone($startdate, "UTC", "UTC");
            $end = DateTime::createFromFormat($format, $duedate);      //$duedate;////DateTimeField::convertTimeZone($duedate, "UTC", "UTC");

            $timeStart = $start->format("H") . ":" . $start->format("i") . ":" . $start->format("s");
            $dateStart = $start->format("Y") . "-" . $start->format("m") . "-" . $start->format("d");

            $timeEnd = $end->format("H") . ":" . $end->format("i") . ":" . $end->format("s");
            $dateEnd = $end->format("Y") . "-" . $end->format("m") . "-" . $end->format("d");

            $r = array("startDate" => $dateStart,
                       "startTime" => $timeStart,
                       "endDate" => $dateEnd,
                       "endTime" => $timeEnd);

            return $r;
    }

    function getAppointmentRecurrence($appointment) {
        return $recurrence;
    }

    function IsContactExchangeEnabled($contactId)
    {
        global $adb;
        $field = GetFieldNameFromFieldLabel("Sync To Outlook");
        $query = "SELECT {$field} FROM vtiger_contactscf WHERE contactid={$contactId}";
        $result = $adb->pquery($query,array());
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, $field);
        return 0;
    }
}

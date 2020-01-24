<?php

include_once("include/utils/omniscientCustom.php");

class OmniCal_ExchangeContacts_Model extends OmniCal_ExchangeEws_Model{
    public $contact_info;
    
    public function __construct($server = 'lanserver33', $user = 'concertglobal\concertadmin', $password = 'Consec1', $exchange_version = 'Exchange2007_SP1') {
        parent::__construct($server, $user, $password, $exchange_version);
    }
    
    /**
     * Get contact in from exchange.  If no contact id is specified, it will return all contacts
     * @param type $contact_id
     * @return string
     */
    public function GetContactInfoFromExchange($contact_id=''){
        if(!isset($this->sid->PrimarySmtpAddress) && !isset($this->sid->PrincipalName))
            return 'Impersonation needs to be set';
        
        if(strlen($contact_id) > 0)
            return $this->GetContactInfo($exchange_id);
        else
            return $this->GetAllContactsFromExchange();
    }
    
    private function GetContactInfo($exchange_id){
        if($exchange_id){
            $request = new EWSType_GetItemType();

            $request->ItemShape = new EWSType_ItemResponseShapeType();
            $request->ItemShape->BaseShape = EWSType_DefaultShapeNamesType::ALL_PROPERTIES;

            $request->ItemIds = new EWSType_NonEmptyArrayOfBaseItemIdsType();
            $request->ItemIds->ItemId = new EWSType_ItemIdType();
            $request->ItemIds->ItemId->Id = $exchange_id;

            $response = $this->ews->GetItem($request);   
            return $response;
        }
        return 0;
    }
    
    /**
     * Get all contacts from exchange
     * @return type
     */
    private function GetAllContactsFromExchange(){
        return $this->GetAllContactIDsFromExchangeBySyncState();
/*
        $request = new EWSType_FindItemType();

        $request->ItemShape = new EWSType_ItemResponseShapeType();
        $request->ItemShape->BaseShape = EWSType_DefaultShapeNamesType::ALL_PROPERTIES;

        $request->ContactsView = new EWSType_ContactsViewType();
        $request->ContactsView->InitialName = 'a';
        $request->ContactsView->FinalName = 'z';

        $request->ParentFolderIds = new EWSType_NonEmptyArrayOfBaseFolderIdsType();
        $request->ParentFolderIds->DistinguishedFolderId = new EWSType_DistinguishedFolderIdType();
        $request->ParentFolderIds->DistinguishedFolderId->Id = EWSType_DistinguishedFolderIdNameType::CONTACTS;

        $request->Traversal = EWSType_ItemQueryTraversalType::SHALLOW;

        $response = $this->ews->FindItem($request);
        return $response;
 */
    }
    
    private function PhoneType($type, $phone){
        $ph = new EWSType_PhoneNumberDictionaryEntryType();
        $ph->Key = new EWSType_PhoneNumberKeyType();
        $ph->Key->_ = constant("EWSType_PhoneNumberKeyType::$type");
        $ph->_ = $phone;
        
        return $ph;
    }
    
    private function AddressType($type, $address_info){        
        $address = new EWSType_PhysicalAddressDictionaryEntryType();
        $address->Key = new EWSType_PhysicalAddressKeyType();
        $address->Key->_ = constant("EWSType_PhysicalAddressKeyType::$type");
        $address->Street = $address_info['Street'];
        $address->City = $address_info['City'];
        $address->State = $address_info['State'];
        $address->PostalCode = $address_info['PostalCode'];
        $address->CountryOrRegion = $address_info['CountryOrRegion'];

        return $address;
    }
    
    private function EmailType($type, $email_address){
        $em = new EWSType_EmailAddressDictionaryEntryType();;
        $em->Key = new EWSType_EmailAddressKeyType();
        $em->Key->_ = constant("EWSType_EmailAddressKeyType::$type");
        $em->_ = $email_address;

        return $em;
    }
    
    public function UpdateCRMExchangeIDAndChangeKey($record, $id, $changekey){
        global $adb;
        $query = "UPDATE vtiger_contactscf 
                  SET contact_exchange_item_id = ?, 
                  contact_exchange_change_key = ?
                  WHERE contactid = ?";
        $adb->pquery($query, array($id, $changekey, $record));

    }
    
    public function CreateContactInExchange($firstname, $lastname, $title=null, $assistant=null, $nickname=null, $birthday=null, $emails=null, $phones=null, $addresses=null){
        $request = new EWSType_CreateItemType();
        $contact = new EWSType_ContactItemType();
        
        $request->SendMeetingInvitations = 'SendToAllAndSaveCopy';
        $request->SavedItemFolderId->DistinguishedFolderId->Id = EWSType_DistinguishedFolderIdNameType::CONTACTS;
        
        $contact->GivenName = $firstname;
        $contact->Surname = $lastname;
        $contact->JobTitle = $title;
        $contact->AssistantName = $assistant;
		
		if($birthday != '' && $birthday != 'null')
			$contact->Birthday = $birthday . 'T09:30:10Z';//,'2009-02-02T09:30:10Z';//$birthday;
			
        $contact->Nickname = $nickname;
        
        if(sizeof($phones) > 0){
            $contact->PhoneNumbers = new EWSType_PhoneNumberDictionaryType();
            foreach($phones AS $k => $v){
                $contact->PhoneNumbers->Entry[] = $this->PhoneType($k, $v);
            }
        }
        
        $contact->PhysicalAddresses = new EWSType_PhysicalAddressDictionaryType();
        foreach($addresses AS $k => $v){
            $contact->PhysicalAddresses->Entry[] = $this->AddressType($k, $v);
        }
        
        if(sizeof($emails) > 0){
            $contact->EmailAddresses = new EWSType_EmailAddressDictionaryType();
            foreach($emails AS $k => $v){
                $contact->EmailAddresses->Entry[] = $this->EmailType($k, $v);
            }
        }

        $request->Items->Contact[] = $contact;        
        $response = $this->ews->CreateItem($request);		

        return $response;
    }
    
    public function UpdateContactInExchange($id, $ckey, $firstname, $lastname, $title=null, $assistant=null, $nickname=null, $birthday=null, $emails=null, 
                                            $phones=null, $addresses=null){
		$updates = array(
                'contacts:GivenName'                  => $firstname,
                'contacts:Surname'                    => $lastname,
                'contacts:JobTitle'                   => $title,
                'contacts:AssistantName'              => $assistant,
                'contacts:Nickname'                   => $nickname);

		/* if($birthday != '' && $birthday != 'null')
			$updates['contacts:Birthday'] = $birthday . 'T09:30:10Z';
		 */
        $request = new EWSType_UpdateItemType();

        $request->SendMeetingInvitationsOrCancellations = EWSType_CalendarItemUpdateOperationType::SEND_TO_ALL_AND_SAVE_COPY;
        $request->MessageDisposition = 'SaveOnly';
        $request->ConflictResolution = 'AlwaysOverwrite';
        $request->ItemChanges = new EWSType_NonEmptyArrayOfItemChangesType();

        $request->ItemChanges->ItemChange->ItemId->Id = $id;
        $request->ItemChanges->ItemChange->ItemId->ChangeKey = $ckey;
        $request->ItemChanges->ItemChange->Updates = new EWSType_NonEmptyArrayOfItemChangeDescriptionsType();
        
        //populate update array
        $n = 0;
        $request->ItemChanges->ItemChange->Updates->SetItemField = array();
        foreach($updates as $furi => $update){
            if($update){//To save the repition of doing this for every field, we do it in this loop otherwise.  This is no different than doing the same thing as if($body) below
                $prop = array_pop(explode(':',$furi));
                $request->ItemChanges->ItemChange->Updates->SetItemField[$n]->FieldURI->FieldURI = $furi;
                $request->ItemChanges->ItemChange->Updates->SetItemField[$n]->Contact->$prop = $update;
                $n++;
            }
        }
        
        if(sizeof($emails) > 0){
            foreach($emails AS $k => $v){
                $field = new EWSType_SetItemFieldType();//Create the field
                $field->IndexedFieldURI->FieldURI = 'contacts:EmailAddress';//Set the info of the field
                $field->IndexedFieldURI->FieldIndex = constant("EWSType_EmailAddressKeyType::$k");
                $field->Contact->EmailAddresses->Entry = $this->EmailType($k, $v);//Get the field type as created by EmailType
                $request->ItemChanges->ItemChange->Updates->SetItemField[$n] = $field;//Attach it to our sending object
                $n++;
            }
        }
        
        if(sizeof($phones) > 0){
            foreach($phones AS $k => $v){
                $field = new EWSType_SetItemFieldType();//Create the field
                $field->IndexedFieldURI->FieldURI = 'contacts:PhoneNumber';//Set the info of the field
                $field->IndexedFieldURI->FieldIndex = constant("EWSType_PhoneNumberKeyType::$k");
                $field->Contact->PhoneNumbers->Entry = $this->PhoneType($k, $v);//Get the field type as created by EmailType
                $request->ItemChanges->ItemChange->Updates->SetItemField[$n] = $field;//Attach it to our sending object
                $n++;
            }
        }
        
        foreach($addresses AS $k => $v){
            foreach($v AS $type => $value){
                $field = new EWSType_SetItemFieldType();
                $field->IndexedFieldURI->FieldURI =  "contacts:PhysicalAddress:{$type}";
                $field->IndexedFieldURI->FieldIndex = constant("EWSType_PhysicalAddressKeyType::$k");

                $field->Contact = new EWSType_ContactItemType();
                $field->Contact->PhysicalAddresses = new EWSType_PhysicalAddressDictionaryType();
                $address = new EWSType_PhysicalAddressDictionaryEntryType();
                $address->Key = constant("EWSType_PhysicalAddressKeyType::$k");

                $field->Contact->PhysicalAddresses->Entry = $address;
                $field->Contact->PhysicalAddresses->Entry->$type = $value;

                $request->ItemChanges->ItemChange->Updates->SetItemField[$n] = $field;
    //            $field->Contact->PhysicalAddresses->Entry = $this->AddressType($k, $v);
    //            $request->ItemChanges->ItemChange->Updates->SetItemField[$n] = $field;//Attach it to our sending object
                $n++;
            }
        }

        $n++;

        $response = $this->ews->UpdateItem($request);
		
		return $response;	    
    }
    
    public function GetAllContactIDsFromExchangeBySyncState(){
        try{
            $sync_state = OmniCal_CRMExchangeHandler_Model::GetSyncInfo($this->user_id, "Contact");
            $request = new EWSType_SyncFolderItemsType;

            $request->SyncState = $sync_state[0]['state'];
            $request->MaxChangesReturned = 512;
            $request->ItemShape = new EWSType_ItemResponseShapeType;
            $request->ItemShape->BaseShape = EWSType_DefaultShapeNamesType::ID_ONLY;

            $request->SyncFolderId = new EWSType_NonEmptyArrayOfBaseFolderIdsType;
            $request->SyncFolderId->DistinguishedFolderId = new EWSType_DistinguishedFolderIdType;
            $request->SyncFolderId->DistinguishedFolderId->Id = EWSType_DistinguishedFolderIdNameType::CONTACTS;

            $response = $this->ews->SyncFolderItems($request);
            $new_sync_state = $response->ResponseMessages->SyncFolderItemsResponseMessage->SyncState;
            $changes = $response->ResponseMessages->SyncFolderItemsResponseMessage->Changes;

            OmniCal_CRMExchangeHandler_Model::UpdateSyncState($sync_state[0]['table_id'], $new_sync_state);
            $response = array();

            if(property_exists($changes, 'Create')) {
                foreach($changes->Create as $contact) {
                    if($contact->Contact->ItemId){
                        $id = $contact->Contact->ItemId->Id;
                        $change_key = $contact->Contact->ItemId->ChangeKey;
                    } else{
                        $id = $contact->ItemId->Id;
                        $change_key = $contact->ItemId->ChangeKey;
                    }
                    $tmp = array('id'=>$id,
                                 'changekey'=>$change_key);
                    $response['create'][] = $tmp;
                }
            }

            // updated events
            if(property_exists($changes, 'Update')) {
                foreach($changes->Update as $contact) {
                    if($contact->Contact->ItemId){
                        $id = $contact->Contact->ItemId->Id;
                        $change_key = $contact->Contact->ItemId->ChangeKey;
                    } else{
                        $id = $contact->ItemId->Id;
                        $change_key = $contact->ItemId->ChangeKey;
                    }
                    $tmp = array('id'=>$id,
                                 'changekey'=>$change_key);
                    if(OmniCal_ExchangeBridge_Model::DoesContactExist($id))
                        $response['update'][] = $tmp;
                    else
                        $response['create'][] = $tmp;
                }
            }

            // deleted events
            if(property_exists($changes, 'Delete')) {
                foreach($changes->Delete as $contact) {
                    if($contact->Contact->ItemId){
                        $id = $contact->Contact->ItemId->Id;
                        $change_key = $contact->Contact->ItemId->ChangeKey;
                    } else if($contact->ItemId->Id){
                        $id = $contact->ItemId->Id;
                        $change_key = $contact->ItemId->ChangeKey;
                    }
                    else{
                        $id = $contact->Id;
                        $change_key = $contact->ChangeKey;
                    }
                    $tmp = array('id'=>$id,
                                 'changekey'=>$change_key);
                    $response['delete'][] = $tmp;
                }
            }

            return $response;
        }
        catch (Exception $ex){
            return 0;
        }
    }
    
    /**
     * Auto create, update, delete CRM
     * @param type $event_info
     */
    public function AutoCreateUpdateDeleteCRMWithContactInfo($contact_info){
        foreach($contact_info AS $action => $action_values){
            switch($action){
                case "create":
                    foreach($action_values AS $k => $v){
						$contact = $this->GetContactInfo($v['id']);
						$data = $this->RequestToData($contact);
						if(is_array($data))
							$this->CreateContactInCRM($data);
                    }
                    break;
                case "update":
                    foreach($action_values AS $k => $v){
                        $record = OmniCal_ExchangeBridge_Model::DoesContactExist($v['id']);
                        if($record && !OmniCal_ExchangeBridge_Model::DoChangeKeysMatch($record, $v['changekey'])){//Make sure the item exists and the change key's don't match
                            $contact = $this->GetContactInfo($v['id']);
							$data = $this->RequestToData($contact);
                            $this->UpdateContactInCRM($record, $data);
                        }
                    }
                    break;
            }
        }
    }
    
    /**
     * Create the contact in the crm.  This function actually "updates" seeing as we don't want to create every contact.
     * @param array $data
     * @return type
     */
    public function CreateContactInCRM($data){
        $contactid = $this->DoesEmailExistInCRM($data['email']);
        if(!$contactid)
            $contactid = OmniCal_ExchangeBridge_Model::DoesContactExist($data['contact_exchange_item_id']);

		if($contactid)
            $this->UpdateContactInCRM($contactid, $data);
        return;
    }

    public function UpdateContactInCRM($record, $updated_data){
        
		if($record){
            
			try{
                
				if(!Omnical_ExchangeBridge_Model::IsContactExchangeEnabled($record)){
                   $this->UpdateCRMExchangeIDAndChangeKey($record, $updated_data['contact_exchange_item_id'], $updated_data['contact_exchange_change_key']);
                    return;
                }
				
                $recordModel = Vtiger_record_Model::getInstanceById($record, 'Contacts');
                
				$data = $recordModel->getData();
				
				/* $birthday = $updated_data['birthday'];
				
				if($birthday){
					$updated_data['birthday'] = getValidDisplayDate($birthday);
				} */
				
                $final_data = array_replace($data, $updated_data);
                
				$_REQUEST['ignore_exchange_update'] = 1;
                
				$recordModel->setData($final_data);
				
				$recordModel->set('mode', 'edit');
                
				$recordModel->save();
            } catch (Exception $ex){
                return;
            }
        }
    }
    
    public function DoesEmailExistInCRM($email){
        global $adb;
        $query = "SELECT contactid 
                  FROM vtiger_contactdetails cd
                  JOIN vtiger_crmentity e ON e.crmid = cd.contactid
                  WHERE email=? AND email != ''
                  AND e.smownerid = ? AND e.deleted = 0";
        $result = $adb->pquery($query, array($email, $this->user_id));
        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, 'contactid');
        }
        return 0;
    }
    
    public function GetExchangeInfoFromContactId($record){
        global $adb;
        $query = "SELECT contact_exchange_item_id, contact_exchange_change_key FROM vtiger_contactscf WHERE contactid = ?";
        $result = $adb->pquery($query, array($record));
        if($adb->num_rows($result) > 0){
            $id = $adb->query_result($result, 0, 'contact_exchange_item_id');
            $ck = $adb->query_result($result, 0, 'contact_exchange_change_key');
            $info = array('id' => $id,
                          'changekey' => $ck);
            return $info;
        }
        return 0;
    }
    
    public function GetEmailInfoFromData($data){
        $emails = array();
        if($data['email'])
            $emails['EMAIL_ADDRESS_1'] = $data['email'];
        if($data['secondaryemail'])
            $emails['EMAIL_ADDRESS_2'] = $data['secondaryemail'];
        
        return $emails;
    }
    
    public function GetPhoneInfoFromData($data){
        if($data['homephone'])
            $phone_info['HOME_PHONE'] = $data['homephone'];
        if($data['phone'])
            $phone_info['BUSINESS_PHONE'] = $data['phone'];
        if($data['mobile'])
            $phone_info['MOBILE_PHONE'] = $data['mobile'];
        if($data['fax'])
            $phone_info['BUSINESS_FAX'] = $data['fax'];

        return $phone_info;
    }
    
    public function GetAddressInfoFromData($data){
        $employerName = GetFieldNameFromFieldLabel("Employer Name");
        $employerPhone = GetFieldNameFromFieldLabel("Employer Phone Number");
        $employerState = GetFieldNameFromFieldLabel("Employer State");
        $employerPostal = GetFieldNameFromFieldLabel("Employer Postal Code");
        $employerWebsite = GetFieldNameFromFieldLabel("Employer Website");
        $employerStreet1 = GetFieldNameFromFieldLabel("Employer Street Address 1");
        $employerStreet2 = GetFieldNameFromFieldLabel("Employer Street Address 2");
        $employerCity = GetFieldNameFromFieldLabel("Employer City");
        $employerFax = GetFieldNameFromFieldLabel("Employer Fax");
        $nickname = GetFieldNameFromFieldLabel("Nickname");
        
        $addresses = array();
        $addresses['HOME'] = array("Street"  => $data['mailingstreet'],
                                   "City"    => $data['mailingcity'],
                                   "State"   => $data['mailingstate'],
                                   "PostalCode"     => $data['mailingzip'],
                                   "CountryOrRegion" => $data['mailingcountry']);
        $addresses['BUSINESS'] = array("Street"  => $data[$employerStreet1] . " " . $data[$employerStreet2],
                                       "City"    => $data[$employerCity],
                                       "State"   => $data[$employerState],
                                       "PostalCode"     => $data[$employerPostal]);
        return $addresses;
    }
    
    static public function RequestToData($response){
        $data = array();
        if($response->ResponseMessages->GetItemResponseMessage->ResponseClass == "Success"){
            self::WipeExchangeRelatedData($data);
            $contact = $response->ResponseMessages->GetItemResponseMessage->Items->Contact;
            $data['contact_exchange_item_id'] = $contact->ItemId->Id;
            $data['contact_exchange_change_key'] = $contact->ItemId->ChangeKey;
            $data['firstname'] = $contact->GivenName;
            $data['lastname'] = $contact->Surname;
            $data['title'] =  $contact->JobTitle;
            $data['assistant'] =  $contact->AssistantName;
            $data['nickname'] = $contact->Nickname;
			
			if(strlen($contact->Birthday) > 2){
                $date = new DateTime($contact->Birthday);
                $d = $date->format('Y-m-d');
                //$data['birthday'] = $d;
            }

            self::FillEmailDataFromResponse($response, $data);
            self::FillAddressesDataFromResponse($response, $data);
            self::FillPhoneDataFromResponse($response, $data);
            return $data;
        }
        return 0;
    }
    
    /**
     * Wipe all exchange related data (lastname, title, assistant, etc) keeping the CRM specific stuff in tact
     * @param type $data
     */
    private function WipeExchangeRelatedData(&$data){
        $employerPhone = GetFieldNameFromFieldLabel("Employer Phone Number");
        $employerState = GetFieldNameFromFieldLabel("Employer State");
        $employerPostal = GetFieldNameFromFieldLabel("Employer Postal Code");
        $employerStreet1 = GetFieldNameFromFieldLabel("Employer Street Address 1");
        $employerStreet2 = GetFieldNameFromFieldLabel("Employer Street Address 2");
        $employerCity = GetFieldNameFromFieldLabel("Employer City");

        $data['contact_exchange_item_id'] = '';
        $data['contact_exchange_change_key'] = '';
        $data['firstname'] = '';
        $data['lastname'] = '';
        $data['title'] =  '';
        $data['assistant'] =  '';
        $data['nickname'] = '';
        $data['mailingstreet'] = '';
        $data['mailingcity'] = '';
        $data['mailingstate'] = '';
        $data['mailingzip'] = '';
        $data['mailingcountry'] = '';
        $data[$employerStreet1] = '';
        $data[$employerStreet2] = '';
        $data[$employerCity] = '';
        $data[$employerState] = '';
        $data[$employerPostal] = '';
        $data['homephone'] = '';
        $data['phone'] = '';
        $data['mobile'] = '';
        $data['fax'] = '';
    }
    
    private function FillPhoneDataFromResponse($response, &$data){
        $contact = $response->ResponseMessages->GetItemResponseMessage->Items->Contact;
        if($contact->PhoneNumbers){
            if(is_array($contact->PhoneNumbers->Entry)){
                foreach($contact->PhoneNumbers->Entry AS $k => $v){
                    if($v->Key == "HomePhone"){
                        $data['homephone'] = $v->_;
                    }
                    if($v->Key == "BusinessPhone"){
                        $data['phone'] = $v->_;
                    }
                    if($v->Key == "MobilePhone"){
                        $data['mobile'] = $v->_;
                    }
                    if($v->Key == "BusinessFax"){
                        $data['fax'] = $v->_;
                    }
                }
            } else{
                if($contact->PhoneNumbers->Entry->Key == "HomePhone"){
                    $data['homephone'] = $contact->PhoneNumbers->Entry->_;
                }
                if($contact->PhoneNumbers->Entry->Key == "BusinessPhone"){
                    $data['phone'] = $contact->PhoneNumbers->Entry->_;
                }
                if($contact->PhoneNumbers->Entry->Key == "MobilePhone"){
                    $data['mobile'] = $contact->PhoneNumbers->Entry->_;
                }
                if($contact->PhoneNumbers->Entry->Key == "BusinessFax"){
                    $data['fax'] = $contact->PhoneNumbers->Entry->_;
                }
            }
        }
    }
    
    /**
     * Fill the data array with address information.  Basically used to clean up the RequestToData function
     * @param type $response
     * @param type $data
     */    
    private function FillAddressesDataFromResponse($response, &$data){
        $employerPhone = GetFieldNameFromFieldLabel("Employer Phone Number");
        $employerState = GetFieldNameFromFieldLabel("Employer State");
        $employerPostal = GetFieldNameFromFieldLabel("Employer Postal Code");
        $employerStreet1 = GetFieldNameFromFieldLabel("Employer Street Address 1");
        $employerStreet2 = GetFieldNameFromFieldLabel("Employer Street Address 2");
        $employerCity = GetFieldNameFromFieldLabel("Employer City");
        
        $contact = $response->ResponseMessages->GetItemResponseMessage->Items->Contact;
        if($contact->PhysicalAddresses){
            if(is_array($contact->PhysicalAddresses->Entry)){
                foreach($contact->PhysicalAddresses->Entry AS $k => $v){
                    if($v->Key == "Home"){
                        $data['mailingstreet'] = $v->Street;
                        $data['mailingcity'] = $v->City;
                        $data['mailingstate'] = $v->State;
                        $data['mailingzip'] = $v->PostalCode;
                        $data['mailingcountry'] = $v->CountryOrRegion;
                    }
                    if($v->Key == "Business"){
                        $data[$employerStreet1] = $v->Street;
                        $data[$employerCity] = $v->City;
                        $data[$employerState] = $v->State;
                        $data[$employerPostal] = $v->PostalCode;
                    }
                }
            } else{
                if($contact->PhysicalAddresses->Entry->Key == "Home"){
                    $data['mailingstreet'] = $contact->PhysicalAddresses->Entry->Street;
                    $data['mailingcity'] = $contact->PhysicalAddresses->Entry->City;
                    $data['mailingstate'] = $contact->PhysicalAddresses->Entry->State;
                    $data['mailingzip'] = $contact->PhysicalAddresses->Entry->PostalCode;
                    $data['mailingcountry'] = $contact->PhysicalAddresses->Entry->CountryOrRegion;                    
                }
                if($contact->PhysicalAddresses->Entry->Key == "Business"){
                    $data[$employerStreet1] = $contact->PhysicalAddresses->Entry->Street;
                    $data[$employerCity] = $contact->PhysicalAddresses->Entry->City;
                    $data[$employerState] = $contact->PhysicalAddresses->Entry->State;
                    $data[$employerPostal] = $contact->PhysicalAddresses->Entry->PostalCode;                    
                }
            }
        }
    }
    
    /**
     * Fill the data array with email information.  Basically used to clean up the RequestToData function
     * @param type $response
     * @param type $data
     */
    private function FillEmailDataFromResponse($response, &$data){
        $contact = $response->ResponseMessages->GetItemResponseMessage->Items->Contact;
        if($contact->EmailAddresses){
            if(is_array($contact->EmailAddresses->Entry)){
                foreach($contact->EmailAddresses->Entry AS $k => $v){
                    if($v->Key == "EmailAddress1")
                        $data['email'] = $v->_;
                    if($v->Key == "EmailAddress2")
                        $data['secondaryemail'] = $v->_;
                }
            } else{
                $data['email'] = $contact->EmailAddresses->Entry->_;
            }
        }
    }
}

?>
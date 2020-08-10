<?php

class Office365_Contacts_Connector extends Office365_Base_Connector{
    
    protected $totalRecords;
    
    protected $createdRecords;
    
    protected $mappingFields = array();
    
    protected $indexedPagingOffset = 0;
    
    public function __construct($user) {
    	$this->user = $user;
    	
    	$mappingFields = Office365_Utils_Helper::getFieldMappingDetails($user, 'Contacts');
    	
    	$this->mappingFields = $mappingFields = array_reduce($mappingFields, function ($result, $item) {
    	    $result[$item['CRM']] = $item['Office365'];
    	    return $result;
    	}, array());
    }
    
	public function getName() {
		return 'Office365Contacts';
	}
    
    /**
     * Pull the contacts from office365
     * @return <array> office Records
     */
    public function pull($user = false) {
        return $this->getContacts($this->user);
    }


    /**
     * Pull the contacts from Office
     * @param <object> $SyncState
     * @return <array> office Records
     */
    public function getContacts($user = false) {
        
    	$lastUpdatedTime = false;
    	
    	if (Office365_Utils_Helper::getSyncTime('Contacts')) {
    	    $lastUpdatedTime = Office365_Utils_Helper::getSyncTime('Contacts');
        	$lastUpdatedTime = date("Y-m-d\TH:i:s.000\Z", strtotime($lastUpdatedTime));
        }
        
        $response = array();
        
        $contacts = $this->getExchangeRecords($lastUpdatedTime);     
        
		$exchangeRecords = array();
        
		$exchange_modified_time = array();
        
		foreach ($contacts as $exchangeContact) {
        	
		    $recordModel = Office365_Contacts_Model::getInstanceFromValues(array('entity' => $exchangeContact));
            
		    $recordModel->setType($this->getSynchronizeController()->getSourceType())->setMode(Office365_SyncRecord_Model::UPDATE_MODE);
		    
		    $itemId = $exchangeContact->getId();
		    
		    $exchangeRecords[$itemId] = $recordModel;
            
		    $exchange_modified_time[] = $exchangeContact->getLastModifiedDateTime()->format('Y-m-d H:i:s');
        }
        
        $last_modified_time = date("Y-m-d H:i:s",max(array_map('strtotime',$exchange_modified_time)));
        
        $this->createdRecords = count($exchangeRecords);
        
        if (isset($last_modified_time)&& !empty($exchange_modified_time)){
            Office365_Utils_Helper::updateSyncTime('Contacts', $last_modified_time, $user);
        } else {
            Office365_Utils_Helper::updateSyncTime('Contacts', false, $user);
        }
    
        return $exchangeRecords;
    }

    /**
     * Push the vtiger records to office
     * @param <array> $records vtiger records to be pushed to office
     * @return <array> pushed records
     */
    public function push($allRecords) {
        
        $VTERecords = array();
        
        foreach ($allRecords as $mode => $records) {
            
            if($mode == Office365_SyncRecord_Model::UPDATE_MODE){
                
                $exchangeRecords = array();
                
                foreach($records as $record){
                    
                    $entity = $record->getEntityData();
                    
                    $exchangeRecords[$entity['id']] = $entity;
                }
        
                $exchangeRecordss = array_chunk($exchangeRecords, 200, true);
                
                foreach($exchangeRecordss as $exchangeRecords){
                    
                    $updateResponse = $this->updateMSExchangeContacts($exchangeRecords);
                    
                    if(!empty($updateResponse)){
                        
                        foreach($updateResponse as $updateItemResponse){
                            
                            $contactItem = $updateItemResponse->getItems()->getContact();
                            
                            $itemId = $contactItem->getItemId();
                            
                            $records[$itemId->getId()]->set("entity", $itemId)->set("exchangeResponse", true);
                        }
                        
                        $VTERecords = $VTERecords + $records;
                    }
                
                }
                
            } else if ($mode == Office365_SyncRecord_Model::CREATE_MODE) {
                
                foreach($records as $record){
                    
                    $entity = $record->getEntityData();
                    
                    $newEntity = $this->addExchangeRecord($entity);
                    
                    $record->set('entity', $newEntity);
                    
                    $record->set("exchangeResponse", true);
                    
                    $VTERecords[] = $record;
                }
            } else if($mode == Office365_SyncRecord_Model::DELETE_MODE){
                
                $deleteItems = array();
                
                foreach($records as $record){
                    
                    $entity = $record->getEntityData();
                    
                    $record->set("exchangeResponse", true);
                    
                    $deleteItems[] = array("Id" => $entity['id']);
                }
                
                if(!empty($deleteItems)){
                    
                    $response = $this->deleteMSExchangeContacts($deleteItems);
                    
                    if($response){
                        $VTERecords = $VTERecords + $records;
                    }
                }
            }
        }
        
        return $VTERecords;
    }

    /**
     * Transform  Vtiger Records to Office Records
     * @param <array> $vtRecords 
     * @return <array> tranformed vtiger Records
     */
    public function transformToTargetRecord($vtRecords) {
    	
    	$records = array();
    	
    	$updateContacts = array();
    	
        foreach ($vtRecords as $vtRecord) {
            
            if($vtRecord->get('_id') != '' && $vtRecord->getMode() == Office365_SyncRecord_Model::UPDATE_MODE){
                $updateContacts[$vtRecord->get('_id')] = $vtRecord;
                continue;
            }
            
            $contactData = $vtRecord->getData();
            
            $exchangeContact = $this->getMSExchangeContactData($contactData, $vtRecord->getMode());
            
            $recordModel = Office365_Contacts_Model::getInstanceFromValues(array('entity' => $exchangeContact));
            $recordModel->setType($this->getSynchronizeController()->getSourceType())->setMode($vtRecord->getMode())->setSyncIdentificationKey($vtRecord->get('_syncidentificationkey'));
            $recordModel = $this->performBasicTransformations($vtRecord, $recordModel);
            $recordModel = $this->performBasicTransformationsToTargetRecords($recordModel, $vtRecord);
            $records[$vtRecord->getMode()][] = $recordModel;
        }
        
        if(!empty($updateContacts)){
            
            $exchangeContactsDetails = array();
            
            $syncController = $this->getSynchronizeController();
            
            $MSExchangeModel = $syncController->getMSExchangeModel();
            
            $exchange_contact_ids = array_keys($updateContacts);
            
            $exchange_contact_ids = array_chunk($exchange_contact_ids, 250);
            
            if(!empty($exchange_contact_ids)){
                
                foreach($exchange_contact_ids as $index => $exchange_contact_chunk_ids){
                    
                    $exchangeContactsChunkDetails = $MSExchangeModel->getItems($exchange_contact_chunk_ids);
                    
                    if(is_object($exchangeContactsChunkDetails)){
                        $exchangeContactsChunkDetails = array($exchangeContactsChunkDetails);
                    }
                    
                    $exchangeContactsDetails = array_merge($exchangeContactsDetails, array_filter($exchangeContactsChunkDetails));
                }
            }
            
            if(!empty($exchangeContactsDetails)){
                
                foreach($exchangeContactsDetails as $exchangeContact){
                    
                    $itemId = $exchangeContact->getItemId()->getId();
                    
                    $vtContact = $updateContacts[$itemId];
                    
                    $exchangeContact = $this->getMSExchangeContactData($vtContact->getData(), $vtContact->getMode(), $exchangeContact);
                    
                    $recordModel = Office365_Contacts_Model::getInstanceFromValues(array('entity' => $exchangeContact));
                    $recordModel->setType($this->getSynchronizeController()->getSourceType())->setMode($vtContact->getMode())->setSyncIdentificationKey($vtContact->get('_syncidentificationkey'));
                    $recordModel = $this->performBasicTransformations($vtContact, $recordModel);
                    $recordModel = $this->performBasicTransformationsToTargetRecords($recordModel, $vtContact);
                    $records[$vtContact->getMode()][$itemId] = $recordModel;
                }
            }
        }
        
        return $records;
    }
    
     /**
     * Transform Office Records to Vtiger Records
     * @param <array> $targetRecords 
     * @return <array> tranformed Office Records
     */
    public function transformToSourceRecord($targetRecords, $user = false) {
        
        $contacts = array();
        
        if(!$user)
            $user = Users_Record_Model::getCurrentUserModel();
        
        foreach ($targetRecords as $exchangeRecord) {
            
            $entity = array();
            
            if ($exchangeRecord->getMode() != Office365_SyncRecord_Model::DELETE_MODE) {
                
                $entity = $this->getContactEntityData($exchangeRecord, $user);
                
                $entity['assigned_user_id'] = vtws_getWebserviceEntityId('Users', $user->id);
                
                if (empty($entity['lastname'])) {
                    if (!empty($entity['firstname'])) {
                        $entity['lastname'] = $entity['firstname'];
                    } else if(empty($entity['firstname']) && !empty($entity['email'])) { 
                        $entity['lastname'] = $entity['email']; 
                    } else if( !empty($entity['mobile']) || !empty($entity['mailingstreet'])) { 
                        $entity['lastname'] = 'Office365 Contact'; 
                    }
               }
               
               if (empty($entity['lastname'])) {
               		continue;
               }
            }
            
            $contact = $this->getSynchronizeController()->getSourceRecordModel($entity);
			$contact = $this->performBasicTransformations($exchangeRecord, $contact);			
			$contact = $this->performBasicTransformationsToSourceRecords($contact, $exchangeRecord);
            
            $contacts[] = $contact;
        }
        
        return $contacts;
    }
    
   
    
    public function getExchangeRecords($lastUpdatedTime){
    	
        $syncController = $this->getSynchronizeController();
            
        $MSExchangeModel = $syncController->getMSExchangeModel();
        
        $response = $MSExchangeModel->getContacts($lastUpdatedTime, $this->indexedPagingOffset);
        
        return $response;
    }
    
    
	public function getExchangeDataById($contactid){
	    
	    $syncController = $this->getSynchronizeController();
	    
	    $MSExchangeModel = $syncController->getMSExchangeModel();
	    
	    $response = $MSExchangeModel->getContactById($contactid);
	        	
    	return $response;
    }
    
	public function addExchangeRecord($data){
		
	    $syncController = $this->getSynchronizeController();
	    
	    $MSExchangeModel = $syncController->getMSExchangeModel();
	    
	    $response = $MSExchangeModel->createContact($data);
	    
		return $response;
	}
    
	public function updateExchangeRecord($itemId, $data){

	    $syncController = $this->getSynchronizeController();
	    
	    $MSExchangeModel = $syncController->getMSExchangeModel();
	    
	    unset($data['id']);
	    
	    $response = $MSExchangeModel->updateContact($itemId, $data);
	    
		return $response;
    }

    /**
     * returns if more records exits or not
     * @return <boolean> true or false
     */
    public function moreRecordsExits() {
        return ($this->totalRecords - $this->createdRecords > 0) ? true : false;
    }
    
 	function MSExchangeFormat($date) {
        $datTime = new DateTime($date);
        $timeZone = new DateTimeZone('UTC');
        $datTime->setTimezone($timeZone);
        $googleFormat = $datTime->format('Y-m-d\TH:i:s\Z');
        return $googleFormat;
    }

    function getExchangeContactData($exchangeRecord){
        
        $entity = new Office365_OfficeContacts_Model();
        
        $entity->setExchangeId($exchangeRecord->getItemId());
        
        $entity->setFirstName($exchangeRecord->getGivenName());
        
        $entity->setLastName($exchangeRecord->getSurname());
        
        $entity->setTitle($exchangeRecord->getJobTitle());
        
        $entity->setCompanyName($exchangeRecord->getCompanyName());
        
        $entity->setBirthday($exchangeRecord->getBirthday());
        
        $entity->setEmails($exchangeRecord->getEmailAddresses());
        
        $entity->setDescription($exchangeRecord->getNotes());
        
        $recordPhones = $exchangeRecord->getPhoneNumbers();
        
        if(!empty($recordPhones)){
            
            $recordPhones = $recordPhones->Entry;
            
            if(is_array($recordPhones) && !empty($recordPhones)){
                
                foreach($recordPhones as $recordPhone){
                    
                    $type = $recordPhone->getKey();
                    
                    if($type == 'MobilePhone'){
                        $entity->setMobile($recordPhone->_);
                    } else if($type == 'BusinessPhone'){
                        $entity->setBusinessPhone($recordPhone->_);
                    } else if($type == 'HomePhone'){
                        $entity->setHomePhone($recordPhone->_);
                    }
                }
            } else {
                
                $type = $recordPhones->getKey();
                
                if($type == 'MobilePhone'){
                    $entity->setMobile($recordPhones->_);
                } else if($type == 'BusinessPhone'){
                    $entity->setBusinessPhone($recordPhones->_);
                } else if($type == 'HomePhone'){
                    $entity->setHomePhone($recordPhones->_);
                }
            }
        }
        
        $physical_address = $exchangeRecord->getPhysicalAddresses();
        
        if(!empty($physical_address)){
            
            $physical_address = $physical_address->Entry;
            
            $address = array();
            
            if(is_array($physical_address) && !empty($physical_address)){
                
                foreach($physical_address as $address){
                    
                    $type = $address->getKey();
                    
                    if($type == 'Home'){
                        $entity->setHomeAddress(
                            $address->getStreet(),
                            $address->getCity(),
                            $address->getState(),
                            $address->getCountryOrRegion(),
                            $address->getPostalCode()
                        );
                    } else if($type == 'Business'){
                        $entity->setBusinessAddress(
                            $address->getStreet(),
                            $address->getCity(),
                            $address->getState(),
                            $address->getCountryOrRegion(),
                            $address->getPostalCode()
                        );
                    }
                }
            } else {
                
                $type = $physical_address->getKey();
                
                if($type == 'Home'){
                    $entity->setHomeAddress(
                        $physical_address->getStreet(),
                        $physical_address->getCity(),
                        $physical_address->getState(),
                        $physical_address->getCountryOrRegion(),
                        $physical_address->getPostalCode()
                    );
                } else if($type == 'Business'){
                    $entity->setBusinessAddress(
                        $physical_address->getStreet(),
                        $physical_address->getCity(),
                        $physical_address->getState(),
                        $physical_address->getCountryOrRegion(),
                        $physical_address->getPostalCode()
                    );
                }
            }
        }
        
        return $entity;
    }
    
    function getContactEntityData($exchangeRecord, $user, $fetchAccountName = false){
        
        $mappingFields = $this->mappingFields;
         
        $homeAddress = $exchangeRecord->getHomeAddress();
        
        $businessAddress = $exchangeRecord->getBusinessAddress();
        
        $otherAddress = $exchangeRecord->getOtherAddress();
        
        $entity = array();
        
        foreach($mappingFields as $dbFName => $exchangeFName){
            
            switch ($exchangeFName){
                
                case 'First Name':
                    $entity[$dbFName] = $exchangeRecord->getFirstName();
                    break;
                case 'Last Name':
                    $entity[$dbFName] = $exchangeRecord->getLastName();
                    break;
                case 'Email':
                    $entity[$dbFName] = $exchangeRecord->getEmailAddress1();
                    break;
                case 'Job Title':
                    $entity[$dbFName] = $exchangeRecord->getTitle();
                    break;
                case 'Department':
                    $entity[$dbFName] = $exchangeRecord->getDepartment();
                    break;
                case 'Business Phone':
                    $entity[$dbFName] = $exchangeRecord->getBusinessPhone();
                    break;
                case 'Home Phone':
                    $entity[$dbFName] = $exchangeRecord->getHomePhone();
                    break;
                case 'Mobile Phone':
                    $entity[$dbFName] = $exchangeRecord->getMobilePhone();
                    break;
                case 'Assistant':
                    $entity[$dbFName] = $exchangeRecord->getAssistant();
                    break;
                case 'Company':
                    $entity[$dbFName] = $exchangeRecord->getAccountName($user->getId(), $fetchAccountName);
                    break;
                case 'Business_Street':
                    $entity[$dbFName] = $businessAddress['street'];
                    break;
                case 'Business_City':
                    $entity[$dbFName] = $businessAddress['city'];
                    break;
                case 'Business_State/Province':
                    $entity[$dbFName] = $businessAddress['state'];
                    break;
                case 'Business_ZIP/Postal code':
                    $entity[$dbFName] = $businessAddress['postalCode'];
                    break;
                case 'Business_Country/Region':
                    $entity[$dbFName] = $businessAddress['countryOrRegion'];
                    break;
                case 'Other_Street':
                    $entity[$dbFName] = $otherAddress['street'];
                    break;
                case 'Other_City':
                    $entity[$dbFName] = $otherAddress['city'];
                    break;
                case 'Other_State/Province':
                    $entity[$dbFName] = $otherAddress['state'];
                    break;
                case 'Other_ZIP/Postal code':
                    $entity[$dbFName] = $otherAddress['postalCode'];
                    break;
                case 'Other_Country/Region':
                    $entity[$dbFName] = $otherAddress['countryOrRegion'];
                    break;
                case 'Home_Street':
                    $entity[$dbFName] = $homeAddress['street'];
                    break;
                case 'Home_City':
                    $entity[$dbFName] = $homeAddress['city'];
                    break;
                case 'Home_State/Province':
                    $entity[$dbFName] = $homeAddress['state'];
                    break;
                case 'Home_ZIP/Postal code':
                    $entity[$dbFName] = $homeAddress['postalCode'];
                    break;
                case 'Home_Country/Region':
                    $entity[$dbFName] = $homeAddress['countryOrRegion'];
                    break;
                case 'Notes':
                    $entity[$dbFName] = $exchangeRecord->getDescription();
                    break;
                case "Birthday":
                    $entity[$dbFName] = $exchangeRecord->getBirthday();
                    break;
                    
            }
        }
        
        return $entity;
    }
    
    function getMSExchangeContactData($vtData, $mode, $exchangeObject = false ){
        
        $mappingFields = $this->mappingFields;
        
        $newRecord = new Office365_OfficeContacts_Model();
        
        $user = Users_Record_Model::getCurrentUserModel();
            
        if(isset($vtData['_id']) && !empty($vtData['_id'])){
            
            $newRecord->setId($vtData['_id']);
            
            if($mode == Office365_SyncRecord_Model::UPDATE_MODE) {
                
                $allData = $exchangeObject;
                
                $newRecord->setExchangeId($allData->getItemId());
                
                $exchangeContactdata = $this->getContactEntityData(new Office365_Contacts_Model(array("entity" => $allData)), $user, true);
                
                $vtData = array_merge($exchangeContactdata, $vtData);
            } 
        }
        
        $lastName = $vtData['lastname'];
        
        $firstName = $vtData['firstname'];
        
        if ( $firstName == '') {
            if ( $lastName != '') {
                $firstName = $lastName;
            } else if( $firstName == '' && $vtData['email'] != '') {
                $firstName = $vtData['email'];
            } else {
                $firstName = 'CRM Contact';
            }
        }
        
        $newRecord->setLastName($lastName);
        
        $newRecord->setFirstName($firstName);
        
        $newRecord->setDisplayName($firstName."-".$lastName);
        
        $emails = array();
        
        if($vtData['email'] != ''){
            $emails[] = $vtData['email'];
        }
        
        if( !empty($emails)){
            $newRecord->setEmails($emails, true);
        }
           
        $businessAddress = $otherAddress = $homeAddress = array();
        
        foreach($mappingFields as $dbFName => $exchangeFName){
            
            $fieldValue = $vtData[$dbFName];
            
            switch ($exchangeFName){
                
                case 'Job Title':
                    $newRecord->setTitle($fieldValue);
                    break;
                case 'Department':
                    $newRecord->setDepartment($fieldValue);
                    break;
                case 'Business Phone':
                    $newRecord->setBusinessPhone($fieldValue);
                    break;
                case 'Home Phone':
                    $newRecord->setHomePhone($fieldValue);
                    break;
                case 'Mobile Phone':
                    $newRecord->setMobile($fieldValue);
                    break;
                case 'Assistant':
                    $newRecord->setAssistantName($fieldValue);
                    break;
                case 'Company':
                    $newRecord->setCompanyName($fieldValue);
                    break;
                case 'Business_Street':
                    $businessAddress['street'] = $fieldValue;
                    break;
                case 'Business_City':
                    $businessAddress['city'] = $fieldValue;
                    break;
                case 'Business_State/Province':
                    $businessAddress['state'] = $fieldValue;
                    break;
                case 'Business_ZIP/Postal code':
                    $businessAddress['postalCode'] = $fieldValue;
                    break;
                case 'Business_Country/Region':
                    $businessAddress['countryOrRegion'] = $fieldValue;
                    break;
                case 'Other_Street':
                    $otherAddress['street'] = $fieldValue;
                    break;
                case 'Other_City':
                    $otherAddress['city'] = $fieldValue;
                    break;
                case 'Other_State/Province':
                    $otherAddress['state'] = $fieldValue;
                    break;
                case 'Other_ZIP/Postal code':
                    $otherAddress['postalCode'] = $fieldValue;
                    break;
                case 'Other_Country/Region':
                    $otherAddress['countryOrRegion'] = $fieldValue;
                    break;
                case 'Home_Street':
                    $homeAddress['street'] = $fieldValue;
                    break;
                case 'Home_City':
                    $homeAddress['city'] = $fieldValue;
                    break;
                case 'Home_State/Province':
                    $homeAddress['state'] = $fieldValue;
                    break;
                case 'Home_ZIP/Postal code':
                    $homeAddress['postalCode'] = $fieldValue;
                    break;
                case 'Home_Country/Region':
                    $homeAddress['countryOrRegion'] = $fieldValue;
                    break;
                case 'Notes':
                    $newRecord->setDescription($fieldValue);
                    break;
                case "Birthday":
                    if($fieldValue)
                        $newRecord->setBirthday($fieldValue);
                    break;
            }
        }
        
        if ( !empty(array_filter($businessAddress))) {
            $newRecord->setBusinessAddress($businessAddress['street'], $businessAddress['city'], $businessAddress['state'], $businessAddress['countryOrRegion'], $businessAddress['postalCode']);
        }
        if ( !empty(array_filter($homeAddress))) {
            $newRecord->setHomeAddress($homeAddress['street'], $homeAddress['city'], $homeAddress['state'], $homeAddress['countryOrRegion'], $homeAddress['postalCode']);
        }
        if ( !empty(array_filter($otherAddress))) {
            $newRecord->setOtherAddress($otherAddress['street'], $otherAddress['city'], $otherAddress['state'], $otherAddress['countryOrRegion'], $otherAddress['postalCode']);
        }
        
        return $newRecord->getData();
    }
    
    function updateMSExchangeContacts($records){
        
        $syncController = $this->getSynchronizeController();
        
        $MSExchangeModel = $syncController->getMSExchangeModel();
        
        $response = $MSExchangeModel->updateContactItems($records);
        
        return $response;
    }
    
    function deleteMSExchangeContacts($records){
        
        $syncController = $this->getSynchronizeController();
        
        $MSExchangeModel = $syncController->getMSExchangeModel();
        
        $response = $MSExchangeModel->deleteItems($records);
        
        return $response;
    }
}
?>


<?php

class MSExchange_Contacts_Model extends MSExchange_SyncRecord_Model {
    
    /**
     * return id of MSExchange Record
     * @return <string> id
     */
    public function getId() {
        
        if($this->get("exchangeResponse") == true)
            return $this->data['entity']->getId();
        else
            return $this->data['entity']->getItemId()->getId(); 
    }
    
    function getEntityData(){
    	if(isset($this->data['entity'])){
    		return $this->data['entity'];
    	}
    	return array();
    }

    /**
     * return modified time of MSExchange Record
     * @return <date> modified time 
     */
    public function getModifiedTime() {
        
        $reflect = new ReflectionClass($this->data['entity']);
        
        if ($reflect->getShortName() === 'SyncFolderItemsDeleteType') {
            return false;
        }
        
        if($this->get("exchangeResponse") == true)
            return date('Y-m-d H:i:s');
        else    
            return date('Y-m-d H:i:s',strtotime($this->data['entity']->getLastModifiedTime())); 
    } 


    function getInitials() {
        $val = $this->data['entity']->getInitials(); 
        return $val;
    }
    

    /**
     * return first name of MSExchange Record
     * @return <string> $first name
     */
    function getFirstName() {
        $fname = $this->data['entity']->getGivenName(); 
        return $fname;
    }

    /**
     * return Lastname of MSExchange Record
     * @return <string> Last name
     */
    function getLastName() {
        $lname = $this->data['entity']->getSurname(); 
        return $lname;
    }

    /**
     * return Emails of MSExchange Record
     * @return <array> emails
     */
    function getEmails() {
        $emails = $this->data['entity']->getEmailAddresses(); 
        if(!empty($emails))
            return $emails->Entry;
        else
            return $emails;
    }
    
    function getEmailAddress1(){
        
        $emails = $this->getEmails();
        
        if( !empty($emails) ){
            
            if(is_array($emails)){
                return $emails[0]->__toString();
            } else {
                return $emails->__toString();
            }
        }
        return $emails;
    }

    function getEmailAddress2(){
        
        $emails = $this->getEmails();
        
        $email2 = "";
        
        if( !empty($emails) ){
            
            if(is_array($emails) && !empty($emails)){
                
                for( $k=1; $k < count($emails); $k++ ){
                    $email2 = $emails[$k]->__toString();
                    break;
                }
            } 
        }
        return $email2;
    }
    /**
     * return Phone number of MSExchange Record
     * @return <array> phone numbers
     */
    function getPhones() {

        $phones = array();

        $phones['mobilePhone'] = $this->getMobilePhone(); 
        $phones['businessPhones'] = $this->getBusinessPhone(); 
        $phones['homePhones'] = $this->getHomePhone(); 
        
        return $phones; 
    }
    
 	function getMobilePhone() {
 	    
 	    $recordPhones = $this->data['entity']->getPhoneNumbers();
 	    
 	    if(!empty($recordPhones)){
 	      
 	        $recordPhones = $recordPhones->Entry;
 	      
            if(is_array($recordPhones) && !empty($recordPhones)){
                
                foreach($recordPhones as $recordPhone){
                    
                    $type = $recordPhone->getKey();
                    
                    if($type == 'MobilePhone'){
                        $value = $recordPhone->_;
                        break;
                    }
                }
            } else {
                
                $type = $recordPhones->getKey();
                
                if($type == 'MobilePhone')
                    $value = $recordPhones->_;
            }
            
            return $value;
 	    }
 	    
 	    return $recordPhones;
    }
    
 	function getBusinessPhone() {
 	    
 	    $recordPhones = $this->data['entity']->getPhoneNumbers();
 	    
 	    if(!empty($recordPhones)){
 	        
 	        $recordPhones = $recordPhones->Entry;
 	        
 	        if(is_array($recordPhones) && !empty($recordPhones)){
 	            
 	            foreach($recordPhones as $recordPhone){
 	                
 	                $type = $recordPhone->getKey();
 	                
 	                if($type == 'BusinessPhone'){
 	                    $value = $recordPhone->_;
 	                    break;
 	                }
 	            }
 	        } else {
 	            
 	            $type = $recordPhones->getKey();
 	            
 	            if($type == 'BusinessPhone')
 	                $value = $recordPhones->_;
 	        }
 	        
 	        return $value;
 	    }
 	    
 	    return $recordPhones;
    }
    
 	function getHomePhone() {
 	    
 	    $recordPhones = $this->data['entity']->getPhoneNumbers();
 	    
 	    if(!empty($recordPhones)){
 	        
 	        $recordPhones = $recordPhones->Entry;
 	        
 	        if(is_array($recordPhones) && !empty($recordPhones)){
 	            
 	            foreach($recordPhones as $recordPhone){
 	                
 	                $type = $recordPhone->getKey();
 	                
 	                if($type == 'HomePhone'){
 	                    $value = $recordPhone->_;
 	                    break;
 	                }
 	            }
 	        } else {
 	            
 	            $type = $recordPhones->getKey();
 	            
 	            if($type == 'HomePhone')
 	                $value = $recordPhones->_;
 	        }
 	        
 	        return $value;
 	    }
 	    
 	    return $recordPhones;
    }

    /**
     * return Addresss of MSExchange Record
     * @return <array> Addresses
     */
    function getAddresses() {
        
        $addresses = array();
        
        $addresses['homeAddress'] = $this->getHomeAddress();
        $addresses['businessAddress'] = $this->getBusinessAddress();
        $addresses['otherAddress'] = $this->getOtherAddress();
        
        return $addresses;
    }
    
    
	function getHomeAddress() {
	    
	    $physical_address = $this->data['entity']->getPhysicalAddresses();
	    
	    if(!empty($physical_address)){
	    
	        $physical_address = $physical_address->Entry;
	        
	        $homeAddress = array();
	        
	        if(is_array($physical_address) && !empty($physical_address)){
    	        
	            foreach($physical_address as $address){
	                
	                $type = $address->getKey();
	                
	                if($type == 'Home'){
	                    
	                    $homeAddress = array(
	                        'street' => $address->getStreet(),
	                        'city' => $address->getCity(),
	                        'state' => $address->getState(),
	                        'countryOrRegion' => $address->getCountryOrRegion(),
	                        'postalCode' => $address->getPostalCode()
	                    );
	                    break;
	                }
    	        }
	        } else {
	            
	            $type = $physical_address->getKey();
	            
	            if($type == 'Home'){
	                
	                $homeAddress = array(
	                    'street' => $physical_address->getStreet(),
	                    'city' => $physical_address->getCity(),
	                    'state' => $physical_address->getState(),
	                    'countryOrRegion' => $physical_address->getCountryOrRegion(),
	                    'postalCode' => $physical_address->getPostalCode()
	                );
	            }
	        }
	        
	        return $homeAddress;
	    }
	    
	    return $physical_address;
	}
	
	function  getBusinessAddress(){
	    
	    $physical_address = $this->data['entity']->getPhysicalAddresses();
	    
	    if(!empty($physical_address)){
	        
	        $physical_address = $physical_address->Entry;
	        
	        $businessAddress = array();
	        
	        if(is_array($physical_address) && !empty($physical_address)){
	            
	            foreach($physical_address as $address){
	                
	                $type = $address->getKey();
	                
	                if($type == 'Business'){
	                    
	                    $businessAddress = array(
	                        'street' => $address->getStreet(),
	                        'city' => $address->getCity(),
	                        'state' => $address->getState(),
	                        'countryOrRegion' => $address->getCountryOrRegion(),
	                        'postalCode' => $address->getPostalCode()
	                    );
	                    break;
	                }
	            }
	        } else {
	            
	            $type = $physical_address->getKey();
	            
	            if($type == 'Business'){
	                
	                $businessAddress = array(
	                    'street' => $physical_address->getStreet(),
	                    'city' => $physical_address->getCity(),
	                    'state' => $physical_address->getState(),
	                    'countryOrRegion' => $physical_address->getCountryOrRegion(),
	                    'postalCode' => $physical_address->getPostalCode()
	                );
	            }
	        }
	        
	        return $businessAddress;
	    }
	    
	    return $physical_address;
	}
	
	function getOtherAddress(){
	    
	    $physical_address = $this->data['entity']->getPhysicalAddresses();
	    
	    if(!empty($physical_address)){
	        
	        $physical_address = $physical_address->Entry;
	        
	        $otherAddress = array();
	        
	        if(is_array($physical_address) && !empty($physical_address)){
	            
	            foreach($physical_address as $address){
	                
	                $type = $address->getKey();
	                
	                if($type == 'Other'){
	                    
	                    $otherAddress = array(
	                        'street' => $address->getStreet(),
	                        'city' => $address->getCity(),
	                        'state' => $address->getState(),
	                        'countryOrRegion' => $address->getCountryOrRegion(),
	                        'postalCode' => $address->getPostalCode()
	                    );
	                    break;
	                }
	            }
	        } else {
	            
	            $type = $physical_address->getKey();
	            
	            if($type == 'Other'){
	                
	                $otherAddress = array(
	                    'street' => $physical_address->getStreet(),
	                    'city' => $physical_address->getCity(),
	                    'state' => $physical_address->getState(),
	                    'countryOrRegion' => $physical_address->getCountryOrRegion(),
	                    'postalCode' => $physical_address->getPostalCode()
	                );
	            }
	        }
	        
	        return $otherAddress;
	    }
	    
	    return $physical_address;
    }
    
    function getBirthday() {
        
        $birthday = $this->data['entity']->getBirthday();
        
        if(!empty($birthday)){
            $birthday = date("Y-m-d", strtotime($birthday));
        }
        
        return $birthday;
    }
    
    function getTitle() {
        return $this->data['entity']->getJobTitle(); 
    }
    
    function getAccountName($userId, $returnAccountName = false) {
        
        $description = false;
        
        $orgName = $this->data['entity']->getCompanyName();
        
        if($returnAccountName){
            return $orgName;
        }
        
        if(empty($orgName)) {
            $contactsModel = Vtiger_Module_Model::getInstance('Contacts');
            $accountFieldInstance = Vtiger_Field_Model::getInstance('account_id', $contactsModel);
            if($accountFieldInstance->isMandatory()) {
                $orgName = '????';
                $description = 'This Organization is created to support MSExchange Contacts Synchronization. Since Organization Name is mandatory !';
            }
        }
        if(!empty($orgName)) {
            $db = PearDatabase::getInstance();
            $result = $db->pquery("SELECT crmid FROM vtiger_crmentity WHERE label = ? AND deleted = ? AND setype = ?", array($orgName, 0, 'Accounts'));
            if($db->num_rows($result) < 1) {
                try {
                    $accountModel = Vtiger_Module_Model::getInstance('Accounts');
                    $recordModel = Vtiger_Record_Model::getCleanInstance('Accounts');
                    
                    $fieldInstances = Vtiger_Field_Model::getAllForModule($accountModel);
                    foreach($fieldInstances as $blockInstance) {
                        foreach($blockInstance as $fieldInstance) {
                            $fieldName = $fieldInstance->getName();
                            $fieldValue = $recordModel->get($fieldName);
                            if(empty($fieldValue)) {
                                $defaultValue = $fieldInstance->getDefaultFieldValue();
                                if($defaultValue) {
                                    $recordModel->set($fieldName, decode_html($defaultValue));
                                }
                                if($fieldInstance->isMandatory() && !$defaultValue) {
                                    $randomValue = Vtiger_Util_Helper::getDefaultMandatoryValue($fieldInstance->getFieldDataType());
                                    if($fieldInstance->getFieldDataType() == 'picklist' || $fieldInstance->getFieldDataType() == 'multipicklist') {
                                        $picklistValues = $fieldInstance->getPicklistValues();
                                        $randomValue = reset($picklistValues);
                                    }
                                    $recordModel->set($fieldName, $randomValue);
                                }
                            }
                        }
                    }
                    $recordModel->set('mode', '');
                    $recordModel->set('accountname', $orgName);
                    $recordModel->set('assigned_user_id', $userId);
                    $recordModel->set('source', 'MSExchange');
                    if($description) {
                        $recordModel->set('description', $description);
                    }
                    $recordModel->save();
                    $account_id = $recordModel->getId();
                } catch (Exception $e) {
                    //TODO - Review
                }
            } else{
                $account_id = $db->query_result($result,0,'crmid');
            }
            return vtws_getWebserviceEntityId('Accounts',$account_id);
        }
        return false;
    }
    
    function getDescription() { 
        
        $body = $this->data['entity']->getBody();
        
        if($body)
            return $body->__toString();
        else
            return $body;
    }

    function getDepartment() {
        return $this->data['entity']->getDepartment();
    }
    
    function getAssistant() {
        return $this->data['entity']->getAssistantName();
    }
    
    /**
     * Returns the MSExchange_Contacts_Model of MSExchange Record
     * @param <array> $recordValues
     * @return MSExchange_Contacts_Model
     */
    public static function getInstanceFromValues($recordValues) {
        $model = new MSExchange_Contacts_Model($recordValues);
        return $model;
    }

    /**
     * converts the MSExchange Format date to 
     * @param <date> $date MSExchange Date
     * @return <date> Vtiger date Format
     */
    public function vtigerFormat($date) {
        list($date, $timestring) = explode('T', $date);
        list($time, $tz) = explode('.', $timestring);

        return $date . " " . $time;
    }
    
	public function getSyncIdentificationKey(){
		return $this->data['_syncidentificationkey'];
	}

}

?>

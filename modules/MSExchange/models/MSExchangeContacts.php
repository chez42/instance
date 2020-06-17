<?php
class MSExchange_MSExchangeContacts_Model {
	
	protected $data = array('id' => '');
	
	function __construct($data = array()){
		if(!empty($data)){
			$this->data = $data;
		}
	}
	
	function getData(){
		return $this->data;
	}
	
	function setId($id){
		$this->data['id'] = $id;
	}
	
	function setExchangeId($id){
	    $this->data['itemId'] = $id;
	}
	
	function setFirstName($val){
		$this->data['GivenName'] = $val;
	}
	
	function setLastName($val){
		$this->data['Surname'] = $val;
	}
	
	function setDisplayName($val){
		$this->data['DisplayName'] = $val;
	}
	
	function setBirthday($val){
	    
	    if($val){
	        $val = $val ." 08:00:00";
	        $val = date("Y-m-d\TH:i:s.000\Z", strtotime($val));
	    }
        $this->data['Birthday'] = $val;
	}
	
	function setEmails($emails, $emptyPrev = false){
	    
		if( is_array($emails) && !empty($emails) ){
		    
		    if($emptyPrev){
		        $this->data['EmailAddresses'] = array();
		    }
		        
		    $this->data['EmailAddresses'] = $emails;
		}
	}
	
	function setMobile($val){
	    $this->data['PhoneNumbers']['mobile'] = $val;
	}
	
	function setHomePhone($val){
		$this->data['PhoneNumbers']['home'] = $val;
	}
	
	function setBusinessPhone($val){
        $this->data['PhoneNumbers']['business'] = $val;
	}
	
	function setHomeAddress($street, $city, $state, $country_region, $postal){
		$this->data['PhysicalAddresses']['home'] = array(
		   "street" => $street,
			"city" => $city,
			"state" => $state,
			"countryOrRegion" => $country_region,
			"postalCode" => $postal,
		);
	}
	

	function setOtherAddress($street, $city, $state, $country_region, $postal){
		$this->data['PhysicalAddresses']['other'] = array(
		    "street" => $street,
			"city" => $city,
			"state" => $state,
			"countryOrRegion" => $country_region,
			"postalCode" => $postal,
		);
	}
	
	function setBusinessAddress($street, $city, $state, $country_region, $postal){
		$this->data['PhysicalAddresses']['business'] = array(
		    "street" => $street,
			"city" => $city,
			"state" => $state,
			"countryOrRegion" => $country_region,
			"postalCode" => $postal,
		);
	}
	
	
	function setTitle($title) {
	    return $this->data['JobTitle'] = $title;
	}
	
	function setCompanyName($accountName){
	   return $this->data['CompanyName'] = $accountName;
	}
	
	function setDescription($notes){
	    return $this->data['Body'] = array('BodyType' => 'HTML', '_value' => (!empty($notes))?nl2br(decode_html($notes)):$notes);
	}
	
	function setDepartment($value){
	    return $this->data['Department'] = $value;
	}
	
	function setAssistantName($value){
	    return $this->data['AssistantName'] = $value;
	}
	
	
}

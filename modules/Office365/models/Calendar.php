<?php

class Office365_Calendar_Model extends Office365_SyncRecord_Model {

    function getEntityData(){
    	if(isset($this->data['entity'])){
    		return $this->data['entity'];
    	}
    	return array();
    }
    
    /**
     * return id of Office365 Record
     * @return <string> id
     */
    function getId() {
        
        if($this->get("office365Response") == true)
            return $this->data['entity']['id'];
        else{
            
            if(is_object($this->data['entity']))
                return $this->data['entity']->getId();
            else {
                return '';
            }
        }
    }

    /**
     * return modified time of Office365 Record
     * @return <date> modified time 
     */
    public function getModifiedTime() {
//         $reflect = new ReflectionClass($this->data['entity']);
        
//         if ($reflect->getShortName() === 'SyncFolderItemsDeleteType') {
//            return false;
//         }
       
        if($this->get("office365Response") == true)
            return date('Y-m-d H:i:s');
        else    
            return $this->data['entity']->getLastModifiedDateTime() ? $this->vtigerFormat($this->data['entity']->getLastModifiedDateTime()->format('Y-m-d H:i:s')) : '';
    }

    /**
     * return Subject of Office365 Record
     * @return <string> Subject
     */
    function getSubject() {
    	return $this->data['entity']->getSubject();
    }

    /**
     * return start date in UTC of Office365 Record
     * @return <date> start date
     */
    function getStartDate() {
        return $this->data['entity']->getStart()->getDateTime();
    }

    /**
     * return  End  date in UTC of Office365 Record
     * @return <date> end date
     */
    function getEndDate() {
        return $this->data['entity']->getEnd()->getDateTime();
    }

    /**
     * return title of Office365 Record
     * @return <string> title
     */
    function getTitle() {
        return $this->data['entity']->getSubject();
    }
    
    /**
     * function to get Visibility of Office365 calendar event
     * @return <string> visibility of Office365 event (Private or Public)
     */
    function getVisibility() {
    	return 'Public';
    }
    
	function getPriority() {
	    $priority = strtolower($this->data['entity']->getImportance()->value());
    	if($priority == 'low'){
    		return 'Low';
    	} else if($priority == 'high'){
    		return 'High';
    	} else if ($priority == 'normal'){
    		return 'Medium';
    	} else {
    		return '';
    	}
	}
	
	function isOrganizer(){
	    $isOrganizer = $this->data['entity']->getResponseStatus()->getResponse()->value();
		if($isOrganizer == 'organizer'){
			return '1';
		}
		return '';
	}
	
	function getShowAs(){
		return $this->data['entity']['showAs'];
	}
	
	function isAllDay(){
	    return $this->data['entity']->getIsAllDay();
	}
	
	function getRecurrenceInfo(){
		return $this->data['entity']->getRecurrence();
	}

	function getBodyPreview(){
		return $this->data['entity']['bodyPreview'];	
	}
	
    /**
     * return discription of Office365 Record
     * @return <string> Discription
     */
    function getDescription(){
        
        $body = $this->data['entity']->getBody();
        
        if($body)
            return $body->getContent();
        else 
            return $body;
    }

    /**
     * return location of Office365 Record
     * @return <string> location
     */
    function getWhere() {
        $where = $this->data['entity']->getLocation()->getDisplayName();
        return $where;
    }

    function getSeriesMasterId(){
        return (isset($this->data['seriesMasterId']) && $this->data['seriesMasterId'] != '') ? $this->data['seriesMasterId'] : '';
    }
    
    function setSeriesMasterId($masterId){
        $this->data['seriesMasterId'] = $masterId;
    }
    /**
     * Returns the Office365_Calendar_Model of Google Record
     * @param <array> $recordValues
     * @return Office365_Calendar_Model
     */
    public static function getInstanceFromValues($recordValues) {
        $model = new Office365_Calendar_Model($recordValues);
        return $model;
    }

    /**
     * converts the Office365 Format date to 
     * @param <date> $date Office365 Date
     * @return <date> Vtiger date Format
     */
    public function vtigerFormat($date) {
        
        return date("Y-m-d H:i:s", strtotime($date));
        
        list($date, $timestring) = explode('T', $date);
        list($time, $tz) = explode('.', $timestring);

        return $date . " " . $time;
    }
    
	public function getSyncIdentificationKey(){
		return $this->data['_syncidentificationkey'];
	}

	public function get($key){
	    return $this->data[$key];
	}
	
	function getAttendees(){
	    
	    $addresses = array();
	    
	    $attendees = $this->data['entity']->getAttendees();
	    
	    foreach($attendees as $attendee){
    	    
	        $addresses[] = $attendee['emailAddress']['address'];
	        
	    }
	    
	    return $addresses;
	}
}

?>
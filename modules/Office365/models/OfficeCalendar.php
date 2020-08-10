<?php
class Office365_OfficeCalendar_Model {
	
	function  __construct($values = array()) {
	    $this->data = $values;
	}
	
	function getData(){
		return $this->data;
	}
	
	function setId($id){
		$this->data['id'] = $id;
	}
	
	function setSubject($val){
		$this->data['Subject'] = $val;
	}
	
	function setLocation($val){
		$this->data['Location'] = $val;
	}
	
	function setDescription($val){
	    $this->data['Body'] =  nl2br($val);
	}
	
	function setStart($val){
	    $this->data['Start'] = $val;
	}
	
	function setEnd($val){
	    $this->data['End'] = $val;
	}
	
	function setSensitivity($val){
	   $this->data['Sensitivity'] = 'normal';
	}
	
	function setSendNotification($val){
	    $this->data['SendNotification'] = $val;
	}
	function setAttendees($attendees){
	    
	    if(!empty($attendees)){
	        
	        $this->data['Attendee'] = array();
	        
	        foreach($attendees as $attendee){
	            
	            $this->data['Attendee'][] = array(
	                'emailAddress' => array(
	                    'address' => $attendee,
	                )
	            );
	        }
	    }
	}
	
	function setAllDayEvent($val){
	    $this->data['IsAllDayEvent'] = $val;
	}
}

<?php
class MSExchange_MSExchangeTask_Model {
    
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
    
    function setDescription($val){
        $this->data['Body'] = array('BodyType' => 'HTML', '_value' => nl2br($val));
    }
     
    function setEndDate($val){
        $this->data['DueDate'] = $val;
    }
    
    function setSensitivity($val){
        $this->data['Sensitivity'] = 'Normal';
    }
    
    function setStatus($val){
        $this->data['Status'] = $val;
    }
    
    function setSendNotification($val){
        $this->data['SendNotification'] = $val;
    }
   
}

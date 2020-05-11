<?php

class MSExchange_Task_Model extends MSExchange_SyncRecord_Model {
    
    function getEntityData(){
        if(isset($this->data['entity'])){
            return $this->data['entity'];
        }
        return array();
    }
    
    /**
     * return id of MSExchange Record
     * @return <string> id
     */
    function getId() {
        if($this->get("exchangeResponse") == true)
            return $this->data['entity']->getId();
        else
            return $this->data['entity']->getItemId()->getId();
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
            return $this->vtigerFormat($this->data['entity']->getLastModifiedTime());
    }
    
    /**
     * return Subject of MSExchange Record
     * @return <string> Subject
     */
    function getSubject() {
        return $this->data['entity']->getSubject();
    }
    
    /**
     * return start date in UTC of MSExchange Record
     * @return <date> start date
     */
    function getStartDate() {
        return $this->data['entity']->getStartDate();
    }
    
    /**
     * return  End  date in UTC of MSExchange Record
     * @return <date> end date
     */
    function getEndDate() {
        return $this->data['entity']->getDueDate();
    }
    
    /**
     * return title of MSExchange Record
     * @return <string> title
     */
    function getTitle() {
        return $this->data['entity']->getSubject();
    }
   
    function getPriority() {
        $priority = strtolower($this->data['entity']->getSensitivity());
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
    
    
    function getBodyPreview(){
        return $this->data['entity']['bodyPreview'];
    }
    
    /**
     * return discription of MSExchange Record
     * @return <string> Discription
     */
    function getDescription(){
        
        $body = $this->data['entity']->getBody();
        
        if($body)
            return $body->__toString();
        else
            return $body;
    }
    
    /**
     * return location of MSExchange Record
     * @return <string> location
     */
    function getStatus() {
        $status = $this->data['entity']->getStatus();
        return $status;
    }
    
    function getSeriesMasterId(){
        return (isset($this->data['seriesMasterId']) && $this->data['seriesMasterId'] != '') ? $this->data['seriesMasterId'] : '';
    }
    
    function setSeriesMasterId($masterId){
        $this->data['seriesMasterId'] = $masterId;
    }
    /**
     * Returns the MSExchange365_Task_Model of Google Record
     * @param <array> $recordValues
     * @return MSExchange_Task_Model
     */
    public static function getInstanceFromValues($recordValues) {
        $model = new MSExchange_Task_Model($recordValues);
        return $model;
    }
    
    /**
     * converts the MSExchange Format date to
     * @param <date> $date MSExchange Date
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
   
    
}

?>
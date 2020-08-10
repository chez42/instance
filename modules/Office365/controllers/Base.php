<?php

abstract class Office365_Base_Controller {
    
	public $user;

	abstract function getTargetConnector();
	
	function __construct($user) {
		$this->db = PearDatabase::getInstance();
		$this->user = $user;
		$this->targetConnector = $this->getTargetConnector();
		$this->sourceConnector = $this->getSourceConnector();
	}
	
	
    // vtiger connector
	
    public function getSourceConnector() {
        $connector = new Office365_Vtiger_Connector($this->user);
		$connector->setSynchronizeController($this);
		$targetName = $this->targetConnector->getName();
		if(empty($targetName)){
			throw new Exception('Target Name cannot be empty');
		}
        return $connector->setName('Vtiger_'.$targetName);
    }
    
	function getTargetRecordModel($data) {
	    return new Office365_Calendar_Model($data);
	}

	function getSourceRecordModel($data) {
	    return new Office365_Vtiger_Model($data);
	}
    
	function getOffice365Model(){
	    return $this->api;
	}
}

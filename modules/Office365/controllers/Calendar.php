<?php
require_once 'modules/WSAPP/WSAPPLogs.php';
require_once 'modules/WSAPP/synclib/models/SyncRecordModel.php';

class Office365_Calendar_Controller extends Office365_Base_Controller{
    
    protected $api;
    
    public $syncId = false;
    
    function __construct($user = false){
        
        if(!$user){
            $user = Users_Record_Model::getCurrentUserModel();
        }
        
        parent::__construct($user);
        
        $syncUserCredx = Office365_Utils_Helper::getCredentialsForUser($user, 'Calendar');
        
        $this->userOffice365Settings = $syncUserCredx;
        
        $this->syncId = $syncUserCredx['id'];
        
        if(!$this->api){
            $this->api = new Office365_Office365Calendar_Model($syncUserCredx['access_token'], $syncUserCredx['refresh_token']);
            $this->api->getInstance($syncUserCredx['access_token'], $syncUserCredx['refresh_token']);
        }
    }
    
    public function getTargetConnector() {
        $connector =  new Office365_Calendar_Connector($this->user);
        $connector->setSynchronizeController($this);
        return $connector;
    }
    
    public function getSourceType() {
        return 'Events';
    }
    
    public function getSyncType(){
        return 'user';
    }
    
    /*
     * function to pull from vtiger and push to office365
     */
    
    public function synchronizePull() {
        
        $synchronizedRecords = array();
        
        //pull vtiger records
        
        $sourceRecords = $this->sourceConnector->pull('Calendar');
       
        $synchronizedRecords = array();
        
        if(!empty($sourceRecords)){
            
            foreach($sourceRecords as $record){
                $record->setSyncIdentificationKey(uniqid(rand(), true));
            }
            
            $transformedRecords = $this->targetConnector->transformToTargetRecord($sourceRecords);
            
            $targetRecords = $this->targetConnector->push($transformedRecords, $this->user);
            
            foreach($sourceRecords as $sourceRecord){
                $sourceId = $sourceRecord->getId();
                foreach($targetRecords as $targetRecord){
                    if($sourceRecord->getSyncIdentificationKey() == $targetRecord->getSyncIdentificationKey()){
                        $sychronizeRecord = array();
                        $sychronizeRecord['source'] = $sourceRecord;
                        $sychronizeRecord['target'] = $targetRecord;
                        $synchronizedRecords[] = $sychronizeRecord;
                        break;
                    }
                    
                }
            }
        }
       
        $this->sourceConnector->postEvent('pull', $synchronizedRecords); // call vtiger's post pull event
        
        return $synchronizedRecords;
    }
    
    /*
     * function to push to vtiger by pull from office365
     */
    function synchronizePush(){
        
        $synchronizedRecords = array();
        
        $targetRecords = $this->targetConnector->pull();
        
        foreach($targetRecords as $record){
            $record->setSyncIdentificationKey(uniqid(rand(), true));
        }
        
        $transformedRecords = $this->targetConnector->transformToSourceRecord($targetRecords);
        
        $sourceRecords = $this->sourceConnector->push($transformedRecords);
        
        foreach ($targetRecords as $targetRecord) {
            $targetId = $targetRecord->getId();
            foreach ($sourceRecords as $sourceRecord) {
                if ($sourceRecord->getSyncIdentificationKey() == $targetRecord->getSyncIdentificationKey()) {
                    $sychronizeRecord = array();
                    $sychronizeRecord['source'] = $sourceRecord;
                    $sychronizeRecord['target'] = $targetRecord;
                    $synchronizedRecords[] = $sychronizeRecord;
                    break;
                }
            }
        }
        
        return $synchronizedRecords;
        
    }
    
    public function synchronize($pullTargetFirst = true, $push = true, $pull = true) {
        $records = array();
        $currentTime = date('y-m-d H:i:s');
        $user = Users_Record_model::getCurrentUserModel();
        $records['synctime'] = $currentTime;
        $records['Extension'] = explode('_',get_class($this));
        $records['ExtensionModule'] = $this->getSourceType();
        $records['user'] = $user->id;
        
        if ($pullTargetFirst) {
            if($push) $records['push'] = $this->synchronizePush();
            if($pull) $records['pull'] = $this->synchronizePull();
        } else {
            if($pull) $records['pull'] = $this->synchronizePull();
            if($push) $records['push'] = $this->synchronizePush();
        }
        
        //To Log sync information
        WSAPP_Logs::add($records);
        
        return $records;
    }
}

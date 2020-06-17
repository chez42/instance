<?php
require_once 'modules/WSAPP/WSAPPLogs.php';
require_once 'modules/WSAPP/synclib/models/SyncRecordModel.php';

class MSExchange_Calendar_Controller extends MSExchange_Base_Controller{
    
    protected $api;
    
    public $syncId = false;
    
    function __construct($user = false){
        
        if(!$user){
            $user = Users_Record_Model::getCurrentUserModel();
        }
        
        parent::__construct($user);
        
        $syncUserCredx = MSExchange_Utils_Helper::getCredentialsForUser($user, 'Calendar');
        
        $this->userExchangeSettings = $syncUserCredx;
        
        $this->syncId = $syncUserCredx['id'];
        
        if(!$this->api){
            
            $result = $this->db->pquery("select * from vtiger_msexchange_global_settings");
            
            if($this->db->num_rows($result)){
                
                $globalSettings = $this->db->fetchByAssoc($result);
                
                $userSettings = $this->userExchangeSettings;
                
                if($globalSettings['impersonate_user_account'] == 1){
                    
                    if($globalSettings['password'] != ''){
                        $globalSettings['password'] = MSExchange_Utils_Helper::fromProtectedText($globalSettings['password']);
                    }
                    
                    $this->api = new MSExchange_ExchangeCalendar_Model($globalSettings['url'], $globalSettings['username'], $globalSettings['password'], $globalSettings['exchange_version']);
                    $this->api->setImpersonation($globalSettings['impersonation_type'], $userSettings['impersonation_identifier']);
                } else {
                    $this->api = MSExchange_ExchangeCalendar_Model::getInstance($globalSettings['url'], $userSettings['username'], $userSettings['password'], $globalSettings['exchange_version']);
                }
            }
        }
    }
    
    public function getTargetConnector() {
        $connector =  new MSExchange_Calendar_Connector($this->user);
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
     * function to pull from vtiger and push to exchange
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
     * function to push to vtiger by pull from msexchange
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

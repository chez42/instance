<?php

require_once 'include/Webservices/Utils.php';
require_once 'include/database/PearDatabase.php';
require_once 'include/Webservices/GetUpdates.php';
require_once 'include/utils/CommonUtils.php';
require_once 'include/Webservices/Update.php';
require_once 'include/Webservices/Revise.php';
require_once 'include/Webservices/Create.php';
require_once 'include/Webservices/Delete.php';

class Office365_Base_Connector {
    
    protected $create = "create";
    protected $update = "update";
    protected $delete = "delete";
    protected $save = "save";
    
    public $etv_seriesMasterIds;
    
    public $maxResults = 100;
    
    function getSynchronizeController(){
        return $this->syncController;
    }
    
    function setSynchronizeController($syncController){
        $this->syncController = $syncController;
    }
    
    public function office365Format($date) {
        $datTime = new DateTime($date);
        $timeZone = new DateTimeZone('UTC');
        $datTime->setTimezone($timeZone);
        $newFormat = $datTime->format('Y-m-d\TH:i:s\Z');
        return $newFormat;
    }
    
    
    public function vtigerFormat($date) {
        list($date, $timestring) = explode('T', $date);
        list($time, $tz) = explode('.', $timestring);
        
        return $date . " " . $time;
    }
    
    
    /*
     * This will performs basic transformation between two records
     * <params>
     *		The sourece records refers to record which has data
     *			Target record refers to record to which data has to be copied
     *
     */
    public function performBasicTransformations(Office365_SyncRecord_Model $sourceRecord, Office365_SyncRecord_Model $targetRecord){
        $targetRecord->setType($sourceRecord->getType());
        $targetRecord->setMode($sourceRecord->getMode());
        $targetRecord->setSyncIdentificationKey($sourceRecord->getSyncIdentificationKey());
        return $targetRecord;
    }
    
    public function performBasicTransformationsToSourceRecords(Office365_SyncRecord_Model $sourceRecord, Office365_SyncRecord_Model $targetRecord){
        $sourceRecord->setId($targetRecord->getId())
        ->setModifiedTime($targetRecord->getModifiedTime());
        return $sourceRecord;
    }
    
    public function performBasicTransformationsToTargetRecords(Office365_SyncRecord_Model $sourceRecord, Office365_SyncRecord_Model $targetRecord){
        $sourceRecord->setId($targetRecord->get('_id'))
        ->setModifiedTime($targetRecord->get('_modifiedtime'));
        
        return $sourceRecord;
    }
    
    public function checkIfRecordsAssignToUser($recordsIds,$userIds){
        $assignedRecordIds = array();
        if(!is_array($recordsIds))
            $recordsIds = array($recordsIds);
        if(count($recordsIds)<=0)
            return $assignedRecordIds;
        if(!is_array($userIds))
            $userIds = array($userIds);
        $db = PearDatabase::getInstance();
        $query = "SELECT * FROM vtiger_crmentity where crmid IN (".generateQuestionMarks($recordsIds).") and smownerid in (".generateQuestionMarks($userIds).")";
        $params = array();
        foreach($recordsIds as $id){
            $params[] = $id;
        }
        foreach($userIds as $userId){
            $params[] = $userId;
        }
        $queryResult = $db->pquery($query,$params);
        $num_rows = $db->num_rows($queryResult);
        
        for($i=0;$i<$num_rows;$i++){
            $assignedRecordIds[] = $db->query_result($queryResult,$i,"crmid");
        }
        return $assignedRecordIds;
    }
    
    function getRecordEntityNameIds($entityNames,$modules,$user){
        $entityMetaList = array();
        $db = PearDatabase::getInstance();
        
        if(empty($entityNames)) return;
        
        if(!is_array($entityNames))
            $entityNames = array($entityNames);
        if(empty($modules))
            return array();
        if(!is_array($modules))
            $modules = array($modules);
        
        $entityNameIds = array();
        foreach($modules as $moduleName){
            if(empty($entityMetaList[$moduleName])){
                $handler = vtws_getModuleHandlerFromName($moduleName, $user);
                $meta = $handler->getMeta();
                $entityMetaList[$moduleName] = $meta;
            }
            $meta = $entityMetaList[$moduleName];
            $nameFieldsArray = explode(",",$meta->getNameFields());
            if(count($nameFieldsArray)>1){
                $nameFields = "concat(".implode(",' ',",$nameFieldsArray).")";
            }
            else
                $nameFields = $nameFieldsArray[0];
                
            $query = "SELECT ".$meta->getObectIndexColumn()." as id,$nameFields as entityname
            FROM ".$meta->getEntityBaseTable()." as moduleentity INNER JOIN vtiger_crmentity as crmentity
            WHERE $nameFields IN(".generateQuestionMarks($entityNames).") AND crmentity.deleted=0 AND crmentity.crmid = moduleentity.".$meta->getObectIndexColumn()."";
            $result = $db->pquery($query,$entityNames);
            $num_rows = $db->num_rows($result);
            for($i=0;$i<$num_rows;$i++){
                $id = $db->query_result($result, $i,'id');
                $entityName = $db->query_result($result, $i,'entityname');
                $entityNameIds[decode_html($entityName)] = vtws_getWebserviceEntityId($moduleName, $id);
            }
        }
        return $entityNameIds;
    }
    
    
    /* ---------------------- Record Mapping Functions ------------------------------ */
    
    /**
     * Create serverid-clientid record map for the application
     */
    function idmap_put($syncid, $serverid, $clientid, $clientModifiedTime, $serverModifiedTime, $mode="save", $parentId = false) {
        
        $db = PearDatabase::getInstance();
        
        if($mode == $this->create)
            $this->idmap_create($syncid, $serverid, $clientid,$clientModifiedTime,$serverModifiedTime,$parentId);
            
        else if ($mode == $this->update)
            $this->idmap_update($syncid, $serverid, $clientid, $clientModifiedTime,$serverModifiedTime,$parentId);
            
        else if($mode==$this->save){
                
            $result = $db->pquery("SELECT * FROM vtiger_office365_recordmapping WHERE serverid=?
	          and BINARY officeid=?",array($serverid,$clientid));
            if($db->num_rows($result)<=0)
                $this->idmap_create($syncid, $serverid, $clientid, $clientModifiedTime,$serverModifiedTime,$parentId);
            else
                $this->idmap_update($syncid, $serverid, $clientid, $clientModifiedTime,$serverModifiedTime,$parentId);
                    
        }else if ($mode == $this->delete)
            $this->idmap_delete($syncid, $serverid, $clientid);
    }
    
    /**
     * @param  $appid
     * @param  $serverid
     * @param  $clientid
     * @param  $modifiedTime
     *  *create mapping for server and client id
     */
    function idmap_create($syncid, $serverid, $clientid, $clientModifiedTime, $serverModifiedTime, $parentId = 'NULL'){
        $db = PearDatabase::getInstance();
        $db->pquery("INSERT INTO vtiger_office365_recordmapping (sync_id, serverid, officeid, office365modifiedtime,
		servermodifiedtime, parent_exchangeid) VALUES (?,?,?,?,?,?)",
            array($syncid, $serverid, $clientid, $clientModifiedTime,$serverModifiedTime, $parentId));
    }
    
    /**
     *
     * @param <type> $appid
     * @param <type> $serverid
     * @param <type> $clientid
     * @param <type> $modifiedTime
     * update the mapping of server and client id
     */
    function idmap_update($syncid, $serverid, $clientid,$clientModifiedTime,$serverModifiedTime, $parentId = 'NULL'){
        $db = PearDatabase::getInstance();
        $db->pquery("UPDATE vtiger_office365_recordmapping SET office365modifiedtime=?,servermodifiedtime=?,parent_exchangeid=?
        WHERE sync_id = ? and serverid = ? and BINARY officeid = ? ",
            array($clientModifiedTime, $serverModifiedTime, $parentId, $syncid, $serverid, $clientid));
    }
    
    /**
     *
     * @param <type> $appid
     * @param <type> $serverid
     * @param <type> $clientid
     * delete the mapping for client and server id
     */
    function idmap_delete($syncid, $serverid, $clientid){
        $db = PearDatabase::getInstance();
        $db->pquery("DELETE FROM vtiger_office365_recordmapping WHERE sync_id = ? and serverid=?
		and BINARY officeid=?", array($syncid, $serverid, $clientid));
    }
    
    
    function idmap_updateMapDetails($syncid, $clientid,$clientModifiedTime,$serverModifiedTime){
        $db = PearDatabase::getInstance();
        $db->pquery("UPDATE vtiger_office365_recordmapping SET office365modifiedtime=?, servermodifiedtime=?
		WHERE sync_id = ? and clientid = ?", array($clientModifiedTime,$serverModifiedTime, $syncid, $clientid));
    }
    
    
    /**
     * Retrieve serverid-clientid record map information for the given
     * application and serverid
     */
    function idmap_get_clientmap($syncid, $serverids) {
        if (!is_array($serverids)) $serverids = array($serverids);
        $db = PearDatabase::getInstance();
        $result = $db->pquery(sprintf(
            "SELECT serverid, officeid, office365modifiedtime, servermodifiedtime,id
			FROM vtiger_office365_recordmapping WHERE sync_id = ? and serverid IN ('%s')",
            implode("','", $serverids)), array($syncid));
        
        $mapping = array();
        if ($db->num_rows($result)) {
            while ($row = $db->fetch_array($result)) {
                $mapping[$row['serverid']] = array("officeid"=>$row['officeid'],"office365modifiedtime"=>$row['office365modifiedtime'],
                    "servermodifiedtime"=>$row['servermodifiedtime'],"id"=>$row['id']);
            }
        }
        return $mapping;
    }
    
    /**
     * Retrieve serverid-clientid record map information for the given
     * application and client
     */
    function idmap_get_clientservermap($syncid, $clientids){
        if(!is_array($clientids) && $clientids != '') $clientids = array($clientids);
        
        $db = PearDatabase::getInstance();
        
        $result = $db->pquery(sprintf("SELECT serverid, officeid FROM vtiger_office365_recordmapping
		WHERE sync_id = ? and BINARY officeid IN ('%s')",implode("','", $clientids)), array($syncid));
        
        $mapping = array();
        if($db->num_rows($result)){
            while($row = $db->fetch_array($result)){
                $mapping[$row['officeid']] = $row['serverid'];
            }
        }
        
        return $mapping;
    }
    
    function getQueueDeleteRecord($appId){
        $db = PearDatabase::getInstance();
       /* $result = $db->pquery("SELECT * FROM vtiger_msexchange_queuerecords
		INNER JOIN vtiger_office365_recordmapping ON (vtiger_office365_recordmapping.id=vtiger_msexchange_queuerecords.syncserverid)
		WHERE vtiger_office365_recordmapping.sync_id=? ",array($appId));*/
        $serverIds = array();
        /*$num_rows = $db->num_rows($result);
        for($i=0;$i<$num_rows;$i++){
            $serverId = $db->query_result($result,$i,'serverid');
            $serverIds[] = $serverId;
        }*/
        return $serverIds;
    }
    
    function getSyncServerId($clientId,$serverId,$clientAppId){
        $db = PearDatabase::getInstance();
        $syncServerId = NULL;
        $query = "SELECT id FROM vtiger_office365_recordmapping WHERE officeid=? and serverid=? and sync_id=?";
        $result = $db->pquery($query,array($clientId,$serverId,$clientAppId));
        if($db->num_rows($result)>0){
            $syncServerId = $db->query_result($result,0,'id');
        }
        return $syncServerId;
    }
    
    function deleteQueueRecords($syncServerIdList){
        $db= PearDatabase::getInstance();
//         $deleteQuery = "DELETE FROM vtiger_msexchange_queuerecords WHERE syncserverid IN (".generateQuestionMarks($syncServerIdList).")";
//         $result = $db->pquery($deleteQuery,$syncServerIdList);
    }
    
    function deleteQueueMasterRecords($syncServerIdList){
        $db= PearDatabase::getInstance();
        $deleteQuery = "DELETE FROM vtiger_office365_recordmapping WHERE parent_exchangeid IN (".generateQuestionMarks($syncServerIdList).")";
        $result = $db->pquery($deleteQuery,$syncServerIdList);
    }
    
}
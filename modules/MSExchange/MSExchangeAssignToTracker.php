<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once 'include/Webservices/Utils.php';
require_once 'include/events/VTEntityData.inc';
require_once 'data/VTEntityDelta.php';
require_once 'include/Webservices/DataTransform.php';

class MSExchangeAssignToTracker extends VTEventHandler{
    function  __construct() {
        
    }
    
    function handleEvent($eventName, $entityData) {
        global $current_user;
        $db = PearDatabase::getInstance();
        $moduleName = $entityData->getModuleName();
        
        if ($moduleName == 'Users') {
            return;
        }
        
        $recordId = $entityData->getId();
        $vtEntityDelta = new VTEntityDelta ();
        $newEntityData = $vtEntityDelta->getNewEntity($moduleName,$recordId);
        $recordValues = $newEntityData->getData();
        $isAssignToModified = $this->isAssignToChanged($moduleName,$recordId,$current_user);
        if(!$isAssignToModified){
            return;
        }
        $wsModuleName = $this->getWsModuleName($moduleName);
        if($wsModuleName =="Calendar")
        {
            $wsModuleName = vtws_getCalendarEntityType($recordId);
        }
        $handler = vtws_getModuleHandlerFromName($wsModuleName, $current_user);
        $meta = $handler->getMeta();
        $recordValues = DataTransform::sanitizeData($recordValues,$meta);
        
        $recordWsId = $recordValues['id'];
        $modifiedTime = $recordValues['modifiedtime'];
        $db = PearDatabase::getInstance();
        $query = "SELECT * FROM vtiger_msexchange_recordmapping WHERE serverid=? and servermodifiedtime < ?";
        $params = array($recordWsId,$modifiedTime);
        $result = $db->pquery($query,$params);
        while($arre = $db->fetchByAssoc($result)){
            $syncServerId = $arre["id"];
            $clientMappedId = $arre["sync_id"];
            if(!$this->checkIdExistInQueue($syncServerId)){
                $this->idmap_storeRecordsInQueue($syncServerId,$recordValues,'delete',$clientMappedId);
            }
        }
    }
    
    function isAssignToChanged($moduleName,$recordId,$user){
        $wsModuleName = $this->getWsModuleName($moduleName);
        $handler = vtws_getModuleHandlerFromName($wsModuleName, $user);
        $meta = $handler->getMeta();
        $moduleOwnerFields = $meta->getOwnerFields();
        $assignToChanged = false;
        $vtEntityDelta = new VTEntityDelta ();
        foreach($moduleOwnerFields as $ownerField){
            $assignToChanged = $vtEntityDelta->hasChanged($moduleName, $recordId, $ownerField);
            if($assignToChanged)
                break;
        }
        return $assignToChanged;
    }
    
    function getWsModuleName($workFlowModuleName){
        //TODO: Handle getting the webservice modulename in a better way
        $wsModuleName = $workFlowModuleName;
        if($workFlowModuleName == "Activity")
            $wsModuleName = "Calendar";
            return $wsModuleName;
    }
    
    function checkIdExistInQueue($syncServerId){
        $db = PearDatabase::getInstance();
        $checkQuery = "SELECT syncserverid FROM vtiger_msexchange_queuerecords WHERE syncserverid=?";
        $result = $db->pquery($checkQuery,array($syncServerId));
        if($db->num_rows($result)>0)
            return true;
            return false;
    }
    
    function idmap_storeRecordsInQueue($syncServerId,$recordDetails,$flag,$sync_id){
        if(!is_array($recordDetails))
            $recordDetails = array($recordDetails);
            
            $db = PearDatabase::getInstance();
            $params = array();
            $params[] = $syncServerId;
            $params[] = Zend_Json::encode($recordDetails);
            $params[] = $flag;
            $params[] = $sync_id;
            $db->pquery("INSERT INTO vtiger_msexchange_queuerecords(syncserverid,details,flag,sync_id) VALUES(?,?,?,?)",array($params));
    }
    
}

?>

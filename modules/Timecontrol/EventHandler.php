<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Stefan Warnat <support@stefanwarnat.de>
 * Date: 08.03.15 14:53
 * You must not use this file without permission.
 */
global $root_directory;
require_once($root_directory."/modules/Timecontrol/autoload_wf.php");

class TimecontrolEventHandler extends VTEventHandler {
    
    /**
     * @param $handlerType
     * @param $entityData VTEntityData
     */
    public function handleEvent($handlerType, $entityData) {
        if($entityData->getModuleName() != 'Invoice' && $entityData->getModuleName() != 'Timecontrol' ) {
            return;
        }
        
        if($entityData->getModuleName() == 'Timecontrol'){
            
            if($entityData->get('relatedto')){
            
                $adb = \PearDatabase::getInstance();
                $timeSum = '00:00';
                $timecontrol = $adb->pquery("SELECT  SEC_TO_TIME( SUM( TIME_TO_SEC( vtiger_timecontrol.totaltime ) ) ) AS timesum   
                FROM vtiger_timecontrol
                INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_timecontrol.timecontrolid
                WHERE vtiger_crmentity.deleted = 0 AND vtiger_timecontrol.relatedto = ?",array($entityData->get('relatedto')));
                
                if($adb->num_rows($timecontrol)){
                    $timeSum = $adb->query_result($timecontrol, 0, 'timesum');
                }
                
               $timeTotal = date( 'H:i:s', strtotime($timeSum));
                
               $adb->pquery("UPDATE vtiger_troubletickets SET total_time_spent = ? WHERE ticketid = ?",array($timeTotal, $entityData->get('relatedto')));
            
            }
        }
        /*if($entityData->getModuleName() == 'Invoice') {
            if(isset($_POST['timecontrol_ids']) && !empty($_POST['timecontrol_ids'])) {
                $tmp = explode(',', $_POST['timecontrol_ids']);
                $ids = array();
                foreach($tmp as $id) {
                    $ids[] = intval($id);
                }
                
                $adb = \PearDatabase::getInstance();
                foreach($ids as $id) {
                    $sql = 'UPDATE vtiger_timecontrol SET invoiced = 1, invoiced_on = ? WHERE timecontrolid = ?';
                    $adb->pquery($sql, array($entityData->getId(), intval($id)), true);
                }
            }
        }*/
        
       /* if($entityData->getModuleName() == 'Timecontrol' && $entityData->isNew()) {
            $related_account_id = $entityData->get('related_account_id');
            if(!empty($related_account_id)) {
                return;
            }
            
            $relatedRecordId = $entityData->get('relatedto');
            if(!empty($relatedRecordId)) {
                $record = \Vtiger_Record_Model::getInstanceById($relatedRecordId);
                
                $related_account_id = 0;
                if($record->getModuleName() == 'Accounts') {
                    $related_account_id =  $record->getId();
                } else {
                    $references = \TimeControl\VtUtils::getReferenceFieldsForModule($record->getModuleName());
                    
                    foreach($references as $reference) {
                        if($reference['module'] == 'Accounts') {
                            $checkValue = $record->get($reference['fieldname']);
                            
                            if(!empty($checkValue)) {
                                $checkRecord = \Vtiger_Record_Model::getInstanceById($checkValue);
                                if($checkRecord->getModuleName() == 'Accounts') {
                                    $related_account_id = $checkValue;
                                }
                            }
                        }
                    }
                }
                
                if(!empty($related_account_id)) {
                    $adb = \PearDatabase::getInstance();
                    $adb->pquery('UPDATE vtiger_timecontrol SET related_account_id = ? WHERE timecontrolid = ?', array($related_account_id, $entityData->getId()), false);
                }
            }
        }*/
    }
    
}

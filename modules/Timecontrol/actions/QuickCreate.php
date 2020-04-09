<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
global $root_directory;
require_once($root_directory."/modules/Timecontrol/autoload_wf.php");

class Timecontrol_QuickCreate_Action extends Vtiger_Action_Controller {

    public function checkPermission(Vtiger_Request $request) {
   		$moduleName = $request->getModule();
   		$record = $request->get('record');

   		if(!Users_Privileges_Model::isPermitted($moduleName, 'Save')) {
   			throw new AppException('LBL_PERMISSION_DENIED');
   		}
   	}

    public function process(Vtiger_Request $request) {
        $current_user = Users_Record_Model::getCurrentUserModel();

        $relatedto = $request->get('related');
        $title = $request->get('title');
        $response = new Vtiger_Response();
        global $adb;
        
        $query = $adb->pquery('SELECT vtiger_timecontrol.*, relfield.setype FROM vtiger_crmentity
                    INNER JOIN vtiger_timecontrol ON (vtiger_timecontrol.timecontrolid = vtiger_crmentity.crmid)
                    LEFT JOIN vtiger_crmentity as relfield ON (relfield.crmid = vtiger_timecontrol.relatedto)
                    WHERE
                        vtiger_crmentity.setype = "Timecontrol" AND
                        vtiger_crmentity.smownerid = ? AND
                        timecontrolstatus != "finish" AND
                        vtiger_crmentity.deleted = 0 AND relatedto = ?',array($current_user->id,$relatedto));
        
        if(!$adb->num_rows($query)){
            $record = Vtiger_Record_Model::getCleanInstance('Timecontrol');
    
            if(empty($title)) {
                if(!empty($relatedto)) {
                    
                    if(getSalesEntityType($relatedto) == 'HelpDesk'){
                        
                        $tic_status = $adb->pquery("SELECT vtiger_troubletickets.* FROM vtiger_troubletickets
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid =  vtiger_troubletickets.ticketid
                        WHERE vtiger_crmentity.deleted = 0 AND vtiger_troubletickets.ticketid = ?",array($relatedto));
                        
                        if($adb->num_rows($tic_status)){
                            $ticketStatus = $adb->query_result($tic_status, 0, 'status');
                           
                            if($ticketStatus == 'QA'){
                                $ticket = $adb->pquery("SELECT * FROM vtiger_modtracker_basic
                                INNER JOIN vtiger_modtracker_detail ON vtiger_modtracker_detail.id = vtiger_modtracker_basic.id
                                WHERE vtiger_modtracker_basic.crmid = ? AND vtiger_modtracker_detail.fieldname = ?
                                AND postvalue = ?
                                ORDER BY changedon DESC LIMIT 1",array($relatedto, 'ticketstatus', $ticketStatus));
                                
                                if($adb->num_rows($ticket)){
                                    $ticketModified = $adb->query_result($ticket, 0, 'whodid');
                                    $record->set('ticket_status_modified_by', getUserFullName($ticketModified));
                                }
                            }
                            
                            $record->set('ticket_status', $ticketStatus);
                        }
                       
                        
                    }
                    
                    
                    $record->set('relatedto', $relatedto);
                    
                    $record->set('title', 'Timer '.Vtiger_Functions::getCRMRecordLabel($relatedto));
                } else {
                    $record->set('title', 'Timer '.rand(1000,9999));
                }
            } else {
                if(!empty($relatedto)) {
                    $record->set('relatedto', $relatedto);
                }
    
                $record->set('title', 'Timer '.$title);
            }
    
            $datetimefield = new DateTimeField('');
            $nowDate = $datetimefield->convertToUserTimeZone(date('Y-m-d H:i:s'));
            $finishDateTS = strtotime($nowDate->format('Y-m-d H:i:s'));
    
            $record->set('timecontrolstatus', 'run');
            $record->set('date_start', date('Y-m-d', $finishDateTS));
            $record->set('time_start', date('H:i:s', $finishDateTS));
            $record->set('productid', $current_user->get('tcproduct'));
        
            /*if(!empty($relatedto)){
    
    	        $db = PearDatabase::getInstance();
    	        $result = $db->pquery("SELECT crmid,setype FROM vtiger_crmentity WHERE crmid =?", array($relatedto));
    	        $setype = $db->query_result($result, 0, 'setype');
    	        $crmid =  $db->query_result($result, 0, 'crmid');
    
            	if($setype == "HelpDesk"){
    
            		$result_account = $db->pquery("SELECT vtiger_account.accountid FROM vtiger_account  
    												INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_account.accountid 
    												INNER JOIN vtiger_troubletickets ON vtiger_troubletickets.parent_id = vtiger_account.accountid 
    												WHERE vtiger_crmentity.deleted = 0 AND vtiger_troubletickets.ticketid =?", array($crmid));
    				$accountid = $db->query_result($result_account, 0, 'accountid');
    
    
    	        }elseif ($setype == "Printjob"){
    
            		$result_account = $db->pquery("SELECT printjob_account FROM vtiger_printjob WHERE printjobid =?",array($relatedto));
    		        $accountid = $db->query_result($result_account, 0, 'printjob_account');
    
    	        }
    
    	        if(!empty($accountid) && $accountid != "0"){
    
    		        $record->set('related_account_id', $accountid);
    
    	        }
    
            }*/
    
            $record->save();

            \TimeControl\ImageGeneration::generateImage($current_user->id);
            
            
            $response->setResult($record->getId());
            
        }else{
            $response->setResult(false);
        }
        $response->emit();
  //      parent::process($request);
    }
}
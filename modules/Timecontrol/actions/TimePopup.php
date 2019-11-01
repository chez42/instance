<?php
/************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L.  --  This file is a part of vtiger CRM TimeControl extension.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 * *********************************************************************************** */
global $root_directory;
require_once($root_directory."/modules/Timecontrol/autoload_wf.php");


class Timecontrol_TimePopup_Action extends Vtiger_Save_Action {
    
    function __construct() {
        parent::__construct();
        $this->exposeMethod('getAllTickets');
    }

	public function process(Vtiger_Request $request) {
	    
	    $mode = $request->getMode();
	    if(!empty($mode)) {
	        return $this->invokeExposedMethod($mode, $request);
	    }
	    
		$adb = \PearDatabase::getInstance();

        $current_user = Users_Record_Model::getCurrentUserModel();

        $sql = 'SELECT vtiger_timecontrol.*, relfield.setype FROM vtiger_crmentity
                    INNER JOIN vtiger_timecontrol ON (vtiger_timecontrol.timecontrolid = vtiger_crmentity.crmid)
                    LEFT JOIN vtiger_crmentity as relfield ON (relfield.crmid = vtiger_timecontrol.relatedto)
                    WHERE
                        vtiger_crmentity.setype = "Timecontrol" AND
                        vtiger_crmentity.smcreatorid = ? AND
                        timecontrolstatus != "finish" AND
                        vtiger_crmentity.deleted = 0
                    ORDER BY vtiger_crmentity.createdtime ASC
        ';

        $result = $adb->pquery($sql, array($current_user->id));

        $timer = array('timer' => array());
        $datetimefield = new DateTimeField('');

        while($row = $adb->fetchByAssoc($result)) {

            $timer['timer'][] = array(
                'start_date' => $row['date_start'],
                'start_time' => $row['time_start'],
                'timestamp' => strtotime($datetimefield->convertToDBTimeZone($row['date_start'].' '.$row['time_start'])->format('Y-m-d H:i:s')),
                'relatedname' => empty($row['relatedname'])?'':$row['relatedname'],
                'relatedurl' => 'index.php?module=' . $row['setype'] . '&view=Detail&record=' . $row['relatedto'],
                'title' => $row['title'],
                'timecontrolid' => $row['timecontrolid'],
            );
        }

        $timer['label']['no existing timer'] = getTranslatedString('no existing timer', 'Timecontrol');
        $timer['label']['create timer'] = getTranslatedString('Start timer', 'Timecontrol');
        
        //$tickets = $this->getAllTickets();
        
        /*$timer['label']['quick timer'] = getTranslatedString('quick timer', 'Timecontrol');
        $timer['label']['title of new timer'] = getTranslatedString('title for new quick timer', 'Timecontrol');*/
        $timer['label']['running timer'] = getTranslatedString('running timer', 'Timecontrol');
        //$timer['tickets'] = $tickets;

        echo json_encode($timer);
        exit();
	}
	
	public static function getAllTickets(Vtiger_Request $request){
	    
	    $searchValue = $request->get('search_value');
	    
	    $db = PearDatabase::getInstance();
	    
	    $moduleName = "HelpDesk";
	    
	    $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
	    
	    if($currentUserModel->hasModulePermission(getTabid($moduleName))) {
	        
	        $queryGenerator = new QueryGenerator($moduleName, $currentUserModel);
	        
	        $queryGenerator->setFields( array('ticketid','id', 'ticket_no') );
	        
	        $listviewController = new ListViewController($db, $currentUserModel, $queryGenerator);
	        
	        $query = $queryGenerator->getQuery();
	        
	        $query .= " AND vtiger_troubletickets.ticket_no LIKE '%".$searchValue."%'";
	        
	        $result = $db->pquery($query,array());
	        
	        $rows = $db->num_rows($result);
	        
	        $tickets = array();
	        
	        for($i=0; $i<$rows; $i++){
	            $tickets[] = array('label'=>decode_html($db->query_result($result, $i, 'ticket_no')), 'value'=>decode_html($db->query_result($result, $i, 'ticket_no')), 'id'=>$db->query_result($result, $i, 'ticketid'));
	        }
	        
	        $response = new Vtiger_Response();
	        $response->setResult($tickets);
	        $response->emit();
	    }
	    return array();
	}
	

    public function validateRequest(Vtiger_Request $request) {
        $request->validateReadAccess();
    }
}
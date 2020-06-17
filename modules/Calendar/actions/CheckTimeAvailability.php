<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Calendar_CheckTimeAvailability_Action extends Vtiger_Action_Controller{
    
    public function checkPermission(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        
        $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());
        
        if(!$permission) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }
    }
    
    public function process(Vtiger_Request $request) {
        
        global $adb,$current_user;
        
		$timeStart = Vtiger_Time_UIType::getTimeValueWithSeconds($request->get('timestart'));
        
		$timeEnd = Vtiger_Time_UIType::getTimeValueWithSeconds($request->get('timeend'));
        
		$date_start = $request->get('datestart');
        
		$date_end = $request->get('dateend');
		
		$record = $request->get('record');
		
		$user_id = $request->get('user_id');
        
        $startdate = Vtiger_Datetime_UIType::getDBDateTimeValue($date_start.' '. $timeStart);
        
		$enddate = Vtiger_Datetime_UIType::getDBDateTimeValue($date_end .' '. $timeEnd);
        
		if($record){
			$eventOn = $adb->pquery("SELECT * FROM vtiger_activity
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_activity.activityid
			WHERE vtiger_activity.eventstatus not in ('Cancelled', 'Held') and vtiger_crmentity.deleted = 0 and 
			vtiger_crmentity.crmid != ? and vtiger_crmentity.smownerid = ? and
			(
				(
					CONCAT(vtiger_activity.date_start,' ',vtiger_activity.time_start) BETWEEN ? AND ? OR
					CONCAT(vtiger_activity.due_date,' ',vtiger_activity.time_end) BETWEEN ? AND ? OR
					? BETWEEN CONCAT(vtiger_activity.date_start,' ',vtiger_activity.time_start) AND 
					CONCAT(vtiger_activity.due_date,' ',vtiger_activity.time_end)
				) 
			)",array($record,$user_id, $startdate,$enddate,  $startdate,$enddate, $startdate));
		} else {
			$eventOn = $adb->pquery("SELECT * FROM vtiger_activity
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_activity.activityid
			WHERE vtiger_activity.eventstatus not in ('Cancelled', 'Held') and  
			vtiger_crmentity.deleted = 0 and vtiger_crmentity.smownerid = ? and
			(
				(
					CONCAT(vtiger_activity.date_start,' ',vtiger_activity.time_start) BETWEEN ? AND ? OR
					CONCAT(vtiger_activity.due_date,' ',vtiger_activity.time_end) BETWEEN ? AND ? OR
					? BETWEEN CONCAT(vtiger_activity.date_start,' ',vtiger_activity.time_start) AND 
					CONCAT(vtiger_activity.due_date,' ',vtiger_activity.time_end)
				) 
			)",array($user_id, $startdate,$enddate,  $startdate,$enddate, $startdate));
		
		}
        $eventResult = array();
        
		if($adb->num_rows($eventOn)){
            
			for($e=0;$e<$adb->num_rows($eventOn);$e++){
			    $eventResult[$e] =$adb->query_result_rowdata($eventOn, $e);
               
                $value = Vtiger_Datetime_UIType::getDisplayDateTimeValue($adb->query_result($eventOn, $e, 'date_start').' '.$adb->query_result($eventOn,$e,'time_start'));
                list($startDate, $startTime) = explode(' ', $value);
                
                $currentUser = Users_Record_Model::getCurrentUserModel();
                
                $duevalue = Vtiger_Datetime_UIType::getDisplayDateTimeValue($adb->query_result($eventOn, $e, 'due_date').' '.$adb->query_result($eventOn,$e,'time_end'));
                list($dueDate, $dueTime) = explode(' ', $duevalue);
                    
                if($currentUser->get('hour_format') == '12'){
                    $startTime = Vtiger_Time_UIType::getTimeValueInAMorPM($startTime);
                    $dueTime = Vtiger_Time_UIType::getTimeValueInAMorPM($dueTime);
                }
                
                $eventResult[$e]['dateStart'] = $startDate . ' ' . $startTime;
                $eventResult[$e]['dueDate'] = $dueDate . ' ' . $dueTime;
            }
            
			$result = array('success'=>true,'data'=>$eventResult);
			
        } else {
            $result = array('success'=>false,'data'=>'');
        }
     
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
        
    }
    
}

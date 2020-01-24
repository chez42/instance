<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class OmniCal_ActivityReminder_Action extends Vtiger_Action_Controller{

	public function checkPermission(Vtiger_Request $request) {
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

            $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
            $permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());

            if(!$permission) {
                    throw new AppException('LBL_PERMISSION_DENIED');
            }
	}

        public function Dismiss($activities){
            global $adb, $log;
            $log->debug("in Dismiss  ");              
            $questions = generateQuestionMarks($activities);
            $query = "UPDATE vtiger_activity_reminder_popup 
                      SET status = 1 
                      WHERE recordid IN({$questions})";
            $adb->pquery($query, array($activities));//All reminders have been dismissed
/*
            //Determine which activities are Recurring
            $recurring = new OmniCal_Recurring_Model();
            $data = $recurring->GetRecurringData($activities);
            foreach($data AS $k => $v){
                $serialized = $v['serialized'];
                $activity_id = $v['activityid'];
                $recurObj = $recurring->getRecurringObjValue($v['serialized']);
                $activity = new OmniCal_Activity_Model();
                $record_model = $activity->GetActivityRecordModel($activity_id);
                $record_model->set("set_reminder", "Yes");//set reminder isn't actually a variable, but we know it should be "Yes" because we are here
                $set_date = $recurring->GetReminderDate($record_model, $recurObj);
                $activity->UpdateActivityReminderTime($activity_id, $set_date, $record_model->get('time_start'), 0);//Enable the reminder
            }*/
            $log->debug("out of Dismiss");
        }
        
        public function Snooze($activities, $minutes){
            global $adb;
            $current_user = Users_Privileges_Model::getCurrentUserPrivilegesModel();
            
            $interval = $current_user->get("reminder_interval");
            $questions = generateQuestionMarks($activities);
            $snooze = date("Y-m-d H:i", strtotime("+{$minutes} min"));
            $separate = new DateTime($snooze);
            $separate->add(date_interval_create_from_date_string($interval));

            $reminder_start = $separate->format("Y-m-d");
            $reminder_time = $separate->format("H:i");
            
            $query = "UPDATE vtiger_activity_reminder_popup
                      SET date_start=?, time_start=?, status=0
                      WHERE recordid IN ({$questions})";
            $adb->pquery($query, array($reminder_start, $reminder_time, $activities));
            
/*We originally updated the actual activity time to reflect the snooze changes...
            $activity_query = "UPDATE vtiger_activity
                               SET date_start=?, time_start=?
                               WHERE activityid IN ({$questions})";
            $adb->pquery($activity_query, array($reminder_start, $reminder_time, $activities));*/
        }
        
	public function process(Vtiger_Request $request) {
            $mode = $request->getMode();
            switch($mode){
                case 'getReminders':
                    $postpone = new OmniCal_Postpone_View();
                    echo $postpone->process($request);
                    break;
                case 'dismiss':
//                    print_r($request->get('activities'));
                    $this->Dismiss($request->get('activities'));
                    break;
                case 'snooze':
                    $this->snooze($request->get('activities'), $request->get('minutes'));
                    break;
            }
	}

	function getReminders(Vtiger_Request $request) {
            $recordModels = Calendar_Module_Model::getCalendarReminder();
            foreach($recordModels as $record) {
                    $records[] = $record->getDisplayableValues();
//			$record->updateReminderStatus();
            }

            return $records;
/*          $response = new Vtiger_Response();
            $response->setResult($records);                
            $response->emit();*/
	}

	function postpone(Vtiger_Request $request) {
/*          $recordId = $request->get('record');
            $module = $request->getModule();
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $module);
            $recordModel->updateReminderStatus(0);*/
	}
}

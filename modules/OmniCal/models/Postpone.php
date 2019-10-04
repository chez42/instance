<?php

class OmniCal_Postpone_Model extends Calendar_Module_Model{
    public $reminders;
    
    function __construct(Vtiger_Request $request) {
            $this->setReminders($request);
//		$this->exposeMethod('postpone');
    }

    public function checkPermission(Vtiger_Request $request) {
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

            $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
            $permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());

            if(!$permission) {
                    throw new AppException('LBL_PERMISSION_DENIED');
            }
    }

    public function GetReminderTime($activity_id){
        global $adb;
        $query = "SELECT date_start, time_start
                  FROM vtiger_activity_reminder_popup
                  WHERE recordid = ?";
        $result = $adb->pquery($query, array($activity_id));
        if($adb->num_rows($result) > 0){
            $date_start = $adb->query_result($result, 0, "date_start");
            $time_start = $adb->query_result($result, 0, "time_start");
            return array("reminder_date_start" => $date_start,
                         "reminder_time_start" => $time_start);
        }
    }

    public function GetStartDateTime($activity_id){
        global $adb;
        $query = "SELECT date_start, time_start
                  FROM vtiger_activity
                  WHERE activityid = ?";
        $result = $adb->pquery($query, array($activity_id));
        if($adb->num_rows($result) > 0){
            $date_start = $adb->query_result($result, 0, "date_start");
            $time_start = $adb->query_result($result, 0, "time_start");
            return array("reminder_date_start" => $date_start,
                         "reminder_time_start" => $time_start);
        }
    }

    public static function getCalendarReminder() {

		$db = PearDatabase::getInstance();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$activityReminder = $currentUserModel->getCurrentUserActivityReminderInSeconds();
                
		$recordModels = array();

		if($activityReminder != '' ) {
			$currentTime = time();
			$date = date('Y-m-d', strtotime("+$activityReminder seconds", $currentTime));
			$time = date('H:i',   strtotime("+$activityReminder seconds", $currentTime));
			$reminderActivitiesResult = "SELECT reminderid, recordid, semodule, date_start, time_start FROM vtiger_activity_reminder_popup
								INNER JOIN vtiger_crmentity WHERE vtiger_activity_reminder_popup.status = 0
								AND vtiger_activity_reminder_popup.recordid = vtiger_crmentity.crmid
								AND vtiger_crmentity.smownerid = ? AND vtiger_crmentity.deleted = 0
								AND (
                                                                        (DATE_FORMAT(vtiger_activity_reminder_popup.date_start,'%Y-%m-%d') < ?)
                                                                        OR ( 
                                                                             (DATE_FORMAT(vtiger_activity_reminder_popup.date_start,'%Y-%m-%d') <= ?) 
                                                                             AND (TIME_FORMAT(vtiger_activity_reminder_popup.time_start,'%H:%i') <= ?)
                                                                           )
                                                                    )";
			$result = $db->pquery($reminderActivitiesResult, array($currentUserModel->getId(), $date, $date, $time));
			$rows = $db->num_rows($result);
			for($i=0; $i<$rows; $i++) {
				$recordId = $db->query_result($result, $i, 'recordid');
				$recordModels[] = Vtiger_Record_Model::getInstanceById($recordId, 'Calendar');
			}
		}

		return $recordModels;
    }
    
    function setReminders(Vtiger_Request $request) {
            $recordModels = $this->getCalendarReminder();            
            foreach($recordModels as $record) {
                    $tmp_record = $record->getDisplayableValues();
                    $reminder_time = $this->GetStartDateTime($tmp_record['id']);
                    $tmp_record['reminder_date_start'] = $reminder_time['reminder_date_start'];
                    $tmp_record['reminder_time_start'] = $reminder_time['reminder_time_start'];
                    
                    $tmp_record['due_in'] = $this->date_difference($tmp_record['reminder_date_start'] . ' ' . $tmp_record['reminder_time_start']);
                    if($tmp_record['due_in']['days'] >= -14)//We don't show anything due more than 7 days ago
                        $records[] = $tmp_record;
//			$record->updateReminderStatus();
            }
            
            $this->reminders = $records;
    }
    
    function getReminders(){
        return $this->reminders;
    }
    
    public function date_difference($date1timestamp) {
        $format = "Y-m-d H:i";
        $now = date('Y-m-d H:i');
        $date1timestamp = date('Y-m-d H:i', strtotime($date1timestamp));
        
        $dateTime = DateTime::createFromFormat($format, $date1timestamp);
        $dateTime2 = DateTime::createFromFormat($format, $now);
        $timestamp1 = $dateTime->format('U');
        $timestamp2 = $dateTime2->format('U');

        $all = round(($timestamp1 - $timestamp2) / 60);
        $negative = "";
        if($all < 0)
            $negative = "-";
        $d = floor(abs($all) / 1440);
        $h = floor((abs($all) - $d * 1440) / 60);
        $m = (abs($all) - ($d * 1440) - ($h * 60));

        $d = $negative . $d;
        $h = $negative . $h;
        $m = $negative . $m;
        return array('days' => $d, 'hours'=>$h, 'mins'=>$m);
    }
}

?>

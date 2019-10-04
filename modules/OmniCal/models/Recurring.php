<?php

class OmniCal_Recurring_Model extends Vtiger_Module_Model{   
    public function __construct() {
        parent::__construct();
    }
    
   /**
     * 
     * @return type
     */
    static function getRecurringObjValue($serialized) {
        $params = array();
        parse_str($serialized, $params);
        $recurring_data = array();

        if (isset($params['recurringtype']) && $params['recurringtype'] != null && $params['recurringtype'] != '--None--') {
                if (!empty($params['date_start'])) {
                        $startDate = $params['date_start'];
                }
                if (!empty($params['calendar_repeat_limit_date'])) {
                        $endDate = $params['calendar_repeat_limit_date'];
                } elseif (isset($params['due_date']) && $params['due_date'] != null) {
                        $endDate = $params['due_date'];
                }
                if (!empty($params['time_start'])) {
                        $startTime = $params['time_start'];
                }
                if (!empty($params['time_end'])) {
                        $endTime = $params['time_end'];
                }

                $recurring_data['startdate'] = $startDate;
                $recurring_data['starttime'] = $startTime;
                $recurring_data['enddate'] = $endDate;
                $recurring_data['endtime'] = $endTime;

                $recurring_data['type'] = $params['recurringtype'];
                if ($params['recurringtype'] == 'Weekly') {
                        if (isset($params['sun_flag']) && $params['sun_flag'] != null)
                                $recurring_data['sun_flag'] = true;
                        if (isset($params['mon_flag']) && $params['mon_flag'] != null)
                                $recurring_data['mon_flag'] = true;
                        if (isset($params['tue_flag']) && $params['tue_flag'] != null)
                                $recurring_data['tue_flag'] = true;
                        if (isset($params['wed_flag']) && $params['wed_flag'] != null)
                                $recurring_data['wed_flag'] = true;
                        if (isset($params['thu_flag']) && $params['thu_flag'] != null)
                                $recurring_data['thu_flag'] = true;
                        if (isset($params['fri_flag']) && $params['fri_flag'] != null)
                                $recurring_data['fri_flag'] = true;
                        if (isset($params['sat_flag']) && $params['sat_flag'] != null)
                                $recurring_data['sat_flag'] = true;
                }
                elseif ($params['recurringtype'] == 'Monthly') {
                        if (isset($params['repeatMonth']) && $params['repeatMonth'] != null)
                                $recurring_data['repeatmonth_type'] = $params['repeatMonth'];
                        if ($recurring_data['repeatmonth_type'] == 'date') {
                                if (isset($params['repeatMonth_date']) && $params['repeatMonth_date'] != null)
                                        $recurring_data['repeatmonth_date'] = $params['repeatMonth_date'];
                                else
                                        $recurring_data['repeatmonth_date'] = 1;
                        }
                        elseif ($recurring_data['repeatmonth_type'] == 'day') {
                                $recurring_data['repeatmonth_daytype'] = $params['repeatMonth_daytype'];
                                switch ($params['repeatMonth_day']) {
                                        case 0 :
                                                $recurring_data['sun_flag'] = true;
                                                break;
                                        case 1 :
                                                $recurring_data['mon_flag'] = true;
                                                break;
                                        case 2 :
                                                $recurring_data['tue_flag'] = true;
                                                break;
                                        case 3 :
                                                $recurring_data['wed_flag'] = true;
                                                break;
                                        case 4 :
                                                $recurring_data['thu_flag'] = true;
                                                break;
                                        case 5 :
                                                $recurring_data['fri_flag'] = true;
                                                break;
                                        case 6 :
                                                $recurring_data['sat_flag'] = true;
                                                break;
                                }
                        }
                }
                if (isset($params['repeat_frequency']) && $params['repeat_frequency'] != null)
                        $recurring_data['repeat_frequency'] = $params['repeat_frequency'];
                $recurObj = RecurringType::fromUserRequest($recurring_data);

                return $recurObj;
        }
    }
    
    // Code included by Jaguar - starts
    /** Function to insert values in vtiger_recurringevents table for the specified tablename,module
      * @param $recurObj -- Recurring Object:: Type varchar
     */	
    function insertIntoRecurringTable(& $recurObj, $record_model, $serialized)
    {
            global $log,$adb;
            $log->info("in insertIntoRecurringTable  ");
            $st_date = $recurObj->startdate->get_DB_formatted_date();
            $log->debug("st_date ".$st_date);
            $end_date = $recurObj->enddate->get_DB_formatted_date();
            $log->debug("end_date is set ".$end_date);
            $type = $recurObj->getRecurringType();
            $log->debug("type is ".$type);
            $flag="true";
            if($record_model->get('mode') == 'edit')
            {
                    $activity_id=$record_model->get('id');

                    $sql='select min(recurringdate) AS min_date,max(recurringdate) AS max_date, recurringtype, activityid from vtiger_recurringevents where activityid=? group by activityid, recurringtype';
                    $result = $adb->pquery($sql, array($activity_id));
                    $noofrows = $adb->num_rows($result);
                    for($i=0; $i<$noofrows; $i++)
                    {
                            $recur_type_b4_edit = $adb->query_result($result,$i,"recurringtype");
                            $date_start_b4edit = $adb->query_result($result,$i,"min_date");
                            $end_date_b4edit = $adb->query_result($result,$i,"max_date");
                    }
                    if(($st_date == $date_start_b4edit) && ($end_date==$end_date_b4edit) && ($type == $recur_type_b4_edit))
                    {
                            if($record_model->get('set_reminder') == 'Yes')
                            {
                                    $sql = 'delete from vtiger_activity_reminder where activity_id=?';
                                    $adb->pquery($sql, array($activity_id));
//                                    $sql = 'delete  from vtiger_recurringevents where activityid=?';
//                                    $adb->pquery($sql, array($activity_id));
                                    $flag="true";
                            }
                            elseif($record_model->get('set_reminder') == 'No')
                            {
                                    $sql = 'delete  from vtiger_activity_reminder where activity_id=?';
                                    $adb->pquery($sql, array($activity_id));
                                    $flag="false";
                            }
                            else
                                    $flag="false";
                    }
                    else
                    {
                            $sql = 'delete from vtiger_activity_reminder where activity_id=?';
                            $adb->pquery($sql, array($activity_id));
//                            $sql = 'delete  from vtiger_recurringevents where activityid=?';
//                            $adb->pquery($sql, array($activity_id));
                    }
            }

            $recur_freq = $recurObj->getRecurringFrequency();
            $recurringinfo = $recurObj->getDBRecurringInfoString();

            if($flag=="true") {
                    $max_recurid_qry = 'select max(recurringid) AS recurid from vtiger_recurringevents;';
                    $result = $adb->pquery($max_recurid_qry, array());
                    $noofrows = $adb->num_rows($result);
                    $recur_id = 0;
                    if($noofrows > 0) {
                            $recur_id = $adb->query_result($result,0,"recurid");
                    }
                    $current_id =$recur_id+1;
                    $recurring_insert = "insert into vtiger_recurringevents values (?,?,?,?,?,?,?)";
                    $rec_params = array($current_id, $record_model->get('id'), $st_date, $type, $recur_freq, $recurringinfo, $serialized);
                    $adb->pquery($recurring_insert, $rec_params);
                    unset($_SESSION['next_reminder_time']);
            }
    }
    
    /**
     * Save to the activity clone table.  This saves all recurring dates and times for each recurring event
     * @global type $log
     * @param type $record_model
     * @param type $recurObj
     */
    public function SaveActivityClones($record_model, $recurObj){
        global $log;
        $log->debug("in SaveActivityClones");   
        $set_date = $record_model->get('date_start');
        
        $log->debug("ID: {$record_model->get('id')}, start date: " . $set_date . ", start time: " . $record_model->get('time_start'));
        $log->debug("current time/date (currently not used): {$current_time} {$current_date}");
        
        $sql = "INSERT INTO vtiger_recurringclones (activityid, date_start, time_start, due_date, time_end) VALUES ";
        $insert = "";

//        if(strtotime($current_date . " " . $current_time) > strtotime($record_model->get('date_start') . " " . $record_model->get('time_start'))){//Current date/time is greater than the one passed in
            if(is_object($recurObj)){//This is a recurring item
                foreach($recurObj->recurringdates AS $k => $v){
                    if(strtotime($v) > strtotime($record_model->get('date_start'))){//If the activities date/time is greater than the current time, we use that as our next reminder
                        $insert .= "(" . 
                                   $record_model->get('id') . ", '{$v}', '{$record_model->get('time_start')}, '{$record_model->get('due_date')}', '{$record_model->get('time_start')}'),";
                    }
                }
                $insert .= rtrim($insert, ",");
                $sql .= $insert;
                $db = PearDatabase::getInstance();
                $db->pquery($sql, array());
            }
//        }
        $log->debug("out of SaveActivityClones");
    }
    
    /**
     * Returns the 'next' date to use in the reminder popup window
     * @global type $log
     * @param type $record_model
     * @param type $recurObj
     * @return type
     */
    public function GetReminderDate($record_model, $recurObj){
        global $log;
        $log->debug("in GetReminderDate ");   
        $set_date = $record_model->get('date_start');
        $log->debug("start date: " . $set_date . ", start time: " . $record_model->get('time_start'));
        $log->debug("set reminder set as: " . $record_model->get('set_reminder'));
        if($record_model->get('set_reminder') == "Yes"){//We should be setting a reminder
            $current_time = date("H:i:s");
            $current_date = date("Y-m-d");
            $log->debug("current time/date: {$current_time} {$current_date}");
            if(strtotime($current_date . " " . $current_time) > strtotime($record_model->get('date_start') . " " . $record_model->get('time_start'))){//Current date/time is greater than the one passed in
                if(is_object($recurObj)){//This is a recurring item
                    foreach($recurObj->recurringdates AS $k => $v){
                        if(strtotime($v . " " . $record_model->get('time_start')) > strtotime($current_date . " " . $current_time)){//If the activities date/time is greater than the current time, we use that as our next reminder
                            $set_date = $v;
                            break;
                        }
                    }
                }
            }
        }
        $log->debug("out of GetReminderDate ");
        return $set_date;
    }
    
    /**
     * Get all information from recurringevents table based on passed in array of activity ID's
     * @global type $log
     * @global type $adb
     * @param type $activities
     */
    public function GetRecurringData($activities){
        global $log;
	$log->debug("in GetRecurringData  ");        
        $db = PearDatabase::getInstance();
        $questions = generateQuestionMarks($activities);
        $query = "SELECT * FROM vtiger_recurringevents WHERE activityid IN ({$questions})";
        $result = $db->pquery($query, array($activities));
        
        if($db->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $recurring[] = $v;
            }
        }
        
        return $recurring;
    }
    
    /**
     * Get the serialized string as it was passed into the vtiger_recurringevents function
     * @global type $log
     * @param type $record_model
     * @return string
     */
    public function GetSerializedString($record_model){
        global $log;
	$log->debug("in GetSerializedString  ");
        
        $db = PearDatabase::getInstance();
        $query = "SELECT serialized FROM vtiger_recurringevents WHERE activityid = ?";

        $result = $db->pquery($query, array($record_model->get('id')));
        if($db->num_rows($result) > 0){
            $log->debug("out of GetSerializedString with a found result");
            return $db->query_result($result, 0, "serialized");
        }

        $log->debug("out of GetSerializedString with no result");
        return '';
    }
    
    /**
     * Returns the serialized formatted array
     * @param type $record_model
     */
    public function GetSerializedArray($record_model){
        $serialized = html_entity_decode($this->GetSerializedString($record_model));
        $params = array();
        parse_str($serialized, $params);
        return $params;
    }
}
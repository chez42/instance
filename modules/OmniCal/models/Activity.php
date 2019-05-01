<?php
/*
Array ( 
 * [subject] => Test Task 
 * [assigned_user_id] => 22830 
 * [date_start] => 2014-04-23 
 * [time_start] => 19:08:00 
 * [time_end] => 
 * [due_date] => 2014-04-23 
 * [parent_id] => 
 * [contact_id] => 
 * [taskstatus] => Planned 
 * [eventstatus] => 
 * [taskpriority] => 
 * [sendnotification] => 0 
 * [createdtime] => 2014-04-23 19:08:47 
 * [modifiedtime] => 2014-04-23 21:30:02 
 * [activitytype] => Task 
 * [visibility] => Private 
 * [description] => 
 * [duration_hours] => 
 * [duration_minutes] => 
 * [location] => 
 * [reminder_time] => 
 * [recurringtype] => 
 * [notime] => 0 
 * [modifiedby] => 22830 
 * [record_id] => 1524156 
 * [record_module] => Calendar 
 * [id] => 1524156 )
 */
class OmniCal_Activity_Model extends Vtiger_Module_Model {    
    public function GetActivityRecordModel($activity_id){
        if($activity_id)
            $recordModel = Vtiger_Record_Model::getInstanceById($activity_id, 'Calendar');
        else
            $recordModel = Vtiger_record_Model::getCleanInstance ('Calendar');
        return $recordModel;
    }
    
    static public function GetMasterActivityFromChild($child_id){
        global $adb;
        $query = "SELECT master_id FROM vtiger_master_child WHERE child_id = ?";
        $result = $adb->pquery($query, array($child_id));
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'master_id');
        else
            return 0;
    }
    
    static public function GetChildActivityFromMaster($master_id){
        global $adb;
        $query = "SELECT child_id FROM vtiger_master_child WHERE master_id = ?";
        $result = $adb->pquery($query, array($child_id));
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'child_id');
        else
            return 0;        
    }
    
    static public function SaveMasterChildActivity($master_id, $child_id, $index){
        global $adb;
//        echo "MASTER: {$master_id}, CHILD: {$child_id}, INDEX: {$index}";
        $query = "INSERT INTO vtiger_master_child (master_id, child_id, vtiger_master_child.index) VALUES (?, ?, ?)";
        echo $query;
        $adb->pquery($query, array($master_id, $child_id, $index));
    }
    
    static public function GetIndexFromMasterChildActivity($master_id, $child_id){
        global $adb;
        $query = "SELECT vtiger_master_child.index FROM vtiger_master_child WHERE master_id = ? AND child_id = ?";
        $result = $adb->pquery($query, array($master_id, $child_id));
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'index');
        else
            return 0; 
    }
    
    public function SetDefaultTaskData(){
        $data = array();
        $data['subject'] = '';
        $data['taskstatus'] = "Planned";
        $data['sendnotification'] = 0;
        $data['activitytype'] = "Task";
        $data['visibility'] = "Private";
        $data['record_module'] = "Calendar";
        $data['date_start'] = date("m/d/Y");
        $data['time_start'] = date("h:i A");
        return $data;
    }
    
    public function SetDefaultEventData(Vtiger_Request $request){
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $data['subject'] = '';
        $data['eventstatus'] = "Planned";
        $data['sendnotification'] = 0;
        $data['activitytype'] = "Call";
        $data['visibility'] = "Private";
        $data['taskpriority'] = "High";
        $data['record_module'] = "Calendar";
        $data['callduration'] = $currentUserModel->get('callduration');
        $data['othereventduration'] = $currentUserModel->get('othereventduration');

        
        if(strlen($request->get('start_date')) > 0)
            $data['date_start'] = $request->get('start_date');
        else
            $data['date_start'] = date("Y-m-d");
        
        if(strlen($request->get('start_time')) > 0)
            $data['time_start'] = $request->get('start_time');
        else
            $data['time_start'] = date("h:i A");
        
        $converted_start = date('Y-m-d H:i', strtotime($data['date_start'] . ' ' . $data['time_start']));
        $converted_start = new DateTime(gmdate('Y-m-d H:i', strtotime($converted_start . $currentUserModel->get('time_zone'))));
        
        $converted_end = date('Y-m-d H:i', strtotime($data['date_start'] . ' ' . $data['time_start'] . ' +' . $data['callduration'] . ' minutes'));
        $converted_end = new DateTime(gmdate('Y-m-d H:i', strtotime($converted_end . $currentUserModel->get('time_zone'))));
        
        $data['date_start'] = $converted_start->format("Y-m-d");
        $data['time_start'] = $converted_start->format("H:i");
        
        $data['due_date'] = $converted_end->format("Y-m-d");
        $data['time_end'] = $converted_end->format("H:i");

        return $data;
    }
    
    public function GetActivityData($activity_id, $activity_type="Task", Vtiger_Request $request){
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        if($activity_id){
            $recordModel = Vtiger_Record_Model::getInstanceById($activity_id, 'Calendar');
            $data = $recordModel->getData();
        }
        else 
        {
            if($activity_type == "Task")
                $data = $this->SetDefaultTaskData();
            else
                $data = $this->SetDefaultEventData($request);
        }

        $converted_start = DateTimeField::convertToUserTimeZone($data['date_start'] . ' ' . $data['time_start']);
        $data['date_start'] = $converted_start->format("m/d/Y");
        $data['time_start'] = $converted_start->format("h:i A");
        
        $converted_end = DateTimeField::convertToUserTimeZone($data['due_date'] . ' ' . $data['time_end']);
        $data['due_date'] = $converted_end->format("m/d/Y");
        $data['time_end'] = $converted_end->format("h:i A");
        
        return $data;
    }
    /**
     * Use this function to get all parent data using the parent_id
     * @global type $adb
     * @param type $parent_id
     * @return type
     */
    public function GetActivityParentInfo($parent_id){
        global $adb;
        if($parent_id){
            $module = GetSettypeFromID($parent_id);
            $recordModel = Vtiger_Record_Model::getInstanceById($parent_id, $module);        
            $data = $recordModel->getData();
            $query = "SELECT label FROM vtiger_crmentity WHERE crmid = ?";
            $result = $adb->pquery($query, array($data['record_id']));
            foreach($result AS $k => $v){
                $data['display_name'] = $v['label'];
            }
        }
        return $data;
    }
    
    public function GetActivityContacts($activity_id, $record=null){
        global $adb;
        if($activity_id)
            $ignore_record = true;
        
        if($ignore_record){
            $query = "SELECT a.contactid, cd.firstname, cd.lastname, cd.email
                      FROM vtiger_cntactivityrel a
                      LEFT JOIN vtiger_contactdetails cd ON cd.contactid = a.contactid
                      WHERE activityid = ?";
            $result = $adb->pquery($query, array($activity_id));
        }
        else{
            $query = "SELECT cd.contactid, cd.firstname, cd.lastname
                      FROM vtiger_contactdetails cd
                      WHERE contactid = ?";
            $result = $adb->pquery($query, array($record));
        }
        
        $contacts = array();
        if($adb->num_rows($result) > 0)
            foreach($result AS $k => $v){
                $tmp = array("firstname" => $v['firstname'],
                             "lastname"  => $v['lastname'],
                             "email"     => $v['email'],
                             "id"        => $v['contactid'],
                             "fullname"  => $v['firstname'] . ' ' . $v['lastname']);
                $contacts[] = $tmp;
            }        
        return $contacts;
    }
    
    public function SaveActivityContacts($activity_id, $contact_list){
        global $adb;
        $query = "DELETE FROM vtiger_cntactivityrel WHERE activityid = ?";
        $adb->pquery($query, array($activity_id));
        $query = "INSERT INTO vtiger_cntactivityrel (contactid, activityid) VALUES ";
        $count = 1;//We start at 1 because if it is less than the sizeof array, we add 1.  0 is less than one, so a comma will be added improperly causing hell to break loose
        $used = array();
        $final_list = array();
        foreach($contact_list AS $k => $v){
            if(!in_array($v['id'], $used)){
                $final_list[] = $v['id'];
            }
            $used[] = $v['id'];
        }
        $contact_list = $final_list;
        if(is_array($contact_list))
        foreach($contact_list AS $k => $v){
            $query .= "({$v}, {$activity_id})";
            if($count < sizeof($contact_list)){
                $query .= ", ";
            }
            $count++;
        }
        $adb->pquery($query, array());
    }
    
    // Function to unlink all the dependent entities of the given Entity by Id
    public static function unlinkDependencies($id, $unlink_contacts=1) {
        $db = PearDatabase::getInstance();
        global $log;
        
        $sql = 'DELETE FROM vtiger_activity_reminder WHERE activity_id=?';
        $db->pquery($sql, array($id));

/*        $sql = 'DELETE FROM vtiger_recurringevents WHERE activityid=?';
        $db->pquery($sql, array($id));*/

        if($unlink_contacts){
            $sql = 'DELETE FROM vtiger_cntactivityrel WHERE activityid = ?';
            $db->pquery($sql, array($id));
        }
        
        $sql = 'DELETE FROM vtiger_recurringclones WHERE activityid = ?';
        $db->pquery($sql, array($id));
        
        $sql = 'DELETE FROM vtiger_invitees WHERE activityid = ?';
        $db->pquery($sql, array($id));

        $sql = 'DELETE FROM vtiger_invitees_contacts WHERE activityid = ?';
        $db->pquery($sql, array($id));

        $sql = 'DELETE FROM vtiger_invitees_manual WHERE activityid = ?';
        $db->pquery($sql, array($id));

    }
    
    public static function UpdateActivityReminderTime($activity_id, $start_date, $start_time, $status=0, $reminder_time=0){
//        $converted_time = date('Y-m-d H:i:s', strtotime($start_date . ' ' . $start_time));
        $time = strtotime($start_date . ' ' . $start_time);
        $converted_time = $time-($reminder_time*60);
        $converted_time = date("Y-m-d H:i", $converted_time);
        $converted_time = new DateTime(gmdate('Y-m-d H:i', strtotime($converted_time)));
        
        $start_date = $converted_time->format("Y-m-d");
        $start_time = $converted_time->format("H:i");
        global $adb;
        $query = "UPDATE vtiger_activity_reminder_popup
                  SET date_start=?, time_start=?, status=?
                  WHERE recordid=?";
        $adb->pquery($query, array($start_date, $start_time, $status, $activity_id));
    }
    
    public static function UpdateActivityReminderTable($days, $hours, $mins, $activity_id, $reminder_sent=0, $recurringid=0){
        $reminder_time = $days * 24 * 60 + $hours * 60 + $mins;
        global $adb;
        $query = "INSERT INTO vtiger_activity_reminder (activity_id, reminder_time, reminder_sent, recurringid)
                  VALUES (?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE 
                  activity_id=VALUES(activity_id), reminder_time=VALUES(reminder_time), reminder_sent=VALUES(reminder_sent), recurringid=VALUES(recurringid)";
        $adb->pquery($query, array($activity_id, $reminder_time, $reminder_sent, $recurringid));
        return $reminder_time;
    }
    
    public function HasReminder($activity_id){
        global $adb;
        $query = "SELECT * FROM vtiger_activity_reminder_popup WHERE recordid = ?";
        $result = $adb->pquery($query, array($activity_id));
        if($adb->num_rows($result) > 0)
            if($adb->query_result($result, 0, "status") == 1)
                return 'yes';
        return 'no';
    }
    
     public function getInvities($activity_id) {
         $adb = PearDatabase::getInstance();
         $sql = "select vtiger_invitees.* from vtiger_invitees where activityid=?";
         $result = $adb->pquery($sql,array($activity_id));
         $invitiesId = array();

         $num_rows = $adb->num_rows($result);

         for($i=0; $i<$num_rows; $i++) {
             $invitiesId[] = $adb->query_result($result, $i,'inviteeid');
         }
         return $invitiesId;
     }
     
     public function getContactInvities($activity_id){
         $adb = PearDatabase::getInstance();
         $sql = "select i.*, cd.email
                 FROM vtiger_invitees_contacts i
                 JOIN vtiger_contactdetails cd ON i.contactid = cd.contactid
                 WHERE i.activityid=?";
         $result = $adb->pquery($sql,array($activity_id));
         $invities = array();

         $num_rows = $adb->num_rows($result);

         for($i=0; $i<$num_rows; $i++) {
             $id = $adb->query_result($result, $i,'activityid');
             $email = $adb->query_result($result, $i,'email');
             $contact_id = $adb->query_result($result, $i, 'contactid');
             $tmp = array("id" => $id,
                          "contactid" => $contact_id,
                          "email" => $email);
             $invities[] = $tmp;
         }
         return $invities;
     }
     
     public function getManualInvities($activity_id){
         $adb = PearDatabase::getInstance();
         $sql = "select vtiger_invitees_manual.*
                 FROM vtiger_invitees_manual
                 WHERE activityid=?";
         $result = $adb->pquery($sql,array($activity_id));
         $invities = array();

         $num_rows = $adb->num_rows($result);

         for($i=0; $i<$num_rows; $i++) {
             $id = $adb->query_result($result, $i,'activityid');
             $email = $adb->query_result($result, $i,'email');
             $tmp = array("id" => $id,
                          "email" => $email);
             $invities[] = $tmp;
         }
         return $invities;
     }
     
     public function GetRecurringInfo($activity_id){
         $adb = PearDatabase::getInstance();
         $query = "SELECT recurringtype, recurringinfo, recurringfreq, DATE_FORMAT(recurringdate, '%m/%d/%Y') AS recurringdate,
                   DATE_FORMAT(recurringenddate, '%m/%d/%Y') AS recurringenddate FROM vtiger_recurringevents WHERE activityid = ?";
         $result = $adb->pquery($query, array($activity_id));
         $recurring = array();
         if($adb->num_rows($result) > 0){
             foreach($result AS $k => $v){
                 $recurring = array("type"=>$v['recurringtype'],
                                    "info"=>$v['recurringinfo'],
                                    "start"=>$v['recurringdate'],
                                    "end"=>$v['recurringenddate'],
                                    "frequency"=>$v['recurringfreq']);
                 return $recurring;
             }
         } else{
             return 0;
         }
     }
     
     public function GetSplitDays($activity_id){
         $adb = PearDatabase::getInstance();
         $query = "SELECT recurringinfo FROM vtiger_recurringevents WHERE activityid = ?";
         $result = $adb->pquery($query, array($activity_id));
         if($adb->num_rows($result) > 0){
             $days = $adb->query_result($result, 0, 'recurringinfo');
             $days_split = explode(" ", $days);
             return $days_split;
         }else{
             return 0;
         }
     }
     
     public function GetAddedUsersInfo($user_id){
        $user = Users_Record_Model::getInstanceById($user_id, 'Users');
        $data = $user->getData();
        $return_data = array("id" => $data['id'],
                             "first_name" => $data['first_name'],
                             "last_name" => $data['last_name'],
                             "user_name" => $data['user_name'],
                             "email" => $data['email1']);
        return $return_data;
     }
     
     static public function ConvertMassEditIDSToTaskOrEvent($selectedIds, &$tasks, &$events){
         global $adb;
         $query = "SELECT activitytype FROM vtiger_activity WHERE activityid = ?";
         foreach($selectedIds AS $k => $v){
             $result = $adb->pquery($query, array($v));
             if($adb->query_result($result, 0, 'activitytype') == 'Task')
                $tasks[] = $v;
             else
                 $events[] = $v;
        }
     }
     
     /**
      * Returns the type of attendee for the table based on if the email is in the users table or contacts table.  Otherwise it is manual.  This
      * is used for the 'remove' functionality of the table.
      * @param type $attendees
      */
     static public function DetermineTypeFromAttendeeArray($attendees){
         global $adb;
         $tmp = array();
         foreach($attendees AS $k => $v){
             $query = "SELECT id FROM vtiger_users WHERE email1 = ?";
             $result = $adb->pquery($query, array($v['email']));
             if($adb->num_rows($result) > 0){
                 $v['type'] = 'user';
                 $v['id'] = $adb->query_result($result, 0, 'id');
                 $tmp[] = $v;
             }else{
                 $query = "SELECT contactid FROM vtiger_contactdetails WHERE email = ?";
                 $result = $adb->pquery($query, array($v['email']));
                 if($adb->num_rows($result) > 0){
                     $v['type'] = 'contact';
                     $v['id'] = $adb->query_result($result, 0, 'contactid');
                     $tmp[] = $v;
                 }else{
                     $v['type'] = 'manual';
                     $tmp[] = $v;
                 }
             }
         }
         return $tmp;
     }
}

?>

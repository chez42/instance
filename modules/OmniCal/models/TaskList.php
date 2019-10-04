<?php

class OmniCal_TaskList_Model extends Activity{
    /**
     * Function to get list of task for user with given limit
     * @param  string   $user_name        - User Name
     * @param  string   $from_index       - query string
     * @param  string   $offset           - query string 
     * returns tasks in array format
     */
    function get_tasks($user_name,$from_index,$offset){
	global $log;
        $log->debug("Entering get_tasks(".$user_name.",".$from_index.",".$offset.") method ...");
	$query = "SELECT vtiger_activity.subject as name,vtiger_crmentity.modifiedtime as date_modified, vtiger_activity.date_start start_date,
                         vtiger_activity.activityid as id,vtiger_activity.status as status, vtiger_crmentity.description as description, 
                         vtiger_activity.priority as priority, vtiger_activity.due_date as date_due ,vtiger_contactdetails.firstname cfn, 
                         vtiger_contactdetails.lastname cln from vtiger_activity 
                            INNER JOIN vtiger_salesmanactivityrel on vtiger_salesmanactivityrel.activityid=vtiger_activity.activityid 
                            INNER JOIN vtiger_users on vtiger_users.id=vtiger_salesmanactivityrel.smid 
                            LEFT JOIN vtiger_cntactivityrel on vtiger_cntactivityrel.activityid=vtiger_activity.activityid 
                            LEFT JOIN vtiger_contactdetails on vtiger_contactdetails.contactid=vtiger_cntactivityrel.contactid 
                            INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid 
                            LEFT JOIN vtiger_activity_reminder_popup pop ON vtiger_activity.activityid = pop.recordid
                         WHERE user_name='" .$user_name ."' 
                         AND vtiger_crmentity.deleted=0 
                         AND vtiger_activity.activitytype='Task' 
                         AND vtiger_activity.status != 'Completed'
                         LIMIT " .$from_index ."," .$offset;
//                         AND (pop.status != 1 OR (vtiger_activity.date_start BETWEEN (NOW() - INTERVAL 1 MONTH) AND (NOW() + INTERVAL 1 MONTH)))
//                         LIMIT " .$from_index ."," .$offset;
	$log->debug("Exiting get_tasks method ...");
        return $this->process_list_query1($query);
    }
}

?>

<?php

class Omniscient_ManualInteractions_Model extends PortfolioInformation_PCQuery_Model{
    /**
     * Wipe the calendar sharing table for the given user
     * @param type $user_id
     */
    public function ResetCalendarSharing($user_id){
        global $adb;
        $query = "DELETE FROM vtiger_sharedcalendar WHERE userid = ?";
        $adb->pquery($query, array($user_id));
        $query = "UPDATE vtiger_users SET calendarsharedtype = 'selectedusers' WHERE id = ?";
        $adb->pquery($query, array($user_id));
    }
    
    public function AddMembersToSharedCalendar($user_id, $members){
        global $adb;
        $query = "INSERT INTO vtiger_sharedcalendar (userid, sharedid) VALUES (?, ?)";
        foreach($members AS $k => $v){
            $adb->pquery($query, array($user_id, $v));
        }
    }
    
    public function RunAllCalendarSharing(){
        $users = $this->GetAllNonAdminUsers();
        foreach($users AS $k => $v){
            $this->ResetCalendarSharing($v);
            $groups = fetchUserGroupids($v);
            $members = $this->GetUsersFromGroups($groups);
            $this->AddMembersToSharedCalendar($v, $members);
        }
    }
    
    public function RunIndividualCalendarSharing($username){
        global $adb;
        $query = "SELECT id FROM vtiger_users WHERE user_name = ?";
        $result = $adb->pquery($query, array($username));
        if($adb->num_rows($result) > 0){
            $id = $adb->query_result($result, 0, 'id');
            $this->ResetCalendarSharing($id);
            $groups = fetchUserGroupids($id);
            $members = $this->GetUsersFromGroups($groups);
            $this->AddMembersToSharedCalendar($id, $members);
            return "{$username} Reset";
        }else{
            return "Invalid Username";
        }
    }
    
    public function GetUsersFromGroups($csv_groups){
        global $adb;
        $users = array();
        $query = "SELECT userid FROM vtiger_users2group WHERE groupid IN ({$csv_groups})";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $users[] = $v['userid'];
            }
        }
        array_unique($users);
        return $users;
    }
    
    public function GetAllNonAdminUsers(){
        global $adb;
        $query = "SELECT id FROM vtiger_users WHERE is_admin = 'off'";
        $result = $adb->pquery($query, array());
        $users = array();
        foreach($result AS $k => $v){
            $users[] = $v['id'];
        }
        return $users;
    }
}
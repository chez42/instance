<?php

class OmniCal_NavigateActivities_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        $activity_id = $request->get('activity_id');
        $direction = $request->get('direction');
        if($direction == 'next')
            echo $activity_id;
//            echo $this->GetNextActivity($activity_id, 'Meeting');
        if($direction == 'prev')
            echo $activity_id;
//            echo $this->GetPrevActivity($activity_id, 'Meeting');
    }
    
    public function GetPrevActivity($activity_id, $activity_type){
        global $adb;
        $currentUserModel = Users_Record_Model::getCurrentUserModel();            
        $list = Vtiger_ListView_Model::GetInstance("Calendar", $_COOKIE['calendar_view_id']);
        $query = $list->getQuery();
        $query .= " AND vtiger_activity.activityid = ? AND vtiger_activity.activitytype = ? ORDER BY activityid DESC ";
        $result = $adb->pquery($query, array($activity_id, $activity_type));
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'activityid');
        return 0;
    }
    
    public function GetNextActivity($activity_id, $activity_type){
        global $adb;
        $currentUserModel = Users_Record_Model::getCurrentUserModel();            
        $list = Vtiger_ListView_Model::GetInstance("Calendar", $_COOKIE['calendar_view_id']);
        $query = $list->getQuery();
        $query .= " AND vtiger_activity.activityid = ? AND vtiger_activity.activitytype = ? LIMIT 1";
        $result = $adb->pquery($query, array($activity_id, $activity_type));
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'activityid');
        return 0;

/*        if($currentUserModel->isAdminUser() || $sub_admin->HasSubAdminAccess("Calendar") == "yes"){
            return $this->GetAdminActivityNext($activity_id, 'Meeting');                
        }
        else{
            return $this->GetNonAdminActivityNext($activity_id, 'Meeting');
        }

/*        
        require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

        foreach($PortfolioInformation_share_read_permission['GROUP'] AS $groups => $users){
            foreach($users AS $k => $v)
                $related_ids[] = $v;
            $related_ids[] = $groups;
        }
        $related_ids[] = $current_user->id;//Always at least give the current user ID
        $questions = generateQuestionMarks($related_ids);
        $query = "SELECT SUM(total_value) AS total_value, SUM(market_value) AS market_value, SUM(cash_value) AS cash_value, SUM(annual_management_fee) AS annual_management_fee 
                  FROM vtiger_portfolioinformation vpi
                  JOIN vtiger_portfolioinformationcf cf ON cf.portfolioinformationid = vpi.portfolioinformationid
                  LEFT JOIN vtiger_crmentity e ON e.crmid = vpi.portfolioinformationid 
                  WHERE e.smownerid IN ({$questions})
                  AND e.deleted = 0";*/
    }
        
    public function GetAdminActivityNext($activity_id, $activity_type){
        global $adb;
        $query = "SELECT activityid FROM vtiger_activity WHERE activitytype = ? AND activityid > ? LIMIT 1";
        $result = $adb->pquery($query, array($activity_type, $activity_id));
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'activityid');
    }
    
    public function GetNonAdminActivityNext($activity_id, $activity_type){
        
    }
}

?>
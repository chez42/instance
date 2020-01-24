<?php

class OmniCal_GetActivityInfo_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        $activity_id = $request->get('activity_id');
        $activity = new OmniCal_Activity_Model();
        $data = $activity->GetActivityData($activity_id, null, $request);
        $data['description'] = html_entity_decode($data['description']);
        $data['contact_list'] = $activity->GetActivityContacts($activity_id);
        echo json_encode($data);
    }
    
    static public function GetRelatedContacts($record){
        global $adb;
        $contact_info = array();
        $query = "SELECT cd.contactid, cd.lastname, cd.firstname
                  FROM vtiger_cntactivityrel ar
                  JOIN vtiger_contactdetails cd ON cd.contactid = ar.contactid
                  WHERE ar.activityid = ?";
        $result = $adb->pquery($query, array($record));
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){                
                $contact_info[] = $v;
            }
        }
        return $contact_info;
    }
}

?>

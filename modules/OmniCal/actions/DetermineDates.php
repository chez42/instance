<?php
class OmniCal_DetermineDates_Action extends Vtiger_Action_Controller{
    public function DetermineTimes($type, &$start_date, &$end_date, &$start_time, &$end_time){
        $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $start = strtotime($start_date . " " . $start_time);
        $end = strtotime($end_date . " " . $end_time);
        if($start > $end){
            switch($type){
                case 'Call':
                    $time = $userPrivilegesModel->get('callduration');
                    break;
                case 'Meeting':
                    $time = $userPrivilegesModel->get('othereventduration');
                    break;
                default:
                    $time = $time = $userPrivilegesModel->get('othereventduration');
                    break;
            }
            $end = $start+(60*$time);
            $end_date = date('m/d/Y', $end);
            $end_time = date('h:i A', $end);
        }
    }
    
    public function process(\Vtiger_Request $request) {
        $start_date = $request->get('start_date');
        $start_time = $request->get('start_time');
        $end_date = $request->get('end_date');
        $end_time = $request->get('end_time');
        $type = $request->get('activity_type');

        $this->DetermineTimes($type, $start_date, $end_date, $start_time, $end_time);
        $date_time = array('start_date' => $start_date,
                           'start_time' => $start_time,
                           'end_date' => $end_date,
                           'end_time' => $end_time);
        echo json_encode($date_time);
    }
}

?>
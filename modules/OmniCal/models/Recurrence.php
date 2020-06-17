<?php
define("RECCURENCE_TYPE", 0);
define("END_RECURRENCE", 1);
/*define("DAILY", array("Interval"=>null));
define("WEEKLY", array("Interval"=>null, "DaysOfWeek"=>null));
define("MONTHLY", array("Interval"=>null, "DayOfMonth"=>null));
define("YEARLY", array("Interval"=>null, "DayOfMonth"=>null, "Month"=>null));*/

class OmniCal_Recurrence_Model extends Vtiger_Module{
    public $recurrenceType;
    public $startDate, $endDate, $startDateTime;
    public $interval_type;
    public $endRecurrenceType;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function FillRecurrence($recurrence){
//        print_r($recurrence);
        $recurrence_info = array();
        foreach($recurrence AS $k => $v){
            $recurrence_info[] = array("parent"=>$k, "child"=>$v);
        }
        $this->recurrenceType = $recurrence_info[RECCURENCE_TYPE]['parent'];
        $this->interval_type = $recurrence_info[RECCURENCE_TYPE]['child'];
        $this->endRecurrenceType = $recurrence_info[END_RECURRENCE]['parent'];
        $this->startDate = $recurrence_info[END_RECURRENCE]['child']->StartDate;
        $this->endDate = $recurrence_info[END_RECURRENCE]['child']->EndDate;
    }
}

?>
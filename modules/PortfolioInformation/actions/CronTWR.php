<?php
require_once "libraries/reports/new/nTWR.php";
require_once "libraries/reports/new/nCommon.php";

class PortfolioInformation_CronTWR_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        global $adb;
        set_time_limit (0);
//        nTWR::CreateIntervals('678-323748', '1900-01-01');
        $dates = nTWR::GetStoppingDates('678-323748');
        foreach($dates AS $k => $v){
            $value = nTWR::GetIntervalAccountTotal('678-323748', $v);
            nTWR::WriteIntervalTotal('678-323748', $v, $value);
        }
        
        echo "All done in the sun";
    }
}
?>

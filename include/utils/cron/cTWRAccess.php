<?php
require_once("include/utils/cron/cTransactionsAccess.php");
require_once("include/utils/cron/cPortfolioAccess.php");

class cTWRAccess{
    public function __construct() {
        
    }
    
    /**
     * Get all months for the portfolio id (inception to now)
     * @param type $pids
     * @return array
     */
    public function GetMonths($pids){
        $t = new cTransactionsAccess();
        $sdate = $t->GetInceptionDate($pids);
        $start = new DateTime($sdate);
        $start->modify('first day of this month');
        $edate = Date("Y-m-d");
        $last_month = new DateTime($edate);
        $last_month->modify('last day of last month');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($start, $interval, $last_month);
        $months = array();
        foreach ($period as $dt) {
            $mstart = $dt->format("Y-m-d");//First of the month
            $mend = $dt->format("Y-m-t");//Last of the month
            $months[] = array("start" => array("date" => $mstart, "value" => 0),
                              "end" => array("date" => $mend, "value" => 0));
        }
        $today = Date("Y-m-d");
        if($today != Date("Y-m-01")){
            $mstart = Date("Y-m-01");
            $mend = $today;
            $months[] = array("start" => array("date" => $mstart, "value" => 0),
                              "end" => array("date" => $mend, "value" => 0));
        }else{
            $months[] = array("start" => array("date" => $today, "value" => 0),
                              "end" => array("date" => $today, "value" => 0));
        }
        
        return $months;
    }
    
    public function CalculateIntervals($pids, $months){
        $paccess = new cPortfolioAccess();
        $first = 1;
        $last_value = 0;
        foreach($months AS $k => $v){
            if($first == 1){
                $first = 0;
                $last_value = 0;
                $start_value = 0;
            }
            else 
                $start_value = $last_value;
            $end_value = $paccess->GetPortfolioValueAsOfDate($pids, $v['end']['date']);
            $last_value = $end_value;

            $months[$k]['start']['value'] = $start_value;
            $months[$k]['end']['value'] = $end_value;
            $months[$k]['flow'] = $paccess->GetFlowDataAsOfDates($pids, $v['start']['date'], $v['end']['date']);
            $flow = $months[$k]['flow'];
            if($start_value == 0)
                $return = ( $end_value - $flow ) / $end_value;
            else
                $return = round(( $end_value - $flow - $start_value) / $start_value, 3);
            $months[$k]['return'] = $return;
            $months[$k]['pids'] = $pids;
//            echo $v['start']['date'] . " - {$start_value}, " . $v['end']['date'] . " - {$end_value}, FLOW: {$flow}, RETURN: {$return}<br />";            
        }
        
        return $months;
    }
    
    /**
     * Write to the intervals table
     * @param type $intervals
     */
    public function WriteIntervals($intervals){
        global $adb;
        $query = "INSERT INTO vtiger_twr_intervals (portfolio_id, interval_start, interval_end, start_value, end_value, flow_amount, return_amount)
                  VALUES (?, ?, ?, ?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE start_value = VALUES(start_value), end_value = VALUES(end_value), flow_amount = VALUES(flow_amount), return_amount = VALUES(return_amount)";
        foreach($intervals AS $k => $v){
            $adb->pquery($query, array($v['pids'],
                                       $v['start']['date'],
                                       $v['end']['date'],
                                       $v['start']['value'],
                                       $v['end']['value'],
                                       $v['flow'],
                                       $v['return']));
        }
    }
    
    public function CalculateReturn($start_date, $end_date, $pids){
        global $adb;
        $query = "SELECT * FROM "
               . "vtiger_twr_intervals WHERE interval_start >= ? AND interval_end <= ? "
               . "AND portfolio_id IN ({$pids}) ";
        $result = $adb->pquery($query, array($start_date, $end_date));
        if($adb->num_rows($result) > 0){
            $return = 1;
            foreach($result AS $k => $v){
                if($return == 0)
                    $return = 1;
                $return = $return * ($v['return_amount']+1);
            }

            $start = strtotime($start_date);
            $end = strtotime($end_date);

            $datediff = ceil(abs($end - $start) / 86400);
            $type = "";//The type of return, annualized or not
            if($datediff >= 365)
            {
                $exponent = 365/$datediff;

                $return = pow(($return), $exponent);
                $type = "Annualized";
            }

            $return = $return-1;
            $return *= 100;

            $return = round($return, 2);

            echo "RETURN: {$return}<br />";
        }
    }
}

?>
<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * cTWR will handle all TWR calculations.
 *
 * @author theshado
 */                 
                    
require_once("libraries/reports/cTransactions.php");

class cTWR extends cTransactions {
    /**
     * Construction creates a temporary table for quick information access
     * @global type $adb
     * @param type $pids 
     * @param type $special_instructions
     */         
    public function __construct() {
        parent::__construct();
                
        global $adb;
                
        $query = "drop table if exists t_TWR";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE t_TWR (
                        id INTEGER NOT NULL PRIMARY KEY,
                        pid int,
                        activity_id int,
                        quantity float,
                        total_value float,
                        net_amount float,
                        trade_date datetime,
                        principal float,
                        add_sub_status_type_id int,
                        cost_basis_adjustment float,
                        report_as_type_id int,
                        calculated_amount float,
                        money_id int,
                        accrued_interest float,
                        symbol_id int,
                        security_symbol VARCHAR(25),
                        description VARCHAR(250),
                        price_adjustment float,
                        account_number VARCHAR(50),
                        code_id int,
                        code_description VARCHAR(50),
                        security_type_id int,
                        activity_name VARCHAR(50),
                        transaction_description VARCHAR(150),
                        current_price float,
                        origination VARCHAR(50),
                        report_as_type_name VARCHAR(100),
                        security_factor float,
                        value float)";
        $adb->pquery($query, array());
    }
    
    /** 
     * Calculates the TWR for the given interval
     * @param type $sdate
     * @param type $edate
     * @param type $interval_values 
     * @param type $accounts
     * @param type $inception_date
     * @return type
     */  
    public function CalculateTWRUsingIntervals($sdate, $edate, $ends, $transaction_handler, $start_value = 0)
    {
        $last_date = $sdate;
        $current_date = $sdate;
        $last_value = $start_value;
        $current_value = 0;
        
        $isFirst = true;
////        echo "START VALUE: {$start_value}<br />";
        foreach($ends AS $k => $v)
        {
////            echo "SDATE: {$sdate}, K: {$k}, EDATE: {$edate}<br />";
            if($k >= $sdate && $k <= $edate)
            {
                if($last_value == 0)
                    $dbz = 1;
                else
                    $dbz = $last_value;

                $symbol_totals = $this->GetSymbolTotals($k, false, null);
                $current_value = $this->AddAllSymbolTotals($symbol_totals);
////                echo "CURRENT VALUE FOR {$k}, {$current_value}<br />";

                if($isFirst && $start_value == 0)
                {
                    $last_value = 0;
                    $isFirst = false;
                }
//                else
//                    $last_value = $current_value;

                $dbz = $last_value;//Used to be current_value, it must divide by last value not the current!
                if($dbz == 0) {
                	$t = 0;
					$dbz = 1;
				}else
                $t = ( $current_value - $ends[$k] - $last_value) / $dbz;

////                echo "T: ( {$current_value} - {$ends[$k]} - {$last_value}) / {$dbz} = {$t}<br />";//{$t}<br />";
                $last_value = $current_value;
                $tmpVal = round($t, 3);
                $r[] = $tmpVal;

                /*
                {
                    $dbz = $current_value;
//                    $last_value = $current_value;
                    $isInception = true;
                }
//                echo "CURRENT VALUE FOR: {$k}, {$current_value}<br />";
                $t = ( $current_value - $v - $last_value) / $dbz;
                if(!$isfirst)
                {
                    $tmpVal = round(($current_value - $v - $last_value) / $dbz, 3);
                    $r[] = $tmpVal;
                }
                else
                {
///                    echo "IS FIRST!<br />";
                    $isfirst = false;
                }*/
            }
        }

        $return = 1;
        foreach($r AS $k => $v)
        {
            $tmp = $v+1;
////                     echo "K: {$k}, V:{$v} ---- RETURN CALCULATION: RETURN = {$return} * {$tmp}<br />";
            if($return == 0)
                $return = $v;
            else
            {
                $tmp = $v+1;
                $return = $return * ($tmp);
            }

////                    echo "RETURN SO FAR: {$return}<br />";
        }
        $start = strtotime($sdate);
        $end = strtotime($edate);

        $datediff = ceil(abs($end - $start) / 86400);

        $type = "";//The type of return, annualized or not
        if($datediff >= 365)
        {
            $exponent = 365/$datediff;

    ///        echo "POWER CHECK: {$check}<br />";
    ///        echo "DATE DIFFERENCE {$edate} - {$sdate} = {$datediff} ---- Exponent: {$exponent}<br />";
    ///        echo "RETURN BEFORE ANNUALIZED: {$return}<br />";
///            echo "Annualized: {$sdate} - {$edate}<br />";
///            echo "RETURN: {$return}, EXPONENT: {$exponent}<br />";
            $return = pow(($return), $exponent);
            $type = "Annualized";
        }
        else
        {
            $type = "Monthly";
///            echo "Not Annualized: {$sdate} - {$edate}<br />";
        }
        $return = $return-1;
        $return *= 100;
        
        $return = round($return, 2);
//        echo "ANNUALIZED RETURN: {$return}<br />";
        return array("type"=>$type, "value"=>$return);
        
    }
/*    public function CalculateTWRUsingIntervals($sdate, $edate, $interval_values, $accounts, $inception_date)
    {           
        $sdate = str_replace("00:00:00", "", $sdate);
        $sdate = str_replace(" ", "", $sdate);
        $edate = str_replace("00:00:00", "", $edate);
        $edate = str_replace(" ", "", $edate);

        $last_date = $sdate;
        $current_date = $sdate;
        $last_value = 0;
        $current_value = 0;
        $r = array();
        $count = 0;
        if($sdate <= $inception_date)
        {
            $isfirst = true;
            $isInception = true;
        }       
        else
            $isfirst = false;
//        echo "START DATE: {$sdate}, END DATE: {$edate}<br />";
//        echo "THE ENDING VALUE IS: " . $this->GetIntervalValues($accounts, $sdate, $edate) . "<br />";
        $start_value = $this->GetIntervalValues($accounts, "1920-01-01", $sdate);
////        $start_value = $this->GetAccountTotalValueAsOfDate($sdate);


///        echo "START VALUE: {$start_value}<br />";
        $current_value = $start_value;
///        echo "START VALUE: {$current_value}<br />";
        if($interval_values)
        foreach($interval_values AS $k => $v)
        {
            if($k >= $sdate && $k <= $edate)
            {
///                echo "{$count} - DATE: <strong>{$k}</strong>, TRANSACTION AMOUNT: <strong>{$v}</strong><br />";
///                echo "SDATE {$sdate}, EDATE: {$k}<br />";
                $count++;
//                $transaction_amount = $v;
//                $values = $this->GetAccountSymbolValues($accounts, $sdate, $k, true);
//                $val = $this->CalculateTotalValues($values);
//                $va = $val['grand_totals']['grand_totals']['value'];
                $last_value = $current_value;
                if($last_value == 0)
                    $dbz = 1;//dbz stands for divide by zero.  In case last_value happens to equal 0, we won't have an issue when dividing.
                else
                    $dbz = $last_value;
                $current_value = $this->GetIntervalValues($accounts, "1920-01-01", $k);
//                $current_value = $this->GetAccountTotalValueAsOfDate($k);
                if(!$isInception)
                {
                    $dbz = $current_value;
//                    $last_value = $current_value;
                    $isInception = true;
                }
//                echo "CURRENT VALUE FOR: {$k}, {$current_value}<br />";
                $t = ( $current_value - $v - $last_value) / $dbz;
                if(!$isfirst)
                {
                    $tmpVal = round(($current_value - $v - $last_value) / $dbz, 3);
                    $r[] = $tmpVal;
                }
                else 
                {
///                    echo "IS FIRST!<br />";
                    $isfirst = false;
                }
    
//                echo "( ({$current_value} - {$v}) - {$last_value}) / {$dbz} = {$t}<br />";
            }
            session_write_close();
            session_start();
        }   
        $return = 1;
        foreach($r AS $k => $v)
        {
            $tmp = $v+1;
///             echo "K: {$k}, V:{$v} ---- RETURN CALCULATION: RETURN = {$return} * {$tmp}<br />";
            if($return == 0)
                $return = $v;
            else
            {
                $tmp = $v+1;
                $return = $return * ($tmp);
            }

///            echo "RETURN SO FAR: {$return}<br />";
        }

        $start = strtotime($sdate);
        $end = strtotime($edate);

        $datediff = ceil(abs($end - $start) / 86400);

        $type = "";//The type of return, annualized or not
        if($datediff >= 365)
        {
            $exponent = 365/$datediff;

    ///        echo "POWER CHECK: {$check}<br />";
    ///        echo "DATE DIFFERENCE {$edate} - {$sdate} = {$datediff} ---- Exponent: {$exponent}<br />";
    ///        echo "RETURN BEFORE ANNUALIZED: {$return}<br />";
///            echo "Annualized: {$sdate} - {$edate}<br />";
///            echo "RETURN: {$return}, EXPONENT: {$exponent}<br />";
            $return = pow(($return), $exponent);
            $type = "Annualized";
        }
        else
        {
            $type = "Monthly";
///            echo "Not Annualized: {$sdate} - {$edate}<br />";
        }
        $return = $return-1;
        $return *= 100;

        $return = round($return, 2);
///        echo "ANNUALIZED RETURN: {$return}<br />";
        return array("type"=>$type, "value"=>$return);
    }*/

    /**
     * Returns an array of the months between the two dates
     * @param type $date1
     * @param type $date2
     * @return type
     */
    function GetMonths($date1, $date2)
    {
        $count = 0;
        $date = array();

        $date1 = date("Y-m", strtotime($date1));
        while($date1 <= $date2)
        {
            $year = date("Y",strtotime($date1));
            $month = date("m",strtotime($date1));
            $date[] = "{$year}-{$month}-01";
            $date1 = date("Y-m", strtotime($date1 . "+1 month"));
        }

        return $date;
    }

    /**
     * This function determines the value at the end of the day before sdate and the value at the end of the day on edate.
     * It then determines the total transactions amount
     * @global type $adb
     * @param type $accounts
     * @param type $sdate
     * @param type $edate
     */
    public function GetIntervalValues($accounts='', $sdate, $edate)
    {
        global $adb;

        $start_total = $this->GetSymbolTotals($sdate);
        $end_total = $this->GetSymbolTotals($edate);
        $start_value = $this->AddAllSymbolTotals($start_total);
        $end_value = $this->AddAllSymbolTotals($end_total);
        $ends = $this->GetIntervalActivity($accounts, array("sdate"=>$sdate, "edate"=>$edate));

//        echo "Started With: {$start_value} ON {$sdate}<br />";
//        echo "Ended With: {$end_value} ON {$edate}<br />";
    }

    /**
     * Get the stopping points for the given accounts.  
     * Dates must be entered as an array of sdate=>x, edate=>y and must have values for the 'between' calculation if entered
     * If accounts and/or dates aren't entered, it uses the entire temporary transaction table.
     * @global type $adb
     * @param type $accounts
     * @param type $dates
     * @return type
     */
    public function GetStoppingPoints($accounts='', $dates=null, $threshold=0)
    {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        if($threshold)
            $threshold = " AND (value < -{$threshold} || value > {$threshold})";
        else
            $threshold = "";
        if($accounts)
            $accounts = " AND account_number IN ({$accounts}) ";
        if($dates)
            $between = " AND trade_date BETWEEN '{$dates['sdate']}' AND '{$dates['edate']}' ";
        $query = "SELECT * FROM t_transactions_{$current_user->get('id')}
                  WHERE (activity_id IN (10, 50, 60, 70, 80, 90, 120, 130, 140, 150, 160)
                  OR report_as_type_id IN (60, 70, 130)
                  OR (activity_id = 160 AND report_as_type_id = 80))
                  {$threshold}
                  {$accounts}
                  {$between}";
        $result = $adb->pquery($query, array());
        return $result;
    }
    /**
     * Determine the interval end dates.  Returned is the date with the total transactions amount on that day
     * Accounts must be passed in as comma separated strings -- IE:  $accounts = "'309','444'"
     * Dates is used to pass into the GetStoppingPoints function that is used inside of this one.  Same rules apply in that if used, it must have sdate=>x and edate=>y
     */
    public function GetIntervalActivity($accounts='', $dates=null, $threshold=0)
    {
        global $adb;
        $end_dates = array();
        $date_values = array();
        $totals = array();
        
        $result = $this->GetStoppingPoints($accounts, $dates, $threshold);
        
        while($v = $adb->fetchByAssoc($result))
            $end_dates[] = $v['trade_date'];//Assign the dates into an array
        $end_dates = array_unique($end_dates);//we only want unique dates
        sort($end_dates);
    
        foreach($end_dates AS $k => $v)
        {
            $and = " AND trade_date = '{$v}' ";
/*                     AND (activity_id IN (10, 50, 60, 70, 130, 140)
                     OR report_as_type_id IN (60, 70, 130)
                     OR (activity_id = 160 AND report_as_type_id = 80)) ";
*/      
            $symbol_totals = $this->GetSymbolTotals($v, false, $and);
            $total = $this->AddAllSymbolTotals($symbol_totals);
            if($total != 0)
                $totals[$v] = $total;
        }

        if(!$totals[$dates['edate']])
            $totals[$dates['edate']] = 0;
        return $totals;
/*  
         foreach($accounts AS $a => $b)
 
        {
            if(is_array($b))
            {
                foreach($b AS $k => $v)
                {
                $calculate = false;
//                    if($v['symbol_id'])
                    {
                        switch($v['activity_id'])
                        {
                            case 10:
//                              case 11:
//                              case 12:
                            case 50:
                            case 60:
                            case 130:
//                              case 120:
//                              case 70:
//                              case 140:
                            case 160:
                            {
                                if($v['activity_id'] == 160 && $v['report_as_type_id'] == 80)
                                    $calculate = true;
                                else
                                if($v['activity_id'] != 160)
                                    $calculate = true;
                            }break;
                        }
                        switch($v['report_as_type_id'])
                        {
//                            case 30:
//                            case 20:
                            case 60:
                            case 70:
                            case 130:
                            {
                                $calculate = true;
                            }
                        }
                        if($calculate)
                        {
                                if($v['trade_date'] != '0000-00-00')
                                {
                                    if($v['code_description'] == "Options")
                                        $v['current_price'] *= 100;
                                    $value = $v['quantity'] * $v['current_price'] * $v['price_adjustment'];
                                    if( ($v['security_symbol'] == "CASH") || ($v['security_type_id'] == 11))
                                        $value = $v['quantity'] * 1;

                                    if($v['security_factor'] > 0)
                                        $value *= $v['security_factor'];
    //                                echo "POSITIVE: DATE: {$v['trade_date']}, SPA: {$v['price_adjustment']}, QUANTITY: {$v['quantity']}, PRICE: {$v['current_price']}, SECURITY FACTOR: {$v['security_factor']} -- CBA: {$v['cost_basis_adjustment']}, MY VALUE: {$value}<br />";
if($v['id'] == 9155835)//This is hardcoded for the albury account... FIGURE THIS OUT!!  The issue is something to do with the KFT/KRFT merger
 $value = 3155.67;
                                    if($value != 0)//$value >= 1 || $value <= -1)
                                    {
                                        $dates[] = $v['trade_date'];
                                        {
///                                            echo "DESC: {$v['code_description']}, SYMBOL: {$v['security_symbol']}, DATE: {$v['trade_date']}, SPA: {$v['price_adjustment']}, QUANTITY: {$v['quantity']}, PRICE: {$v['current_price']}, SECURITY FACTOR: {$v['security_factor']} -- CBA: {$v['cost_basis_adjustment']}, MY VALUE: {$value}<br />";
                                            $date_values[$v['trade_date']][] = $value;
                                        }
                                    }
                                }
                        }
                    }
                }
            }
        }

        $dates = array_unique($dates);
        sort($dates);

        $dv = array();
        foreach($date_values AS $date => $t)
            foreach($t AS $k => $v)
            {
                $dv[$date]+=$v;
            }

        $date_values = array();
        foreach($dates AS $k => $v)
            $date_values[$v] = $dv[$v];

        return $date_values;*/
    }
}

?>
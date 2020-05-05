<?php
require_once("libraries/Reporting/Indexing.php");
require_once("libraries/Reporting/ProjectedIncomeModel.php");
require_once("libraries/Reporting/ReportCommonFunctions.php");

function DetermineIntervalStartDate($account_number, $sdate){
    global $adb;
    $questions = generateQuestionMarks($account_number);

    $query = "SELECT DATE_ADD(MAX(intervalbegindate), INTERVAL 1 DAY) AS begin_date
              FROM intervals_daily 
              WHERE accountnumber IN ({$questions}) AND intervalbegindate <= ?";
    $result = $adb->pquery($query, array($account_number, $sdate));
    if($adb->num_rows($result) > 0){
        $result = $adb->query_result($result, 0, 'begin_date');
        if(is_null($result))
            return $sdate;
        return $result;
    }
    return $sdate;
}

function DetermineIntervalEndDate($account_number, $edate){
    global $adb;
    $questions = generateQuestionMarks($account_number);

    $query = "SELECT MAX(intervalenddate) AS end_date
              FROM intervals_daily 
              WHERE accountnumber IN ({$questions}) AND intervalenddate <= ?";
    $result = $adb->pquery($query, array($account_number, $edate));
    if($adb->num_rows($result) > 0){
        $result = $adb->query_result($result, 0, 'end_date');
        if(is_null($result))
            return $edate;
        return $result;
    }
    return $edate;
}

class IndividualPerformance{
    public $account_number, $amount, $transaction_type, $transaction_activity, $operation, $buy_sell_indicator, $disable_performance;
}

class Performance{
    public $amount, $transaction_type, $transaction_activity, $operation, $buy_sell_indicator;
}

class AccountValues{
    public $account_number, $date, $value, $disable_performance;
}

class TransactionTypes{
    public $type, $activity;
}

class Performance_Model extends Vtiger_Module {
    private $isValid = false;//During construction, this determines if the accounts and dates have valid results returned.  If this remains false, no report can be generated
    private $beginning_date_altered = false;
    private $beginning_values, $ending_values;
    private $beginning_values_summed, $ending_values_summed;
    private $start_date_changed = false;
    private $end_date_changed = false;
    private $start_date, $end_date;
    private $performance;
    private $individual_performance_summed;
    private $individual_start_values, $individual_end_values;
    private $performance_summed;
    private $capital_appreciation, $individual_appreciation;
    private $appreciation_percent, $individual_appreciation_percent;
    private $transaction_types;
    private $intervals, $interval_begin_date, $interval_end_date;
    private $twr, $individual_twr, $irr, $individual_irr;
    private $account_numbers;
    private $dividend_accrual;
    private $benchmark;
    private $estimated_income;

    public function Performance_Model(array $account_numbers, $start_date, $end_date, $legacy=false){
        global $adb;
        $passed_in_date = $start_date;
        $this->account_numbers = $account_numbers;

        $questions = generateQuestionMarks($account_numbers);
        #$start_date = GetDateMinusOneDay($start_date);

        $this->beginning_values_summed = new AccountValues();
        $this->ending_values_summed = new AccountValues();

        $this->dividend_accrual = 0;

        //Get all rows with max intervalenddate <= to the date entered for the accounts
        $query = "CALL GET_INTERVAL_START_DATE_VALUES(\"{$questions}\", ?, ?)";
        $adb->pquery($query, array($account_numbers, $start_date, $end_date), true);
        $beginning_date_result = $adb->pquery("SELECT * FROM tmp_intervals_daily");//Contains all account first date values

#        echo $query . '<br />';
#        print_r($account_numbers);
#        echo '<br />' . $start_date . ' - ' . $end_date . '<br />';
#        exit;

        //Determine the absolute earliest date from all provided accounts.
        //This filters out accounts that were created after the first existing account
        $result = $adb->pquery("SELECT MIN(intervalenddate) as intervaldate FROM tmp_intervals_daily");
        if($adb->num_rows($result) == 0){
            echo "There has been an error determining the earliest interval date!";
            return;
        }
        $earliest_date = $adb->query_result($result, 0, 'intervaldate');//Earliest date is the earliest account date
        $earliest_start_date_result = $adb->pquery("SELECT IntervalBeginDate FROM tmp_intervals_daily WHERE IntervalEndDate = ?", array($earliest_date));//Earliest "start" date is the earliest accounts end of previous day
        $earliest_start_date = $adb->query_result($earliest_start_date_result, 0, 'intervalbegindate');

        $query = "CALL GET_INTERVAL_END_DATE_VALUES(\"{$questions}\", ?, ?)";
        $adb->pquery($query, array($account_numbers, $start_date, $end_date));
        $ending_date_result = $adb->pquery("SELECT * FROM tmp_intervals_daily ORDER BY intervalendvalue DESC");

        $query = "CALL CALCULATE_DIVIDEND_ACCRUAL(\"{$questions}\", ?)";
        $adb->pquery($query, array($account_numbers, $end_date));
        $accrual_result = $adb->pquery("SELECT SUM(dividend_accrual_amount) AS dividend_accrual_amount FROM DIVIDEND_ACCRUAL");

        if($adb->num_rows($accrual_result) > 0)
            $this->dividend_accrual = $adb->query_result($accrual_result, 0, 'dividend_accrual_amount');

        #IF intervalenddate == date entered, use interalbeginvalue ELSE use intervalendvalue as the starting value
        if($adb->num_rows($beginning_date_result) > 0 && $adb->num_rows($ending_date_result) > 0){//If we have info for both beginning and end dates
            while($v = $adb->fetchByAssoc($beginning_date_result)){//Loop through all beginning date results
                $set_zero = 0;
                $tmp = new AccountValues();//variables: Account Number, Date, Value, disable performance
                $tmp->account_number = $v['accountnumber'];

                if($v['intervalenddate'] == $earliest_date) {//If the accounts first end date <= the earliest account date we have
                    $tmp->value = $v['intervalbeginvalue'];//Set the value to the start value, and only if they are equal
                }

                if($earliest_date != $start_date){//This happens if a weekend or holiday is chosen as a start date
                    $this->start_date_changed = true;//Flag that we have changed the start date
                    $start_date = $earliest_date;//Set the new start date to be the earliest date we have in our table (which would be a monday if saturday/sunday were selected for example)
                }

                $this->start_date = $earliest_date;

//                $this->start_date = GetDatePlusOneDay($earliest_start_date);
/*                if($v['intervalenddate'] != $start_date){
                    $this->start_date_changed = true;//We had to change the interval start date because we don't know what the end date started with
                    $start_date = $v['intervalenddate'];//GetDatePlusOneDay($v['intervalenddate']);//We don't want to calculate transactions for the same day we know the ending value for
                    $tmp->date = $start_date;
                }*/
#                if($this->start_date > $start_date || strlen($this->start_date) < 1)//Set the start date to the smallest date available
#                    $this->start_date = $start_date;
                //$start_date is now set to the earliest date (the true start date)
                $tmp->date = $start_date;//Set the individual account start date to be the earliest date, even if in reality the account was opened 3 months later
                $this->beginning_values[] = $tmp;//Now set our beginning values array to be equal to the accounts determined beginning oject information

                $this->beginning_values_summed->date = $this->start_date;

                $individuals = new AccountValues();
                $individuals->account_number = $v['accountnumber'];
                $individuals->date = $v['intervalenddate'];
                $individuals->value = PortfolioInformation_Module_Model::GetIntervalBeginValueForDate($v['accountnumber'], $start_date);//$tmp->value;
                $individuals->disable_performance = PortfolioInformation_Module_Model::IsPerformanceDisabled($v['accountnumber']);
////                PortfolioInformation_Module_Model::CreateDailyIntervalsForAccounts($this->account_numbers, $v['intervalenddate']);
/*
                $query = "CALL GET_END_OF_DAY_VALUE_AS_OF_DATE(\"?\", ?)";
                $adb->pquery($query, array($v['accountnumber'], $v['intervalenddate']));

                $query = "SELECT * FROM tmp_intervals_daily";
                $r = $adb->pquery($query, array());
                if($adb->num_rows($r) > 0) {
                    $individuals->date = $adb->query_result($r, 0, 'intervalenddate');#$v['intervalbegindate'];
                    if($set_zero)
                        $individuals->value = 0;
                    else
                        $individuals->value = $adb->query_result($r, 0, 'intervalbeginvalue');#$v['intervalbeginvalue'];
                }else{
                    $date_override = 1;
                    $individuals->date = $v['intervalenddate'];
                    $individuals->value = 0;
                    if($this->start_date >= $v['intervalbegindate'])//Set the start date to the smallest date available
                        $override = GetDateMinusOneDay($this->start_date);
                }*/

                if($individuals->disable_performance != 1)
                    $this->beginning_values_summed->value += $individuals->value;//Only add to the grand total if performance is enabled

#                echo $v['accountnumber'] . ' -- ' . $v['intervalbeginvalue'] . "<br />";
                $this->individual_start_values[$v['accountnumber']] = $individuals;//This sets the individual account start information

#                echo "Adding: " . $this->beginning_values_summed->value . " for " . $v['accountnumber'] . "..." . $start_date . "<br />";
            }

            while($v = $adb->fetchByAssoc($ending_date_result)){
                $tmp = new AccountValues();
                $tmp->account_number = $v['account_number'];
                $tmp->date = $v['intervalenddate'];
                $tmp->value = $v['intervalendvalue'];
                $tmp->disable_performance = PortfolioInformation_Module_Model::IsPerformanceDisabled($v['accountnumber']);

                if($v['intervalenddate'] != $this->end_date){
                    $this->end_date_changed = true;//We had to change the interval end date because we don't know what the end ended with
                    $end_date = $v['intervalenddate'];//Set the end date to equal to the last end date for the account we are in the loop
                }

                // If an end date isn't already set, or the currently set "real end date" is less than the end date of the account we are on,
                // set the "real end date" to equal the account we are on
                if($this->end_date < $end_date || strlen($end_date) < 1)
                    $this->end_date = $end_date;

                $tmp->date = $this->end_date;//Set the account value object to equal the true end date

                $this->ending_values[] = $tmp;//Set the ending values for this account

                $this->ending_values_summed->date = $this->end_date;//Set the combined end date to equal the real end date
                if($tmp->disable_performance != 1)//If performance isn't disabled
                    $this->ending_values_summed->value += $tmp->value;//Add account value if we are supposed to (performance enabled)

                $individuals = new AccountValues();
                $individuals->account_number = $v['accountnumber'];
                $individuals->date = $v['intervalbegindate'];
                $individuals->value = $v['intervalendvalue'];
                $this->individual_end_values[$v['accountnumber']] = $individuals;//Set the individual account end data
            }

            $query = "CALL PERFORMANCE(\"{$questions}\", ?, ?)";
#            if($date_override)
#                $adb->pquery($query, array($account_numbers, $override, $this->end_date));
#            else

            $adb->pquery($query, array($account_numbers, $this->start_date, $this->end_date));
            $query = "UPDATE performance SET transaction_type = 'income_div_interest'
                      WHERE transaction_type = 'Income' 
                      AND (transaction_activity LIKE ('%dividend%') OR transaction_activity LIKE ('%interest%'));";
            $adb->pquery($query, array());
#            $adb->pquery($query, array($account_numbers, $this->start_date, $this->end_date));

            $query = "CALL INDIVIDUAL_PERFORMANCE(\"{$questions}\", ?, ?)";
#            if($date_override)
#                $adb->pquery($query, array($account_numbers, $override, $this->end_date));
#            else
                $adb->pquery($query, array($account_numbers, $this->start_date, $this->end_date));

            $query = "UPDATE individual_performance SET transaction_type = 'income_div_interest'
                      WHERE transaction_type = 'Income' 
                      AND (transaction_activity LIKE ('%dividend%') OR transaction_activity LIKE ('%interest%'));";
            $adb->pquery($query, array());

            $performance_result = $adb->query("SELECT * FROM performance");
//            if($adb->num_rows($performance_result) > 0){
                while($v = $adb->fetchByAssoc($performance_result)){
                    $tmp = new Performance();
                    $tmp->amount = $v['amount'];
                    $tmp->transaction_type = $v['transaction_type'];
                    $tmp->transaction_activity = $v['transaction_activity'];
                    $tmp->operation = $v['operation'];
                    $tmp->buy_sell_indicator = $v['buy_sell_indicator'];
                    $this->performance[$tmp->transaction_type][$tmp->transaction_activity] = $tmp;
                    $this->transaction_types[$tmp->transaction_type][] = $tmp->transaction_activity;
                }

                $query = "SELECT SUM(amount) AS amount, transaction_type, transaction_activity, trade_date, operation, buy_sell_indicator
                          FROM performance GROUP BY transaction_type";
                $result = $adb->pquery($query, array());

                if($adb->num_rows($result) > 0){
                    while($v = $adb->fetchByAssoc($result)){
                        $tmp = new Performance();
                        $tmp->amount = $v['amount'];
                        $tmp->transaction_type = $v['transaction_type'];
                        $tmp->transaction_activity = $v['transaction_activity'];
                        $tmp->operation = $v['operation'];
                        $tmp->buy_sell_indicator = $v['buy_sell_indicator'];
                        $this->performance_summed[$v['transaction_type']] = $tmp;
                    }
                }

                $query = "SELECT account_number, SUM(amount) AS amount, transaction_type, transaction_activity, trade_date, operation, buy_sell_indicator, disable_performance
                          FROM individual_performance GROUP BY account_number, transaction_type";
                $result = $adb->pquery($query, array());

                foreach($this->individual_end_values AS $k => $v){
                    $this->individual_performance_summed[$k] = '';
                }

                if($adb->num_rows($result) > 0){
                    while($v = $adb->fetchByAssoc($result)){
                        $tmp = new IndividualPerformance();
                        $tmp->account_number = $v['account_number'];
                        $tmp->amount = $v['amount'];
                        $tmp->transaction_type = $v['transaction_type'];
                        $tmp->transaction_activity = $v['transaction_activity'];
                        $tmp->operation = $v['operation'];
                        $tmp->buy_sell_indicator = $v['buy_sell_indicator'];
                        $tmp->disable_performance = $v['disable_performance'];
 #                       echo "SETTING: " . $v['account_number'] . '<br />';
                                $this->individual_performance_summed[$v['account_number']][$v['transaction_type']] = $tmp;
                        $this->individual_performance_summed[$v['account_number']]['account_name'] = PortfolioInformation_Module_Model::GetAccountNameFromAccountNumber($v['account_number']);
                        $this->individual_performance_summed[$v['account_number']]['account_type'] = PortfolioInformation_Module_Model::GetAccountTypeFromAccountNumber($v['account_number']);
                    }
                }
#foreach($this->individual_performance_summed AS $k => $v){
#    print_r($v);
#    echo "<br />";
#}

                $this->intervals = PortfolioInformation_Module_Model::GetIntervalsForAccounts($account_numbers);//Create combined accounts intervals
                $monthly_start = $start_date;
                $monthly_end = $end_date;
                PortfolioInformation_Module_Model::GetMonthlyIntervalDatesStartDate($monthly_start, $monthly_end);
                $this->interval_begin_date = $monthly_start;
                $this->interval_end_date = $monthly_end;

                $this->isValid = true;
#                $this->CalculateCapitalAppreciation();
#                $this->CalculateIndividualCapitalAppreciation();
                $this->CalculateInvestmentReturn();
                $this->CalculateIndividualInvestmentReturn();
                $this->CalculateIndividualChangeInValue();
                $this->CalculateIndividualEstimatedIncome();
                $this->CalculateIndividualTWRCumulative($this->start_date, $this->end_date);
                $this->CalculateCombinedTWRCumulative($this->start_date, $this->end_date);
//                $this->CalculateTWRFromIntervals($this->start_date, $this->end_date);
//                $this->CalculateIndividualTWR($this->start_date, $this->end_date);//Creates intervals for each individual account
//                $this->CalculateIndividualIRR($this->start_date, $this->end_date);
                $this->CalculateEstimatedIncome();
                $this->performance_summed['change_in_value'] = $this->ending_values_summed->value - $this->beginning_values_summed->value - $this->performance_summed['Flow']->amount - $this->performance_summed['Reversal']->amount + $this->GetCommissionAmount();
#            print_r($this->performance_summed);exit;
            }

//        }else{
 //           $this->isValid = false;
//        }
    }

    private function CalculateTWRFromIntervals($start_date, $end_date){
        global $adb;
        $query = "CALL TWR_CALCULATED(?, ?, @twr)";
        $adb->pquery($query, array($start_date, $end_date));

        $query = "SELECT @twr AS twr";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            $this->twr = $adb->query_result($result, 0, 'twr');
            return;
        }
        $this->twr = 0;
    }

    private function CalculateIndividualTWR($start_date, $end_date){
        global $adb;

        foreach($this->account_numbers AS $k => $v){
            PortfolioInformation_Module_Model::GetIntervalsForAccounts(array($v));

            $query = "CALL TWR_CALCULATED(?, ?, @twr)";
            $adb->pquery($query, array($start_date, $end_date));

            $query = "SELECT @twr AS twr";
            $result = $adb->pquery($query, array());
            if($adb->num_rows($result) > 0){
                $this->individual_twr[$v] = $adb->query_result($result, 0, 'twr');
            }else{
                $this->individual_twr[$v] = 0;
            }

            if($this->individual_twr[$v] >= 100){
                $this->individual_twr[$v] = 0;
            }
        }
    }


    public function CalculateIndividualTWRCumulative($start_date, $end_date){
        global $adb;

        foreach($this->account_numbers AS $k => $v){
            $questions = generateQuestionMarks($v);
            $twr = 1;
            $query = "CALL CALCULATE_INTERVALS_FROM_DAILY_COMBINED(\"{$questions}\", ?, ?)";
            $adb->pquery($query, array($v, $start_date, $end_date), true);
            $query = "SELECT * FROM tmpDailyPreTWR";
            $result = $adb->pquery($query, array());

            if($adb->num_rows($result) > 0){
                while($r = $adb->fetchByAssoc($result)){
                    $twr *= $r['netreturnamount'];
                }
                $this->individual_twr[$v] = ($twr - 1) * 100;//$adb->query_result($result, 0, 'twr');
            }else{
                $this->individual_twr[$v] = 0;
            }

            if($this->individual_twr[$v] >= 100 || $this->individual_twr[$v] <= -100){
                $this->individual_twr[$v] = 0;
            }

        }
    }

/*    public function CalculateIndividualTWRCumulative($start_date, $end_date){
        global $adb;

        foreach($this->account_numbers AS $k => $v){
#            $query = "CALL CALCULATE_DAILY_INTERVALS_LOOP(\"942826345\", \"2019-01-01\", \"2020-02-11\", \"TD\", \"live_omniscient\");"
            $query = 'CALL CALCULATE_MONTHLY_INTERVALS_FROM_DAILY("?", ?, ?)';
            $adb->pquery($query, array($v, $start_date, $end_date));
            $query = "SELECT SUM(twr) AS twr
                      FROM tmpMonthlyTWR";
            $result = $adb->pquery($query, array());
            if($adb->num_rows($result) > 0){
                $this->individual_twr[$v] = $adb->query_result($result, 0, 'twr');
            }else{
                $this->individual_twr[$v] = 0;
            }
        }
    }*/

    public function CalculateCombinedTWRCumulative($start_date, $end_date){
        global $adb;
        $twr = 1;

        $questions = generateQuestionMarks($this->account_numbers);
        $query = "CALL CALCULATE_INTERVALS_FROM_DAILY_COMBINED(\"{$questions}\", ?, ?)";
//        $query = "CALL TWR_INTERVALS_CALCULATED(\"{$questions}\", ?, ?)";
        $adb->pquery($query, array($this->account_numbers, $start_date, $end_date), true);

        $query = "SELECT * FROM tmpDailyPreTWR";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
##                echo "({$v['intervalbegindate']} - {$v['intervalenddate']}) CALCULATING {$twr} *= {$v['netreturnamount']} equals ";
                $twr *= $v['netreturnamount'];
##                echo $twr . '<br />';
            }
            $this->twr = ($twr-1) * 100;//$adb->query_result($result, 0, 'twr');
##            echo "final twr is " . $this->twr . '<br />';
        }

        if($this->twr >= 100 || $this->twr <= -100){
            $this->twr = 0;
        }


        /*        $query = "SELECT SUM(twr) AS twr FROM tmpMonthlyTWR";
                $result = $adb->pquery($query, array(), true);
                if($adb->num_rows($result) > 0){
                    $this->twr = $adb->query_result($result, 0, 'twr');
                    return;
                }
                $this->twr = 0;
        /*        $summed = $this->ending_values_summed->value;
                $end_values = $this->GetIndividualEndValues();
                $individual_twr = $this->GetIndividualTWR();
                $return_total = 0;
                foreach($this->account_numbers AS $k => $v){
                    $end_values[$v]->weight = $end_values[$v]->value / $summed * 100;
                    $end_values[$v]->twr = $individual_twr[$v];
                    $end_values[$v]->return = ($end_values[$v]->twr/100) * $end_values[$v]->value;
                    $return_total += $end_values[$v]->return;
                }

                $this->twr = $return_total / $summed * 100;*/

    }

    private function CalculateIndividualIRR($start_date, $end_date){
        global $adb;

        foreach($this->account_numbers AS $k => $v){
            #$query = "CALL TWR_CALCULATED(?, ?, @twr)";
            $query = 'CALL IRR_TABLE("?", ?, ?, @beginValue, @endValue, @startDate, @numDays)';
            $adb->pquery($query, array($v, $start_date, $end_date));

            $query = "SELECT @beginValue, @endValue, @startDate, @numDays";
            $result = $adb->pquery($query, array());

            $query = 'CALL IRR_ESTIMATOR(@beginvalue, @endvalue, @numdays, 1000, @irr)';
            $result = $adb->pquery($query, array());

            $query = 'SELECT @irr AS irr';
            $result = $adb->pquery($query, array());

            if($adb->num_rows($result) > 0){
                $this->individual_irr[$v] = $adb->query_result($result, 0, 'irr');
            }else{
                $this->individual_irr[$v] = 0;
            }
        }
    }

    private function GetCommissionAmount($account_number = null){
        global $adb;
        $params = array();
        if($account_number) {
            $and = " WHERE account_number = ?";
            $params[] = $account_number;
        }
        $query = "SELECT SUM(commission) AS commission FROM individual_performance {$and}";
        $result = $adb->pquery($query, $params);
        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, 'commission');
        }
        return 0;
    }

    private function CalculateCapitalAppreciation(){
    $this->capital_appreciation = $this->ending_values_summed->value -
        ($this->beginning_values_summed->value +
            $this->performance_summed['Flow']->amount +
            $this->performance_summed['Expense']->amount +
            $this->performance_summed['Income']->amount);
    $this->appreciation_percent = $this->capital_appreciation / $this->ending_values_summed->value * 100;
}

    private function CalculateIndividualCapitalAppreciation(){
        foreach($this->account_numbers AS $k => $v){
            if($this->individual_performance_summed[$v]['Flow']->disable_performance != 1) {
                $tmp = $this->individual_start_values[$v]->value +
                    $this->individual_performance_summed[$v]['Flow']->amount +
                    $this->individual_performance_summed[$v]['Expense']->amount +
                    $this->individual_performance_summed[$v]['Income']->amount;
                /*                echo $this->individual_start_values[$v]->value . ' + ' . $this->individual_performance_summed[$v]['Flow']->amount . ' + ' .
                                     $this->individual_performance_summed[$v]['Expense']->amount . ' + ' . $this->individual_performance_summed[$v]['Income']->amount;
                                echo '<br />' . $this->individual_end_values[$v]->value . "<br />";*/
                $appreciation = $this->individual_end_values[$v]->value - $tmp;
                $this->individual_appreciation[$v] = $appreciation;
                $this->individual_appreciation_percent[$v] = $appreciation / $this->individual_end_values[$v]->value * 100;
            }
        }
    }

    private function CalculateEstimatedIncome(){
        $projected = new ProjectedIncome_Model($this->account_numbers);
        $calendar = CreateMonthlyCalendar($this->start_date, $this->end_date);
        $projected->CalculateMonthlyTotals($calendar);
        $this->estimated_income = $projected;
    }

    private function CalculateIndividualEstimatedIncome(){
        foreach($this->account_numbers AS $k => $v){
            $projected = new ProjectedIncome_Model(array($v));
            $calendar = CreateMonthlyCalendar($this->start_date, $this->end_date);
            $projected->CalculateMonthlyTotals($calendar);
            $this->individual_performance_summed[$v]['estimated'] = $projected;
        }
    }

    private function CalculateInvestmentReturn(){
        $this->capital_appreciation = $this->ending_values_summed->value -
            ($this->beginning_values_summed->value +
                $this->performance_summed['Flow']->amount +
                $this->performance_summed['Expense']->amount);
        $this->appreciation_percent = $this->capital_appreciation / $this->ending_values_summed->value * 100;
    }

    private function CalculateIndividualInvestmentReturn(){
        foreach($this->account_numbers AS $k => $v){
            if($this->individual_performance_summed[$v]['Flow']->disable_performance != 1) {
                $tmp = $this->individual_start_values[$v]->value +
                    $this->individual_performance_summed[$v]['Flow']->amount +
                    $this->individual_performance_summed[$v]['Expense']->amount;

                $appreciation = $this->individual_end_values[$v]->value - $tmp;
                $this->individual_appreciation[$v] = $appreciation;
                $this->individual_appreciation_percent[$v] = $appreciation / $this->individual_end_values[$v]->value * 100;
            }
        }
    }

    private function CalculateIndividualChangeInValue(){
        foreach($this->account_numbers AS $k => $v){
/*            echo $v . '<br />';
            echo $this->individual_end_values[$v]->value . ' (1) <br />';
            echo $this->individual_start_values[$v]->value . ' (2) <br />';
            echo $this->individual_performance_summed[$v]['Flow']->amount . ' (3) <br />';
            echo $this->individual_performance_summed[$v]['Reversal']->amount . ' (4) <br />';
            echo $this->GetCommissionAmount() . '(5)<br /><br />';*/
            $this->individual_performance_summed[$v]['change_in_value'] = $this->individual_end_values[$v]->value -
                                                                          $this->individual_start_values[$v]->value -
                                                                          $this->individual_performance_summed[$v]['Flow']->amount -
                                                                          $this->individual_performance_summed[$v]['Reversal']->amount +
                                                                          $this->GetCommissionAmount($v);
        }
    }

    public function GetCapitalAppreciation(){
        return $this->capital_appreciation;
    }

    public function IsReportValid(){
        return $this->isValid;
    }

    public function GetPerformance(){
        return $this->performance;
    }

    public function GetPerformanceSummed(){
        return $this->performance_summed;
    }

    public function WasStartDateChanged(){
        return $this->start_date_changed;
    }

    public function WasEndDateChanged(){
        return $this->end_date_changed;
    }

    public function GetBeginningValues(){
        return $this->beginning_values;
    }

    public function GetBeginningValuesSummed(){
        return $this->beginning_values_summed;
    }

    public function GetEndingValues(){
        return $this->ending_values;
    }

    public function GetEndingValuesSummed(){
        return $this->ending_values_summed;
    }

    /**
     * Get the date in given format.  IE:  %Y-%m-%d  with return Y-m-d format
     * @param $params
     * @return false|string
     */
    public function GetStartDateWithParams($params){
        return date($params, strtotime($this->start_date));
    }

    public function GetEndDateWithParams($params){
        return date($params, strtotime($this->end_date));
    }

    public function GetStartDate(){
        return date("Y-m-d", strtotime($this->start_date));
    }

    public function GetEndDate(){
        return date("Y-m-d", strtotime($this->end_date));
    }

    public function GetStartDateMDY(){
        return date("m/d/Y", strtotime($this->start_date));
    }

    public function GetEndDateMDY(){
        return date("m/d/Y", strtotime($this->end_date));
    }

    public function GetTransactionTypes(){
        return $this->transaction_types;
    }

    public function GetBeginningDateAltered(){
        return $this->beginning_date_altered;
    }

    public function GetIntervals(){
        return $this->intervals;
    }

    public function GetTWR(){
        return $this->twr;
    }

    public function GetIRR(){
        return $this->irr;
    }

    public function GetIndex($index){
        return getReferenceReturn($index,$this->start_date,$this->end_date);
    }

    public function GetIntervalBeginDate(){
        return $this->interval_begin_date;
    }

    public function GetIntervalEndDate(){
        return $this->interval_end_date;
    }

    public function GetAppreciationPercent(){
        return $this->appreciation_percent;
    }

    public function GetIndividualSummedBalance(){
        return $this->individual_performance_summed;
    }

    public function GetIndividualBeginValues(){
        return $this->individual_start_values;
    }

    public function GetIndividualEndValues(){
        return $this->individual_end_values;
    }

    public function GetIndividualCapitalAppreciation(){
        return $this->individual_appreciation;
    }

    public function GetIndividualCapitalAppreciationPercent(){
        return $this->individual_appreciation_percent;
    }

    public function GetIndividualTWR(){
        return $this->individual_twr;
    }

    public function GetIndividualIRR(){
        return $this->individual_irr;
    }

    public function GetEstimatedIncome(){
        return $this->estimated_income;
    }

    public function ConvertPieToBenchmark($pie){
        $tmp = array();
        foreach($pie AS $k => $v){
            $tmp[$v['title']] = $v['percentage'];
        }
        return $tmp;
    }
    public function SetBenchmark($stocks, $cash, $bonds){
        $s1 = $this->GetIndex("GSPC");// * $stocks / 100;
        $s2 = $this->GetIndex("DVG");// * $stocks / 100;
        $b1 = $this->GetIndex("SP500BDT");// * $bonds / 100;
        $b2 = $this->GetIndex("IDCOTCTR");// * $bonds / 100;

        $this->benchmark = ($s1 * 0.3) + ($s2 * 0.3) + ($b1 * 0.3) + ($b2 * 0.1);
#        $b = $this->GetIndex("AGG") * $bonds / 100;
#        $this->benchmark = $s + $b;
    }

    public function GetBenchmark(){
        return $this->benchmark;
    }

    public function GetDividendAccrualAmount(){
        return $this->dividend_accrual;
    }

}
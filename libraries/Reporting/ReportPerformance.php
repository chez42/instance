<?php
require_once("libraries/Reporting/Indexing.php");

function DetermineIntervalStartDate($account_number, $sdate){
    global $adb;
    $questions = generateQuestionMarks($account_number);

    $query = "SELECT DATE_ADD(MAX(intervalbegindate), INTERVAL 1 DAY) AS begin_date
              FROM intervals_daily 
              WHERE accountnumber IN ({$questions}) AND intervalbegindate <= ? AND intervaltype = 'monthly'";
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
              WHERE accountnumber IN ({$questions}) AND intervalenddate <= ? AND intervaltype = 'monthly'";
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
    private $twr, $individual_twr;
    private $account_numbers;
    private $dividend_accrual;
    private $benchmark;

    public function Performance_Model(array $account_numbers, $start_date, $end_date){
        global $adb;
        $passed_in_date = $start_date;
        $this->account_numbers = $account_numbers;

        $questions = generateQuestionMarks($account_numbers);
        #$start_date = GetDateMinusOneDay($start_date);

        $this->beginning_values_summed = new AccountValues();
        $this->ending_values_summed = new AccountValues();

        $this->dividend_accrual = 0;

        //Get all rows with max intervalenddate <= to the date entered for the accounts
        $query = "CALL GET_INTERVAL_START_DATE_VALUES_NOT_DAILY(\"{$questions}\", ?, ?)";
        $adb->pquery($query, array($account_numbers, $start_date, $end_date));
        $beginning_date_result = $adb->pquery("SELECT * FROM tmp_intervals_daily");
        if($adb->num_rows($beginning_date_result) == 0){
            $query = "CALL GET_INTERVAL_START_DATE_VALUES(\"{$questions}\", ?, ?)";
            $adb->pquery($query, array($account_numbers, $start_date, $end_date));
            $beginning_date_result = $adb->pquery("SELECT * FROM tmp_intervals_daily");
        }
#        echo $query . '<br />';
#        print_r($account_numbers);
#        echo '<br />' . $start_date . ' - ' . $end_date . '<br />';
#        exit;
        $result = $adb->pquery("SELECT MIN(intervalenddate) as intervaldate FROM tmp_intervals_daily");
        if($adb->num_rows($result) == 0){
            echo "There has been an error determining the earliest interval date!";
            return;
        }
        $earliest_date = $adb->query_result($result, 0, 'intervaldate');
        $earliest_start_date_result = $adb->pquery("SELECT IntervalBeginDate FROM tmp_intervals_daily WHERE IntervalEndDate = ?", array($earliest_date));
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
        if($adb->num_rows($beginning_date_result) > 0 && $adb->num_rows($ending_date_result) > 0){
            while($v = $adb->fetchByAssoc($beginning_date_result)){
                $set_zero = 0;
                $tmp = new AccountValues();
                $tmp->account_number = $v['accountnumber'];

                if($v['intervalenddate'] <= $earliest_date) {
                    $tmp->value = $v['intervalbeginvalue'];
                }

                if($earliest_date != $start_date){
                    $this->start_date_changed = true;
                    $start_date = $earliest_date;
#                    $tmp->date = $start_date;
                }

                $this->start_date = GetDatePlusOneDay($earliest_start_date);
/*                if($v['intervalenddate'] != $start_date){
                    $this->start_date_changed = true;//We had to change the interval start date because we don't know what the end date started with
                    $start_date = $v['intervalenddate'];//GetDatePlusOneDay($v['intervalenddate']);//We don't want to calculate transactions for the same day we know the ending value for
                    $tmp->date = $start_date;
                }*/
#                if($this->start_date > $start_date || strlen($this->start_date) < 1)//Set the start date to the smallest date available
#                    $this->start_date = $start_date;
                $tmp->date = $this->start_date;
                $this->beginning_values[] = $tmp;

                $this->beginning_values_summed->date = $this->start_date;

                $individuals = new AccountValues();
                $individuals->account_number = $v['accountnumber'];
                $individuals->date = $v['intervalenddate'];
                $individuals->value = $tmp->value;
                $individuals->disable_performance = PortfolioInformation_Module_Model::IsPerformanceDisabled($v['accountnumber']);
                PortfolioInformation_Module_Model::CreateDailyIntervalsForAccounts($this->account_numbers, $v['intervalenddate']);
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
                    $this->beginning_values_summed->value += $individuals->value;

#                echo $v['accountnumber'] . ' -- ' . $v['intervalbeginvalue'] . "<br />";
                $this->individual_start_values[$v['accountnumber']] = $individuals;

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
                    $end_date = $v['intervalenddate'];
                }

                if($this->end_date < $end_date || strlen($end_date) < 1)
                    $this->end_date = $end_date;

                $tmp->date = $this->end_date;

                $this->ending_values[] = $tmp;

                $this->ending_values_summed->date = $this->end_date;
                if($tmp->disable_performance != 1)
                    $this->ending_values_summed->value += $tmp->value;

                $individuals = new AccountValues();
                $individuals->account_number = $v['accountnumber'];
                $individuals->date = $v['intervalbegindate'];
                $individuals->value = $v['intervalendvalue'];
                $this->individual_end_values[$v['accountnumber']] = $individuals;
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
                        $this->individual_performance_summed[$v['account_number']][$v['transaction_type']] = $tmp;
                        $this->individual_performance_summed[$v['account_number']]['account_name'] = PortfolioInformation_Module_Model::GetAccountNameFromAccountNumber($v['account_number']);
                    }
                }


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
                $this->CalculateTWRFromIntervals($this->start_date, $this->end_date);
                $this->CalculateIndividualTWR($this->start_date, $this->end_date);//Creates intervals for each individual account

                $this->performance_summed['change_in_value'] = $this->ending_values_summed->value - $this->beginning_values_summed->value - $this->performance_summed['Flow']->amount;
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
        }
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
            $this->individual_performance_summed[$v]['change_in_value'] = $this->individual_end_values[$v]->value -
                                                                          $this->individual_start_values[$v]->value -
                                                                          $this->individual_performance_summed[$v]['Flow']->amount;
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

    public function GetStartDate(){
        return date("F Y", strtotime($this->start_date));
    }

    public function GetEndDate(){
        return date("F Y", strtotime($this->end_date));
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

    public function ConvertPieToBenchmark($pie){
        $tmp = array();
        foreach($pie AS $k => $v){
            $tmp[$v['title']] = $v['percentage'];
        }
        return $tmp;
    }
    public function SetBenchmark($stocks, $cash, $bonds){
        $s = $this->GetIndex("S&P 500") * $stocks / 100;
        $b = $this->GetIndex("AGG") * $bonds / 100;
        $this->benchmark = $s + $b;
    }

    public function GetBenchmark(){
        return $this->benchmark;
    }

    public function GetDividendAccrualAmount(){
        return $this->dividend_accrual;
    }

}
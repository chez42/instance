<?php
#include_once "libraries/Reporting/Reporting.php";

class cPerformanceCombined extends cPerformance{
    public $account;
    public $start_date, $start_balance, $start_accounts;
    public $end_date, $end_balance, $end_accounts;

    public function __construct(array $account_number, $sdate, $edate){
        $transaction_types = array();
        $transaction_activities = array();
        foreach($account_number AS $k => $v){
            $tmp = new cPerformance($v, $sdate, $edate);
            $this->account[$v] = $tmp;
            $transaction_types[] = $tmp->GetAllTransactionTypes();
            $transaction_activities[] = $tmp->GetAllTransactionActivities();
        }

        $this->start_date = $this->DetermineEarliestDate();
        $this->end_date = $this->DetermineLastDate();
        $this->start_balance = $this->DetermineStartValue();
        $this->end_balance = $this->DetermineEndValue();
        $this->transaction_types = $this->GetUniqueArrayValues($transaction_types);
        $this->transaction_activities = $this->GetUniqueArrayValues($transaction_activities);
    }

    /**
     * Takes the multi-demensional array, makes its values unique and returns the final array result
     * @param array $data
     * @return array
     */
    protected function GetUniqueArrayValues(array $data){
        $pids = array();
        foreach ($data AS $k => $v) {
            $pids[] = $v;
        }
        return array_unique($pids);
    }

    protected function CalculateInvestmentReturn(){
        $this->investment_return = 0;
        foreach($this->account AS $k => $v){
            $this->investment_return += $v->GetInvestmentReturn();
        }
    }

    protected function DetermineEarliestDate(){
        $tmp_date = null;
        foreach($this->account AS $k => $v){
            if(is_null($tmp_date))
                $tmp_date = $v->GetStartInfo()->end_of_day_date;
            else{
                if(strtotime($v->GetStartInfo()->end_of_day_date) < strtotime($tmp_date) && $v->GetStartInfo()->end_of_day_date != '')
                    $tmp_date = $v->GetStartInfo()->end_of_day_date;
            }
        }
        return $tmp_date;
    }

    protected function DetermineLastDate(){
        $tmp_date = null;
        foreach($this->account AS $k => $v){
            if(is_null($tmp_date))
                $tmp_date = $v->GetEndInfo()->end_of_day_date;
            else{
                if(strtotime($v->GetEndInfo()->end_of_day_date) > strtotime($tmp_date) && $v->GetEndInfo()->end_of_day_date != '')
                    $tmp_date = $v->GetEndInfo()->end_of_day_date;
            }
        }
        return $tmp_date;
    }

    protected function DetermineStartValue(){
        $value = 0;
        foreach($this->account AS $k => $v){
            if($v->GetStartInfo()->end_of_day_date == $this->start_date)
                $value += $v->GetStartInfo()->start_value;
        }
        return $value;
    }

    protected function DetermineEndValue(){
        $value = 0;
        foreach($this->account AS $k => $v){
            if($v->GetEndInfo()->end_of_day_date == $this->end_date)
                $value += $v->GetEndInfo()->end_value;
        }
        return $value;
    }

    /**
     * Returns the sum of all values of given transaction type
     * @param $type
     * @return int
     */
    public function GetSumOfTransactionType(array $type){
        $amount = 0;
        foreach($this->account AS $k => $v){
            $amount += $v->GetSumOfTransactionType($type);
        }
        return $amount;
    }

    /**
     * Returns the sum of all values of given transaction activity
     * @param $activity
     * @return int
     */
    public function GetSumOfTransactionActivity(array $activity){
        $amount = 0;
        foreach($this->account AS $k => $v){
            $amount += $v->GetSumOfTransactionActivity($activity);
        }
        return $amount;
    }

    /**
     * Returns the sum of all values of given transaction activity
     * @param $activity
     * @return int
     */
    public function GetSumOfTransactionTypeAndActivity($type, array $activity){
        $amount = 0;
        foreach($this->account AS $k => $v){
            $amount += $v->GetSumOfTransactionTypeAndActivity($type, $activity);
        }
        return $amount;
    }

    /**
     * Returns the sum of all values of given transaction activity
     * @param $activity
     * @return int
     */
    public function GetSumOfTransactionTypeIgnoringActivity($type, array $activity){
        $amount = 0;
        foreach($this->account AS $k => $v){
            $amount += $v->GetSumOfTransactionTypeIgnoringActivity($type, $activity);
        }
        return $amount;
    }
}
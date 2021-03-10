<?php

include_once "libraries/Reporting/Reporting.php";

class cAccountValues{
    public $account_number, $date, $value, $disable_performance;
}

class cPerformance{
    protected $account_number;
    protected $start_balance, $end_balance;
    protected $portfolio;
    private $transactionsPerformance;
    protected $transaction_types, $transaction_activities;

    protected $dividend_accrual;//This was a Fidelity specific procedure
    protected $investment_return, $investment_return_percent, $change_in_value;
    protected $commission;

    public function __construct($account_number, $sdate, $edate){
        $this->account_number = $account_number;

        $portfolio_id = PortfolioInformation_Module_Model::GetRecordIDFromAccountNumber($account_number);
        $this->portfolio = Vtiger_Record_Model::getInstanceById($portfolio_id);

        $this->start_balance = new cBalanceInfo($account_number, $sdate);
        $this->end_balance = new cBalanceInfo($account_number, $edate);

        $this->transactionsPerformance = GetTransactionsPerformanceData($account_number, $sdate, $edate);
        $this->SetDividendAccrual();

        foreach($this->transactionsPerformance AS $k => $v){
            if(!in_array($v->transaction_type, $this->transaction_types))
                $this->transaction_types[] = $v->transaction_type;

            if(!in_array($v->transaction_activity, $this->transaction_activities))
                $this->transaction_activities[] = $v->transaction_activity;
#print_r($v);echo '<br /><br />';
            $this->commission += $v->commission;
        }

        $this->CalculateInvestmentReturn();
        $this->CalculateChangeInValue();
    }

    protected function Initialize(){
        $this->account_number = null;
        $this->start_balance = null;
        $this->end_balance = null;
        $this->portfolio = null;
        $this->dividend_accrual = 0;
    }

    public function GetStartInfo(){
        return $this->start_balance;
    }

    public function GetEndInfo(){
        return $this->end_balance;
    }

    public function GetDividendAccrual(){
        return $this->dividend_accrual;
    }

    private function SetDividendAccrual(){
        global $adb;
        $query = "SELECT account_number, dividend_accrual
		          FROM custodian_omniscient.custodian_balances_fidelity 
		          WHERE account_number = ?
                  AND as_of_date = ?";
        $result = $adb->pquery($query, array($this->account_number, $this->end_balance->end_of_day_date));
        if($adb->num_rows($result) > 0){
            $this->dividend_accrual = $adb->query_result($result, 0, 'dividend_accrual');
        }
        $this->dividend_accrual = 0;
    }

    protected function CalculateInvestmentReturn(){
/*        if($this->individual_performance_summed[$v]['Flow']->disable_performance != 1) {
            $tmp = $this->individual_start_values[$v]->value +
                $this->individual_performance_summed[$v]['Flow']->amount +
                $this->individual_performance_summed[$v]['Expense']->amount;

            $appreciation = $this->individual_end_values[$v]->value - $tmp;
            $this->individual_appreciation[$v] = $appreciation;
            $this->individual_appreciation_percent[$v] = $appreciation / $this->individual_end_values[$v]->value * 100;
        }*/
        $tmp = $this->GetStartInfo()->start_value +
               $this->GetSumOfTransactionTypeIgnoringActivity('Flow', array('Payment in lieu')) +
               $this->GetSumOfTransactionTypeIgnoringActivity('Expense', array('Management Fee'));
        $this->investment_return = $this->GetEndInfo()->end_value - $tmp;
        $this->investment_return_percent = $this->investment_return / $this->GetEndInfo()->end_value * 100;
    }

    protected function CalculateChangeInValue(){
        $this->change_in_value = $this->GetEndInfo()->end_value -
                                 $this->GetStartInfo()->start_value -
                                 $this->GetSumOfTransactionTypeIgnoringActivity('Flow', array('Payment in lieu')) -
                                 $this->GetSumOfTransactionActivity(array('Management Fee')) +
                                 $this->GetCommission();
    }

    public function GetChangeInValue(){
        return $this->change_in_value;
    }

    public function GetCommission(){
        return $this->commission;
    }

    public function GetPerformance(){
        return $this->transactionsPerformance;
    }

    public function GetAllTransactionTypes(){
        return $this->transaction_types;
    }

    public function GetAllTransactionActivities(){
        return $this->transaction_activities;
    }

    public function GetInvestmentReturn(){
        return $this->investment_return;
    }

    public function GetInvestmentReturnPercent(){
        return $this->investment_return_percent;
    }

    /**
     * Returns the sum of all values of given transaction type
     * @param $type
     * @return int
     */
    public function GetSumOfTransactionType(array $type){
        $amount = 0;
        foreach($this->transactionsPerformance AS $k => $v){
            if(in_array(strtoupper($v->transaction_type), array_map('strtoupper', $type))){
                $amount += $v->amount;
            }
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
        foreach($this->transactionsPerformance AS $k => $v){
            if(in_array(strtoupper($v->transaction_activity), array_Map('strtoupper', $activity))){
                $amount += $v->amount;
            }
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
        foreach($this->transactionsPerformance AS $k => $v){
            if(strtoupper($v->transaction_type) == strtoupper($type) &&
               in_array(strtoupper($v->transaction_activity), array_map('strtoupper', $activity))){
                    $amount += $v->amount;
            }
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
        foreach($this->transactionsPerformance AS $k => $v){
            if(strtoupper($v->transaction_type) == strtoupper($type) &&
                !in_array(strtoupper($v->transaction_activity), array_map('strtoupper', $activity))){
                $amount += $v->amount;
            }
        }
        return $amount;
    }
}
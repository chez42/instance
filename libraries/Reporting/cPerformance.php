<?php

include_once "libraries/Reporting/Reporting.php";

class cAccountValues{
    public $account_number, $date, $value, $disable_performance;
}

class cPerformance{
    private $account_number;
    private $start_balance, $end_balance;//These
    private $portfolio;
    private $transactionsPerformance;
    private $transaction_types, $transaction_activities;

    private $dividend_accrual;//This was a Fidelity specific procedure

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
        }
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

    public function GetPerformance(){
        return $this->transactionsPerformance;
    }

    public function GetAllTransactionTypes(){
        return $this->transaction_types;
    }

    public function GetAllTransactionActivities(){
        return $this->transaction_activities;
    }

    /**
     * Returns the sum of all values of given transaction type
     * @param $type
     * @return int
     */
    public function GetSumOfTransactionType($type){
        $amount = 0;
        foreach($this->transactionsPerformance AS $k => $v){
            if(strtoupper($v->transaction_type) == strtoupper($type)){
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
    public function GetSumOfTransactionActivity($activity){
        $amount = 0;
        foreach($this->transactionsPerformance AS $k => $v){
            if(strtoupper($v->transaction_activities) == strtoupper($activity)){
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
    public function GetSumOfTransactionTypeAndActivity($type, $activity){
        $amount = 0;
        foreach($this->transactionsPerformance AS $k => $v){
            if(strtoupper($v->transaction_type) == strtoupper($type) &&
               strtoupper($v->transaction_activity) == strtoupper($activity)){
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
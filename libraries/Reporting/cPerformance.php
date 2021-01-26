<?php

include_once "libraries/Reporting/Reporting.php";

class cPerformance{
    private $account_number;
    private $start_balance, $end_balance;//These
    private $portfolio;
    private $transactionsPerformance;

    private $dividend_accrual;//This was a Fidelity specific procedure

    public function __construct($account_number, $sdate, $edate){
        $this->account_number = $account_number;
        $portfolio_id = PortfolioInformation_Module_Model::GetRecordIDFromAccountNumber($account_number);
        $this->portfolio = Vtiger_Record_Model::getInstanceById($portfolio_id);

        $tmp = new cBalanceInfo($account_number, $sdate);
        $this->start_balance = $tmp;

        $tmp = new cBalanceInfo($account_number, $edate);
        $this->end_balance = $tmp;

        $this->transactionsPerformance = GetTransactionsPerformanceData($account_number, $sdate, $edate);
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


}
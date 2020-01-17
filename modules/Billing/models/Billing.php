<?php

require_once("modules/Billing/models/Parent.php");

class Account{
    public function __construct($account_number, $start_amount, $end_amount)
    {
        $this->account_number = $account_number;
        $this->start_amount = $start_amount;
        $this->end_amount = $end_amount;
    }

    public $account_number, $start_amount, $end_amount;
}

class Billing extends BillingParent{
    private $billing_calculations;
    private $portfolios, $households, $contacts;//Array of record objects
    private $transactions;//Transactions fed from report itself.  Ranged for example only cares about flows
    private $accounts;

    public function __construct($account_numbers, $report_title = null, $client_name = null, $module_type = null, $start_period = null, $end_period = null)
    {
        $records = GetAccountDataRecords($account_numbers);
#       $this->transactions = GetTransactionRecords($account_numbers, array("transaction_type" => " = 'Flow'",
#                                                                           "trade_date" => " between '{$start_period}' AND '{$end_period}'"));
#        $this->transactions = GetTransactionRecords($account_numbers);


        foreach($account_numbers AS $k => $v){
            $accounts[$v] = new Account($v,
                                      $this->CalculateStartDateValue($v, $start_period, $end_period),
                                      $this->CalculateEndDateValue($v, $start_period, $end_period));
        }

        $this->start_amount = $this->CalculateStartDateValue($account_numbers, $start_period, $end_period);
        $this->end_amount = $this->CalculateEndDateValue($account_numbers, $start_period, $end_period);

        foreach($records AS $k => $v){
            $this->contacts[] = $v['contact'];
            $this->households[] = $v['household'];
            $this->portfolios[] = $v['portfolio'];
        }

        $this->accounts = $accounts;

        parent::__construct($report_title, $client_name, $module_type, $start_period, $end_period);
    }

    public function SetTransactions($transactions){
        $this->transactions = $transactions;
    }

    public function GetPortfolios(){
        return $this->portfolios;
    }

    public function &GetPortfoliosByRef(){
        return $this->portfolios;
    }

    public function GetHouseholds(){
        return $this->households;
    }

    public function &GetHouseholdsByRef(){
        return $this->households;
    }

    public function GetContacts(){
        return $this->contacts;
    }

    public function &GetContactsByRef(){
        return $this->contacts;
    }

    public function GetAccounts(){
        return $this->accounts;
    }

    public function GetAccountInfoByNumber($account_number){
        return $this->accounts[$account_number];
    }

    public function GetTransactions(){
        return $this->transactions;
    }

    public function &GetTransactionsByRef(){
        return $this->transactions;
    }

}
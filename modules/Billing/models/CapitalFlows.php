<?php

require_once("modules/Billing/models/Billing.php");

class Billing_CapitalFlows_Model extends Vtiger_DetailView_Model {
    private $transactions;

    /**
     * Billing_CapitalFlows_Model constructor.
     * Start is reference to flow date start
     * End is reference to the ending date we are calculating against (starting day in regards to ranged)
     * @param $account_numbers
     * @param $start
     * @param $end
     * @param array $values
     */
    public function __construct($account_numbers, $start, $end, $values = array())
    {
        $transactions = GetTransactionRecords($account_numbers, array("transaction_type" => " = 'Flow'",
                                                                      "trade_date" => " between '{$start}' AND '{$end}'"));
        foreach($transactions AS $k => $record){
            $data = $record->getData();
            $data['transaction_fraction'] = GetNumberOfDaysBetween($data['trade_date'], $start);//how many days in when the transaction hit
            $data['period_amount'] = GetNumberOfDaysBetween($start, $end);//Number of days in the calculation period
            $transactions[$k]->setData($data);
        }
        $this->transactions = $transactions;
        parent::__construct($values);
    }

    public function GetTransactions(){
        return $this->transactions;
    }

    public function &GetTransactionsByRef(){
        return $this->transactions;
    }
}

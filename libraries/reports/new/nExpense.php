<?php

class nExpense{
    public $amount;
    
    private $account_number;
    public function __construct($account_number) {
        $this->account_number = $account_number;
    }
    
    /**
     * Calculate the amount of the fee between 2 dates.  sdate and edate use BETWEEN $sdate AND $edate
     * @global type $adb
     * @param type $sdate
     * @param type $edate
     * @return type
     */
    public function CalculateAmount($sdate, $edate){
        global $adb;
        if (strpos($sdate,'NOW()') == false) {//Doesn't contain the word NOW, we must be using a specific date and need to surround it by quotes
            $sdate = "'" . $sdate . "'";
        }
        
        if (strpos($edate,'OW()') == false) {//Doesn't contain the word NOW, we must be using a specific date and need to surround it by quotes
            $edate = "'" . $edate . "'";//Had to remove the N from NOW() or else it returns true for some reason
        }
        
        $query = "SELECT SUM(cost_basis_adjustment) AS expense_amount 
                  FROM vtiger_pc_transactions t
                  JOIN vtiger_portfolios p ON p.portfolio_id = t.portfolio_id
                  WHERE p.portfolio_account_number = ?
                  AND t.trade_date BETWEEN {$sdate} AND {$edate}
                  AND t.activity_id = 160
                  AND t.report_as_type_id = 60";
        $result = $adb->pquery($query, array($this->account_number));
        if($adb->num_rows($result) > 0){
            $this->amount = $adb->query_result($result, 0, 'expense_amount');
        }
        return $this->amount;
    }
    //Get the fees amount for the account
    public function GetAmount(){
        return $this->amount;
    }
    
    //Set the amount manually
    public function SetAmount($amount){
        $this->amount = $amount;
    }
}

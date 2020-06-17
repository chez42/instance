<?php
require_once("libraries/Reporting/ReportCommonFunctions.php");

class IndividualIncome{
    public $account_number, $amount, $security_symbol, $security_name, $trade_date, $month, $year, $quantity, $interest_rate, $dividend_share;
}

class MonthlyValues{
    public $date, $value, $month, $year;
}

class Income_Model extends Vtiger_Module {
    private $isValid = false;//During construction, this determines if the accounts and dates have valid results returned.  If this remains false, no report can be generated
    private $all_monthly_income_values;
    private $all_individual_income_values;
    private $yearly_grand_total_for_symbol;
    private $monthly_grand_total;
    private $combined_income;
    private $income_graph;

    private $account_numbers;

    public function Income_Model(array $account_numbers){
        global $adb;
        $this->account_numbers = $account_numbers;

        $questions = generateQuestionMarks($account_numbers);

        $query = "CALL SPECIFIC_TRANSACTION_TYPES(\"{$questions}\", \"'Income'\", \"''\");";
        $adb->pquery($query, array($account_numbers));

        $this->all_individual_income_values = self::CalculateIndividualAccounts();
        $this->all_monthly_income_values = self::CalculateMonthlyTotals();
        $this->combined_income = self::CalculateCombinedSymbols();

    }

    static private function CalculateMonthlyTotals(){
        global $adb;
        $query = "SELECT monthly_total, DATE_FORMAT(trade_date, '%Y-%m') AS trade_date, DATE_FORMAT(trade_date, '%Y') AS year, DATE_FORMAT(trade_date, '%m') AS month FROM MONTHLY_TOTALS";
        $result = $adb->pquery($query, array());
        $toReturn = array();
        if($adb->num_rows($result) > 0) {
            while ($v = $adb->fetchByAssoc($result)) {
                $tmp = new MonthlyValues();
                $tmp->date = $v['trade_date'];
                $tmp->monthly_total = $v['monthly_total'];
                $tmp->year = $v['year'];
                $tmp->month = $v['month'];
                $toReturn[] = $tmp;
            }
        }
        return $toReturn;
    }

    static public function GetCombinedSymbolsForDates($start, $end){
        global $adb;
        $query = "SELECT UPPER(security_symbol) AS security_symbol, security_name, quantity, interest_rate, dividend_share, SUM(monthly_total) AS net_amount, DATE_FORMAT(trade_Date,'%Y-%m') AS trade_date, DATE_FORMAT(trade_date, '%Y') AS year, DATE_FORMAT(trade_date, '%b') AS month
                  FROM MONTHLY_TRANSACTIONS 
                  WHERE net_amount != 0 AND trade_date BETWEEN ? AND ?
                  GROUP BY security_symbol, month(trade_date), year(trade_date)";
        $result = $adb->pquery($query, array($start, $end));
        $toReturn = array();
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $tmp = new IndividualIncome();
                $tmp->account_number = 'Combined';
                $tmp->security_symbol = $v['security_symbol'];
                $tmp->security_name = $v['security_name'];
                $tmp->quantity = $v['quantity'];
                $tmp->interest_rate = $v['interest_rate'];
                $tmp->dividend_share = $v['dividend_share'];
                $tmp->amount = $v['net_amount'];
                $tmp->trade_date = $v['trade_date'];
                $tmp->month = $v['month'];
                $tmp->year = $v['year'];
                $toReturn[$v['security_symbol']][] = $tmp;
            }
        }

        return $toReturn;
    }

    public function CalculateCombineSymbolsYearEndToal($start, $end){
        global $adb;
        $query = "SELECT UPPER(security_symbol) AS security_symbol, SUM(monthly_total) AS net_amount
                  FROM MONTHLY_TRANSACTIONS 
                  WHERE trade_date BETWEEN ? AND ?
                  GROUP BY security_symbol";
        $result = $adb->pquery($query, array($start, $end));
        $toReturn = array();
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $this->yearly_grand_total_for_symbol[$v['security_symbol']] = $v['net_amount'];
                $toReturn[$v['security_symbol']] = $v['net_amount'];
            }
        }
        return $toReturn;
    }

    public function CalculateGrandTotal($start, $end){
        global $adb;
        $query = "SELECT SUM(monthly_total) AS grand_total
                  FROM MONTHLY_TRANSACTIONS 
                  WHERE trade_date BETWEEN ? AND ?";
        $result = $adb->pquery($query, array($start, $end));
        $toReturn = array();
        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, 'grand_total');
        }
        return 0;
    }

    static private function CalculateCombinedSymbols(){
        global $adb;
        $query = "SELECT UPPER(security_symbol) AS security_symbol, SUM(monthly_total) AS net_amount, DATE_FORMAT(trade_Date,'%Y-%m') AS trade_date, DATE_FORMAT(trade_date, '%Y') AS year, DATE_FORMAT(trade_date, '%M') AS month
                  FROM MONTHLY_TRANSACTIONS 
                  WHERE net_amount != 0
                  GROUP BY security_symbol, month(trade_date), year(trade_date)";
        $result = $adb->pquery($query, array());
        $toReturn = array();
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $tmp = new IndividualIncome();
                $tmp->account_number = 'Combined';
                $tmp->security_symbol = $v['security_symbol'];
                $tmp->amount = $v['net_amount'];
                $tmp->trade_date = $v['trade_date'];
                $tmp->month = $v['month'];
                $tmp->year = $v['year'];
                $toReturn[$v['security_symbol']][] = $tmp;
            }
        }
        return $toReturn;
    }

    static private function CalculateIndividualAccounts(){
        global $adb;
        $query = "SELECT account_number, UPPER(security_symbol) AS security_symbol, net_amount, DATE_FORMAT(trade_Date,'%Y-%m') AS trade_date, DATE_FORMAT(trade_date, '%Y') AS year, DATE_FORMAT(trade_date, '%M') AS month 
                  FROM MONTHLY_TRANSACTIONS WHERE net_amount != 0";
        $result = $adb->pquery($query, array());
        $toReturn = array();
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $tmp = new IndividualIncome();
                $tmp->account_number = $v['account_number'];
                $tmp->security_symbol = $v['security_symbol'];
                $tmp->amount = $v['net_amount'];
                $tmp->trade_date = $v['trade_date'];
                $tmp->month = $v['month'];
                $tmp->year = $v['year'];
                $toReturn[$v['account_number']][] = $tmp;
            }
        }
        return $toReturn;
    }

    public function GetMonthlyTotalForDates($start, $end){
        global $adb;
        $query = "SELECT monthly_total, DATE_FORMAT(trade_date, '%Y-%m') AS trade_date, DATE_FORMAT(trade_date, '%Y') AS year, DATE_FORMAT(trade_date, '%b') AS month FROM MONTHLY_TOTALS WHERE trade_date BETWEEN ? AND ?";
        $result = $adb->pquery($query, array($start, $end));
        $total = array();
        if($adb->num_rows($result) > 0) {
            while ($v = $adb->fetchByAssoc($result)) {
                $tmp = new MonthlyValues();
                $tmp->date = $v['trade_date'];
                $tmp->monthly_total = $v['monthly_total'];
                $tmp->year = $v['year'];
                $tmp->month = $v['month'];
                $total[] = $tmp;
            }
        }
        return $total;
    }

    public function GetIndividualIncomeForDates($start, $end){
        global $adb;
        $query = "SELECT account_number, UPPER(security_symbol) AS security_symbol, net_amount, DATE_FORMAT(trade_Date,'%Y-%m') AS trade_date, DATE_FORMAT(trade_date, '%Y') AS year, DATE_FORMAT(trade_date, '%M') AS month
                  FROM MONTHLY_TRANSACTIONS WHERE trade_date BETWEEN ? AND ? AND net_amount != 0";
        $result = $adb->pquery($query, array($start, $end));

        $individual = array();
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $tmp = new IndividualIncome();
                $tmp->account_number = $v['account_number'];
                $tmp->security_symbol = $v['security_symbol'];
                $tmp->amount = $v['net_amount'];
                $tmp->trade_date = $v['trade_date'];
                $tmp->year = $v['year'];
                $tmp->month = $v['month'];
                $individual[$v['account_number']][] = $tmp;
            }
        }

        return $individual;
    }

    /**This sets the income_graph to equal to the result.  It also returns the result should it be needed to be stored in other variables
     **/
    public function GenerateGraphForDates($start, $end){
        global $adb;
        $query = "SELECT monthly_total, DATE_FORMAT(trade_date, '%M, %Y') AS trade_date FROM MONTHLY_TOTALS WHERE trade_date BETWEEN ? AND ?";
        $result = $adb->pquery($query, array($start, $end));
        if($adb->num_rows($result) > 0) {
            while ($v = $adb->fetchByAssoc($result)) {
                $history[] = array("category"=>$v['trade_date'], "value"=>$v['monthly_total'], "open:"=>"$");
            }
        }
        $this->income_graph = $history;
        return $history;
    }

    public function GetMonthlyIncomeValues(){
        return $this->all_monthly_income_values;
    }

    public function GetIndividualAccounts(){
        return $this->all_individual_income_values;
    }

    public function GetCombinedIncome(){
        return $this->combined_income;
    }

}
<?php
require_once("libraries/Reporting/ReportCommonFunctions.php");

class IndividualProjectedIncome{
    public $account_number, $security_symbol, $security_name, $interest_rate, $quantity, $payment_date, $year_payment, $year, $month, $day, $maturity_date, $matures;
    public $estimate_payment_amount, $pay_frequency, $aclass, $ppy, $pay_dates;//ppy stands for number of payments per year
}

class PayDates{
    public $month, $day;
}

class MonthlyValues{
    public $date, $value, $month, $year;
}

class ProjectedIncome_Model extends Vtiger_Module {
    private $isValid = false;//During construction, this determines if the accounts and dates have valid results returned.  If this remains false, no report can be generated
    private $all_monthly_income_values;
    private $grand_total;
    private $all_individual_income_values;
    private $all_grouped_income_values;
    private $combined_income;
    private $income_graph;
    private $monthly_total;

    private $account_numbers;

    public function ProjectedIncome_Model(array $account_numbers){
        global $adb;
        $this->account_numbers = $account_numbers;

        $questions = generateQuestionMarks($account_numbers);

        $query = "CALL PROJECTED_INCOME(\"{$questions}\");";
        $adb->pquery($query, array($account_numbers));

        $this->all_individual_income_values = self::CalculateIndividualAccounts();
        $this->all_grouped_income_values = self::CalculateGroupedAccounts();
#        $this->all_monthly_income_values = self::CalculateMonthlyTotals();
#        $this->combined_income = self::CalculateCombinedSymbols();
    }

    static private function CalculateIndividualAccounts(){
        global $adb;
        $query = "SELECT account_number, UPPER(security_symbol) AS security_symbol, security_name, interest_rate, quantity, payment_date, DATE_FORMAT(payment_date, '%Y') AS year, DATE_FORMAT(payment_date, '%m') AS month, DATE_FORMAT(payment_date, '%d') AS day,
                         year_payment, estimatePaymentAmount, pay_frequency, aclass, ppy, maturity_date, matures
                  FROM PROJECTED_INCOME ORDER BY security_symbol ASC";
        $result = $adb->pquery($query, array());
        $toReturn = array();
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $tmp = new IndividualProjectedIncome();
                $tmp->account_number = $v['account_number'];
                $tmp->security_symbol = $v['security_symbol'];
                $tmp->security_name = $v['security_name'];
                $tmp->interest_rate = $v['interest_rate'];
                $tmp->quantity = $v['quantity'];
                $tmp->payment_date = $v['payment_date'];
                $tmp->year_payment = $v['year_payment'];
                $tmp->year = $v['year'];
                $tmp->month = $v['month'];
                $tmp->day = $v['day'];
                $tmp->estimate_payment_amount = $v['estimatepaymentamount'];
                $tmp->pay_frequency = $v['pay_frequency'];
                $tmp->aclass = $v['aclass'];
                $tmp->ppy = $v['ppy'];
                $tmp->maturity_date = $v['maturity_date'];
                $tmp->matures = $v['matures'];
                $tmp->pay_dates = self::DeterminePayDates($tmp->month, $tmp->day, $tmp->year, $tmp->ppy, $tmp->maturity_date, $tmp->matures);
                if($tmp->matures)//If it matures, it may have matured during the calculated time, so set the proper year end payment amount
                    $tmp->year_payment = $tmp->estimate_payment_amount * sizeof($tmp->pay_dates);
                $toReturn[$v['account_number']][] = $tmp;
            }
        }
        return $toReturn;
    }

    static private function CalculateGroupedAccounts(){
        global $adb;
        $query = "SELECT account_number, UPPER(security_symbol) AS security_symbol, security_name, interest_rate, SUM(quantity) AS quantity, payment_date, DATE_FORMAT(payment_date, '%Y') AS year, DATE_FORMAT(payment_date, '%m') AS month, DATE_FORMAT(payment_date, '%d') AS day,
                         SUM(year_payment) AS year_payment, SUM(estimatePaymentAmount) AS estimatePaymentAmount, pay_frequency, aclass, ppy, maturity_date, matures
                  FROM PROJECTED_INCOME GROUP BY security_symbol ORDER BY security_symbol ASC";
        $result = $adb->pquery($query, array());
        $toReturn = array();
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $tmp = new IndividualProjectedIncome();
                $tmp->account_number = $v['account_number'];
                $tmp->security_symbol = $v['security_symbol'];
                $tmp->security_name = $v['security_name'];
                $tmp->interest_rate = $v['interest_rate'];
                $tmp->quantity = $v['quantity'];
                $tmp->payment_date = $v['payment_date'];
                $tmp->year_payment = $v['year_payment'];
                $tmp->year = $v['year'];
                $tmp->month = $v['month'];
                $tmp->day = $v['day'];
                $tmp->estimate_payment_amount = $v['estimatepaymentamount'];
                $tmp->pay_frequency = $v['pay_frequency'];
                $tmp->aclass = $v['aclass'];
                $tmp->ppy = $v['ppy'];
                $tmp->maturity_date = $v['maturity_date'];
                $tmp->matures = $v['matures'];
                $tmp->pay_dates = self::DeterminePayDates($tmp->month, $tmp->day, $tmp->year, $tmp->ppy, $tmp->maturity_date, $tmp->matures);
                if($tmp->matures)//If it matures, it may have matured during the calculated time, so set the proper year end payment amount
                    $tmp->year_payment = $tmp->estimate_payment_amount * sizeof($tmp->pay_dates);
                $toReturn[$v['security_symbol']][] = $tmp;
            }
        }
        return $toReturn;
    }

    public function IsDateGreaterEqual($d1, $d2){
        if(is_null($d1) OR strlen($d1) == 0)
            $d1 = '0000-00-00';
        if(is_null($d2) OR strlen($d2) == 0)
            $d2 = '0000-00-00';

        $d1 = new DateTime($d1);
        $d2 = new DateTime($d2);

        if ($d1 >= $d2) {
            return true;
        }else{
            return false;
        }
    }
    /**
     * Using a known pay month and frequency, this function back tracks as far back as it can to January, then calculates all months moving upwards
     * @param $pay_month
     * @param $pay_day
     * @param $ppy
     * @return array
     */
    public function DeterminePayDates($pay_month, $pay_day, $pay_year, $ppy, $maturity_date, $matures){
        $dates = array();
        $tmp = 1;

        $interval_counter = 1;

        switch($ppy){
            case 2:
                $interval_counter = 6;
                break;
            case 4:
                $interval_counter = 3;
                break;
            case 1:
                $interval_counter = 12;
                break;
            default:
                $interval_counter = 1;
        }

        while( ($pay_month) > 0) {
            $tmp_month = $pay_month;
            $tmp_year = $pay_year;
            $pay_month -= $interval_counter;
        }

        $current_month = Date("m");
        $current_year = Date("Y");

        while( ($tmp_month ) <= 12){
            if($tmp_month < $current_month)
                $year = $current_year + 1;
            else
                $year = $current_year;

            $d = "{$year}-{$tmp_month}-{$pay_day}";

            if($matures == 1 && self::IsDateGreaterEqual($maturity_date, $d)) {
#                echo $maturity_date . ' vs ' . $d;
                $tmp_dates = new PayDates();
                $tmp_dates->month = $tmp_month;
                $tmp_dates->day = $pay_day;
                $dates[] = $tmp_dates;
            }else
            if($matures == 0){
                $tmp_dates = new PayDates();
                $tmp_dates->month = $tmp_month;
                $tmp_dates->day = $pay_day;
                $dates[] = $tmp_dates;
            }

            $tmp_month += $interval_counter;
        }

        return $dates;
    }

    public function GetMonthlyTotalForDates($start, $end){
        global $adb;
        $query = "SELECT monthly_total, DATE_FORMAT(trade_date, '%Y-%m') AS trade_date, DATE_FORMAT(trade_date, '%Y') AS year, DATE_FORMAT(trade_date, '%M') AS month FROM MONTHLY_TOTALS WHERE trade_date BETWEEN ? AND ?";
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

    public function GetIndividualAccounts(){
        return $this->all_individual_income_values;
    }

    public function GetGroupedAccounts(){
        return $this->all_grouped_income_values;
    }


    private function ConvertMonthlyTotalToGraph(){
        foreach($this->monthly_total AS $k => $v){
            $monthNum  = $k;
            $dateObj   = DateTime::createFromFormat('!m', $monthNum);
            $monthName = $dateObj->format('F');
            $this->income_graph[] = array("category"=>$monthName, "value"=>$v, "open:"=>"$");
        }
    }

    public function GetMonthlyIncomeGraph(){
        return $this->income_graph;
    }

    public function CalculateMonthlyTotals($calendar){
        $monthly_total = array();
        foreach($calendar AS $k => $v){
            $monthly_total[$v->month] = 0;//Sets the monthly order for the javascript chart
        }

        foreach($this->GetIndividualAccounts() AS $account_number => $holder){
            foreach ($holder AS $holder => $v){
                foreach($calendar AS $a => $month){
                    foreach($v->pay_dates AS $b => $pd){
                        if($pd->month == $month->month){
                            $monthly_total[$month->month] += $v->estimate_payment_amount;
                            $this->grand_total += $v->estimate_payment_amount;
                        }
                    }
                }
            }
        }
        $this->monthly_total = $monthly_total;
        $this->ConvertMonthlyTotalToGraph();
    }

    public function GetMonthlyTotals(){
        return $this->monthly_total;
    }

    public function GetGrandTotal(){
        return $this->grand_total;
    }

}
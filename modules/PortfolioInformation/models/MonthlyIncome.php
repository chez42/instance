<?php

require_once("include/utils/omniscientCustom.php");
require_once("libraries/reports/cTransactions.php");
require_once('libraries/reports/cPortfolioDetails.php');
require_once("libraries/reports/cPholdingsInfo.php");
require_once("libraries/reports/pdf/cPDFDBAccess.php");
require_once("libraries/reports/cReports.php");
require_once("libraries/reports/cPholdingsv2.php");

class PortfolioInformation_MonthlyIncome_Model extends Vtiger_Module {
    public $display_months = array();
    public $display_years_current = array();
    public $display_years_projected = array();
    public $monthly_values = array();
    public $monthly_totals = array();
    public $estimate_payout = array();
    public $estimated_monthly_totals = array();
    public $estimated_grand_total = array();
    public $grand_total;
    public $history;
    public $estimated_income;
    public $account;
    public $main_categories_previous = array(), $main_categories_projected = array();
    public $sub_sub_categories_previous = array(), $sub_sub_categories_projected = array();
    public $previous_symbols = array(), $previous_monthly_totals = array();
    public $projected_symbols = array(), $projected_monthly_totals = array();
    public $individual_previous_symbols = array(), $individual_projected_symbols = array();

    /**
     * Get individual symbols and their total
     * @global type $adb
     * @param type $account_numbers
     * @param type $start_date
     * @param type $end_date
     * @return type
     */
    public function GetIndividualSymbols($account_numbers, $start_date, $end_date){
        global $adb;
        $questions = generateQuestionMarks($account_numbers);
        
        $query = "SELECT mi.symbol, mi.category, mi.sub_sub_category, mi.description,
                    (SELECT SUM(amount) 
                        FROM account_monthly_income_pdf
                        WHERE account_number IN ({$questions})
                        AND date BETWEEN ? AND ?
                        AND symbol=mi.symbol) AS symbol_total
                  FROM account_monthly_income_pdf mi
                  WHERE account_number IN ({$questions})
                  AND date BETWEEN ? AND ?
                  GROUP BY symbol";
        $result = $adb->pquery($query, array($account_numbers, $start_date, $end_date,
                                             $account_numbers, $start_date, $end_date));
        $symbols = array();
        if($adb->num_rows($result) > 0)
            foreach($result AS $k => $v){
                $symbols[] = $v;
            }

        return $symbols;
    }
    
    /**
     * Calculate the monthly totals for all symbols and the grand total
     * @param type $account_numbers
     * @param type $start_date
     * @param type $end_date
     */
    public function CalculateMonthlyTotals($account_numbers, $start_date, $end_date){
        global $adb;
        $questions = generateQuestionMarks($account_numbers);

        $query = "SELECT month, SUM(amount) as monthly_total 
                  FROM account_monthly_income_pdf
                  WHERE account_number IN ({$questions})
                  AND date BETWEEN ? AND ?
                  GROUP BY month";
        $result = $adb->pquery($query, array($account_numbers, $start_date, $end_date));
        
        $symbols = array();
        $grand_total = 0;
        if($adb->num_rows($result) > 0)
            foreach($result AS $k => $v){
                $monthly_totals[$v['month']] = $v;
                $grand_total += $v['monthly_total'];
            }
        $monthly_totals['grand_total'] = $grand_total;
        return $monthly_totals;
    }
    
    /**
     * Get the symbol amounts and their total
     * @global type $adb
     * @param type $account_numbers
     * @param type $start_date
     * @param type $end_date
     * @return type
     */
    public function CalculateMonthlySymbols($account_numbers, $start_date, $end_date){
        global $adb;
        $questions = generateQuestionMarks($account_numbers);
        
        $query = "SELECT mi.symbol, mi.month, mi.amount, mi.category, mi.sub_sub_category, 
                    (SELECT SUM(amount) 
                        FROM account_monthly_income_pdf
                        WHERE account_number IN ({$questions})
                        AND date BETWEEN ? AND ?
                        AND symbol=mi.symbol) AS symbol_total
                  FROM account_monthly_income_pdf mi
                  WHERE account_number IN ({$questions})
                  AND date BETWEEN ? AND ?
                  GROUP BY symbol, month";
        $result = $adb->pquery($query, array($account_numbers, $start_date, $end_date,
                                             $account_numbers, $start_date, $end_date));
        $symbols = array();
        
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $symbols[$v['symbol']][$v['month']] = $v;
            }
        }
        return $symbols;
    }
    
    /**
     * Calculates the display months starting from this month to the next 12
     * @return type
     */
    public function CalculateDisplayMonths(){
        $display_months = array();
        $currentMonth = (int)date('m');//Get the current month
        for($x = $currentMonth; $x < $currentMonth+12; $x++) {//Calculate the next 12 months so we can sort them in order from the current month
            $month = substr(date('F', mktime(0, 0, 0, $x, 1)), 0, 3);
            $display_months[] = $month;//Only take the first 3 letters from the month because the database returns them as Jan, Feb, Mar, etc...
        }
        
        return $display_months;
    }
    
    /**
     * Calculate the main categories and their totals
     * @global type $adb
     * @param type $account_numbers
     * @param type $start_date
     * @param type $end_date
     * @return type
     */
    public function CalculateMainCategories($account_numbers, $start_date, $end_date){
        global $adb;
        $questions = generateQuestionMarks($account_numbers);
        
        $query = "SELECT category, SUM(amount) as category_total 
                  FROM account_monthly_income_pdf
                  WHERE account_number IN ({$questions})
                  AND date BETWEEN ? AND ?
                  GROUP BY category";
        $result = $adb->pquery($query, array($account_numbers, $start_date, $end_date));
        
        $main_categories = array();
        
    	if($adb->num_rows($result) > 0){
       		while($v = $adb->fetchByAssoc($result)){
                $main_categories[] = $v;
            }
       	}

        return $main_categories;
    }
    
    /**
     * Calculate the sub categories and their totals
     * @global type $adb
     * @param type $account_numbers
     * @param type $start_date
     * @param type $end_date
     * @return type
     */
    public function CalculateSubSubCategories($account_numbers, $start_date, $end_date){
        global $adb;
        $questions = generateQuestionMarks($account_numbers);
        
        $query = "SELECT category, sub_sub_category, SUM(amount) as sub_category_total 
                  FROM account_monthly_income_pdf
                  WHERE account_number IN ({$questions})
                  AND date BETWEEN ? AND ?
                  GROUP BY category, sub_sub_category";
        $result = $adb->pquery($query, array($account_numbers, $start_date, $end_date));
        
        $sub_sub_categories = array();

        if($adb->num_rows($result) > 0){
         	while($v = $adb->fetchByAssoc($result)){
                $sub_sub_categories[] = $v;
            }
        }

        return $sub_sub_categories;
    }
    
    public function CalculateYear($month, $isCurrent=false)
    {
        $current_month = date("m");
        $current_year = date("y");

        if($current_month > $month || $month > 12)
            $year = $current_year+1;
        else
            $year = $current_year;

        if($isCurrent)
            $year -= 1;
        return $year;
    }
    
    public function AutoFillTables($accounts){
        $m = date('m');
        $d = date('d');
        $Y = date('Y');

        $start = date('Y-m-d 00:00:00',mktime(0,0,0,$m,1,$Y-1));
        $today = date('Y-m-d 00:00:00',mktime(0,0,0,$m,0,$Y));
        $estimate_start = date('Y-m-d 00:00:00',mktime(0,0,0,$m,1,$Y));
        $estimate_end = date('Y-m-d 00:00:00',mktime(0,0,0,$m,0,$Y+1));

        $this->main_categories_previous = $this->CalculateMainCategories($accounts, $start, $today);
        $this->sub_sub_categories_previous = $this->CalculateSubSubCategories($accounts, $start, $today);
        $this->previous_symbols = $this->CalculateMonthlySymbols($accounts, $start, $today);
        $this->individual_previous_symbols = $this->GetIndividualSymbols($accounts, $start, $today);
        $this->previous_monthly_totals = $this->CalculateMonthlyTotals($accounts, $start, $today);

        $this->main_categories_projected = $this->CalculateMainCategories($accounts, $estimate_start, $estimate_end);
        $this->sub_sub_categories_projected = $this->CalculateSubSubCategories($accounts, $estimate_start, $estimate_end);
        $this->projected_symbols = $this->CalculateMonthlySymbols($accounts, $estimate_start, $estimate_end);
        $this->individual_projected_symbols = $this->GetIndividualSymbols($accounts, $estimate_start, $estimate_end);
        $this->projected_monthly_totals = $this->CalculateMonthlyTotals($accounts, $estimate_start, $estimate_end);
    }
    
    public function GenerateReport(Vtiger_Request $request){
        global $adb;
        $report = new cReports();
        $calling_record = $request->get('calling_record');
        switch($request->get('calling_module')){
            case "Accounts":
                $pids = GetPortfolioIDsFromHHID($calling_record);
                $numbers = GetPortfolioAccountNumbersFromPids($pids);
                break;
            case "Contacts":
                $pids = GetPortfolioIDsFromContactID($calling_record);
                $numbers = GetPortfolioAccountNumbersFromPids($pids);
                break;
            default:
                $numbers = $request->get('account_number');
                $acct = $request->get('account_number');
                $pids = $report->GetPortfolioIdsFromAccountNumber($acct);
                break;
        }

        $report = new cReports();
        
        //$pids = SeparateArrayWithCommas($pids); returns Like 1,1,2,3 instead of use this function use implode

        $pids = implode(",", $pids);

        global $adb;

        $holdings = new cPholdingsv2($pids);//Load all Portfolio Info

        $accounts = $holdings->GetAccountTransactions($acct);//Get transactions separated by account
        $totals = $holdings->GetAccountSymbolValues($accounts);
        $totals = $holdings->CalculateTotalValues($totals);

        $transactions = array();

        $m = date('m');
        $d = date('d');
        $Y = date('Y');

        $start = date('Y-m-d 00:00:00',mktime(0,0,0,$m,1,$Y-1)); //Current year se one year back
        $today = date('Y-m-d 00:00:00',mktime(0,0,0,$m,0,$Y)); // Current month se last month ki end date
        
        $query = "drop table if exists monthly_income_tmp";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE monthly_income_tmp (
                    id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    symbol_id int,            
                    date datetime,
                    quantity int,
                    price float,
                    value float,
                    description varchar(500),
                    activity_name varchar(100),
                    code_description varchar(100),
                    symbol varchar(25),
                    account_number varchar(25))";

        $adb->pquery($query, array());

        function monthName($month_int) {
            $month_int = (int)$month_int;
            $timestamp = mktime(0, 0, 0, $month_int, 1);
            return date("M", $timestamp);
        }

        foreach($accounts AS $a => $b)//$a is account number, $b array
            foreach($b AS $c => $d)//C is the actual transaction
            {
                $showinfo=1;
                if($d['code_description'] == "Cash")
                {
                        $showinfo=1;
                }

                if($showinfo){
                    // MyChanges 13June,2016
                	$query = "SELECT s.security_price_adjustment, s.security_annual_income_rate, 
                		f.frequency_type_name, f.frequency_type_interval_multiplier FROM vtiger_securities s
                    	INNER JOIN vtiger_pc_frequency_types f ON s.security_income_frequency_id = f.frequency_type_id
                        WHERE s.security_id = ? LIMIT 1";
                    
                    /*
                    	$query = "SELECT s.security_price_adjustment, s.security_annual_income_rate, f.frequency_type_name, f.frequency_type_interval_multiplier
                              FROM vtiger_dividend_intervals di
                              LEFT JOIN vtiger_securities s ON s.security_id = ?
                              LEFT JOIN vtiger_pc_frequency_types f ON s.security_income_frequency_id = f.frequency_type_id
                              LIMIT 1";
                    */
                	
                    $result = $adb->pquery($query, array($d['symbol_id']));
                    
                    if($adb->num_rows($result)){
                	    $multiplier = $adb->query_result($result, 0, "frequency_type_interval_multiplier");
                        $adjustment = $adb->query_result($result, 0, "security_price_adjustment");
                        $annual_income_rate = $adb->query_result($result, 0, "security_annual_income_rate");
                    }
                    if($d['net_amount'] > 0)
                    {
                        if($d['symbol_id'] == 1 && $d['is_reinvested_flag'] == 0)
                            continue;
                        $na = $d['net_amount'];// * $annual_income_rate * $multiplier * $adjustment;
                        $query = "INSERT INTO monthly_income_tmp (symbol_id, date, quantity, price, value, description, code_description, symbol, activity_name, account_number)
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $adb->pquery($query, array($d['symbol_id'], $d['trade_date'], $d['quantity'], $d['current_price'], $d['net_amount'], $d['description'], $d['code_description'], $d['security_symbol'], $d['activity_name'], $a));
                    }
                }
            }

        $query = "SELECT *, YEAR(date) AS year, substr(MONTHNAME(date), 1, 3) AS month, SUM(value) AS total, SUM(quantity) AS total_quantity 
                  FROM `monthly_income_tmp` 
                  WHERE date >= ?
                  AND date <= ?
                  AND activity_name = ?
                  GROUP BY MONTH(date), YEAR(date), symbol ORDER BY date ASC";
        
        $result = $adb->pquery($query, array($start, $today, "Income"));

        $monthly_values = array();
        $monthly_totals = array();

        $pdf_insert_previous = array();
        $pdf_insert_estimate = array();

        $grand_total = 0;

        if($adb->num_rows($result)){
	        while($v = $adb->fetchByAssoc($result)){
				if($v['activity_name'] == "Income" && $v['code_description'] != "Cash"){
	                $pdf_insert_previous[] = $v;
	                $monthly_values[$v['code_description']][$v['symbol']]['description'] = $v['description'];
	                $monthly_values[$v['code_description']][$v['symbol']][$v['month']] = $v['total']; 
	                $monthly_values[$v['code_description']][$v['symbol']]['total'] += $v['total'];
	                $monthly_totals[$v['month']]['total'] += $v['total'];
	                $monthly_totals[$v['month']]['year'] = $v['year'];
	                $grand_total += $v['total'];
	            }
	        }
        }

         // Need To be discussed
        $query = "SELECT *, YEAR(date) AS year, substr(MONTHNAME(date), 1, 3) AS month, SUM(value) AS total, SUM(quantity) AS total_quantity
                  FROM monthly_income_tmp 
                  WHERE date >= ?
                  AND date <= ?
                  AND activity_name = ?
                  GROUP BY MONTH(date), YEAR(date), value, date 
                  HAVING count(*) <= 1";
        $result = $adb->pquery($query, array($start, $today, "Income"));

        if($adb->num_rows($result)){
        	while($v = $adb->fetchByAssoc($result)) {
	            if($v['code_description'] == "Cash"){
	                $monthly_values[$v['code_description']][$v['symbol']]['description'] = $v['description'];
	                $monthly_values[$v['code_description']][$v['symbol']][$v['month']] = $v['total']; 
	                $monthly_values[$v['code_description']][$v['symbol']]['total'] += $v['total'];
	                $monthly_totals[$v['month']]['total'] += $v['total'];
	                $monthly_totals[$v['month']]['year'] = $v['year'];
	                $grand_total += $v['total'];
	            }
        	}
        }

        $categories = $holdings->TotalsToCategories($totals);

        $estimate_values = array();

        foreach($categories AS $a => $b)
        {
            if($a != "Cash")
            {
                foreach($b AS $k => $v)
                    {   
                        $symbol_id = $v['security_id'];
                        $symbol = $v['symbol'];
                        $quantity = $v['quantity'];
                        $price = $v['price'];
                        $description = $v['security_description'];
                        $account = $v['account'];
                        $code_description = $v['code_description'];
                        $estimate_values[$v['symbol']]['symbol_id'] = $symbol_id;
                        $estimate_values[$v['symbol']]['symbol'] = $symbol;
                        $estimate_values[$v['symbol']]['quantity'] += $quantity;
                        $estimate_values[$v['symbol']]['price'] = $price;
                        $estimate_values[$v['symbol']]['description'] = $description;
                        $estimate_values[$v['symbol']]['code_description'] = $code_description;
                        $estimate_values[$v['symbol']]['account_number'] = $account;
                        
                        //Need To be Discussed for example for 29 symbol_id 4rows found use only oth index data
                        
                        $query = "SELECT * FROM vtiger_dividend_intervals di 
                                  JOIN vtiger_dividend_frequency df ON df.symbol_id = di.symbol_id
                                  WHERE di.symbol_id = ?";
                        $result = $adb->pquery($query, array($symbol_id));
                        $amount = $adb->query_result($result, 0, "amount");
                        $frequency = $adb->query_result($result, 0, "frequency");
                        if(!$frequency)
                        {
                            $query = "SELECT s.security_price_adjustment, s.security_annual_income_rate, f.frequency_type_name, f.frequency_type_interval_multiplier
                                      FROM vtiger_securities s
                                      LEFT JOIN vtiger_pc_frequency_types f ON f.frequency_type_id = s.security_income_frequency_id
                                      WHERE s.security_id = ?";
                            
                            $result = $adb->pquery($query, array($symbol_id));
                            $multiplier = $adb->query_result($result, 0, "frequency_type_interval_multiplier");
                            $frequency = $adb->query_result($result, 0, "frequency_type_name");
                            $adjustment = $adb->query_result($result, 0, "security_price_adjustment");
                            if($adjustment == 0)
                                $adjustment = 1;
                            $frequency = strtolower($frequency);
                            if($frequency == "semiannual")
                                $frequency = "semi";
                            $divider = 0;

                            switch($frequency)
                            {
                                case "annual":
                                    $divider = 1;
                                    break;
                                case "semi":
                                    $divider = 2;
                                    break;
                                case "quarterly":
                                    $divider = 4;
                                    break;
                                case "monthly":
                                    $divider = 12;
                                    break;
                            }
                            if($divider == 0)
                                $amount = 0;
                            else
                                $amount = $adb->query_result($result, 0, "security_annual_income_rate")/$divider*$adjustment; //USED TO USE /$divider*$multiplier*$adjustment
                        }
                        $estimate_values[$v['symbol']]['amount'] = $amount;
                        $estimate_values[$v['symbol']]['frequency'] = $frequency;//$adb->query_result($result, 0, "frequency");
                    }
            }
        }

        $estimated_monthly_totals = array();
        $estimated_grand_totals = array();

        foreach($estimate_values AS $k => $v)
        {
            if($k != 'CASH')
            {
                $price = $v['quantity'] * $v['amount'];
                if($v['price'])
                $quantity = ceil($price / $v['price']);

                $query = "SELECT pay_date, MONTH(pay_date) AS start_month, YEAR(pay_date) AS start_year FROM vtiger_dividend_intervals WHERE symbol_id = ? ORDER BY pay_date ASC LIMIT 1";
                $r = $adb->pquery($query, array($v['symbol_id']));
                $pay_date = $adb->query_result($r, 0, "pay_date");
                $start_month = $adb->query_result($r, 0, "start_month");

                if(!$pay_date)//we don't have a pay date from yahoo, get it from PC
                {
                    $tmp_query = "SELECT * FROM vtiger_pc_transactions WHERE symbol_id=? AND activity_id=90 LIMIT 1";
                    $tmp_result = $adb->pquery($tmp_query, array($v['symbol_id']));
                    $pay_date = str_replace(" 00:00:00", "", $adb->query_result($tmp_result, 0, "trade_date"));
                    $timestamp = strtotime($pay_date);
                    $start_month = date("m", $timestamp);
                }
                $frequency = $v['frequency'];

                $estimate_values[$k]['estimate_price'] = $price;
                if($v['price'])
                    $estimate_values[$k]['estimate_quantity'] = $quantity;

                $estimate_values[$k]['description'] = $v['description'];
                $estimate_values[$k]['pay_start'] = $pay_date;
                $estimate_values[$k]['frequency'] = $frequency;
                $estimate_values[$k]['start_month'] = $start_month;
            }
        }

        $estimate_payout = array();

        foreach($estimate_values AS $k => $v)
        {
            $current_month = date("m");
            $current_year = date("y");

            if($v['frequency'] == "monthly")
            {
                for($x = 1; $x <= 12; $x++)
                {
                    $year = $this->CalculateYear($x);
                    $month = monthName($x);
                    $v['month'] = $month;
                    $v['year'] = $year;
                    $pdf_insert_estimate[] = $v;
                    $estimate_payout[$v['code_description']][$k][$month] = $v;
                    $estimate_payout[$v['code_description']][$k]['description'] = $v['description'];
                    $estimate_payout[$v['code_description']][$k]['total'] += round($v['estimate_price'], 2);
                    $estimate_payout[$v['code_description']][$k]['frequency'] = $v['frequency'];
                }
            }
            else
            if($v['frequency'] == "quarterly")
            {
                for($x = $v['start_month']; $x < $v['start_month']+11; $x+=3)
                {
                    $year = $this->CalculateYear($x);
                    $month = monthName($x);
                    $v['month'] = $month;
                    $v['year'] = $year;
                    $pdf_insert_estimate[] = $v;
                    $estimate_payout[$v['code_description']][$k][$month] = $v;
                    $estimate_payout[$v['code_description']][$k]['description'] = $v['description'];
                    $estimate_payout[$v['code_description']][$k]['total'] += round($v['estimate_price'], 2);
                    $estimate_payout[$v['code_description']][$k]['frequency'] = $v['frequency'];
                }
            }
            else
            if($v['frequency'] == "semi")
            {
                for($x = $v['start_month']; $x < $v['start_month']+11; $x+=6)
                {
                    $year = $this->CalculateYear($x);
                    $month = monthName($x);
                    $v['month'] = $month;
                    $v['year'] = $year;
                    $pdf_insert_estimate[] = $v;
                    $estimate_payout[$v['code_description']][$k][$month] = $v;
                    $estimate_payout[$v['code_description']][$k]['description'] = $v['description'];
                    $estimate_payout[$v['code_description']][$k]['total'] += round($v['estimate_price'], 2);
                    $estimate_payout[$v['code_description']][$k]['frequency'] = $v['frequency'];
                }
            }
            else
            if($v['frequency'] == "yearly")
            {
                for($x = $v['start_month']; $x < $v['start_month']+11; $x+=12)
                {
                    $year = $this->CalculateYear($x);
                    $month = monthName($x);
                    $v['month'] = $month;
                    $v['year'] = $year;
                    $pdf_insert_estimate[] = $v;
                    $estimate_payout[$v['code_description']][$k][$month] = $v;
                    $estimate_payout[$v['code_description']][$k]['description'] = $v['description'];
                    $estimate_payout[$v['code_description']][$k]['total'] += round($v['estimate_price'], 2);
                    $estimate_payout[$v['code_description']][$k]['frequency'] = $v['frequency'];
                }
            }
        }

        foreach($estimate_payout AS $x => $y)
            foreach($y AS $a => $b)
            foreach($b AS $k => $v)
                if($k != "description")
                {
                    $estimated_monthly_totals[$k]['total']+=$v['estimate_price'];//[$v['month']]['total'] += $v['total'];
                    $estimated_grand_total += $v['estimate_price'];
                }

        $display_months = array();

//        $history = "";
//        $notFirst = true;

        $currentMonth = (int)date('m');//Get the current month
        for($x = $currentMonth; $x < $currentMonth+12; $x++) {//Calculate the next 12 months so we can sort them in order from the current month
            $month = substr(date('F', mktime(0, 0, 0, $x, 1)), 0, 3);
            $display_months[] = $month;//Only take the first 3 letters from the month because the database returns them as Jan, Feb, Mar, etc...
            $tmp_month = date("n", strtotime("01-".$month));
            $display_years_current[$month] = '20'.$this->CalculateYear($tmp_month, true);//Should this still be live by the year 2100, this will need to be changed to 21 instead of just 20
            $display_years_projected[$month] = '20'.$this->CalculateYear($tmp_month, false);

/*            if(!$notFirst)
            {
                $history .= ",";
                $estimated_income .= ",";
            }*/
            if(!$monthly_totals[$month]['total'])
                $monthly_totals[$month]['total'] = 0;
            if(!$estimated_monthly_totals[$month]['total'])
                $estimated_monthly_totals[$month]['total'] = 0;

//            $history .= "{date:\"{$month}\", value:{$monthly_totals[$month]['total']}, open:\"$\"}";
            $history[] = array("date"=>$month, "value"=>$monthly_totals[$month]['total'], "open:"=>"$");
            $estimated_income[] = array("date"=>$month, "value"=>$estimated_monthly_totals[$month]['total'], "open:"=>"$");
//            $estimated_income .= "{date:\"{$month}\", value:{$estimated_monthly_totals[$month]['total']}, open:\"$\"}";
//            $notFirst = false;
        }   

        $this->display_months = $display_months;
        $this->display_years_current = $display_years_current;
        $this->display_years_projected = $display_years_projected;
        $this->monthly_values = $monthly_values;
        $this->monthly_totals = $monthly_totals;
        $this->grand_total = $grand_total;
        
        $this->estimate_payout = $estimate_payout;
        $this->estimated_monthly_totals = $estimated_monthly_totals;
        $this->estimated_grand_total = $estimated_grand_total;

        $this->history = $history;
        $this->estimated_income = $estimated_income;

        foreach($display_months AS $k => $v)
        {
            $pdf_bar[$v] = $monthly_totals[$v]['total'];
            $pdf_estimate[$v] = $estimated_monthly_totals[$v]['total'];
        }

        $pdf_access = new cPDFDBAccess();

        $questions = generateQuestionMarks($numbers);
        $query = "DELETE FROM account_monthly_income_pdf WHERE REPLACE(account_number, '-', '') IN ({$questions})";
        $adb->pquery($query, array($numbers));

        foreach($pdf_insert_previous AS $k => $v)
            $pdf_access->WriteMonthlyProjected($v);
        $this->main_categories_previous = $this->CalculateMainCategories($numbers, $start, $today);
        $this->sub_sub_categories_previous = $this->CalculateSubSubCategories($numbers, $start, $today);
        $this->previous_symbols = $this->CalculateMonthlySymbols($numbers, $start, $today);
        $this->individual_previous_symbols = $this->GetIndividualSymbols($numbers, $start, $today);
#        print_r($this->individual_previous_symbols);exit;
        $this->previous_monthly_totals = $this->CalculateMonthlyTotals($numbers, $start, $today);
        
        foreach($pdf_insert_estimate AS $k => $v)
        {
            $v['total'] = $v['estimate_price'];
            $v['year'] = '20'.$v['year'];
            $pdf_access->WriteMonthlyProjected($v);
        }

        $estimate_start = date('Y-m-d 00:00:00',mktime(0,0,0,$m,1,$Y));
        $estimate_end = date('Y-m-d 00:00:00',mktime(0,0,0,$m,0,$Y+1));

        $this->main_categories_projected = $this->CalculateMainCategories($numbers, $estimate_start, $estimate_end);
        $this->sub_sub_categories_projected = $this->CalculateSubSubCategories($numbers, $estimate_start, $estimate_end);
        $this->projected_symbols = $this->CalculateMonthlySymbols($numbers, $estimate_start, $estimate_end);
        $this->individual_projected_symbols = $this->GetIndividualSymbols($numbers, $estimate_start, $estimate_end);
        $this->projected_monthly_totals = $this->CalculateMonthlyTotals($numbers, $estimate_start, $estimate_end);
        $this->account = $numbers;
    }

    static private function CreateMonthlyTable($accounts){
		global $adb;
		include_once("include/utils/omniscientCustom.php");

		if(is_array($accounts))
			$acc = $accounts;
		else
			$acc[] = $accounts;
		$acc = RemoveDashes($acc);
		$questions = generateQuestionMarks($acc);
		$query = "DROP TABLE IF EXISTS historical_monthly";
		$adb->pquery($query, array());
		$query = "CREATE TEMPORARY TABLE historical_monthly SELECT date(date) AS date, account_number, market_value, cash_value, fixed_income, equities, total_value FROM vtiger_portfolioinformation_historical WHERE REPLACE(account_number, '-', '') IN ({$questions})";
		$adb->pquery($query, array($acc));
	}
    static public function GetMonthyValuesForAccounts($accounts){
		global $adb;
    	self::CreateMonthlyTable($accounts);
		$query = "SELECT CONCAT(MONTHNAME(date), ', ', YEAR(date)) AS date, SUM(total_value) AS total FROM historical_monthly WHERE date >= NOW() - INTERVAL 1 YEAR GROUP BY date LIMIT 12";
		$result = $adb->pquery($query, array());
		if($adb->num_rows($result) > 0){
			$monthly = array();
			while($v = $adb->fetchByAssoc($result)){
				$monthly[] = array('date' => $v['date'], 'value' => $v['total']);
			}
			return $monthly;
		}
		return 0;
	}
}
?>
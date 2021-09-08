<?php
require_once("libraries/Reporting/Indexing.php");
require_once("libraries/Reporting/ProjectedIncomeModel.php");
require_once("libraries/Reporting/ReportCommonFunctions.php");
include_once "libraries/custodians/cCustodian.php";
class IndividualPerformance{
    public $account_number, $amount, $transaction_type, $transaction_activity, $operation, $buy_sell_indicator, $disable_performance;
}

class Performance{
    public $amount, $transaction_type, $transaction_activity, $operation, $buy_sell_indicator;
}

class AccountValues{
    public $account_number, $date, $value, $disable_performance;
}

class TransactionTypes{
    public $type, $activity;
}

class PerformanceReport_Model extends Vtiger_Module {
    private $isValid = false;//During construction, this determines if the accounts and dates have valid results returned.  If this remains false, no report can be generated
    private $beginning_date_altered = false;
    private $beginning_values, $ending_values;
    private $beginning_values_summed, $ending_values_summed;
    private $start_date_changed = false;
    private $end_date_changed = false;
    private $start_date, $end_date, $transaction_start_date, $transaction_end_date;
    private $performance;
    private $individual_performance_summed;
    private $individual_start_values, $individual_end_values;
    private $performance_summed;
    private $capital_appreciation, $individual_appreciation;
    private $appreciation_percent, $individual_appreciation_percent;
    private $transaction_types;
    private $intervals, $interval_begin_date, $interval_end_date;
    private $twr, $individual_twr, $irr, $individual_irr;
    private $account_numbers;
    private $dividend_accrual;
    private $benchmark;
    private $estimated_income;
	
	private $since_inception = false;
	
	private $report_type;
	
	public function PerformanceReport_Model(array $account_numbers, $start_date, $end_date, $legacy=false, $since_inception = false, $report_type = ''){
        global $adb;
        
		$passed_in_date = $start_date;
		
        if(empty($account_numbers))
            return null;
        
		$this->report_type = $report_type;
		
		$this->account_numbers = $account_numbers;
		
		$this->since_inception = $since_inception;
		
        $this->transaction_start_date = $start_date;
		
        $questions = generateQuestionMarks($account_numbers);
        
		#$start_date = GetDateMinusOneDay($start_date);

        $this->beginning_values_summed = new AccountValues();
		
        $this->ending_values_summed = new AccountValues();

        $this->dividend_accrual = 0;
		
		
		$table_name = 'tmp_intervals_daily'.uniqid();
		
		$adb->pquery("DROP TABLE IF EXISTS $table_name");
		
		$adb->pquery("CREATE TEMPORARY TABLE $table_name LIKE intervals_daily");
		
		
		if($since_inception){
				$adb->pquery("INSERT INTO $table_name SELECT AccountNumber, IntervalID, IntervalBeginDate, IntervalEndDate AS IntervalEndDate, IntervalBeginValue, 
			IntervalEndValue, NetFlowAmount, NetReturnAmount, GrossReturnAmount, EntryDate, PriceBeginDate, PriceEndDate, 
			FirstDayFlows, FirstDayGrossFlows, LastModifiedDate, expenseamount, incomeamount, journalamount, tradeamount, 
			intervaltype, investmentreturn, uid FROM (SELECT AccountNumber, IntervalID, IntervalBeginDate, IntervalEndDate 
			AS IntervalEndDate, IntervalBeginValue, IntervalEndValue, NetFlowAmount, NetReturnAmount, GrossReturnAmount, 
			EntryDate, PriceBeginDate, PriceEndDate, FirstDayFlows, FirstDayGrossFlows, LastModifiedDate, expenseamount, incomeamount, 
			journalamount, tradeamount, intervaltype, investmentreturn, uid FROM intervals_daily 
			
			WHERE (AccountNumber, IntervalBeginDate) 
			
			IN (SELECT AccountNumber, MIN(IntervalBeginDate) FROM intervals_daily 
			WHERE AccountNumber IN (" . $questions . ") 
			GROUP BY AccountNumber ORDER BY IntervalEndDate ASC) AND 
				AccountNumber IN (" . $questions . ") ORDER BY intervaltype ASC
			) AS ordered GROUP BY AccountNumber", array($account_numbers, $account_numbers));
		} else {
				$adb->pquery("INSERT INTO $table_name SELECT AccountNumber, IntervalID, IntervalBeginDate, IntervalEndDate AS IntervalEndDate, IntervalBeginValue, 
			IntervalEndValue, NetFlowAmount, NetReturnAmount, GrossReturnAmount, EntryDate, PriceBeginDate, PriceEndDate, 
			FirstDayFlows, FirstDayGrossFlows, LastModifiedDate, expenseamount, incomeamount, journalamount, tradeamount, 
			intervaltype, investmentreturn, uid FROM (SELECT AccountNumber, IntervalID, IntervalBeginDate, IntervalEndDate 
			AS IntervalEndDate, IntervalBeginValue, IntervalEndValue, NetFlowAmount, NetReturnAmount, GrossReturnAmount, 
			EntryDate, PriceBeginDate, PriceEndDate, FirstDayFlows, FirstDayGrossFlows, LastModifiedDate, expenseamount, incomeamount, 
			journalamount, tradeamount, intervaltype, investmentreturn, uid FROM intervals_daily 
			
			WHERE (AccountNumber, IntervalEndDate) 
			
			IN (SELECT AccountNumber, MIN(IntervalEndDate) FROM intervals_daily 
			
			WHERE IntervalEndDate >= ? AND IntervalEndDate <= ? 
			AND AccountNumber IN (" . $questions . ") 
			
			
			GROUP BY AccountNumber ORDER BY IntervalEndDate ASC) AND 
				AccountNumber IN (" . $questions . ") ORDER BY intervaltype ASC
			) AS ordered GROUP BY AccountNumber", array($start_date, $end_date, $account_numbers, $account_numbers));
			
		}
		
		$beginning_date_result = $adb->pquery("SELECT * FROM $table_name");
		
		$result = $adb->pquery("SELECT MIN(intervalenddate) as intervaldate FROM $table_name");
        
		if($adb->num_rows($result) == 0){
            echo "There has been an error determining the earliest interval date!";
            return;
        }
		
		
        $earliest_date = $adb->query_result($result, 0, 'intervaldate');
		
        $earliest_start_date_result = $adb->pquery("SELECT IntervalBeginDate FROM $table_name 
		WHERE IntervalEndDate = ?", array($earliest_date));
        
		$earliest_start_date = $adb->query_result($earliest_start_date_result, 0, 'intervalbegindate');

		$adb->pquery("DROP TEMPORARY TABLE IF EXISTS $table_name");
		
		$adb->pquery("CREATE TEMPORARY TABLE $table_name LIKE intervals_daily");
		
		$adb->pquery("INSERT INTO $table_name 
		SELECT AccountNumber, IntervalID, IntervalBeginDate, IntervalEndDate AS IntervalEndDate, IntervalBeginValue, IntervalEndValue,
		NetFlowAmount, NetReturnAmount, GrossReturnAmount, EntryDate, PriceBeginDate, PriceEndDate, FirstDayFlows, 
		FirstDayGrossFlows, LastModifiedDate, expenseamount, incomeamount, journalamount, tradeamount, intervaltype, 
		investmentreturn, uid FROM intervals_daily WHERE (AccountNumber, IntervalEndDate) IN 
		(SELECT AccountNumber, MAX(IntervalEndDate)  FROM intervals_daily WHERE IntervalEndDate >= ? 
		AND IntervalEndDate <= ? AND AccountNumber IN (" . $questions . ")   
		GROUP BY AccountNumber ORDER BY IntervalEndDate DESC) AND AccountNumber IN (" . $questions . ") 
		GROUP BY AccountNumber", array( $start_date, $end_date, $account_numbers, $account_numbers));
		
		
		$ending_date_result = $adb->pquery("SELECT * FROM $table_name ORDER BY intervalendvalue DESC");

		//$query = "CALL CALCULATE_DIVIDEND_ACCRUAL(\"{$questions}\", ?)";
        //$adb->pquery($query, array($account_numbers, $end_date));

		$adb->pquery("DROP TABLE IF EXISTS DIVIDEND_ACCRUAL");

		$adb->pquery("CREATE TEMPORARY TABLE DIVIDEND_ACCRUAL(
account_number VARCHAR(50),
dividend_accrual_amount DECIMAL(20,5))");
		
		$adb->pquery("INSERT INTO DIVIDEND_ACCRUAL  SELECT account_number, dividend_accrual 
FROM custodian_omniscient.custodian_balances_fidelity  WHERE account_number IN (" . $questions . ") 
AND as_of_date = (SELECT MAX(as_of_date) FROM custodian_omniscient.custodian_balances_fidelity WHERE 
account_number IN (" . $questions . ") AND as_of_date <= ?)", array($account_numbers, $account_numbers, $end_date));

		$accrual_result = $adb->pquery("SELECT SUM(dividend_accrual_amount) AS dividend_accrual_amount 
		FROM DIVIDEND_ACCRUAL");

        if($adb->num_rows($accrual_result) > 0)
            $this->dividend_accrual = $adb->query_result($accrual_result, 0, 'dividend_accrual_amount');

        #IF intervalenddate == date entered, use interalbeginvalue ELSE use intervalendvalue as the starting value
        
		if($adb->num_rows($beginning_date_result) > 0 && $adb->num_rows($ending_date_result) > 0){
			
			//If we have info for both beginning and end dates
            
			while($v = $adb->fetchByAssoc($beginning_date_result)){
				
				//Loop through all beginning date results
                
				$set_zero = 0;
                
				$tmp = new AccountValues();//variables: Account Number, Date, Value, disable performance
                
				$tmp->account_number = $v['accountnumber'];
				
				//If the accounts first end date <= the earliest account date we have
                if($v['intervalenddate'] == $earliest_date) {
				    //Set the value to the start value, and only if they are equal
					$tmp->value = $v['intervalbeginvalue'];
				}
				
				//This happens if a weekend or holiday is chosen as a start date
                if($earliest_date != $start_date){
                    $this->start_date_changed = true;//Flag that we have changed the start date
                    $start_date = $earliest_date;//Set the new start date to be the earliest date we have in our table (which would be a monday if saturday/sunday were selected for example)
                }

                $this->start_date = $earliest_date;
				
				//Set the individual account start date to be the earliest date, even if in reality the account was opened 3 months later
                $tmp->date = $start_date;
				
				//Now set our beginning values array to be equal to the accounts determined beginning oject information
                $this->beginning_values[] = $tmp;

                $this->beginning_values_summed->date = $this->start_date;

                $individuals = new AccountValues();
                
				$individuals->account_number = $v['accountnumber'];
                
				$individuals->date = $v['intervalenddate'];
				
                $individuals->value = PortfolioInformation_Module_Model::GetIntervalBeginValueForDate($v['accountnumber'], $start_date);//$tmp->value;
                
				$individuals->disable_performance = PortfolioInformation_Module_Model::IsPerformanceDisabled($v['accountnumber']);
				
				//Only add to the grand total if performance is enabled
				if($individuals->disable_performance != 1)
                    $this->beginning_values_summed->value += $individuals->value;

#               //This sets the individual account start information
                $this->individual_start_values[$v['accountnumber']] = $individuals;

         }

            while($v = $adb->fetchByAssoc($ending_date_result)){
                
				$tmp = new AccountValues();
                
				$tmp->account_number = $v['account_number'];
                
				$tmp->date = $v['intervalenddate'];
                
				$tmp->value = $v['intervalendvalue'];
                
				$tmp->disable_performance = PortfolioInformation_Module_Model::IsPerformanceDisabled($v['accountnumber']);
				
				//We had to change the interval end date because we don't know what the end ended with
                if($v['intervalenddate'] != $this->end_date){
                    $this->end_date_changed = true;
					//Set the end date to equal to the last end date for the account we are in the loop
                    $end_date = $v['intervalenddate'];
                }

                // If an end date isn't already set, or the currently set "real end date" is less than the end date of the account we are on,
                // set the "real end date" to equal the account we are on
                
				if($this->end_date < $end_date || strlen($end_date) < 1)
                    $this->end_date = $end_date;

                $tmp->date = $this->end_date;//Set the account value object to equal the true end date

                $this->ending_values[] = $tmp;//Set the ending values for this account

                $this->ending_values_summed->date = $this->end_date;//Set the combined end date to equal the real end date
                
				if($tmp->disable_performance != 1)//If performance isn't disabled
                    $this->ending_values_summed->value += $tmp->value;//Add account value if we are supposed to (performance enabled)

                $individuals = new AccountValues();
                
				$individuals->account_number = $v['accountnumber'];
                
				$individuals->date = $v['intervalbegindate'];
                
				$individuals->value = $v['intervalendvalue'];
                
				$this->individual_end_values[$v['accountnumber']] = $individuals;//Set the individual account end data
            }
			
			
			$performance_table = 'performance'.uniqid();
			
			// Performance Begins
			$adb->pquery("DROP TABLE IF EXISTS $performance_table");
			
			if($since_inception){
			
				$adb->pquery("CREATE TEMPORARY TABLE $performance_table SELECT SUM(CONCAT(operation, net_amount)) 
				AS amount, transaction_type, transaction_activity, trade_date, operation, buy_sell_indicator, 
				SUM(commission) AS commission FROM vtiger_transactions t JOIN vtiger_transactionscf cf USING (transactionsid) 
				JOIN vtiger_crmentity e ON e.crmid = t.transactionsid JOIN vtiger_portfolioinformation p ON 
				p.account_number = t.account_number JOIN vtiger_portfolioinformationcf pcf ON 
				p.portfolioinformationid = pcf.portfolioinformationid WHERE t.account_number IN (" . $questions . ") 
				AND trade_date >= p.inceptiondate AND trade_date <= ? AND e.deleted = 0 AND 
				pcf.disable_performance != 1 GROUP BY transaction_type, transaction_activity, buy_sell_indicator",
				array($account_numbers, $this->end_date));
			
			} else {
			
				$adb->pquery("CREATE TEMPORARY TABLE $performance_table SELECT SUM(CONCAT(operation, net_amount)) 
				AS amount, transaction_type, transaction_activity, trade_date, operation, buy_sell_indicator, 
				SUM(commission) AS commission FROM vtiger_transactions t JOIN vtiger_transactionscf cf USING (transactionsid) 
				JOIN vtiger_crmentity e ON e.crmid = t.transactionsid JOIN vtiger_portfolioinformation p ON 
				p.account_number = t.account_number JOIN vtiger_portfolioinformationcf pcf ON 
				p.portfolioinformationid = pcf.portfolioinformationid WHERE t.account_number IN (" . $questions . ") 
				AND trade_date >= ? AND trade_date <= ? AND e.deleted = 0 AND 
				pcf.disable_performance != 1 GROUP BY transaction_type, transaction_activity, buy_sell_indicator",
				array($account_numbers, $this->transaction_start_date, $this->end_date));
				
			}
			
			
			$adb->pquery("UPDATE $performance_table SET transaction_type = 'Reversal' WHERE 
			transaction_activity IN ('Management fee')");
			
			$query = "UPDATE $performance_table SET transaction_type = 'income_div_interest'
			  WHERE transaction_type = 'Income' 
			  AND (transaction_activity LIKE ('%dividend%') OR 
			  transaction_activity LIKE ('%interest%') or 
			  transaction_activity LIKE ('%Monthly Sch1 Credit Int.%') or
			  transaction_activity LIKE ('%Margin expense%') or
			  transaction_activity LIKE ('%Cash in lieu transaction%')
			  );";
			$adb->pquery($query, array());
			
			$query = "UPDATE $performance_table SET amount = 0 WHERE transaction_activity = 'Payment in lieu'";
            $adb->pquery($query, array());
			
			
			$performance_result = $adb->query("SELECT * FROM $performance_table");

			while($v = $adb->fetchByAssoc($performance_result)){
				$tmp = new Performance();
				$tmp->amount = $v['amount'];
				$tmp->transaction_type = $v['transaction_type'];
				$tmp->transaction_activity = $v['transaction_activity'];
				$tmp->operation = $v['operation'];
				$tmp->buy_sell_indicator = $v['buy_sell_indicator'];
				$this->performance[$tmp->transaction_type][$tmp->transaction_activity] = $tmp;
				$this->transaction_types[$tmp->transaction_type][] = $tmp->transaction_activity;
			}

			$query = "SELECT SUM(amount) AS amount, transaction_type, transaction_activity, trade_date, operation, 
			buy_sell_indicator FROM $performance_table GROUP BY transaction_type";
			$result = $adb->pquery($query, array());

			if($adb->num_rows($result) > 0){
				while($v = $adb->fetchByAssoc($result)){
					$tmp = new Performance();
					$tmp->amount = $v['amount'];
					$tmp->transaction_type = $v['transaction_type'];
					$tmp->transaction_activity = $v['transaction_activity'];
					$tmp->operation = $v['operation'];
					$tmp->buy_sell_indicator = $v['buy_sell_indicator'];
					$this->performance_summed[$v['transaction_type']] = $tmp;
				}
			}
			
		
			
			// Individual Performance Begins
            
			$individual_performance_table = 'individual_performance'.uniqid();
			
			$adb->pquery("DROP TABLE IF EXISTS $individual_performance_table");
			
			$adb->pquery("CREATE TEMPORARY TABLE $individual_performance_table 
			SELECT account_number, SUM(CONCAT(operation, net_amount)) AS amount, transaction_type, transaction_activity, 
			trade_date, operation, buy_sell_indicator, SUM(commission) AS commission, 0 AS disable_performance 
			FROM vtiger_transactions t JOIN vtiger_transactionscf cf USING (transactionsid) 
			JOIN vtiger_crmentity e ON e.crmid = t.transactionsid\nWHERE account_number IN (" . $questions .") 
			AND trade_date >= ? AND trade_date <= ? AND e.deleted = 0 
			GROUP BY account_number, transaction_type, transaction_activity, buy_sell_indicator",
			array($account_numbers, $this->transaction_start_date, $this->end_date));
			
			
			$adb->pquery("UPDATE $individual_performance_table SET account_number = TRIM(account_number)");
			
			$adb->pquery("UPDATE $individual_performance_table h JOIN vtiger_portfolioinformation p 
			ON p.account_number = h.account_number JOIN vtiger_portfolioinformationcf cf ON 
			p.portfolioinformationid = cf.portfolioinformationid SET h.disable_performance = 1 
			WHERE h.account_number = p.account_number AND cf.disable_performance = 1");
			
			
			$adb->pquery("UPDATE $individual_performance_table SET transaction_type = 'Reversal' WHERE transaction_activity IN ('Management fee')");
			
			$adb->pquery("UPDATE $individual_performance_table SET transaction_type = 'Unknown' WHERE transaction_type IN ('')");
			
			$adb->pquery("UPDATE $individual_performance_table SET transaction_activity = 'Unknown' WHERE transaction_activity IN ('')");
			
			$adb->pquery("DELETE FROM $individual_performance_table WHERE transaction_type LIKE('%DUPE%')");
			
			
            $query = "UPDATE $individual_performance_table SET transaction_type = 'income_div_interest'
                      WHERE transaction_type = 'Income' 
                      AND (transaction_activity LIKE ('%dividend%') OR transaction_activity LIKE ('%interest%'));";
            $adb->pquery($query, array());

            $query = "UPDATE $individual_performance_table SET amount = 0 WHERE transaction_activity = 'Payment in lieu'";
            $adb->pquery($query, array());

            
			$query = "SELECT account_number, SUM(amount) AS amount, transaction_type, transaction_activity, trade_date, 
			operation, buy_sell_indicator, disable_performance FROM $individual_performance_table GROUP BY account_number, transaction_type";
			
			$result = $adb->pquery($query, array());

			foreach($this->individual_end_values AS $k => $v){
				$this->individual_performance_summed[$k] = array();
			}

			if($adb->num_rows($result) > 0){
				
				while($v = $adb->fetchByAssoc($result)){
					$tmp = new IndividualPerformance();
					$tmp->account_number = $v['account_number'];
					$tmp->amount = $v['amount'];
					$tmp->transaction_type = $v['transaction_type'];
					$tmp->transaction_activity = $v['transaction_activity'];
					$tmp->operation = $v['operation'];
					$tmp->buy_sell_indicator = $v['buy_sell_indicator'];
					$tmp->disable_performance = $v['disable_performance'];

					$this->individual_performance_summed[$v['account_number']][$v['transaction_type']] = $tmp;
					$this->individual_performance_summed[$v['account_number']]['account_name'] = PortfolioInformation_Module_Model::GetAccountNameFromAccountNumber($v['account_number']);
					$this->individual_performance_summed[$v['account_number']]['account_type'] = PortfolioInformation_Module_Model::GetAccountTypeFromAccountNumber($v['account_number']);
				}
				
			}

			$this->intervals = PortfolioInformation_Module_Model::GetIntervalsForAccounts($account_numbers);//Create combined accounts intervals
			
			$monthly_start = $start_date;
			
			$monthly_end = $end_date;
			
			PortfolioInformation_Module_Model::GetMonthlyIntervalDatesStartDate($monthly_start, $monthly_end);
			
			$this->interval_begin_date = $monthly_start;
			
			$this->interval_end_date = $monthly_end;

			$this->isValid = true;

                
			$this->CalculateInvestmentReturn();
			
			$this->CalculateIndividualInvestmentReturn();
			
			//$this->CalculateIndividualChangeInValue();
			
			//$this->CalculateIndividualEstimatedIncome();
			
			//$this->CalculateIndividualTWRCumulative($this->start_date, $this->end_date);
			
			//$this->CalculateCombinedTWRCumulative($this->start_date, $this->end_date);
			
			//$this->CalculateEstimatedIncome();
			
			//$this->performance_summed['change_in_value'] = $this->ending_values_summed->value - $this->beginning_values_summed->value - $this->performance_summed['Flow']->amount - $this->performance_summed['Reversal']->amount;
		}
    }
	

    private function CalculateTWRFromIntervals($start_date, $end_date){
        global $adb;
        $query = "CALL TWR_CALCULATED(?, ?, @twr)";
        $adb->pquery($query, array($start_date, $end_date));

        $query = "SELECT @twr AS twr";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            $this->twr = $adb->query_result($result, 0, 'twr');
            return;
        }
        $this->twr = 0;
    }

    private function CalculateIndividualTWR($start_date, $end_date){
        global $adb;

        foreach($this->account_numbers AS $k => $v){
            PortfolioInformation_Module_Model::GetIntervalsForAccounts(array($v));

            $query = "CALL TWR_CALCULATED(?, ?, @twr)";
            $adb->pquery($query, array($start_date, $end_date));

            $query = "SELECT @twr AS twr";
            $result = $adb->pquery($query, array());
            if($adb->num_rows($result) > 0){
                $this->individual_twr[$v] = $adb->query_result($result, 0, 'twr');
            }else{
                $this->individual_twr[$v] = 0;
            }

            if($this->individual_twr[$v] >= 100){
                $this->individual_twr[$v] = 0;
            }
        }
    }


    public function CalculateIndividualTWRCumulative($start_date, $end_date){
        global $adb;

        $query = "SELECT netreturnamount 
                  FROM intervals_daily 
                  WHERE AccountNumber = ? 
                  AND IntervalEndDate BETWEEN ? AND ?";

        foreach($this->account_numbers AS $k => $v){
            $twr = 1;
            $result = $adb->pquery($query, array($v, $start_date, $end_date));

            if($adb->num_rows($result) > 0){
                while($x = $adb->fetchByAssoc($result)){
                    if($x['netreturnamount'] != 1) {
                        $twr *= $x['netreturnamount'];
                    }
                    else
                        $twr *= $x['netreturnamount'];
                }
            }

            if($twr != 1)
                $this->individual_twr[$v] = ($twr - 1) * 100;
            else
                $this->individual_twr[$v] = 0;

        }
    }



    public function CalculateCombinedTWRCumulative($start_date, $end_date){
        global $adb;
        $questions = generateQuestionMarks($this->account_numbers);
        $query = "SELECT SUM(intervalEndValue) / (SUM(intervalBeginValue) + (SUM(NetFlowAmount) + SUM(expenseamount))) AS netreturnamount, 
                         SUM(investmentreturn) AS investmentreturn, IntervalEndDate,
                         SUM(intervalBeginValue) AS intervalBeginValue, SUM(intervalEndValue) AS intervalEndValue,
                         SUM(NetFlowAmount) AS netflowamount,
                         SUM(expenseamount) AS expenseamount,
                         SUM(incomeamount) AS incomeamount
                  FROM intervals_daily 
                  WHERE AccountNumber IN ({$questions}) 
                  AND IntervalEndDate BETWEEN ? AND ?
                  GROUP BY intervalEndDate";

        $twr = 1;
        $result = $adb->pquery($query, array($this->account_numbers, $start_date, $end_date));

        if ($adb->num_rows($result) > 0) {
            
			while ($x = $adb->fetchByAssoc($result)) {
                if ($x['netreturnamount'] != 1 && $x['netreturnamount'] < 1.5 && $x['netreturnamount'] > -1.5 && abs($x['netreturnamount']) != 0) {
                    $twr *= $x['netreturnamount'];
                } else {

                }
            }
			
        }
		
        if ($twr != 1)
            $this->twr = ($twr - 1) * 100;
        else
            $this->twr = 0;


    }

    public function CalculateIndividualIRR($start_date, $end_date){
        global $adb;

        foreach($this->account_numbers AS $k => $v){
            #$query = "CALL TWR_CALCULATED(?, ?, @twr)";
            $query = 'CALL IRR_TABLE("?", ?, ?, @beginValue, @endValue, @startDate, @numDays)';
            $adb->pquery($query, array($v, $start_date, $end_date));

            $query = "SELECT @beginValue, @endValue, @startDate, @numDays";
            $result = $adb->pquery($query, array());

            $query = 'CALL IRR_ESTIMATOR(@beginvalue, @endvalue, @numdays, 1000, @irr)';
            $result = $adb->pquery($query, array());

            $query = 'SELECT @irr AS irr';
            $result = $adb->pquery($query, array());

            if($adb->num_rows($result) > 0){
                $this->individual_irr[$v] = $adb->query_result($result, 0, 'irr');
            }else{
                $this->individual_irr[$v] = 0;
            }
        }
    }

    private function GetCommissionAmount($account_number = null){
        global $adb;
        $params = array();
        if($account_number) {
            $and = " WHERE account_number = ?";
            $params[] = $account_number;
        }
        $query = "SELECT SUM(commission) AS commission FROM individual_performance {$and}";
        $result = $adb->pquery($query, $params);
        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, 'commission');
        }
        return 0;
    }
	
	public function GetInvestmentGain(){
	
		return $this->getCapAppreciation() + 
		$this->performance_summed['income_div_interest']->amount + 
		$this->performance_summed['Reversal']->amount +
		$this->performance_summed['Expense']->amount;
		
	}

    private function CalculateCapitalAppreciation(){
		$this->capital_appreciation = $this->ending_values_summed->value -
			($this->beginning_values_summed->value +
				$this->performance_summed['Flow']->amount +
				$this->performance_summed['Expense']->amount +
				$this->performance_summed['Income']->amount);
		$this->appreciation_percent = $this->capital_appreciation / $this->ending_values_summed->value * 100;
	}

    private function CalculateIndividualCapitalAppreciation(){
        foreach($this->account_numbers AS $k => $v){
            if($this->individual_performance_summed[$v]['Flow']->disable_performance != 1) {
                $tmp = $this->individual_start_values[$v]->value +
                    $this->individual_performance_summed[$v]['Flow']->amount +
                    $this->individual_performance_summed[$v]['Expense']->amount +
                    $this->individual_performance_summed[$v]['Income']->amount;
                $appreciation = $this->individual_end_values[$v]->value - $tmp;
                $this->individual_appreciation[$v] = $appreciation;
                $this->individual_appreciation_percent[$v] = $appreciation / $this->individual_end_values[$v]->value * 100;
            }
        }
    }
	
	public function getCapAppreciation(){
		
		$value = $this->GetEndingValuesSummed()->value - $this->GetBeginningValuesSummed()->value;
		$value = $value - $this->performance_summed['Flow']->amount;
		$value = $value - $this->performance_summed['income_div_interest']->amount;
		$value = $value - $this->performance_summed['Reversal']->amount;
		$value = $value - $this->performance_summed['Expense']->amount;
		
		return $value;
	}


    private function CalculateEstimatedIncome(){
        $projected = new ProjectedIncome_Model($this->account_numbers, $this->end_date);
        $calendar = CreateMonthlyCalendar($this->start_date, $this->end_date);
        $projected->CalculateMonthlyTotals($calendar);
        $this->estimated_income = $projected;
    }

    private function CalculateIndividualEstimatedIncome(){
        foreach($this->account_numbers AS $k => $v){
            $projected = new ProjectedIncome_Model(array($v), $this->end_date);
            $calendar = CreateMonthlyCalendar($this->start_date, $this->end_date);
            $projected->CalculateMonthlyTotals($calendar);
            $this->individual_performance_summed[$v]['estimated'] = $projected;
        }
    }

    private function CalculateInvestmentReturn(){
        $this->capital_appreciation = $this->ending_values_summed->value -
            ($this->beginning_values_summed->value +
                $this->performance_summed['Flow']->amount +
                $this->performance_summed['Expense']->amount);
        $this->appreciation_percent = $this->capital_appreciation / $this->ending_values_summed->value * 100;
    }

    private function CalculateIndividualInvestmentReturn(){
        foreach($this->account_numbers AS $k => $v){
            if($this->individual_performance_summed[$v]['Flow']->disable_performance != 1) {
                $tmp = $this->individual_start_values[$v]->value +
                    $this->individual_performance_summed[$v]['Flow']->amount +
                    $this->individual_performance_summed[$v]['Expense']->amount;

                $appreciation = $this->individual_end_values[$v]->value - $tmp;
                $this->individual_appreciation[$v] = $appreciation;
                $this->individual_appreciation_percent[$v] = $appreciation / $this->individual_end_values[$v]->value * 100;
            }
        }
    }

    private function CalculateIndividualChangeInValue(){
        foreach($this->account_numbers AS $k => $v){
            $this->individual_performance_summed[$v]['change_in_value'] = $this->individual_end_values[$v]->value -
                                                                          $this->individual_start_values[$v]->value -
                                                                          $this->individual_performance_summed[$v]['Flow']->amount -
                                                                          $this->individual_performance_summed[$v]['Reversal']->amount +
                                                                          $this->GetCommissionAmount($v);
            
            $this->individual_performance_summed[$v]['account_name'] = PortfolioInformation_Module_Model::GetAccountNameFromAccountNumber($v);
            $this->individual_performance_summed[$v]['account_type'] = PortfolioInformation_Module_Model::GetAccountTypeFromAccountNumber($v);
        }
    }

    public function GetCapitalAppreciation(){
        return $this->capital_appreciation;
    }

    public function IsReportValid(){
        return $this->isValid;
    }

    public function GetPerformance(){
        return $this->performance;
    }

    public function GetPerformanceSummed(){
        return $this->performance_summed;
    }

    public function WasStartDateChanged(){
        return $this->start_date_changed;
    }

    public function WasEndDateChanged(){
        return $this->end_date_changed;
    }

    public function GetBeginningValues(){
        return $this->beginning_values;
    }

    public function GetBeginningValuesSummed(){
        return $this->beginning_values_summed;
    }

    public function GetEndingValues(){
        return $this->ending_values;
    }

    public function GetEndingValuesSummed(){
        return $this->ending_values_summed;
    }

    /**
     * Get the date in given format.  IE:  %Y-%m-%d  with return Y-m-d format
     * @param $params
     * @return false|string
     */
    public function GetStartDateWithParams($params){
        return date($params, strtotime($this->start_date));
    }

    public function GetEndDateWithParams($params){
        return date($params, strtotime($this->end_date));
    }

    public function GetStartDate(){
        return date("Y-m-d", strtotime($this->start_date));
    }

    public function GetEndDate(){
        return date("Y-m-d", strtotime($this->end_date));
    }

    public function GetStartDateMDY(){
        return date("m/d/Y", strtotime($this->start_date));
    }

    public function GetEndDateMDY(){
        return date("m/d/Y", strtotime($this->end_date));
    }

    public function GetTransactionTypes(){
        return $this->transaction_types;
    }

    public function GetBeginningDateAltered(){
        return $this->beginning_date_altered;
    }

    public function GetIntervals(){
        return $this->intervals;
    }

    public function GetTWR(){
        return $this->twr;
    }

    public function GetIRR(){
        return $this->irr;
    }
	
	
    public function GetIndex($index){
        
		global $adb;
		
		if($index == 'GSPC'){
			$index = 'GSPC.INDX';
		} 
		
		if($this->since_inception){
			
			$questions = generateQuestionMarks($this->account_numbers);
			
			$result = $adb->pquery("select * from vtiger_portfolioinformation where 
			account_number in ({$questions})", array($this->account_numbers));
			
			$inception_date = $adb->query_result($result, 0, "inceptiondate");
			
			$start_date = date("Y-m-d", strtotime($inception_date . " - 1 DAY"));
			
		} else {
			$start_date = $this->start_date;
		}
		
		$url = "https://eodhistoricaldata.com/api/eod/$index?from=".$start_date."&to=".$this->end_date."&api_token=59838effd9cac&fmt=json";
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPGET, true);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
		
		$response = curl_exec($ch);
		
		curl_close($ch);
		
		$response = json_decode($response, true);
		
		$start_price = $response[0]['close'];
		
		$end_price = $response[count($response)-1]['close'];
		
		return round((($end_price - $start_price) / $start_price)*100, 2);
		
		//return getReferenceReturn($index,$this->start_date,$this->end_date);
	}
	
	public function get_portfolio_return($since_inception = false, $total_days = 0){
		
		$investment_gain = $this->ending_values_summed->value - ($this->beginning_values_summed->value + $this->performance_summed['Flow']->amount);
		
		if(!$investment_gain || !($this->beginning_values_summed->value + $this->performance_summed['Flow']->amount)){
			return 0;
		}
		
		$investment_gain = $investment_gain / ($this->beginning_values_summed->value + $this->performance_summed['Flow']->amount);
		
		if($since_inception && $total_days > 365){
			
			$y = 1 + $investment_gain;
			
			$n = bcdiv(($total_days/365), 1, 1);
			
			$power = 1 / $n;
			
			if($y < 0){
				$pow = (-1*$y) ** $power;
			} else {
				$pow = $y ** $power;
			}
			
			return round(($pow - 1) * 100, 2);
			
		} else {
			return round(($investment_gain * 100), 2);
		}
	}
	
	
    public function GetIntervalBeginDate(){
        return $this->interval_begin_date;
    }

    public function GetIntervalEndDate(){
        return $this->interval_end_date;
    }

    public function GetAppreciationPercent(){
        return $this->appreciation_percent;
    }

    public function GetIndividualSummedBalance(){
        return $this->individual_performance_summed;
    }

    public function GetIndividualBeginValues(){
        return $this->individual_start_values;
    }

    public function GetIndividualEndValues(){
        return $this->individual_end_values;
    }

    public function GetIndividualCapitalAppreciation(){
        return $this->individual_appreciation;
    }

    public function GetIndividualCapitalAppreciationPercent(){
        return $this->individual_appreciation_percent;
    }

    public function GetIndividualTWR(){
        return $this->individual_twr;
    }

    public function GetIndividualIRR(){
        return $this->individual_irr;
    }

    public function GetEstimatedIncome(){
        return $this->estimated_income;
    }

    public function ConvertPieToBenchmark($pie){
        $tmp = array();
        foreach($pie AS $k => $v){
            $tmp[$v['title']] = $v['percentage'];
        }
        return $tmp;
    }
    
	/*public function SetBenchmark($stocks, $cash, $bonds){
        $s1 = $this->GetIndex("GSPC");// * $stocks / 100;
        $s2 = $this->GetIndex("DVG");// * $stocks / 100;
        $b1 = $this->GetIndex("SP500BDT");// * $bonds / 100;
        $b2 = $this->GetIndex("IDCOTCTR");// * $bonds / 100;

        $this->benchmark = ($s1 * 0.3) + ($s2 * 0.3) + ($b1 * 0.3) + ($b2 * 0.1);
    }

    public function GetBenchmark(){
        return $this->benchmark;
    }*/

    public function GetDividendAccrualAmount(){
        return $this->dividend_accrual;
    }

}
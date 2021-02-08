<?php

class cIntervalData{public $accountNumber, $intervalBeginDate, $intervalEndDate, $intervalBeginValue, $intervalEndValue, $netFlowAmount,
                           $netReturnAmount, $expenseAmount, $journalAmount, $tradeAmount, $intervalType, $investmentReturn;}

class DayBalances{public $date, $value;}
class DayTransactions{public $transactionType, $transactionActivity, $amount;}

class cIntervals{
    protected $account_number, $transactions, $balances, $custodianAccess, $intervals;
    protected $lastDate, $lastBalance;//Tokens

    public function __construct($account_number){
        $this->account_number = $account_number;
        $this->ResetMemberVariables();
    }

    public function ResetMemberVariables(){
        $this->transactions = array();
        $this->balances = array();
        $this->intervals = array();
        $this->lastDate = null;
        $this->lastBalance = 0;
        $this->custodianAccess = new CustodianClassMapping($this->account_number);
    }

    public function ResetIntervals(array $account_number){
        global $adb;

        $query = "DELETE FROM intervals_daily WHERE AccountNumber = ?";
        $adb->pquery($query, array($account_number));
    }

    protected function SetGroupedTransactionsForDay($date){
        global $adb;

        if(!empty($this->transactions))
            $this->transactions = array();//Reset so we don't double up on transactions

        $query = "SELECT SUM(CONCAT(operation, ABS(net_amount))) AS amount, transaction_type, transaction_activity, trade_date, operation 
                  FROM vtiger_transactions t 
                  JOIN vtiger_transactionscf cf USING (transactionsid) 
                  JOIN vtiger_crmentity e ON e.crmid = t.transactionsid
                  WHERE account_number = ? 
                  AND trade_date = ?
		          AND e.deleted = 0 
		          GROUP BY transaction_type, transaction_activity";
        $result = $adb->pquery($query, array($this->account_number, $date));
        if($adb->num_rows($result) > 0){
            while($x = $adb->fetchByAssoc($result)){
                $tmp = new DayTransactions();
                $tmp->amount = $x['amount'];
                $tmp->transactionActivity = $x['transaction_activity'];
                $tmp->transactionType = $x['transaction_type'];
#                $this->transactions[$x['trade_date']][] = $tmp;
#                $this->transactions[$x['trade_date']][$x['transaction_type']][$x['transaction_activity']] = $x;
                $this->transactions[$x['trade_date']][strtolower($x['transaction_type'])][strtolower($x['transaction_activity'])] = $tmp;
            }
        }
    }

    protected function SetAllGroupedTransactionsBetweenDates($sdate, $edate){
        global $adb;

        if(!empty($this->transactions))
            $this->transactions = array();//Reset so we don't double up on transactions

        $query = "SELECT SUM(CONCAT(operation, ABS(net_amount))) AS amount, transaction_type, transaction_activity, trade_date, operation 
                  FROM vtiger_transactions t 
                  JOIN vtiger_transactionscf cf USING (transactionsid) 
                  JOIN vtiger_crmentity e ON e.crmid = t.transactionsid
                  WHERE account_number = ? 
                  AND trade_date between ? AND ?
		          AND e.deleted = 0 
		          GROUP BY trade_date, transaction_type, transaction_activity";
        $result = $adb->pquery($query, array($this->account_number, $sdate, $edate));
        if($adb->num_rows($result) > 0){
            while($x = $adb->fetchByAssoc($result)){
                $tmp = new DayTransactions();
                $tmp->amount = $x['amount'];
                $tmp->transactionActivity = $x['transaction_activity'];
                $tmp->transactionType = $x['transaction_type'];

#                $this->transactions[$x['trade_date']][] = $tmp;
                $this->transactions[$x['trade_date']][strtolower($x['transaction_type'])][strtolower($x['transaction_activity'])] = $tmp;
            }
        }
    }

    protected function SetAllGroupedBalancesBetweenDates($sdate, $edate){
        if(!empty($this->balances))
            $this->balances = array();

        $balances = $this->custodianAccess->portfolios::BalanceBetweenDates(array($this->account_number), $sdate, $edate);
        foreach ($balances[$this->account_number] AS $k => $v) {
            $tmp = new DayBalances();
            $tmp->date = $v['date'];
            $tmp->value = $v['value'];
            $this->balances[] = $tmp;
        }
    }

    public function CalculateDayType($date, $type){
        $total = 0;
        foreach($this->transactions[$date][$type] AS $k => $v){
            $total += $v->amount;
        }

        return $total;
    }

    public function CalculateIntervals($sdate, $edate){
        global $adb;
        $this->ResetIntervals(array($this->account_number));
        $this->SetAllGroupedTransactionsBetweenDates($sdate, $edate);
        $this->SetAllGroupedBalancesBetweenDates($sdate, $edate);

        foreach($this->balances AS $k => $balanceDay){
            if(is_null($this->lastDate)){
                $this->lastDate = GetDateMinusOneDay($balanceDay->date);
            }
            $interval = new cIntervalData();
            $interval->accountNumber = $this->account_number;
            $interval->intervalBeginDate = $balanceDay->date;
            $interval->intervalBeginValue = $this->lastBalance;
            $interval->intervalEndDate = $balanceDay->date;
            $interval->intervalEndValue = $balanceDay->value;
            $interval->expenseAmount = $this->CalculateDayType($balanceDay->date, 'expense');
            $interval->netFlowAmount = $this->CalculateDayType($balanceDay->date, 'flow');
            $interval->tradeAmount = $this->CalculateDayType($balanceDay->date, 'trade');
            $interval->intervalType = 'Daily';

            if($interval->intervalBeginValue == 0)
                $beginValue = 1;
            else
                $beginValue = $interval->intervalBeginValue;

            $interval->netReturnAmount =
                (($interval->intervalEndValue - $interval->intervalBeginValue) -
                    ($interval->netFlowAmount + $interval->expenseAmount)) /
                $beginValue;

            if($interval->intervalBeginValue == 0 && $interval->netFlowAmount == 0)
                $interval->netReturnAmount = 1;

            $interval->investmentReturn = $interval->intervalEndValue - ($interval->netFlowAmount + $interval->expenseAmount) - $interval->intervalBeginValue;

            if($interval->netReturnAmount == 0)
                $interval->netReturnAmount = 1;

            $this->intervals[] = $interval;
            $this->lastDate = $balanceDay->date;
            $this->lastBalance = $balanceDay->value;
        }
        $writer = "";
        $counter = 0;
        $params = array();
        foreach($this->intervals AS $k => $v){
            if($counter >= 100){
                $writer = rtrim($writer, ', ');
                $query = "INSERT INTO intervals_daily (AccountNumber, IntervalBeginDate, IntervalBeginValue, IntervalEndDate, IntervalEndValue, expenseamount, NetFlowAmount, tradeamount, investmentreturn, NetReturnAmount, intervalType)
                          VALUES {$writer}";
                $adb->pquery($query, $params, true);
                $counter = 0;
                $writer = "";
                $params = array();
            }
            $writer .= "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?), ";
            $params[] = array($v->accountNumber, $v->intervalBeginDate, $v->intervalBeginValue, $v->intervalEndDate, $v->intervalEndValue, $v->expenseAmount, $v->netFlowAmount, $v->tradeAmount ,$v->investmentReturn, $v->netReturnAmount, $v->intervalType);
            $counter++;
        }

        if(!empty($params)) {
            $writer = rtrim($writer, ', ');
            $query = "INSERT INTO intervals_daily (AccountNumber, IntervalBeginDate, IntervalBeginValue, IntervalEndDate, IntervalEndValue, expenseamount, NetFlowAmount, tradeamount, investmentreturn, NetReturnAmount, intervalType)
                      VALUES {$writer}";
            $adb->pquery($query, $params, true);
        }
#        $data = $map->portfolios::GetBeginningBalanceAsOfDate(array($account_number), $date);
    }
}

/*
class cIntervalData{public $accountNumber, $intervalBeginDate, $intervalEndDate, $intervalBeginValue, $intervalEndValue, $netFlowAmount,
                           $netReturnAmount, $expenseAmount, $journalAmount, $tradeAmount, $intervalType, $investmentReturn;}

 */
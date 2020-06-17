<?php


require_once('include/utils/omniscientCustom.php');
require_once("libraries/reports/Portfolios.php");
require_once("libraries/reports/cPPerformance.php");

class cPortfolioInfo{
    
    private $neg_transactions = array();
    private $pos_transactions = array();
    private $withdrawals = array();
    private $contributions = array();
    private $dividends = array();
    private $interest = array();    
    private $qtr = array();
    private $ytd = array();
    private $lyr = array();
    private $inception = array();
    private $management = array();//Management fees
    private $expenses = array();//Expense fees
    private $transactions = array();//All transactions
    private $assets = array();//All assets

    private $symbols = array();
    private $descriptions = array();
    private $actions = array();
    private $security_types = array();
    
    public function __construct($pids, $special_instructions = null) {
//        if(!$_SESSION['pulled_transactions'])//We only want to enter this once per page.  There are times this class is called twice in a single page requesting different data
        {
            $ids = explode(",",$pids);
            $skip_copy = 1;//by default, we assume we skip the transactions pull
            foreach($ids AS $k => $v)
                if(!$_SESSION["pulled_ids"][$v])
                    $skip_copy = 0;//We haven't pulled the portfolio transactions yet, make sure we do!

            foreach($ids AS $k => $v)
                    $_SESSION["pulled_ids"][$v] = 1;

//            if(!$skip_copy)//We don't want to skip the transaction pull
                $this->CopyTransactions($pids);//First get the latest transaction info from PC
            $_SESSION['pulled_transactions'] = 1;
        }
        $this->SetupInfo($pids, $special_instructions);//Setup the class for use
    }
    
    /**Return management fees*/
    public function GetManagementFees()
    {
        return $this->management;
    }
    /**Get the portfolio accountname from the record ID*/
    public function GetPortfolioAccountName($hhid)
    {
        global $adb;
        $query = "SELECT accountname 
                  FROM vtiger_account
                  WHERE accountid=?";
        $result = $adb->pquery($query,array($hhid));
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, "accountname");
        else
        {
            $query = "SELECT CONCAT(c.firstname, ' ', c.lastname) AS accountname
                      FROM vtiger_contactdetails c
                      WHERE c.contactid = ?";
            $result = $adb->pquery($query, array($hhid));
            if($adb->num_rows($result) > 0)
                return $adb->query_result($result, 0, "accountname");
        }
        
        return 0;
    }
    
    /*Return a list of symbols pulled*/
    public function ReturnSymbols()
    {
        return $this->symbols;
    }
    
    /*Return a list of descriptions*/
    public function ReturnDescriptions()
    {
        return $this->descriptions;
    }
    
    /*Return a list of actions*/
    public function ReturnActions()
    {
        return $this->actions;
    }
    
    /*Return a list of security types*/
    public function ReturnSecurityTypes()
    {
        return $this->security_types;
    }
    
    /*Separate the symbols, descriptions, actions and security_types from the given transactions*/
    public function SeparateTransactionsForFiltering($transactions)
    {
        foreach($transactions AS $k => $v)
        {
            if($v['security_symbol'])
                $symbols[] = $v['security_symbol'];
            if($v['transaction_description'])
                $descriptions[] = $v['transaction_description'];
            if($v['activity_name'])
                $actions[] = $v['activity_name'];
            if($v['code_description'])
                $security_types[] = $v['code_description'];
        }

        if($symbols)
            $symbols = array_unique($symbols);
        if($descriptions)
            $descriptions = array_unique($descriptions);
        if($actions)
            $actions = array_unique($actions);
        if($security_types)
            $security_types = array_unique($security_types);
        
        $this->symbols = $symbols;
        $this->descriptions = $descriptions;
        $this->actions = $actions;
        $this->security_types = $security_types;
    }
    
    /**Convert the sql date to a proper format*/
    public function ConvertDate($date)
    {   
        $time = strtotime($date);
        $time = date('Y-m-d 00:00:00', $time);
        return $time;
    }
    /*Copy all transactions for the given portfolio ID's from PC to the crm*/
    public function CopyTransactions($pids)
    {
        global $adb;
        $myServer = "lanserver2n";
        $myUser = "syncuser";
        $myPass = "Consec11";
        $myDB = "PortfolioCenter";
        
        if(!$pids)
            return;
        //connection to the database
        $dbhandle = mssql_connect($myServer, $myUser, $myPass);// or die("Couldn't connect to SQL Server on $myServer");
        if(!$dbhandle)
        {
            //echo "NO HANDLE!<br />"; Felipe : Changes 2016-07-25 create problem in portal
        }
        else 
        {
            $query = "SELECT * FROM transactions
                      WHERE portfolioID IN ({$pids}) ORDER BY TransactionID DESC";//Get all transactions
            
//echo $query;
        //        AND SymbolID = 1
        //        AND TransactionID NOT IN(6430380, 7235746, 7562524, 7569131, 8134001, 8138838, 8139299, 8143422, 8143423, 8144271, 8144273, 8144275, 8144277, 8144279, 8144281, 8144283, 8144285, 8144287, 8144289, 8144291, 8144293, 8144295, 8144297, 8144299, 8144301, 8144303, 8144305, 8144307, 8144309, 8144311, 8144313, 8144315, 8144317, 8144319, 8144321, 8144323, 8144325, 8144327, 8144329, 8144331, 8144333, 8144335, 8144337, 8155874, 8155875, 8968318, 8968319, 8980893, 8980894, 8980895, 8980896, 8993660, 9013353, 9024966, 9060259, 9061252, 9099495, 9102363, 9102364, 9113984, 9113985, 9113986, 9179389)";

            $transactions = mssql_query($query);
            if($transactions)
            while($row = mssql_fetch_array($transactions))
            {
                $query = "UPDATE vtiger_pc_transactions 
                          SET symbol_id = ?, money_id = ?, net_amount = ?, total_value = ?, quantity = ? 
                          WHERE transaction_id = ?";
                
                $transaction_id = $row['TransactionID'];
                $id_list[] = $transaction_id;
                $portfolio_id = $row['PortfolioID'];
                $sell_lot_id = $row['SellLotID'];
                $trade_lot_id = $row['TradeLotID'];
                $link_id = $row['LinkID'];
                $custodian_id = $row['CustodianID'];
                $symbol_id = $row['SymbolID'];
                $activity_id = $row['ActivityID'];
                $money_id = $row['MoneyID'];
                $broker_id = $row['BrokerID'];
                $report_as_type_id = $row['ReportAsTypeID'];
                $quantity = $row['Quantity'];
                $total_value = $row['TotalValue'];
                $conversion_value = $row['ConversionValue'];
                $accrued_interest = $row['AccruedInterest'];
                $yield_at_purchase = $row['YieldAtPurchase'];
                $advisor_fee = $row['AdvisorFee'];
                $amounter_per_share = $row['AmountPerShare'];
                $other_fee = $row['OtherFee'];
                $net_amount = $row['NetAmount'];
                
                $settlement_date = $row['SettlementDate'];
                $settlement_date = $this->ConvertDate($settlement_date);
                $trade_date = $row['TradeDate'];
                $trade_date = $this->ConvertDate($trade_date);
                $original_trade_date = $row['OriginalTradeDate'];
                $original_trade_date = $this->ConvertDate($original_trade_date);
                $entry_date = $row['EntryDate'];
                $entry_date = $this->ConvertDate($entry_date);
                $link_date = $row['LinkDate'];
                $link_date = $this->ConvertDate($link_date);
                $last_modified_date = $row['LastModifiedDate'];
                $last_modified_date = $this->ConvertDate($last_modified_date);
                
                $odd_income_payment_flag = $row['OddIncomePaymentFlag'];
                $long_position_flag = $row['LongPositionFlag'];
                $reinvest_gains_flag = $row['ReinvestGainsFlag'];
                $reinvest_income_flag = $row['ReinvestIncomeFlag'];
                $keep_fractional_shares_flag = $row['KeepFractionalSharesFlag'];
                $taxable_prev_year_flag = $row['TaxablePrevYearFlag'];
                $complete_transaction_flag = $row['CompleteTransactionFlag'];
                $is_reinvested_flag = $row['IsReinvestedFlag'];
                $notes = $row['Notes'];
                $principal = $row['Principal'];
                $add_sub_status_type_id = $row['AddSubStatusTypeID'];
                $contribution_type_id = $row['ContributionTypeID'];
                $matching_method_id = $row['MatchingMethodID'];
                $custodian_account = $row['CustodianAccount'];
                $original_link_account = $row['OriginalLinkAccount'];
                $origination_id = $row['OriginationID'];
                $trans_link_id = $row['TransLinkID'];
                $status_type_id = $row['StatusTypeID'];
                $last_modified_user_id = $row['LastModifiedUserID'];
                $dirty_flag = $row['DirtyFlag'];
                $invalid_cost_basis_flag = $row['InvalidCostBasisFlag'];
                $cost_basis_adjustment = $row['CostBasisAdjustment'];
                $security_split_flag = $row['SecuritySplitFlag'];
                $reset_cost_basis_flag = $row['ResetCostBasisFlag'];
                
            $query = "INSERT INTO vtiger_pc_transactions (transaction_id, portfolio_id, sell_lot_id, trade_lot_id, link_id, custodian_id, symbol_id, activity_id, 
                                                          money_id, broker_id, report_as_type_id, quantity, total_value, conversion_value, accrued_interest, 
                                                          yield_at_purchase, advisor_fee, amount_per_share, other_fee, net_amount, settlement_date, trade_date, 
                                                          origina_trade_date, entry_date, link_date, odd_income_payment_flag, long_position_flag, reinvest_gains_flag, 
                                                          reinvest_income_flag, keep_fractional_shares_flag, taxable_prev_year_flag, complete_transaction_flag, 
                                                          is_reinvested_flag, notes, principal, add_sub_status_type_id, contribution_type_id, matching_method_id, 
                                                          custodian_account, original_link_account, origination_id, last_modified_date, trans_link_id, status_type_id, 
                                                          last_modified_user_id, dirty_flag, invalid_cost_basis_flag, cost_basis_adjustment, security_split_flag, 
                                                          reset_cost_basis_flag) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
                                                                                          ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                      ON DUPLICATE KEY UPDATE symbol_id = ?, money_id = ?, net_amount = ?, total_value = ?, quantity = ?, trade_date ";
                $adb->pquery($query, array($transaction_id,
                                          $portfolio_id,
                                          $sell_lot_id,
                                          $trade_lot_id,
                                          $link_id,
                                          $custodian_id,
                                          $symbol_id,
                                          $activity_id,
                                          $money_id,
                                          $broker_id,
                                          $report_as_type_id,
                                          $quantity,
                                          $total_value,
                                          $conversion_value,
                                          $accrued_interest,
                                          $yield_at_purchase,
                                          $advisor_fee,
                                          $amounter_per_share,
                                          $other_fee,
                                          $net_amount,
                                          $settlement_date,
                                          $trade_date,
                                          $original_trade_date,
                                          $entry_date,
                                          $link_date,
                                          $odd_income_payment_flag,
                                          $long_position_flag,
                                          $reinvest_gains_flag,
                                          $reinvest_income_flag,
                                          $keep_fractional_shares_flag,
                                          $taxable_prev_year_flag,
                                          $complete_transaction_flag,
                                          $is_reinvested_flag,
                                          $notes,
                                          $principal,
                                          $add_sub_status_type_id,
                                          $contribution_type_id,
                                          $matching_method_id,
                                          $custodian_account,
                                          $original_link_account,
                                          $origination_id,
                                          $last_modified_date,
                                          $trans_link_id,
                                          $status_type_id,
                                          $last_modified_user_id,
                                          $dirty_flag,
                                          $invalid_cost_basis_flag,
                                          $cost_basis_adjustment,
                                          $security_split_flag,
                                          $reset_cost_basis_flag, 
                                          $symbol_id, $money_id, $net_amount, $total_value, $quantity, $trade_date));
            }
            $query = "SELECT transaction_id FROM vtiger_pc_transactions WHERE portfolio_id IN ({$pids}) ORDER BY transaction_id DESC";
            $result = $adb->pquery($query, array());
            if($result)
            foreach($result AS $k => $v)
                $crm_list[] = $v['transaction_id'];
            
            $delete_ids = array_merge($id_list, $crm_list);
            $delete_ids = RemoveDuplicates($delete_ids);
            $query = "INSERT IGNORE INTO vtiger_pc_transactions_deleted (transaction_id, deleted) VALUES (?, 1)";
            
            foreach($delete_ids AS $k => $v)
                $adb->pquery($query, array($v));
        }
    }
    
    public function GetInvestments()
    {
        $investments = array();
        $investments['neg_transactions'] = $this->neg_transactions;
        $investments['pos_transactions'] = $this->pos_transactions;
        $investments['withdrawals'] = $this->withdrawals;
        $investments['contributions'] = $this->contributions;
        $investments['dividends'] = $this->dividends;
        $investments['interest'] = $this->interest;
        $investments['qtr'] = $this->qtr;
        $investments['ytd'] = $this->ytd;
        $investments['lyr'] = $this->lyr;
        $investments['inception'] = $this->inception;
//        echo "END DATE: {$this->inception['end']['date']}<br />";
        $investments['management'] = $this->management;
        $investments['expenses'] = $this->expenses;
        
        return $investments;
    }

    public function SetupIntervalDates($portfolio_ids)
    {
        global $adb;
        $query = "SELECT trade_date FROM vtiger_pc_transactions
                  LEFT JOIN vtiger_pc_transactions_deleted d ON d.transaction_id = vtiger_pc_transactions.transaction_id
                  WHERE portfolio_id IN ({$portfolio_ids})
                  AND trade_date > 0
                  AND status_type_id = 100
                  AND d.deleted is null
                  ORDER BY trade_date ASC
                  LIMIT 1";
//                  echo "PIDS: {$portfolio_ids}<br />";
        $result = $adb->pquery($query, array());
        $inception['begin']['date'] = $adb->query_result($result, 0, "trade_date");
        
/*        $query = "SELECT trade_date FROM vtiger_pc_transactions
                  WHERE portfolio_id IN ({$portfolio_ids})
                  AND trade_date > 0
                  ORDER BY trade_date DESC
                  LIMIT 1";
        $result = $adb->pquery($query, array());
//        $inception['end']['date'] = $adb->query_result($result, 0, "trade_date");*/
        $m = date('m');
        $d = date('d');
        $Y = date('Y');
        
        $inception['end']['date'] = date('Y-m-d 00:00:00',mktime(0,0,0,$m,$d,$Y));
        $this->inception = $inception;
        
        $qtrStart = date('Y-m-d 00:00:00',mktime(0,0,0,$m-3,$d,$Y));
        $qtrEnd = date('Y-m-d 00:00:00',mktime(0,0,0,$m,$d,$Y));
        $this->qtr['begin']['date'] = $qtrStart;
        $this->qtr['end']['date'] = $qtrEnd;
        
        $ytdStart = date('Y-m-d 00:00:00',mktime(0,0,0,$m,$d,$Y-1));
        $ytdEnd = date('Y-m-d 00:00:00',mktime(0,0,0,$m,$d,$Y));
        $this->ytd['begin']['date'] = $ytdStart;
        $this->ytd['end']['date'] = $ytdEnd;
        
        $lyStart = date('Y-m-d 00:00:00',mktime(0,0,0,13,0,$Y-2));
        $lyEnd = date('Y-m-d 00:00:00',mktime(0,0,0,13,0,$Y-1));

//        if($inception['begin']['date'] > $lyStart)
//            $lyStart = $inception['begin']['date'];
        $this->lyr['begin']['date'] = $lyStart;
        $this->lyr['end']['date'] = $lyEnd;
    }
    
    /**Loops through an array of dates figuring out when the start of the month and end of the month should be for each value.
     * 
     */
    public function AddStartAndEndOfMonthsToArray($dates)
    {
        
    }
    
    /**Get the interval values between two dates
     * 
     */
    public function GetIntervalValues($accounts, $sdate, $edate)
    {
///        echo "GET INTERVALS SDATE {$sdate}, EDATE: {$edate}<br />";
        $values = $this->GetAccountSymbolValues($accounts, $sdate, $edate);
        $val = $this->CalculateTotalValues($values);
//        $v = $val['grand_totals']['grand_totals']['market_value'];
        $v = round($val['grand_totals']['grand_totals']['value'], 2);
///        echo "VALUE IS {$v}<br />";
        return $v;
    }
    
    /**Get all ending values for each month specified.  sdate is the start month, edate is the end month.  Each ending value is the 'ending value' for the last day
     * of each month returned.  If sdate for example is september 25th and edate is december 20th, the first month will be the value as it is at the end of september.
     */
    public function GetMonthlyValues($accounts, $sdate=null, $edate=null)
    {
        $sdate = str_replace("00:00:00", "", $sdate);
        $sdate = str_replace(" ", "", $sdate);
        $edate = str_replace("00:00:00", "", $edate);
        $edate = str_replace(" ", "", $edate);
        
        $d1 = date('Y-m-d',strtotime($sdate));
        $d2 = date('Y-m-d',strtotime($edate));

        $d1 = new DateTime($d1);
        $d2 = new DateTime($d2);
        $interval = date_diff($d1, $d2);
        $years = $interval->format("%y");
        $num_months = ($years*12)+$interval->format("%m");
        
        $monthly_values = array();
        $last_month_value = 0;
        for($x = 0; $x <= $num_months; $x++)
        {
            $m = date("m", strtotime($sdate));
            $m+=$x;
            $d = date("d", strtotime($sdate));
            $Y = date("Y", strtotime($sdate));
            
            $month_start = date("Y-m-d", mktime(0, 0, 0, $m+1, 1, $Y));
            $month_end = date("Y-m-t", mktime(0, 0, 0, $m+1, 1, $Y));
            
            if(!$last_month_value)
            {
                $tmp = $this->GetAccountSymbolValues($accounts, null, $month_start, true);
                $val = $this->CalculateTotalValues($tmp);
                $last_month_value = $val['grand_totals']['grand_totals']['value'];
            }
///            $tmp = $this->GetAccountSymbolValues($accounts, null, $month_start);
///            $start_values = $this->CalculateTotalValues($tmp);
            
            $tmp = $this->GetAccountSymbolValues($accounts, null, $month_end, true);
            $end_values = $this->CalculateTotalValues($tmp);
            
            $monthly_values[$month_start] = $last_month_value;
            $monthly_values[$month_end] = $end_values['grand_totals']['grand_totals']['value'];
            
            $last_month_value = $end_values['grand_totals']['grand_totals']['value'];//Set the last month value to equal the end of this month so start copies properly
        }
        return $monthly_values;
    }

    public function GetAccountTotalValueAsOfDate($date, $onlyPositiveValues = true)
    {
        global $adb;
        $query = "SELECT SUM(quantity) AS total_quantity, SUM(cost_basis_adjustment) AS cba_total, account_number, price_adjustment, code_description, description, 
                  security_symbol, symbol_id, security_type_id, trade_date, current_price 
                  FROM t_TWR 
                  WHERE trade_date <= ?
                  GROUP BY symbol_id";
        $result = $adb->pquery($query, array($date));
        
        $tmp = array();
        $tv = 0;
        foreach($result AS $k => $v)
        {
            $price = $this->GetSecurityPriceAsOfDate($v['symbol_id'], $date);
//            $price = $this->GetLatestSecurityPrice($v['symbol_id']);
            if($v['security_type_id'] == 11)
                $price = 1;
            $v['latest_price'] = $price;
            $v['latest_value'] = $price*$v['total_quantity'];
            $tv+=$v['latest_value'];
        }
        return $tv;
//        return $tmp;
    }    
/*    
    public function CalculateTWRUsingIntervals($sdate, $edate, $interval_values, $accounts, $inception_date)
    {
        $sdate = str_replace("00:00:00", "", $sdate);
        $sdate = str_replace(" ", "", $sdate);
        $edate = str_replace("00:00:00", "", $edate);
        $edate = str_replace(" ", "", $edate);
        
        $last_date = $sdate;
        $current_date = $sdate;
        $last_value = 0;
        $current_value = 0;
        $r = array();
        $count = 0;
        if($sdate <= $inception_date)
        {
            $isfirst = true;
            $isInception = true;
        }
        else
            $isfirst = false;
//        echo "START DATE: {$sdate}, END DATE: {$edate}<br />";
//        echo "THE ENDING VALUE IS: " . $this->GetIntervalValues($accounts, $sdate, $edate) . "<br />";
////        $start_value = $this->GetIntervalValues($accounts, "1920-01-01", $sdate);
        $start_value = $this->GetAccountTotalValueAsOfDate($sdate);

        
///        echo "START VALUE: {$start_value}<br />";
        $current_value = $start_value;
///        echo "START VALUE: {$current_value}<br />";
        foreach($interval_values AS $k => $v)
            if($k >= $sdate && $k <= $edate)
            {
///                echo "{$count} - DATE: <strong>{$k}</strong>, TRANSACTION AMOUNT: <strong>{$v}</strong><br />";
///                echo "SDATE {$sdate}, EDATE: {$k}<br />";
                $count++;
//                $transaction_amount = $v;
//                $values = $this->GetAccountSymbolValues($accounts, $sdate, $k, true);
//                $val = $this->CalculateTotalValues($values);
//                $va = $val['grand_totals']['grand_totals']['value'];
                $last_value = $current_value;
                if($last_value == 0)
                    $dbz = 1;//dbz stands for divide by zero.  In case last_value happens to equal 0, we won't have an issue when dividing.
                else
                    $dbz = $last_value;
                $current_value = $this->GetAccountTotalValueAsOfDate($k);//$this->GetIntervalValues($accounts, "1920-01-01", $k);
                if(!$isInception)
                {
                    $dbz = $current_value;
//                    $last_value = $current_value;
                    $isInception = true;
                }
///                echo "CURRENT VALUE FOR: {$k}, {$current_value}<br />";
                $t = ( $current_value - $v - $last_value) / $dbz;
                if(!$isfirst)
                {
                    $tmpVal = round(($current_value - $v - $last_value) / $dbz, 3);
                    $r[] = $tmpVal;
                }
                else
                {
///                    echo "IS FIRST!<br />";
                    $isfirst = false;
                }
                
//                echo "( ({$current_value} - {$v}) - {$last_value}) / {$dbz} = {$t}<br />";
            }
            
        $return = 1;
        foreach($r AS $k => $v)
        {
            $tmp = $v+1;
///             echo "K: {$k}, V:{$v} ---- RETURN CALCULATION: RETURN = {$return} * {$tmp}<br />";
            if($return == 0)
                $return = $v;
            else
            {
                $tmp = $v+1;
                $return = $return * ($tmp);
            }
            
///            echo "RETURN SO FAR: {$return}<br />";
        }
        
        $start = strtotime($sdate);
        $end = strtotime($edate);

        $datediff = ceil(abs($end - $start) / 86400);
        
        $type = "";//The type of return, annualized or not
        if($datediff >= 365)
        {
            $exponent = 365/$datediff;

    ///        echo "POWER CHECK: {$check}<br />";
    ///        echo "DATE DIFFERENCE {$edate} - {$sdate} = {$datediff} ---- Exponent: {$exponent}<br />";
    ///        echo "RETURN BEFORE ANNUALIZED: {$return}<br />";
///            echo "Annualized: {$sdate} - {$edate}<br />";
///            echo "RETURN: {$return}, EXPONENT: {$exponent}<br />";
            $return = pow(($return), $exponent);
            $type = "Annualized";
        }
        else
        {
            $type = "Monthly";
///            echo "Not Annualized: {$sdate} - {$edate}<br />";
        }
        $return = $return-1;
        $return *= 100;
        
        $return = round($return, 2);
///        echo "ANNUALIZED RETURN: {$return}<br />";
        return array("type"=>$type, "value"=>$return);
    }
    */
    /**
     * Calculate the Time Weight Return (TWR) based on the start and end dates using the interval_values as stopping points for the calculations
     * @param type $sdate
     * @param type $edate
     * @param type $interval_values 
     */
/*    public function CalculateTWRUsingIntervals($sdate, $edate, $interval_values, $accounts)
    {
        $sdate = str_replace("00:00:00", "", $sdate);
        $sdate = str_replace(" ", "", $sdate);
        $edate = str_replace("00:00:00", "", $edate);
        $edate = str_replace(" ", "", $edate);
        
        $last_date = $sdate;
        $current_date = $sdate;
        $last_value = 0;
        $current_value = 0;
        $r = array();
        $count = 0;
        $isfirst = true;
//        echo "START DATE: {$sdate}, END DATE: {$edate}<br />";
//        echo "THE ENDING VALUE IS: " . $this->GetIntervalValues($accounts, $sdate, $edate) . "<br />";
        foreach($interval_values AS $k => $v)
            if($k >= $sdate && $k <= $edate)
            {
///                echo "{$count} - DATE: <strong>{$k}</strong>, TRANSACTION AMOUNT: <strong>{$v}</strong><br />";
//                echo "SDATE {$sdate}, EDATE: {$k}<br />";
                $count++;
//                $transaction_amount = $v;
//                $values = $this->GetAccountSymbolValues($accounts, $sdate, $k, true);
//                $val = $this->CalculateTotalValues($values);
//                $va = $val['grand_totals']['grand_totals']['value'];
                $last_value = $current_value;
                if($last_value == 0)
                    $dbz = 1;//dbz stands for divide by zero.  In case last_value happens to equal 0, we won't have an issue when dividing.
                else
                    $dbz = $last_value;
                $current_value = $this->GetIntervalValues($accounts, $sdate, $k);
                echo "CURRENT VALUE FOR: {$k}, {$current_value}<br />";
                $t = ( $current_value - $v - $last_value) / $dbz;
                if(!$isfirst)
                {
                    $tmpVal = round(($current_value - $v - $last_value) / $dbz, 3);
                    $r[] = $tmpVal;
                }
                else
                {
                    $isfirst = false;
                }
                echo "( ({$current_value} - {$v}) - {$last_value}) / {$dbz} = {$t}<br />";
            }
            
        $return = 1;
        foreach($r AS $k => $v)
        {
            $tmp = $v+1;
             echo "K: {$k}, V:{$v} ---- RETURN CALCULATION: RETURN = {$return} * {$tmp}<br />";
            if($return == 0)
                $return = $v;
            else
            {
                $tmp = $v+1;
                $return = $return * ($tmp);
            }
            
///            echo "RETURN SO FAR: {$return}<br />";
        }
        
        $start = strtotime($sdate);
        $end = strtotime($edate);

        $datediff = ceil(abs($end - $start) / 86400);
        
        $type = "";//The type of return, annualized or not
        if($datediff >= 365)
        {
            $exponent = 365/$datediff;

            $check = pow(1.442, 0.1676619);
    ///        echo "POWER CHECK: {$check}<br />";
    ///        echo "DATE DIFFERENCE {$edate} - {$sdate} = {$datediff} ---- Exponent: {$exponent}<br />";
    ///        echo "RETURN BEFORE ANNUALIZED: {$return}<br />";
            echo "Annualized: {$sdate} - {$edate}<br />";
            $return = pow(($return), $exponent);
            $type = "Annualized";
        }
        else
            echo "Not Annualized: {$sdate} - {$edate}<br />";
        $return = $return-1;
        $return *= 100;
        
        echo "ANNUALIZED RETURN: {$return}<br />";
        return array("type"=>$type, "value"=>$return);
    }
    */
    /**Calculate the Time Weighted Return (TWR) based on monthly values passed in and the specified dates*/
    public function CalculateTWR($sdate, $edate, $monthly_values)
    {
        $sdate = str_replace("00:00:00", "", $sdate);
        $sdate = str_replace(" ", "", $sdate);
        $edate = str_replace("00:00:00", "", $edate);
        $edate = str_replace(" ", "", $edate);
        
        $m = date("m", strtotime($sdate));
        $d = date("d", strtotime($sdate));
        $Y = date("Y", strtotime($sdate));

        $sdate = date("Y-m-d", mktime(0, 0, 0, $m, 1, $Y));

        $m = date("m", strtotime($edate));
        $d = date("d", strtotime($edate));
        $Y = date("Y", strtotime($edate));
        $edate = date("Y-m-t", mktime(0, 0, 0, $m, $d, $Y));
            
        $filtered = array();
        foreach($monthly_values AS $k => $v)
        {
            if($k >= $sdate && $k <= $edate)
            {
                $filtered[$k] = $v;
            }
        }
        
        $pairings = array();
        $count = 0;
        $token = 0;
        foreach($filtered AS $k => $v)
        {
            $pairings[$token][$k] = $v;
            if($count >= 1)
            {
                $count = 0;
                $token++;
            }
            else
                $count++;
        }
        
        $decimal_values = array();
        foreach($pairings AS $a => $b)
        {
//            echo "PAIR {$a} ---- <br />";
            $value = array();
            foreach($b AS $k => $v)
            {
//                echo "DATE: {$k}, VALUE: {$v}<br />";
                $value[] = $v;
            }
            if($value[0] == 0)
                $value[0] = $value[1];//prevent divide by 0
            $val = $value[1]/$value[0];
            $tot = $val - 1;
//            echo "VALUE {$val}, ";
//            echo "{$val} - 1 = {$tot}, TOTAL: {$tot}<br />";
            $decimal_values[] = $tot;//$value[1]/$value[0]-1;
        }
        
        $twr = 1;
        foreach($decimal_values AS $k => $v)
        {
            $twr = $twr*(1+$v);
//            echo "DECIMAL VALUES IN ORDER: {$v}, TWR IS: {$twr}<br />";
        }
        
        $twr -= 1;
        $twr *= 100;
//        echo "<strong>TWR: {$twr}</strong><br />";
//        $twr = ($twr-1)*100;
        return $twr;
//            echo "TWR: {$twr}<br />";
    }
    
    public function SetupIntervalValues($accounts, $sdate, $edate, $intervaltype)
    {
        $sdate = str_replace("00:00:00", "", $sdate);
        $sdate = str_replace(" ", "", $sdate);
        $edate = str_replace("00:00:00", "", $edate);
        $edate = str_replace(" ", "", $edate);
        
        $tmp = $this->GetAccountSymbolValues($accounts, null, $edate);
        $values = $this->CalculateTotalValues($tmp);
        
        $begin = $this->GetAccountSymbolValues($accounts, null, $sdate);
        $begin = $this->CalculateTotalValues($begin);

        $interval['begin']['date'] = $sdate;
        $interval['begin']['value'] = $begin['grand_totals']['grand_totals']['value'];
/*        foreach($begin['subtotals'] AS $k => $v)
        {
            echo "K: {$k}<br /><br />";
            foreach($v AS $a => $b)
            {
                echo "A: {$a}, B: {$b}<br />";
            }
        }*/
//        echo "SDATE: {$sdate}, STARTING WITH: {$interval['begin']['value']}<br />";
        if($intervaltype == "inception")
            $interval['begin']['value'] = 0;

        $interval['end']['date'] = $edate;
        $interval['end']['value'] = $values['grand_totals']['grand_totals']['value'];

//        echo "I AM HERE!, BEGIN DATE: {$interval['begin']['date']} -- TOTAL: {$interval['begin']['value']}<br />";
//        echo "I AM HERE!, END DATE: {$interval['end']['date']} -- TOTAL: {$interval['end']['value']}<br />";
        $totals = array();
        
        foreach($this->withdrawals AS $a => $b)
        {
            if($a)
            foreach($this->withdrawals[$a] AS $k => $v)
            {
                if($v['trade_date'] >= $sdate && $v['trade_date'] <= $edate)
                {
                    $totals["withdrawals"][] = $v;
                    $amount = $v['calculated_amount'];
                    $totals["withdrawals"]["total"] = $totals["withdrawals"]['total'] + $amount;
                }
            }
        }

        foreach($this->contributions AS $a => $b)
        {
            if($a)
            foreach($this->contributions[$a] AS $k => $v)
            {
                if($v['trade_date'] >= $sdate && $v['trade_date'] <= $edate)
                {
                    $totals["contributions"][] = $v;
    //                echo "AMOUNT: {$v['calculated_amount']}, TYPE: {$v['activity_id']}<br />";
                    $amount = $v['calculated_amount'];
                    $totals["contributions"]["total"] = $totals["contributions"]['total'] + $amount;
                }
            }
        }
        
        foreach($this->dividends AS $k => $v)
            if($v['trade_date'] >= $sdate && $v['trade_date'] <= $edate)
            {
                $amount = $v['calculated_amount'];//$v['cost_basis_adjustment'];
                $totals['dividends'][] = $v;
                $totals["dividends"]["total"] += $amount;
            }
        
        foreach($this->interest AS $a => $b)
            foreach($this->interest[$a] AS $k => $v)
            {
                if($v['trade_date'] >= $sdate && $v['trade_date'] <= $edate)
                {
                    $amount = $v['calculated_amount'];
                    $totals["interest"]["total"] += $amount;
                }
            }
            
        foreach($this->management AS $k => $v)
        {
            if($v['trade_date'] >= $sdate && $v['trade_date'] <= $edate)
            {
                $amount = $v['calculated_amount'];//$v['cost_basis_adjustment'];
                $totals['management'][] = $v;
                $totals['management']['total'] += $amount;
            }
        }

        foreach($this->expenses AS $k => $v)
        {
            if($v['trade_date'] >= $sdate && $v['trade_date'] <= $edate)
            {
                $amount = $v['calculated_amount'];//$v['cost_basis_adjustment'];
                $totals['expenses'][] = $v;
                $totals['expenses']['total'] += $amount;
            }
        }
        
        $totals['net_contributions'] = $totals['contributions']['total'] + $totals['withdrawals']['total'];
////            $periods[$p]['return']       = $periods[$p]['end_value'] - $periods[$p]['start_value'] - $periods[$p]['net_contrib'];
        
        $net_total = $interval['begin']['value'] + $totals['net_contributions'];
//        if($datevalues['end']['value'] > $net_total)
//            $totals['return'] = $datevalues['end']['value'] - $net_total;
//        else
        
            
        $totals['div_interest'] = $totals['dividends']['total'] + $totals['interest']['total'];
////        $totals['value_change'] = $totals['return'] + $totals['management']['total'] + $totals['expenses']['total'] - $totals['div_interest'];
        $totals['value_change'] = $totals['management']['total'] + $totals['expenses']['total'];
        $totals['return'] = -1*($net_total + $totals['value_change'] - $interval['end']['value']);
        
        $totals['capital_appreciation'] = $totals['return'] + ($totals['div_interest'] + $totals['value_change']);
        $totals['income_expenses'] = $totals['div_interest'] + $totals['value_change'];
        $totals['income'] = $totals['div_interest'];
        $totals['management_fees'] = $totals['management']['total'];
        $totals['other_expenses'] = $totals['expenses']['total'];
        
        switch($intervaltype)
        {
            case "inception":
                $this->inception = $interval;
                $this->inception['return'] = $totals['return'];
                $this->inception['div_interest'] = $totals['div_interest'];
                $this->inception['value_change'] = $totals['value_change'];
                $this->inception['capital_appreciation'] = $totals['capital_appreciation'];
                $this->inception['income_expenses'] = $totals['income_expenses'];
                $this->inception['income'] = $totals['income'];
                $this->inception['management_fees'] = $totals['management_fees'];
                $this->inception['other_expenses'] = $totals['other_expenses'];
                $this->inception['net_contributions'] = $totals['net_contributions'];
                $this->inception['withdrawals']['total'] = $totals["withdrawals"]["total"];
                $this->inception['contributions']['total'] = $totals["contributions"]["total"];
                break;
            case "lyr":
                $this->lyr = $interval;
                $this->lyr['content'] = $totals;
                $this->lyr['content']['contributions']['total'] = $totals['contributions']['total'];
                $this->lyr['content']['withdrawals']['total'] = $totals['withdrawals']['total'];
                $this->lyr['value_change'] = $totals['value_change'];
                break;
            case "qtr":
                $this->qtr = $interval;
                $this->qtr['content'] = $totals;
                $this->qtr['content']['contributions']['total'] = $totals['contributions']['total'];
                $this->qtr['content']['withdrawals']['total'] = $totals['withdrawals']['total'];
                $this->qtr['value_change'] = $totals['value_change'];
                break;
            case "ytd":
                $this->ytd = $interval;
                $this->ytd['content'] = $totals;
                $this->ytd['content']['contributions']['total'] = $totals['contributions']['total'];
                $this->ytd['content']['withdrawals']['total'] = $totals['withdrawals']['total'];
                $this->ytd['value_change'] = $totals['value_change'];
                break;
        }
    }
        
    private function SetupIntervals($portfolio_ids)
    {
        global $adb;
        $query = "SELECT interval_begin_value, interval_begin_date, to_days(interval_begin_date) as begin_date_days 
                                       FROM vtiger_pc_portfolio_intervals where portfolio_id IN ({$portfolio_ids}) 
                                       ORDER BY interval_begin_date ASC";
//        echo "QUERY: {$query}<br />{$portfolio_ids}<br />";
        $result = $adb->pquery($query, array());
        $inception["begin"]['date'] = $adb->query_result($result, 0, "interval_begin_date");
        
        $query = "SELECT interval_end_value, interval_end_date, to_days(interval_end_date) as end_date_days 
                                       FROM vtiger_pc_portfolio_intervals where portfolio_id IN ({$portfolio_ids}) 
                                       ORDER BY interval_end_date DESC";
        $result = $adb->pquery($query, array());
        $inception['end']['date'] = $adb->query_result($result, 0, "interval_end_date");
//        $inception['end']['date'] = '2012-06-30';
        
        $this->inception = $inception;
        $m = date('m');
        $d = date('d');
        $Y = date('Y');

/*        $lastMonthEnd   = date('Y-m-d 00:00:00',mktime(0,0,0,$m,0,$Y));
        $lastMonthStart = date('Y-m-d 00:00:00',mktime(0,0,0,$m-1,0,$Y));
        echo "LAST MONTH START: {$lastMonthStart}<br />";
        echo "LAST MONTH END: {$lastMonthEnd}<br />";*/
        //343936.19   
        
        $qtrStart = date('Y-m-d 00:00:00',mktime(0,0,0,$m-3,$d,$Y));
        $qtrEnd = date('Y-m-d 00:00:00',mktime(0,0,0,$m,$d,$Y));
        $this->qtr['begin']['date'] = $qtrStart;
        $this->qtr['end']['date'] = $qtrEnd;
        $query = "SELECT SUM(interval_begin_value) AS begin_value
                  FROM vtiger_pc_portfolio_intervals 
                  WHERE portfolio_id IN ({$portfolio_ids}) 
                  AND interval_begin_date IN ('{$qtrStart}', '{$qtrEnd}')
                  GROUP BY interval_begin_date
                  ORDER BY interval_begin_date ASC";
                 
$query = "SELECT 
          (
           SELECT SUM(interval_begin_value) 
           FROM vtiger_pc_portfolio_intervals 
           WHERE portfolio_id IN ($portfolio_ids) AND interval_begin_date IN ('$qtrStart') GROUP BY interval_begin_date ORDER BY interval_begin_date ASC) AS begin_value,
           (SELECT SUM(interval_end_value) 
           FROM vtiger_pc_portfolio_intervals 
           WHERE portfolio_id IN ($portfolio_ids) AND interval_end_date IN ('$qtrEnd') GROUP BY interval_end_date ORDER BY interval_end_date DESC) AS end_value";
////        echo "QTR QUERY: {$query}<br />{$portfolio_ids}<br />";
        $result = $adb->pquery($query, array());
        $this->qtr['begin']['value'] = $adb->query_result($result, 0, "begin_value");
        $this->qtr['end']['value'] = $adb->query_result($result, 0, "end_value");
/*        echo "QTR START: {$qtrStart}, VALUE: {$this->qtr['begin']['value']}<br />";
        echo "QTR END: {$qtrEnd}, VALUE: {$this->qtr['end']['value']}<br />";*/
        
        $ytdStart = date('Y-m-d 00:00:00',mktime(0,0,0,$m,$d,$Y-1));
        $ytdEnd = date('Y-m-d 00:00:00',mktime(0,0,0,$m,$d,$Y));
        $this->ytd['begin']['date'] = $ytdStart;
        $this->ytd['end']['date'] = $ytdEnd;
$query = "SELECT 
          (
           SELECT SUM(interval_begin_value) 
           FROM vtiger_pc_portfolio_intervals 
           WHERE portfolio_id IN ($portfolio_ids) AND interval_begin_date IN ('$ytdStart') GROUP BY interval_begin_date ORDER BY interval_begin_date ASC) AS begin_value,
           (SELECT SUM(interval_end_value) 
           FROM vtiger_pc_portfolio_intervals 
           WHERE portfolio_id IN ($portfolio_ids) AND interval_end_date IN ('$ytdEnd') GROUP BY interval_end_date ORDER BY interval_end_date DESC) AS end_value";
////                 echo "QUERY: {$query}<br />{$portfolio_ids}<br />";
        $result = $adb->pquery($query, array());
        $this->ytd['begin']['value'] = $adb->query_result($result, 0, "begin_value");
        $this->ytd['end']['value'] = $adb->query_result($result, 0, "end_value");
        
/*        echo "YTD START: {$ytdStart}, VALUE: {$this->ytd['begin']['value']}<br />";
        echo "YTD END: {$ytdEnd}, VALUE: {$this->ytd['end']['value']}<br />";*/
        
        $lyStart = date('Y-m-d 00:00:00',mktime(0,0,0,13,0,$Y-2));
        $lyEnd = date('Y-m-d 00:00:00',mktime(0,0,0,13,0,$Y-1));

        if($inception['begin']['date'] > $lyStart)
            $lyStart = $inception['begin']['date'];
        $this->lyr['begin']['date'] = $lyStart;
        $this->lyr['end']['date'] = $lyEnd;
$query = "SELECT 
          (
           SELECT SUM(interval_begin_value) 
           FROM vtiger_pc_portfolio_intervals 
           WHERE portfolio_id IN ($portfolio_ids) AND interval_begin_date IN ('$lyStart') GROUP BY interval_begin_date ORDER BY interval_begin_date ASC) AS begin_value,
           (SELECT SUM(interval_end_value) 
           FROM vtiger_pc_portfolio_intervals 
           WHERE portfolio_id IN ($portfolio_ids) AND interval_end_date IN ('$lyEnd') GROUP BY interval_end_date ORDER BY interval_end_date DESC) AS end_value";
////                  echo "QUERY 2: <strong>{$query}</strong><br />{$portfolio_ids}<br />";
        $result = $adb->pquery($query, array());
        $this->lyr['begin']['value'] = $adb->query_result($result, 0, "begin_value");
        $this->lyr['end']['value'] = 3344;//$adb->query_result($result, 0, "end_value");
        
/*        echo "LY START: {$lyStart}, VALUE: {$this->lyr['begin']['value']}<br />";
        echo "LY END: {$lyEnd}, VALUE: {$this->lyr['end']['value']}<br />";*/
        
    }
    
        public function CalculateWithDate($start, $end, $datevalues)
    {
////        echo "START DATE: {$start}, END DATE: {$end}<br />";
        $totals = array();
        foreach($this->withdrawals AS $a => $b)
        {
            if($a)
            foreach($this->withdrawals[$a] AS $k => $v)
            {
                if($v['trade_date'] >= $start && $v['trade_date'] <= $end)
                {
                    $totals["withdrawals"][] = $v;
                    //echo "AMOUNT: {$v['calculated_amount']}<br />";
//                    if($v['calculated_amount'] > 0)
  //                      $amount = $v['calculated_amount']*-1;
    //                else
                        $amount = $v['calculated_amount'];
                    $totals["withdrawals"]["total"] = $totals["withdrawals"]['total'] + $amount;
                }
            }
        }

        foreach($this->contributions AS $a => $b)
        {
            if($a)
            foreach($this->contributions[$a] AS $k => $v)
            {
                if($v['trade_date'] >= $start && $v['trade_date'] <= $end)
                {
                    $totals["contributions"][] = $v;
    //                echo "AMOUNT: {$v['calculated_amount']}, TYPE: {$v['activity_id']}<br />";
                    $amount = $v['calculated_amount'];
                    $totals["contributions"]["total"] = $totals["contributions"]['total'] + $amount;
                }
            }
        }
        
        foreach($this->dividends AS $k => $v)
            if($v['trade_date'] >= $start && $v['trade_date'] <= $end)
            {
                $amount = $v['cost_basis_adjustment'];
                $totals['dividends'][] = $v;
                $totals["dividends"]["total"] += $amount;
            }
        
        foreach($this->interest AS $a => $b)
            foreach($this->interest[$a] AS $k => $v)
            {
                if($v['trade_date'] >= $start && $v['trade_date'] <= $end)
                {
                    $amount = $v['calculated_amount'];
                    $totals["interest"]["total"] += $amount;
                }
            }
            
        foreach($this->management AS $k => $v)
        {
            if($v['trade_date'] >= $start && $v['trade_date'] <= $end)
            {
                $amount = $v['cost_basis_adjustment'];
                $totals['management'][] = $v;
                $totals['management']['total'] += $amount;
            }
        }

        foreach($this->expenses AS $k => $v)
        {
            if($v['trade_date'] >= $start && $v['trade_date'] <= $end)
            {
                $amount = $v['cost_basis_adjustment'];
                $totals['expenses'][] = $v;
                $totals['expenses']['total'] += $amount;
            }
        }
        
        $totals['net_contributions'] = $totals['contributions']['total'] + $totals['withdrawals']['total'];
////            $periods[$p]['return']       = $periods[$p]['end_value'] - $periods[$p]['start_value'] - $periods[$p]['net_contrib'];
        
        $net_total = $datevalues['begin']['value'] + $totals['net_contributions'];
//        if($datevalues['end']['value'] > $net_total)
//            $totals['return'] = $datevalues['end']['value'] - $net_total;
//        else
        $totals['return'] = -1*($net_total - $datevalues['end']['value']);
            
        $totals['div_interest'] = $totals['dividends']['total'] + $totals['interest']['total'];
////        $totals['value_change'] = $totals['return'] + $totals['management']['total'] + $totals['expenses']['total'] - $totals['div_interest'];
        $totals['value_change'] = $totals['management']['total'] + $totals['expenses']['total'];

        $totals['capital_appreciation'] = $totals['return'] + ($totals['div_interest'] + $totals['value_change']);
        $totals['income_expenses'] = $totals['div_interest'] + $totals['value_change'];
        $totals['income'] = $totals['div_interest'];
        $totals['management_fees'] = $totals['management']['total'];
        $totals['other_expenses'] = $totals['expenses']['total'];
/*        echo "Withdrawals: {$totals["withdrawals"]['total']}<br />";
        echo "Contributions: {$totals["contributions"]['total']}<br />";
        echo "Dividends: {$totals["dividends"]['total']}<br />";
        echo "Interest: {$totals["interest"]['total']}<br />";
        echo "Management: {$totals["management"]['total']}<br />";
        echo "Expenses: {$totals["expenses"]['total']}<br /><br />";
        
        $totals['end_value'] = $totals["withdrawals"]['total'] 
                               + $totals['contributions']['total'] 
                               + $totals['dividends']['total'] 
                               + $totals['interest']['total']
                               + $totals['management']['total']
                               + $totals['expenses']['total'];*/
/*        echo "TOTAL WITHDRAWAL: " . $totals['withdrawals']['total'] . "<br />";
        echo "TOTAL CONTRIBUTIONS: " . $totals['contributions']['total'] . "<br />";
        echo "TOTAL DIVIDENDS: " . $totals['dividends']['total'] . "<br />";
        echo "TOTAL INTEREST: " . $totals['interest']['total'] . "<br />";
        echo "TOTAL MANAGEMENT FEES: " . $totals['management']['total'] . "<br />";
        echo "TOTAL EXPENSE FEES: " . $totals['expenses']['total'] . "<br />";
        echo "TOTAL NET CONTRIBUTIONS: " . $totals['net_contributions'] . "<br />";
        echo "CHANGE IN VALUE: " . $totals['value_change'] . "<br />";
        echo "RETURN VALUE: " . $totals['return'] . "<br />";*/
        
        return $totals;
    }
    
    private function CalculateInception($portfolio_ids)
    {
        global $adb;
        $inception = $this->inception;
/*        echo "INCEPTION BEGIN DATE: " . $inception['begin']['date'] . "<br />";
        echo "INCEPTION END DATE: " . $inception['end']['date'] . "<br />";*/
        
        $query = "SELECT SUM(interval_begin_value) AS begin_total, interval_begin_value, interval_begin_date, to_days(interval_begin_date) as begin_date_days 
                                       FROM vtiger_pc_portfolio_intervals 
                                       WHERE portfolio_id IN ({$portfolio_ids}) 
                                       AND interval_begin_date = '{$inception['begin']['date']}'
                                       GROUP BY interval_begin_date
                                       ORDER BY interval_begin_date ASC";
////                                       echo "<br /><br />" . $query . "<br /><br />";
        $result = $adb->pquery($query, array());
        
//        $inception["begin"]['date'] = $adb->query_result($result, 0, "interval_begin_date");
//        $inception["begin"]['value'] = $adb->query_result($result, 0, "begin_total");
        
        $query = "SELECT SUM(interval_end_value) AS end_total, min(interval_begin_date), interval_end_value, interval_end_date, to_days(interval_end_date) as end_date_days 
                                       FROM vtiger_pc_portfolio_intervals where portfolio_id IN ({$portfolio_ids}) 
                                       AND interval_end_date = '{$inception['end']['date']}'
                                       GROUP BY interval_end_date
                                       ORDER BY interval_end_date DESC";
////                                       echo "<br /><br />" . $query . "<br /><br />";
        $result = $adb->pquery($query, array());
        $inception['end']['date'] = $adb->query_result($result, 0, "interval_end_date");
        $inception['end']['value'] = $adb->query_result($result, 0, "end_total");
        
        foreach($this->withdrawals AS $a => $b)
        {
            if($a)
            foreach($this->withdrawals[$a] AS $k => $v)
            {
                if($v['trade_date'] <= $inception['end']['date'])
                {
                    $inception["withdrawals"][] = $v;
                    //echo "AMOUNT: {$v['calculated_amount']}<br />";
                    if($v['calculated_amount'] > 0)
                        $amount = $v['calculated_amount']*-1;
                    else
                        $amount = $v['calculated_amount'];
                    $inception["withdrawals"]["total"] = $inception["withdrawals"]['total'] + $amount;
                }
            }
        }

        foreach($this->contributions AS $a => $b)
        {
            if($a)
            foreach($this->contributions[$a] AS $k => $v)
            {
                if($v['trade_date'] <= $inception['end']['date'])
                {
                    $inception["contributions"][] = $v;
    //                echo "AMOUNT: {$v['calculated_amount']}, TYPE: {$v['activity_id']}<br />";
                    $amount = $v['calculated_amount'];
                    $inception["contributions"]["total"] = $inception["contributions"]['total'] + $amount;
                }
            }
        }
        
        foreach($this->dividends AS $k => $v)
        {
            if($v['trade_date'] <= $inception['end']['date'])
            {
                $inception['dividends'][] = $v;
                $amount = $v['cost_basis_adjustment'];
                $inception['dividends']['total'] = $inception['dividends']['total'] + $amount;
            }
        }

        foreach($this->interest AS $a => $b)
        {
            if($a)
            foreach($this->interest[$a] AS $k => $v)
            {
                if($v['trade_date'] <= $inception['end']['date'])
                {
                    $inception["interest"][] = $v;
    //                echo "AMOUNT: {$v['calculated_amount']}, TYPE: {$v['activity_id']}<br />";
                    $amount = $v['calculated_amount'];
                    $inception["interest"]["total"] = $inception["interest"]['total'] + $amount;
                }
            }
        }
        
        foreach($this->management AS $k => $v)
        {
            if($v['trade_date'] <= $inception['end']['date'])
            {
                $inception['management'][] = $v;
                $amount = $v['cost_basis_adjustment'];
                $inception['management']['total'] += $amount;
            }
        }
        
        foreach($this->expenses AS $k => $v)
        {
            if($v['trade_date'] <= $inception['end']['date'])
            {
                $inception['expenses'][] = $v;
                $amount = $v['cost_basis_adjustment'];
                $inception['expenses']['total'] += $amount;
            }
        }
        
        $inception['net_contributions'] = $inception['contributions']['total'] + $inception['withdrawals']['total'];
        

        $net_totals = $inception['begin']['value'] + $inception['net_contributions'];
//        if($inception['end']['value'] > ($inception['begin']['value'] + $inception['net_contributions']))
//            $inception['return'] = $inception['begin']['value'] + $inception['net_contributions'] - $inception['end']['value'];
//        else
//            $inception['return'] = $inception['end']['value'] - $inception['begin']['value'] + $inception['net_contributions'];
        $inception['return'] = -1*($net_totals - $inception['end']['value']);
        
//        $inception['return'] = $inception['begin']['value'] + $inception['net_contributions'] - $inception['end']['value'];
        $inception['div_interest'] = $inception['dividends']['total'] + $inception['interest']['total'];
//        $inception['value_change'] = $inception['return'] + $inception['management']['total'] + $inception['expenses']['total'] - $inception['div_interest'];
        $inception['value_change'] = $inception['management']['total'] + $inception['expenses']['total'];
        $inception['capital_appreciation'] = $inception['return'] + ($inception['div_interest'] + $inception['value_change']);
        $inception['income_expenses'] = $inception['div_interest'] + $inception['value_change'];
        $inception['income'] = $inception['div_interest'];
        $inception['management_fees'] = $inception['management']['total'];
        $inception['other_expenses'] = $inception['expenses']['total'];
        
///        echo "RETURN: {$inception['return']} - ({$inception['div_interest']} + {$inception['value_change']})<br />";
        
        $this->inception = $inception;
/*        echo "INCEPTION BEGIN DATE: " . $inception['begin']['date'] . ", VALUE: " . $inception['begin']['value'] . "<br />";
        echo "INCEPTION END DATE: " . $inception['end']['date'] . ", VALUE: " . $inception['end']['value'] . "<br />";
        echo "TOTAL WITHDRAWAL INCEPTION: " . $inception['withdrawals']['total'] . "<br />";
        echo "TOTAL CONTRIBUTIONS INCEPTION: " . $inception['contributions']['total'] . "<br />";
        echo "TOTAL DIVIDENDS INCEPTION: " . $inception['dividends']['total'] . "<br />";
        echo "TOTAL INTEREST INCEPTION: " . $inception['interest']['total'] . "<br />";
        echo "TOTAL MANAGEMENT FEES INCEPTION: " . $inception['management']['total'] . "<br />";
        echo "TOTAL EXPENSE FEES INCEPTION: " . $inception['expenses']['total'] . "<br />";
        echo "NET CONTRIBUTIONS: " . $inception['net_contributions'] . "<br />";
        echo "INCEPTION RETURN: " . $inception['return'] . "<br />";*/
    }
    
    private function SetupInfo($pids, $special_instructions = null)
    {
        $this->transactions = $this->GetAllPortfolioTransactions($pids, $special_instructions);
        $this->SetupTransactions($this->transactions);
        $this->SetupWithdrawalsAndContributions();
        $this->SetupIntervals($pids);
        $this->CalculateInception($pids);
        
////        echo "<strong>LAST QUARTER<br /></strong>";
        $this->qtr['content'] = $this->CalculateWithDate($this->qtr['begin']['date'], $this->qtr['end']['date'], $this->qtr);
////        echo "QTR BEGIN VALUE: " . $this->qtr['begin']['value'] . ", QTR END VALUE: " . $this->qtr['end']['value'] . "<br />";
////        echo "<strong>LAST YEAR<br /></strong>";
        $this->lyr['content'] = $this->CalculateWithDate($this->lyr['begin']['date'], $this->lyr['end']['date'], $this->lyr);
////        echo "LAST YEAR BEGIN VALUE: " . $this->lyr['begin']['value'] . ", LAST YEAR END VALUE: " . $this->lyr['end']['value'] . "<br />";
////        echo "<strong>YEAR TO DATE<br /></strong>";
        $this->ytd['content'] = $this->CalculateWithDate($this->ytd['begin']['date'], $this->ytd['end']['date'], $this->ytd);
////        echo "YEAR TO DATE: " . $this->ytd['begin']['value'] . ", YTD END VALUE: " . $this->ytd['end']['value'] . "<br />";
    }
    
    //Get the annual income rate for the given security ID
    public function GetAnnualIncomeRate($security_id)
    {
        global $adb;
        $query = "SELECT security_annual_income_rate
                  FROM vtiger_securities
                  WHERE security_id = ?";
        $result = $adb->pquery($query, array($security_id));
        return $adb->query_result($result, 0, "security_annual_income_rate");
    }

    //Get the annual income rate for the given security ID
    public function GetIncomeFrequencyID($security_id)
    {
        global $adb;
        $query = "SELECT security_income_frequency_id
                  FROM vtiger_securities
                  WHERE security_id = ?";
        $result = $adb->pquery($query, array($security_id));
        return $adb->query_result($result, 0, "security_income_frequency_id");
    }

    //Get all transaction info
    public function GetTransactions()
    {
        return $this->transactions;
    }

    /**Separate Transacations by account.  Date is entered to check against.  If a transaction is > than the entered date, it will not be copied
     */
    public function GetAccountTransactions($acct_number=null, $date=null)
    {
        $accounts = array();
        $transactions = $this->GetTransactions();

        foreach($transactions AS $k => $v)
        {
            if($acct_number)
            {
//                if($acct_number == $v['account_number'])
                    if($date)//A date was entered
                    {
                        if($v['trade_date'] <= $date)
                            $accounts[$v['account_number']][] = $v;
                    }
                    else
                        $accounts[$v['account_number']][] = $v;
            }
            else
            if($v['account_number'])
            {
                if($date)//A date was entered
                {
                    if($v['trade_date'] <= $date)
                        $accounts[$v['account_number']][] = $v;
                }
                else
                    $accounts[$v['account_number']][] = $v;
            }
        }
        return $accounts;
    }
    
    /*Get the categories (code descriptions) from an array containing ['code_description'] in it*/
    public function GetCategories($transactions)
    {
        $categories = array();
        if($transactions)
        foreach($transactions AS $k => $v)
            $categories[] = $v['code_description'];

        return $categories;
    }
    
    /*Get Account Symbol Values/Totals ... The returning result is an array in the form of
     *$totals[account_number][symbol][<quantity, account, cost_basis, etc...>]
     * @param startDate <optional> to specify a start date of the values we want returned
     * @param endDate <optional> to specify an end date of the values we want returned
     * If no start or end date entered, values are returned since inception
     **/
    public function GetAccountSymbolValues($accounts, $startDate=null, $endDate=null, $ignore_flow=false)
    {
        $totals = array();
        $dates = array();
        if(is_array($accounts))
        foreach($accounts AS $a => $b)
        {if(is_array($b))
            foreach($b AS $k => $v)
            {
                if($v['activity_id'] == 50)//transfer of securities
                    $dates[] = $v['trade_date'];
                
                //70 is Buy, 10 is Flow
//                if($v['activity_id'] == 70 && in_array($v['trade_date'], $dates) || ( ($v['activity_id'] == 10) && ($ignore_flow == true) ) )
//                {}
//                else
                {
                    $shouldCopy = 0;//By default, we do not copy
                    if(!$startDate && !$endDate)//Start date and end date were not entered, so we should copy all
                        $shouldCopy = 1;
                    else
                    if($startDate && !$endDate)//Start date was entered but end date was not
                    {
                        if($v['trade_date'] >= $startDate)
                            $shouldCopy = 1;
                    }
                    if(!$startDate && $endDate)//Start date was not entered, but end date was
                    {                        
                        if($v['trade_date'] <= $endDate)                      
                            $shouldCopy = 1;
                    }
                    else
                    if($startDate != '' && $endDate != '')//Start date and end date were entered
                    {
                        if($v['trade_date'] >= $startDate && $v['trade_date'] <= $endDate)
                            $shouldCopy = 1;
                    }
                    
//                if($v['cost_basis_adjustment'] < 0)
//                    continue;
//                echo "SYMBOL: {$v['symbol_id']}<br />";
//            if($v['symbol_id'] == 50895)
//                echo "Account: {$a}, SYMBOL: {$v['symbol_id']}<br />";
                    if($shouldCopy)
                    {
                        $totals[$a][$v['security_symbol']]['quantity'] += $v['quantity'];//Somewhat difficult to read... $a is the account number, $v is the transaction, so we want the symbol, then add to the quantity, value, etc
                        $totals[$a][$v['security_symbol']]['account'] = $v['account_number'];
                        $totals[$a][$v['security_symbol']]['cost_basis'] += $v['cost_basis_adjustment'];
                        $totals[$a][$v['security_symbol']]['price_adjustment'] = $v['price_adjustment'];
                        $totals[$a][$v['security_symbol']]['code_description'] = $v['code_description'];
                        $totals[$a][$v['security_symbol']]['security_description'] = $v['description'];
                        $totals[$a][$v['security_symbol']]['symbol'] = $v['security_symbol'];
                        $totals[$a][$v['security_symbol']]['security_id'] = $v['symbol_id'];
                        $totals[$a][$v['security_symbol']]['security_type_id'] = $v['security_type_id'];
                        $totals[$a][$v['security_symbol']]['code_name'] = $v['code_name'];
                        $totals[$a][$v['security_symbol']]['security_type_name'] = $v['security_type_name'];
                        $totals[$a][$v['security_symbol']]['trade_date'] = $v['trade_date'];
                        
                        if(!$totals[$a][$v['security_symbol']]['price'])
                        {
/*                            if($v['security_factor'] > 0)
                                $totals[$a][$v['security_symbol']]['price'] = $v['price_adjustment'] * $v['price'] * $v['security_factor'];
                            else
                                $totals[$a][$v['security_symbol']]['price'] = $v['price_adjustment'] * $v['price'];*/
//                            $totals[$a][$v['security_symbol']]['price'] = $this->GetSecurityPriceAsOfDate($v['symbol_id'], $endDate);
                            if($endDate)
                                $totals[$a][$v['security_symbol']]['price'] = $this->GetSecurityPriceAsOfDate($v['symbol_id'], $endDate);
                            else
                                $totals[$a][$v['security_symbol']]['price'] = $this->GetLatestSecurityPrice($v['symbol_id']);
//                            echo "date: {$endDate} -- SYMBOL: {$v['security_symbol']}, PRICE: " . $totals[$a][$v['security_symbol']]['price'] . "<br />";
/////                            $totals[$a][$v['security_symbol']]['price'] = $this->GetSecurityPriceAsOfDate($v['symbol_id'], $endDate);
                            if($v['security_symbol'] == 'CASH' || $v['security_type_id'] == 11)
                                $totals[$a][$v['security_symbol']]['price'] = 1;
                            if($v['code_description'] == "Options")//OPTIONS ARE MULTIPLIED BY 100
                                $totals[$a][$v['security_symbol']]['price'] *= 100;
        //                    if($v['security_symbol'] == '931142BY8')
        //                        echo "price: {$totals[$a][$v['security_symbol']]['quantity']}<br />";
                        }
                    }
                }
            }
        }
        
        foreach ($totals AS $account_num => $a) {
            foreach($a AS $k=>$v) {//Round the quantity to the nearest whole number
           		if($totals[$account_num][$k]['symbol'] != "CASH")
            		$totals[$account_num][$k]['quantity'] = $totals[$account_num][$k]['quantity'];//round($totals[$account_num][$k]['quantity']);
            }
        }  
        return $totals;
    }
    
    //Get portfolio transactions for the portfolio ID's.  $pids is comma separated
    private function GetAllPortfolioTransactions($pids, $special_instructions = null)
    {
        if($special_instructions)
        {
            $direction = $special_instructions['direction'];
            $order_by = $special_instructions['order_by'];
            $filter = $special_instructions['filter'];
        }
        else
        {
            $direction = "DESC";
            $order_by = "t.trade_date";
        }
        /*This is an extra check in case we pass in direction but not order_by, or vice versa.  The array will come in as positive but not fill in anything for one
        of these two values*/
        if(!$direction)
            $direction = "ASC";
        if(!$order_by)
            $order_by = "t.trade_date";
//        echo "ORDER BY: {$order_by}<br />";
//        echo "DIRECTION: {$direction}<br />";
        
        global $adb;
        $query = "SELECT pr.price*t.quantity AS value, t.*, pr.price, a.activity_name, p.portfolio_account_number AS AccountNumber, s.security_id, s.security_symbol, s.security_type_id, cde.code_name, st.security_type_name,
                              s.security_description, s.security_price_adjustment, s.security_factor, c.code_id, cd.code_description, o.interface_name, rat.report_as_type_name
                  FROM vtiger_pc_transactions t
                  LEFT JOIN vtiger_portfolios p ON p.portfolio_id = t.portfolio_id
                  LEFT JOIN vtiger_securities s ON s.security_id = t.symbol_id
                  LEFT JOIN vtiger_pc_codes c ON c.code_id = 
                     (SELECT code_id FROM vtiger_pc_security_codes WHERE security_id = s.security_id AND code_type_id = 20)
                  LEFT JOIN vtiger_pc_security_codes sc ON sc.security_id = s.security_id
                  LEFT JOIN vtiger_pc_codes cd ON cd.code_id = c.code_id
                  LEFT JOIN vtiger_pc_codes cde ON cde.code_id = sc.code_id
                  LEFT JOIN vtiger_pc_activities a ON a.activity_id = t.activity_id
                  LEFT JOIN vtiger_pc_interface_originations o ON t.origination_id = o.origination_id
                  LEFT JOIN vtiger_pc_report_as_types rat ON rat.report_as_type_id = t.report_as_type_id
                  LEFT JOIN vtiger_pc_security_prices pr ON (pr.price_date = t.trade_date AND pr.security_id = s.security_id)
                  LEFT JOIN vtiger_security_types st ON st.security_type_id = s.security_type_id
                  WHERE p.portfolio_id IN ({$pids}) 
                  AND status_type_id = 100
                  {$filter}
                  GROUP BY transaction_id
                  ORDER BY {$order_by} {$direction}";//Only care about "posted" transactions, so status_type_id = 100
                  
        $result = $adb->pquery($query, array());
        $transactions = array();
        $total = 0;
        $symbols = array();
        $descriptions = array();
        $actions = array();
        $security_types = array();
        
        if($result)
        foreach($result AS $k => $v)
        {
/*            $query = "SELECT price FROM vtiger_pc_security_prices
                      WHERE security_id = ?
                      AND price_date = ?";
            $r = $adb->pquery($query, array($v['security_id'], $v['trade_date']));
            foreach($r AS $a => $b)
                $price = $b['price'];*/
            $price = $v['price'];
            if($v['security_symbol'] == "CASH" || $v['security_type_id'] == 11)
                $price = 1;
/*            else
            if($v['security_factor'] > 0)
                $price = $v['security_price_adjustment'] * $v['price'] * $v['security_factor'];
            else
                $price = $v['security_price_adjustment'] * $v['price'];
//CASE WHEN s.security_factor > 0 THEN s.security_price_adjustment * p.price * s.security_factor
//                                                                 ELSE s.security_price_adjustment * p.price END AS total_price                */
            $value = $price * $v['quantity'];
            $v['trade_date'] = str_replace(" 00:00:00", '', $v['trade_date']);
            $v['trade_date_display'] = date('m-d-Y', strtotime($v['trade_date']));

            $transactions[] = array("id" => $v['transaction_id'],
                                    "pid" => $v['portfolio_id'],
                                    "activity_id" => $v['activity_id'],
                                    "quantity" => $v['quantity'],
                                    "total_value" => $v['total_value'],
                                    "net_amount" => $v['net_amount'],
                                    "trade_date" => $v['trade_date'],
                                    "trade_date_display" => $v['trade_date_display'],
                                    "principal" => $v['principal'],
                                    "add_sub_status_type_id" => $v['add_sub_status_type_id'],
                                    "cost_basis_adjustment" => $v['cost_basis_adjustment'],
                                    "report_as_type_id" => $v['report_as_type_id'],
                                    "calculated_amount" => $value,
                                    "money_id" => $v['money_id'],
                                    "accrued_interest" => $v['accrued_interest'],
                                    "symbol_id" => $v['symbol_id'],
                                    "security_symbol" => $v['security_symbol'],
                                    "description" => $v['security_description'],
                                    "price_adjustment" => $v['security_price_adjustment'],
                                    "account_number" => $v['AccountNumber'],
                                    "code_id" => $v['code_id'],
                                    "code_description" => $v['code_description'],
                                    "code_name" => $v['code_name'],
                                    "security_type_name" => $v['security_type_name'],
                                    "security_type_id" => $v['security_type_id'],
                                    "activity_name" => $v['activity_name'],
                                    "transaction_description" => $v['notes'],
                                    "current_price" => $price,
                                    "origination" => $v['interface_name'],
                                    "report_as_type_name" => $v['report_as_type_name'],
                                    "security_factor" => $v['security_factor'],
                                    "is_reinvested_flag" => $v['is_reinvested_flag'],
                                    "value" => $value);
//            echo "RAT: {$v['report_as_type_name']}<br />";//ACCT: {$v['AccountNumber']}, SYMBOL: {$v['security_symbol']}, CBA: {$v['cost_basis_adjustment']}<br />";
        }
        
        return $transactions;
    }
/*MAGIC QUERY FOR INTEREST
 * 
SELECT(
    SELECT SUM( net_amount )
    FROM vtiger_pc_transactions
    WHERE Portfolio_id
    IN ( 309, 444 ) 
    AND report_as_type_id
    IN ( 20 ) 
    AND money_id =1
    AND Trade_Date <  '2012-08-01') AS NA,

(
SELECT SUM(accrued_interest)
FROM vtiger_pc_transactions
WHERE portfolio_id IN (309, 444)
AND accrued_interest > 0 AND activity_id IN (140)
AND trade_date < '2012-08-01') AS AI
 * 
 */    
    public function SetupWithdrawalsAndContributions()
    {
        $withdrawals = $this->neg_transactions;
        $contributions = $this->pos_transactions;
        $transfers = array();
    //10: flow
    //50: Receipt of Securities
    //120: Transfer of Securities
    //160: Expenses
//            echo "A: {$a}<br />";
/*            foreach($b AS $k => $v)
            {
            }*/
/**OLD**
        foreach($withdrawals AS $a => $b)
        {
            switch($a)
            {
                case 10:
                {
                    foreach($contributions[$a] AS $k => $v)
                    {
                        foreach($b AS $c => $d)
                        {
//                        echo "CALCULATED AMOUNT: " . $b['calculated_amount'] . "<br />";
//                            echo $d['calculated_amount'];
                            if( (($d['calculated_amount']*-1) == $v['calculated_amount']) && ($d['trade_date'] == $v['trade_date']) )
                            {
                                $transfers['neg'][$a] = $d;
                                $transfers['pos'][$a] = $v;
                                $contributions[$a][$k] = null;
                                $withdrawals[$a][$c] = null;
                                echo "FOUND: " . $v['calculated_amount'] . "<br />";
                            }
                                //echo "FOUND: " . $v['calculated_amount'] . "<br />";
                        }
                    }
                } break;
            
            }
        }
*/       $count = 0;
        //Mapping maps positive against negative
        $mapping = array("10" => "10",
                         "50" => "120"
                        );
        foreach($mapping AS $p => $n)
        {
            if($contributions[$p])
            foreach($contributions[$p] AS $ck => $cv)
                if($withdrawals[$n])
                foreach($withdrawals[$n] AS $wk => $wv)
                {
                    if($p == '10')
                        $multiplyer = -1;
                    else
                        $multiplyer = 1;
    //                if( (($d['calculated_amount']) == $v['calculated_amount']*-1) && ($d['trade_date'] == $v['trade_date']) )
                    if( (($cv['calculated_amount']) == $wv['calculated_amount']*$multiplyer) && ($cv['trade_date'] == $wv['trade_date']) )
                    {
//                        echo $count;
                        $transfers['neg'][$n] = $wv;
                        $transfers['pos'][$p] = $cv;
                        $withdrawals[$n][$wk] = null;
                        $contributions[$p][$ck] = null;
                        $count++;
                    }
                }
                
//           echo "P: {$p}, N: {$n}<br />";
        }
/*            foreach($withdrawals AS $a => $b)
                foreach($withdrawals[$a] AS $k => $v)
                {
                    if($a == '10')
                        echo "FLOW: " . $v['calculated_amount'] . "<br />";
                    if($a == '120')
                        echo "xfer of securities: " . $v['calculated_amount'] . "<br />";
                }
        
/*        foreach($withdrawals AS $a => $b)
            foreach($withdrawals[$a] AS $k => $v)
            {
                switch($a)
                {
                    case 10: $pos_type = 10; $neg_type = 10;
                        break;
                    case 120: $pos_type = 50; $neg_type = 120;
                        break;
                }
                foreach($contributions[$pos_type] AS $c => $d)
                {
                    if( (($d['calculated_amount']) == $v['calculated_amount']*-1) && ($d['trade_date'] == $v['trade_date']) )
                    {
                        $transfers['neg'][$neg_type] = $d;
                        $transfers['pos'][$pos_type] = $v;
                        $contributions[$pos_type][$k] = null;
                        $withdrawals[$neg_type][$c] = null;
                        echo "FOUND: " . $v['calculated_amount'] . "<br />";
                    }
//                    if( (($d['calculated_amount']*-1) == $v['calculated_amount']) && ($d['trade_date'] == $v['trade_date']) )
                    
                }
                    
/*                foreach($b AS $c => $d)
                {
//                        echo "CALCULATED AMOUNT: " . $b['calculated_amount'] . "<br />";
//                            echo $d['calculated_amount'];
                    if( (($d['calculated_amount']*-1) == $v['calculated_amount']) && ($d['trade_date'] == $v['trade_date']) )
                    {
                        switch($a)
                        {
                            case 10: $pos_type = 10; $neg_type = 10;
                                break;
                            case 120: $pos_type = 50; $neg_type = 120;
                                break;
                        }
                        $transfers['neg'][$neg_type] = $d;
                        $transfers['pos'][$pos_type] = $v;
                        $contributions[$pos_type][$k] = null;
                        $withdrawals[$neg_type][$c] = null;
                        echo "FOUND: " . $v['calculated_amount'] . "<br />";
                    }
                        //echo "FOUND: " . $v['calculated_amount'] . "<br />";
                }*/
//            }
/*            
        foreach($withdrawals AS $a => $b)
            foreach($withdrawals[$a] AS $k => $v)
            {
                if($v['calculated_amount'] != 0)
                    echo "AMOUNT: {$a} -- " . $v['calculated_amount'] . "<br />";
            }*/
        $this->withdrawals = $withdrawals;
        $this->contributions = $contributions;
    }
    
    //Get the negative transactions
    public function GetNegativeTransactions()
    {
        return $this->neg_transactions;
    }
    
    //Get the positive transactions
    public function GetPositiveTransactions()
    {
        return $this->pos_transactions;
    }
    
    //Setup the transactions for withdrawals/contributions
    public function SetupTransactions($transactions)
    {
        /*Withdrawal is Cash Withdrawals (Flow) 
                        + Transfer of Security (market value) 
                        + (gross proceeds - brokerage fees - other fees + accrued interest)
                        + Expenses 
        */
        $withdrawals = array();
        $contributions = array();
        $dividends = array();
        $interest = array();
        $management = array();
        $expenses = array();
        $assets = array();
        
        foreach($transactions AS $k => $v)
        {
            switch($v['activity_id'])
            {
                case 10://Flow
                {
                    if($v['net_amount'] < 0)
                    {
                        $v['calculated_amount'] = $v['net_amount'];
                        $withdrawals[] = $v;
                    }
                    elseif($v['net_amount'] > 0)
                    {
                        $v['calculated_amount'] = $v['net_amount'];
                        $contributions[] = $v;
                    }
                } break;
            
                case 50://Receipt of Securities
                {
                    if($v['total_value'] != 0)
                    {
                        $v['calculated_amount'] = $v['total_value'];
                        $contributions[] = $v;
                    }
                } break;
                                
                case 120://Transfer of Securities
                {
                    if($v['total_value'] != 0)
                    {
                        $v['calculated_amount'] = $v['total_value'];
                        $withdrawals[] = $v;
                    }
                } break;

                case 160://Expenses
                {
                    if($v['net_amount'] < 0)
                    {
                        if($v['report_as_type_id'] == 80)
                        {
                            $v['calculated_amount'] = $v['net_amount'];
                            $withdrawals[] = $v;
                        }
                    }
                } break;
                
                case 140://Accrued Interest
                {
/*                    if($v['accrued_interest'] > 0)
                    {
                        $v['calculated_amount'] = $v['accrued_interest'];
                        $interest['accrued'][] = $v;
                    }*/
                } break;
            }
            
            switch($v['report_as_type_id'])
            {
                case 30://Dividend
//                case 280://Qualified Dividend
                {
                    $dividends[] = $v;
//                    print_r($v);
//                    echo "<br /><br />";
                } break;
                
                case 20://Interest
                {
                    if($v['money_id'] == 1)
                    {
                        $v['calculated_amount'] = $v['net_amount'];
                        $interest['regular'][] = $v;
                    }
                } break;
                
                case 60://Management Fee
                {
                    $management[] = $v;
                } break;
            
                case 70://Expenses
                case 130:
                {
                    $expenses[] = $v;
                } break;
            }
            
            if($v['symbol_id'])
                $assets[$v['symbol_id']][] = $v;//IE: Assets[1][] = $v (1 being CASH)
        }
        
        $neg_transactions = array();
        
        foreach($withdrawals AS $k => $v)
        {
            $id = $v['activity_id'];
            $neg_transactions[$id][] = $v;
        }

        $pos_transactions = array();
        
        foreach($contributions AS $k => $v)
        {
            $id = $v['activity_id'];
            $pos_transactions[$id][] = $v;
        }
        
/*        $types = array();
        foreach($neg_transactions AS $a => $b)
        {
            foreach($neg_transactions[$a] AS $k => $v)
            {
                $id = $a;
                if(!in_array($id,$types))
                {
                    if($id == 10)
                        echo "<span style='color:red;'>Negative Cash Flow</span><br />";
                    if($id == 120)
                        echo "<span style='color:red;'>Transfer of Securities</span><br />";
                    if($id == 160)
                        echo "<span style='color:red;'>Expenses</span><br />";

                    $types[] = $id;
                }
                echo $v['calculated_amount'] . "<br />";
            }
        }
        echo "</div>";
        
        
        echo "<div style='float:left; display:bloack; width:50%;'>";
        
        echo "<br /><strong>CONTRIBUTIONS</strong><br />";
        
        $types = array();
        foreach($pos_transactions AS $a => $b)
        {
            foreach($pos_transactions[$a] AS $k => $v)
            {
                $id = $a;
                if(!in_array($id,$types))
                {
                    if($id == 10)
                        echo "<span style='color:red;'>Positive Cash Flow</span><br />";
                    if($id == 50)
                        echo "<span style='color:red;'>Receipt of Securities</span><br />";

                    $types[] = $id;
                }
                echo $v['calculated_amount'] . "<br />";
            }
        }
        echo "</div>";
        */
        $this->neg_transactions = $neg_transactions;
        $this->pos_transactions = $pos_transactions;
        $this->dividends = $dividends;
        $this->interest = $interest;
        $this->management = $management;
        $this->expenses = $expenses;
        $this->assets = $assets;
        
/*        foreach($assets AS $a => $b)
        {
//            echo "ASSET ID: {$a}<br />";
            foreach($b AS $k => $v)
            {
//                echo "TRADE DATE: {$v['trade_date']}, quantity: {$v['quantity']}<br />";
            }
        }*/
    }
    
    //return all assets
    public function GetAllAssets()
    {
        return $this->assets;
    }
    
    /**
     * Get the security price for a security based on the date
     * @param type $symbol_id
     * @param type $date 
     */
    public function GetSecurityPriceAsOfDate($symbol_id, $date)
    {
        global $adb;
        $query = "SELECT p.price, s.security_price_adjustment, CASE WHEN s.security_factor > 0 THEN s.security_price_adjustment * p.price * s.security_factor
                                                                 ELSE s.security_price_adjustment * p.price END AS total_price
                  FROM vtiger_pc_security_prices p
                  LEFT JOIN vtiger_securities s ON s.security_id = p.security_id
                  WHERE p.security_id = '{$symbol_id}'
                  AND (price_date <= '{$date}')
                  GROUP BY price_date DESC
                  LIMIT 1";
        $result = $adb->pquery($query, array());
        
        return $adb->query_result($result, 0, "total_price");
    }
    
    //Get the latest price for the given security
    public function GetLatestSecurityPrice($symbol_id)
    {
        global $adb;
        $query = "SELECT p.price, s.security_price_adjustment, CASE WHEN s.security_factor > 0 THEN s.security_price_adjustment * p.price * s.security_factor
                                                                 ELSE s.security_price_adjustment * p.price END AS total_price
                  FROM vtiger_pc_security_prices p
                  LEFT JOIN vtiger_securities s ON s.security_id = p.security_id
                  WHERE p.security_id = '{$symbol_id}'
                  GROUP BY price_date DESC
                  LIMIT 1";
        $result = $adb->pquery($query, array());
        
        return $adb->query_result($result, 0, "total_price");
    }

    //Get the latest price date for the given security
    public function GetLatestSecurityPriceDate($security_id)
    {
        global $adb;
        $query = "SELECT * FROM vtiger_pc_security_prices
                  WHERE security_id = '{$security_id}'
                  GROUP BY price_date DESC
                  LIMIT 1";
        $result = $adb->pquery($query, array());
        
        return $adb->query_result($result, 0, "price_date");
    }
    
    //Get all asset information starting at a given start date and ending on the given end date
    public function CalculateAssetsFromDate($assets, $date)
    {
        global $adb;
        $add = 1;
        $asset_info = array();
        foreach($assets AS $a => $b)
            foreach($b AS $k => $v)
            {
//                if(!$asset_info[$a]['trade_dates'])
//                    $asset_info[$a]['trade_dates'] = array();
                if($v['trade_date'] <= $date && $v['activity_id'] != 20)
                {
//                    if($v['symbol_id'] == 1 || ($v['activity_id'] == 60 && !in_array($v['trade_date'], $asset_info[$a]['trade_dates'])))
                    {
                        $asset_info[$a][] = $v;
                        $asset_info[$a]['total_quantity'] += $v['quantity'];
//                        echo "A: {$a}, TRADE DATE: {$v['trade_date']}, quantity: {$v['quantity']}, TQ: {$asset_info[$a]['total_quantity']}<br />";
//                        $asset_info[$a]['trade_dates'][] = $v['trade_date'];
                    }
                }
            }
            
        foreach($asset_info AS $k => $v)
        {
            $query = "SELECT s.security_symbol, s.security_type_id, s.security_price_adjustment, s.security_description, c.code_description, p.price
                      FROM vtiger_securities s
                      LEFT JOIN vtiger_pc_codes c ON c.code_id = 
                                                     (SELECT code_id FROM vtiger_pc_security_codes WHERE security_id = {$k} AND code_type_id = 20)
                      LEFT JOIN vtiger_pc_security_prices p ON (p.security_id = {$k} AND p.price_date = '{$date}')
                      WHERE s.security_id = {$k}";//Get the code info
                      
            $result = $adb->pquery($query, array());
            foreach($result AS $a => $b)
            {
                $asset_info[$k]['symbol'] = $b['security_symbol'];
                $asset_info[$k]['security_description'] = $b['security_description'];
                $asset_info[$k]['code_description'] = $b['code_description'];
                if($asset_info[$k]['symbol'] == "CASH" || $asset_info[$k]['security_type_id'] == 11)
                            $b['price'] = 1;
                
                $asset_info[$k]['price'] = $b['price'];// * $b['security_price_adjustment'];
//                $asset_info[$k]['total_quantity'] = round($asset_info[$k]['total_quantity']);
                $asset_info[$k]['total_value'] = $asset_info[$k]['total_quantity'] * $b['price'];// * $b['security_price_adjustment'];
            }
        }
        
        return $asset_info;
    }
    
    //Returns the assets that are used in the asset allocation pie graph
    public function GetAssetAllocationInfo($assets, $date)
    {
        $date = "2012-09-30";
        $tmp = $this->CalculateAssetsFromDate($assets, $date);
        $asset_info = array();
        foreach($tmp AS $k => $v)
        {
            if($tmp[$k]['total_value'] > 0)
            {
                $asset_info[$v['code_description']] += $tmp[$k]['total_value'];//Add the total value for each code description
//                echo $asset_info[$v['code_description']] . "<br />";
            }
        }
        
        return $asset_info;//Return the sorted asset information
    }
    
    public function GetValueHistory($portfolioIDs) {
        global $adb;
        $query = "
            select
                date_format(interval_end_date,'%Y-%m') as date_key, 
                date_format(interval_end_date,'%b %Y') as date_name,
                sum(interval_end_value) as value
            from 
                vtiger_pc_portfolio_intervals 
            where 
                portfolio_id in ({$portfolioIDs})
                and interval_end_date >= now() - interval 13 month
                AND MONTH(interval_begin_date) != MONTH(interval_end_date)
                AND DAY(interval_end_date) >= 28
            group by 1 
            order by interval_end_date desc 
            limit 12";//This was right above "GROUP BY 1" ----    and to_days(interval_end_date) - to_days(interval_begin_date) >= 28    -----

        $result = $adb->pquery($query, null);

        $valueData = array();
        foreach ($result as $k => $v) {
            $valueData[$v['date_key']] = $v;
        }
    
        ksort($valueData);

        return $valueData;
    }    
    
    public function getPeriodIRR($portfolios,$startDate,$endDate,$startVal,$endVal) {
        global $adb;
        $result = $adb->pquery("SELECT to_days(?) - to_days(?)", array($endDate, $startDate));

        foreach($result AS $k => $v)
            $intervalDays = $v[0];
        
        //IRR
        $ivals = array();
        $query = "
            select
                if(trade_date > '{$startDate}',(to_days(trade_date) - to_days('{$startDate}') -1)/{$intervalDays},0) as days, 
                sum(if (t.symbol_id = 1 OR t.symbol_id is NULL,net_amount,if(t.activity_id in (70,90,110,120,130,140,160),-1,1)*if(total_value = 0,net_amount,total_value))) as VAL
            FROM vtiger_pc_transactions t 
                LEFT JOIN vtiger_pc_transactions_deleted d ON d.transaction_id = t.transaction_id
                LEFT JOIN vtiger_pc_activities a on a.activity_id = t.activity_id 
                LEFT JOIN vtiger_pc_report_as_types r on r.report_as_type_id = t.report_as_type_id 
                LEFT JOIN vtiger_securities s on s.security_id = t.symbol_id
            WHERE 
                t.trade_date between '{$startDate}' and '{$endDate}'
                AND t.portfolio_id in ({$portfolios})
                AND t.status_type_id = 100
                AND d.deleted is null
                AND (
                    (t.activity_id in (10,50,120) AND t.report_as_type_id is NULL)
                    OR (t.activity_id = 160 AND t.report_as_type_id = 80)
                )
                group by 1
            having VAL <> 0
        ";
/*
        $query = "SELECT t.*, pr.price, a.activity_name, p.portfolio_account_number AS AccountNumber, s.security_id, s.security_symbol, s.security_type_id,
                              s.security_description, s.security_price_adjustment, s.security_factor, c.code_id, cd.code_description, o.interface_name, rat.report_as_type_name
                  FROM vtiger_pc_transactions t
                  LEFT JOIN vtiger_portfolios p ON p.portfolio_id = t.portfolio_id
                  LEFT JOIN vtiger_securities s ON s.security_id = t.symbol_id
                  LEFT JOIN vtiger_pc_codes c ON c.code_id = 
                     (SELECT code_id FROM vtiger_pc_security_codes WHERE security_id = s.security_id AND code_type_id = 20)
                  LEFT JOIN vtiger_pc_codes cd ON cd.code_id = c.code_id
                  LEFT JOIN vtiger_pc_activities a ON a.activity_id = t.activity_id
                  LEFT JOIN vtiger_pc_interface_originations o ON t.origination_id = o.origination_id
                  LEFT JOIN vtiger_pc_report_as_types rat ON rat.report_as_type_id = t.report_as_type_id
                  LEFT JOIN vtiger_pc_security_prices pr ON (pr.price_date = t.trade_date AND pr.security_id = s.security_id)
                  WHERE p.portfolio_id IN ({$pids}) 
                  AND status_type_id = 100
                  {$filter}
                  ORDER BY {$order_by} {$direction}";//Only care about "posted" transactions, so status_type_id = 100

 */                
                
//                echo $query . "<br />";
        $ivals = $adb->pquery($query,array());
        
        $counter = 0;
        if($adb->num_rows($ivals) > 0)
        foreach($ivals AS $k => $v)
        {
            $sivals[] = $v;//array(0 => $v[0], 1 => $v[1]);
        }

        $sivals[] = array(0, $startVal);
        $sivals[] = array(1, $endVal * -1);

        $guess = $this->getIRR($sivals);
        
        if ($intervalDays >= 365)
            $irr = pow((1+$guess),(365/$intervalDays)) - 1;
        else
            $irr = $guess;
        return $irr;
    }
    
    public function getReferenceReturn($symbol,$startDate,$endDate,$feePct = 0) {
        global $adb;

        $start = $adb->pquery("SELECT to_days(price_date), price_date, price from 
                               vtiger_securities join 
                               vtiger_pc_security_prices using (security_id) where price_date = ? 
                               AND security_symbol = ? 
                               order by price_date asc limit 1",array($startDate,$symbol));
        
        if($adb->num_rows($start) <= 0)
            return 0;
        
        foreach($start AS $k => $v)
            $start = $v;

/*        $query = "SELECT to_days(pr.price_date), pr.price_date, pr.price FROM vtiger_securities 
                  join vtiger_pc_security_prices pr USING (security_id) 
                  where price_date <= ? AND security_id = (SELECT security_id FROM vtiger_securities WHERE security_symbol = ? LIMIT 1)
                  order by pr.price_date desc limit 1";
//        $query = "SELECT to_days(price_date), price_date, price from vtiger_securities join vtiger_pc_security_prices using (security_id) where price_date = ? AND security_symbol = ? order by price_date desc limit 1";
*/      $query = "SELECT to_days(price_date), price_date, price FROM vtiger_securities 
                  join vtiger_pc_security_prices USING (security_id) 
                  WHERE price_date BETWEEN DATE_SUB(?, INTERVAL 90 DAY) AND (?)
                  AND security_symbol = ?
                  order by price_date desc limit 1";
        $end = $adb->pquery($query,array($endDate,$endDate,$symbol));
//        echo $query . "<br />";
//        echo "END DATE: {$endDate}<br />";
//        echo "SYMBOL: {$symbol}<br />";
        
        if($adb->num_rows($end) <= 0)
            return 0;
        
        foreach($end AS $k => $v)
            $end = $v;
        
        $intervalDays = $end[0] - $start[0];

        $guess = $end[2] / $start[2] - 1;

        if ($intervalDays >= 365)
            $irr = pow((1+$guess),(365/$intervalDays)) - 1;
        else
            $irr = $guess;

        return $irr;

    }

    public function getIRR($ivals) {
        $guess = 0.5;
        if ($guess <= -1)
            $guess = -0.999999999;

        $cnt = 0;
        do {
            $sum = 0;
            $sumDeriv = 0;
            foreach ($ivals as $i) {
                $pow = pow(1 + $guess,$i[0]);
                //debug("pow(1+$guess,$i[0]) = $pow<br/>");
                $sum += $i[1] / $pow;
                $sumDeriv += ($i[1]*$i[0]) / $pow;
            }
            if($sumDeriv != 0)
                $guess = $guess - $sum / (-1*$sumDeriv);    
            $cnt++;
            //debug("SUM: $sum SUMDERIV: $sumDeriv GUESS: $guess CNT: $cnt<BR/>\n");

        } while (abs($sum) > 0.00001 && $cnt < 200 && $guess > -1);

        return $guess;
    }    
}

?>
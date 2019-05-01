<?php
include_once("include/utils/cron/cPortfolioCenter.php");

class cTransactionsAccess {
    private $pc;
    private $modified_date;
    private $reset;
    private $max_rows;
    private $datasets;
    private $debug;

    public function __construct($debug=false) {
        $this->debug = $debug;
        $this->pc = new cPortfolioCenter();
        $this->reset = 5000;
        $this->datasets = $this->pc->GetDatasets();//"1, 28";
        $this->max_rows = 20000;
        global $adb;

        if($portfolio_id)
            $condition = " WHERE portfolio_id = {$portfolio_id} ";

        $query = "SELECT last_modified_date FROM vtiger_pc_transactions {$condition} GROUP BY last_modified_date DESC LIMIT 1";

        $result = $adb->pquery($query, array());
        $this->modified_date = $adb->query_result($result, 0, "last_modified_date");
    }

    /**
     * Find the last modified date from transactions to get our last transaction date
     * @global type $adb
     * @param type $portfolio_id
     */
    public function GetLastModifiedDate($portfolio_id=null){
        return $this->modified_date;
    }

    public function GetAllTransactionIDsFromPC($row_start=0, $row_end=50000){
        global $adb;
        if(!$this->pc->connect())
            return "Error Connecting to PC";

        $query = "with cte as (
                      SELECT t.TransactionID,
                       ROW_NUMBER () OVER (ORDER BY TransactionID ASC) as rn
                      FROM Transactions t 
                      LEFT JOIN Portfolios p ON p.PortfolioID = t.PortfolioID
                      WHERE p.PortfolioTypeID = 16 AND p.ClosedAccountFlag=0 AND p.DataSetID IN ({$this->datasets})) 
                      SELECT TransactionID FROM cte 
                      WHERE rn BETWEEN {$row_start} and {$row_end}";
        $results = mssql_query($query);
        if($this->debug)
            echo "Querying Transactions with: {$query}<br /><br />";
        ob_flush();
        flush();
        if($results)
            while($row = mssql_fetch_array($results)){
                $transactions[] = $row['TransactionID'];
            }

        return $transactions;
    }

    public function GetTranasactionCountForDate($date){
        if(!$this->pc->connect())
            return "Error Connecting to PC";
        $datasets = $this->datasets;

        $query = "SELECT count(*) AS count FROM transactions t
                  JOIN Portfolios p ON t.PortfolioID = p.PortfolioID
                  WHERE p.DataSetID IN ($datasets)
                  AND t.TradeDate = '{$date}'
                  AND p.PortfolioTypeID = 16 AND p.ClosedAccountFlag=0";//Get the number of transactions

        $result = mssql_query($query);

        if(mssql_num_rows($result) > 0)
            return mssql_result ($result, 0, 'count');
    }

    public function GetAllTransactionCount(){
        if(!$this->pc->connect())
            return "Error Connecting to PC";
        $datasets = $this->datasets;

        $query = "SELECT count(*) AS count FROM transactions t
                  JOIN Portfolios p ON t.PortfolioID = p.PortfolioID
                  WHERE p.DataSetID IN ($datasets)
                  AND p.PortfolioTypeID = 16 AND p.ClosedAccountFlag=0";//Get the number of transactions

        $result = mssql_query($query);

        if(mssql_num_rows($result) > 0)
            return mssql_result ($result, 0, 'count');
    }

    public function CopyAllTransactionIDsFromPCToCRM(){
        $this->reset = 500000;
        $this->max_rows = 1000000;
        $transaction_result_count = 0;
        $num_transactions = $this->GetAllTransactionCount();
        $transaction_loop_counter = $num_transactions/$this->max_rows;
        $x = 1;
        if($this->debug)
            echo "NUM TRANSACTIONS: {$num_transactions}<br />";
        ob_flush();
        flush();
        do{
            gc_collect_cycles();
            echo "Garbage was collected" . "<br />";
            $start = $x * $this->max_rows;
            $end = $start + $this->max_rows;
            if($this->debug)
                echo "START: {$start}, END ROW: {$end} <br />";
            $transaction_result_count += $this->WriteTransactionIDs($start, $end);
            $x++;
        } while ($x < $transaction_loop_counter);

        return $transaction_result_count;
    }

    static public function DetermineValidPortfolioFromDupes($pids){
        $counts = cTransactionsAccess::GetNumberOfTransactionsForPortfolioID($pids);
        arsort($counts);
        reset($counts);
        $valid = key($counts);
        foreach($counts AS $k => $v){
            if($k != $valid){
                PortfolioInformation_Module_Model::InvalidatePortfolioAndSetDeleted($k);
            }
        }
        return $valid;
    }

    static public function GetNumberOfTransactionsForPortfolioID($pids){
        global $adb;
        $questions = generateQuestionMarks($pids);
        $query = "SELECT COUNT(*) AS count, portfolio_id FROM vtiger_pc_transactions WHERE portfolio_id IN ({$questions}) GROUP BY portfolio_id";
        $result = $adb->pquery($query, array($pids));
        if($adb->num_rows($result) > 0){
            $tmp = array();
            foreach($result AS $k => $v){
                $tmp[$v['portfolio_id']] = $v['count'];
            }
            return $tmp;
        }
        return 0;
    }

    public function GetTransactionCount($pids=null, $date=null){
        if(strlen($pids) < 1){
            if(strlen($date) < 2){
                $date = $this->GetLastModifiedDate();
            }
            $where = " WHERE t.TradeDate >= '{$date}' ";
        }
        else{
            $where = " WHERE p.PortfolioID IN ({$pids}) ";
        }

        if(!$this->pc->connect())
            return "Error Connecting to PC";
        $datasets = $this->datasets;

        $query = "SELECT count(*) AS count FROM PortfolioCenter.dbo.transactions t
                  LEFT JOIN PortfolioCenter.dbo.Portfolios p ON t.PortfolioID = p.PortfolioID
                  {$where}
                  AND p.DataSetID IN ($datasets)";//Get the number of transactions

        $result = mssql_query($query);

        if(mssql_num_rows($result) > 0)
            return mssql_result ($result, 0, 'count');
    }

    /**
     * Copy transactions from PC to the CRM
     * @global type $adb
     * @param type $date
     */
    public function GetTransactionsFromPC($pids=null, $date=null, $row_start=0, $row_end=50000){
        global $adb;

        if(strlen($pids) < 1){
            if(strlen($date) < 2){
                $date = $this->GetLastModifiedDate();
            }
            $where = " WHERE t.TradeDate >= '{$date}' ";
        }
        else{
            $where = " WHERE p.PortfolioID IN ({$pids}) ";
        }

        if(!$this->pc->connect())
            return "Error Connecting to PC";

        /*            $query = "SELECT TOP 50000 * FROM transactions
							  WHERE LastModifiedDate >= '{$date}' ORDER BY TransactionID ASC";//Get all transactions*/
        /*            $query = "with cte as (
							  SELECT *,
							   ROW_NUMBER () OVER (ORDER BY TransactionID ASC) as rn
							  FROM Transactions WHERE LastModifiedDate >= '{$date}')
							  SELECT * FROM cte
							  WHERE rn BETWEEN {$row_start} and {$row_end}";*/
        $query = "with cte as (
                      SELECT t.*,
                       ROW_NUMBER () OVER (ORDER BY TransactionID ASC) as rn
                      FROM PortfolioCenter.dbo.Transactions t 
                      LEFT JOIN PortfolioCenter.dbo.Portfolios p ON p.PortfolioID = t.PortfolioID
                      {$where} AND p.DataSetID IN ({$this->datasets})) 
                      SELECT * FROM cte 
                      WHERE rn BETWEEN {$row_start} and {$row_end}";
        $results = mssql_query($query);
        if($this->debug)
            echo "Querying Transactions with: {$query}<br /><br />";
        ob_flush();
        flush();
        if($results)
            while($row = mssql_fetch_array($results)){
                $transactions[] = $row;
            }

        return $transactions;
    }

    private function ExecuteQuery($query, $feedback=''){
        global $adb;

        if(strlen($feedback) > 1)
            $feedback .= date('Y-m-d H:i:s') . "<br />\r\n";
        if($this->debug)
            echo $feedback;
        ob_flush();
        flush();

        $adb->pquery($query, array());

    }

    public function WriteTransactionsDirectly($transactions){
        $count = 0;
        $reset = 0;
        if($this->debug)
            echo "Retrieved Transactions<br />";
        if(sizeof($transactions) > 0)
        {
            if($this->debug)
                echo "NUM TRANSACTIONS PULLED: " . sizeof($transactions) . "<br />";
            ob_flush();
            flush();

            $transaction_result_count = sizeof($transactions);
            $query = "INSERT INTO vtiger_pc_transactions (transaction_id, portfolio_id, sell_lot_id, trade_lot_id, link_id, custodian_id, symbol_id, activity_id, 
                                                          money_id, broker_id, report_as_type_id, quantity, total_value, conversion_value, accrued_interest, 
                                                          yield_at_purchase, advisor_fee, amount_per_share, other_fee, net_amount, settlement_date, trade_date, 
                                                          origina_trade_date, entry_date, link_date, odd_income_payment_flag, long_position_flag, reinvest_gains_flag, 
                                                          reinvest_income_flag, keep_fractional_shares_flag, taxable_prev_year_flag, complete_transaction_flag, 
                                                          is_reinvested_flag, notes, principal, add_sub_status_type_id, contribution_type_id, matching_method_id, 
                                                          custodian_account, original_link_account, origination_id, last_modified_date, trans_link_id, status_type_id, 
                                                          last_modified_user_id, dirty_flag, invalid_cost_basis_flag, cost_basis_adjustment, security_split_flag, 
                                                          reset_cost_basis_flag) VALUES ";
            $extension = "";
            $update = " ON DUPLICATE KEY UPDATE symbol_id = VALUES(symbol_id), money_id = VALUES(money_id), net_amount = VALUES(net_amount), total_value = VALUES(total_value), 
                quantity = VALUES(quantity), trade_date = VALUES(trade_date), last_modified_date = VALUES(last_modified_date), cost_basis_adjustment = VALUES(cost_basis_adjustment)";
            foreach($transactions AS $k => $row)
            {
                $transaction_id = $row['TransactionID'];
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
                $settlement_date = $this->pc->ConvertDateKeepingTime($settlement_date);
                $trade_date = $row['TradeDate'];
                $trade_date = $this->pc->ConvertDateKeepingTime($trade_date);
                $original_trade_date = $row['OriginalTradeDate'];
                $original_trade_date = $this->pc->ConvertDateKeepingTime($original_trade_date);
                $entry_date = $row['EntryDate'];
                $entry_date = $this->pc->ConvertDateKeepingTime($entry_date);
                $link_date = $row['LinkDate'];
                $link_date = $this->pc->ConvertDateKeepingTime($link_date);
                $last_modified_date = $row['LastModifiedDate'];
                $last_modified_date = $this->pc->ConvertDateKeepingTime($last_modified_date);

                $odd_income_payment_flag = $row['OddIncomePaymentFlag'];
                $long_position_flag = $row['LongPositionFlag'];
                $reinvest_gains_flag = $row['ReinvestGainsFlag'];
                $reinvest_income_flag = $row['ReinvestIncomeFlag'];
                $keep_fractional_shares_flag = $row['KeepFractionalSharesFlag'];
                $taxable_prev_year_flag = $row['TaxablePrevYearFlag'];
                $complete_transaction_flag = $row['CompleteTransactionFlag'];
                $is_reinvested_flag = $row['IsReinvestedFlag'];
                $notes = mysql_real_escape_string($row['Notes']);
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

                $extension .= "('{$transaction_id}','{$portfolio_id}','{$sell_lot_id}','{$trade_lot_id}','{$link_id}','{$custodian_id}','{$symbol_id}','{$activity_id}','{$money_id}',
                              '{$broker_id}','{$report_as_type_id}','{$quantity}','{$total_value}','{$conversion_value}','{$accrued_interest}','{$yield_at_purchase}','{$advisor_fee}',
                              '{$amounter_per_share}','{$other_fee}','{$net_amount}','{$settlement_date}','{$trade_date}','{$original_trade_date}','{$entry_date}','{$link_date}','{$odd_income_payment_flag}',
                              '{$long_position_flag}','{$reinvest_gains_flag}','{$reinvest_income_flag}','{$keep_fractional_shares_flag}','{$taxable_prev_year_flag}','{$complete_transaction_flag}',
                              '{$is_reinvested_flag}','{$notes}','{$principal}','{$add_sub_status_type_id}','{$contribution_type_id}','{$matching_method_id}','{$custodian_account}',
                              '{$original_link_account}','{$origination_id}','{$last_modified_date}','{$trans_link_id}','{$status_type_id}','{$last_modified_user_id}','{$dirty_flag}',
                              '{$invalid_cost_basis_flag}','{$cost_basis_adjustment}','{$security_split_flag}','{$reset_cost_basis_flag}')";
                $count++;
                $reset++;
                if($count < sizeof($transactions) && $reset < $this->reset)
                    $extension .= ",";

                if($reset >= $this->reset)
                {
                    $reset = 0;//Reset the query insert
                    $this->ExecuteQuery($query . $extension . $update, "Inserting into vtiger_pc_transactions ");
                    unset($extension);
                }
            }

            if(strlen($extension) > 0)
                $this->ExecuteQuery($query . $extension . $update, "Inserting into vtiger_pc_transactions -- Final insert ");

            unset($extension);
            unset($transactions);
        }
        return $transaction_result_count;
    }

    private function WriteTransactionIDs($start, $end){
        $transactions = $this->GetAllTransactionIDsFromPC($start, $end);
        $count = 0;
        $reset = 0;
        if($this->debug)
            echo "Retrieved Transactions<br />";
        if(sizeof($transactions) > 0)
        {
            if($this->debug)
                echo "NUM TRANSACTIONS PULLED: " . sizeof($transactions) . "<br />";
            ob_flush();
            flush();

            $transaction_result_count = sizeof($transactions);
            $query = "INSERT INTO vtiger_pc_valid_transactions (id) VALUES ";
            $extension = "";
            $update = " ON DUPLICATE KEY UPDATE id = VALUES(id)";
            foreach($transactions AS $k => $v)
            {

                $extension .= "({$v})";
                $count++;
                $reset++;
                if($count < sizeof($transactions) && $reset < $this->reset)
                    $extension .= ",";

                if($reset >= $this->reset)
                {
                    $reset = 0;//Reset the query insert
                    $this->ExecuteQuery($query . $extension . $update, "Inserting into vtiger_pc_valid_transactions ");
                    unset($extension);
                }
            }

            if(strlen($extension) > 0)
                $this->ExecuteQuery($query . $extension . $update, "Inserting into vtiger_pc_valid_transactions -- Final insert ");

            unset($extension);
            unset($transactions);
        }
        return $transaction_result_count;
    }

    private function WriteTransactions($pids, $date, $start, $end){
        $transactions = $this->GetTransactionsFromPC($pids, $date, $start, $end);
        $count = 0;
        $reset = 0;
        if($this->debug)
            echo "Retrieved Transactions<br />";
        if(sizeof($transactions) > 0)
        {
            if($this->debug)
                echo "NUM TRANSACTIONS PULLED: " . sizeof($transactions) . "<br />";
            ob_flush();
            flush();

            $transaction_result_count = sizeof($transactions);
            $query = "INSERT INTO vtiger_pc_transactions (transaction_id, portfolio_id, sell_lot_id, trade_lot_id, link_id, custodian_id, symbol_id, activity_id, 
                                                          money_id, broker_id, report_as_type_id, quantity, total_value, conversion_value, accrued_interest, 
                                                          yield_at_purchase, advisor_fee, amount_per_share, other_fee, net_amount, settlement_date, trade_date, 
                                                          origina_trade_date, entry_date, link_date, odd_income_payment_flag, long_position_flag, reinvest_gains_flag, 
                                                          reinvest_income_flag, keep_fractional_shares_flag, taxable_prev_year_flag, complete_transaction_flag, 
                                                          is_reinvested_flag, notes, principal, add_sub_status_type_id, contribution_type_id, matching_method_id, 
                                                          custodian_account, original_link_account, origination_id, last_modified_date, trans_link_id, status_type_id, 
                                                          last_modified_user_id, dirty_flag, invalid_cost_basis_flag, cost_basis_adjustment, security_split_flag, 
                                                          reset_cost_basis_flag) VALUES ";
            $extension = "";
            $update = " ON DUPLICATE KEY UPDATE symbol_id = VALUES(symbol_id), money_id = VALUES(money_id), net_amount = VALUES(net_amount), total_value = VALUES(total_value), 
                quantity = VALUES(quantity), trade_date = VALUES(trade_date), last_modified_date = VALUES(last_modified_date), cost_basis_adjustment = VALUES(cost_basis_adjustment)";
            foreach($transactions AS $k => $row)
            {
                $transaction_id = $row['TransactionID'];
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
                $settlement_date = $this->pc->ConvertDateKeepingTime($settlement_date);
                $trade_date = $row['TradeDate'];
                $trade_date = $this->pc->ConvertDateKeepingTime($trade_date);
                $original_trade_date = $row['OriginalTradeDate'];
                $original_trade_date = $this->pc->ConvertDateKeepingTime($original_trade_date);
                $entry_date = $row['EntryDate'];
                $entry_date = $this->pc->ConvertDateKeepingTime($entry_date);
                $link_date = $row['LinkDate'];
                $link_date = $this->pc->ConvertDateKeepingTime($link_date);
                $last_modified_date = $row['LastModifiedDate'];
                $last_modified_date = $this->pc->ConvertDateKeepingTime($last_modified_date);

                $odd_income_payment_flag = $row['OddIncomePaymentFlag'];
                $long_position_flag = $row['LongPositionFlag'];
                $reinvest_gains_flag = $row['ReinvestGainsFlag'];
                $reinvest_income_flag = $row['ReinvestIncomeFlag'];
                $keep_fractional_shares_flag = $row['KeepFractionalSharesFlag'];
                $taxable_prev_year_flag = $row['TaxablePrevYearFlag'];
                $complete_transaction_flag = $row['CompleteTransactionFlag'];
                $is_reinvested_flag = $row['IsReinvestedFlag'];
                $notes = mysql_real_escape_string($row['Notes']);
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

                $extension .= "('{$transaction_id}','{$portfolio_id}','{$sell_lot_id}','{$trade_lot_id}','{$link_id}','{$custodian_id}','{$symbol_id}','{$activity_id}','{$money_id}',
                              '{$broker_id}','{$report_as_type_id}','{$quantity}','{$total_value}','{$conversion_value}','{$accrued_interest}','{$yield_at_purchase}','{$advisor_fee}',
                              '{$amounter_per_share}','{$other_fee}','{$net_amount}','{$settlement_date}','{$trade_date}','{$original_trade_date}','{$entry_date}','{$link_date}','{$odd_income_payment_flag}',
                              '{$long_position_flag}','{$reinvest_gains_flag}','{$reinvest_income_flag}','{$keep_fractional_shares_flag}','{$taxable_prev_year_flag}','{$complete_transaction_flag}',
                              '{$is_reinvested_flag}','{$notes}','{$principal}','{$add_sub_status_type_id}','{$contribution_type_id}','{$matching_method_id}','{$custodian_account}',
                              '{$original_link_account}','{$origination_id}','{$last_modified_date}','{$trans_link_id}','{$status_type_id}','{$last_modified_user_id}','{$dirty_flag}',
                              '{$invalid_cost_basis_flag}','{$cost_basis_adjustment}','{$security_split_flag}','{$reset_cost_basis_flag}')";
                $count++;
                $reset++;
                if($count < sizeof($transactions) && $reset < $this->reset)
                    $extension .= ",";

                if($reset >= $this->reset)
                {
                    $reset = 0;//Reset the query insert
                    $this->ExecuteQuery($query . $extension . $update, "Inserting into vtiger_pc_transactions ");
                    unset($extension);
                }
            }

            if(strlen($extension) > 0)
                $this->ExecuteQuery($query . $extension . $update, "Inserting into vtiger_pc_transactions -- Final insert ");

            unset($extension);
            unset($transactions);
        }
        return $transaction_result_count;
    }

    public function CopyTransactionsFromPCToCRM($pids=null, $date=null){
        $transaction_result_count = 0;
        $num_transactions = $this->GetTransactionCount($pids, $date);
		if($num_transactions == "Error Connecting to PC")
			return false;
        $transaction_loop_counter = $num_transactions/$this->max_rows;
        $x = 0;
        if($this->debug)
            echo "NUM TRANSACTIONS: {$num_transactions}<br />";
        ob_flush();
        flush();
        do{
            $start = $x * $this->max_rows;
            $end = $start + $this->max_rows;
            if($this->debug)
                echo "START: {$start}, END ROW: {$end} <br />";
            $transaction_result_count += $this->WriteTransactions($pids, $date, $start, $end);
            $x++;
        } while ($x < $transaction_loop_counter);

        return $transaction_result_count;
    }

    /**
     * Fill in the vtiger_pc_transactions_pricing table.
     * @global type $adb
     * @param type $portfolio_id
     * @param type $date
     */
    public function CalculatePriceAndCostBasis($portfolio_id=null, $date=null){
        global $adb;
        if(strlen($date) < 2)
            $date = $this->GetLastModifiedDate();

        $condition = '';
        if(strlen($portfolio_id) > 1)
            $condition .= " AND portfolio_id IN ({$portfolio_id}) ";

        $query = "INSERT IGNORE INTO vtiger_pc_transactions_pricing (transaction_id, trade_price, cost_basis) 
        SELECT t.transaction_id, CASE WHEN (s.security_factor > 0) THEN s.security_price_adjustment * pr.price * s.security_factor
                                                                   ELSE s.security_price_adjustment * pr.price END AS total_price,
        t.cost_basis_adjustment AS cost_basis
        FROM vtiger_pc_transactions t
        LEFT JOIN vtiger_pc_security_prices pr ON pr.security_price_id = (SELECT security_price_id FROM vtiger_pc_security_prices  
                              WHERE security_id = t.symbol_id AND price_date <= t.last_modified_date GROUP BY price_date DESC LIMIT 1)
        LEFT JOIN vtiger_securities s ON s.security_id = t.symbol_id
        WHERE  t.last_modified_date >= '{$date}' AND t.last_modified_date <= NOW()
        {$condition}
        ON DUPLICATE KEY UPDATE trade_price=CASE WHEN (s.security_factor > 0) THEN s.security_price_adjustment * pr.price * s.security_factor
                                                                   ELSE s.security_price_adjustment * pr.price END, 
                                cost_basis=t.cost_basis_adjustment";

        $adb->pquery($query, array());
    }

    public function DebugGetPriceInfo($portfolio_id=null, $date=null){
        global $adb;
        if(strlen($date) < 2)
            $date = $this->GetLastModifiedDate();

        $condition = '';
        if($portfolio_id)
            $condition .= " AND portfolio_id = {$portfolio_id} ";

        $query = "SELECT t.transaction_id, CASE WHEN (s.security_factor > 0) THEN s.security_price_adjustment * pr.price * s.security_factor
                                                                   ELSE s.security_price_adjustment * pr.price END AS total_price,
        t.cost_basis_adjustment AS cost_basis
        FROM vtiger_pc_transactions t
        LEFT JOIN vtiger_pc_security_prices pr ON pr.security_price_id = (SELECT security_price_id FROM vtiger_pc_security_prices  
                              WHERE security_id = t.symbol_id AND price_date <= t.trade_date GROUP BY price_date DESC LIMIT 1)
        LEFT JOIN vtiger_securities s ON s.security_id = t.symbol_id
        WHERE  t.last_modified_date >= '{$date}' AND t.last_modified_date <= NOW()
        {$condition}
        ORDER BY t.transaction_id";

        $result = $adb->pquery($query, array());
        foreach($result AS $k => $v)
            echo "TRANS: {$v['transaction_id']}, TOTAL: {$v['total_price']}, CB: {$v['cost_basis']}<br />";
    }

    /**
     * Get the inception date for the given account number(s).  Passed in as a comma separated string
     * @param type $account_numbers
     */
    public function GetInceptionDate($pids)
    {
        global $adb;
        $query = "SELECT trade_date 
                  FROM vtiger_pc_transactions
                  WHERE portfolio_id IN({$pids})
                  ORDER BY trade_date ASC LIMIT 1";
        $result = $adb->pquery($query, array());
        $date = $adb->query_result($result, 0, "trade_date");
        return $date;
    }
}

?>
<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of cTransactions
 *
 * The transactions class will handle all transaction flow and logic.
 * @author theshado
 */

class cTransactions {
    public function __construct() {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        $query = "DROP TABLE if exists t_transactions_{$current_user->get('id')}";
        $adb->pquery($query, array());
        
        $query = "CREATE TEMPORARY TABLE t_transactions_{$current_user->get('id')} (
                    id INTEGER NOT NULL PRIMARY KEY,
                    pid int,
                    activity_id int,
                    quantity float,
                    total_value float,
                    net_amount float,
                    trade_date datetime,
                    principal float,
                    add_sub_status_type_id int,
                    cost_basis_adjustment float,
                    report_as_type_id int,
                    calculated_amount float,
                    money_id int,
                    accrued_interest float,
                    symbol_id int,
                    security_symbol VARCHAR(25),
                    description VARCHAR(250),
                    price_adjustment float,
                    account_number VARCHAR(50),
                    code_id int,
                    code_description VARCHAR(50),
                    sub_sub_category VARCHAR(75),
                    security_type_id int,
                    activity_name VARCHAR(50),
                    transaction_description VARCHAR(150),
                    current_price float,
                    origination VARCHAR(50),
                    report_as_type_name VARCHAR(100),
                    security_factor float,
                    is_reinvested_flag int,
                    value float)";
        $adb->pquery($query, array());//Create the temp table to work with the transactions
    }

    /**Convert the sql date to a proper format*/
    public function ConvertDate($date)
    {   
        $time = strtotime($date);
        $time = date('Y-m-d h:i:s', $time);
        return $time;
    }
    
    /**
     * Get the last transaction date for the given portfolio ID(s)
     * @global type $adb
     * @param type $pids
     */
    public function GetLastTransactionDate($pids, $reset=0){
        global $adb;
        if($reset != 0)
            return "1800-01-01 00:00:00";
        
        $query = "SELECT DATE_FORMAT(last_modified_date,'%Y-%m-%d 00:00:00') AS last_modified_date
                  FROM vtiger_pc_transactions 
                  WHERE portfolio_id IN ({$pids}) 
                  ORDER BY last_modified_date DESC LIMIT 1";
                  
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, "last_modified_date");
        else
            return "1800-01-01 00:00:00";
    }
/**
     * Copy all transactions for the given portfolio ID's from PC to the crm
     * 
     * @global type $myServer
     * @global type $myUser
     * @global type $myPass
     * @global type $myDB
     * @global type $adb
     * @param type $pids
     * @return type
     */
    public function CopyTransactions($pids, $reset=0)
    {
        $myServer = "lanserver2n";
        $myUser = "syncuser";
        $myPass = "Consec11";
        $myDB = "PortfolioCenter";
        global $adb;

        if(!$pids)
            return;
        
        $last_transaction_date = $this->GetLastTransactionDate($pids, $reset);
        echo "Last Transaction Date: {$last_transaction_date}<br />";
        //connection to the database
        $dbhandle = mssql_connect($myServer, $myUser, $myPass);// or die("Couldn't connect to SQL Server on $myServer");
        if(!$dbhandle)
        {
            echo "NO HANDLE!<br />";
        }
        else 
        {
            $query = "SELECT * FROM transactions
                      WHERE portfolioID IN ({$pids}) 
                      AND LastModifiedDate > DATEADD(day,1,'{$last_transaction_date}')
                      ORDER BY TransactionID DESC";//Get all transactions
      
        //        AND SymbolID = 1
        //        AND TransactionID NOT IN(6430380, 7235746, 7562524, 7569131, 8134001, 8138838, 8139299, 8143422, 8143423, 8144271, 8144273, 8144275, 8144277, 8144279, 8144281, 8144283, 8144285, 8144287, 8144289, 8144291, 8144293, 8144295, 8144297, 8144299, 8144301, 8144303, 8144305, 8144307, 8144309, 8144311, 8144313, 8144315, 8144317, 8144319, 8144321, 8144323, 8144325, 8144327, 8144329, 8144331, 8144333, 8144335, 8144337, 8155874, 8155875, 8968318, 8968319, 8980893, 8980894, 8980895, 8980896, 8993660, 9013353, 9024966, 9060259, 9061252, 9099495, 9102363, 9102364, 9113984, 9113985, 9113986, 9179389)";
            $transactions = mssql_query($query);
            if($transactions)
            while($row = mssql_fetch_array($transactions))
            {
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
                      ON DUPLICATE KEY UPDATE symbol_id = ?, money_id = ?, net_amount = ?, total_value = ?, quantity = ?, trade_date = ?, last_modified_date=?";
                echo "ADDING TRANSACTION ID: {$transaction_id}, MODIFIED: {$last_modified_date}<br />";
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
                                          $symbol_id, $money_id, $net_amount, $total_value, $quantity, $trade_date, $last_modified_date));
                /**UNDELETE CODE...*/
//                $query = "UPDATE vtiger_pc_transactions_deleted SET deleted = 0 WHERE transaction_id=?";
//                $adb->pquery($query, array($transaction_id));                
            }

/*            
            $query = "SELECT transaction_id FROM vtiger_pc_transactions WHERE portfolio_id IN ({$pids}) ORDER BY transaction_id DESC";
            $result = $adb->pquery($query, array());
            if($result)
            foreach($result AS $k => $v)
                $crm_list[] = $v['transaction_id'];//Get a list of all transaction ID's in the CRM database
            
            $delete_ids = array_merge($id_list, $crm_list);
            $delete_ids = RemoveDuplicates($delete_ids);
            $query = "INSERT IGNORE INTO vtiger_pc_transactions_deleted (transaction_id, deleted) VALUES (?, 1)";
            
            foreach($delete_ids AS $k => $v)
                $adb->pquery($query, array($v));
                        
            $query = "SELECT transaction_id FROM vtiger_pc_transactions WHERE portfolio_id IN ({$pids}) AND complete_transaction_flag = 0";
            $result = $adb->pquery($query, array());
            if($result)
                foreach($result AS $k => $v){
                    $delete_list[] = $v['transaction_id'];
                }
                
            $query = "INSERT IGNORE INTO vtiger_pc_transactions_deleted (transaction_id, deleted) VALUES (?, 1)";
            foreach($delete_list AS $k => $v){
                $adb->pquery($query, array($v));
            }*/
        }
        
        return $last_transaction_date;
    }    
    
        /*
     * Get a list of account numbers from the portfolio ID's passed in as comma separated values
     */
    public function GetAccountsFromCSVPids($pids)
    {
        global $adb;
        $query = "SELECT account_number FROM vtiger_pc_transactions WHERE portfolio_id IN ({$pids}) GROUP BY account_number";
        $result = $adb->pquery($query, array());
        if($result)
        foreach($result AS $k => $v)
            $accounts[] = $v['account_number'];
        return $accounts;
    }
    
    /**
     * Get the estimated security price.  It takes the date and finds if the security exists on or before that date.  If it doesn't, it then checks if it exists
     * on or after the date given.
     */
    public function GetEstimateSecurityPrice($symbol_id, $date)
    {
        $price = $this->GetSecurityPriceAsOfDate($symbol_id, $date);
//        echo $symbol_id . "<br />";
        if(!$price)
            $price = $this->GetSecurityPriceByDateGreater ($symbol_id, $date);
        return $price;
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
                  WHERE p.security_id = ?
                  AND (price_date <= ?)
                  AND p.price != 0
                  GROUP BY price_date DESC
                  LIMIT 1";
        $result = $adb->pquery($query, array($symbol_id, $date));
        
        return $adb->query_result($result, 0, "total_price");
    }
    
    /**
     * Get the security price for the given symbol to the nearest earlier date.  Will not get dates < than the entered date
     * @global type $adb
     * @param type $symbol_id
     * @param type $date
     * @return type
     */
    public function GetSecurityPriceByDateGreater($symbol_id, $date)
    {
        global $adb;
        $query = "SELECT p.price, CASE WHEN s.security_factor > 0 THEN s.security_price_adjustment * p.price * s.security_factor
                                  ELSE s.security_price_adjustment * p.price END AS total_price
                  FROM vtiger_pc_security_prices p
                  LEFT JOIN vtiger_securities s ON s.security_id = p.security_id
                  WHERE p.security_id = ? AND p.price_date >= ?
                  AND p.price != 0
                  GROUP BY price_date ASC
                  LIMIT 1";
        
        $result = $adb->pquery($query, array($symbol_id, $date));
        
        return $adb->query_result($result, 0, "total_price");
    }
    
    /**
     * Get the latest security price for the given symbol
     * @global type $adb
     * @param type $symbol_id
     * @return type
     */
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
    
    /**
     * Fill the transactions table with the transactions grabbed during construction
     * @global type $adb
     * @param type $transactions
     */
    public function FillTransactionTable($transactions)
    {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        $query = "TRUNCATE TABLE t_transactions_{$current_user->get('id')}";
        $adb->pquery($query, array());
        $query = "INSERT INTO t_transactions_{$current_user->get('id')} (id, pid, activity_id, quantity, total_value, net_amount, trade_date, principal, add_sub_status_type_id, cost_basis_adjustment, report_as_type_id,
                              calculated_amount, money_id, accrued_interest, symbol_id, security_symbol, description, price_adjustment, account_number, code_id, code_description,
                              security_type_id, activity_name, transaction_description, current_price, origination, report_as_type_name, security_factor, value, sub_sub_category, is_reinvested_flag) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        if($transactions)
        foreach($transactions AS $k => $v)
        {
            if($v['security_symbol'] && $v['quantity'] != 0)
            {
                if($v['activity_id'] == 150)
                {
//                    if($v['quantity'] > 0)
//                        $v['quantity'] *= -1;
                }
                if($v['activity_id'] == 80)
                    $v['quantity'] *= -1;
                if($v['activity_id'] == 150)
                    $v['quantity'] *= -1;
                if(strlen($v['sub_sub_category']) == 0)
                    $v['sub_sub_category'] = '';
                if($v['security_type_id'] == 8){
//                    $v['quantity'] *= 100;                  
                }
                $adb->pquery($query, array($v['id'],$v['pid'],$v['activity_id'],$v['quantity'],$v['total_value'],$v['net_amount'],$v['trade_date'],$v['principal'],$v['add_sub_status_type_id'],
                                           $v['cost_basis_adjustment'],$v['report_as_type_id'],$v['calculated_amount'], $v['money_id'], $v['accrued_interest'], $v['symbol_id'], $v['security_symbol'], $v['description'], $v['price_adjustment'], 
                                           $v['account_number'], $v['code_id'], $v['code_description'],$v['security_type_id'], $v['activity_name'], $v['transaction_description'], $v['current_price'], $v['origination'], 
                                           $v['report_as_type_name'], $v['security_factor'], $v['value'], $v['sub_sub_category'], $v['is_reinvested_flag']));
            }
        }
//        $this->ShowTTransactionsTable();
    }

    public function ShowTTransactionsTable()
    {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        $query = "SELECT * FROM t_transactions_{$current_user->get('id')}";
                  //WHERE security_symbol = 'CASH' AND account_number='Z70-304700'";
        $result = $adb->pquery($query, array());
        $total = 0;
        foreach($result AS $k => $v)
        {
            print_r($v);
            echo "<br /><br />";
//            echo "QUANTITY: {$v['quantity']}<br />";
//            $total+=$v['quantity'];
        }
//        echo "FINAL TOTAL: {$total}<br />";
    }
    /**
     * 
     * @global type $adb
     * @param type $symbol_totals
     */
    public function CreateSummaryTable($symbol_totals)
    {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        $query = "drop table if exists t_summary_table_{$current_user->get('id')} ";
        $adb->pquery($query, array());
        
        $query = "CREATE TABLE t_summary_table_{$current_user->get('id')}  (
                    id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    quantity float(10,2),
                    cost_basis_adjustment float(10,2),
                    account_number VARCHAR(100),
                    price_adjustment float(10,5),
                    code_description VARCHAR(100),
                    sub_sub_category VARCHAR(75),
                    description VARCHAR(200),
                    security_symbol VARCHAR(20),
                    symbol_id int(20),
                    security_type_id int,
                    report_as_type_id int,
                    current_price float(100,5),
                    latest_price float(100,5),
                    latest_value float(100,5),
                    origination VARCHAR(100),
                    ugl float(100,5),
                    gl float(100,5),
                    weight float(100,5),
                    activity_id int)";
        $adb->pquery($query, array());
        
        $cash_subtraction = array();
        foreach($symbol_totals AS $k => $v)
        {
            if($v['activity_id'] == 80){
                $v['ugl'] = $v['latest_value'] + $v['cba_total'];
                $cash_subtraction[$v['account_number']] += $v['cba_total'];
            }
            else
                $v['ugl'] = $v['latest_value'] - $v['cba_total'];
            
            if($v['cba_total'] != 0)
                $v['gl'] = $v['ugl'] / $v['cba_total']*100;
            else
                $v['gl'] = 0;
            $query = "INSERT INTO t_summary_table_{$current_user->get('id')}  (quantity, cost_basis_adjustment, account_number, price_adjustment, 
                                    code_description, description, security_symbol, symbol_id, security_type_id, report_as_type_id, current_price, latest_price, latest_value, origination, activity_id, sub_sub_category, ugl, gl)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $adb->pquery($query, array($v['total_quantity'], $v['cba_total'], $v['account_number'], $v['price_adjustment'], $v['code_description'], $v['description'],
                                       $v['security_symbol'], $v['symbol_id'], $v['security_type_id'], $v['report_as_type_id'], $v['current_price'], $v['latest_price'], $v['latest_value'], $v['origination'], $v['activity_id'], $v['sub_sub_category'], $v['ugl'], $v['gl']));
        }
        
        $query = "UPDATE t_summary_table_{$current_user->get('id')}
                  SET latest_value = latest_value - ?,
                  cost_basis_adjustment = cost_basis_adjustment - ?
                  WHERE account_number=?
                  AND symbol_id=1";

        foreach($cash_subtraction AS $k => $v){//We need to subtrace short amount from cash value
//            $adb->pquery($query, array($v, $v, $k));
        }
/*        $query = "UPDATE t_summary_table_{$current_user->get('id')}
                  SET ugl = (CASE WHEN activity_id = 80 THEN SUM(a.latest_value) + SUM(a.cost_basis_adjustment) 
                                             ELSE SUM(a.latest_value) - SUM(a.cost_basis_adjustment) 
                                             END)";
        echo $query;exit;
        $query = "SELECT * FROM t_summary_table_{$current_user->get('id')}";
        $result = $adb->pquery($query, array());
        foreach($result AS $k => $v){
            print_r($v);
            echo "<br /><br />";
        }*/
/*        
        $query = "UPDATE t_summary_table_{$current_user->get('id')}  
                  SET latest_value = latest_value * -1 
                  WHERE activity_id = 80";
        $adb->pquery($query, array());
        
        $query = "drop table if exists summary_t2_{$current_user->get('id')}";
        $adb->pquery($query, array());
        
        $query = "create temporary table summary_t2_{$current_user->get('id')} SELECT * from t_summary_table_{$current_user->get('id')} ;";
        $adb->pquery($query, array());
        
        $query = "UPDATE t_summary_table_{$current_user->get('id')}  t1
                  SET t1.latest_value = t1.latest_value + (SELECT latest_value + cost_basis_adjustment + latest_value FROM summary_t2_{$current_user->get('id')} WHERE account_number = t1.account_number AND activity_id=80),
                  t1.cost_basis_adjustment = 0
                  WHERE t1.symbol_id = 1";*/
       	/* ====  START : Felipe 2016-07-25 MyChanges ===== */
       
       	// $adb->pquery($query, array()); 
       		//ToDo : Changes 11june,2016 need to pass query params
         /* ====  END : Felipe 2016-07-25 MyChanges ===== */
                  
    }
    
    /**
     * 
     * Get Annual Management Fees
     */
    public function GetAnnualManagementFee($account_number)
    {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        $query = "SELECT SUM(cost_basis_adjustment) AS cba_total 
                  FROM t_transactions_{$current_user->get('id')} 
                  WHERE account_number = ? 
                  AND report_as_type_id = 60 
                  AND trade_date >= now() - interval 12 month";
        $result = $adb->pquery($query, array($account_number));
        return $adb->query_result($result, 0, "cba_total");
    }
    
    /**
     * Selects the sum of quantity (total_quantity), sum of cost basis adjustment (cba_total), account_number, price_adjustment, code_description, description, 
     * origination, security_symbol, report_as_type_id, symbol_id, security_type_id, trade_date, current_price from t_transactions.  
     * The returns is grouped by account_number, symbol_id -- You can get the same symbol multiple times if in multiple accounts
     * By default, $trade_date is set to NOW()
     * Groups by account_number, symbol_id
     * @global type $adb
     * @return type
     */
    public function GetSymbolTotals($trade_date='NOW()',$onlyPositiveValues = false, $and='')
    {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        //Cost basis was using SUM(quantity)*current_price AS cba_total     -- Gave the wrong cost basis by far
        $query = "SELECT SUM(quantity) AS total_quantity, SUM(cost_basis_adjustment) AS cba_total, account_number, price_adjustment, code_description, description, origination,
                  security_symbol, id, activity_id, report_as_type_id, symbol_id, security_type_id, DATE_FORMAT(trade_date,'%m-%d-%Y') AS trade_date, current_price, sub_sub_category
                  FROM t_transactions_{$current_user->get('id')} 
                  WHERE trade_date <= ?
                  {$and}
                  GROUP BY account_number, symbol_id
                  ORDER BY trade_date DESC";
        $result = $adb->pquery($query, array($trade_date));

        $tmp = array();
        
        $k = '0';
        
        while($v = $adb->fetchByAssoc($result))
        {
            if($v['total_quantity'] <= -0.01 || $v['total_quantity'] >= 0.01)
            {
                $price = $this->GetEstimateSecurityPrice($v['symbol_id'], $trade_date);

    //            if($v['symbol_id'] == 91199)
    //                echo "PRICE: {$price}, TD: {$trade_date}<br />";
    //            $price = $v['current_price'];//$this->GetSecurityPriceAsOfDate($v['symbol_id'], $trade_date);// $v['current_price'];//$this->GetLatestSecurityPrice($v['symbol_id']);
                if($v['security_type_id'] == 11)
                    $price = 1;
                if($v['security_type_id'] == 8)//If the transaction is dealing with an option, we multiply it by 100
                    $v['total_quantity'] *= 100;
                $v['latest_price'] = round($price,2);
                $v['current_price'] = round($price,2);
                $v['latest_value'] = $price*$v['total_quantity'];

    /*            if($v['activity_id'] == 80)// || $v['activity_id'] == 150)//If the activity is a short or a cover, we multiply it by -1
                    $v['quantity'] *= -1;
    //                $v['latest_value'] *= -1;
    */                      
                if($onlyPositiveValues)
                {
                    if($v['latest_value'] > 0)
                        $tmp[$k] = $v;
                }
                else
                    $tmp[$k] = $v;
            }
/*            
            if($v['security_type_id'] == 8)
                $tmp[$k] = $v;
            else
                if($v['latest_value'] > 0)
                    $tmp[$k] = $v;
 */
//            echo "TID: {$v['id']}, TD: {$v['trade_date']}, symbol: {$v['security_symbol']}, SYMBOL ID: {$v['symbol_id']}, PRICE: {$price}, Quantity: {$v['total_quantity']}, SID: {$v['symbol_id']}, ACCT: {$v['account_number']}<br />";
//            echo "SYMBOL: {$v['security_symbol']}, {$v['total_quantity']}, CP: {$v['latest_price']}, TOTAL: <strong>{$v['latest_value']}</strong><br />";
			$k++;
        }
        return $tmp;
    }
    
    /**
     * Calculate the total short value for the symbols passed in.  Uses the results from the "GetSymbolTotals" function
     * @param type $symbol_totals
     * @return type
     */
    public function GetShortValueFromSymbolTotals($symbol_totals)
    {
        $total = 0;
        foreach($symbol_totals AS $k => $v)
        {
            if($v['activity_id'] == 80)
                $total -= $v['latest_value'];
        }

        return $total;
    }
    
    /**
     * Calculate the total cover value for the symbols passed in.  Uses the results from the "GetSymbolTotals" function
     * @param type $symbol_totals
     * @return type
     */
    public function GetCoverValueFromSymbolTotals($symbol_totals)
    {
        $total = 0;
        foreach($symbol_totals AS $k => $v)
        {
            if($v['activity_id'] == 150)
                $total += $v['latest_value'];
        }

        return $total;
    }
    
    /**
     * Calculate the total for the symbols passed in.  Uses the results from the "GetSymbolTotals" function
     * @param type $symbol_totals
     * @return type
     */
    public function AddAllSymbolTotals($symbol_totals)
    {
        $total = 0;
        foreach($symbol_totals AS $k => $v)
        {
            $total += $v['latest_value'];
        }

        return $total;
    }
    
    /**
     * Get all transactions from the created transaction table
     * @global type $adb
     * @return type
     */
    public function GetAllTransactions($accounts=null, $special_instructions = null)
    {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        if($special_instructions)
        {
            $direction = $special_instructions['direction'];
            $order_by = $special_instructions['order_by'];
            $filter = $special_instructions['filter'];
            $pid = $special_instructions['pid'];
        }
        else
        {
            $direction = "DESC";
            $order_by = "trade_date";
        }

        /*This is an extra check in case we pass in direction but not order_by, or vice versa.  The array will come in as positive but not fill in anything for one
        of these two values*/
        if(!$direction)
            $direction = "ASC";
        if(!$order_by)
            $order_by = "trade_date";
        if($pid)
            $pid = " AND t.portfolio_id IN ({$pid})";
            
        if($accounts)
            $accounts = " WHERE account_number IN ({$accounts}) ";
        if($filter == 'null')
            $filter = "";
        $query = "SELECT *, DATE_FORMAT(trade_date,'%m-%d-%Y') AS trade_date_display FROM t_transactions_{$current_user->get('id')} {$accounts} {$filter} ORDER BY {$order_by} {$direction}";

        $result = $adb->pquery($query, array());

        return $result;
    }

    /**
     * Get the inception date for the given account number(s).  Passed in as a comma separated string
     * @param type $account_numbers
     */
    public function GetInceptionDate($pids)
    {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        $query = "SELECT trade_date 
                  FROM t_transactions_{$current_user->get('id')}
                  WHERE pid IN({$pids})
                  ORDER BY trade_date ASC LIMIT 1";
        $result = $adb->pquery($query, array());
        $date = $adb->query_result($result, 0, "trade_date");
        return $date;
    }
    
    /**
     * Get all unique account numbers in the transactions table
     * @global type $adb
     * @return type
     */
    public function GetAccountNumbers()
    {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        $query = "SELECT account_number FROM t_transactions_{$current_user->get('id')} GROUP BY account_number";
        $result = $adb->pquery($query, array());
        foreach($result AS $k => $v)
            $accounts[] = $v['account_number'];
        return $accounts;
    }
    
    /**
     * 
     * GetAccountTransactions returns the transactions for a single account
     * @global type $adb
     * @param type $account_number
     * @param type $date
     * @return type
     */
    public function GetAccountTransactions($account_number, $date = null)
    {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        $m = date('m');
        $d = date('d');
        $Y = date('Y');

        $today = date('Y-m-d',mktime(0,0,0,$m,$d,$Y));
        
        if($date == null)
            $date = $today;
        $query = "SELECT * FROM t_transactions_{$current_user->get('id')} WHERE account_number = ? AND trade_date <= ?";
        $result = $adb->pquery($query, array($account_number, $date));
        return $result;
    }
    
    //Get portfolio transactions for the portfolio ID's.  $pids is comma separated
    public function GetAllPortfolioTransactions($pids, $special_instructions = null)
    {
        if($special_instructions)
        {
            $direction = $special_instructions['direction'];
            $order_by = $special_instructions['order_by'];
            $filter = $special_instructions['filter'];
            $pid = $special_instructions['pid'];
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
        if($pid)
            $pid = " AND t.portfolio_id IN ({$pid})";
        global $adb;

/*        if($latest_price_only)
            $join = "LEFT JOIN vtiger_pc_security_prices pr ON pr.security_price_id = (SELECT security_price_id FROM vtiger_pc_security_prices  
                     WHERE security_id = s.security_id GROUP BY price_date DESC LIMIT 1) ";
        else*/
            $join = "LEFT JOIN vtiger_pc_security_prices pr ON pr.security_price_id = (SELECT security_price_id FROM vtiger_pc_security_prices  
                     WHERE security_id = s.security_id AND price_date = t.trade_date LIMIT 1) ";

        $query = "SELECT t.*, DATE_FORMAT(t.trade_date,'%m-%d-%Y') AS trade_date_display, pr.*, a.activity_name, p.portfolio_account_number AS AccountNumber, s.security_id, s.security_symbol, s.security_type_id, cde.code_name, st.security_type_name,
                              s.security_description, s.security_price_adjustment, s.security_factor, c.code_id, cd.code_description, o.interface_name, rat.report_as_type_name,
                              CASE WHEN (s.security_factor > 0) THEN s.security_price_adjustment * pr.price * s.security_factor
                                                                 ELSE s.security_price_adjustment * pr.price END AS total_price, csub.code_description AS sub_sub_category
                  FROM vtiger_pc_transactions t
                  LEFT JOIN vtiger_portfolios p ON p.portfolio_id = t.portfolio_id
                  LEFT JOIN vtiger_securities s ON s.security_id = t.symbol_id
                  LEFT JOIN vtiger_pc_codes c ON c.code_id = 
                     (SELECT code_id FROM vtiger_pc_security_codes WHERE security_id = s.security_id AND code_type_id = 20)
                  LEFT JOIN vtiger_pc_security_codes sc ON sc.security_id = s.security_id
                  LEFT JOIN vtiger_pc_codes cd ON cd.code_id = c.code_id
                  LEFT JOIN vtiger_pc_codes cde ON cde.code_id = sc.code_id
                  LEFT JOIN vtiger_pc_codes csub ON csub.code_id = 
                     (SELECT code_id FROM vtiger_pc_security_codes WHERE security_id = s.security_id AND code_type_id = 10)
                  LEFT JOIN vtiger_pc_activities a ON a.activity_id = t.activity_id
                  LEFT JOIN vtiger_pc_interface_originations o ON t.origination_id = o.origination_id
                  LEFT JOIN vtiger_pc_report_as_types rat ON rat.report_as_type_id = t.report_as_type_id
                  {$join}
                  LEFT JOIN vtiger_security_types st ON st.security_type_id = s.security_type_id
                  WHERE p.portfolio_id IN ({$pids})
                  AND status_type_id = 100
                  {$pid}
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
        while($v = $adb->fetchByAssoc($result))
        {
            $v['total_price'] = 0;

            if(!$v['total_price'] && $v['security_type_id'] != 11 && $v['security_id'])
            {//The price didn't exist when we did our pull (there was nothing set <= the trade date, so grab the next nearest one)
                $v['price'] = $this->GetEstimateSecurityPrice($v['security_id'], $v['trade_date']); //$this->GetSecurityPriceByDateGreater($v['security_id'], $v['trade_date']);
                $v['total_price'] = $v['price'];
            }
            $price = $v['total_price'];

            if($v['security_symbol'] == "CASH" || $v['security_type_id'] == 11)
                $price = 1;
            
            if($v['security_type_id'] == 8)//If the transaction is dealing with an option, we multiply it by 1000
                $price *= 100;

            if($v['activity_id'] == 80)// || $v['activity_id'] == 150)//If the activity is a short, we multiply it by -1
            {
//                $value *= -1; 
//                $v['cost_basis_adjustment'] *= -1;
 //               $v['quantity'] *= -1;
            }
            
            if($v['activity_id'] == 90){//Income
                
            }
            if($v['activity_id'] == 150){
//                $v['quantity'] *= -1;
//                echo "PRICE: {$v['price']}, Quantity: {$v['quantity']}<br />";
            }

            $value = $price * $v['quantity'];

            $desc = $v['code_description'];

            $v['trade_date'] = str_replace(" 00:00:00", '', $v['trade_date']);
            $transactions[] = array("id" => $v['transaction_id'],
                                    "pid" => $v['portfolio_id'],
                                    "activity_id" => $v['activity_id'],
                                    "quantity" => $v['quantity'],
                                    "total_value" => $v['total_value'],
                                    "net_amount" => $v['net_amount'],
                                    "trade_date" => $v['trade_date'],
                                    "principal" => $v['principal'],
                                    "add_sub_status_type_id" => $v['add_sub_status_type_id'],
                                    "cost_basis_adjustment" => $v['cost_basis_adjustment'],
                                    "report_as_type_id" => $v['report_as_type_id'],
                                    "calculated_amount" => '0',
                                    "money_id" => $v['money_id'],
                                    "accrued_interest" => $v['accrued_interest'],
                                    "symbol_id" => $v['symbol_id'],
                                    "security_symbol" => $v['security_symbol'],
                                    "description" => $v['security_description'],
                                    "price_adjustment" => $v['security_price_adjustment'],
                                    "account_number" => $v['accountnumber'],
                                    "code_id" => $v['code_id'],
                                    "code_description" => $desc, //$v['code_description'],
                                    "sub_sub_category" => $v['sub_sub_category'],
                                    "security_type_id" => $v['security_type_id'],
                                    "activity_name" => $v['activity_name'],
                                    "transaction_description" => $v['notes'],
                                    "current_price" => $price,
                                    "origination" => $v['interface_name'],
                                    "report_as_type_name" => $v['report_as_type_name'],
                                    "security_factor" => $v['security_factor'],
                                    "is_reinvested" => $v['is_reinvested_flag'],
                                    "value" => $value);
        }

        return $transactions;
    }

    /**
     * Returns the unique field values
     * @global type $adb
     * @param type $field
     */
    private function GetUniqueFields($field, $pids)
    {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        if($pids)
            $pid = " WHERE pid IN ({$pids}) ";
        $query = "select {$field} FROM t_transactions_{$current_user->get('id')} {$pid} GROUP BY {$field}";
        $result = $adb->pquery($query, array());
        if($result)
            foreach($result AS $k => $v)
                $f[] = $v[$field];
        return $f;
    }
/**
     * Separate the symbols, descriptions, actions and security_types from the given transactions
     * @param type $transactions
     */
    public function SeparateTransactionsForFiltering($pids)
    {
        $symbols = $this->GetUniqueFields('security_symbol', $pids);
        $transaction_description = $this->GetUniqueFields('transaction_description', $pids);
        $activity_name = $this->GetUniqueFields('activity_name', $pids);
        $code_description = $this->GetUniqueFields('code_description', $pids);
        
        $results = array("symbols"=>$symbols,
                         "descriptions"=>$transaction_description,
                         "actions"=>$activity_name,
                         "security_types"=>$code_description);
        return $results;
    }

    /**
     * Return's all cover transactions
     * @global type $adb
     * @param type $accounts
     * @param type $sdate
     * @param type $edate
     * @return type
     */
    public function GetCovers($accounts=null, $sdate=null, $edate=null){
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        if($accounts)
            $accounts = " AND account_number IN ({$accounts}) ";

        if($sdate && $edate)
            $between = " AND trade_date BETWEEN '{$sdate}' AND '{$edate}' ";
                    
        $query = "SELECT *
               FROM t_transactions_{$current_user->get('id')}
               WHERE activity_id IN(150)
               {$accounts}
               {$between}";

        $result = $adb->pquery($query, array());
        
        $covers = array();
    	
        if($adb->num_rows($result)){
        	while($v = $adb->fetchByAssoc($result))
        		$covers[] = $v;
        }

        return $covers;
    }
    
    /**
     * Return's all short transactions
     * @global type $adb
     * @param type $accounts
     * @param type $sdate
     * @param type $edate
     * @return type
     */
    public function GetShorts($accounts=null, $sdate=null, $edate=null)
    {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        if($accounts)
            $accounts = " AND account_number IN ({$accounts}) ";

        if($sdate && $edate)
            $between = " AND trade_date BETWEEN '{$sdate}' AND '{$edate}' ";
                    
        $query = "SELECT *
               FROM t_transactions_{$current_user->get('id')}
               WHERE activity_id IN(80)
               {$accounts}
               {$between}";

        $result = $adb->pquery($query, array());
        
        $shorts = array();
        
        if($adb->num_rows($result)){
        	while($v = $adb->fetchByAssoc($result))
        		$shorts[] = $v;
        }
       
        return $shorts;
    }

    /**
     * Return's all sells in transactions
     * @global type $adb
     * @param type $accounts
     * @param type $sdate
     * @param type $edate
     * @return type
     */
    public function GetSells($accounts=null, $sdate=null, $edate=null)
    {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        if($accounts)
            $accounts = " AND account_number IN ({$accounts}) ";

        if($sdate && $edate)
            $between = " AND trade_date BETWEEN '{$sdate}' AND '{$edate}' ";
                    
        $query = "SELECT *
               FROM t_transactions_{$current_user->get('id')}
               WHERE activity_id IN(140)
               {$accounts}
               {$between}";

        $result = $adb->pquery($query, array());
        
        $sells = array();
    
        if($adb->num_rows($result)){
        	while($v = $adb->fetchByAssoc($result))
        		$sells[] = $v;
        }
        
        return $sells;
    }    
    
    /**
     * Return's all buys in transactions
     * @global type $adb
     * @param type $accounts
     * @param type $sdate
     * @param type $edate
     * @return type
     */
    public function GetBuys($accounts=null, $sdate=null, $edate=null)
    {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        if($accounts)
            $accounts = " AND account_number IN ({$accounts}) ";

        if($sdate && $edate)
            $between = " AND trade_date BETWEEN '{$sdate}' AND '{$edate}' ";
                    
        $query = "SELECT *
               FROM t_transactions_{$current_user->get('id')}
               WHERE activity_id IN(70)
               {$accounts}
               {$between}";

        $result = $adb->pquery($query, array());
        
        $buys = array();
        
        if($adb->num_rows($result)){
        	while($v = $adb->fetchByAssoc($result))
        		$buys[] = $v;
        }
	
        return $buys;
    }    
    
    /**
     * Get contributions for the given accounts.  The returned result is an array without filtering out transfers
     * @global type $adb
     * @param type $accounts
     * @param type $sdate
     * @param type $edate
     * @return type
     */
    public function GetContributions($accounts=null, $sdate=null, $edate=null)
    {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        if($accounts)
            $accounts = " AND account_number IN ({$accounts}) ";

        if($sdate && $edate)
            $between = " AND trade_date BETWEEN '{$sdate}' AND '{$edate}' ";
            
        $query = "SELECT *
                  FROM t_transactions_{$current_user->get('id')}
                  WHERE activity_id IN(10, 50)
                  {$accounts}
                  {$between}
                  AND value > 0";
                  
        $result = $adb->pquery($query, array());
        
        $contributions = array();

        if($adb->num_rows($result)){
            while($v = $adb->fetchByAssoc($result))
                $contributions[] = $v;
        }

        return $contributions;
/*                  
        $isFirst = 1;
        $result = $adb->pquery($query, array());
        foreach($result AS $k => $v)
        {
            $val = $v['value'] * -1;
            if($isFirst)
            {
                $q .= " AND (trade_date = '{$v['trade_date']}' AND value={$val}) ";
                $isFirst = 0;
            }
            else
                $q .= " OR (trade_date = '{$v['trade_date']}' AND value={$val}) ";
        }

        $query = "SELECT id, value
                  FROM t_transactions
                  WHERE activity_id IN (10)
                  {$accounts}
                  {$between}
                  {$q}";
                  
        $r = $adb->pquery($query, array());
        foreach($r AS $k => $v)
            $remove[] = $v['id'];
            
        $remove = SeparateArrayWithCommas($remove);

        $query = "SELECT *
                  FROM t_transactions
                  WHERE activity_id IN(10, 50)
                  {$accounts}
                  {$between}
                  AND value > 0
                  AND id NOT IN ({$remove})";
        return $result;*/
    }
    
    /**
     * Get all withdrawals.  This returns an array without filtering transfers
     * @global type $adb
     * @param type $accounts
     * @param type $sdate
     * @param type $edate
     * @return type
     */
    public function GetWithdrawals($accounts=null, $sdate=null, $edate=null)
    {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        if($accounts)
            $accounts = " AND account_number IN ({$accounts}) ";

        if($sdate && $edate)
            $between = " AND trade_date BETWEEN '{$sdate}' AND '{$edate}' ";
            
        $query = "SELECT *
                  FROM t_transactions_{$current_user->get('id')}
                  WHERE activity_id IN(10, 120)
                  {$accounts}
                  {$between}
                  AND value < 0";
                  
        $result = $adb->pquery($query, array());
        
        $withdrawals = array();
        if($adb->num_rows($result)){
            while($v = $adb->fetchByAssoc($result))
                $withdrawals[] = $v;
        }
        
        return $withdrawals;
    }
    
    /**
     * This is a pass by reference function. Contributions and withdrawals will filter themselves out if there is a transaction amount of the same value on the same day
     * between the two accounts.  IE:  contributions has 35,000 on Sept. 4th 2013, withdrawals has -35,000 on Sept. 4th 2013 .... These will both be removed from the array
     * @param string $contributions
     * @param type $withdrawals
     */
    public function FilterTransfers(&$contributions, &$withdrawals)
    {
        foreach($contributions AS $a => $b)
            foreach($withdrawals AS $k => $v)
            {
                if($b['trade_date'] == $v['trade_date'])
                    if($b['value'] == $v['value']*-1)
                    {
                        $contributions[$a] = null;
                        $withdrawals[$k] = null;
                    }
            }
    }
    
    /**
     * Get all dividends
     * @global type $adb
     * @param type $accounts
     * @param type $sdate
     * @param type $edate
     * @return type
     */
    public function GetDividends($accounts=null, $sdate=null, $edate=null)
    {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        if($accounts)
            $accounts = " AND account_number IN ({$accounts}) ";

        if($sdate && $edate)
            $between = " AND trade_date BETWEEN '{$sdate}' AND '{$edate}' ";
            
        $query = "SELECT *
                  FROM t_transactions_{$current_user->get('id')}
                  WHERE report_as_type_id IN(30, 280, 290, 300)
                  {$accounts}
                  {$between}
                  AND value <> 0";
                  
        $result = $adb->pquery($query, array());
        
        $dividends = array();
        if($result)
            foreach($result AS $k => $v)
                $dividends[] = $v;
        
        return $dividends;
    }
    
    /**
     * Get all interest
     * @global type $adb
     * @param type $accounts
     * @param type $sdate
     * @param type $edate
     * @return type
     */
    public function GetInterest($accounts=null, $sdate=null, $edate=null)
    {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        if($accounts)
            $accounts = " AND account_number IN ({$accounts}) ";

        if($sdate && $edate)
            $between = " AND trade_date BETWEEN '{$sdate}' AND '{$edate}' ";
            
        $query = "SELECT *
                  FROM t_transactions_{$current_user->get('id')}
                  WHERE report_as_type_id IN(20)
                  {$accounts}
                  {$between}
                  AND value > 0";
                  
        $result = $adb->pquery($query, array());
        
        $interest = array();
        if($result)
            foreach($result AS $k => $v)
                $interest[] = $v;
        
        return $interest;
    }
    
    /**
     * This returns all activities considered Income.  Dividends/Interest/Etc
     * @global type $adb
     * @param type $accounts
     * @param type $sdate
     * @param type $edate
     * @return type
     */
    public function GetIncome($accounts=null, $sdate=null, $edate=null)
    {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        if($accounts)
            $accounts = " AND account_number IN ({$accounts}) ";

        if($sdate && $edate)
            $between = " AND trade_date BETWEEN '{$sdate}' AND '{$edate}' ";
            
        $query = "SELECT *
                  FROM t_transactions_{$current_user->get('id')}
                  WHERE activity_id IN(90, 165)
                  {$accounts}
                  {$between}
                  AND value > 0";
                  
        $result = $adb->pquery($query, array());
        
        $income = array();
        if($result)
            foreach($result AS $k => $v)
                $income[] = $v;
        
        return $income;
    }
    
    public function GetManagementFees($accounts=null, $sdate=null, $edate=null)
    {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        if($accounts)
            $accounts = " AND account_number IN ({$accounts}) ";

        if($sdate && $edate)
            $between = " AND trade_date BETWEEN '{$sdate}' AND '{$edate}' ";
            
        $query = "SELECT *
                  FROM t_transactions_{$current_user->get('id')}
                  WHERE report_as_type_id IN(60)
                  {$accounts}
                  {$between}";
                  
        $result = $adb->pquery($query, array());
        
        $management = array();
        if($result)
            foreach($result AS $k => $v)
                $management[] = $v;
        
        return $management;
    }

    /**
     * Return summed management fees
     * @global type $adb
     * @param type $accounts
     * @param type $sdate
     * @param type $edate
     * @return type
     */
    public function GetSummedManagementFees($accounts=null, $sdate=null, $edate=null)
    {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        if($accounts)
            $accounts = " AND account_number IN ({$accounts}) ";

        if($sdate && $edate)
            $between = " AND trade_date BETWEEN '{$sdate}' AND '{$edate}' ";
            
        $query = "SELECT SUM(t.cost_basis_adjustment) AS management_fee
                  FROM t_transactions_{$current_user->get('id')} t
                  WHERE report_as_type_id IN(60)
                  {$accounts}
                  {$between}";
                  
        $result = $adb->pquery($query, array());
        
        if($adb->num_rows($result) > 0)
            $management = $adb->query_result($result, 0, 'management_fee');
        
        return $management;
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
    
    /**
     * This returns all expenses.  Management Fees/etc
     * @global type $adb
     * @param type $accounts
     * @param type $sdate
     * @param type $edate
     * @return type
     */
    public function GetExpenses($accounts=null, $sdate=null, $edate=null)
    {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        if($accounts)
            $accounts = " AND account_number IN ({$accounts}) ";

        if($sdate && $edate)
            $between = " AND trade_date BETWEEN '{$sdate}' AND '{$edate}' ";
            
        $query = "SELECT *
                  FROM t_transactions_{$current_user->get('id')}
                  WHERE activity_id IN(160)
                  {$accounts}
                  {$between}
                  AND value < 0";
                  
        $result = $adb->pquery($query, array());
        
        $expenses = array();
        if($result)
            foreach($result AS $k => $v)
                $expenses[] = $v;
        
        return $expenses;
    }
    
    public function __destruct() {
        
    }       
}

?>

<?php

class cPDFDBAccess{
    public function __construct() {
        
    }
    
    public function GetNonZeroAccountsNotX($account){
        if(count($account) > 1){//If there is more than one account, we need to combine it
            global $adb;
            $questions = generateQuestionMarks($account);//This insures we always use the first account number in the list regardless of its order
            $query = "SELECT account_number "
                    . "FROM vtiger_portfolioinformation "
                    . "WHERE account_number IN ({$questions}) "
                    . "AND total_value != 0 "
                    . "AND total_value is not null "
                    . "ORDER BY portfolioinformationid ASC";

            $result = $adb->pquery($query, array($account));
            if($adb->num_rows($result) > 0)
                foreach($result AS $k => $v){
                    $valid_accounts[] = $v['account_number'];
                }
        }
        else{
            if(is_array($account))
                $valid_accounts[] = substr_replace($account[0], str_repeat('x', strlen($account[0]) - 4), 0, -4);
            else
                $valid_accounts[] = substr_replace($account, str_repeat('x', strlen($account) - 4), 0, -4);;
        }

        return $valid_accounts;
    }
    
    /**
     * Get accounts that don't have a 0 dollar balance and aren't null
     * @global type $adb
     * @param type $account
     * @return type
     */
    public function GetNonZeroAccounts($account){
        if(count($account) > 1){//If there is more than one account, we need to combine it
            global $adb;
            $questions = generateQuestionMarks($account);//This insures we always use the first account number in the list regardless of its order
            $query = "SELECT lpad(right(account_number,4),length(account_number),'x') AS account_number "
                    . "FROM vtiger_portfolioinformation "
                    . "WHERE account_number IN ({$questions}) "
                    . "AND total_value != 0 "
                    . "AND total_value is not null "
                    . "ORDER BY portfolioinformationid ASC";

            $result = $adb->pquery($query, array($account));
            if($adb->num_rows($result) > 0)
                foreach($result AS $k => $v){
                    $valid_accounts[] = $v['account_number'];
                }
        }
        else{
            if(is_array($account))
                $valid_accounts[] = substr_replace($account[0], str_repeat('x', strlen($account[0]) - 4), 0, -4);
            else
                $valid_accounts[] = substr_replace($account, str_repeat('x', strlen($account) - 4), 0, -4);;
        }

        return $valid_accounts;
    }
    
    /**
     * DetermineAccount determines what account name should be used in the database.  If multiple accounts are used, it uses the one with the lowest portfolioinformationid just in case
     * some time in the future a 'lower' account number comes in, it won't mess with things.
     * @global type $adb
     * @param type $account
     * @return type
     */
    public function DetermineAccount($account){
        if(count($account) > 1){//If there is more than one account, we need to combine it
            global $adb;
            foreach($account AS $k => $v){
                $account[$k] = str_replace('-', '', $account[$k]);
            }
            $questions = generateQuestionMarks($account);//This insures we always use the first account number in the list regardless of its order
            $query = "SELECT account_number FROM vtiger_portfolioinformation WHERE account_number IN ({$questions}) ORDER BY portfolioinformationid ASC";
            $result = $adb->pquery($query, array($account));
            if($adb->num_rows($result) > 0)
                $account = "combined_" . $adb->query_result($result, 0, "account_number");
        }
        else{
            if(is_array($account))
                $account = $account[0];
            else
                $account = $account;
        }
      
        return $account;
    }
    
    /**
     * Read from the TWR table
     * @global type $adb
     * @param type $accounts
     * @param type $type
     * @return type
     */
    public function ReadTWR($accounts, $type){
        global $adb;
        
        $accounts = SeparateArrayWithCommasAndSingleQuotes($accounts);
        $query = "SELECT type, trailing_3, trailing_12, year_to_date, inception
                  FROM account_twr_pdf
                  WHERE account_number IN ({$accounts})
                  AND type = ?";
        
        $result = $adb->pquery($query, array($type));
        return $result;
    }
    
    /**
     * Read the performance table
     * @global type $adb
     * @param type $accounts
     * @return type
     */
    public function ReadPerformance($accounts){
        global $adb;
        
        $accounts = SeparateArrayWithCommasAndSingleQuotes($accounts);
        $query = "SELECT account_number, beginning_value, net_contributions, capital_appreciation, income, expenses, 
                  ending_value, investment_return, DATE_FORMAT(start_date, '%m/%d/%Y') AS start_date, 
                  DATE_FORMAT(end_date, '%m/%d/%Y') AS end_date, serialized_accounts, goal, management_total, 
                  other_expenses, accounts_used
                  FROM account_performance_pdf
                  WHERE account_number IN ({$accounts})";
                    
         $result = $adb->pquery($query, array());
         return $result;
    }
    
    /**
     * Write to the TWR table. This also works for S&P 500
     * @global type $adb
     * @param type $data
     */
    public function WriteTWR($data){
        global $adb;
        $account = $this->DetermineAccount($data['account_number']);
        $query = "INSERT INTO account_twr_pdf (type, account_number, trailing_3, trailing_12, year_to_date, inception, last_update)
                                              VALUES (?, ?, ?, ?, ?, ?, NOW())
                  ON DUPLICATE KEY UPDATE trailing_3=VALUES(trailing_3), trailing_12=VALUES(trailing_12), year_to_date=VALUES(year_to_date), inception=VALUES(inception), last_update=NOW()";
        
        $adb->pquery($query, array($data['type'],
                                   $account,
                                   $data['trailing_3'],
                                   $data['trailing_12'],
                                   $data['year_to_date'],
                                   $data['inception']));
    }
    
    /**
     * Write the performance table
     * @global type $adb
     * @param type $data
     */
    public function WritePerformance($data){
        global $adb;
        $query = "INSERT INTO account_performance_pdf (account_number, beginning_value, net_contributions, capital_appreciation, income, expenses, ending_value, investment_return, start_date, end_date, last_update, serialized_accounts, goal, management_total, other_expenses, accounts_used)
                                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE beginning_value=VALUES(beginning_value), net_contributions=VALUES(net_contributions), capital_appreciation=VALUES(capital_appreciation), income=VALUES(income), 
                  expenses=VALUES(expenses), ending_value=VALUES(ending_value), investment_return=VALUES(investment_return), start_date=VALUES(start_date), end_date=VALUES(end_date), last_update=NOW(), serialized_accounts=VALUES(serialized_accounts), goal=VALUES(goal), management_total=VALUES(management_total), other_expenses=VALUES(other_expenses), accounts_used=VALUES(accounts_used)";

        $account = $this->DetermineAccount($data['account_number']);
        $valid_accounts = $this->GetNonZeroAccounts($data['account_number']);
        $non_x = $this->GetNonZeroAccountsNotX($data['account_number']);
        $serialized = serialize($valid_accounts);
        $accounts_used = serialize($non_x);

        $adb->pquery($query, array($account,
                                   $data['start_value'],
                                   $data['net_contributions'],
                                   $data['capital_appreciation'],
                                   $data['income'],
                                   $data['expenses'],
                                   $data['end_value'],
                                   $data['investment_return'],
                                   $data['start_date'],
                                   $data['end_date'],
                                   $serialized,
                                   $data['goal'],
                                   $data['management_total'],
                                   $data['other_expenses'],
                                   $accounts_used));
/*INSERT INTO account_monthly_income_pdf (account_number, symbol, category, description, frequency, month, year, amount, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE description=?, category=?, frequency=?, amount=?, date=?*/        
    }
    
    /**
     * Read the monthly projected report
     * @global type $adb
     * @param type $accounts
     * @param type $start
     * @param type $end
     * @return type
     */
    public function ReadMonthlyProjected($accounts, $start, $end){
        global $adb;
        $accounts = SeparateArrayWithCommasAndSingleQuotes($accounts);

        $query = "SELECT * FROM(
                    SELECT IFNULL(symbol, 'total_month') as symbol, category, description, frequency, month, year, SUM(monthly_total) AS monthly_total, symbol_total, date, last_update, sub_sub_category
                    FROM(
                            (SELECT symbol, category, description, frequency, month, year, SUM(amount) AS monthly_total, 
                                            (SELECT SUM(amount)
                                            FROM account_monthly_income_pdf b
                                            WHERE account_number IN ({$accounts}) 
                                                    AND (date between '{$start}' AND '{$end}') 
                                                    AND a.symbol = b.symbol
                                            GROUP BY symbol) AS symbol_total, date, last_update, sub_sub_category
                                    FROM account_monthly_income_pdf a
                                    WHERE account_number IN ({$accounts}) 
                                    GROUP BY month, year, symbol)
                            ) t 
                    WHERE month is not null AND (date between '{$start}' AND '{$end}')
                    GROUP BY year, month, symbol WITH ROLLUP
                    ) t 
                    WHERE month is not null
                    ORDER BY category, symbol, date";

         $result = $adb->pquery($query, array());
         return $result;
    }
    
    /**
     * Write to the monthly projected table (not specifically projected, as it also writes to history)
     * @global type $adb
     * @param type $data
     */
    public function WriteMonthlyProjected($data){
        global $adb;
        
        $t = $data['month'] . " 1, " . $data['year'];
        $d = date('Y-m-d', strtotime($t));

        $account = $this->DetermineAccount($data['account_number']);
        $tmp_holding = new cPholdingsInfo();
        $sub_sub_category = $tmp_holding->DetermineSubSubCategoryBySymbol($data['symbol']);
        $sub_category = ModSecurities_Module_Model::GetSubCategoryBySymbol($data['symbol']);
        $category = ModSecurities_Module_Model::GetCategoryBySymbol($data['symbol']);

        $account = str_replace('-', '', $account);
        $query = "INSERT INTO account_monthly_income_pdf (account_number, symbol, category, description, frequency, month, year, amount, date, last_update, sub_sub_category) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)
                  ON DUPLICATE KEY UPDATE description=VALUES(description), category=VALUES(category), frequency=VALUES(frequency), amount=VALUES(amount), date=(date), last_update=NOW(), sub_sub_category=VALUES(sub_sub_category)";

        $adb->pquery($query, array($account,
                                   $data['symbol'],
                                   $category, #$data['code_description'],
                                   $data['description'],
                                   $data['frequency'],
                                   $data['month'],
                                   $data['year'],
                                   $data['total'],
                                   $d,
                                   $sub_category));#$sub_sub_category));
    }
    
    /**
     * Read the "other account" info from the table with the sum values
     * @global type $adb
     * @param type $accounts
     * @return type
     */
    public function ReadOtherAccountsWithTotals($accounts, $and=null){
        global $adb;
        $accounts = SeparateArrayWithCommasAndSingleQuotes($accounts);
        $query = "SELECT t1.primary_account, t1.account_number, t1.total_value, t1.market_value, t1.cash_value, DATE_FORMAT(t1.last_update,'%m/%d/%Y') AS last_update,
                         SUM(t2.total_value) AS total_value_sum, SUM(t2.market_value) AS market_value_sum, SUM(t2.cash_value) AS cash_value_sum, ac.nickname
                  FROM account_other_accounts_pdf t1
                  LEFT JOIN vtiger_pc_account_custom ac ON ac.account_number = t1.account_number,
                         (SELECT total_value, market_value, cash_value 
                          FROM account_other_accounts_pdf 
                          WHERE primary_account IN ({$accounts})
                          AND primary_account = account_number
                          GROUP BY account_number) t2
                  WHERE primary_account IN ({$accounts})
                  {$and}
                  GROUP BY account_number";
                  
        $result = $adb->pquery($query, array());
        return $result;
    }
    
    /**
     * Read the "other account" info from the table
     * @global type $adb
     * @param type $accounts
     * @return type
     */
    public function ReadOtherAccounts($accounts){
        global $adb;
        $accounts = SeparateArrayWithCommasAndSingleQuotes($accounts);
        $query = "SELECT * FROM account_other_accounts_pdf WHERE primary_account IN ({$accounts})";
        $result = $adb->pquery($query, array());
        
        return $result;
    }
    
    /**
     * Write the "Other Accounts in Portfolio" table
     * @global type $adb
     * @param type $primary_account
     * @param type $accounts
     */
    public function WriteOtherAccounts($primary_account, $accounts){
        global $adb;
        foreach($accounts AS $k => $v)
        {
            $query = "INSERT INTO account_other_accounts_pdf (primary_account, account_number, total_value, market_value, cash_value, last_update)
                      VALUES (?, ?, ?, ?, ?, NOW())
                      ON DUPLICATE KEY UPDATE primary_account=?, account_number=?, total_value=?, market_value=?, cash_value=?, last_update=NOW()";
            $adb->pquery($query, array($primary_account, $v['number'], $v['total'], $v['market_value'], $v['cash_value'],
                                       $primary_account, $v['number'], $v['total'], $v['market_value'], $v['cash_value']));
        }
    }
    
    /**
     * Write to the positions table
     * @param type $account_number
     * @param type $positions
     */    
    public function WritePositions($positions){
        global $adb;
        
        $accounts = array();
        foreach($positions AS $k => $v){
            $accounts[] = $v['account_number'];
        }
        
        $questions = generateQuestionMarks($accounts);
        $query = "DELETE FROM account_positions_pdf WHERE account_number IN ({$questions})";
        $adb->pquery($query, array($accounts));
        
        foreach($positions AS $k => $v)
        {
            $query = "INSERT INTO account_positions_pdf (account_number, security_symbol, description, security_type, asset_class, quantity, 
                                                         last_price, current_value, percent, cost_basis, gain_loss, gain_loss_percent, last_update, sub_sub_category)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)
                      ON DUPLICATE KEY UPDATE account_number=?, security_symbol=?, description=?, security_type=?, asset_class=?, quantity=?, 
                                              last_price=?, current_value=?, percent=?, cost_basis=?, gain_loss=?, gain_loss_percent=?, last_update=NOW(), sub_sub_category=?";

            $adb->pquery($query, array($v['account_number'], $v['security_symbol'], $v['description'], '', $v['code_description'], $v['quantity'], $v['latest_price'], 
                                       $v['latest_value'], $v['weight'], $v['cost_basis_adjustment'], $v['ugl'], $v['gl'], $v['sub_sub_category'],
                                       $v['account_number'], $v['security_symbol'], $v['description'], '', $v['code_description'], $v['quantity'], $v['latest_price'],
                                       $v['latest_value'], $v['weight'], $v['cost_basis_adjustment'], $v['ugl'], $v['gl'], $v['sub_sub_category']));
        }
    }
    
    /**
     * Read all position information.. Also adds subtotals/grand total
     * @global type $adb
     * @param type $accounts
     * @return type
     */
    public function ReadPositions($accounts){
        global $adb;
        $accounts = SeparateArrayWithCommasAndSingleQuotes($accounts);
        $query = "SELECT *,
                  IFNULL(asset_class, 1) as grand_total,
                  IFNULL(security_symbol, 1) AS sub_total,
                  SUM(current_value) AS current_value_sum, SUM(cost_basis) AS cost_basis_sum, SUM(gain_loss) AS gain_loss_sum, 
                  SUM(gain_loss_percent) AS gain_loss_percent_sum, SUM(percent) AS percent_sum,
                  lpad(right(account_number,4),length(account_number),'x') AS account_number_hash
                  FROM account_positions_pdf 
                  WHERE account_number IN ({$accounts})
                  GROUP BY asset_class, security_symbol, account_number";
        $result = $adb->pquery($query, array());
        return $result;
    }
    
    /**
     * Write to the investment returns table. $return_column is in array form $k = irrelivant, $v['start_value'], $v['net_contributions'], etc...
     * @param type $account_number
     * @param type $return_column
     */
    public function WriteAccountInvestmentReturns($account_number, $return_column){
        $adb = PearDatabase::getInstance();
        $v = $return_column;//using $v just because it saves a bunch of typing below
        $date = $v['start_date'] . " to " . $v['end_date'];
        $account = $this->DetermineAccount($account_number);

        $query = "INSERT INTO account_investment_returns (beginning_value, net_contributions, short_values, net_income, capital_appreciation, ending_value, other_net_contributions,
                                                          other_withdrawals, other_income, other_investment_return, account, type_column, date_to, management_fee, other_expenses, expenses)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE beginning_value=VALUES(beginning_value), net_contributions=VALUES(net_contributions), short_values=VALUES(short_values), net_income=VALUES(net_income),
                                          capital_appreciation=VALUES(capital_appreciation), ending_value=VALUES(ending_value), other_net_contributions=VALUES(other_net_contributions),
                                          other_withdrawals=VALUES(other_withdrawals), other_income=VALUES(other_income), other_investment_return=VALUES(other_investment_return),
                                          account=VALUES(account), type_column=VALUES(type_column), date_to=VALUES(date_to), management_fee=VALUES(management_fee),
                                          other_expenses=VALUES(other_expenses), expenses=VALUES(expenses)";
        $adb->pquery($query, array($v['start_value'], $v['net_contributions'], $v['shorts'], $v['net_income'], $v['capital_appreciation'], $v['end_value'], $v['contributions'],
                                   $v['withdrawals'], $v['income'], $v['investment_return'], $account, $v['column_name'], $date, $v['management_fee'], $v['other_expenses'], $v['expenses']));
    }
    
    /**
     * Read everything from investment return table
     * @global type $adb
     * @param type $account_number
     * @return type
     */
    public function ReadAccountInvestmentReturns($account_number, $type_column=null){
        global $adb;
        $accounts = SeparateArrayWithCommasAndSingleQuotes($account_number);
        if(strlen($type_column) > 0)
            $and = " AND type_column = '{$type_column}'";
        $query = "SELECT * FROM account_investment_returns WHERE account IN ({$accounts}) {$and}";        
        $result = $adb->pquery($query, array());
        $coutn = 0;
        if($adb->num_rows($result) > 1)
            $rows[] = $adb->fetch_array($result);
        else
            $rows = $adb->fetch_array($result);
        return $rows;
    }
    
    /**
     * Write the account history bar graph
     * @param type $account_number
     * @param type $history
     */
    public function WriteAccountHistory($account_number, $history){
        global $adb;
        $account = $this->DetermineAccount($account_number);
        foreach($history AS $k => $v){
            $query = "INSERT INTO account_value_history (account, date, month, value)
                      VALUES (?, ?, ?, ?)
                      ON DUPLICATE KEY UPDATE date=VALUES(date), month=VALUES(month), value=VALUES(value)";
            $adb->pquery($query, array($account, $v['date_time'], $v['date'], $v['value']));//The 3rd value is actually the month
        }
    }
    
    /**
     * Read the account history table
     * @global type $adb
     * @param type $account_number
     * @return type
     */
    public function ReadAccountHistory($account_number){
        global $adb;
        $accounts = SeparateArrayWithCommasAndSingleQuotes($account_number);
        $query = "SELECT * FROM account_value_history WHERE account IN ({$accounts})";
        $result = $adb->pquery($query, array());
        return $result;
    }
    /**
     * Get the positions from the positions pdf summary table
     * @global type $adb
     * @param type $accounts
     * @return type
     */
/*    public function ReadPositions($accounts){
        global $adb;
        $accounts = SeparateArrayWithCommasAndSingleQuotes($accounts);
        $query = "SELECT * FROM account_positions_pdf WHERE account_number IN ({$accounts})";
        $result = $adb->pquery($query, array());
        return $result;
    }
    
    /**
     * Insert the pie information for the given account number
     * @global type $adb
     * @param type $account_number
     * @param type $pie
     */
    public function WritePie($account_number, $pie){
        global $adb;
        $account = $this->DetermineAccount($account_number);
        $query = "DELETE FROM account_pie_pdf WHERE account_number = ?";
        $adb->pquery($query, array($account));
        foreach($pie AS $a => $b)
            foreach($b AS $k => $v)
            {
                $query = "INSERT INTO account_pie_pdf (account_number, title, value, last_update)
                          VALUES (?, ?, ?, NOW())
                          ON DUPLICATE KEY UPDATE title=VALUES(title), value=VALUES(value), last_update=NOW()";
                $adb->pquery($query, array($account, $k, $v));
            }
    }
    
    public function ReadRevenue($accounts){
        return PortfolioInformation_HistoricalInformation_Model::GetTrailing12Revenue($accounts);
    }
    
    public function ReadAUM($accounts){
        return PortfolioInformation_HistoricalInformation_Model::GetTrailing12AUM($accounts);
    }
    
    /**
     * Get the pie chart data for the given account number
     * @global type $adb
     * @param type $account_number
     * @return type
     */
    public function ReadPie($accounts){
        global $adb;
        $accounts = SeparateArrayWithCommasAndSingleQuotes($accounts);
        $query = "SELECT * FROM account_pie_pdf WHERE account_number IN ({$accounts})";
        $result = $adb->pquery($query, array());
        return $result;
    }
    
    /**
     * Write to the account_details_pdf table
     * @global type $adb
     * @param type $details
     */
    public function WriteDetails($details){
        global $adb;
        
        $query = "INSERT INTO account_details_pdf (account_number, account_name, master_account, custodian, account_type, 
                                                   management_fee, market_value, cash_value, annual_management_fee, total_value, last_update)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                  ON DUPLICATE KEY UPDATE account_name=?, master_account=?, custodian=?, account_type=?, management_fee=?, 
                                          market_value=?, cash_value=?, annual_management_fee=?, total_value=?, last_update=NOW()";
        
        $adb->pquery($query, array($details['number'], $details['name'], $details['master_account'], $details['custodian'], $details['type'],
                                   $details['management_fee'], $details['market_value'], $details['cash_value'], $details['annual_fee'], $details['total'],
                                   $details['name'], $details['master_account'], $details['custodian'], $details['type'], $details['management_fee'],
                                   $details['market_value'], $details['cash_value'], $details['annual_fee'], $details['total']));
    }
    
    /**
     * Get the details from the account_details_pdf table
     * @global type $adb
     * @param type $accounts
     * @return type
     */
    public function ReadDetails($accounts){
        global $adb;
        $accounts = SeparateArrayWithCommasAndSingleQuotes($accounts);
        $query = "SELECT * FROM account_details_pdf 
                  WHERE account_number IN ({$accounts})";
        
        $result = $adb->pquery($query, array());
        
        return $result;
    }
    
    /**
     * Get the last update for the given table
     * @global type $adb
     * @param type $account
     * @param type $table
     * @return type
     */
    public function ReadLastUpdate($account, $table){
        global $adb;
        $query = "SELECT DATE_FORMAT(last_update,'%m/%d/%Y') AS last_update 
                  FROM {$table} 
                  WHERE account_number = ?";
        $result = $adb->pquery($query, array($account));
        if($adb->num_rows($result) > 0)
            foreach($result AS $k => $v)
                return $v['last_update'];
        else
            return 0;
    }

    /**
     * Get the individual category titles for the accounts
     * @param type $accounts
     */
    public function GetAccountTotals($accounts){
        global $adb;
        $accounts = SeparateArrayWithCommasAndSingleQuotes($accounts);
        $query = "SELECT account_number, SUM(current_value) AS total_value, 
                  SUM(cost_basis) AS cost_basis, 
                  SUM(gain_loss) AS ugl, 
                  SUM(gain_loss_percent) AS gl, 
                  SUM(current_value) / (SELECT SUM(current_value) 
                                                          FROM account_positions_pdf 
                                                          WHERE account_number IN ({$accounts}))*100 AS weight,
                  lpad(right(account_number,4),length(account_number),'x') AS account_number_hash
                  FROM account_positions_pdf p
                  WHERE account_number IN ({$accounts}) 
                  GROUP BY account_number ASC";
        $result = $adb->pquery($query, array());
        $accounts = array();
        foreach($result AS $k => $v){
            $accounts[$v['account_number']] = $v;
        }
        return $accounts;
    }
    
    /**
     * Get the main category titles for the accounts
     * @param type $accounts
     */
    public function GetSimpleCategories($accounts){
        global $adb;
        $accounts = SeparateArrayWithCommasAndSingleQuotes($accounts);
        $query = "SELECT asset_class, account_number, SUM(current_value) AS total_value,
                  SUM(cost_basis) AS cost_basis,
                  SUM(gain_loss) AS ugl,
                  SUM(gain_loss_percent) AS gl,
                  SUM(current_value) / (SELECT SUM(current_value)
                    FROM account_positions_pdf 
                    WHERE account_number IN ({$accounts}))*100 AS weight
                  FROM account_positions_pdf
                  WHERE account_number IN ({$accounts})
                  GROUP BY asset_class, account_number ASC";
        $result = $adb->pquery($query, array());
        $category = array();
        foreach($result AS $k => $v){
            $category[] = $v;
        }
        return $category;
    }
    
    /**
     * Get the sub category titles for the accounts
     * @param type $accounts
     */
    public function GetSimpleSubCategories($accounts){
        global $adb;
        $accounts = SeparateArrayWithCommasAndSingleQuotes($accounts);
        $query = "SELECT p.sub_sub_category, account_number, asset_class, SUM(current_value) AS total_value, 
                    SUM(cost_basis) AS cost_basis, 
                    SUM(gain_loss) AS ugl, 
                    (gain_loss_percent) AS gl, 
                    SUM(current_value) / (SELECT SUM(current_value) 
                                                              FROM account_positions_pdf 
                                                              WHERE account_number IN ({$accounts}))*100 AS weight 
                    FROM account_positions_pdf p
                    WHERE account_number IN ({$accounts}) 
                    GROUP BY asset_class, sub_sub_category, account_number ASC";

        $result = $adb->pquery($query, array());
        $category = array();
        foreach($result AS $k => $v){
            $category[] = $v;
        }
        
        return $category;
    }
    
    /**
     * Get the entire sorted structure of the positions table
     * @param type $accounts
     */
    public function GetPositionsSorted($accounts){
        global $adb;
        $accounts = SeparateArrayWithCommasAndSingleQuotes($accounts);
        $query = "SELECT * "
                . "FROM account_positions_pdf "
                . "WHERE account_number IN ({$accounts})
                   GROUP BY account_number, asset_class, sub_sub_category, security_symbol ASC";

        $result = $adb->pquery($query, array());
        $category = array();
        foreach($result AS $k => $v){
            $category[] = $v;
        }
        return $category;
    }
    
    /**
     * Get the main position categories
     * @param type $accounts
     */
    public function GetMainPositionCategories($accounts){
        global $adb;
        $accounts = SeparateArrayWithCommasAndSingleQuotes($accounts);
        $query = "SELECT asset_class,
                  SUM(current_value) AS total_value, 
                  SUM(cost_basis) AS cost_basis, 
                  SUM(current_value) - SUM(cost_basis) as gain_loss, 
                  (SUM(current_value) - SUM(cost_basis))/SUM(cost_basis)*100 AS gain_loss_percent,
                  SUM(current_value) / 
                    (SELECT SUM(current_value) FROM account_positions_pdf WHERE account_number IN ({$accounts})) * 100 AS weight
                  FROM account_positions_pdf
                  WHERE account_number IN ({$accounts})
                  GROUP BY asset_class ASC";

        $result = $adb->pquery($query, array());
        $category = array();
        foreach($result AS $k => $v){
            $category[$v['asset_class']] = $v;
        }
        return $category;
    }
    
    /**
     * Get the sub sub categories of the positions
     * @param type $accounts
     */
    public function GetPositionSubSubCategories($accounts){
        global $adb;
        $accounts = SeparateArrayWithCommasAndSingleQuotes($accounts);
        $query =  "SELECT asset_class, sub_sub_category,
                  SUM(current_value) AS total_value, 
                  SUM(cost_basis) AS cost_basis, 
                  SUM(current_value) - SUM(cost_basis) as gain_loss, 
                  (SUM(current_value) - SUM(cost_basis))/SUM(cost_basis)*100 AS gain_loss_percent,
                  SUM(current_value) / 
                    (SELECT SUM(current_value) FROM account_positions_pdf WHERE account_number IN ({$accounts})) * 100 AS weight
                  FROM account_positions_pdf
                  WHERE account_number IN ({$accounts})
                  GROUP BY sub_sub_category";
        $result = $adb->pquery($query, array());
        $category = array();
        foreach($result AS $k => $v){
            $category[$v['sub_sub_category']] = $v;
        }
        return $category;
    }
    
    /**
     * Read the positions grand total amount
     * @global type $adb
     * @param type $account_numbers
     * @return type
     */
    public function GetPositionsGrandTotal($accounts){
        global $adb;
        $accounts = SeparateArrayWithCommasAndSingleQuotes($accounts);
        $query = "SELECT SUM(current_value) AS total_value, SUM(cost_basis) AS cost_basis, SUM(gain_loss) AS ugl, 
                  SUM(gain_loss)/SUM(current_value)*100 AS ugl_percent, SUM(percent) AS weight
                  FROM account_positions_pdf 
                  WHERE account_number IN ({$accounts})";
        $result = $adb->pquery($query, array());
        $totals = array();
        foreach($result AS $k => $v){
            $totals = $v;
        }
        return $totals;
    }
    
    /**
     * Read transactions from pdf table
     * @global type $adb
     * @param type $accounts
     * @return type
     */
    public function ReadTransactions($accounts){
        global $adb;
        $questions = generateQuestionMarks($accounts);
        
        $query = "SELECT trade_date, lpad(right(account_number,4),length(account_number),'x') AS account_number, 
                  activity, security_symbol, description, security_type, detail, quantity, price, amount
                  FROM account_transactions_pdf WHERE account_number IN ({$questions})";
        $result = $adb->pquery($query, array($accounts));
        $transactions = array();
        if($adb->num_rows($result) > 0)
            foreach($result AS $k => $v){
                $transactions[] = $v;
            }
            
        return $transactions;
    }
}

?>

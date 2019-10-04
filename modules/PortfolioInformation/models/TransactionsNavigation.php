<?php
require_once("include/utils/omniscientCustom.php");
require_once("libraries/reports/cTransactions.php");
require_once('libraries/reports/cPortfolioDetails.php');
require_once("libraries/reports/cPholdingsInfo.php");
require_once("libraries/reports/cReports.php");

class PortfolioInformation_TransactionsNavigation_Model extends Vtiger_Module {
    public $transaction_info = array();
    public $account_info = array();
    public $date, $account_number, $orderby, $direction, $num_pages, $next_page, $prev_page, $current_page, $searchcontent, $searchtype;
    public $search_list, $num_results, $pagination, $symbols, $security_types, $descriptions, $actions, $filter;
    public $actions_checked, $symbols_checked, $descriptions_checked, $security_types_checked, $date_range_checked;
    public $transaction_filter_actions_value_carried, $transaction_filter_symbols_value_carried, $transaction_filter_descriptions_value_carried;
    public $transaction_filter_security_types_value_carried, $transaction_filter_symbols_value, $transaction_filter_date_range_start_carried;
    public $transaction_filter_date_range_end_carried;
    
    public function GetAllTransactions(Vtiger_Request $request){
        global $adb;
        $questions = generateQuestionMarks($request->get('account_numbers'));
        $query = "SELECT t.* FROM vtiger_pc_transactions t
                  JOIN vtiger_portfolios p ON p.portfolio_id = t.portfolio_id
                  WHERE p.portfolio_account_number IN ({$questions})";

        $result = $adb->pquery($query, array($request->get('account_numbers')));
        $transactions = array();
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $transactions[] = $v;
            }
        }
        return $transactions;
    }
        
    /**
     * Get all activities
     * @global type $adb
     * @param Vtiger_Request $request
     * @return type
     */
    public function GetAllActivities(Vtiger_Request $request){
        global $adb;
        $questions = generateQuestionMarks($request->get('account_numbers'));
        
        $query = "SELECT a.activity_id, a.activity_name
                  FROM vtiger_pc_activities a
                  JOIN vtiger_pc_transactions t ON t.activity_id = a.activity_id
                  JOIN vtiger_portfolios p ON p.portfolio_id = t.portfolio_id
                  WHERE p.portfolio_account_number IN ({$questions})
                  GROUP BY a.activity_name ASC";
        $result = $adb->pquery($query, array($request->get('account_numbers')));
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $activities[$v['activity_id']] = $v['activity_name'];
            }
        }
        return $activities;
    }
    
    /**
     * Get all symbols
     * @global type $adb
     * @param Vtiger_Request $request
     * @return type
     */
    public function GetAllSymbols(Vtiger_Request $request){
        global $adb;
        $questions = generateQuestionMarks($request->get('account_numbers'));
        
        $query = "SELECT s.security_id, s.security_symbol
                  FROM vtiger_securities s
                  JOIN vtiger_pc_transactions t ON t.symbol_id = s.security_id
                  JOIN vtiger_portfolios p ON p.portfolio_id = t.portfolio_id
                  WHERE p.portfolio_account_number IN ({$questions})
                  GROUP BY s.security_symbol ASC";
        $result = $adb->pquery($query, array($request->get('account_numbers')));
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $securities[$v['security_id']] = $v['security_symbol'];
            }
        }
        return $securities;
    }
    
    /**
     * Get all symbols
     * @global type $adb
     * @param Vtiger_Request $request
     * @return type
     */
    public function GetAllSecurityTypes(Vtiger_Request $request){
        global $adb;
        $questions = generateQuestionMarks($request->get('account_numbers'));
        
        $query = "SELECT cd.code_id, cd.code_description 
                  FROM vtiger_securities s
                  JOIN vtiger_pc_transactions t ON t.symbol_id = s.security_id
                  JOIN vtiger_portfolios p ON p.portfolio_id = t.portfolio_id
                  JOIN vtiger_security_types st ON s.security_type_id = st.security_type_id
                  LEFT JOIN vtiger_pc_codes c ON c.code_id = 
                     (SELECT code_id FROM vtiger_pc_security_codes WHERE security_id = s.security_id AND code_type_id = 20)
                  LEFT JOIN vtiger_pc_security_codes sc ON sc.security_id = s.security_id
                  LEFT JOIN vtiger_pc_codes cd ON cd.code_id = c.code_id
                  LEFT JOIN vtiger_pc_codes cde ON cde.code_id = sc.code_id
                  WHERE p.portfolio_account_number IN ({$questions})
                  GROUP BY cd.code_description ASC";
                  
        $result = $adb->pquery($query, array($request->get('account_numbers')));
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $securities[$v['code_id']] = $v['code_description'];
            }
        }
        
        return $securities;
    }
    
    public function WriteFilteredTransactions(Vtiger_Request $request, $transactions){
        global $adb;

        $account_questions = generateQuestionMarks($request->get('account_numbers'));
        
        $query = "DELETE FROM account_transactions_pdf WHERE account_number IN ({$account_questions})";
        $adb->pquery($query, array($request->get('account_numbers')));

        $query = "INSERT INTO account_transactions_pdf (trade_date, account_number, activity, security_symbol,
                                                        description, security_type, detail, quantity, price, amount)
                  VALUES ";
        $extensions = "";
/*
            <td width="10%">{$v.trade_date}</td>
            <td>{$v.account_number}</td>
            <td>{$v.activity_name}</td>
            <td>{$v.report_as_type_name}</td>
            <td>{$v.security_symbol}</td>
            <td>{$v.description}</td>
            <td>{$v.code_description}</td>
            <td>{$v.transaction_description}</td>
            <td>{$v.quantity}</td>
            <td>${$v.current_price|number_format:2}</td>
            <td>${$v.value|number_format:2}</td>
 */        
        foreach($transactions AS $k => $v){
            $extensions .= "('{$v['trade_date']}', '{$v['account_number']}', '{$v['activity_name']}', '{$v['security_symbol']}',
                             '{$v['description']}', '{$v['code_description']}', '{$v['transaction_description']}',
                             '{$v['quantity']}', '{$v['current_price']}', '{$v['value']}')";
            if($k != count($transactions)-1)
                $extensions .= ",";
        }
        
        $query .= $extensions;
        $adb->pquery($query, array());
    }
    
    /**
     * Get the filtered transactions
     * @global type $adb
     * @param Vtiger_Request $request
     * @param type $special_instructions
     * @return string
     */
    public function GetFilteredTransactions(Vtiger_Request $request)
    {
        global $adb;
        $direction = "DESC";
        $order_by = "t.trade_date";
        
        /*This is an extra check in case we pass in direction but not order_by, or vice versa.  The array will come in as positive but not fill in anything for one
        of these two values*/
        if(!$direction)
            $direction = "ASC";
        if(!$order_by)
            $order_by = "t.trade_date";

        $activity_questions = generateQuestionMarks($request->get('selected_activities'));
        $security_types_questions = generateQuestionMarks($request->get('selected_security_types'));
        $symbols_questions = generateQuestionMarks($request->get('selected_symbols'));
        $start_date = $this->FormatDate($request->get('start_date'));
        $end_date = $this->FormatDate($request->get('end_date'));
        
        $and = " AND a.activity_id IN ({$activity_questions}) ";
        $and .= " AND cd.code_id IN ({$security_types_questions}) ";
        $and .= " AND t.symbol_id IN ({$symbols_questions}) ";
        $and .= " AND (t.trade_date BETWEEN '{$start_date}' AND '{$end_date}') ";
        
        $questions = generateQuestionMarks($request->get('account_numbers'));
        //selected_security_types, selected_activities, selected_symbols

        $query = "SELECT t.*, p.portfolio_account_number, DATE_FORMAT(t.trade_date,'%m-%d-%Y') AS trade_date_display, pr.*, a.activity_name, p.portfolio_account_number AS AccountNumber, s.security_id, s.security_symbol, s.security_type_id, cde.code_name, st.security_type_name,
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
                  LEFT JOIN vtiger_pc_security_prices pr ON pr.security_price_id = (SELECT security_price_id FROM vtiger_pc_security_prices  
                    WHERE security_id = s.security_id AND price_date = t.trade_date LIMIT 1)
                  LEFT JOIN vtiger_security_types st ON st.security_type_id = s.security_type_id
                  WHERE p.portfolio_account_number IN ({$questions})
                  {$and}
                  AND status_type_id = 100
                  AND t.quantity <> 0
                  {$filter}
                  GROUP BY transaction_id
                  ORDER BY {$order_by} {$direction}";//Only care about "posted" transactions, so status_type_id = 100                 

        $result = $adb->pquery($query, array($request->get('account_numbers'), $request->get('selected_activities'),
                                             $request->get('selected_security_types'), $request->get('selected_symbols')));
        $transactions = array();
        $total = 0;
        $symbols = array();
        $descriptions = array();
        $actions = array();
        $security_types = array();

        if($adb->num_rows($result) > 0)
        foreach($result AS $k => $v)
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

            $value = $price * $v['quantity'];

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
                                    "code_id" => $v['code_id'],
                                    "code_description" => $v['code_description'],
                                    "sub_sub_category" => $v['sub_sub_category'],
                                    "security_type_id" => $v['security_type_id'],
                                    "activity_name" => $v['activity_name'],
                                    "transaction_description" => $v['notes'],
                                    "current_price" => $price,
                                    "origination" => $v['interface_name'],
                                    "report_as_type_name" => $v['report_as_type_name'],
                                    "security_factor" => $v['security_factor'],
                                    "account_number" => $v['portfolio_account_number'],
                                    "value" => $value);
        }

        $this->WriteFilteredTransactions($request, $transactions);
        return $transactions;
    }
    
    /**
     * Get the estimated security price.  It takes the date and finds if the security exists on or before that date.  If it doesn't, it then checks if it exists
     * on or after the date given.
     */
    public function GetEstimateSecurityPrice($symbol_id, $date)
    {
        $price = $this->GetSecurityPriceAsOfDate($symbol_id, $date);
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
     * Get the earliest and latest trade dates for the given accounts
     * @global type $adb
     * @param Vtiger_Request $request
     * @return type
     */
    public function GetTradeDates(Vtiger_Request $request){
        global $adb;
        $questions = generateQuestionMarks($request->get('account_numbers'));
        
        $query = "SELECT DATE_FORMAT(MIN(t.trade_date), '%m/%d/%Y') AS inception_date, 
                         DATE_FORMAT(MAX(t.trade_date), '%m/%d/%Y') AS last_trade_date
                  FROM vtiger_pc_transactions t
                  JOIN vtiger_portfolios p ON p.portfolio_id = t.portfolio_id
                  WHERE p.portfolio_account_number IN ({$questions})";
                  
        $result = $adb->pquery($query, array($request->get('account_numbers')));
        if($adb->num_rows($result) > 0){
            $dates = array('inception_date'=>$adb->query_result($result, 0, 'inception_date'),
                           'last_trade_date'=>$adb->query_result($result, 0, 'last_trade_date'));
            }
        
        return $dates;
    }
    
    /**
     * Format the date so it goes in as y-m-d
     * @param type $date
     * @return type
     */
    public function FormatDate($date) {
        //date = 07/12/13
        $date = explode('/', $date);
        
        //for some reason in ubuntu month had a space had to get last 2 characters
        $month = substr($date[0], -2);
        $day = trim($date[1]);
        $year = $date[2];

        return $year . "-" . $month . "-" . $day;
    }
}
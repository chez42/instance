<?php

require_once("include/utils/omniscientCustom.php");
require_once("libraries/reports/cTransactions.php");
require_once('libraries/reports/cPortfolioDetails.php');
require_once("libraries/reports/cPholdingsInfo.php");
require_once("libraries/reports/cReports.php");

class PortfolioInformation_Transactions_Model extends Vtiger_Module {
    public $transaction_info = array();
    public $account_info = array();
    public $date, $account_number, $orderby, $direction, $num_pages, $next_page, $prev_page, $current_page, $searchcontent, $searchtype;
    public $search_list, $num_results, $pagination, $symbols, $security_types, $descriptions, $actions, $filter;
    public $actions_checked, $symbols_checked, $descriptions_checked, $security_types_checked, $date_range_checked;
    public $transaction_filter_actions_value_carried, $transaction_filter_symbols_value_carried, $transaction_filter_descriptions_value_carried;
    public $transaction_filter_security_types_value_carried, $transaction_filter_symbols_value, $transaction_filter_date_range_start_carried;
    public $transaction_filter_date_range_end_carried;

    public function GenerateReport(Vtiger_Request $request){
        global $list_max_entries_per_page, $adb;
        $report = new cReports();

        $pids = $report->GetPortfolioIdsFromAccountNumber($request->get('account_number'));
        $acct = $request->get('account_number');
        $pids = SeparateArrayWithCommas($pids);
        $direction = $request->get('direction');
        if(!isset($direction))
            $direction = "DESC";
        
        $current_page = $request->get('pagenumber');
        if($current_page <= 0)
                $current_page = 1;

        $url_string = '';

        $reverse = $request->get("reverse");
        $searchtype = $request->get("searchtype");
        $searchcontent = $request->get("searchtext");
        $symbol_values = $request->get("transaction_filter_symbols_value");
        $orderby = $request->get("order_by");
        
        if(!$orderby)
            $orderby = "trade_date";
        
        if(!$direction)
        {
            $direction = "DESC";
            $_SESSION['direction'] = "DESC";
        }

        if($reverse == "1")
        {
            if($direction == "ASC")
            {
                $direction = "DESC";
                $_SESSION['direction'] = "DESC";
            }
            else
            {
                $direction = "ASC";
                $_SESSION['direction'] = "ASC";
            }
        }

        $pagination = $_SESSION['transaction_pagination'];

        if(pagination == null)//Cookies haven't been set
                $pagination = 20;

        ob_start();
        if($request->get("numresults") > 0)
                $pagination = $request->get("numresults");
        
        $_SESSION['transaction_pagination'] = $pagination;
        ob_end_flush();

        if($pagination == 0)
                $pagination = 20;//Avoid divide by zero issues

        $report = new cReports();
        $acct = $request->get("account_number");
        $filter = "";

        if($request->get('transaction_filter_submit'))
            $current_page = 1;

        if(!$request->get('transaction_filter_reset'))
        {
            if($request->get('transaction_filter_actions_value'))
            {
                $actions_checked = "checked";
                $action = $request->get('transaction_filter_actions_value');
                $action = SeparateArrayWithCommasAndSingleQuotes($action);

                if($action)
                    $filter .= "AND activity_name IN({$action}) ";
            }
            if($request->get('transaction_filter_symbols_value'))
            {
                $symbols_checked = "checked";
                $symbols = $_POST['transaction_filter_symbols_value'];
                $symbols = mysql_real_escape_string($symbols);
                $symbols = str_replace(" ", "", $symbols);
                $symbols = explode(",", $symbols);
                $symbols = SeparateArrayWithCommasAndSingleQuotes($symbols);
                if($symbols)
                    $filter .= "AND security_symbol IN ({$symbols}) ";
            }
            if($request->get('transaction_filter_descriptions_value'))
            {
                $descriptions_checked = "checked";
                $description = $request->get('transaction_filter_descriptions_value');
                $description = SeparateArrayWithCommasAndSingleQuotes($description);
        //        $description = mysql_real_escape_string($description);
                if($description)
                    $filter .= "AND notes IN({$description}) ";
            }
            if($request->get('transaction_filter_security_types_value'))
            {
                $security_types_checked = "checked";
                $security_types = $_POST['transaction_filter_security_types_value'];
                $security_types = SeparateArrayWithCommasAndSingleQuotes($security_types);
        //        $security_types = mysql_real_escape_string($security_types);
                if($security_types)
                    $filter .= "AND code_description IN({$security_types}) ";
            }
            if($request->get('transaction_filter_date_range_start') || $request->get('transaction_filter_date_range_end'))
            {
                $date_range_checked = "checked";
                $date_start = $request->get('transaction_filter_date_range_start');
                $date_end = $request->get('transaction_filter_date_range_end');

                if($date_start)
                {
                    //Date formated as mm/dd/yyyy
                    $date_start = date("Y-m-d", strtotime($date_start));
                    $filter .= "AND trade_date >= '{$date_start}'";
                }
                if($date_end)
                {
                    //Date formated as mm/dd/yyyy
                    $date_end = date("Y-m-d", strtotime($date_end));
                    $filter .= "AND trade_date <= '{$date_end}'";
                }
            }
        }

        if($request->get('transaction_filter_reset'))
            $filter = "";

        if($filter == "" && !$request->get('transaction_filter_reset'))
            $filter = $request->get('filter');

        $instructions = array("order_by"=>$orderby,
                              "direction"=>$direction,
                              "filter"=>$filter);
        $transaction_handler = new cTransactions();        
        $portfolio_transactions = $transaction_handler->GetAllPortfolioTransactions($pids, null);
        $transaction_handler->FillTransactionTable($portfolio_transactions);
        $symbol_totals = $transaction_handler->GetSymbolTotals();
        $transaction_handler->CreateSummaryTable($symbol_totals);
        $all_transactions = $transaction_handler->GetAllTransactions("'{$acct}'", $instructions);
        $filtering = $transaction_handler->SeparateTransactionsForFiltering("{$pids}");

        $holdings_info = new cPholdingsInfo();

        $num_rows = $adb->num_rows($all_transactions);

        $num_pages = $num_rows/$pagination;
        $num_pages = ceil($num_pages);
        $next_page = $current_page+1;
        $prev_page = $current_page-1;
        if($next_page > $num_pages)
                $next_page = $num_pages;
        if($prev_page < 1)
                $prev_page = 1;

        $transaction_info = array();
        $x = ( ($pagination * $current_page) - $pagination);

        foreach($all_transactions AS $k => $v)
        {
            if($k >= $x && $k < ($pagination * $current_page))
                $transaction_info[] = $v;
        }

        $time = strtotime("-1 year", time());
        $lyear = date("Y-m-d", $time);

        $account_info = array();

        $other_totals = array();

        $priceDate = str_replace(" 00:00:00", '', $priceDate);
        
        $this->date = date("m/d/Y");
        $this->account_number = $acct;
        $this->transaction_info = $transaction_info;
        $this->account_info = $account_info;
        $this->orderby = $orderby;
        $this->direction = $direction;
        $this->num_pages = $num_pages;
        $this->next_page = $next_page;
        $this->prev_page = $prev_page;
        $this->current_page = $current_page;
        $this->searchcontent = $searchcontent;
        $this->searchtype = $searchtype;
        $this->search_list = $search_list;
        $this->num_results = $num;
        $this->pagination = $pagination;
        $this->symbols = $filtering['symbols'];
        $this->security_types = $filtering['security_types'];
        $this->descriptions = $filtering['descriptions'];
        $this->actions = $filtering['actions'];
        $this->filter = $filter;
        $this->actions_checked = $actions_checked;
        $this->symbols_checked = $symbols_checked;
        $this->descriptions_checked = $descriptions_checked;
        $this->security_types_checked = $security_types_checked;
        $this->date_range_checked = $date_range_checked;
        $this->transaction_filter_actions_value_carried = $request->get('transaction_filter_actions_value');
        $this->transaction_filter_symbols_value_carried = $request->get('transaction_filter_symbols_value');
        $this->transaction_filter_descriptions_value_carried = $request->get('transaction_filter_descriptions_value');
        $this->transaction_filter_security_types_value_carried = $request->get('transaction_filter_security_types_value');
        $this->transaction_filter_symbols_value = $symbol_values;
        $this->transaction_filter_date_range_start_carried = $request->get('transaction_filter_date_range_start');
        $this->transaction_filter_date_range_end_carried = $request->get('transaction_filter_date_range_end');        
    }
}
?>
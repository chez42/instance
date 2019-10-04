<?php


require_once("libraries/reports/cReports.php");
require_once('libraries/reports/cTransactions.php');
require_once("libraries/reports/cPholdingsInfo.php");
require_once("libraries/reports/cPortfolioDetails.php");
require_once("libraries/reports/pdf/cPDFDBAccess.php");
include_once("modules/PortfolioInformation/PortfolioInformation.php");
           
class PortfolioInformation_Positions_Model extends Vtiger_Module {
    public $categories = array();
    public $grand_totals = array();
    public $account_number = array();
    public $accounts_used = array();
    public $chart_data = array();
    public $main_categories = array();
    public $sub_sub_categories = array();
    public $messages;
    public $date, $priceDate, $account;

    public function __construct() {
        $this->messages = new PortfolioInformation_Messages_Model();
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
                break;
        }        
        $pids = $report->GetPortfolioIdsFromAccountNumber($numbers);
        $pids = SeparateArrayWithCommas($pids);
        $acct = $request->get('account_number');

        $transaction_handler = new cTransactions();
        $holdings_info = new cPholdingsInfo();
        $portfolio_details = new cPortfolioDetails();

        if(strlen($acct) > 0)
            $tmp = "AND portfolio_account_number IN ('{$acct}') ";
        $filter = array("filter" => $tmp);
        
        $all_transactions = $transaction_handler->GetAllPortfolioTransactions($pids, $filter);// $portfolio_info->GetAllPortfolioTransactions($pids, null);
        $transaction_handler->FillTransactionTable($all_transactions);

        $shorts = $holdings_info->CalculateShorts($numbers); // ToDo : Should be calculated after create summary table
        $symbol_totals = $transaction_handler->GetSymbolTotals();

        $transaction_handler->CreateSummaryTable($symbol_totals);
        $categories = $holdings_info->GetCategories();
        $main_categories = array();
        foreach($categories AS $k => $v){
            $result = $holdings_info->CalculateMainCategory($k);
            $main_categories[$k]['totals'] = $result;
        }
        
        $sub_sub_categories = $holdings_info->CalculateSubSubCategories();
        
        $accounts = $transaction_handler->GetAccountNumbers();
        
        $account_info = array();
        foreach($accounts AS $k => $v)
        {
            $result = GetPortfolioInfoFromPortfolioAccountNumber($v);
            $acct_name = $adb->query_result($result, 0, "portfolio_account_name");
            $nickname = $adb->query_result($result, 0, "nickname");
            $account_info[$v] = array("acct_name"=>$acct_name,
                                      "nickname"=>$nickname);
        }

        $firstSet = 0;

        $pie = $holdings_info->CalculateSubTotals($acct);
        
        $content = array();
        foreach($pie AS $k => $v)
        {
            $color = PortfolioInformation::GetChartColorForTitle($k);
                if($color && $v['sub_total']['value'] >= 0)
                    $content[] = array("title"=>$k, 
                                       "value"=>number_format($v['sub_total']['value'], 2, '.', ''),
                                       "color"=>$color);
                else
                    $content[] = array("title"=>$k, 
                                       "value"=>number_format($v['sub_total']['value'], 2, '.', ''));
			
          	$v = number_format($v['sub_total']['value'], 2, '.', '');
            
          	$pdf_pie[] = array($k=>$v); 
		}
        
        $securities = $holdings_info->GetSecurities($acct);
        foreach($securities AS $k => $v)
            $final[] = $v;

        $pdfAccess = new cPDFDBAccess();
        $pdfAccess->WritePositions($final);

        $gt = $holdings_info->CalculateGrandTotals($acct);
        $this->messages->GenerateMessagesFromMainCategories($main_categories);

        $priceDate = str_replace(" 00:00:00", '', $priceDate);

        $this->main_categories = $main_categories;
        $this->sub_sub_categories = $sub_sub_categories;
        $this->categories = $categories;
        $this->grand_totals = $gt;
        $this->account = $numbers;
        $this->account_number = $account_number;
        $this->accounts_used = $account_info;
        $this->date = date("m/d/Y");
        $this->chart_data = $content;
    }
}
?>
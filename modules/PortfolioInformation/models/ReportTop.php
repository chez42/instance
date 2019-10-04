<?php
require_once("include/utils/omniscientCustom.php");
require_once("libraries/reports/cTransactions.php");
require_once('libraries/reports/cPortfolioDetails.php');
require_once("libraries/reports/cPholdingsInfo.php");
require_once("libraries/reports/pdf/cPDFDBAccess.php");
require_once("libraries/reports/cReports.php");
include_once("include/utils/cron/cTransactionsAccess.php");
include_once("modules/PortfolioInformation/PortfolioInformation.php");

class PortfolioInformation_ReportTop_Model extends Vtiger_Module {
    public $final, $account_totals, $id, $account_info, $date;
    public $account_number, $summary_info, $updates, $chart_data;

    public function GenerateReport($account_number){
        if(strlen($account_number) > 0){
            $report = new cReports();
            $pids = $report->GetPortfolioIdsFromAccountNumber(array($account_number));
            $pids = SeparateArrayWithCommasAndSingleQuotes($pids);
//            $pids = "309, 29209";
//            $id = 46;
            $acct = $account_number;

            if(strlen($pids) > 0){
                $transactions_copier = new cTransactionsAccess();
                $transactions_copier->CopyTransactionsFromPCToCRM($pids);
                $transaction_handler = new cTransactions();
                $holdings_info = new cPholdingsInfo();
                $portfolio_details = new cPortfolioDetails();

                $all_transactions = $transaction_handler->GetAllPortfolioTransactions($pids, null);

                $transaction_handler->FillTransactionTable($all_transactions);

                $symbol_totals = $transaction_handler->GetSymbolTotals();
                $transaction_handler->CreateSummaryTable($symbol_totals);
                $securities = $holdings_info->GetSecurities($acct);
                $accounts = $transaction_handler->GetAccountNumbers();

                $account_info = $portfolio_details->GetAccountDetails($acct);
                foreach($accounts AS $k => $v)
                    $summary_info[] = $portfolio_details->GetAccountDetails($v);

                $account_totals = $portfolio_details->CalculateGrandTotal($summary_info);

                foreach($securities AS $k => $v)
                    $final[] = $v;

                $firstSet = 0;

                $pie = array();
                
                if(isset($account_info['shorts']) && $account_info['shorts'] != ''){
	                $account_info['shorts'] = abs($account_info['shorts']);
	                $pie["Shorts"]["sub_total"]["value"] = abs($account_info['shorts']);
                }

                $subTotal = $holdings_info->CalculateSubTotals($acct);
                
                if(!empty($subTotal))
                	$pie = array_merge($pie, $subTotal);
                
               	if(!empty($pie)){
	                foreach($pie AS $k => $v){
	
	                    $v = number_format($v['sub_total']['value'], 2, '.', '');
	
	                    $color = PortfolioInformation::GetChartColorForTitle($k);
	                    
	                    if($color)
	                        $content[] = array("title"=>$k, "value"=>$v, "color"=>$color);
	                    else
	                        $content[] = array("title"=>$k, "value"=>$v);
	                    
	                    $pdf_pie[] = array($k=>$v);
	                }
               	}

                $fees = $transaction_handler->GetAnnualManagementFee($acct);
                $account_info['annual_fee'] = $fees;

                $pdfAccess = new cPDFDBAccess();
                $pdfAccess->WriteDetails($account_info);
                $pdfAccess->WritePie($account_info['number'], $pdf_pie);
                $pdfAccess->WriteOtherAccounts($account_info['number'], $summary_info);
                foreach($summary_info AS $k => $v){
                    $tmp[] = $v;
                    $pdfAccess->WriteOtherAccounts($v['number'], $tmp);
                }    
                $holdings_update = $pdfAccess->ReadLastUpdate($account_info['number'], "account_positions_pdf");
                if(!$holdings_update)
                    $holdings_update = "Never";
                $monthly_update = $pdfAccess->ReadLastUpdate($account_info['number'], "account_monthly_income_pdf");
                if(!$monthly_update)
                    $monthly_update = "Never";
                $performance_update = $pdfAccess->ReadLastUpdate($account_info['number'], "account_performance_pdf");
                if(!$performance_update)
                    $performance_update = "Never";

                $updates = array("holdings" => $holdings_update,
                                 "monthly" => $monthly_update,
                                 "performance" => $performance_update);
                
                $this->final = $final;
                $this->account_totals = $account_totals;
                $this->account_number = $account_number;
                $this->account_info = $account_info;
                $this->date = date("m/d/Y");
                $this->summary_info = $summary_info;
                $this->updates = $updates;
                $this->chart_data = json_encode($content);
            }
        }
    }
    
    public function VerifyAccountNumber($account_number){
        global $adb;
        $query = "SELECT * FROM vtiger_portfolioinformation WHERE account_number = ?";
        $result = $adb->pquery($query, array($account_number));
        if($adb->num_rows($result) > 0)
            return true;
        return false;
    }
}
?>
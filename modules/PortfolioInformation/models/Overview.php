<?php
require_once("libraries/reports/cPOverview.php");
require_once("libraries/reports/cTWR.php");
require_once("libraries/reports/cReturn.php");
require_once("libraries/reports/calculateReturn.php");

require_once("libraries/reports/cReports.php");
require_once("libraries/reports/cReportGlobals.php");
require_once('libraries/reports/cTransactions.php');
require_once("libraries/reports/cPholdingsInfo.php");
require_once("libraries/reports/cPortfolioDetails.php");
require_once("libraries/reports/pdf/cPDFDBAccess.php");

require_once("modules/PortfolioInformation/PortfolioInformation.php");
class PortfolioInformation_Overview_Model extends Vtiger_Module {
    public $inception = array();
    public $trailing = array();
    public $qtr = array();
    public $lyr = array();
    public $ytd = array();
    public $content = array();
    public $account_info = array();
    public $value_history = array();
    public $warning, $as_of, $inception_irr, $qtr_irr, $trailing_irr, $ytd_irr;
    public $inception_ref, $qtr_ref, $trailing_ref, $ytd_ref;
    public $qtr_bab, $trailing_bab, $ytd_bab, $inception_bab;
    public $goal, $management_fees;
    public $pids, $account, $transactions, $all_account_numbers;
    public $client_name;

    /**
     * Get the calculated goal amount based on the passed in pids
     * @global type $adb
     * @param type $pids
     */
    public function GetGoalPercent($pids){
        global $adb;
        
       	/* ====  START : Felipe 2016-07-25 MyChanges ===== */
        $query = "SELECT portfolioinformationid 
                  FROM vtiger_portfolioinformation p
                  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
                  WHERE account_number IN (SELECT portfolio_account_number FROM vtiger_portfolios WHERE portfolio_id IN ({$pids}))
                  AND (total_value is not null OR total_value != 0)
                  AND e.deleted = 0";
                  
        $result = $adb->pquery($query, array());
        $goal = 0;
        $calc = $adb->num_rows($result) * 100;//Number of rows * 100 for calculation purposes
        while($v = $adb->fetchByAssoc($result)){
            //$tmp = Vtiger_Record_Model::getInstanceById($v['portfolioinformationid']);
            //$goal += $tmp->get('cf_802');
            $goal += getSingleFieldValue("vtiger_portfolioinformationcf", 'cf_802', 'portfolioinformationid', $v['portfolioinformationid']);
        }

        $goal = $goal / $calc * 100;
        return $goal;
    }
    
    /* ====  START : Felipe 2016-07-25 MyChanges ===== */
       
    /**
     * Returns the account number and management fees amount based on portfolio ids
     * @global type $adb
     * @param type $pids
     * @return type
     */
    public function GetManagementFees($pids){
        global $adb;
        $query = "select p.portfolio_account_number AS account_number, SUM(cost_basis_adjustment) AS management_fee 
                  from vtiger_pc_transactions t
                  join vtiger_portfolios p ON p.portfolio_id = t.portfolio_id
                  WHERE activity_id = 160 
                  AND t.portfolio_id IN ({$pids}) 
                  AND report_as_type_id = 60
                  GROUP BY t.portfolio_id";
        $result = $adb->pquery($query, array());
        $fees = array();
        $total = 0;
        if($adb->num_rows($result) > 0)
        foreach($result AS $k => $v){
            $fees[$v['account_number']] = $v['management_fee'];
        }
        
        return $fees;        
    }
    
    public function GenerateReport(Vtiger_Request $request){
        global $adb;
        $report = new cReports();
        $calling_record = $request->get('calling_record');
        switch($request->get('calling_module')){
            case "Accounts":
                $pids = GetPortfolioIDsFromHHID($calling_record);
                $numbers = GetPortfolioAccountNumbersFromPids($pids);
                $this->client_name = cReportGlobals::GetClientName($numbers[0], 'Accounts');
                break;
            case "Contacts":
                $pids = GetPortfolioIDsFromContactID($calling_record);
                $numbers = GetPortfolioAccountNumbersFromPids($pids);
                $this->client_name = cReportGlobals::GetClientName($numbers[0], 'Contacts');
                break;
            default:
                $numbers = $request->get('account_number');
                break;
        }

        $pids = $report->GetPortfolioIdsFromAccountNumber($numbers);
        $pids = SeparateArrayWithCommas($pids);
        
        $this->goal = $this->GetGoalPercent($pids);
        $this->management_fees = $this->GetManagementFees($pids);

        $accounts = $request->get('account_number');

        $transaction_handler = new cReturn();//cReturn is a child of cTransactions
        $holdings_info = new cPholdingsInfo();
        $portfolio_details = new cPortfolioDetails();

        $m = date('m');
        $d = date('d');
        $Y = date('Y');

        $date = date('Y-m-d 00:00:00',mktime(0,0,0,$m,$d,$Y));

        $accts_back = $accounts;

        $months = array();
        $months[] = date('Y-m-d 00:00:00',mktime(0,0,0,$m-11,0,$Y));
        $months[] = date('Y-m-d 00:00:00',mktime(0,0,0,$m-10,0,$Y));
        $months[] = date('Y-m-d 00:00:00',mktime(0,0,0,$m-9,0,$Y));
        $months[] = date('Y-m-d 00:00:00',mktime(0,0,0,$m-8,0,$Y));
        $months[] = date('Y-m-d 00:00:00',mktime(0,0,0,$m-7,0,$Y));
        $months[] = date('Y-m-d 00:00:00',mktime(0,0,0,$m-6,0,$Y));
        $months[] = date('Y-m-d 00:00:00',mktime(0,0,0,$m-5,0,$Y));
        $months[] = date('Y-m-d 00:00:00',mktime(0,0,0,$m-4,0,$Y));
        $months[] = date('Y-m-d 00:00:00',mktime(0,0,0,$m-3,0,$Y));
        $months[] = date('Y-m-d 00:00:00',mktime(0,0,0,$m-2,0,$Y));
        $months[] = date('Y-m-d 00:00:00',mktime(0,0,0,$m-1,0,$Y));
        $months[] = date('Y-m-d 00:00:00',mktime(0,0,0,$m-0,0,$Y));

        $history = array();
        $abreviated_months = array();
        $currentMonth = (int)date('m');//Get the current month
        $count = 0;
        for($x = $currentMonth; $x < $currentMonth+12; $x++) {//Calculate the next 12 months so we can sort them in order from the current month
            $abreviated_months[$count] = substr(date('F', mktime(0, 0, 0, $x, 1)), 0, 3);//Only take the first 3 letters from the month because the database returns them as Jan, Feb, Mar, etc...
            $count++;
        }

        $portfolio_transactions = $transaction_handler->GetAllPortfolioTransactions($pids, null);// $portfolio_info->GetAllPortfolioTransactions($pids, null);
        $transaction_handler->FillTransactionTable($portfolio_transactions);
        $all_transactions = $transaction_handler->GetAllTransactions();

        foreach($months AS $k => $v)
        {
            $t = $transaction_handler->GetSymbolTotals($v);
            $val = $transaction_handler->AddAllSymbolTotals($t);
            $history[$abreviated_months[$k]] = $val;
        }

        $categories = array();
        $price_dates = array();

        // To Do: Why Pass Empty $price_dates array
        foreach($price_dates AS $k => $v)
            if($priceDate <= $v)
                $priceDate = $v;

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

        $symbol_totals = $transaction_handler->GetSymbolTotals();
        $transaction_handler->CreateSummaryTable($symbol_totals);


        $qtr_start_calculation_date = strtotime("-3 month, -2 day");
        $qtr_start_calculation_date = date("Y-m-d", $qtr_start_calculation_date);
        $qtr_start_show_date = strtotime("-3 month, -1 day");
        $qtr_end_calculation_date = strtotime("-1 day");

        $qtr_start = date("Y-m-d", $qtr_start_show_date);//The date we show as the start date.. We have a -1 day calculation date to get the end value of the previous day
        $qtr_end = date("Y-m-d", $qtr_end_calculation_date);//Today's date, when we are finished calculating for the given interval

        $qtr = GetInvestmentReturnColumn($transaction_handler, $pids, $qtr_start_calculation_date, $qtr_end);
        $qtr['start_date'] = $qtr_start;
        $qtr['end_date'] = $qtr_end;
        $qtr['column_name'] = "qtr";

        $lyr_start_calculation_date = strtotime("last day of december, 2 years ago");

        $lyr_start_show_date = strtotime("first day of January, -1 year");
        $lyr_start_calculation_date = date("Y-m-d", $lyr_start_calculation_date);
        $lyr_end_calculation_date = strtotime("last day of december, 1 year ago");

        $lyr_start = date("Y-m-d", $lyr_start_show_date);
        $lyr_end = date("Y-m-d", $lyr_end_calculation_date);

        $lyr = GetInvestmentReturnColumn($transaction_handler, $pids, $lyr_start_calculation_date, $lyr_end);
        $lyr['start_date'] = $lyr_start;
        $lyr['end_date'] = $lyr_end;
        $lyr['column_name'] = "lyr";

        $trailing_start_calculation_date = strtotime("-1 year, -2 days");
        $trailing_start_show_date = strtotime("-1 year, -1 day");
        $trailing_start_calculation_date = date("Y-m-d", $trailing_start_calculation_date);
        $trailing_end_calculation_date = strtotime("-1 day");

        $trailing_start = date("Y-m-d", $trailing_start_show_date);
        $trailing_end = date("Y-m-d", $trailing_end_calculation_date);

        $trailing = GetInvestmentReturnColumn($transaction_handler, $pids, $trailing_start_calculation_date, $trailing_end);
        $trailing['start_date'] = $trailing_start;
        $trailing['end_date'] = $trailing_end;
        $trailing['column_name'] = "trailing_12";
//debug was here, remove this
        $ytd_start_calculation_date = strtotime("last day of december, 1 year ago");
        $ytd_start_show_date = strtotime("first day of January, -1 day");
        $ytd_start_calculation_date = date("Y-m-d", $ytd_start_calculation_date);
        $ytd_end_calculation_date = strtotime("-1 day");

        $ytd_start = date("Y-m-d", $ytd_start_show_date);
        $ytd_end = date("Y-m-d", $ytd_end_calculation_date);

        $ytd = GetInvestmentReturnColumn($transaction_handler, $pids, $ytd_start_calculation_date, $ytd_end);
        $ytd['start_date'] = $ytd_start;
        $ytd['end_date'] = $ytd_end;
        $ytd['column_name'] = "ytd";

        $inception_d = $transaction_handler->GetInceptionDate($pids);
        $inception_start_calculation_date = strtotime("{$inception_d}, -1 day");
        $inception_start_show_date = $inception_d;
        $inception_start_calculation_date = date("Y-m-d", $inception_start_calculation_date);
        $inception_end_calculation_date = strtotime("-1 day");

        $inception_start = str_replace("00:00:00", '', $inception_d);
        $inception_end = date("Y-m-d", $inception_end_calculation_date);

        $inception = GetInvestmentReturnColumn($transaction_handler, $pids, $inception_start_calculation_date, $inception_end);
        $inception['start_date'] = $inception_start;
        $inception['end_date'] = $inception_end;
        $inception['column_name'] = "inception";

        if($qtr['end_value']){
            $qtr_ref = $transaction_handler->getReferenceReturn("S&P 500",$qtr['start_date'],$qtr['end_date'],$qtr['expenses']/$qtr['end_value']);
            $qtr_bab = $transaction_handler->getReferenceReturn("AGG",$qtr['start_date'],$qtr['end_date'],$qtr['expenses']/$qtr['end_value']);
        }
        $qtr_ref = round(100*$qtr_ref, 2);
        $qtr_bab = round(100*$qtr_bab, 2);

        if($trailing['end_value']){
            $trailing_ref = $transaction_handler->getReferenceReturn("S&P 500",$trailing['start_date'],$trailing['end_date'],$trailing['expenses']/$trailing['end_value']);
            $trailing_bab = $transaction_handler->getReferenceReturn("AGG",$trailing['start_date'],$trailing['end_date'],$trailing['expenses']/$trailing['end_value']);
        }
        $trailing_ref = round(100*$trailing_ref, 2);
        $trailing_bab = round(100*$trailing_bab, 2);

        if($ytd['end_value']){
            $ytd_ref = $transaction_handler->getReferenceReturn("S&P 500",$ytd['start_date'],$ytd['end_date'],$ytd['expenses']/$ytd['end_value']);
            $ytd_bab = $transaction_handler->getReferenceReturn("AGG",$ytd['start_date'],$ytd['end_date'],$ytd['expenses']/$ytd['end_value']);
        }
        $ytd_ref = round(100*$ytd_ref, 2);
        $ytd_bab = round(100*$ytd_bab, 2);

        if($inception['end_value']){
#			echo $inception['start_date'] . " -- " . $inception['end_date'];
            $inception_ref = $transaction_handler->getReferenceReturn("S&P 500",$inception['start_date'],$inception['end_date'],$inception['expenses']/$inception['end_value']);
            $inception_bab = $transaction_handler->getReferenceReturn("AGG",$inception['start_date'],$inception['end_date'],$inception['expenses']/$inception['end_value']);
        }
        $inception_ref = round(100*$inception_ref, 2);
        $inception_bab = round(100*$inception_bab, 2);

        $as_of = new DateTime();
        $as_of->add(DateInterval::createFromDateString('yesterday'));
        $as_of = $as_of->format('m-d-Y') . "\n";

        if($inception['start_date'] >= $trailing['start_date'])
            $warning = "*";

        $inception['account_number'] = $numbers;

        $pdf_access = new cPDFDBAccess();
        $pdf_access->WritePerformance($inception);

        $pie = array();
        
        if(isset($inception['shorts']) && $inception['shorts'])
        	$pie['Shorts']['sub_total']['value'] = $inception['shorts'];
        
        $pie = $pie + $holdings_info->CalculateSubTotals();//Calculate the subtotals for the pie chart
        foreach($pie AS $k => $v){
            if($v['sub_total']['value'] >= 1){
//                $v = number_format($v['sub_total']['value'], 2, '.', '');
                $color = PortfolioInformation::GetChartColorForTitle($k);
                if($color)
                    $content[] = array("title"=>$k, 
                                       "value"=>number_format($v['sub_total']['value'], 2, '.', ''),
                                       "color"=>$color);
                else
                    $content[] = array("title"=>$k, 
                                       "value"=>number_format($v['sub_total']['value'], 2, '.', ''));

                $pdf_pie[] = array($k=>$v);
            }
        }

        $notFirst = true;
        $value_history = "";
        $count = 0;
        foreach($history AS $k => $v)
        {
            $value = money_format("%i", $v);
//            $value_history .= "{date: \"{$k}\", value:{$value}, open:\"$\"}";
            $pdf_bar[$k] = $value;            
            $value_history[] = array("date"=>$k, "value"=>$value, "open:"=>"$", "date_time"=>$months[$count]);
            $count++;
        }
        
        $qtr['start_date'] = date("m-d-Y", strtotime($qtr['start_date']));
        $qtr['end_date'] = date("m-d-Y", strtotime($qtr['end_date']));
        $lyr['start_date'] = date("m-d-Y", strtotime($lyr['start_date']));
        $lyr['end_date'] = date("m-d-Y", strtotime($lyr['end_date']));
        $trailing['start_date'] = date("m-d-Y", strtotime($trailing['start_date']));
        $trailing['end_date'] = date("m-d-Y", strtotime($trailing['end_date']));
        $ytd['start_date'] = date("m-d-Y", strtotime($ytd['start_date']));
        $ytd['end_date'] = date("m-d-Y", strtotime($ytd['end_date']));
        $inception['start_date'] = date("m-d-Y", strtotime($inception['start_date']));
        $inception['end_date'] = date("m-d-Y", strtotime($inception['end_date']));

		PortfolioInformation_HoldingsReport_Model::GenerateReportFromAccounts($numbers);
		$positions = cHoldingsReport::GetWeightedPositions();
		$positions = cHoldingsReport::CategorizePositions($positions);
//		$content = cHoldingsReport::CreatePieFromPositions($positions);
        PortfolioInformation_HoldingsReport_Model::GenerateEstimateTables($accounts);
        $estimatePie = PortfolioInformation_Reports_Model::GetPieFromTable();

        $this->inception = $inception;
        $this->trailing = $trailing;
        $this->qtr = $qtr;
        $this->lyr = $lyr;
        $this->ytd = $ytd;
        $this->content = $estimatePie;
        $this->account_info = $account_info;
        $this->warning = $warning;
        $this->as_of = $as_of;
        $this->inception_irr = $request->get('TWR_INCEPTION');
        $this->qtr_irr = $request->get('TWR_QTR');
        $this->trailing_irr = $request->get('TWR_TRAILING');
        $this->ytd_irr = $request->get('TWR_YTD');
        $this->inception_ref = $inception_ref;
        $this->qtr_ref = $qtr_ref;
        $this->trailing_ref = $trailing_ref;
        $this->ytd_ref = $ytd_ref;
        $this->pids = $pids;
        $this->value_history = $value_history;
        $this->qtr_bab = $qtr_bab;
        $this->trailing_bab = $trailing_bab;
        $this->ytd_bab = $ytd_bab;
        $this->inception_bab = $inception_bab;
		$this->all_account_numbers = $numbers;
        
        $pdf_access = new cPDFDBAccess();
        $this->account = $pdf_access->DetermineAccount($accounts);

        $ref = array("type"=>"ref",
                     "account_number"=>$accounts,
                     "trailing_3"=>$this->qtr_ref,
                     "trailing_12"=>$this->trailing_ref,
                     "year_to_date"=>$this->ytd_ref,
                     "inception"=>$this->inception_ref);
        $bar = array("type"=>"bar",
                     "account_number"=>$accounts,
                     "trailing_3"=>$this->qtr_bab,
                     "trailing_12"=>$this->trailing_bab,
                     "year_to_date"=>$this->ytd_bab,
                     "inception"=>$this->inception_bab);

        $pdf_access->WriteTWR($ref);
        $pdf_access->WriteTWR($bar);
        $pdf_access->WriteAccountHistory($accounts, $value_history);
        $converted = $this->ConvertPieForWriting($pie);
        $pdf_access->WritePie($accounts, $converted);
        $pdf_access->WriteAccountInvestmentReturns($accounts, $qtr);
        $pdf_access->WriteAccountInvestmentReturns($accounts, $lyr);
        $pdf_access->WriteAccountInvestmentReturns($accounts, $ytd);
        $pdf_access->WriteAccountInvestmentReturns($accounts, $trailing);
    }
    
    /**
     * Convert the pie chart for writing to the database
     * @param type $pie
     */
    private function ConvertPieForWriting($pie){
        foreach($pie AS $type => $sub_total){
            foreach($sub_total AS $k => $v){
                $converted[][$type] = $v['value'];
            }
        }
        
        return $converted;
    }
}
?>

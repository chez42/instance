<?php

require_once("libraries/reports/cTransactions.php");
require_once("libraries/reports/pdf/cPDFGenerator.php");
require_once("libraries/reports/pdf/cPDFDBAccess.php");
require_once("libraries/reports/cPortfolioDetails.php");
require_once("libraries/reports/cReportGlobals.php");
include_once("libraries/reports/pdf/cNewPDFGenerator.php");

class PortfolioInformation_PrintReport_Model extends Vtiger_Module {
    
    public $settings, $user_name, $account_details, $other_accounts, $other_totals, $final_value, $performance;
    public $ref, $twr, $bar, $months, $past_months, $month_total, $display_years_current, $stylesheet, $display_years_projected;
    public $pdf, $template_file, $pdf_name;
    public $future_months, $month_future_total, $projected_bar, $historical_bar;
    public $main_categories, $sub_sub_categories, $grand_total, $sorted_positions;
    public $future_symbol_totals, $accounts, $simple_categories, $simple_sub_categories, $account_totals;
    public $performance_accounts_used, $transactions;
    public $income_report;
    public $client_name;
    public $overview_pie, $overview_bar, $qtr, $lyr, $ytd, $trailing;
    public $mailing_info;
    public $pie_image;
    
	public $renderPieChart, $renderLineChart, $renderBarChart, $renderHistoricalBarChart;
		
    public function GenerateOverviewReport(Vtiger_Request $request){
        ini_set('memory_limit','2048M');
        $report_settings = new ReportSettings_Settings_Model();
        $current_user = Users_Record_Model::getCurrentUserModel();
        $accounts = $request->get("account_number");

        $this->mailing_info = PortfolioInformation_Reports_Model::GetMailingInformationForAccount(null, $request->get("account_number"));

        $pdf = new cPDFGenerator('c','LETTER-L','20','Arial',10,10,20,10,1,1);
        $pdf->SetupPDF(null, null, null, $current_user->get('id'));
        
        $pdfAccess = new cPDFDBAccess();
        
        $ref_result = $pdfAccess->ReadTWR($accounts, "ref");
        $twr_result = $pdfAccess->ReadTWR($accounts, "twr");
        $bar_result = $pdfAccess->ReadTWR($accounts, "bar");

        $this->ref = $this->ConvertTWR($ref_result);
        $this->twr = $this->ConvertTWR($twr_result);       
        $this->bar = $this->ConvertTWR($bar_result);
       
        $this->qtr = $pdfAccess->ReadAccountInvestmentReturns($accounts, 'qtr');
        $this->lyr = $pdfAccess->ReadAccountInvestmentReturns($accounts, 'lyr');
        $this->ytd = $pdfAccess->ReadAccountInvestmentReturns($accounts, 'ytd');
        $this->trailing = $pdfAccess->ReadAccountInvestmentReturns($accounts, 'trailing_12');

        $this->transactions = $pdfAccess->ReadTransactions($request->get('overview_account_numbers'));

        $pie_result = $pdfAccess->ReadPie($accounts);
        $pdf_pie = $this->ConvertPie($pie_result);
        
        $bar_result = $pdfAccess->ReadAccountHistory($accounts);
        $pdf_bar = $this->ConvertBar($bar_result);

        $performance_result = $pdfAccess->ReadPerformance($accounts);
        $performance = $this->ConvertPerformance($performance_result);
        $inception = $performance['start_date'];
        $used_accounts_tmp = unserialize($performance['accounts_used']);
        $serialized_accounts = unserialize($performance['serialized_accounts']);
        foreach($used_accounts_tmp AS $k => $v){
            $nickname = cReportGlobals::GetAccountNickname($v);
            $performance_accounts_used[] = array('account_number'=>$serialized_accounts[$k],
                                                 'account_nickname'=>$nickname);
        }
        if(!$performance_accounts_used){
            $accounts = $request->get('account_number');
            foreach($accounts AS $k => $v){
                $performance_accounts_used[] = array('account_number' => str_repeat('*', strlen($v) - 4) . substr($v, -4),
                                                     'account_nickname' => '');
            }
        }
        $other_result = $pdfAccess->ReadOtherAccountsWithTotals($accounts);
        $others = array();
        foreach($other_result AS $k => $v){
            $others[$v['account_number']] = $v;
        }

        if(!empty($pdf_pie)){
            PortfolioInformation_HoldingsReport_Model::GenerateReportFromAccounts($request->get('overview_account_numbers'));
            $positions = cHoldingsReport::GetWeightedPositions(true);
            $positions = cHoldingsReport::CategorizePositions($positions);
            $pdf_pie = cHoldingsReport::CreatePHPGeneratorCompatiblePieFromPositions($positions);
#            $pdf_pie = cHoldingsReport::CreatePieFromPositions($positions);
            $pdf->CreatePieChart($pdf_pie, "overview_pie.png", 640, 480);
            $this->renderPieChart = true;
        }

        if(!empty($pdf_bar)){
            $pdf->CreateBarChart($pdf_bar, "chart.png", 600, 325);
            $this->renderBarChart = true;
        }

        $settings = array();
        $settings_result = $report_settings->GetPrintSectionSetting($current_user->get('id'));
        foreach($settings_result AS $k => $v)
            $settings[$k] = $v;

        if(strlen($request->get('pie_image')) > 1) {
            $image = cNewPDFGenerator::TextToImage($request->get('pie_image'));
            $this->pie_image = '<img src="data:image/jpeg;base64,' . base64_encode($image) . '" width="640" height="480" />';
        }

        $income_report = new PortfolioInformation_MonthlyIncome_Model();
        $this->income_report = $income_report;

        $this->overview_pie = $pdf_pie;
        $this->overview_bar = $pdf_bar;
        $this->client_name = $request->get('client_name');

        $this->pdf_name = "overview";
        $this->template_file = "pdf/overview_pdf.tpl";
        $this->stylesheet = 'layouts/vlayout/modules/PortfolioInformation/css/pdf/global_pdf.css';
        $this->pdf = $pdf;
        $this->user_name = $pdf->user_name;
        $this->other_accounts = $others;
        $this->performance_accounts_used = $performance_accounts_used;
        $this->performance = $performance;
        

		$logo = $current_user->getImageDetails();
		
		if(isset($logo['user_logo']) && !empty($logo['user_logo'])){
			if(isset($logo['user_logo'][0]) && !empty($logo['user_logo'][0])){
				$logo = $logo['user_logo'][0];
				$logo = $logo['path']."_".$logo['name'];
			} else
				$logo = 0;
		} else
			$logo = "";

		if($logo == "_" || !$logo)
		    $logo = "test/logo/Omniscient Logo small.png";
			
		$pdf->logo = $this->logo = $logo;
		
		$pdf->setAutoBottomMargin = "stretch";
		$pdf->setAutoTopMargin = 'stretch';
        
        $pdf->SetupHeader($logo, $this->client_name, $inception);

		/* === END : Changes For Report Logo 2017-02-16 === */
		
#        $pdf->SetupHeader("storage/logos/".$settings['logo'], $this->client_name, $inception);
#        $pdf->SetupHeader("test/logo/Omniscient Logo small.png", $this->client_name, $inception);

        $pdf->SetupFooter();        
    }
    
    public function GenerateReport(Vtiger_Request $request){
        $accounts = $request->get('account_number');

        foreach($accounts AS $k => $v){
            $info = new cPortfolioDetails();
            $account_info = $info->GetAccountDetails($v);
        }
            if(strlen($request->get('calling_record')) > 0){
            $record = Vtiger_Record_Model::getInstanceById($request->get('calling_record'));
            switch($record->getModuleName()){
                case "Accounts":
                    $account_name = $record->get('cf_722');
                    if(!strlen($account_name))
                        $account_name = $record->get("accountname");            
                    break;
                case "Contacts":
                    $account_name = $record->get('cf_721');
                    if(!strlen($account_name))
                        $account_name = $record->get("firstname") . " " . $record->get("lastname");
                    break;
                default:
                    $account_name = "Portfolio Un-named";
                    break;
            }
        }

        $report_settings = new ReportSettings_Settings_Model();
        $current_user = Users_Record_Model::getCurrentUserModel();

        $pdf = new cPDFGenerator('c','LETTER-L','20','Arial',10,10,20,10,1,1);
        $pdf->SetupPDF(null, null, $account_info['name'], $current_user->get('id'));

	$pdfAccess = new cPDFDBAccess();
        $pie_result = $pdfAccess->ReadPie($accounts);
        $line_result = $pdfAccess->ReadAUM($accounts);
        $revenue_result = $pdfAccess->ReadRevenue($accounts);
        $details_result = $pdfAccess->ReadDetails($accounts);
        $other_result = $pdfAccess->ReadOtherAccountsWithTotals($accounts);
        $positions_result = $pdfAccess->ReadPositions($accounts);
        $performance_result = $pdfAccess->ReadPerformance($accounts);
        $ref_result = $pdfAccess->ReadTWR($accounts, "ref");
        $twr_result = $pdfAccess->ReadTWR($accounts, "twr");
        $bar_result = $pdfAccess->ReadTWR($accounts, "bar");
        
        $final = array();
        $isFirst = true;
        
        foreach($details_result AS $k => $v)
        {
            $details[$v['account_number']]['name'] = $v['account_name'];
            $details[$v['account_number']]['number'] = $v['account_number'];
            $details[$v['account_number']]['custodian'] = $v['custodian'];
            $details[$v['account_number']]['type'] = $v['account_type'];
            $details[$v['account_number']]['total'] = $v['total_value'];
            $details[$v['account_number']]['market_value'] = $v['market_value'];
            $details[$v['account_number']]['cash_value'] = $v['cash_value'];
            $details[$v['account_number']]['management_fee'] = $v['management_fee'];
            $details[$v['account_number']]['annual_fee'] = $v['annual_management_fee'];
            $details[$v['account_number']]['master_account'] = $v['master_account'];

            if(!$isFirst)
            {
                $final['name'] += ", " . $v['account_name'];
                $final['number'] += ", " . $v['account_number'];
                $final['master_account'] += $v['master_account'];
            }
            else
            {
                $final['name'] = $v['account_name'];
                $final['number'] = $v['account_number'];
                $final['custodian'] = $v['custodian'];
                $final['type'] = $v['account_type'];
                $final['total'] += $v['total_value'];
                $final['market_value'] += $v['market_value'];
                $final['cash_value'] += $v['cash_value'];
                $final['management_fee'] += $v['management_fee'];
                $final['annual_fee'] += $v['annual_management_fee'];
                $final['master_account'] = $v['master_account'];
            }

            $isFirst = false;
        }
        
		$pdf_pie = array();
		
		$pdf_pie = $this->ConvertPie($pie_result);
		
        $line_result = $this->ConvertLine($line_result); // Changes 2016-08-23 For Print PDF Report
        
        $others = array();
        $other_totals = array();
        foreach($other_result AS $k => $v){
            $others[$v['account_number']] = $v;
            $other_totals['total_value'] = $v['total_value_sum'];
            $other_totals['market_value'] = $v['market_value_sum'];
            $other_totals['cash_value'] = $v['cash_value_sum'];
        }

        $final_value = array();
        $holdings = array();
        
        $main_categories = $pdfAccess->GetMainPositionCategories($accounts);
        $sub_sub_categories = $pdfAccess->GetPositionSubSubCategories($accounts);
        $grand_total = $pdfAccess->GetPositionsGrandTotal($accounts);
        $sorted_positions = $pdfAccess->GetPositionsSorted($accounts);
        $simple_categories = $pdfAccess->GetSimpleCategories($accounts);
        $simple_sub_categories = $pdfAccess->GetSimpleSubCategories($accounts);
        $account_totals = $pdfAccess->GetAccountTotals($accounts);
        
        foreach($positions_result AS $k => $v){
            $final_value[$k] = $v;
            $holdings[$v['asset_class']] = $v;
        }

        if(is_array($pdf_pie) && sizeof($pdf_pie) > 0)
        {
		
			$pdf->CreatePieChart($pdf_pie, "positions_pie.png", 400, 400);
			$this->renderPieChart = true;
		}
		
		if(!empty($line_result)){
			$pdf->CreateLineChart($line_result, "positions_line.png", 1000, 300);
            $pdf->CreateAUMBarChart($line_result, "account_historical.png", 900, 400, 90,30,30,30, "");
			
			$this->renderLineChart = true;
			$this->renderBarChart = true;
		}

        $stylesheet = 'layouts/vlayout/modules/PortfolioInformation/css/pdf/global_pdf.css';
        $template_file = "CustomPdf.tpl";
        $pdf_name = "custom_settings";

        $start_date = "";
        $end_date = "";
        $currentMonth = (int)date('m');//Get the current month
        for($x = $currentMonth; $x < $currentMonth+12; $x++) {//Calculate the next 12 months so we can sort them in order from the current month
            $month = substr(date('F', mktime(0, 0, 0, $x, 1)), 0, 3);
            $tmp_month = date("n", strtotime("01-".$month));
            $display_months[] = $month;//Only take the first 3 letters from the month because the database returns them as Jan, Feb, Mar, etc...
            $display_years_current[$month] = '20'.$this->CalculateYear($tmp_month, true);//Should this still be live by the year 2100, this will need to be changed to 21 instead of just 20
            $display_years_projected[$month] = '20'.$this->CalculateYear($tmp_month, false);
            if(!strlen($start_date))
                $start_date = $display_years_current[$month] . "-" . $tmp_month . "-" . "01";
            $end_date = $display_years_current[$month] . "-" . $tmp_month . "-" . "01";
        }

        $future_start = date("Y-m-01");
        $future_end=date('Y-m-01', strtotime('+11 months'));
        
        
        $income_report = new PortfolioInformation_MonthlyIncome_Model();
        $income_report->AutoFillTables($accounts);
        $this->income_report = $income_report;
        
        $monthly_past_result = $pdfAccess->ReadMonthlyProjected($accounts, $start_date, $end_date);
        $monthly_future_result = $pdfAccess->ReadMonthlyProjected($accounts, $future_start, $future_end);
        
        $monthly_past = array();
        $monthly_future = array();
        $future_symbol_totals = array();
        
        foreach($monthly_past_result AS $k => $v){
            $monthly_past[$v['category']][$v['symbol']][$v['month']] = $v;
            if($v['symbol'] == "total_month")
                $month_total[$v['month']] = $v['monthly_total'];
        }
        if(is_array($month_total) && !empty($month_total)){
            $historical = $pdf->CreateBarChart($month_total, "historical.png", 900, 150, 90,30,30,30, "Trailing 12 Months Income");
			$this->renderHistoricalBarChart = true;
		}
		
        foreach($monthly_future_result AS $k => $v){
            $monthly_future[$v['category']][$v['symbol']][$v['month']] = $v;
            if($v['symbol'] == "total_month")
                $month_future_total[$v['month']] = $v['monthly_total'];
            $future_symbol_totals[$v['symbol']] = $v['symbol_total'];
        }

        if(is_array($month_future_total))
            $projected = $pdf->CreateBarChart($month_future_total, "projected.png", 900, 150, 90,30,30,30, "Projected 12 Months Income");
        
        $performance = $this->ConvertPerformance($performance_result);
        $used_accounts_tmp = unserialize($performance['accounts_used']);
        $serialized_accounts = unserialize($performance['serialized_accounts']);
        foreach($used_accounts_tmp AS $k => $v){
            $nickname = cReportGlobals::GetAccountNickname($v);
            $performance_accounts_used[] = array('account_number'=>$serialized_accounts[$k],
                                                 'account_nickname'=>$nickname);
        }
        
        $ref = $this->ConvertTWR($ref_result);
        $twr = $this->ConvertTWR($twr_result);
        $bar = $this->ConvertTWR($bar_result);
        
        $settings = array();
        $settings_result = $report_settings->GetPrintSectionSetting($current_user->get('id'));
        foreach($settings_result AS $k => $v)
            $settings[$k] = $v;

        switch($request->get('report_type')){
            case "performance":
                $settings = $this->CustomizeSettings($settings, 'performance');
                break;
            case "monthly_income":
                $settings = $this->CustomizeSettings($settings, 'monthly_income');
                break;
            case "holdings":
                $settings = $this->CustomizeSettings($settings, 'holdings,pie_chart');
                break;
        }
        $inception = date("m/d/Y", strtotime($performance['start_date']));
        $name = $pdf->AutoReportName();
        if(!strlen($name))
            $name = $account_name;

        if($record)
        $this->client_name = cReportGlobals::GetClientName($accounts[0], $record->getModuleName());
        else
            $this->client_name = "";

		$logo = $current_user->getImageDetails();
		
		if(isset($logo['user_logo']) && !empty($logo['user_logo'])){
			if(isset($logo['user_logo'][0]) && !empty($logo['user_logo'][0])){
				$logo = $logo['user_logo'][0];
				$logo = $logo['path']."_".$logo['name'];
			} else
				$logo = 0;
		} else
			$logo = "";

		if($logo == "_" || !$logo)
		    $logo = "storage/logos/".$settings['logo'];
			
		$pdf->logo = $this->logo = $logo;
			
		$pdf->setAutoBottomMargin = "stretch";
		$pdf->setAutoTopMargin = 'stretch';
        
        $pdf->SetupHeader($logo, $this->client_name, $inception);
		
        //$pdf->SetupHeader("storage/logos/".$settings['logo'], $this->client_name, $inception);
        $pdf->SetupFooter();
        
        $this->settings = $settings;
        $this->user_name = $pdf->user_name;
        $this->account_details = $final;
        $this->other_accounts = $others;
        $this->other_totals = $other_totals;
        $this->final_value = $final_value;
        $this->performance = $performance;
        $this->ref = $ref;
        $this->twr = $twr;
        $this->bar = $bar;
        $this->months = $display_months;
        $this->past_months = $monthly_past;
        $this->month_total = $month_total;
        $this->future_months = $monthly_future;
        $this->month_future_total = $month_future_total;
        $this->future_symbol_totals = $future_symbol_totals;
        $this->sorted_positions = $sorted_positions;
        $this->stylesheet = $stylesheet;
        $this->display_years_current = $display_years_current;
        $this->display_years_projected = $display_years_projected;
        $this->template_file = $template_file;
        $this->pdf_name = $pdf_name;
        $this->pdf = $pdf;
        $this->projected_bar = $projected;
        $this->main_categories = $main_categories;
        $this->sub_sub_categories = $sub_sub_categories;
        $this->grand_total = $grand_total;
        $this->simple_categories = $simple_categories;
        $this->simple_sub_categories = $simple_sub_categories;
        $this->accounts = $accounts;
        $this->account_totals = $account_totals;
        $this->performance_accounts_used = $performance_accounts_used;
    }
    
    /**
     * REF/TWR work the same way
     * @param type $twr_result
     */
    private function ConvertTWR($twr_result){
        $res = array();        
        foreach($twr_result AS $k => $v)
            $res = $v;
        
        return $res;
    }
    
    private function ConvertPerformance($performance_result){
        $performance = array();
        foreach($performance_result AS $k => $v){
            $performance = $v;
        }
        
        return $performance;
    }
    
    private function ConvertPie($pie_result){
        /**
         * Doing it this way allows for multiple account number bits of information <-- Could be used for drag and drop, etc...
         */
        foreach($pie_result AS $k => $v)
        {
            if($v['value'] != 0)
                $pdf_pie[$v['account_number']][$v['title']] = $v['value'];
        }
        
        $tmp = array();
        if(sizeof($pdf_pie) > 0)
        foreach($pdf_pie AS $account => $info)
            foreach($info AS $title => $value){
                $tmp[$title] += $value;
            }
        return $pdf_pie = $tmp;
    }
    
    private function ConvertLine($line_result){
		$lineData = array();
        foreach($line_result as $line_data){
			if(isset($line_data['date']) && $line_data['date'])
				$lineData[] = $line_data;
        }
		return $lineData;
    }
    
    private function ConvertBar($bar_result){
        foreach($bar_result AS $k => $v){
            $pdf_bar[$v['month']] = $v['value'];
        }
        return $pdf_bar;
    }
    
    function CustomizeSettings($settings, $setting){
        $set = explode(",", $setting);
        $settings['account_details'] = 0;
        $settings['pie_chart'] = 0;
        $settings['other_accounts'] = 0;
        $settings['holdings'] = 0;
        $settings['monthly_income'] = 0;
        $settings['performance'] = 0;
        $settings['positions'] = 0;
        
        foreach($set AS $k => $v){
            $settings[$v] = 1;
        }
        
//        $settings[$setting] = 1;

        return $settings;
    }
    
    function CalculateYear($month, $isCurrent=false)
    {
        $current_month = date("m");
        $current_year = date("y");

        if($current_month > $month)
            $year = $current_year+1;
        else
            $year = $current_year;

        if($isCurrent)
            $year -= 1;
        return $year;
    }    
}
?>

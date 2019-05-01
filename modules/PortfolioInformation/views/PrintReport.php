<?php
require_once("libraries/reports/cTransactions.php");
require_once('libraries/reports/cPortfolioDetails.php');
require_once("libraries/reports/cPholdingsInfo.php");
require_once("libraries/reports/pdf/cPDFDBAccess.php");
require_once("libraries/reports/cReports.php");

class PortfolioInformation_PrintReport_View extends Vtiger_BasicAjax_View{
    
    function process(Vtiger_Request $request) {
        $pdf = new PortfolioInformation_PrintReport_Model();
        switch($request->get('report_type')){
            case "overview":
                $pdf->GenerateOverviewReport($request);
                break;
            default:
                $pdf->GenerateReport($request);
        }

        $viewer = $this->getViewer($request);

        $viewer->assign("MAILING_INFO", $pdf->mailing_info);
		$viewer->assign("RENDER_PIE_CHART", $pdf->renderPieChart);
		$viewer->assign("RENDER_LINE_CHART", $pdf->renderLineChart);
		$viewer->assign("RENDER_BAR_CHART", $pdf->renderBarChart);
	
        $viewer->assign("RENDER_HISTORICAL_BAR_CHART", $pdf->renderHistoricalBarChart);
		
		
		$viewer->assign("CLIENT_NAME", $pdf->client_name);
        $viewer->assign("SETTINGS", $pdf->settings);
        $viewer->assign("USER_NAME",$pdf->user_name);
        $viewer->assign("ACCT_DETAILS", $pdf->account_details);
        $viewer->assign("OTHER_ACCOUNTS", $pdf->other_accounts);
        $viewer->assign("OTHER_TOTALS", $pdf->other_totals);
        $viewer->assign("FINAL_VALUE", $pdf->final_value);
        $viewer->assign("PERFORMANCE", $pdf->performance);
        $viewer->assign("REF", $pdf->ref);
        $viewer->assign("TWR", $pdf->twr);
        $viewer->assign("BAR", $pdf->bar);

        $viewer->assign("QTR", $pdf->qtr);
        $viewer->assign("LYR", $pdf->lyr);
        $viewer->assign("YTD", $pdf->ytd);
        $viewer->assign("TRAILING", $pdf->trailing);
        
        $viewer->assign("SHOW_GOAL", $request->get('enable_goal'));
        $viewer->assign("SHOW_TRANSACTIONS", $request->get('enable_transactions'));
        $viewer->assign("SHOW_INCEPTION", $request->get('enable_inception'));
        $viewer->assign("SHOW_EXPENSES", $request->get('enable_expenses'));
        $viewer->assign("SHOW_BREAKDOWN", $request->get('enable_breakdown'));
        $viewer->assign("SIMPLE_CATEGORIES", $pdf->simple_categories);
        $viewer->assign("SIMPLE_SUB_CATEGORIES", $pdf->simple_sub_categories);
        $viewer->assign("MAIN_CATEGORIES", $pdf->main_categories);
        $viewer->assign("SORTED_POSITIONS", $pdf->sorted_positions);
        $viewer->assign("SUB_SUB_CATEGORIES", $pdf->sub_sub_categories);
        $viewer->assign("GRAND_TOTAL", $pdf->grand_total);
        $viewer->assign("MONTHS", $pdf->months);
        $viewer->assign("PAST_MONTHS", $pdf->past_months);
        $viewer->assign("MONTH_TOTAL", $pdf->month_total);
        $viewer->assign("FUTURE_MONTHS", $pdf->future_months);
        $viewer->assign("FUTURE_TOTAL", $pdf->month_future_total);
        $viewer->assign("FUTURE_SYMBOL_TOTALS", $pdf->future_symbol_totals);
        $viewer->assign("DISPLAY_YEARS_CURRENT", $pdf->display_years_current);
        $viewer->assign("DISPLAY_YEARS_PROJECTED", $pdf->display_years_projected);
        $viewer->assign("ACCOUNTS", $pdf->accounts);
        $viewer->assign("ACCOUNT_TOTALS", $pdf->account_totals);
        $viewer->assign("TRANSACTIONS", $pdf->transactions);
        $viewer->assign("PERFORMANCE_ACCOUNTS_USED", $pdf->performance_accounts_used);
        $viewer->assign("ACCOUNT_NAME", $account_name);
        
        $viewer->assign("MAIN_CATEGORIES_PREVIOUS", $pdf->income_report->main_categories_previous);
        $viewer->assign("MAIN_CATEGORIES_PROJECTED", $pdf->income_report->main_categories_projected);
        $viewer->assign("SUB_SUB_CATEGORIES_PREVIOUS", $pdf->income_report->sub_sub_categories_previous);
        $viewer->assign("SUB_SUB_CATEGORIES_PROJECTED", $pdf->income_report->sub_sub_categories_projected);
        $viewer->assign("PROJECTED_SYMBOLS", $pdf->income_report->individual_projected_symbols);
        $viewer->assign("PREVIOUS_SYMBOLS", $pdf->income_report->individual_previous_symbols);
        $viewer->assign("PREVIOUS_SYMBOLS_VALUES", $pdf->income_report->previous_symbols);
        $viewer->assign("PROJECTED_SYMBOLS_VALUES", $pdf->income_report->projected_symbols);
        $viewer->assign("PREVIOUS_MONTHLY_TOTALS", $pdf->income_report->previous_monthly_totals);
        $viewer->assign("PROJECTED_MONTHLY_TOTALS", $pdf->income_report->projected_monthly_totals);
        $viewer->assign("DISPLAY_MONTHS", $pdf->income_report->CalculateDisplayMonths());
        $viewer->assign("PIE_IMAGE", $pdf->pie_image);
			
		$viewer->assign("LOGO", $pdf->logo);
			
//        $template = $viewer->fetch('layouts/vlayout/modules/PortfolioInformation/'.$pdf->template_file);
        $template = $viewer->view($pdf->template_file, 'PortfolioInformation', true);

        $pdf->pdf->WritePDF($pdf->stylesheet, $template);
        echo $pdf->pdf->Output($pdf->pdf_name . ".pdf", "D"); 
			// Unlink Images
		if(file_exists("storage/pdf/positions_pie.png")) unlink("storage/pdf/positions_pie.png");
		if(file_exists("storage/pdf/positions_line.png")) unlink("storage/pdf/positions_line.png");
		if(file_exists("storage/pdf/account_historical.png")) unlink("storage/pdf/account_historical.png");
		if(file_exists("storage/pdf/historical.png")) unlink("storage/pdf/historical.png");
		if(file_exists("storage/pdf/projected.png")) unlink("storage/pdf/projected.png");
    
		if(file_exists("storage/pdf/overview_pie.png")) unlink("storage/pdf/overview_pie.png");
		if(file_exists("storage/pdf/chart.png")) unlink("storage/pdf/chart.png");
		
	
		exit;
        return $pdf->pdf->Output($pdf->pdf_name . ".pdf", "D");
    }
}

?>

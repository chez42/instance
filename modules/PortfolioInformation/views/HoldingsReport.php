<?php
/**
 * Created by PhpStorm.
 * User: rsandnes
 * Date: 2016-07-06
 * Time: 3:55 PM
 */
include_once("libraries/reports/pdf/cNewPDFGenerator.php");
include_once("include/utils/omniscientCustom.php");

class PortfolioInformation_HoldingsReport_View extends Vtiger_Index_View{

	function __construct() {
		parent::__construct();
	}
	
	public function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$record = $request->get('record');

		$recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'DetailView', $record);

		if(!$recordPermission) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
	}
	
	function process(Vtiger_Request $request)
	{
		if($request->get('calling_record')) {
			$calling_instance = Vtiger_Record_Model::getInstanceById($request->get('calling_record'));
			$advisor_instance = Users_Record_Model::getInstanceById($calling_instance->get('assigned_user_id'), "Users");
			$assigned_to = getGroupName($calling_instance->get('assigned_user_id'));
			if(sizeof($assigned_to) == 0)
				$assigned_to = GetUserFirstLastNameByID($calling_instance->get('assigned_user_id'), true);
		}

		if(is_array($assigned_to))
			$assigned_to = $assigned_to[0];

	    $pie_image = 0;
		$graph_image = 0;
		$aum_image = 0;
		$pie_file = "storage/pdf/holdings_pie.png";
		$graph_file = "storage/pdf/holdings_graph.png";
		$aum_file = "storage/pdf/aum_graph.png";
		$revenue_file = "storage/pdf/revenue_graph.png";
		unlink($pie_file);
		unlink($graph_file);
		unlink($aum_file);
		unlink($revenue_file);

		$ispdf = $request->get('pdf');
        if(strlen($request->get('pie_image')) > 0){
        	cNewPDFGenerator::CreateImageFile($pie_file, $request->get('pie_image'));
            $pie_image = 1;
        }

		if(strlen($request->get('graph_image')) > 0){
			cNewPDFGenerator::CreateImageFile($graph_file, $request->get('graph_image'));
			$graph_image = 1;
		}

		if(strlen($request->get('aum_image')) > 0){
			cNewPDFGenerator::CreateImageFile($aum_file, $request->get('aum_image'));
			$aum_image = 1;
		}

		if(strlen($request->get('revenue_image')) > 0){
			cNewPDFGenerator::CreateImageFile($revenue_file, $request->get('revenue_image'));
			$revenue_image = 1;
		}

		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
		$account_number = $request->get("account_number");
        $total_weight = 0;
		if(!is_array($account_number))
			$accounts[] = $account_number;
		else
			$accounts = $account_number;
		$accounts = array_unique($accounts);
		if (sizeof($accounts) > 0) {
####			ModSecurities_Module_Model::FillSecuritiesWithYQLOrXigniteDataForAccount($accounts);
			PortfolioInformation_HoldingsReport_Model::GenerateReportFromAccounts($accounts);
			PortfolioInformation_HoldingsReport_Model::GenerateEstimateTables($accounts);
#			$categories = array("aclass");
#			$fields = array("security_symbol", "account_number", "cusip", "description", "quantity", "last_price", "weight", "current_value");
#            $estimateTable = PortfolioInformation_Reports_Model::GetTable("Holdings", "Estimator", $fields, $categories);
#            print_r($estimateTable['table_values']);
#			$estimatePie = PortfolioInformation_Reports_Model::GetPieFromTable();
			$global_total = cHoldingsReport::GetGlobalTotal();
			$primary = cHoldingsReport::GetGroupedPrimary();
			$secondary = cHoldingsReport::GetGroupedSecondary();
			$positions = cHoldingsReport::GetWeightedPositions();
            $positions = cHoldingsReport::CategorizePositions($positions);
			foreach($positions AS $k => $v)
				$symbols[] = $v['security_symbol'];

			if(sizeof($symbols) > 0)
				$position_information = ModSecurities_Module_Model::GetSecurityInformationFromSymbols($symbols);

			$grouped = cHoldingsReport::GetWeightedPositions(true);
			$grouped = cHoldingsReport::CategorizePositions($grouped);

            $categories = cHoldingsReport::TotalCategories($positions, $total_weight);

            $ac = cHoldingsReport::TotalAssetClass($positions);
			$ac_weight = cHoldingsReport::GetACWeights($ac, $global_total);
			$individual_ac = cHoldingsReport::TotalIndividualizedAssetClass($positions);
			$individual_weight = cHoldingsReport::GetACWeights($individual_ac, $global_total);
			$pie = cHoldingsReport::CreatePieFromPositions($positions);
			$monthly_values = PortfolioInformation_MonthlyIncome_Model::GetMonthyValuesForAccounts($account_number);
		};
		$contact_instance = null;
		if(is_array($accounts)){
			$portfolios = array();
			$unsettled_cash = 0;
			foreach($accounts AS $k => $v) {
				$crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($v);
				if($crmid) {
					$p = PortfolioInformation_Record_Model::getInstanceById($crmid);
					$contact_id = $p->get('contact_link');
					if ($contact_id)
						$contact_instance[$p->get('account_number')] = Contacts_Record_Model::getInstanceById($contact_id);

					$portfolios[] = $p->getData();
					$unsettled_cash += $p->get('unsettled_cash');
					if (!$advisor_instance) {
						$advisor_instance = Users_Record_Model::getInstanceById($p->get('assigned_user_id'), "Users");
					}
				}
			}
		}

		if($contact_instance) {//If there is a contact instance to do anything with
			if(!$advisor_instance)
				$advisor_instance = Users_Record_Model::getInstanceById(reset($contact_instance)->get('assigned_user_id'), "Users");

			$household_instance = null;
			if (reset($contact_instance)->get('account_id'))
				$household_instance = Users_Record_Model::getInstanceById(reset($contact_instance)->get('account_id'));
		}

		$account_info = PortfolioInformation_Module_Model::GetAccountIndividualTotals($accounts);
		$account_info_total = PortfolioInformation_module_Model::GetAccountSumTotals($accounts);

		$mailing_info = PortfolioInformation_Reports_Model::GetMailingInformationForAccount($moduleName, $accounts);

		$colors = PortfolioInformation_Module_Model::GetAllChartColors();
		$current_user = Users_Record_Model::getCurrentUserModel();
		$trailing_aum = PortfolioInformation_HistoricalInformation_Model::GetTrailing12AUM($accounts);
		$trailing_revenue = PortfolioInformation_HistoricalInformation_Model::GetTrailing12Revenue($accounts);

		$data = $advisor_instance->getData();
		$has_advisor = 0;
		if(strlen($data['user_name']) > 0)
			$has_advisor = 1;

		$toc = array();
		$toc[] = array("title" => "#1", "name" => "Accounts Overview");
		$toc[] = array("title" => "#2", "name" => "Month End Values / Asset Mix");
#		$toc[] = array("title" => "#3", "name" => "Balances");
		$toc[] = array("title" => "#4", "name" => "OMNIVue Asset Allocation");
#		$toc[] = array("title" => "#5", "name" => "Balances");
#		$toc[] = array("title" => "#6", "name" => "Securities");

		$viewer->assign("DATE", date("F d, Y"));
		$viewer->assign("ASSIGNED_TO", $assigned_to);
		$viewer->assign("HAS_ADVISOR", $has_advisor);
		$viewer->assign("CONTACTS", $contact_instance);
		$viewer->assign("REPORT_TYPE", "Client Statement");
		$viewer->assign("CURRENT_USER", $current_user);
		$viewer->assign("ADVISOR", $advisor_instance);
		$viewer->assign("HOUSEHOLD", $household_instance);
		$viewer->assign("MAILING_INFO", $mailing_info);
		$viewer->assign("NUM_ACCOUNTS_USED", sizeof($accounts));
		$viewer->assign("PORTFOLIO_DATA", $portfolios);
		$viewer->assign("UNSETTLED_CASH", $unsettled_cash);
        $viewer->assign("PIE_IMAGE", $pie_image);
		$viewer->assign("PIE_FILE", $pie_file);
		$viewer->assign("GRAPH_IMAGE", $graph_image);
		$viewer->assign("GRAPH_FILE", $graph_file);
		$viewer->assign("AUM_IMAGE", $aum_image);
		$viewer->assign("AUM_FILE", $aum_file);
		$viewer->assign("REVENUE_IMAGE", $revenue_image);
		$viewer->assign("REVENUE_FILE", $revenue_file);
        $viewer->assign("COLORS", $colors);
		$viewer->assign("GLOBAL_TOTAL", $global_total);
        $viewer->assign("ASSET_CLASS", $ac);
		$viewer->assign("ASSET_CLASS_WEIGHT", $ac_weight);
		$viewer->assign("INDIVIDUAL_AC", $individual_ac);
		$viewer->assign("INDIVIDUAL_WEIGHT", $individual_weight);
        $viewer->assign("TOTAL_WEIGHT", $total_weight);
		$viewer->assign("CALLING_RECORD", $request->get('calling_record'));
		$viewer->assign("TOC", $toc);
		$viewer->assign("PRIMARY", $primary);
		$viewer->assign("SECONDARY", $secondary);
		$viewer->assign("ACCOUNTINFO", $account_info);
		$viewer->assign("ACCOUNTINFOTOTAL", $account_info_total);
		$viewer->assign("CATEGORIES", $categories);
		$viewer->assign("INDIVIDUAL", $positions);
		$viewer->assign("POSITIONS", $position_information);
		$viewer->assign("GROUPED", $grouped);
		$viewer->assign("ACCOUNT_NUMBER", json_encode($accounts));
		$viewer->assign("MODULE", "PortfolioInformation");

		$viewer->assign("PIE", json_encode($pie));
		$viewer->assign("TRAILING_AUM", json_encode($trailing_aum));
		$viewer->assign("TRAILING_REVENUE", json_encode($trailing_revenue));
		$viewer->assign("MONTHLY_TOTALS", json_encode($monthly_values));
		$viewer->assign("MONTHLY_VALUES", $monthly_values);
		$viewer->assign("RANDOM", rand(1,100000));

		/* === START : Changes For Report Logo 2016-12-07 === */

        $logo = PortfolioInformation_Module_Model::GetLogo();//Set the logo
        $viewer->assign("LOGO", $logo);

		/* === END : Changes For Report Logo 2016-12-07 === */
			
		$pdf_content  = $viewer->fetch('layouts/vlayout/modules/PortfolioInformation/pdf/MailingInfo.tpl', $moduleName);
		$pdf_content .= $viewer->fetch('layouts/vlayout/modules/PortfolioInformation/pdf/TitlePage.tpl', $moduleName);
		$pdf_content .= $viewer->fetch('layouts/vlayout/modules/PortfolioInformation/pdf/TableOfContents.tpl', $moduleName);
		$pdf_content .= $viewer->fetch('layouts/vlayout/modules/PortfolioInformation/pdf/GroupAccounts.tpl', $moduleName);
        $pdf_content .= $viewer->fetch('layouts/vlayout/modules/PortfolioInformation/pdf/page_break.tpl', $moduleName);
#		$pdf_content .= $viewer->fetch('layouts/vlayout/modules/PortfolioInformation/pdf/HoldingsCharts.tpl', $moduleName);
#		$pdf_content .= $viewer->fetch('layouts/vlayout/modules/PortfolioInformation/pdf/TrailingAUM.tpl', $moduleName);
#        $pdf_content .= $viewer->fetch('layouts/vlayout/modules/PortfolioInformation/pdf/DynamicTable.tpl', $moduleName);
        $pdf_content .= $viewer->fetch('layouts/vlayout/modules/PortfolioInformation/pdf/HoldingsSummary.tpl', $moduleName);
		$pdf_content .= $viewer->fetch('layouts/vlayout/modules/PortfolioInformation/pdf/HoldingsReportPDF.tpl', $moduleName);
#        $pdf_content .= $viewer->fetch('layouts/vlayout/modules/PortfolioInformation/pdf/BalancesTable.tpl', $moduleName);
#		$pdf_content .= $viewer->fetch('layouts/vlayout/modules/PortfolioInformation/pdf/TypeTable.tpl', $moduleName);
		$pdf_content .= $viewer->fetch('layouts/vlayout/modules/PortfolioInformation/pdf/disclaimer.tpl', $moduleName);

		if($ispdf)
			$this->GeneratePDF($pdf_content, $logo);
		else
			$viewer->view('HoldingsReport.tpl', $moduleName);
	}

	public function GeneratePDF($content, $logo = false){
		$pdf = new cNewPDFGenerator('c','LETTER-L','8','Arial');
//		$name = $pdf->AutoReportName();
//		$pdf->SetupHeader("storage/logos/".$settings['logo'], $name, $inception);

		$pdf->setAutoBottomMargin = "stretch";
		
		/* === START : Changes For Report Logo 2016-12-07 === */

		if($logo)
			$pdf->logo = $logo;

		/* === END : Changes For Report Logo 2016-12-07 === */
			
		$stylesheet  = file_get_contents('layouts/vlayout/modules/PortfolioInformation/css/HoldingsReport.css');
		$stylesheet .= file_get_contents('layouts/vlayout/modules/PortfolioInformation/css/pdf/GroupAccounts.css');
		$stylesheet .= file_get_contents('layouts/vlayout/modules/PortfolioInformation/css/pdf/TableOfContents.css');
		$stylesheet .= file_get_contents('layouts/vlayout/modules/PortfolioInformation/css/pdf/HoldingsSummary.css');
		$stylesheet .= file_get_contents('layouts/vlayout/modules/PortfolioInformation/css/pdf/BalancesTable.css');
		$stylesheet .= file_get_contents('layouts/vlayout/modules/PortfolioInformation/css/pdf/HoldingsCharts.css');

		$pdf->SetupFooter();
		$pdf->WritePDF($stylesheet, $content);
		$pdf->DownloadPDF("Holdings.pdf");
//		$template_file = "holdings_pdf.tpl";
//		$pdf_name = "holdings.pdf";
	}

	public function getHeaderScripts(Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();
		$jsFileNames = array(
#			"~/libraries/jquery/jquery.min.js",
			"~/libraries/jquery/jquery-ui/js/jquery-ui-1.8.16.custom.min.js",
#			"~/libraries/jquery/jquery.class.min.js",
			"~/libraries/jquery/woco/woco.accordion.min.js",
			"~/libraries/jquery/qtip/jquery.qtip.js",
//          "~/libraries/amcharts/amcharts_3.20.9/amcharts/amcharts.js",
//          "~/libraries/amcharts/amcharts_3.20.9/amcharts/pie.js",
//			"~/libraries/amcharts/amcharts_3.20.9/amcharts/serial.js",
//			"~/libraries/jquery/d3/d3.min.js",
//            "~/libraries/amcharts/amcharts_3.20.9/amcharts/plugins/export/export.min.js",

			"~/libraries/amcharts/amcharts/amcharts.js",
            "~/libraries/amcharts/amcharts/pie.js",
			"~/libraries/amcharts/amcharts/serial.js",
            "~/libraries/amcharts/amcharts/plugins/export/export.min.js",
			
#			"~/libraries/amcharts/2.9.0/amcharts/amcharts.js",
#			"~/libraries/amcharts/2.0.5/amcharts/javascript/raphael.js",
			"modules.$moduleName.resources.NewHoldingsReport", // . = delimiter
		);
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

	public function getHeaderCss(Vtiger_Request $request) {
		$headerCssInstances = parent::getHeaderCss($request);
		$cssFileNames = array(
#			'~/libraries/jquery/woco/woco-accordion.min.css',
			'~/layouts/vlayout/modules/PortfolioInformation/css/pdf/TitlePage.css',
			'~/layouts/vlayout/modules/PortfolioInformation/css/HoldingsReport.css',
			'~/layouts/vlayout/modules/PortfolioInformation/css/pdf/HoldingsSummary.css',
			'~/layouts/vlayout/modules/PortfolioInformation/css/pdf/BalancesTable.css',
#			'~/libraries/jquery/qtip/jquery.qtip.css',
//          "~/libraries/amcharts/amcharts_3.20.9/amcharts/plugins/export/export.css",
		    "~/libraries/amcharts/amcharts/plugins/export/export.css",
		);
		$cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
		$headerCssInstances = array_merge($headerCssInstances, $cssInstances);
		return $headerCssInstances;
	}
}

?>
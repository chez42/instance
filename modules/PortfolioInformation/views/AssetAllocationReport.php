<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2017-02-09
 * Time: 4:07 PM
 */

include_once("libraries/reports/pdf/cNewPDFGenerator.php");
include_once("include/utils/omniscientCustom.php");

class PortfolioInformation_AssetAllocationReport_View extends Vtiger_Index_View{

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
        $pie_file = "storage/pdf/dynamic_pie.png";
        unlink($pie_file);

        $ispdf = $request->get('pdf');
        if(strlen($request->get('pie_image')) > 0){
            cNewPDFGenerator::CreateImageFile($pie_file, $request->get('pie_image'));
            $pie_image = 1;
        }

        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $account_number = $request->get("account_number");

        $total_weight = 0;
        if(!is_array($account_number))
            $accounts = explode(",", $request->get("account_number"));
        else {
            $accounts = $account_number;
        }
        $accounts = array_unique($accounts);

        if (sizeof($accounts) > 0) {
            PortfolioInformation_HoldingsReport_Model::GenerateEstimateTables($accounts);
            $categories = array("estimatedtype");
            $fields = array("security_symbol", "account_number", "cusip", "description", "quantity", "last_price", "weight", "current_value");
            $totals = array("current_value", "weight");
            $estimateTable = PortfolioInformation_Reports_Model::GetTable("Holdings", "Estimator", $fields, $categories);
            $estimatePie = PortfolioInformation_Reports_Model::GetPieFromTable();
            $estimateTable['TableTotals'] = PortfolioInformation_Reports_Model::GetTableTotals("Estimator", $totals);
#            print_r($estimateTable['table_categories']);
#            echo "<br /><br />";
            $category_totals = PortfolioInformation_Reports_Model::GetTableCategoryTotals("Estimator", $categories, $totals);
            PortfolioInformation_reports_model::MergeTotalsIntoCategoryRows($categories, $estimateTable, $category_totals);

            global $adb;
            $query = "SELECT @global_total as global_total";
            $result = $adb->pquery($query, array());
            if($adb->num_rows($result) > 0){
                $global_total = $adb->query_result($result, 0, 'global_total');
            }
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
                        echo "NO INSTANCE!";
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
        $toc[] = array("title" => "#2", "name" => "Asset Allocation");

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
        $viewer->assign("DYNAMIC_PIE_FILE", $pie_file);
        $viewer->assign("COLORS", $colors);
        $viewer->assign("TOTAL_WEIGHT", $total_weight);
        $viewer->assign("CALLING_RECORD", $request->get('calling_record'));
        $viewer->assign("TOC", $toc);
        $viewer->assign("ACCOUNTINFO", $account_info);
        $viewer->assign("ACCOUNTINFOTOTAL", $account_info_total);
        $viewer->assign("CATEGORIES", $categories);
        $viewer->assign("ACCOUNT_NUMBER", json_encode($accounts));
        $viewer->assign("MODULE", "PortfolioInformation");

        //$viewer->assign("SCRIPTS", $this->getCustomScripts($request));
        //$viewer->assign("STYLES", $this->getHeaderCss($request));

        $viewer->assign("CATEGORY_TOTALS", $category_totals);
        $viewer->assign("ESTIMATE_TABLE", $estimateTable);
        $viewer->assign("DYNAMIC_PIE", json_encode($estimatePie));
        $viewer->assign("GLOBAL_TOTAL", array("global_total" => $global_total));
        $viewer->assign("TRAILING_AUM", json_encode($trailing_aum));
        $viewer->assign("TRAILING_REVENUE", json_encode($trailing_revenue));
        $viewer->assign("RANDOM", rand(1,100000));

        /* === START : Changes For Report Logo 2016-12-07 === */

        $logo = $current_user->getImageDetails();
        if(isset($logo['user_logo']) && !empty($logo['user_logo'])){
            if(isset($logo['user_logo'][0]) && !empty($logo['user_logo'][0])){
                $logo = $logo['user_logo'][0];
                $logo = $logo['path']."_".$logo['name'];
            } else
                $logo = 0;
        } else
            $logo = "";

        if($logo == "_")
            $logo = "test/logo/Omniscient Logo small.png";
        $viewer->assign("LOGO", $logo);

        /* === END : Changes For Report Logo 2016-12-07 === */

        if($ispdf) {
            $pdf_content  = $viewer->fetch('layouts/vlayout/modules/PortfolioInformation/pdf/MailingInfo.tpl', $moduleName);
            $pdf_content .= $viewer->fetch('layouts/vlayout/modules/PortfolioInformation/pdf/TitlePage.tpl', $moduleName);
            $pdf_content .= $viewer->fetch('layouts/vlayout/modules/PortfolioInformation/pdf/TableOfContents.tpl', $moduleName);
            $pdf_content .= $viewer->fetch('layouts/vlayout/modules/PortfolioInformation/pdf/GroupAccounts.tpl', $moduleName);
            $pdf_content .= $viewer->fetch('layouts/vlayout/modules/PortfolioInformation/pdf/page_break.tpl', $moduleName);
            $pdf_content .= $viewer->fetch('layouts/vlayout/modules/PortfolioInformation/pdf/DynamicPie.tpl', $moduleName);
            $pdf_content .= $viewer->fetch('layouts/vlayout/modules/PortfolioInformation/pdf/DynamicHoldings.tpl', $moduleName);
            $pdf_content .= $viewer->fetch('layouts/vlayout/modules/PortfolioInformation/pdf/disclaimer.tpl', $moduleName);

            $this->GeneratePDF($pdf_content, $logo);
        }
        else
            $viewer->view('AssetAllocationReport.tpl', $moduleName);
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
            "~/libraries/jquery/jquery-ui/js/jquery-ui-1.8.16.custom.min.js",
            /*"~/libraries/amcharts/amcharts_3.20.9/amcharts/amcharts.js",
            "~/libraries/amcharts/amcharts_3.20.9/amcharts/pie.js",
            "~/libraries/amcharts/amcharts_3.20.9/amcharts/serial.js",
            "~/libraries/amcharts/amcharts_3.20.9/amcharts/plugins/export/export.min.js",
			*/
			
			"~/libraries/amcharts/amcharts/amcharts.js",
			"~/libraries/amcharts/amcharts/pie.js",
			"~/libraries/amcharts/amcharts/serial.js",
			"~/libraries/amcharts/amcharts/plugins/export/export.min.js",
			
            "modules.$moduleName.resources.DynamicPie",
            "modules.$moduleName.resources.AssetAllocationReport",
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);
		$cssFileNames = array(
            '~/layouts/vlayout/modules/PortfolioInformation/css/pdf/TitlePage.css',
            '~/layouts/vlayout/modules/PortfolioInformation/css/HoldingsReport.css',
            '~/layouts/vlayout/modules/PortfolioInformation/css/pdf/HoldingsSummary.css',
            '~/layouts/vlayout/modules/PortfolioInformation/css/pdf/BalancesTable.css',
            "~/libraries/amcharts/amcharts/plugins/export/export.css",
//          "~/libraries/amcharts/amcharts_3.20.9/amcharts/plugins/export/export.css",
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
		$headerCssInstances = array_merge($headerCssInstances, $cssInstances);
		return $headerCssInstances;
    }
}

?>
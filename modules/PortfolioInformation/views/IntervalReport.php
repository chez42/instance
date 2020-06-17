<?php
/**
 * Created by PhpStorm.
 * User: rsandnes
 * Date: 2016-07-06
 * Time: 3:55 PM
 */
include_once("libraries/reports/pdf/cNewPDFGenerator.php");
include_once("include/utils/omniscientCustom.php");

class PortfolioInformation_IntervalReport_View extends Vtiger_Index_View{

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
        $account_numbers = $request->get('account_numbers');
        $accounts = explode(",", $account_numbers);

        $accounts = PortfolioInformation_Module_Model::ReturnValidAccountsFromArray($accounts);

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $report_type = $request->get('report_type');
        if(strlen($report_type) == 0)
            $report_type = 'monthly';

        $calculated_return = str_replace("%", "", $request->get('calculated_return'));

        //Create the interval table to be used base don the dates.  Either monthly or daily
        if($report_type == 'daily')
            $intervals = PortfolioInformation_Module_Model::GetDailyIntervalsForAccountsWithDateFilter($accounts, $start_date, $end_date);
        else
            $intervals = PortfolioInformation_Module_Model::GetIntervalsForAccounts($accounts, $start_date, $end_date);

        $summarized = PortfolioInformation_Module_Model::GetSummerizedIntervalInfo($accounts, $start_date, $end_date, $report_type);

        if($request->get('calling_record')) {
            $calling_instance = Vtiger_Record_Model::getInstanceById($request->get('calling_record'));
            $advisor_instance = Users_Record_Model::getInstanceById($calling_instance->get('assigned_user_id'), "Users");
            $assigned_to = getGroupName($calling_instance->get('assigned_user_id'));
            if(sizeof($assigned_to) == 0)
                $assigned_to = GetUserFirstLastNameByID($calling_instance->get('assigned_user_id'), true);
        }

        $account_totals = PortfolioInformation_Module_Model::GetAccountSumTotals($accounts);
        $account_totals['global_total'] = $account_totals['total'];

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

        if(is_array($assigned_to))
            $assigned_to = $assigned_to[0];

        $moduleName = $request->getModule();
        $current_user = Users_Record_Model::getCurrentUserModel();

        $toc = array();
        $toc[] = array("title" => "#1", "name" => "Accounts Overview");
        $toc[] = array("title" => "#2", "name" => "Interval Report");


        $image = cNewPDFGenerator::TextToImage($request->get('image'));
        $image = '<img src="data:image/jpeg;base64,'.base64_encode( $image ).'" />';
//        $image = base64_encode( $image );
        $viewer = $this->getViewer($request);
        $viewer->assign("IMAGE", $image);

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
        $viewer->assign("TOC", $toc);
        $viewer->assign("PORTFOLIO_DATA", $portfolios);
        $viewer->assign("GLOBAL_TOTAL", $account_totals);
        $viewer->assign("CALCULATED_RETURN", $calculated_return);
        $viewer->assign('INTERVALS', $intervals);
        $viewer->assign("SUMMARIZED", $summarized);

/*        $pdf_content  = $viewer->fetch('layouts/vlayout/modules/PortfolioInformation/pdf/MailingInfo.tpl', $moduleName);
        $pdf_content .= $viewer->fetch('layouts/vlayout/modules/PortfolioInformation/pdf/TitlePage.tpl', $moduleName);*/
        $pdf_content = $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf/TableOfContents.tpl', $moduleName);
        $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf/GroupAccounts.tpl', $moduleName);
        $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf/page_break.tpl', $moduleName);
        $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/IntervalReport.tpl', $moduleName);
        $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf/page_break.tpl', $moduleName);
        $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf/disclaimer.tpl', $moduleName);

        $this->GeneratePDF($pdf_content, $logo);
//        $viewer->view('IntervalReport.tpl', $moduleName);
    }

    public function GeneratePDF($content, $logo = false){
        $pdf = new cNewPDFGenerator('c','LETTER-L','8','Arial');
//		$name = $pdf->AutoReportName();
//		$pdf->SetupHeader("storage/logos/".$settings['logo'], $name, $inception);


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
        $pdf->DownloadPDF("Intervals.pdf");
//		$template_file = "holdings_pdf.tpl";
//		$pdf_name = "holdings.pdf";
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "~/libraries/jquery/jquery.min.js",
            "~/libraries/jquery/jquery-ui/js/jquery-ui-1.8.16.custom.min.js",
            "~/libraries/jquery/jquery.class.min.js",
            "~/libraries/jquery/woco/woco.accordion.min.js",
            "~/libraries/jquery/qtip/jquery.qtip.js",
//            "~/libraries/amcharts/amcharts_3.20.9/amcharts/amcharts.js",
//            "~/libraries/amcharts/amcharts_3.20.9/amcharts/pie.js",
//            "~/libraries/amcharts/amcharts_3.20.9/amcharts/serial.js",

			"~/libraries/amcharts/amcharts/amcharts.js",
            "~/libraries/amcharts/amcharts/pie.js",
            "~/libraries/amcharts/amcharts/serial.js",

			//			"~/libraries/jquery/d3/d3.min.js",
//            "~/libraries/amcharts/amcharts_3.20.9/amcharts/plugins/export/export.min.js",
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
            '~/libraries/jquery/woco/woco-accordion.min.css',
            '~/layouts/vlayout/modules/PortfolioInformation/css/pdf/TitlePage.css',
            '~/layouts/vlayout/modules/PortfolioInformation/css/HoldingsReport.css',
            '~/layouts/vlayout/modules/PortfolioInformation/css/pdf/HoldingsSummary.css',
            '~/layouts/vlayout/modules/PortfolioInformation/css/pdf/BalancesTable.css',
            '~/libraries/jquery/qtip/jquery.qtip.css',
            "~/libraries/amcharts/amcharts/plugins/export/export.css",
//          "~/libraries/amcharts/amcharts_3.20.9/amcharts/plugins/export/export.css",
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);
        return $headerCssInstances;
    }
}

?>
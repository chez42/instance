<?php
require_once("libraries/Reporting/ReportCommonFunctions.php");
require_once("libraries/Reporting/ReportPerformance.php");
require_once("libraries/Reporting/ProjectedIncomeModel.php");
require_once("modules/PortfolioInformation/models/NameMapper.php");
require_once("libraries/reports/pdf/cMpdf7.php");

#require_once("libraries/reports/pdf/cNewPDFGenerator.php");

class PortfolioInformation_OmniProjected_View extends Vtiger_Index_View{

    /*    function preProcessTplName(Vtiger_Request $request) {
            return 'PortfolioReportsPerProcess.tpl';
        }*/
    
    public function postProcess(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $viewer = $this->getViewer($request);
        $viewer->view('PortfolioReportsPostProcess.tpl', $moduleName);
        
        parent::postProcess($request);
    }
    
    function process(Vtiger_Request $request) {
        $calling_module = $request->get('calling_module');
        $calling_record = $request->get('calling_record');

        if(strlen($request->get("account_number") > 0) || strlen($calling_module) >= 0){
            $accounts = explode(",", $request->get("account_number"));
            $accounts = array_unique($accounts);

            $start_date = GetDateFirstOfThisMonth();
            $end_date = GetDateLastOfPreviousMonthPlusOneYear();

            $positions = PositionInformation_Module_Model::GetPositionsForAccountNumber($accounts);
/*
            foreach($positions AS $k => $v) {
                //TODO:  Get last EOD date.  If it is less than today - 3 months, call UpdateSecurityFromEOD
                $crmid = ModSecurities_Module_Model::GetCrmidFromSymbol($v['security_symbol']);
                if($crmid > 0) {
                    $instance = ModSecurities_Record_Model::getInstanceById($crmid);
                    $data = $instance->getData();

                    $returned = Date("Y-m-d", strtotime($data['last_eod']));
                    $compared = Date("Y-m-d", strtotime("-3 months"));
#                    if ($returned <= $compared)
                        ModSecurities_ConvertCustodian_Model::UpdateSecurityFromEOD($v['security_symbol'], "US");
                }
            }*/

            $projected = new ProjectedIncome_Model($accounts, $end_date);
            $calendar = CreateMonthlyCalendar($start_date, $end_date);
            $projected->CalculateMonthlyTotals($calendar);
            $graph = $projected->GetMonthlyIncomeGraph();

            $viewer = $this->getViewer($request);

            $viewer->assign("ACCOUNT_NUMBER", $request->get("account_number"));
            $viewer->assign("PROJECTED_INCOME", $projected);
            $viewer->assign("PROJECTED_GRAPH", json_encode($graph));
            $viewer->assign("GRAND_TOTAL", $projected->GetGrandTotal());
            $viewer->assign("CALENDAR", $calendar);
            $viewer->assign("CALLING_RECORD", $calling_record);

            $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));

            $ispdf = $request->get('pdf');

            $logo = PortfolioInformation_Module_Model::GetLogo();//Set the logo
            $viewer->assign("LOGO", $logo);

            if($ispdf) {
                if (strlen($request->get('pie_image')) > 0) {
#                    $pie_image = cNewPDFGenerator::TextToImage($request->get('pie_image'));
                    $pie_image = cMpdf7::TextToImage($request->get('pie_image'));
                    $pie_image = '<img src="data:image/jpg;base64,' . base64_encode($pie_image) . '" />';
                    $viewer->assign("PIE_IMAGE", $pie_image);
                }
                if (strlen($request->get('graph_image')) > 0) {
#                    $graph_image = cNewPDFGenerator::TextToImage($request->get('graph_image'));
                    $graph_image = cMpdf7::TextToImage($request->get('graph_image'));
                    $graph_image = '<img src="data:image/jpg;base64,' . base64_encode($graph_image) . '" />';
                    $viewer->assign("GRAPH_IMAGE", $graph_image);
                }

                $moduleName = $request->getModule();
                $current_user = Users_Record_Model::getCurrentUserModel();

                $account_totals = PortfolioInformation_Module_Model::GetAccountSumTotals($accounts);
                $account_totals['global_total'] = $account_totals['total'];

                if(is_array($accounts)){
                    $portfolios = array();
                    foreach($accounts AS $k => $v) {
                        $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($v);
                        if($crmid) {
                            $p = PortfolioInformation_Record_Model::getInstanceById($crmid);
                            $portfolios[] = $p->getData();
                        }
                    }
                }
                $viewer->assign("PORTFOLIO_DATA", $portfolios);
                $viewer->assign("GLOBAL_TOTAL", $account_totals);

                $toc = array();
                $toc[] = array("title" => "#1", "name" => "Accounts Overview");
                $toc[] = array("title" => "#2", "name" => "Income");
                $viewer->assign("TOC", $toc);

                $pdf_content = $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf/TableOfContents.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf/GroupAccounts.tpl', $moduleName);
                $pdf_content .= '<div class="graph_image" style="width:100%; display:block; margin-left:auto; margin-right:auto; margin-top:10mm;">
    ' . $graph_image . '
</div>';
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf/page_break.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf/OmniProjectedPDF.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf/page_break.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf/disclaimer_landscape.tpl', $moduleName);
                $this->GeneratePDF($pdf_content, $logo, "LETTER", $calling_record);
            }else {
#                $viewer->view('OmniOverview.tpl', "PortfolioInformation");
                $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
                $screen_content = $viewer->fetch('layouts/v7/modules/PortfolioInformation/OmniProjected.tpl', "PortfolioInformation");
                echo $screen_content;
            }
        } else
            return "<div class='ReportBottom'></div>";
}

    public function GeneratePDF($content, $logo = false, $orientation = 'LETTER', $calling_record){
//        $pdf = new cNewPDFGenerator('c','LETTER-L','8','Arial');
        $pdf = new cMpdf7(array('orientation' => 'L'));
        if($logo)
            $pdf->logo = $logo;

        $stylesheet  = file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/GroupAccounts.css');
        $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/TableOfContents.css');
        $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/ProjectedPDF.css');

        $pdf->SetupFooter();
        $pdf->WritePDF($stylesheet, $content);
        $printed_date = date("mdY");
        $pdf->DownloadPDF( GetClientNameFromRecord($calling_record) . "_" . $printed_date . "_ProjectedIncome.pdf");
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "~/libraries/jquery/jquery-ui/js/jquery-ui-1.8.16.custom.min.js",
            "~/libraries/amcharts/amcharts/amcharts.js",
            "~/libraries/amcharts/amcharts/serial.js",
            "~/libraries/amcharts/amcharts/pie.js",
            "~/libraries/amcharts/amcharts/plugins/export/export.js",
#            "~/libraries/amcharts/2.0.5/amcharts/javascript/raphael.js",
            "~/libraries/jquery/acollaptable/jquery.aCollapTable.min.js",
#            "modules.PortfolioInformation.resources.DynamicChart",
            "modules.PortfolioInformation.resources.DynamicPie",
            "modules.$moduleName.resources.printing",
            "modules.$moduleName.resources.jqueryIdealforms",
            "modules.$moduleName.resources.OmniProjected",
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }

    private function GenerateTableCategories($merged_transaction_types){
        $table = array();
        foreach($merged_transaction_types AS $k => $v){
#                print_r($v);
            $vals = array_unique($v);
            $table[$k] = $vals;
        }
        return $table;
    }
}
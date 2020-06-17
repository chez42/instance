<?php
require_once("libraries/Reporting/ReportCommonFunctions.php");
require_once("libraries/Reporting/ReportIncome.php");
require_once("libraries/reports/pdf/cNewPDFGenerator.php");

class PortfolioInformation_LastYearIncome_View extends Vtiger_Index_View{

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

            $income = new Income_Model($accounts);
            $individual = $income->GetIndividualIncomeForDates(GetFirstDayLastYear(), GetLastDayLastYear());
            $monthly = $income->GetMonthlyTotalForDates(GetFirstDayLastYear(), GetLastDayLastYear());
            $graph = $income->GenerateGraphForDates(GetFirstDayLastYear(), GetLastDayLastYear());
            $combined = $income->GetCombinedSymbolsForDates(GetFirstDayLastYear(), GetLastDayLastYear());

            $year_end_totals = $income->CalculateCombineSymbolsYearEndToal(GetFirstDayLastYear(), GetLastDayLastMonth());
            $grand_total = $income->CalculateGrandTotal(GetFirstDayLastYear(), GetLastDayLastMonth());

            $start_month = date("F, Y", strtotime(GetFirstDayLastYear()));
            $end_month = date("F, Y", strtotime(GetLastDayLastYear()));

            $viewer = $this->getViewer($request);

            $viewer->assign("START_MONTH", $start_month);
            $viewer->assign("END_MONTH", $end_month);
            $viewer->assign("MONTHLY_TOTALS", $monthly);
            $viewer->assign("COMBINED_SYMBOLS", $combined);
            $viewer->assign("YEAR_END_TOTALS", $year_end_totals);
            $viewer->assign("GRAND_TOTAL", $grand_total);
            $viewer->assign("DYNAMIC_GRAPH", json_encode($graph));
            $viewer->assign("ACCOUNT_NUMBER", $request->get("account_number"));
            $viewer->assign("CALLING_RECORD", $calling_record);

            $ispdf = $request->get('pdf');

            if($ispdf) {
                if (strlen($request->get('pie_image')) > 0) {
                    $pie_image = cNewPDFGenerator::TextToImage($request->get('pie_image'));
                    $pie_image = '<img src="data:image/jpg;base64,' . base64_encode($pie_image) . '" />';
                    $viewer->assign("PIE_IMAGE", $pie_image);
                }
                if (strlen($request->get('graph_image')) > 0) {
                    $graph_image = cNewPDFGenerator::TextToImage($request->get('graph_image'));
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

                $pdf_content = $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf/TableOfContents.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf/GroupAccounts.tpl', $moduleName);
                $pdf_content .= '<div class="graph_image" style="width:220mm; height:80mm; display:block; margin-left:auto; margin-right:auto; margin-top:10mm;">
    ' . $graph_image . '
</div>';
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf/page_break.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf/LastYearIncomePDF.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf/page_break.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf/disclaimer.tpl', $moduleName);
                $this->GeneratePDF($pdf_content, $logo, $calling_record);
            }else {
#                $viewer->view('OmniOverview.tpl', "PortfolioInformation");
                $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
                $screen_content = $viewer->fetch('layouts/v7/modules/PortfolioInformation/LastYearIncome.tpl', "PortfolioInformation");
                echo $screen_content;
            }
        } else
            return "<div class='ReportBottom'></div>";
    }

    public function GeneratePDF($content, $logo = false, $calling_record){
        $pdf = new cNewPDFGenerator('c','LETTER-L','8','Arial');

        if($logo)
            $pdf->logo = $logo;

        $stylesheet  = file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/GroupAccounts.css');
        $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/TableOfContents.css');
        $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/IncomePDF.css');

        $pdf->SetupFooter();
        $pdf->WritePDF($stylesheet, $content);
        $printed_date = date("mdY");
        $pdf->DownloadPDF( GetClientNameFromRecord($calling_record) . "_" . $printed_date . "_LastYearIncome.pdf");
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
            "modules.$moduleName.resources.OmniIncome",
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
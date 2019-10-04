<?php

class PortfolioInformation_LoadPortfolioReport_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        $report_display = $request->get("report");
        $calling_module = $request->get("calling_module");
        $calling_record = $request->get("calling_record");
//        $top = new PortfolioInformation_ReportTop_View();
//        echo $top->process($request);
        switch($report_display){
            case "holdings":
                $info = new PortfolioInformation_Positions_View();                
                break;
            case "monthly_income":
                $info = new PortfolioInformation_MonthlyIncome_View();
                break;
            case "performance":
                $info = new PortfolioInformation_Performance_View();
                break;
            case "overview":
                $info = new PortfolioInformation_Overview_View();
                break;
            default:
                echo "Generation Error";
                break;
        }
        echo $info->process($request);
    }
}
?>
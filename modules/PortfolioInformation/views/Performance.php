<?php

class PortfolioInformation_Performance_View extends Vtiger_BasicAjax_View{
    
    function process(Vtiger_Request $request) {
        if(strlen($request->get("account_number") > 0) || strlen($request->get('calling_module')) >= 0){
            $holdings = new PortfolioInformation_Performance_Model();
            $holdings->GenerateReport($request);
            
            $viewer = $this->getViewer($request);
            
            $viewer->assign("GOAL", $holdings->goal);
            $viewer->assign("ACCOUNT", $holdings->account);
            $viewer->assign("DISCLAIMER_WARNING", $holdings->warning);    
            $viewer->assign("INCEPTION_IRR", $request->get('TWR_INCEPTION'));
            $viewer->assign("QTR_IRR", $request->get('TWR_QTR'));
            $viewer->assign("YTD_IRR", $request->get('TWR_YTD'));
            $viewer->assign("TRAILING_IRR", $request->get('TWR_TRAILING'));
            $viewer->assign("INCEPTION", $holdings->inception);
            $viewer->assign("QTR_REF", $holdings->qtr_ref);
            $viewer->assign("TRAILING_REF", $holdings->trailing_ref);
            $viewer->assign("YTD_REF", $holdings->ytd_ref);
            $viewer->assign("INCEPTION_REF", $holdings->inception_ref);
            $viewer->assign("YTD_BAB", $holdings->ytd_bab);
            $viewer->assign("QTR_BAB", $holdings->qtr_bab);
            $viewer->assign("TRAILING_BAB", $holdings->trailing_bab);
            $viewer->assign("INCEPTION_BAB", $holdings->inception_bab);
            $viewer->assign("PIDS", $holdings->pids);
            $viewer->assign("DATE", date("m/d/Y"));
            $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
            $viewer->assign("management_fees", $holdings->management_fees);
            $viewer->assign("CALLING_RECORD", $request->get('calling_record'));
            $output = $viewer->view('Performance.tpl', "PortfolioInformation", true);
            return $output;
        } else
            return "<div class='ReportBottom'></div>";
    }
    
    public function getHeaderScripts(Vtiger_Request $request) {
            $moduleName = $request->getModule();
            $jsFileNames = array(
                "modules.$moduleName.resources.performance", // . = delimiter
            );
            $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
            return $jsScriptInstances;
    }
}

?>
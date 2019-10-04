<?php

class PortfolioInformation_Overview_View extends Vtiger_Index_View{
    
    function process(Vtiger_Request $request) {
        $calling_module = $request->get('calling_module');
        $calling_record = $request->get('calling_record');
        if(strlen($request->get("account_number") > 0) || strlen($calling_module) >= 0){
            $overview = new PortfolioInformation_Overview_Model();

            $overview->GenerateReport($request);
            
            $viewer = $this->getViewer($request);

            $viewer->assign("CLIENT_NAME", $overview->client_name);
            $viewer->assign("GOAL", $overview->goal);
            $viewer->assign("TRANSACTIONS", $overview->transactions);
            $viewer->assign("management_fees", $holdings->management_fees);
            $viewer->assign("INCEPTION", $overview->inception);
            $viewer->assign("TRAILING", $overview->trailing);
            $viewer->assign("QTR", $overview->qtr);
            $viewer->assign("LYR", $overview->lyr);
            $viewer->assign("YTD", $overview->ytd);
            $viewer->assign("ACCOUNTINFO", $overview->account_info);
            $viewer->assign("DISCLAIMER_WARNING", $overview->warning);
            $viewer->assign("AS_OF", $overview->as_of);
            $viewer->assign("INCEPTION_IRR", $overview->inception_irr);
            $viewer->assign("QTR_IRR", $overview->qtr_irr);
            $viewer->assign("TRAILING_IRR", $overview->trailing_irr);
            $viewer->assign("YTD_IRR", $overview->ytd_irr);
            $viewer->assign("INCEPTION_REF", $overview->inception_ref);
            $viewer->assign("QTR_REF", $overview->qtr_ref);
            $viewer->assign("TRAILING_REF", $overview->trailing_ref);
            $viewer->assign("YTD_REF", $overview->ytd_ref);
            $viewer->assign("PIDS", $overview->pids);
            $viewer->assign("ACCOUNT", $overview->account);
            $viewer->assign("YTD_BAB", $overview->ytd_bab);
            $viewer->assign("QTR_BAB", $overview->qtr_bab);
            $viewer->assign("TRAILING_BAB", $overview->trailing_bab);
            $viewer->assign("INCEPTION_BAB", $overview->inception_bab);
            $viewer->assign("VALUEHISTORY", json_encode($overview->value_history));
            $viewer->assign("HOLDINGSCHART", json_encode($overview->content));
			$viewer->assign("ALL_ACCOUNTS", $overview->all_account_numbers);
            //$viewer->assign("SCRIPTS", $this->getHeaderScripts($request));

            $viewer->view('Overview.tpl', "PortfolioInformation");
			
        } else
            return "<div class='ReportBottom'></div>";
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

//          "~/libraries/amcharts/amcharts_3.20.9/amcharts/amcharts.js",
//          "~/libraries/amcharts/amcharts_3.20.9/amcharts/serial.js",
//          "~/libraries/amcharts/amcharts_3.20.9/amcharts/pie.js",
//          "~/libraries/amcharts/amcharts_3.20.9/amcharts/plugins/export/export.js",
#           "~/libraries/amcharts/2.9.0/amcharts/amcharts.js",
//        	"~/libraries/amcharts/2.0.5/amcharts/javascript/raphael.js",

         	"modules.$moduleName.resources.printing",
            "modules.$moduleName.resources.holdingschart",
            "modules.$moduleName.resources.performance",
           	"modules.$moduleName.resources.transactions",
           	"modules.$moduleName.resources.monthly_income",
           	"modules.$moduleName.resources.overview",
		   	"modules.$moduleName.resources.jqueryIdealforms",
		);
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
    }
}

?>
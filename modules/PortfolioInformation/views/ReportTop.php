<?php
require_once("include/utils/omniscientCustom.php");
require_once("libraries/reports/cTransactions.php");
require_once('libraries/reports/cPortfolioDetails.php');
require_once("libraries/reports/cPholdingsInfo.php");
require_once("libraries/reports/pdf/cPDFDBAccess.php");
require_once("libraries/reports/cReports.php");
class PortfolioInformation_ReportTop_View extends Vtiger_BasicAjax_View{
    
    function process(Vtiger_Request $request) {
		$account_number = $request->get('account_number');
		$t = new PortfolioInformation_PCQuery_Model();
		$pc = $t->DoesAccountExistInPC($account_number);
		if($pc)
			return $this->PCView($request);
		else
			return $this->OmniView($request);
    }

    public function OmniView(Vtiger_Request $request){
    	$account_number = $request->get('account_number');
    	$record = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($request->get('account_number'));
		$instance = PortfolioInformation_Record_Model::getInstanceById($record);
		$data = $instance->getData();

		$viewer = $this->getViewer($request);

		$viewer->assign("DATE",  date("m/d/Y"));
		$viewer->assign("PORT_INFO", $data);
		$viewer->assign('SCRIPTS', $this->getHeaderScriptsOmni($request));
		$viewer->assign('STYLES', $this->getHeaderCss($request));
		$output = $viewer->view('ReportTopOmni.tpl', "PortfolioInformation", true);

		return $output;
//		print_r($data);
	}

    public function PCView(Vtiger_Request $request){
		$report_top = new PortfolioInformation_ReportTop_Model();

		if(strlen($request->get("account_number")) > 0)
			$account_number = $request->get("account_number");
		else
			if(strlen($request->get('acct')) > 0)
				$account_number = $request->get('acct');

		if($report_top->VerifyAccountNumber($account_number)){
			$report_top->GenerateReport($account_number);
			$viewer = $this->getViewer($request);
			$viewer->assign("FINAL_VALUE", $report_top->final);
			$viewer->assign("GRANDTOTALS", $report_top->account_totals);

			$viewer->assign("ACCOUNTNUMBER", $report_top->account_number);
			$viewer->assign("ACCT_DETAILS", $report_top->account_info);
			$viewer->assign("DATE", $report_top->date);
			$viewer->assign("ACCTNUM", $report_top->account_number);
			$viewer->assign("DIRECTION", $request->get('direction'));
			$viewer->assign("CUSTOM_SEARCH", $request->get('customSearch'));
			$viewer->assign("SUMMARY_INFO", $report_top->summary_info);
			$viewer->assign("UPDATES", $report_top->updates);

			$viewer->assign("SHOWDETAILS", true);

			$viewer->assign("CHARTDATA", $report_top->chart_data);
			$viewer->assign('SCRIPTS', $this->getHeaderScriptsPC($request));
			$output = $viewer->view('ReportTop.tpl', "PortfolioInformation", true);
			return $output;
		} else
			return "<div class='ReportTop'></div>";
	}

    // Injecting custom javascript resources
    public function getHeaderScriptsPC(Vtiger_Request $request) {
            $headerScriptInstances = parent::getHeaderScripts($request);
            $moduleName = $request->getModule();
            $jsFileNames = array(
                // "~/libraries/amcharts/2.9.0/amcharts/amcharts.js",
                // "~/libraries/amcharts/2.0.5/amcharts/javascript/raphael.js",
				
				"~/libraries/amcharts/amcharts/amcharts.js",
				"~/libraries/amcharts/amcharts/pie.js",
				"~/libraries/amcharts/amcharts/serial.js",
			
                "modules.$moduleName.resources.topReportNavigation",
                "modules.$moduleName.resources.reportTop", // . = delimiter
            );
            $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
            $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
            return $headerScriptInstances;
    }

	public function getHeaderScriptsOmni(Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();
		$jsFileNames = array(
		    // "~/libraries/amcharts/amcharts_3.20.9/amcharts/plugins/export/export.js",
			// "~/libraries/amcharts/amcharts_3.20.9/amcharts/amcharts.js",

			"~/libraries/amcharts/amcharts/amcharts.js",
            "~/libraries/amcharts/amcharts/pie.js",
            "~/libraries/amcharts/amcharts/serial.js",
			"~/libraries/amcharts/amcharts/plugins/export/export.js",

#			"~/libraries/amcharts/2.0.5/amcharts/javascript/raphael.js",
			"modules.$moduleName.resources.topReportNavigation",
			"modules.$moduleName.resources.reportTopOmni", // . = delimiter
		);
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

	public function getHeaderCss(Vtiger_Request $request) {
		$headerCssInstances = parent::getHeaderCss($request);
		$cssFileNames = array(
            // "~/libraries/amcharts/amcharts_3.20.9/amcharts/plugins/export/export.css",
			'~/layouts/vlayout/modules/PortfolioInformation/css/ReportTop.css',
			// '~/libraries/amcharts/amstockchart_3_20.9/amcharts/style.css',
			
			"~/libraries/amcharts/amcharts/plugins/export/export.css",
			'~/libraries/amcharts/amstockchart/style.css',
		
		);
		$cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
		return $cssInstances;
	}
}

?>
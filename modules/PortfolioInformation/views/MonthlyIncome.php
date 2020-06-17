<?php

class PortfolioInformation_MonthlyIncome_View extends Vtiger_Index_View{
    
    function process(Vtiger_Request $request) {
        $calling_module = $request->get('calling_module');
        $calling_record = $request->get('calling_record');        
        if(strlen($request->get("account_number") > 0) || strlen($calling_module) >= 0){
            $monthly = new PortfolioInformation_MonthlyIncome_Model();
            $monthly->GenerateReport($request);
            
            $viewer = $this->getViewer($request);

            $account = "";
            if(is_array($monthly->account))
                foreach($monthly->account AS $k => $v)
                    $account .= "account_number[]={$v}&";
            else
                $account = "account_number[]={$monthly->account}&";

            $viewer->assign("MAIN_CATEGORIES_PREVIOUS", $monthly->main_categories_previous);
            $viewer->assign("MAIN_CATEGORIES_PROJECTED", $monthly->main_categories_projected);
            $viewer->assign("SUB_SUB_CATEGORIES_PREVIOUS", $monthly->sub_sub_categories_previous);
            $viewer->assign("SUB_SUB_CATEGORIES_PROJECTED", $monthly->sub_sub_categories_projected);
            $viewer->assign("PROJECTED_SYMBOLS", $monthly->individual_projected_symbols);
            $viewer->assign("PREVIOUS_SYMBOLS", $monthly->individual_previous_symbols);
            $viewer->assign("PREVIOUS_SYMBOLS_VALUES", $monthly->previous_symbols);
            $viewer->assign("PROJECTED_SYMBOLS_VALUES", $monthly->projected_symbols);
            $viewer->assign("PREVIOUS_MONTHLY_TOTALS", $monthly->previous_monthly_totals);
            $viewer->assign("PROJECTED_MONTHLY_TOTALS", $monthly->projected_monthly_totals);
            
            $viewer->assign("ACCOUNT", $account);
            $viewer->assign("DISPLAY_MONTHS", $monthly->display_months);
            $viewer->assign("DISPLAY_YEARS_CURRENT", $monthly->display_years_current);
            $viewer->assign("DISPLAY_YEARS_PROJECTED", $monthly->display_years_projected);
            $viewer->assign("MONTHLY_VALUES", $monthly->monthly_values);
            $viewer->assign("MONTHLY_TOTALS", $monthly->monthly_totals);
            $viewer->assign("GRAND_TOTAL", $monthly->grand_total);

            $viewer->assign("ESTIMATE_PAYOUT", $monthly->estimate_payout);
            $viewer->assign("ESTIMATED_MONTHLY_TOTALS", $monthly->estimated_monthly_totals);
            $viewer->assign("ESTIMATED_GRAND_TOTAL", $monthly->estimated_grand_total);

            $viewer->assign("HISTORY_DATA", json_encode($monthly->history));
            $viewer->assign("FUTURE_DATA", json_encode($monthly->estimated_income));
            
            $viewer->assign("CALLING_RECORD", $request->get('calling_record'));
            //$viewer->assign("STYLES", $this->getHeaderCss($request));
            //$viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
            $viewer->view('MonthlyIncome.tpl', "PortfolioInformation");
			
        } else
            return "<div class='ReportBottom'></div>";
    }

    public function getHeaderScripts(Vtiger_Request $request) {
   		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();
	    $jsFileNames = array(
			"~/libraries/jquery/jquery-ui/js/jquery-ui-1.8.16.custom.min.js",
//			"~/libraries/amcharts/2.9.0/amcharts/amcharts.js",
//	      	"~/libraries/amcharts/2.0.5/amcharts/javascript/raphael.js",
			
			"~/libraries/amcharts/amcharts/amcharts.js",
            "~/libraries/amcharts/amcharts/pie.js",
            "~/libraries/amcharts/amcharts/serial.js",
	       	
			"modules.$moduleName.resources.monthly_income", 
	       	"modules.$moduleName.resources.jqueryIdealforms",
		);
	   	$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request) {
    	$headerCssInstances = parent::getHeaderCss($request);
	    $cssFileNames = array(
        	'~/layouts/vlayout/modules/PortfolioInformation/css/MasterReportStyle.css',
       		'~/layouts/vlayout/modules/PortfolioInformation/css/Monthly.css',
      	);
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
		$headerCssInstances = array_merge($headerCssInstances, $cssInstances);
		return $headerCssInstances;
    }  
}

?>
<?php

class PortfolioInformation_Positions_View extends Vtiger_BasicAjax_View{
    
    function process(Vtiger_Request $request) {
        $calling_module = $request->get('calling_module');
        $calling_record = $request->get('calling_record');
        if(strlen($request->get("account_number") > 0) || strlen($calling_module) >= 0){
            $holdings = new PortfolioInformation_Positions_Model();            
            $holdings->GenerateReport($request);
            
            $viewer = $this->getViewer($request);
 
            $viewer->assign("HIDE_PIE", $request->get('hide_pie'));
            $viewer->assign("TOTALS", $holdings->categories);
            $viewer->assign("MAIN_CATEGORIES", $holdings->main_categories);
            $viewer->assign("SUB_SUB_CATEGORIES", $holdings->sub_sub_categories);
            $viewer->assign("GRANDTOTALS", $holdings->grand_totals);
            $viewer->assign("MESSAGES", $holdings->messages->messages);

            $account = "";
            if(is_array($holdings->account))
                foreach($holdings->account AS $k => $v)
                    $account .= "account_number[]={$v}&";
            else
                $account = "account_number[]=" . $holdings->account;
            
            $viewer->assign("ACCOUNT", $account);
            $viewer->assign("ACCOUNTNUMBER", $holdings->account_number);
            $viewer->assign("ACCOUNTSUSED", $holdings->accounts_used);
            $viewer->assign("DATE", $holdings->date);
            $viewer->assign("PRICEDATE", $holdings->priceDate);
            $viewer->assign("HOLDINGSCHART", json_encode($holdings->chart_data));
            $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
            $viewer->assign("STYLES", $this->getHeaderCss($request));
            $viewer->assign("CALLING_RECORD", $request->get('calling_record'));
            $output = $viewer->view('Positions.tpl', "PortfolioInformation", true);
            return $output;
        } else
            return "<div class='ReportBottom'></div>";
    }
    
    public function getHeaderScripts(Vtiger_Request $request) {
            $moduleName = $request->getModule();
            $jsFileNames = array(
                // "~/libraries/amcharts/2.9.0/amcharts/amcharts.js",
                // "~/libraries/amcharts/2.0.5/amcharts/javascript/raphael.js",
				
				"~/libraries/amcharts/amcharts/amcharts.js",
				"~/libraries/amcharts/amcharts/pie.js",
				"~/libraries/amcharts/amcharts/serial.js",

                "modules.$moduleName.resources.holdingspage",
                "modules.$moduleName.resources.holdingschart", // . = delimiter
            );
            $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
            return $jsScriptInstances;
    }
    
    public function getHeaderCss(Vtiger_Request $request) {
            $headerCssInstances = parent::getHeaderCss($request);
            $cssFileNames = array(
                '~/layouts/vlayout/modules/PortfolioInformation/css/MasterReportStyle.css',
                '~/layouts/vlayout/modules/PortfolioInformation/css/Positions.css',
            );
            $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
            return $cssInstances;
    }    
    
}

?>
<?php

class PortfolioInformation_OmniIntervalsDaily_View extends Vtiger_Index_View{

    function preProcessTplName(Vtiger_Request $request) {
        return 'PortfolioReportsPerProcess.tpl';
    }

    public function postProcess(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $viewer = $this->getViewer($request);
        $viewer->view('PortfolioReportsPostProcess.tpl', $moduleName);

        parent::postProcess($request);
    }

    function process(Vtiger_Request $request) {
        $module = $request->get('calling_module');
        $calling_record = $request->get('calling_record');
        $account_numbers = $request->get('account_number');
//        if($module == "PortfolioInformation") {
        $accounts = explode(",", $account_numbers);
        $accounts = PortfolioInformation_Module_Model::ReturnValidAccountsFromArray($accounts);
#        PortfolioInformation_Module_Model::CalculateMonthlyIntervalsForAccounts($accounts);
#        PortfolioInformation_Module_Model::AutoDetermineIntervalCalculationDates($accounts);
        PortfolioInformation_Module_Model::CalculateDailyIntervalsForAccounts($accounts, null, null, true);
//        $intervals = PortfolioInformation_Module_Model::GetDailyIntervalsForAccountsPreCalculated($accounts, '1900-01-01', date("Y-m-d"));
        $intervals = PortfolioInformation_Module_Model::GetDailyIntervalsForAccountsPreCalculated($accounts, '1900-01-01', date("Y-m-d"));

        $viewer = $this->getViewer($request);

        $viewer->assign('INTERVALS', $intervals);
        $viewer->assign("ACCOUNT_NUMBERS", implode(",", $accounts));
        $viewer->assign('SCRIPTS', self::getHeaderScripts($request));
        $viewer->assign('STYLES', self::getHeaderCss($request));
        $viewer->assign("SOURCE_RECORD", $calling_record);
        $viewer->assign("SOURCE_MODULE", $module);
        $viewer->view('IntervalViewDaily.tpl', "PortfolioInformation");
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();
        $jsFileNames = array(
            /*            "~/libraries/amcharts4_9/core.js",
                        "~/libraries/amcharts4_9/charts.js",*/
            "~/libraries/amcharts4_9/themes/dark.js",
            "~/libraries/floathead/jquery.floatThead.min.js",
//            "~/libraries/amcharts4_9/themes/animated.js",
            /*            "~/libraries/amcharts4_9/themes/dark.js",
                        "~/libraries/amcharts/amstockchart/amcharts.js",
                        "~/libraries/amcharts/amstockchart/serial.js",
                        "~/libraries/amcharts/amstockchart/themes/light.js",
                        "~/libraries/amcharts/amstockchart/amstock.js",*/
            #            "~layouts/".Vtiger_Viewer::getDefaultLayoutName()."/lib/bootstrap-daterangepicker/daterangepicker.js",*/
#            "~/libraries/jquery/DateRangePicker/daterangepicker.js",
#            "~/libraries/amcharts/amstockchart/plugins/dataloader/dataloader.js",
#            "~/libraries/amcharts/amstockchart/plugins/export/export.min.js",
//            "~/libraries/jquery/shield2/js/shieldui-all.min.js",
            "modules.PortfolioInformation.resources.IntervalsDaily", // . = delimiter
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
            "~/libraries/amcharts/amstockchart/plugins/export/export.css",
            '~/layouts/v7/modules/PortfolioInformation/css/IntervalViewDaily.css',
//            '~/libraries/shield/css/shield_all.min.css',
#            '~/libraries/bootstrap/js/eternicode-bootstrap-datepicker/css/datepicker3.css',
//            '~/libraries/jquery/DateRangePicker/daterangepicker.css',
#            "~/libraries/amcharts/amstockchart_3/amcharts/plugins/export/export.css",
#            '~/layouts/vlayout/modules/PortfolioInformation/css/PositionsWidget.css',
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);

        return $headerCssInstances;
    }
}
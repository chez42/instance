<?php

class PortfolioInformation_TrailingBalances_Dashboard extends Vtiger_IndexAjax_View {

    public function process(Vtiger_Request $request) {
        $currentUser = Users_Record_Model::getCurrentUserModel();

        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();

        $linkId = $request->get('linkid');

        $widget = Vtiger_Widget_Model::getInstance($linkId, $currentUser->getId());

        $viewer->assign('WIDGET', $widget);
        $viewer->assign('MODULE_NAME', $moduleName);
//        $viewer->assign('DATA', $data);
        $viewer->assign('SCRIPTS', $this->getHeaderScripts($request));

        $viewer->assign("CHART_TYPE", 'portfolio_trailing_balances');

#        $viewer->view('dashboards/Trailing12Revenue.tpl', $moduleName);

        if(!empty($content)) {
            $viewer->view('dashboards/PortfolioWidgetContents.tpl', $moduleName);
        } else {
            $viewer->view('dashboards/TrailingBalances.tpl', $moduleName);
        }
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = array();//Using parent was causing a javascript error due to loading AmCharts twice.  parent::getHeaderScripts($request);
        $moduleName = $request->getModule();
        $jsFileNames = array(
            //"~/libraries/jquery/jquery-ui/js/jquery-ui-1.8.16.custom.min.js",
            "~/libraries/amcharts/amcharts/amcharts.js",
            "~/libraries/amcharts/amcharts/serial.js",
            "~/libraries/amcharts/amcharts/pie.js",
            "~/libraries/amcharts/amcharts/plugins/export/export.js",
            "~/layouts/v7/modules/PortfolioInformation/resources/TrailingBalancesZoomChart.js",
            "~/layouts/v7/modules/PortfolioInformation/resources/TrailingBalances.js"
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
}

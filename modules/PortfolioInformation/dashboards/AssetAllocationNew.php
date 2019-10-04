<?php

class PortfolioInformation_AssetAllocationNew_Dashboard extends Vtiger_IndexAjax_View {

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

        $viewer->assign("CHART_TYPE", 'asset_allocation');

#        $viewer->view('dashboards/Trailing12Revenue.tpl', $moduleName);

        if(!empty($content)) {
            $viewer->view('dashboards/PortfolioWidgetContents.tpl', $moduleName);
        } else {
            $viewer->view('dashboards/AssetAllocationV4.tpl', $moduleName);
        }
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = array();//Using parent was causing a javascript error due to loading AmCharts twice.  parent::getHeaderScripts($request);
        $moduleName = $request->getModule();
        $jsFileNames = array(
            //"~/libraries/jquery/jquery-ui/js/jquery-ui-1.8.16.custom.min.js",
//            "~/libraries/amcharts4/core.js",
#            "~/libraries/amcharts4/charts.js",
#            "~/libraries/amcharts4/themes/animated.js",
            "~/layouts/v7/modules/PortfolioInformation/resources/AssetAllocationPieV4.js",
            "~/layouts/v7/modules/PortfolioInformation/resources/AssetAllocationV4.js"
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
}

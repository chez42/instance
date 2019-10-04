<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2019-02-04
 * Time: 12:51 PM
 */

class PortfolioInformation_PhantomPie_View extends Vtiger_BasicAjax_View{
    function process(Vtiger_Request $request) {
        $viewer = $this->getViewer($request);

        $viewer->assign('SCRIPTS', $this->getHeaderScripts($request));
        $viewer->assign('STYLES', $this->getHeaderCss($request));
        $viewer->assign('PIE_VALUES', json_encode($request->get('chart_data')));

        $output = $viewer->view('phantom/PhantomPie.tpl', "PortfolioInformation", true);
        return $output;
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $jsFileNames = array(
            "~/libraries/jquery/jquery.min.js",
            "~/libraries/jquery/jquery-ui/js/jquery-ui-1.8.16.custom.min.js",
            "~/libraries/amcharts/amcharts/amcharts.js",
            "~/libraries/amcharts/amcharts/serial.js",
            "~/libraries/amcharts/amcharts/pie.js",
            "~/layouts/v7/modules/PortfolioInformation/resources/PhantomPie.js",
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request) {
        $cssFileNames = array(
            '~/libraries/shield/css/shield_all.min.css'
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        return $cssInstances;
    }
}
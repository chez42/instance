<?php


/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2017-05-24
 * Time: 3:30 PM
 */

class PortfolioInformation_Sandbox_View extends Vtiger_Index_View
{
    function process(Vtiger_Request $request)
    {
//        $locations = PortfolioInformation_Administration_Model::GetFileLocations();

        $viewer = $this->getViewer($request);
        $viewer->assign("LOCATIONS", $locations);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));

        $viewer->view('Sandbox.tpl', "PortfolioInformation", false);
    }

    public function getHeaderScripts(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "~/libraries/handsontable/dist/handsontable.full.js",
            "modules.PortfolioInformation.resources.Sandbox", // . = delimiter
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
            '~/libraries/handsontable/dist/handsontable.full.css'
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);
        return $headerCssInstances;
    }

}


/*
require_once("vendor/autoload.php");

class PortfolioInformation_Sandbox_View extends Vtiger_Index_View{
    public function postProcess(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $viewer = $this->getViewer($request);
        $viewer->view('PortfolioReportsPostProcess.tpl', $moduleName, false);

        parent::postProcess($request);
    }

    public function process(Vtiger_Request $request) {
        $viewer = $this->getViewer($request);

        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $viewer->assign('STYLES', $this->getHeaderCss($request));

        $viewer->fetch('Sandbox.tpl', "PortfolioInformation");
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();
        $jsFileNames = array(
//            "~/libraries/handsontable/dist/handsontable.full.js",
            "modules.PortfolioInformation.resources.Sandbox",
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
            '~/libraries/handsontable/dist/handsontable.full.css'
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);
        return $headerCssInstances;
    }
}
*/
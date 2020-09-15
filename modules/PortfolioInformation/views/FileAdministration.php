<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2017-05-24
 * Time: 3:30 PM
 */
require_once("libraries/custodians/cCustodian.php");

class PortfolioInformation_FileAdministration_View extends Vtiger_Index_View{
    function process(Vtiger_Request $request) {
        $files = new cFileHandling();
        $locations = $files->GetFileLocations();

        $viewer = $this->getViewer($request);
        $viewer->assign("LOCATIONS", $locations);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $viewer->assign('STYLES', $this->getHeaderCss($request));

        $viewer->view('FileAdministration.tpl', "PortfolioInformation", false);
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "modules.PortfolioInformation.resources.FileAdministration", // . = delimiter
            "~/libraries/tabulator/dist/js/tabulator.min.js",
            "~/libraries/tabulator/dist/js/tabulator_core.min.js",
            "~/libraries/tabulator/dist/js/jquery_wrapper.min.js",
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
            '~/libraries/tabulator/dist/css/tabulator.min.css'
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);
        return $headerCssInstances;
    }

}
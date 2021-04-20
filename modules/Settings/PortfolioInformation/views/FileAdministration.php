<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2017-05-24
 * Time: 3:30 PM
 */
require_once("libraries/custodians/cCustodian.php");

class Settings_PortfolioInformation_FileAdministration_View extends Settings_Vtiger_Index_View{
	
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
            "modules.Settings.PortfolioInformation.resources.FileAdministration", // . = delimiter
            "~/libraries/tabulator/dist/js/tabulator.min.js",
            "~/libraries/tabulator/dist/js/tabulator_core.min.js",
            "~/libraries/tabulator/dist/js/jquery_wrapper.min.js",
            "~/libraries/tabulator/dist/js/modules/edit.min.js",
            "~/libraries/tabulator/dist/js/modules/sort.min.js",
            "~/libraries/tabulator/dist/js/modules/format.min.js",
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
            '~/libraries/tabulator/dist/css/tabulator.min.css',
            '~/layouts/v7/modules/PortfolioInformation/css/FileAdministration.css',
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);
        return $headerCssInstances;
    }

}
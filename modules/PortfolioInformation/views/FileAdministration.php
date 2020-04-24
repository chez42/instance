<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2017-05-24
 * Time: 3:30 PM
 */

class PortfolioInformation_FileAdministration_View extends Vtiger_Index_View{
    function process(Vtiger_Request $request) {

        $locations = PortfolioInformation_Administration_Model::GetFileLocations();

        $viewer = $this->getViewer($request);
        $viewer->assign("LOCATIONS", $locations);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));

        $viewer->view('FileAdministration.tpl', "PortfolioInformation", false);
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "modules.PortfolioInformation.resources.FileAdministration", // . = delimiter
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }
}
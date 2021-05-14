<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2017-05-26
 * Time: 3:30 PM
 */

class Settings_PortfolioInformation_SchwabTransactionMapping_View extends Vtiger_Index_View{
    function process(Vtiger_Request $request) {
        $mapping = Settings_PortfolioInformation_Administration_Model::GetSchwabTransactionMapping();
        $activities = Settings_PortfolioInformation_Module_Model::GetActivityPicklistValues();

        $viewer = $this->getViewer($request);
        $viewer->assign("MAPPING", $mapping);
        $viewer->assign("ACTIVITIES", $activities);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));

        $viewer->view('SchwabTransactionMapping.tpl', "PortfolioInformation", false);
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "modules.PortfolioInformation.resources.SchwabTransactionMapping", // . = delimiter
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }
}
?>
<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2017-07-13
 * Time: 1:15 PM
 */

class PortfolioInformation_PershingTransactionMapping_View extends Vtiger_Index_View{
    function process(Vtiger_Request $request) {
        $mapping = PortfolioInformation_Administration_Model::GetPershingTransactionMapping();
        $activities = PortfolioInformation_Module_Model::GetActivityPicklistValues();

        $viewer = $this->getViewer($request);
        $viewer->assign("MAPPING", $mapping);
        $viewer->assign("ACTIVITIES", $activities);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));

        $viewer->view('PershingTransactionMapping.tpl', "PortfolioInformation", false);
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "modules.PortfolioInformation.resources.PershingTransactionMapping", // . = delimiter
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }
}
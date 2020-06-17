<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2017-05-26
 * Time: 3:30 PM
 */

class PortfolioInformation_FidelityCashFlowMapping_View extends Vtiger_Index_View{
    function process(Vtiger_Request $request) {
        $mapping = PortfolioInformation_Administration_Model::GetFidelityCashFlowMapping();
        $viewer = $this->getViewer($request);
        $viewer->assign("MAPPING", $mapping);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));

        $viewer->view('FidelityCashFlowMapping.tpl', "PortfolioInformation", false);
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "modules.PortfolioInformation.resources.CashFlowMapping", // . = delimiter
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }
}
?>
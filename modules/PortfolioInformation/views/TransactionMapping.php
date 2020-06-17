<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2017-05-26
 * Time: 3:30 PM
 */

class PortfolioInformation_TransactionMapping_View extends Vtiger_Index_View{
    function process(Vtiger_Request $request) {

        $mapping = PortfolioInformation_Administration_Model::GetTransactionMapping();
        $viewer = $this->getViewer($request);
        $viewer->assign("MAPPING", $mapping);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));

        $viewer->view('TransactionMapping.tpl', "PortfolioInformation", false);
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "modules.PortfolioInformation.resources.TransactionMapping", // . = delimiter
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }
}
?>
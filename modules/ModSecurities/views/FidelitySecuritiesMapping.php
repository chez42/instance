<?php

class ModSecurities_FidelitySecuritiesMapping_View extends Vtiger_Index_View{
    function process(Vtiger_Request $request) {
        $mapping = ModSecurities_Administration_Model::GetFidelitySecuritiesMapping();
        $viewer = $this->getViewer($request);
        $viewer->assign("MAPPING", $mapping);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));

        $viewer->view('FidelitySecuritiesMapping.tpl', "ModSecurities", false);
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "modules.ModSecurities.resources.FidelitySecuritiesMapping", // . = delimiter
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }
}
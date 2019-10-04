<?php

class ModSecurities_TDSecuritiesMapping_View extends Vtiger_Index_View{
    function process(Vtiger_Request $request) {
        $mapping = ModSecurities_Administration_Model::GetTDSecuritiesMapping();
        $viewer = $this->getViewer($request);
        $viewer->assign("MAPPING", $mapping);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));

        $viewer->view('TDSecuritiesMapping.tpl', "ModSecurities", false);
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "modules.ModSecurities.resources.TDSecuritiesMapping", // . = delimiter
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }
}
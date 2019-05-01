<?php

class ModSecurities_SchwabSecuritiesMapping_View extends Vtiger_Index_View{
    function process(Vtiger_Request $request) {
        $mapping = ModSecurities_Administration_Model::GetSchwabSecuritiesMapping();
        $viewer = $this->getViewer($request);
        $viewer->assign("MAPPING", $mapping);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));

        $viewer->view('SchwabSecuritiesMapping.tpl', "ModSecurities", false);
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "modules.ModSecurities.resources.SchwabSecuritiesMapping", // . = delimiter
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }
}
<?php

class OmniCal_TempStuff_View extends Vtiger_BasicAjax_View {
    
    public function process(Vtiger_Request $request) {
        echo "IN HERE";
    }

    public function getHeaderScripts(Vtiger_Request $request) {
            $moduleName = $request->getModule();
            $jsFileNames = array(
            );
            $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
            return $jsScriptInstances;
    }
    
    public function getHeaderCss(Vtiger_Request $request) {
            $headerCssInstances = parent::getHeaderCss($request);
            $cssFileNames = array(
            );
            $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
            return $cssInstances;
    }
}

?> 
<?php

class Omniscient_List_View extends Vtiger_Index_View{
    public function process(Vtiger_Request $request) {
        $viewer = $this->getViewer($request);
        $viewer->assign("SCRIPTS", $this->getCustomScripts($request));
        
        $viewer->view("Index.tpl", $request->getModule());
    }
    
    // Injecting custom javascript resources
    public function getCustomScripts(Vtiger_Request $request) {
            $moduleName = $request->getModule();
            $jsFileNames = array(
                    "modules.$moduleName.resources.punchit", // . = delimiter
            );
            $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
            return $jsScriptInstances;
    }

}

?>
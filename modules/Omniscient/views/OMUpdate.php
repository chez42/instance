<?php

class Omniscient_OMUpdate_View extends Vtiger_Index_View{
    public function process(Vtiger_Request $request) {
        $updater = new Omniscient_OMUpdate_Model();
        $updater->CopyTransactions($request->get('account_numbers'));
        $viewer = $this->getViewer($request);
        
        $viewer->assign("SCRIPTS", $this->getCustomScripts($request));
        $viewer->view("OMUpdate.tpl", $request->getModule());
    }
    
    public function getCustomScripts(Vtiger_Request $request) {
            $moduleName = $request->getModule();
            $jsFileNames = array(
                    "modules.$moduleName.resources.OMUpdate", // . = delimiter
            );
            $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
            return $jsScriptInstances;
    }

}
?>
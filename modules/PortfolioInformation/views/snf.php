<?php
class PortfolioInformation_snf_View extends Vtiger_Index_View{    
    function process(Vtiger_Request $request) {
        $dateTime = new DateTime("first day of last month");
        $date = $dateTime->format("Y-m-d");
        
        $viewer = $this->getViewer($request);
        $viewer->assign("HISTORICAL_DATE", $date);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        
        $viewer->view('snf.tpl', "PortfolioInformation", false);
    }
    
    public function getHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "modules.$moduleName.resources.ManualInteractions", // . = delimiter
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }
}
?>
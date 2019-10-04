<?php
class PortfolioInformation_HistoryCompare_View extends Vtiger_Index_View{    
    function process(Vtiger_Request $request) {
        $asset_allocation = new PortfolioInformation_AssetAllocation_Action();
        echo "MEMORY USAGE BEGIN: " . memory_get_usage() . "<br />";
        gc_enable();
        $asset_allocation->WriteComparisonTable();
//        $asset_allocation->WriteToCompareTable(1271552);
//        $asset_allocation->HistoricalUpdateIndividualAccount(1790752, '2015-01-01');
        gc_collect_cycles();
        echo "MEMORY USAGE END: " . memory_get_usage() . "<br />";
        $viewer = $this->getViewer($request);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));        
        
        $html = $viewer->view('AssetAllocation.tpl', "PortfolioInformation", true);
        echo $html;
    }
    
    public function getHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "modules.$moduleName.resources.assetallocation", // . = delimiter
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }
}
?>
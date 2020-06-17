<?php
class PortfolioInformation_HistoricalUpdate_View extends Vtiger_Index_View{    
    function process(Vtiger_Request $request) {
        $historical = new PortfolioInformation_HistoricalUpdate_Action();
        echo "MEMORY USAGE BEGIN: " . memory_get_usage() . "<br />";
        gc_enable();
            $date = $request->get('historical_date');
            if(strlen($date < 10)){
                echo "Invalid Date";
            }else{
                $historical->UpdateAllHistoricalAccounts($date);
            }
//        $historical->UpdateAllHistoricalAccounts();
//        $historical->UpdateAllHistoricalAccounts();
//        $historical->HistoricalUpdateIndividualAccount(1815637, '2015-03-01');
        gc_collect_cycles();
//        $asset_allocation->UpdateAllHistoricalAccounts();
/*        $asset_allocation->HistoricalUpdateIndividualAccount(1271552);
        gc_collect_cycles();
        echo "MEMORY USAGE END: " . memory_get_usage() . "<br />";
        $viewer = $this->getViewer($request);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));        
        
        $html = $viewer->view('AssetAllocation.tpl', "PortfolioInformation", true);
        echo $html;*/
    }
    
    public function getHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "modules.$moduleName.resources.historicalupdate", // . = delimiter
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }
}
?>
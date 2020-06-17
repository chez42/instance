<?php
class PortfolioInformation_PCQuery_View extends Vtiger_Index_View{    
    function process(Vtiger_Request $request) {
        $pc = new PortfolioInformation_PCQuery_Model();
//        $list = $pc->GetTransactions(6551);
        $query = "SELECT * FROM Codes WHERE CodeId = 328";
        $query = "SELECT * FROM Transactions WHERE PortfolioID = 60637 AND SymbolID = 112468";
//        $query = "SELECT * FROM SecurityCodes WHERE SecurityId = 122506";
        $list = $pc->CustomQuery($query);
        foreach($list AS $k => $v){
            print_r($v);
            echo "<br /><br /><br />";
        }

        $viewer = $this->getViewer($request);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));        
        
        $viewer->view('PCQuery.tpl', "PortfolioInformation", false);
    }
    
    public function getHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "modules.$moduleName.resources.pcquery", // . = delimiter
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }
}
?>
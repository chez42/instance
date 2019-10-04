<?php

class PortfolioInformation_BalanceWidget_View extends Vtiger_Index_View{
    function process(Vtiger_Request $request) {
        $date = date("Y-m-d", strtotime('today -1 Weekday'));
        PortfolioInformation_TotalBalances_Model::ConsolidateBalances();
        PortfolioInformation_TotalBalances_Model::WriteAndUpdateLast7DaysForAllUsers();

        $viewer = $this->getViewer($request);

        $viewer->assign("DATE", $date);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));

        $viewer->view('BalanceWidget.tpl', "PortfolioInformation", false);
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "modules.PortfolioInformation.resources.BalanceWidget", // . = delimiter
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }
}
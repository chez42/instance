<?php

class PortfolioInformation_OmniSol_View extends Vtiger_Index_View{
    function preProcessTplName(Vtiger_Request $request) {
        return 'PortfolioReportsPerProcess.tpl';
    }

    public function postProcess(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $viewer = $this->getViewer($request);
        $viewer->view('PortfolioReportsPostProcess.tpl', $moduleName);

        parent::postProcess($request);
    }

    function process(Vtiger_Request $request) {
/*        $date = date("Y-m-d", strtotime('today -1 Weekday'));
        $transaction_diagnosis = PortfolioInformation_CloudInteractions_Model::GetLatestDates();
        $files_run = PortfolioInformation_CloudInteractions_Model::GetFilesRunByCustodian(50);
*/
        $viewer = $this->getViewer($request);

#        $viewer->assign("DATE", $date);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
#        $viewer->assign("TRANSACTION_DIAGNOSIS", $transaction_diagnosis);
#        $viewer->assign("FILES_RUN", $files_run);

        $viewer->view('OmniSol.tpl', "PortfolioInformation", false);
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "modules.PortfolioInformation.resources.OmniSol", // . = delimiter
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }
}
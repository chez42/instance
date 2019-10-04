<?php

class PortfolioInformation_DailyBalances_View extends Vtiger_Index_View{

    function preProcess(Vtiger_Request $request, $display=true){
        parent::preProcess($request, false);

        $viewer = $this->getViewer($request);
        if($display) {
            $this->preProcessDisplay($request);
        }
    }

    function process(Vtiger_Request $request) {
        if(!$request->get('date'))
            $date = date("Y-m-d", strtotime('today -1 Month'));
        else
            $date = $request->get('date');

        if(!$request->get('custodian_name'))
            $custodian_name = "fidelity";
        else
            $custodian_name = $request->get('custodian_name');

        $viewer = $this->getViewer($request);

        $viewer->assign("DATE", $date);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));

        $custodian = PortfolioInformation_DailyBalances_Model::GetBalances($custodian_name, $date);

        if($custodian) {
            $viewer->assign("CUSTODIAN", $custodian);
            $viewer->assign("CUSTODIAN_HEADERS", $custodian[0]);
        }

        $viewer->assign("CUSTODIAN_NAME", $custodian_name);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $viewer->assign("STYLES", $this->getHeaderCss($request));

        $viewer->view('DailyBalances.tpl', "PortfolioInformation");
    }

    public function postProcess(Vtiger_Request $request) {
        parent::postProcess($request);
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);

        $jsFileNames = array(
            "modules.PortfolioInformation.resources.DailyBalances", // . = delimiter
            "~/libraries/jquery/DataTables/datatables.min.js"
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;



        $moduleName = $request->getModule();
        $jsFileNames = array(
            "modules.PortfolioInformation.resources.DailyBalances", // . = delimiter
            "~/libraries/jquery/DataTables/datatables.min.js"
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request) {
        $parentHeaderCssScriptInstances = parent::getHeaderCss($request);

        $headerCss = array(
            '~/layouts/vlayout/modules/PortfolioInformation/css/DailyBalances.css',
            '~/libraries/jquery/DataTables/datatables.min.css',
        );
        $cssScripts = $this->checkAndConvertCssStyles($headerCss);
        $headerCssScriptInstances = array_merge($parentHeaderCssScriptInstances , $cssScripts);
        return $headerCssScriptInstances;
    }
}
?>
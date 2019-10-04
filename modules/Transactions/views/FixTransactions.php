<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2019-04-17
 * Time: 12:45 PM
 */
class Transactions_FixTransactions_View extends Vtiger_Detail_View {

    public function process(Vtiger_Request $request) {
        $record_id = $request->get("transactionid");
        $quantity = trim($request->get("quantity"));
        $record = Vtiger_Record_Model::getInstanceById($record_id, "Transactions");

        $viewer = $this->getViewer($request);
        $viewer->assign("CURRENT_USER", Users_Record_Model::getCurrentUserModel());
        $viewer->assign("RECORD_ID", $record_id);
        $viewer->assign("RECORD", $record->getData());
        $viewer->assign("QUANTITY", $quantity);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $viewer->assign("CSS", $this->getHeaderCss($request));

        echo $viewer->view('FixTransactions.tpl', 'Transactions', true);

        /*
        $calling_module = $request->get('calling_module');
        $calling_record = $request->get('calling_record');
        $setype = GetSettypeFromID($request->get('calling_record'));

        $account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersFromCrmid($calling_record);
        $datasets = PortfolioInformation_Chart_Model::getHoldingsWidgetDatasetsForRecord($calling_record);

#        PortfolioInformation_HoldingsReport_Model::GenerateAssetClassTables($account_numbers);
#        $pie = PortfolioInformation_Reports_Model::GetPieFromTable();

        $viewer = $this->getViewer($request);
        $viewer->assign("ASSET_PIE", json_encode($datasets));
        $viewer->assign("CURRENT_USER", Users_Record_Model::getCurrentUserModel());
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $viewer->assign("CSS", $this->getHeaderCss($request));

        echo $viewer->view('HoldingsWidget.tpl', $request->get('module'), true);*/
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "~/libraries/jquery/jquery-ui/js/jquery-ui-1.8.16.custom.min.js",
            "modules.Transactions.resources.FixTransactions", // . = delimiter*/
//			"~/libraries/amcharts/amcharts_3.20.9/amcharts/amcharts.js",
//			"~/libraries/amcharts/amcharts_3.20.9/amcharts/pie.js",
//			"~/libraries/amcharts/amcharts_3.20.9/amcharts/serial.js",
//			"~/libraries/amcharts/amcharts_3.20.9/amcharts/plugins/export/export.min.js",
#			"modules.PortfolioInformation.resources.HoldingsReport",
/*
            "~/libraries/amcharts/amcharts/amcharts.js",
            "~/libraries/amcharts/amcharts/pie.js",
            "~/libraries/amcharts/amcharts/plugins/export/export.min.js",
            "~/libraries/amcharts/amcharts/plugins/animate/animate.min.js",
//            "~/libraries/jquery/multirange/multirange.js",
            "modules.PortfolioInformation.resources.AjaxHoldingsWidget", // . = delimiter*/
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
            '~/layouts/v7/modules/Transactions/css/FixTransactions.css',
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);
        return $headerCssInstances;
    }
}
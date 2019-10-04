<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
include_once("libraries/reports/new/nCommon.php");
include_once("libraries/reports/new/nCombinedAccounts.php");

/* ===== START : Felipe Project Run Changes ===== */

include_once("include/utils/omniscientCustom.php");

/* ===== END : Felipe Project Run Changes ===== */

class PortfolioInformation_HoldingsWidget_View extends Vtiger_Detail_View {

    public function process(Vtiger_Request $request) {
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

        echo $viewer->view('HoldingsWidget.tpl', $request->get('module'), true);
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
//			"~/libraries/amcharts/amcharts_3.20.9/amcharts/amcharts.js",
//			"~/libraries/amcharts/amcharts_3.20.9/amcharts/pie.js",
//			"~/libraries/amcharts/amcharts_3.20.9/amcharts/serial.js",
//			"~/libraries/amcharts/amcharts_3.20.9/amcharts/plugins/export/export.min.js",
#			"modules.PortfolioInformation.resources.HoldingsReport",

            "~/libraries/amcharts/amcharts/amcharts.js",
            "~/libraries/amcharts/amcharts/pie.js",
            "~/libraries/amcharts/amcharts/plugins/export/export.min.js",
            "~/libraries/amcharts/amcharts/plugins/animate/animate.min.js",
//            "~/libraries/jquery/multirange/multirange.js",
            "modules.PortfolioInformation.resources.AjaxHoldingsWidget", // . = delimiter
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
            '~/layouts/v7/modules/PortfolioInformation/css/HoldingsWidget.css',
//            "~/libraries/jquery/multirange/multirange.css",
            "~/libraries/amcharts/amcharts/plugins/export/export.css",
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);
        return $headerCssInstances;
    }
}

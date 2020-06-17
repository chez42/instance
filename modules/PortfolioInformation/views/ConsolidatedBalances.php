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

class PortfolioInformation_ConsolidatedBalances_View extends Vtiger_Detail_View {

    public function process(Vtiger_Request $request) {
        $calling_module = $request->get('calling_module');
        $calling_record = $request->get('calling_record');
        $setype = GetSettypeFromID($request->get('calling_record'));

        if($setype == "PortfolioInformation"){
            $account_numbers = array(PortfolioInformation_Module_Model::GetAccountNumberFromCrmid($request->get('calling_record')));
        }
        else
            $account_numbers = GetAccountNumbersFromRecord($request->get('calling_record'));

        $balances = PortfolioInformation_HistoricalInformation_Model::GetConsolidatedBalances($account_numbers, '1900-01-01', date("Y-m-d"));

        $viewer = $this->getViewer($request);
        $viewer->assign("CURRENT_USER", Users_Record_Model::getCurrentUserModel());
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $viewer->assign("CSS", $this->getHeaderCss($request));
        $viewer->assign("RECORD", $calling_record);
        $viewer->assign("CALLING_RECORD", $calling_record);
        $viewer->assign("ACCOUNTS", json_encode($account_numbers));
        $viewer->assign("SOURCE_MODULE", $calling_module);
        $viewer->assign("CONSOLIDATED", json_encode($balances));

#        if($balances == 0)
#            echo $viewer->view('ConsolidatedBalancesEmpty.tpl', $request->get('module'), true);
#        else
            echo $viewer->view('ConsolidatedBalances.tpl', $request->get('module'), true);
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "~/libraries/amcharts/amcharts/amcharts.js",
            "~/libraries/amcharts/amcharts/pie.js",
            "~/libraries/amcharts/amcharts/serial.js",
            "~/libraries/amcharts/amcharts/plugins/export/export.min.js",
            "modules.PortfolioInformation.resources.Consolidated", // . = delimiter
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
            "~/libraries/amcharts/amcharts/plugins/export/export.css",
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);
        return $headerCssInstances;
    }
}

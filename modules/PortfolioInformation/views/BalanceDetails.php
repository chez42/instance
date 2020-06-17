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

/**
 * This is the balance details widget
 * Class PortfolioInformation_HistoricalInformation_View
 */
class PortfolioInformation_BalanceDetails_View extends Vtiger_Index_View {

    public function process(Vtiger_Request $request) {
        echo 'here';return;
        $calling_module = $request->get('calling_module');
        $calling_record = $request->get('calling_record');
        $setype = GetSettypeFromID($request->get('calling_record'));
        if(strlen($request->get('calling_record') ) == 0) {
            echo "Records unable to be determined";
            return;
        }

        if($setype == "PortfolioInformation"){
            $account_numbers = array(PortfolioInformation_Module_Model::GetAccountNumberFromCrmid($request->get('calling_record')));
        }
        else
            $account_numbers = GetAccountNumbersFromRecord($request->get('calling_record'));

        $data = PortfolioInformation_Module_Model::GetDailyIntervalsForAccountsWithDateFilter($account_numbers);



        PortfolioInformation_HoldingsReport_Model::GenerateAssetClassTables($account_numbers);
        $pie = PortfolioInformation_Reports_Model::GetPieFromTable();
        $viewer = $this->getViewer($request);
        $viewer->assign("CURRENT_USER", Users_Record_Model::getCurrentUserModel());
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $viewer->assign("RECORD", $calling_record);
        $viewer->assign("SOURCE_MODULE", $calling_module);

        echo $viewer->view('BalanceDetails.tpl', $request->get('module'), true);
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "~/libraries/amcharts/amcharts/amcharts.js",
            "~/libraries/amcharts/amcharts/serial.js",
            "~/libraries/amcharts/amcharts/plugins/export/export.min.js",

            "modules.PortfolioInformation.resources.PortfolioList", // . = delimiter
            "modules.PortfolioInformation.resources.HistoricalCharts", // . = delimiter
        );
        if($moduleName == "PortfolioInformation")
            $jsFileNames[] = "modules.PortfolioInformation.resources.PositionsWidget";
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }
}

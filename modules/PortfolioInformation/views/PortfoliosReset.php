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

class PortfolioInformation_PortfoliosReset_View extends Vtiger_Index_View {

    /*    function preProcessTplName(Vtiger_Request $request) {
            return 'PortfolioReportsPerProcess.tpl';
        }*/
    
    public function postProcess(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $viewer = $this->getViewer($request);
        $viewer->view('PortfolioReportsPostProcess.tpl', $moduleName);
        
        parent::postProcess($request);
    }
    
    public function process(Vtiger_Request $request) {
        $calling_module = $request->get('calling_module');
        $calling_record = $request->get('calling_record');
        $setype = GetSettypeFromID($request->get('calling_record'));

        if($setype == "PortfolioInformation"){
            $account_numbers = array(PortfolioInformation_Module_Model::GetAccountNumberFromCrmid($request->get('calling_record')));
        }
        else
            $account_numbers = GetAccountNumbersFromRecord($request->get('calling_record'));

        $accounts_string = implode(", ", $account_numbers);
        $viewer = $this->getViewer($request);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $viewer->assign("RECORD", $calling_record);
        $viewer->assign("CALLING_RECORD", $calling_record);
        $viewer->assign("ACCOUNTS", json_encode($account_numbers));
        $viewer->assign("ACCOUNTS_STRING", $accounts_string);
        $viewer->assign("SOURCE_MODULE", $calling_module);

        echo $viewer->view('PortfoliosReset.tpl', $request->get('module'), true);
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "modules.PortfolioInformation.resources.PortfoliosReset", // . = delimiter
        );
#        if($moduleName == "PortfolioInformation")
#            $jsFileNames[] = "modules.PortfolioInformation.resources.PositionsWidget";
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }
}

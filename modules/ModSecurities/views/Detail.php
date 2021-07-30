<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
include_once('libraries/reports/new/nCommon.php');
require_once("libraries/EODHistoricalData/EODGuzzle.php");
require_once("libraries/Reporting/ReportCommonFunctions.php");

class ModSecurities_Detail_View extends Vtiger_Detail_View {

    public function preProcess(Vtiger_Request $request) {

		$guz = new cEodGuzzle();
        
		$security = ModSecurities_Record_Model::getInstanceById($request->get("record"));
        
		if(strlen($security->get("option_root_symbol")) > 0 && trim($security->get("option_root_symbol")) != '') {
            $symbol = $security->get("option_root_symbol");
            $notes = "This security is an option.  Showing information from the root symbol";
        } else {
            $symbol = $security->get("security_symbol");
        }

        $eod = json_decode($guz->getSymbolRealTimePricing($symbol));
        
		try {
            $fund = json_decode($guz->getFundamentals($symbol));
        } catch(Exception $e){}

        $date = date("Y-m-d");
        
		$start = GetDateMinusDays(30);
        
		$security = ModSecurities_Record_Model::getInstanceById($request->get("record"));
        
		ModSecurities_ConvertCustodian_Model::UpdateSecurityPriceFromEOD($symbol, $start, $date);

		$guz = new cEodGuzzle();
		
		$rawData = $guz->getFundamentals($symbol);
		
		$result = json_decode($rawData);
		
		$dividendData = json_decode($guz->getDividends($symbol, "US", $start, $date));
		
		ModSecurities_ConvertCustodian_Model::UpdateFromEODGuzzleResult($result, $dividendData, $symbol);
		
		ModSecurities_ConvertCustodian_Model::WriteRawEODData($symbol, $rawData);
		
        $change = $eod->change;
		
        $percentage = $change / $eod->close * 100;

        date_default_timezone_set('America/Los_Angeles');
        $eod->last_update = date("F d, Y h:i:s a", $eod->timestamp);

        $viewer = $this->getViewer($request);

        $viewer->assign('EOD', $eod);
        $viewer->assign("FUND", $fund);
        $viewer->assign("SECURITY_DATA", $security->getData());
        $viewer->assign("CHANGE", $eod->change);
        $viewer->assign("PERCENTAGE", $percentage);
        $viewer->assign("NOTES", $notes);
        
		if(strlen($fund->General->LogoURL) > 0)
            $viewer->assign("LOGO", URI_LOGOS . $fund->General->LogoURL);
        
		$viewer->assign("EXTRA_SCRIPTS", $this->getCustomScripts($request));
        
		$viewer->assign("EXTRA_STYLES", $this->getExtraHeaderCss($request));

        return parent::preProcess($request);
    }

    function process(Vtiger_Request $request) {
        require_once("libraries/EODHistoricalData/EODGuzzle.php");
        return parent::process($request);
    }
    
    public function postProcess(\Vtiger_Request $request) {
        parent::postProcess($request);
    }

    // Injecting custom javascript resources
    public function getCustomScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);

        $jsFileNames = array(
            '~/layouts/v7/modules/ModSecurities/resources/ListViewRightClickPricing',
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }

    public function getExtraHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
            '~/layouts/v7/modules/ModSecurities/css/DetailViewEODLatestPrice.css',
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        return $cssInstances;
    }
    
    public function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();
        
        $jsFileNames = array(
            'modules.'.$moduleName.'.resources.HistoricalDataList',
        );
        
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        
        return $headerScriptInstances;
    }
}
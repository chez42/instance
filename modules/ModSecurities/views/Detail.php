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

class ModSecurities_Detail_View extends Vtiger_Detail_View {

    public function preProcess(Vtiger_Request $request) {
/*        $i = Vtiger_Record_Model::getInstanceById($request->get('record'));
        $symbol = $i->get('security_symbol');
        ModSecurities_Module_Model::FillWithYQLOrXigniteData($symbol);*/
        $guz = new cEodGuzzle();
        $security = ModSecurities_Record_Model::getInstanceById($request->get("record"));
        if(strlen($security->get("option_root_symbol")) > 0 && trim($security->get("option_root_symbol")) != '') {
            $symbol = $security->get("option_root_symbol");
            $notes = "This security is an option.  Showing information from the root symbol";
        }
        else {
            $symbol = $security->get("security_symbol");
        }

        $eod = json_decode($guz->getSymbolRealTimePricing($symbol));
        $fund = json_decode($guz->getFundamentals($symbol));

        $dividendData = json_decode($guz->getDividends($symbol, "US", '2018-01-01', '2019-11-04'));
        print_r($dividendData);
        exit;
#        ModSecurities_ConvertCustodian_Model::UpdateFromEODGuzzleResult($result, $dividendData, $symbol);
#        ModSecurities_ConvertCustodian_Model::WriteRawEODData($symbol, $rawData);
#print_r($fund);exit;
        $change = $eod->change;//$eod->close - $data['security_price'];
        $percentage = $change / $eod->close * 100;//$data['security_price'] * 100;

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

##        $date = date("Y-m-d");
##        $security = ModSecurities_Record_Model::getInstanceById($request->get("record"));
##        $symbol = $security->get("security_symbol");
##        ModSecurities_ConvertCustodian_Model::UpdateSecurityPriceFromEOD($symbol, '2013-01-01', $date);

/*      ENABLE THIS IF WE WANT EOD TO AUTO UPDATE
 *
 *         $guz = new cEodGuzzle();
        $rawData = $guz->getFundamentals($symbol);
        $result = json_decode($rawData);
        $dividendData = json_decode($guz->getDividends($symbol, "US", '2013-01-01', $date));
        ModSecurities_ConvertCustodian_Model::UpdateFromEODGuzzleResult($result, $dividendData, $symbol);
        ModSecurities_ConvertCustodian_Model::WriteRawEODData($symbol, $rawData);*/

        return parent::process($request);
    }
    
    public function postProcess(\Vtiger_Request $request) {
/*        $viewer = $this->getViewer($request);
        $viewer->assign("RECORD_ID", $record->get('id'));*/
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
}
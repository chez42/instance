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

class ModSecurities_Detail_View extends Vtiger_Detail_View {

    public function preProcess(Vtiger_Request $request) {
/*        $i = Vtiger_Record_Model::getInstanceById($request->get('record'));
        $symbol = $i->get('security_symbol');
        ModSecurities_Module_Model::FillWithYQLOrXigniteData($symbol);*/
        return parent::preProcess($request);
    }

    function process(Vtiger_Request $request) {
        require_once("libraries/EODHistoricalData/EODGuzzle.php");

        $date = date("Y-m-d");
        $security = ModSecurities_Record_Model::getInstanceById($request->get("record"));
        $symbol = $security->get("security_symbol");
        ModSecurities_ConvertCustodian_Model::UpdateSecurityPriceFromEOD($symbol, '2013-01-01', $date);

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
}
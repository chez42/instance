<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2019-02-21
 * Time: 4:15 PM
 */
require_once("libraries/EODHistoricalData/EODGuzzle.php");

class ModSecurities_EODActions_Action extends Vtiger_BasicAjax_Action {

    public function process(Vtiger_Request $request) {
        $record = $request->get('record');
        switch(strtolower($request->get('todo'))){
            case "updateeodsymbol":
                    include_once("libraries/EODHistoricalData/EODGuzzle.php");
                    $guz = new cEodGuzzle();
                    $security_instance = ModSecurities_Record_Model::getInstanceById($record);
                    $symbol = $security_instance->get("security_symbol");
                    $aclass = $security_instance->get('aclass');
                    $security_type = $security_instance->get('securitytype');

                    if($security_type == 'Bond'){
                        $rawData = $guz->getBonds($symbol);
                        ////////WRITE BONDS FUNCTION HERE INTO OMNISCIENT
                    }else{
                        $start = date('Y')  - 1 . "-01-01";
                        $end = date('Y') - 1 . "-12-31";

                        $rawData = $guz->getFundamentals($symbol);
                        $result = json_decode($rawData);
                        $dividendData = json_decode($guz->getDividends($symbol, "US", $start, $end));
                        ModSecurities_ConvertCustodian_Model::UpdateFromEODGuzzleResult($result, $dividendData, $symbol);
                    }

                    ModSecurities_ConvertCustodian_Model::WriteRawEODData($symbol, $rawData);
                    echo 1;
                break;
            case "getdelayedpricing":
                $guz = new cEodGuzzle();
                $security = ModSecurities_Record_Model::getInstanceById($request->get("recordid"));
                if(strlen($security->get("option_root_symbol")) > 0 && trim($security->get("option_root_symbol")) != '') {
                    $symbol = $security->get("option_root_symbol");
                    $eod = json_decode($guz->getSymbolRealTimePricing($symbol));
                    $fund = $guz->getFundamentals($symbol);
                }
                else {
                    $symbol = $security->get("security_symbol");
                    $eod = json_decode($guz->getSymbolRealTimePricing($symbol));
                    $fund = $guz->getFundamentals($symbol);
                }

                date_default_timezone_set('America/Los_Angeles');
                $eod->last_update = date("F d, Y h:i:s a", $eod->timestamp);
                $viewer = new Vtiger_Viewer();
                $viewer->assign("EOD", $eod);
                $output = $viewer->view('DetailViewEODLatestPrice.tpl', "ModSecurities", true);
                echo $output;
                break;
            case "getdelayedpopup":
                $guz = new cEodGuzzle();
                $security = ModSecurities_Record_Model::getInstanceById($request->get("recordid"));
                if(strlen($security->get("option_root_symbol")) > 0 && trim($security->get("option_root_symbol")) != '') {
                    $symbol = $security->get("option_root_symbol");
###                    $eod = json_decode($guz->getOptions($symbol));//WANTED WHEN WE HAVE REALTIME OPTION PRICING
                    $eod = json_decode($guz->getSymbolRealTimePricing($symbol));
                    $notes = "This security is an option.  Showing information from the root symbol";
                }
                else {
                    $symbol = $security->get("security_symbol");
                    $eod = json_decode($guz->getSymbolRealTimePricing($symbol));
                }
                $fund = json_decode($guz->getFundamentals($symbol));
                $data = $security->getData();
                $change = $eod->change;//$eod->close - $data['security_price'];
                $percentage = $change / $eod->close * 100;//$data['security_price'] * 100;
#print_r($eod);exit;
                date_default_timezone_set('America/Los_Angeles');
                $eod->last_update = date("F d, Y h:i:s a", $eod->timestamp);
                $viewer = new Vtiger_Viewer();
                $viewer->assign("EOD", $eod);
                $viewer->assign("SECURITY_DATA", $security->getData());
                $viewer->assign("CHANGE", $eod->change);
                $viewer->assign("PERCENTAGE", $percentage);
                $viewer->assign("NOTES", $notes);
                if(strlen($fund->General->LogoURL) > 0)
                    $viewer->assign("LOGO", URI_LOGOS . $fund->General->LogoURL);
                $output = $viewer->view('DetailViewEODLatestPricePopup.tpl', "ModSecurities", true);
                echo $output;
                break;
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2019-02-21
 * Time: 4:15 PM
 */

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

                    if($aclass == 'Bonds'){
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
        }
    }
}
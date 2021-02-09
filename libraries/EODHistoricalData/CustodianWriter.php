<?php

class CustodianWriter{
    private $guz;

    public function __construct(){
        $this->guz = new cEodGuzzle();
    }

    public function DoesCustodianHavePriceAsOfDate($custodian, $symbol, $date){
        global $adb;

        switch(strtoupper($custodian)){
            case "TD":
                $query = "SELECT COUNT(*) as count
                          FROM custodian_prices_td
                          WHERE symbol = ? AND date = ?";
                $result = $adb->pquery($query, array($symbol, $date));
                break;
            case "FIDELITY":
                $query = "SELECT COUNT(*) as count
                          FROM custodian_prices_fidelity
                          WHERE symbol = ? AND price_date = ?";
                $result = $adb->pquery($query, array($symbol, $date));
                break;
            case "SCHWAB":
                break;
            case "PERSHING":
                break;
        }

        if(!is_null($result)){
            if($adb->num_rows($result) == 0){
                if($adb->query_result($result, 0, 'count') == 0)
                    return false;
            }else{
                return true;
            }
        }

        return false;
    }

    public function UpdatePrice($custodian, $symbol, $price_data){
        global $adb;

        switch(strtoupper($custodian)){
            case "TD":
                $query = "INSERT INTO custodian_omniscient.custodian_prices_td (symbol, date, price, insert_date)
                          VALUES (?, ?, ?, NOW())
                          ON DUPLICATE KEY UPDATE insert_date = insert_date";
                foreach($price_data AS $k => $v) {
                    $adb->pquery($query, array($symbol, $v->date, $v->close), true);
                }
                break;
            case "FIDELITY":
                $query = "INSERT INTO custodian_omniscient.custodian_prices_fidelity (symbol, price_date, price, insert_date)
                          VALUES (?, ?, ?, NOW())
                          ON DUPLICATE KEY UPDATE insert_date = insert_date";
                foreach($price_data AS $k => $v) {
                    $adb->pquery($query, array($symbol, $v->date, $v->close), true);
                }
                break;
            case "SCHWAB":
                break;
            case "PERSHING":
                break;
        }
    }

    public function WriteEodToCustodian($symbol, $sdate, $edate, $custodian){
        global $adb;

        $div = json_decode($this->guz->getDividends($symbol, "US", $sdate, $edate));
        $fundamental = json_decode($this->guz->getFundamentals($symbol));
        $type = $fundamental->General->Type;

        switch(strtolower($type)){
            case "etf":
                $fundamental = json_decode($this->guz->getFundamentals($symbol));
                $data = new TypeETF($fundamental, $div);
                $data->UpdateIntoOmni();
                break;
            case "fund":
                $eod = new EODHistoricalData('json', "US", '59838effd9cac');
                $result = $eod->getSymbolPricing($symbol, $sdate, $edate);
                $data = json_decode($result);
                $this->UpdatePrice($custodian, $symbol, $data);
                break;
            case "stock":
            case "common stock":
                $fundamental = json_decode($this->guz->getFundamentals($symbol));
                $data = new TypeStock($fundamental);
#               print_r($data);exit;
#               $data->UpdateIntoOmni();

            default:
                return 0;//echo "NO DEFINITION!";
                break;
        }
    }
}
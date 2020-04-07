<?php

class ModSecurities_PriceInteraction_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        switch(strtolower($request->get('todo'))){
            case 'saveprice':
                $price_id = $request->get('price_id');
                $price    = $request->get('price');
                $record   = $request->get('record');
                $r = Vtiger_Record_Model::getInstanceById($record, 'ModSecurities');
                $data = $r->getData();
                if(is_numeric($price))
                    $result = ModSecurities_SecurityBridge_Model::UpdatePricingTablePrice($price_id, $price, $data['update_pc']);
                else
                    $result = 0;
                if($result == 0)
                    $result = "Error updating price.  Non-numerical data possibly?";
                $message = array("result"=>$result);
                echo json_encode($message);
                break;
            case 'getprice':
                $symbols = $request->get('symbol');
                $sdate = $request->get('sdate');
                $edate = $request->get('edate');
                $data = array();
                foreach($symbols AS $k => $v){
                    $data[] = self::GetIndexPriceRange($v, $sdate, $edate);
                }
                echo json_encode($data);
                break;
        }
    }

    public function GetIndexPriceRange($symbol, $sdate, $edate){
        global $adb;
        $query = "SELECT symbol, close, open, DATE_FORMAT(date,'%Y-%m-%d') AS formatted_date
                  FROM vtiger_prices_index
                  WHERE symbol = ?
                  AND date BETWEEN ? AND ?";
        $result = $adb->pquery($query, array($symbol, $sdate, $edate));

        $data = array();
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $tmp = array("date" => $v['formatted_date'],
                    "symbol" => $v['symbol'],
                    "open" => $v['open'],
                    "value" => $v['close']);
                $data[] = $tmp;
            }
        }
        return $data;
    }

    public function GetSymbolPriceRange($symbol, $sdate, $edate){
        global $adb;
        $query = "SELECT symbol, date, close, DATE_FORMAT(date,'%Y-%m-%d') AS formatted_date
                  FROM vtiger_prices 
                  WHERE symbol = ?
                  AND date BETWEEN ? AND ?";
        $result = $adb->pquery($query, array($symbol, $sdate, $edate));
        $data = array();
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $tmp = array("date" => $v['formatted_date'],
                    "symbol" => $v['symbol'],
                    "value" => $v['close']);
                $data[] = $tmp;
            }
        }
        return $data;
    }
}
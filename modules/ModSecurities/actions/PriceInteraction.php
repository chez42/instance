<?php

class ModSecurities_PriceInteraction_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        switch($request->get('todo')){
            case 'SavePrice':
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
        }
    }
}
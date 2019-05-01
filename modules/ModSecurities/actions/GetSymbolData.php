<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2018-05-30
 * Time: 3:48 PM
 */

class ModSecurities_GetSymbolData_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        $symbol = $request->get('symbol');
        $id = ModSecurities_Module_Model::GetModSecuritiesIdBySymbol($symbol);
        if($id != 0){
            $record = ModSecurities_Record_Model::getInstanceById($id);
            $data = $record->getData();
            echo json_encode($data);
        }else
            echo 0;
    }
}
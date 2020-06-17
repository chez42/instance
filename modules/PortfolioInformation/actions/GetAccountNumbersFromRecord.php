<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2018-05-31
 * Time: 3:32 PM
 */
include_once("libraries/reports/new/nCommon.php");

class PortfolioInformation_GetAccountNumbersFromRecord_Action extends Vtiger_BasicAjax_Action{

    public function process(Vtiger_Request $request) {
        $record_id = $request->get('record');
        $setype = GetSettypeFromID($record_id);
        if($setype == 'PortfolioInformation') {
            $record = PortfolioInformation_Record_Model::getInstanceById($record_id);
            $account_number = $record->get("account_number");
            echo json_encode(array($account_number));
            return;
        }
        $account_numbers = array(GetAccountNumbersFromRecord($request->get('record')));
        echo json_encode($account_numbers);
    }
}
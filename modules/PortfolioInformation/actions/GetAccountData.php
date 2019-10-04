<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2018-05-31
 * Time: 4:18 PM
 */

class PortfolioInformation_GetAccountData_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        $account_number = $request->get('account_number');
        $id = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($account_number);
        if($id != 0){
            $record = PortfolioInformation_Record_Model::getInstanceById($id);
            $data = $record->getData();
            echo json_encode($data);
        }else
            echo 0;
    }
}
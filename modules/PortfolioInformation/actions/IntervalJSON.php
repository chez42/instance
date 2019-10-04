<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2017-06-26
 * Time: 11:41 AM
 */

class PortfolioInformation_IntervalJSON_Action extends Vtiger_BasicAjax_Action{
    function process(Vtiger_Request $request) {
        $todo = $request->get('todo');
        $accounts = explode(',', $request->get('account_numbers'));
        switch(strtolower($todo)){
            case "endvalues":
                $result = PortfolioInformation_Module_Model::GetEndValuesForAccounts($accounts);
                echo json_encode($result);
                break;
            case "endvaluesdaily":
                $result = PortfolioInformation_Module_Model::GetEndValuesForAccounts($accounts, null, null, "Daily");
                echo json_encode($result);
                break;
        }
    }
}
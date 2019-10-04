<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2017-06-22
 * Time: 3:05 PM
 */

class PortfolioInformation_OmniOverride_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        switch($request->get('todo')){
            case 'GoToPortfolioInformation':
                echo PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($request->get('account_number'));
                break;
            case 'GoToSecurities':
                echo ModSecurities_Module_Model::GetCrmidFromSymbol($request->get('security_symbol'));
                break;
        }
    }
}

?>
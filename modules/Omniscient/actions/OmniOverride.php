<?php

class Omniscient_OmniOverride_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        switch($request->get('todo')){
            case 'GoToPortfolioInformation':
                echo PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($request->get('account_number'));
                break;
        }
    }
}

?>
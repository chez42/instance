<?php

class PortfolioInformation_Statements_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        switch($request->get('todo')){
            case "save_preferences":
                $preferences = $request->get('preferred_ids');
                PortfolioInformation_Indexes_Model::SavePreferences($preferences);
                break;
        }
    }
}
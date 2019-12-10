<?php

class PortfolioInformation_Statements_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        $statement = new PortfolioInformation_Statements_Model();
        switch($request->get('todo')){
            case "save_prepared_by":
                $prepared_by = $request->get('prepared_by');
                $content = $request->get('content');
                $statement->SavePreparedBy($prepared_by, $content);
                echo htmlspecialchars_decode($statement->GetPreparedByData($prepared_by));
                break;
            case "get_prepared_by":
                $prepared_by = $request->get('prepared_by');
                $statement->GetPreparedByData($prepared_by);
                echo htmlspecialchars_decode($statement->GetPreparedByData($prepared_by));
                break;
        }
    }
}
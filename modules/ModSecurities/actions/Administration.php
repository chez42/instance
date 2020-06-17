<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2017-05-24
 * Time: 4:38 PM
 */
class ModSecurities_Administration_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request){
        $todo = $request->get('todo');
        $id = $request->get('id');
        $value = $request->get('value');
        $field = $request->get('field');
        switch($todo){
            case "UpdateSchwabMappingField":
                ModSecurities_Administration_Model::UpdateSchwabMappingField($id, $field, $value);
                break;
            case "UpdateFidelityMappingField":
                ModSecurities_Administration_Model::UpdateFidelityMappingField($id, $field, $value);
                break;
            case "UpdatePershingMappingField":
                ModSecurities_Administration_Model::UpdatePershingMappingField($id, $field, $value);
                break;
            case "UpdateTDMappingField":
                ModSecurities_Administration_Model::UpdateTDMappingField($id, $field, $value);
                break;
            case "CreateTransactionRow":
#                PortfolioInformation_Administration_Model::CreateRow("vtiger_transaction_type_mapping");
#                echo PortfolioInformation_Administration_Model::GetMaxID("vtiger_transaction_type_mapping");
                break;
            case "CreateFileLocation":
#                PortfolioInformation_Administration_Model::CreateRow("custodian_omniscient.file_locations");
#                echo PortfolioInformation_Administration_Model::GetMaxID("custodian_omniscient.file_locations");
                break;
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2017-05-24
 * Time: 4:38 PM
 */
class PortfolioInformation_Administration_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request){
        $todo = $request->get('todo');
        $id = $request->get('id');
        $value = $request->get('value');
        $field = $request->get('field');
        switch($todo){
            case "UpdateFileField":
                PortfolioInformation_Administration_Model::UpdateFileField($id, $field, $value);
                break;
            case "UpdateMappingField":
                PortfolioInformation_Administration_Model::UpdateMappingField($id, $field, $value);
                break;
            case "UpdateSchwabMappingField":
                PortfolioInformation_Administration_Model::UpdateSchwabMappingField($id, $field, $value);
                break;
            case "UpdateFidelityMappingField":
                PortfolioInformation_Administration_Model::UpdateFidelityMappingField($id, $field, $value);
                break;
            case "UpdatePershingMappingField":
                PortfolioInformation_Administration_Model::UpdatePershingMappingField($id, $field, $value);
                break;
            case "UpdateTDMappingField":
                PortfolioInformation_Administration_Model::UpdateTDMappingField($id, $field, $value);
                break;
            case "UpdateFidelityCashFlowMappingField":
                PortfolioInformation_Administration_Model::UpdateFidelityCashFlowMappingField($id, $field, $value);
                break;
            case "CreateTransactionRow":
                PortfolioInformation_Administration_Model::CreateRow("vtiger_transaction_type_mapping");
                echo PortfolioInformation_Administration_Model::GetMaxID("vtiger_transaction_type_mapping");
                break;
            case "CreateFileLocation":
                PortfolioInformation_Administration_Model::CreateRow("custodian_omniscient.file_locations");
                echo PortfolioInformation_Administration_Model::GetMaxID("custodian_omniscient.file_locations");
                break;
        }
    }

    static public function WriteDownloaderData($custodian, $repcode, $filename){
        global $adb;
        $query = "INSERT INTO custodian_omniscient.downloader_data (custodian, rep_code, filename, copy_date)
                  VALUES (?, ?, ?, NOW())";
        $adb->pquery($query, array($custodian, $repcode, $filename));
    }
}
<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

class VTEEmailMarketing_RelationAjax_Action extends Vtiger_RelationAjax_Action
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod("loadOtherEmailMarketing");
    }
    public function loadOtherEmailMarketing($request)
    {
        global $adb;
        $idEmailMarketing = $request->get("idEmailMarketing");
        $recordId = $request->get("recordId");
        $result = $adb->pquery("SELECT * FROM vtiger_vteemailmarketingrel WHERE vteemailmarketingid = ?", array($idEmailMarketing));
        $numrow = $adb->num_rows($result);
        if (0 < $numrow) {
            for ($i = 0; $i < $numrow; $i++) {
                $relId = $adb->query_result($result, $i, "crmid");
                $relModule = $adb->query_result($result, $i, "module");
                $rsCheckRecord = $adb->pquery("SELECT 1 FROM vtiger_vteemailmarketingrel WHERE vteemailmarketingid = ? AND crmid = ?", array($recordId, $relId));
                if ($adb->num_rows($rsCheckRecord) == 0) {
                    $params = array($recordId, $relId, $relModule);
                    $adb->pquery("INSERT INTO vtiger_vteemailmarketingrel(`vteemailmarketingid`,`crmid`,`module`) VALUES(?,?,?)", $params);
                }
                $checkCrmEntity = $adb->pquery("SELECT 1 FROM vtiger_crmentityrel WHERE crmid = ? AND relcrmid =?", array($relId, $recordId));
                if ($adb->num_rows($checkCrmEntity) == 0) {
                    $params1 = array($relId, $relModule, $recordId, "VTEEmailMarketing");
                    $adb->pquery("INSERT INTO vtiger_crmentityrel(`crmid`,`module`,`relcrmid`,`relmodule`) VALUES(?,?,?,?)", $params1);
                }
            }
        }
        $recordModel = Vtiger_Record_Model::getInstanceById($idEmailMarketing, "VTEEmailMarketing");
        $name = $recordModel->getName();
        $response = new Vtiger_Response();
        $response->setResult($name);
        $response->emit();
    }
    public function addRelation($request)
    {
        $sourceModule = $request->getModule();
        $sourceRecordId = $request->get("src_record");
        $relatedModule = $request->get("related_module");
        $relatedRecordIdList = $request->get("related_record_list");
        $sourceModuleModel = Vtiger_Module_Model::getInstance($sourceModule);
        $relatedModuleModel = Vtiger_Module_Model::getInstance($relatedModule);
        $relationModel = Vtiger_Relation_Model::getInstance($sourceModuleModel, $relatedModuleModel);
        $relationModuleModule = Vtiger_Relation_Model::getInstance($relatedModuleModel, $sourceModuleModel);
        foreach ($relatedRecordIdList as $relatedRecordId) {
            $relationModel->addRelation($sourceRecordId, $relatedRecordId);
            $relationModuleModule->addRelation($relatedRecordId, $sourceRecordId);
        }
        $response = new Vtiger_Response();
        $response->setResult(true);
        $response->emit();
    }
}

?>
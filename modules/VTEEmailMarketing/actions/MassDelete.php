<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

class VTEEmailMarketing_MassDelete_Action extends Vtiger_MassDelete_Action
{
    public function process(Vtiger_Request $request)
    {
        global $adb;
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        if ($request->get("selected_ids") == "all" && $request->get("mode") == "FindDuplicates") {
            $recordIds = Vtiger_FindDuplicate_Model::getMassDeleteRecords($request);
        } else {
            $recordIds = $this->getRecordsListFromRequest($request);
        }
        $cvId = $request->get("viewname");
        foreach ($recordIds as $recordId) {
            if (Users_Privileges_Model::isPermitted($moduleName, "Delete", $recordId)) {
                $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleModel);
                $adb->pquery("DELETE FROM vtiger_vteemailmarketingrel WHERE vteemailmarketingid = ? ", array($recordId));
                $adb->pquery("DELETE FROM vtiger_vteemailmarketing_unsubcribes WHERE vteemailmarketingid = ? ", array($recordId));
                $adb->pquery("DELETE FROM vtiger_vteemailmarketing_schedule WHERE vteemailmarketingid = ? ", array($recordId));
                $recordModel->delete();
                deleteRecordFromDetailViewNavigationRecords($recordId, $cvId, $moduleName);
            }
        }
        $response = new Vtiger_Response();
        $response->setResult(array("viewname" => $cvId, "module" => $moduleName));
        $response->emit();
    }
}

?>
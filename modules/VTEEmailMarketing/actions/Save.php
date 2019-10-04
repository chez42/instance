<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

class VTEEmailMarketing_Save_Action extends Vtiger_Save_Action
{
    public function process(Vtiger_Request $request)
    {
        $recordModel = $this->saveRecord($request);
        if ($request->get("returntab_label")) {
            $loadUrl = "index.php?" . $request->getReturnURL();
        } else {
            if ($request->get("relationOperation")) {
                $parentModuleName = $request->get("sourceModule");
                $parentRecordId = $request->get("sourceRecord");
                $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentRecordId, $parentModuleName);
                $loadUrl = $parentRecordModel->getDetailViewUrl();
            } else {
                if ($request->get("returnToList")) {
                    $loadUrl = $recordModel->getModule()->getListViewUrl();
                } else {
                    if ($request->get("returnmodule") && $request->get("returnview")) {
                        $loadUrl = "index.php?" . $request->getReturnURL();
                    } else {
                        $loadUrl = $recordModel->getDetailViewUrl();
                    }
                }
            }
        }
        $appName = $request->get("appName");
        if (0 < strlen($appName)) {
            $loadUrl = $loadUrl . $appName;
        }
        header("Location: " . $loadUrl);
    }
    public function saveRecord($request)
    {
        global $adb;
        if ($request->get("isCreate") == 1) {
            $idEmailMarketing = $request->get("idEmailMarketing");
            $templateId = $request->get("templateEmail");
            $totalRecord = $request->get("totalRecord");
            $getSchedule = $adb->pquery("SELECT * FROM vtiger_vteemailmarketing_schedule WHERE vteemailmarketingid = ?", array($idEmailMarketing));
            $checkBatchDelivery = $adb->query_result($getSchedule, 0, "batch_delivery");
            $batch_delivery = "Off";
            $scheduled = $adb->query_result($getSchedule, 0, "datetime");
            if ($checkBatchDelivery == 1) {
                $number_email = $adb->query_result($getSchedule, 0, "number_email");
                $frequency = $adb->query_result($getSchedule, 0, "frequency");
                $frequency = $frequency / 60;
                $scheduled = $adb->query_result($getSchedule, 0, "datetime");
                $batch_delivery = $number_email . " emails every " . $frequency . " minutes";
            }
            $subject = $this->getTemplateEmail($templateId);
            if ($request->get("from_serveremailid")) {
                $result = $adb->pquery("SELECT vte_multiple_smtp.*,vtiger_users.first_name,vtiger_users.last_name FROM vte_multiple_smtp \r\n                                        INNER JOIN vtiger_users ON vtiger_users.id = vte_multiple_smtp.userid WHERE vte_multiple_smtp.id =?", array($request->get("from_serveremailid")));
                $first_name = $adb->query_result($result, 0, "first_name");
                $last_name = $adb->query_result($result, 0, "last_name");
                $server = $adb->query_result($result, 0, "server");
                $mail = $adb->query_result($result, 0, "server_username");
                $name = $first_name ? $first_name . " " . $last_name : $last_name;
                $smtp_server = (string) $name . " - " . $server . " - " . $mail;
            } else {
                $result = $adb->pquery("SELECT * FROM vtiger_systems");
                $server = $adb->query_result($result, 0, "server");
                $mail = $adb->query_result($result, 0, "server_username");
                $name = "System Outgoing Email Server";
                $smtp_server = (string) $name . " - " . $server . " - " . $mail;
            }
            $status = $request->get("status");
            if (!empty($idEmailMarketing)) {
                $recordModel = Vtiger_Record_Model::getInstanceById($idEmailMarketing, "VTEEmailMarketing");
                $recordModel->set("id", $idEmailMarketing);
                $recordModel->set("mode", "edit");
            } else {
                $recordModel = Vtiger_Record_Model::getCleanInstance("VTEEmailMarketing");
                $recordModel->set("mode", "");
            }
            $recordModel->set("subject", $subject);
            $recordModel->set("scheduled", $scheduled);
            $recordModel->set("batch_delivery", $batch_delivery);
            $recordModel->set("total", $totalRecord);
            $recordModel->set("queued", $totalRecord);
            $recordModel->set("vteemailmarketing_status", $status);
            $recordModel->set("smtp_server", $smtp_server);
            $recordModel->save();
            $this->savedRecordId = $recordModel->getId();
            return $recordModel;
        }
        $recordModel = $this->getRecordModelFromRequest($request);
        if ($request->get("imgDeleted")) {
            $imageIds = $request->get("imageid");
            foreach ($imageIds as $imageId) {
                $status = $recordModel->deleteImage($imageId);
            }
        }
        $recordModel->save();
        if ($request->get("relationOperation")) {
            $parentModuleName = $request->get("sourceModule");
            $parentModuleModel = Vtiger_Module_Model::getInstance($parentModuleName);
            $parentRecordId = $request->get("sourceRecord");
            $relatedModule = $recordModel->getModule();
            $relatedRecordId = $recordModel->getId();
            if ($relatedModule->getName() == "Events") {
                $relatedModule = Vtiger_Module_Model::getInstance("Calendar");
            }
            $relationModel = Vtiger_Relation_Model::getInstance($parentModuleModel, $relatedModule);
            $relationModel->addRelation($parentRecordId, $relatedRecordId);
        }
        $this->savedRecordId = $recordModel->getId();
        return $recordModel;
    }
    public function getTemplateEmail($templateId)
    {
        global $adb;
        $result = $adb->pquery("SELECT * FROM vtiger_emailtemplates WHERE templateid = ?", array($templateId));
        $subject = $adb->query_result($result, 0, "subject");
        return $subject;
    }
}

?>
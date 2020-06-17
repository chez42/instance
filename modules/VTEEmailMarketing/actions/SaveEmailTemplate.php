<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

class VTEEmailMarketing_SaveEmailTemplate_Action extends EmailTemplates_Save_Action
{
    public function checkPermission(Vtiger_Request $request)
    {
    }
    public function process(Vtiger_Request $request)
    {
        $site_URL = vglobal("site_URL");
        $moduleName = "EmailTemplates";
        $record = $request->get("record");
        $emitResponse = $request->get("emitResponse");
        $recordModel = new EmailTemplates_Record_Model();
        $recordModel->setModule($moduleName);
        if (!empty($record)) {
            $recordModel->setId($record);
        }
        $recordModel->set("templatename", $request->get("templatename"));
        $recordModel->set("description", $request->get("description"));
        $recordModel->set("subject", $request->get("subject"));
        $recordModel->set("module", $request->get("modulename"));
        $recordModel->set("systemtemplate", $request->get("systemtemplate"));
        $content = $request->getRaw("templatecontent");
        $processedContent = Emails_Mailer_Model::getProcessedContent($content);
        $recordModel->set("body", $processedContent);
        $recordId = $recordModel->save();
        $recordModel->updateImageName($recordId);
        if ($request->get("returnmodule") && $request->get("returnview")) {
            $loadUrl = "index.php?" . $request->getReturnURL();
        } else {
            if ($request->get("returnmodule") && $request->get("returnview")) {
                $loadUrl = "index.php?" . $request->getReturnURL();
            } else {
                $loadUrl = $recordModel->getDetailViewUrl();
            }
            header("Location: " . $loadUrl);
        }
        global $adb;
        $idtemplate = $recordModel->get("templateid");
        $metadata = $request->get("metadata");
        $template = $request->get("template");
        $base64Image = $request->get("base64image");
        $keytemplate = $request->get("keytemplate");
        if ($base64Image != "") {
            $thumbnailUrl = $this->base64_to_jpeg($base64Image, "test/mosaico/uploads/thumbnails/thumb_template_" . $keytemplate . $idtemplate . ".png");
        } else {
            $thumbnailUrl = $request->get("thumbnailUrl");
        }
        $checkExisted = "SELECT * FROM vtiger_vteemailmarketing_emailtemplate WHERE idtemplate = ?";
        $result = $adb->pquery($checkExisted, array($idtemplate));
        $numRows = $adb->num_rows($result);
        if ($numRows == 0) {
            $sql = "INSERT INTO vtiger_vteemailmarketing_emailtemplate (idtemplate, metadata, template, keytemplate,thumbnail) VALUES (?, ?, ?,?,?);";
            $adb->pquery($sql, array($idtemplate, $metadata, $template, $keytemplate, $thumbnailUrl));
        } else {
            $sql = "UPDATE vtiger_vteemailmarketing_emailtemplate SET metadata = ?, template = ?, keytemplate = ?, thumbnail = ? WHERE idtemplate = ?;";
            $adb->pquery($sql, array($metadata, $template, $keytemplate, $thumbnailUrl, $idtemplate));
        }
    }
    public function base64_to_jpeg($base64_string, $output_file)
    {
        $ifp = fopen($output_file, "wb");
        $data = explode(",", $base64_string);
        fwrite($ifp, base64_decode($data[1]));
        fclose($ifp);
        return $output_file;
    }
}

?>
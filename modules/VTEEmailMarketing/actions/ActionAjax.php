<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

class VTEEmailMarketing_ActionAjax_Action extends Vtiger_Action_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod("enableModule");
        $this->exposeMethod("viewStep4");
        $this->exposeMethod("saveEmailMarketing");
        $this->exposeMethod("saveScheduleLater");
        $this->exposeMethod("getRecordNewFilterStep2");
        $this->exposeMethod("getTotalRelated");
        $this->exposeMethod("saveRelatedRecord");
        $this->exposeMethod("getRelatedRecordVTEEMailMarketing");
        $this->exposeMethod("TestSendMail");
        $this->exposeMethod("getKeyMosaicoTemplateEdit");
        $this->exposeMethod("paggingDetailRelatedRecord");
        $this->exposeMethod("actionResubcribes");
        $this->exposeMethod("duplicateTemplate");
        $this->exposeMethod("actionSchedulerOnDetailView");
        $this->exposeMethod("deleteRelatedRecord");
    }
    public function checkPermission(Vtiger_Request $request)
    {
    }
    public function enableModule(Vtiger_Request $request)
    {
        global $adb;
        $value = $request->get("value");
        $adb->pquery("UPDATE `vteemailmarketing_settings` SET `enable`=?", array($value));
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array("result" => "success"));
        $response->emit();
    }
    public function process(Vtiger_Request $request)
    {
        $mode = $request->get("mode");
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
        }
    }
    public function viewStep4(Vtiger_Request $request)
    {
        $idTemplateEmail = $request->get("idTemplateEmail");
        $idEmailMarketing = $request->get("idEmailMarketing");
        $vteCampaigns = VTEEmailMarketing_Record_Model::getVTEEmailMarketing($idEmailMarketing);
        $emailTemplate = VTEEmailMarketing_Record_Model::getEmailTemplate($idTemplateEmail);
        $data = array("campaign" => $vteCampaigns, "template" => $emailTemplate);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    public function saveEmailMarketing(Vtiger_Request $request)
    {
        global $adb;
        $moduleName = $request->getModule();
        $vteCampaignName = $request->get("vteCampaignName");
        $vteFromName = $request->get("vteFrom_Name");
        $vteFromEmail = $request->get("vteFrom_Email");
        $vteAssignedTo = $request->get("assignedTo");
        $sender = $vteFromName . " (" . $vteFromEmail . ")";
        $idEmailMarketing = $request->get("idEmailMarketing");
        if ($idEmailMarketing) {
            $emailMarketingRecord = Vtiger_Record_Model::getInstanceById($idEmailMarketing);
            $mode = "edit";
            
            $adb->pquery("DELETE FROM vtiger_vteemailmarketingrel WHERE vteemailmarketingid = ?",
                array($idEmailMarketing));
            
        } else {
            $emailMarketingRecord = Vtiger_Record_Model::getCleanInstance($moduleName);
            $mode = "";
        }
        $emailMarketingRecord->set("mode", $mode);
        $emailMarketingRecord->set("vtecampaigns", $vteCampaignName);
        $emailMarketingRecord->set("sender", $sender);
        $emailMarketingRecord->set("assigned_user_id", $vteAssignedTo);
        $emailMarketingRecord->save();
        $recordId = $emailMarketingRecord->getId();
        
        $adb->pquery("UPDATE vtiger_vteemailmarketing SET scheduled = NULL WHERE vteemailmarketingid = ?",
            array($recordId));
        
        $response = new Vtiger_Response();
        $response->setResult($recordId);
        $response->emit();
    }
    public function convertDBTimeZone($datetime)
    {
        $DBTimeZone = DateTimeField::convertToDBTimeZone($datetime);
        $DBDateTime = $DBTimeZone->format("Y-m-d H:i:s");
        return $DBDateTime;
    }
    public function saveScheduleLater(Vtiger_Request $request)
    {
        global $adb;
        $recordId = $request->get("recordId");
        $date = $request->get("schedule_date");
        $time = $request->get("schedule_time");
        if ($date && $time) {
            $date = DateTimeField::convertToDBFormat($date);
            $new_time = DateTime::createFromFormat("h:i A", $time);
            $time = $new_time->format("H:i:s");
            $datetime = $date . " " . $time;
            $schedule_datetime = self::convertDBTimeZone($datetime);
        } else {
            $currentUserDateTime = DateTimeField::convertToUserTimeZone(date("Y-m-d H:i:s"));
            $currenDBDateTime = DateTimeField::convertToDBTimeZone($currentUserDateTime->format("Y-m-d H:i:s"));
            $schedule_datetime = $currenDBDateTime->format("Y-m-d H:i:s");
        }
        $schedule_batch_delivery = $request->get("schedule_batch_delivery");
        $schedule_templateEmail = $request->get("templateEmail");
        $schedule_from_name = $request->get("from_name");
        $schedule_from_email = $request->get("from_email");
        $total_email = $request->get("total_email");
        if ($schedule_batch_delivery == 0) {
            $schedule_number_email = 500;
            $schedule_frequency = 900;
        } else {
            $schedule_number_email = $request->get("schedule_number_email");
            $schedule_frequency = $request->get("schedule_frequency");
        }
        $from_serveremailid = $request->get("from_serveremailid");
        $checkRecord = $adb->pquery("SELECT 1 FROM vtiger_vteemailmarketing_schedule WHERE vteemailmarketingid = ?", array($recordId));
        $numrow = $adb->num_rows($checkRecord);
        if (0 < $numrow) {
            $query = "UPDATE vtiger_vteemailmarketing_schedule SET `datetime`=?, `batch_delivery`=?, `number_email`=?, `frequency`=?, `total_email`=?, `template_email_id` = ?, `from_name` = ? ,`from_email` = ? WHERE vteemailmarketingid = ?";
            $params = array($schedule_datetime, $schedule_batch_delivery, $schedule_number_email, $schedule_frequency, $total_email, $schedule_templateEmail, $schedule_from_name, $schedule_from_email, $recordId);
            $adb->pquery($query, $params);
        } else {
            $query = "INSERT INTO vtiger_vteemailmarketing_schedule(`vteemailmarketingid`,`datetime`,`batch_delivery`,`number_email`,`frequency`,`total_email`,`template_email_id`,`from_name`,`from_email`,`from_serveremailid`) VALUES(?,?,?,?,?,?,?,?,?,?)";
            $params = array($recordId, $schedule_datetime, $schedule_batch_delivery, $schedule_number_email, $schedule_frequency, $total_email, $schedule_templateEmail, $schedule_from_name, $schedule_from_email, $from_serveremailid);
            $adb->pquery($query, $params);
        }
        $response = new Vtiger_Response();
        $response->setResult(true);
        $response->emit();
    }
    public function getRecordNewFilterStep2(Vtiger_Request $request)
    {
        global $adb;
        $cvId = $request->get("cvId");
        $query = "SELECT `cvid`,`viewname`,`entitytype` ,`first_name`,`last_name`\n                FROM vtiger_customview JOIN vtiger_users ON vtiger_customview.userid = vtiger_users.id\n                WHERE vtiger_customview.cvid = ?";
        $result = $adb->pquery($query, array($cvId));
        $firstName = $adb->query_result($result, 0, "first_name");
        $lastName = $adb->query_result($result, 0, "last_name");
        $firstName == " " ? $name = $firstName . " " . $lastName : ($name = $lastName);
        $moduleName = $adb->query_result($result, 0, "entitytype");
        $filterName = $adb->query_result($result, 0, "viewname");
        $getCounts = VTEEmailMarketing_Record_Model::getCountRecordFilter($cvId, $moduleName);
        $data = array("moduleName" => $moduleName, "filterName" => $filterName, "name" => $name, "count" => $getCounts);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    public function getTotalRelated(Vtiger_Request $request)
    {
        global $adb;
        $recordId = $request->get("recordId");
        $result = $adb->pquery("SELECT COUNT(*) as 'count' FROM vtiger_vteemailmarketingrel WHERE vteemailmarketingid =?", array($recordId));
        $data = $adb->query_result($result, 0, "count");
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    public function saveRelatedRecord(Vtiger_Request $request)
    {
        global $adb;
        $cvid = $request->get("cvid");
        $recordId = $request->get("recordId");
        $relModule = $request->get("relModule");
        $count = 0;
        $arrayId = VTEEmailMarketing_Record_Model::getAllRecordIdFilter($relModule, $cvid);
        for ($i = 0; $i < count($arrayId); $i++) {
            $checkRecord = $adb->pquery("SELECT 1 FROM vtiger_vteemailmarketingrel WHERE vteemailmarketingid=? AND crmid = ? AND cvid=? ", array($recordId, $arrayId[$i], $cvid));
            $numrows = $adb->num_rows($checkRecord);
            if ($numrows == 0) {
                $params = array($recordId, $arrayId[$i], $relModule, $cvid);
                $adb->pquery("INSERT INTO vtiger_vteemailmarketingrel(`vteemailmarketingid`,`crmid`,`module`, `cvid`) VALUES(?,?,?,?)", $params);
                $count++;
            }
            $checkCrmEntity = $adb->pquery("SELECT 1 FROM vtiger_crmentityrel WHERE crmid = ? AND relcrmid =?", array($arrayId[$i], $recordId));
            if ($adb->num_rows($checkCrmEntity) == 0) {
                $params1 = array($arrayId[$i], $relModule, $recordId, "VTEEmailMarketing");
                $adb->pquery("INSERT INTO vtiger_crmentityrel(`crmid`,`module`,`relcrmid`,`relmodule`) VALUES(?,?,?,?)", $params1);
            }
        }
        if ($count == 0) {
            $count = "0";
        }
        $response = new Vtiger_Response();
        $response->setResult($count);
        $response->emit();
    }
    public function getRelatedRecordVTEEMailMarketing(Vtiger_Request $request)
    {
        global $adb;
        if ($request->get("pageNumber") == "") {
            $pageNumber = 1;
        } else {
            $pageNumber = intval($request->get("pageNumber"));
        }
        $pageLimit = 10;
        $startIndex = ($pageNumber - 1) * $pageLimit;
        $recordId = $request->get("recordId");
        $query = ' SELECT
                        vtiger_vteemailmarketingrel.crmid ,
                        vtiger_crmentity.label ,
                        vtiger_vteemailmarketingrel.module ,
                        vtiger_leaddetails.email AS "lead_email" ,
                        vtiger_contactdetails.email AS "contacts_email" ,
                        vtiger_account.email1 AS "account_email",
                        vtiger_potentialscf.cf_869 AS "potentials_email"
                FROM vtiger_vteemailmarketingrel
                INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_vteemailmarketingrel.crmid
                LEFT JOIN vtiger_leaddetails ON vtiger_vteemailmarketingrel.crmid = vtiger_leaddetails.leadid
                LEFT JOIN vtiger_leadaddress ON vtiger_vteemailmarketingrel.crmid = vtiger_leadaddress.leadaddressid
                LEFT JOIN vtiger_contactdetails ON vtiger_vteemailmarketingrel.crmid = vtiger_contactdetails.contactid
                LEFT JOIN vtiger_account ON vtiger_vteemailmarketingrel.crmid = vtiger_account.accountid
                LEFT JOIN vtiger_potentialscf ON vtiger_vteemailmarketingrel.crmid = vtiger_potentialscf.potentialid
                WHERE
                    vtiger_vteemailmarketingrel.vteemailmarketingid = ?
                ORDER BY vtiger_crmentity.label';
        
        $params = array($recordId);
        $result = $adb->pquery($query, $params);
        
        $numrow = $adb->num_rows($result);
        $maxPage = ceil($numrow / $pageLimit);
        $data = array();
        $data["pagging"] = (object) array();
        if ($pageNumber <= $maxPage) {
            $query = $query . " LIMIT " . $startIndex . "," . $pageLimit;
            $resultLimit = $adb->pquery($query, $params);
            $numRowLimit = $adb->num_rows($resultLimit);
            for ($i = 0; $i < $numRowLimit; $i++) {
                $data["list"][$i] = $adb->query_result_rowdata($resultLimit, $i);
            }
            $data["pagging"] = (object) array("pageNumber" => $pageNumber, "maxPage" => $maxPage, "startIndex" => $startIndex + 1, "endIndex" => $startIndex + $numRowLimit, "maxRecord" => $numrow);
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    public function TestSendMail(Vtiger_Request $request)
    {
        $outgoingServer = $request->get("outgoing_server");
        if ($outgoingServer == "") {
            $mailerInstance = Emails_Mailer_Model::getInstance();
        } else {
            $_REQUEST["from_serveremailid"] = $outgoingServer;
            $mailerInstance = MultipleSMTP_Mailer_Model::getInstance();
        }
        $templateEmail = self::getTemplateEmail($request->get("templateEmail"));
        $content = $templateEmail["content"];
        $subject = $templateEmail["subject"];
        $processedContent = Emails_Mailer_Model::getProcessedContent($content);
        $mailerInstance->isHTML(true);
        $processedContentWithURLS = $mailerInstance->convertToValidURL($processedContent);
        $from_email = $request->get("from_email");
        $from_name = $request->get("from_name");
        $to = $request->get("to");
        $mailerInstance->AddAddress($to);
        $mailerInstance->From = $from_email;
        $mailerInstance->FromName = decode_html($from_name);
        $mailerInstance->Subject = strip_tags(decode_html($subject));
        $mailerInstance->Body = decode_emptyspace_html($processedContentWithURLS);
        $mailerInstance->Body = Emails_Mailer_Model::convertCssToInline($mailerInstance->Body);
        $mailerInstance->Body = Emails_Mailer_Model::makeImageURLValid($mailerInstance->Body);
        $plainBody = decode_html($processedContentWithURLS);
        $plainBody = preg_replace(array("/<p>/i", "/<br>/i", "/<br \\/>/i"), array("\n", "\n", "\n"), $plainBody);
        $plainBody = strip_tags($plainBody);
        $plainBody = Emails_Mailer_Model::convertToAscii($plainBody);
        $mailerInstance->AltBody = $plainBody;
        $status = $mailerInstance->send(true);
        if ($status === true) {
            $message = "Email has been sent";
        } else {
            $message = $status;
        }
        $html = "                <div class=\"modal-dialog\">\n                <div class=\"modal-content\">\n                    <div class=\"modal-header\"><div class=\"clearfix\"><div class=\"pull-right \" ><button type=\"button\" class=\"close\" aria-label=\"Close\" data-dismiss=\"modal\"><span aria-hidden=\"true\" class='fa fa-close'></span></button></div><h4 class=\"pull-left\">Result</h4></div></div>     \n                    <div class=\"modal-body\">\n                        <div class=\"mailSentSuccessfully\" data-relatedload=\"\">\n                                " . $message . "\n                        </div>\n                </div>\n            </div>";
        $response = new Vtiger_Response();
        $response->setResult($html);
        $response->emit();
    }
    public function getTemplateEmail($templateId)
    {
        global $adb;
        $result = $adb->pquery("SELECT * FROM vtiger_emailtemplates WHERE templateid = ?", array($templateId));
        $data["subject"] = $adb->query_result($result, 0, "subject");
        $data["content"] = $adb->query_result($result, 0, "body");
        return $data;
    }
    public function getKeyMosaicoTemplateEdit(Vtiger_Request $request)
    {
        global $adb;
        $idtemplate = $request->get("idTemplate");
        $sql = "SELECT * FROM vtiger_vteemailmarketing_emailtemplate WHERE idtemplate = ?";
        $result = $adb->pquery($sql, array($idtemplate));
        $data = array();
        if ($result != false) {
            $resultRaw = $adb->raw_query_result_rowdata($result, 0);
            $data["keytemplate"] = $resultRaw["keytemplate"];
            $data["thumbnailUrl"] = $resultRaw["thumbnail"];
            $data["metadata"] = $resultRaw["metadata"];
            $data["template"] = $resultRaw["template"];
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    public function paggingDetailRelatedRecord(Vtiger_Request $request)
    {
        $recordId = $request->get("recordId");
        $page = $request->get("page");
        $dispayType = $request->get("dispayType");
        $data = array();
        $data["list"] = VTEEmailMarketing_Record_Model::getRecordRelatedSentEmail($recordId, $page, $dispayType);
        $data["pagging"] = VTEEmailMarketing_Record_Model::getPaginationRelatedSentEmail($recordId, $page, $dispayType);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    public function duplicateTemplate(Vtiger_Request $request)
    {
        $moduleName = "EmailTemplates";
        $idTemplate = $request->get("idtemlate");
        $recordModelOld = EmailTemplates_Record_Model::getInstanceById($idTemplate, $moduleName);
        $newTemplateName = $recordModelOld->get("templatename");
        $newTemplateName .= "(duplicated)";
        $recordModel = new EmailTemplates_Record_Model();
        $recordModel->set("templatename", $newTemplateName);
        $recordModel->set("subject", $recordModelOld->get("subject"));
        $recordModel->set("description", $recordModelOld->get("description"));
        $recordModel->set("systemtemplate", $recordModelOld->get("systemtemplate"));
        $recordModel->set("module", $recordModelOld->get("module"));
        $recordModel->set("body", decode_html($recordModelOld->get("body")));
        EmailTemplates_Module_Model::saveRecord($recordModel);
        $recordId = $recordModel->get("templateid");
        global $adb;
        $rs = $adb->pquery("SELECT * FROM vtiger_vteemailmarketing_emailtemplate WHERE idtemplate = ? LIMIT 1", array($idTemplate));
        if ($adb->num_rows($rs) == 1) {
            $metadata = $adb->query_result($rs, 0, "metadata");
            $template = $adb->query_result($rs, 0, "template");
            $keytemplate = $adb->query_result($rs, 0, "keytemplate");
            $thumbnailUrl = $adb->query_result($rs, 0, "thumbnail");
            $sql = "INSERT INTO vtiger_vteemailmarketing_emailtemplate (idtemplate, metadata, template, keytemplate,thumbnail) VALUES (?, ?, ?,?,?);";
            $adb->pquery($sql, array($recordId, $metadata, $template, $keytemplate, $thumbnailUrl));
            $response = new Vtiger_Response();
            $response->setResult(true);
            $response->emit();
        }
    }
    public function actionSchedulerOnDetailView(Vtiger_Request $request)
    {
        global $adb;
        $status = $request->get("status");
        $recordId = $request->get("recordId");
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId);
        $recordModel->set("mode", "edit");
        switch ($status) {
            case "Stop":
                $message = "Campaign has been stopped. No more emails will be sent and you will not be ale to resume this campaign.";
                $adb->pquery("UPDATE vtiger_vteemailmarketing_schedule SET `status` = ? WHERE vteemailmarketingid = ?", array($status, $recordId));
                $adb->pquery("UPDATE vtiger_vteemailmarketingrel SET `status` = 0, error_info = ? WHERE vteemailmarketingid = ? AND `status` is null", array($message, $recordId));
                $rsFailed = $adb->pquery("SELECT COUNT(*) as `count_failed` FROM vtiger_vteemailmarketingrel WHERE vteemailmarketingid = ? AND status IN(0,2)", array($recordId));
                $countFailed = $adb->query_result($rsFailed, 0, "count_failed");
                $recordModel->set("vteemailmarketing_status", $status);
                $recordModel->set("queued", 0);
                $recordModel->set("failed_to_send", $countFailed);
                break;
            case "Resume":
                $adb->pquery("UPDATE vtiger_vteemailmarketing_schedule SET `status` = ? WHERE vteemailmarketingid = ?", array("Sending", $recordId));
                $recordModel->set("vteemailmarketing_status", "Sending");
                $message = "Campaign has been resumed. The emails will start going out next time scheduler runs (usually within 15 minutes).";
                break;
            case "Pause":
                $adb->pquery("UPDATE vtiger_vteemailmarketing_schedule SET `status` = ? WHERE vteemailmarketingid = ?", array($status, $recordId));
                $recordModel->set("vteemailmarketing_status", "Paused");
                $message = "Campaign has been paused. The emails will not go out until you resume it.";
                break;
            case "Retry Failed":
                $failedToSend = $request->get("failed_to_send");
                $adb->pquery("UPDATE vtiger_vteemailmarketing_schedule SET `status` = ?, count_sent = 0 WHERE vteemailmarketingid = ?", array("Sending", $recordId));
                $adb->pquery("UPDATE vtiger_vteemailmarketingrel SET `status` = NULL, error_info = ? WHERE vteemailmarketingid =? AND `status` IN(0,2)", array("", $recordId));
                $recordModel->set("vteemailmarketing_status", "Sending");
                $recordModel->set("queued", $failedToSend);
                $recordModel->set("failed_to_send", 0);
                $message = "Failed emails have been re-queued. The emails will start going out next time scheduler runs (usually within 15 minutes).";
                break;
            default:
                $message = "";
                break;
        }
        $recordModel->save();
        $response = new Vtiger_Response();
        $response->setResult($message);
        $response->emit();
    }
    public function actionResubcribes(Vtiger_Request $request)
    {
        global $adb;
        $crmid = $request->get("crmid");
        $recordId = $request->get("recordId");
        $adb->pquery("UPDATE vtiger_vteemailmarketing_unsubcribes SET status = 0, vteemailmarketingid = null WHERE crmid = ?", array($crmid));
        $adb->pquery("UPDATE vtiger_vteemailmarketingrel set error_info = ? WHERE crmid = ? AND status = \"2\"", array("Failed to Send", $crmid));
        $sqlUnsubscribe = "SELECT DISTINCT count(rel.crmid) as \"count\"\n                FROM vtiger_vteemailmarketingrel rel\n                INNER JOIN vtiger_vteemailmarketing_unsubcribes un\n                ON rel.crmid = un.crmid WHERE rel.vteemailmarketingid = ? AND rel.error_info = ?";
        $resultUnsubcribe = $adb->pquery($sqlUnsubscribe, array($recordId, "Unsubscribed in previous campaign"));
        $countUnsubcribe = $adb->query_result($resultUnsubcribe, 0, "count");
        $data = array();
        $data["success"] = true;
        $data["unsubcribe"] = $countUnsubcribe;
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    
    public function deleteRelatedRecord(Vtiger_Request $request){
        
        global $adb;
        $cvid = $request->get("cvid");
        $recordId = $request->get("recordId");
        $relModule = $request->get("relModule");
        
        $arrayId = VTEEmailMarketing_Record_Model::getAllRecordIdFilter($relModule, $cvid);
        
        for ($i = 0; $i < count($arrayId); $i++) {
            $checkRecord = $adb->pquery("SELECT 1 FROM vtiger_vteemailmarketingrel WHERE vteemailmarketingid=? AND crmid = ? AND cvid=? ", array($recordId, $arrayId[$i], $cvid));
            $numrows = $adb->num_rows($checkRecord);
           
            if ($numrows) {
                $params = array($recordId, $arrayId[$i], $relModule, $cvid);
                $adb->pquery("DELETE FROM vtiger_vteemailmarketingrel WHERE `vteemailmarketingid`=? AND `crmid`=? AND `module`=? AND `cvid`=?", $params);
               
            }
            $checkCrmEntity = $adb->pquery("SELECT 1 FROM vtiger_vteemailmarketingrel WHERE crmid = ? AND vteemailmarketingid =?", array($arrayId[$i], $recordId));
            if (!$adb->num_rows($checkCrmEntity)) {
                $adb->pquery("DELETE FROM vtiger_crmentityrel WHERE crmid = ? AND relcrmid =?", array($arrayId[$i], $recordId));
            }
            
        }
        
        $response = new Vtiger_Response();
        $response->setResult(true);
        $response->emit();
        
    }
    
}

?>
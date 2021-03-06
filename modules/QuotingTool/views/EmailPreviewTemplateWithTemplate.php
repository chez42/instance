<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

include_once "modules/QuotingTool/QuotingTool.php";
/**
 * Class QuotingTool_EmailPreviewTemplate_View
 */
class QuotingTool_EmailPreviewTemplateWithTemplate_View extends Vtiger_IndexAjax_View
{
    /**
     * @param Vtiger_Request $request
     */
    public function process(Vtiger_Request $request)
    {
        global $site_URL;
        global $current_user;
        global $adb;
        $moduleName = $request->getModule();
        $viewer = $this->getViewer($request);
        $recordId = $request->get("record");
        $templateId = $request->get("template_id");
        $isCreateNewRecord = $request->get("isCreateNewRecord");
        $childModule = $request->get("childModule");
        $recordModel = new QuotingTool_Record_Model();
        $quotingToolSettingRecordModel = new QuotingTool_SettingRecord_Model();
        $record = $recordModel->getById($templateId);
        $relModule = $record->get("module");
        $quotingTool = new QuotingTool();
        $contentOfTemplate = base64_decode($record->get("content"));
        $varContent = $quotingTool->getVarFromString($contentOfTemplate);
        $hasSignature = 0;
        if (strpos($contentOfTemplate, "quoting_tool-widget-secondary_signature-main") !== false && strpos($contentOfTemplate, "quoting_tool-widget-signature-main")) {
            $hasSignature = 1;
        }
        $customFunction = json_decode(html_entity_decode($record->get("custom_function")));
        $record = $record->decompileRecord($recordId, array("content", "header", "footer", "email_subject", "email_content"), array(), $customFunction);
        $transactionRecordModel = new QuotingTool_TransactionRecord_Model();
        $full_content = $record->get("content");
        $tmp_html = str_get_html($full_content);
        foreach ($tmp_html->find("img") as $img) {
            $json_data_info = $img->getAttribute("data-info");
            $data_info = json_decode(html_entity_decode($json_data_info));
            $img_class = $img->getAttribute("class");
            if ($data_info) {
                $field_id = $data_info->settings_field_image_fields;
                if (0 < $field_id) {
                    $field_model = Vtiger_Field_Model::getInstance($field_id);
                    $field_name = $field_model->getName();
                    $related_record_model = Vtiger_Record_Model::getInstanceById($recordId);
                    if ($related_record_model->get($field_name) != "") {
                        $img_path_array = explode("\$\$", $related_record_model->get($field_name));
                        $img->setAttribute("src", $site_URL . $img_path_array[0]);
                    } else {
                        $img->outertext = "";
                    }
                }
            }
        }
        $signatureImageIndex = 1;
        foreach ($tmp_html->find("img") as $img) {
            $img_class = $quoting_tool_product_image = $img->getAttribute("class");
            if ($quoting_tool_product_image == "quoting_tool_product_image") {
                $product_id = $img->getAttribute("data-productid");
                if ($product_id) {
                    $existingImageSql = "SELECT\r\n                                path,name,vtiger_attachments.attachmentsid as id\r\n                            FROM\r\n                                vtiger_seattachmentsrel\r\n                            INNER JOIN vtiger_attachments ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid\r\n                            LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_seattachmentsrel.crmid\r\n                            WHERE vtiger_products.product_no = ?";
                    $existingImages = $adb->pquery($existingImageSql, array($product_id));
                    $numOfRows = $adb->num_rows($existingImages);
                    if (0 < $numOfRows) {
                        $imageName = $adb->query_result($existingImages, 0, "id") . "_" . $adb->query_result($existingImages, 0, "name");
                        $imagePath = $adb->query_result($existingImages, 0, "path");
                        if ($imagePath && $imageName) {
                            $img->setAttribute("src", $site_URL . "/" . $imagePath . $imageName);
                        }
                    } else {
                        $img->setAttribute("src", "");
                    }
                } else {
                    $img->setAttribute("src", "");
                }
            } else {
                if ($img_class == "quoting_tool-widget-signature-image" || $img_class == "quoting_tool-widget-secondary_signature-image") {
                    $img->setAttribute("data-image-index", "signatureImageIndex" . $signatureImageIndex);
                    $signatureImageIndex++;
                    if ($img_class == "quoting_tool-widget-secondary_signature-image") {
                        $img->setAttribute("style", "height: 40px; width: 130px; display: none;");
                    }
                }
            }
        }
        $full_content = $tmp_html->save();
        preg_match_all("'\\[BARCODE\\|(.*?)\\|BARCODE\\]'si", $full_content, $match);
        if (0 < count($match)) {
            require_once "modules/QuotingTool/resources/barcode/autoload.php";
            $full_content = preg_replace_callback("/\\[BARCODE\\|(.+?)\\|BARCODE\\]/", function ($barcode_val) {
                $array_values = explode("=", $barcode_val[1]);
                list($method, $field_value) = $array_values;
                $qt = new QuotingTool();
                $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
                $barcode_png = "<img src=\"data:image/png;base64," . base64_encode($generator->getBarcode($field_value, $qt->barcode_type_code[$method])) . "\" />";
                return $barcode_png;
            }, $full_content);
        }
        $full_content = base64_encode($full_content);
        $transactionId = $transactionRecordModel->saveTransaction(0, $templateId, $record->get("module"), $recordId, NULL, NULL, $full_content, $record->get("description"));
        $transactionRecord = $transactionRecordModel->findById($transactionId);
        $hash = $transactionRecord->get("hash");
        $hash = $hash ? $hash : "";
        $keys_values = array();
        $site = rtrim($site_URL, "/");
        if ($isCreateNewRecord == 1) {
            $link = (string) $site . "/modules/" . $moduleName . "/proposal/index.php?record=" . $transactionId . "&session=" . $hash . "&iscreatenewrecord=true&childmodule=" . $childModule . "&preview=true";
        } else {
            $link = (string) $site . "/modules/" . $moduleName . "/proposal/index.php?record=" . $transactionId . "&session=" . $hash . "&preview=true";
        }
        $compactLink = preg_replace("(^(https?|ftp)://)", "", $link);
        $companyModel = Settings_Vtiger_CompanyDetails_Model::getInstance();
        $companyfields = array();
        foreach ($companyModel->getFields() as $key => $val) {
            if ($key == "logo") {
                continue;
            }
            $companyfields["\$" . "Vtiger_Company_" . $key . "\$"] = $companyModel->get($key);
        }
        foreach ($varContent as $var) {
            if ($var == "\$custom_proposal_link\$") {
                $keys_values["\$custom_proposal_link\$"] = $compactLink;
            } else {
                if ($var == "\$custom_user_signature\$") {
                    $keys_values["\$custom_user_signature\$"] = preg_replace("/\\v+|\\\\r\\\\n/", "<br/>", $current_user->signature);
                    $keys_values["\$custom_user_signature\$"] = preg_replace("/\\v+|\\\\n/", "<br/>", $keys_values["\$custom_user_signature\$"]);
                    $keys_values["\$custom_user_signature\$"] = preg_replace("/\\v+|\\\\r/", "<br/>", $keys_values["\$custom_user_signature\$"]);
                }
            }
            if (array_key_exists($var, $companyfields)) {
                $keys_values[$var] = $companyfields[$var];
            }
        }
        if (!empty($keys_values)) {
            $full_content = base64_decode($full_content);
            $record->set("content", $quotingTool->mergeCustomTokens($full_content, $keys_values));
            $full_content = base64_encode($record->get("content"));
            $transactionId = $transactionRecordModel->saveTransaction($transactionId, $templateId, $relModule, $recordId, NULL, NULL, $full_content, $record->get("description"));
        }
        $varEmailSubject = $quotingTool->getVarFromString($record->get("email_subject"));
        if (!empty($varEmailSubject)) {
            $keys_values = array();
            $companyModel = Settings_Vtiger_CompanyDetails_Model::getInstance();
            $companyfields = array();
            foreach ($companyModel->getFields() as $key => $val) {
                if ($key == "logo") {
                    continue;
                }
                $companyfields["\$" . "Vtiger_Company_" . $key . "\$"] = $companyModel->get($key);
            }
            foreach ($varEmailSubject as $var) {
                if ($var == "\$custom_proposal_link\$") {
                    $keys_values["\$custom_proposal_link\$"] = $compactLink;
                } else {
                    if ($var == "\$custom_user_signature\$") {
                        $keys_values["\$custom_user_signature\$"] = preg_replace("/\\v+|\\\\r\\\\n/", "<br/>", $current_user->signature);
                        $keys_values["\$custom_user_signature\$"] = preg_replace("/\\v+|\\\\n/", "<br/>", $keys_values["\$custom_user_signature\$"]);
                        $keys_values["\$custom_user_signature\$"] = preg_replace("/\\v+|\\\\r/", "<br/>", $keys_values["\$custom_user_signature\$"]);
                    }
                }
                if (array_key_exists($var, $companyfields)) {
                    $keys_values[$var] = $companyfields[$var];
                }
            }
            if (!empty($keys_values)) {
                $record->set("email_subject", $quotingTool->mergeCustomTokens($record->get("email_subject"), $keys_values));
            }
        }
        $varEmailContent = $quotingTool->getVarFromString($record->get("email_content"));
        if (!empty($varEmailContent)) {
            $keys_values = array();
            $companyModel = Settings_Vtiger_CompanyDetails_Model::getInstance();
            $companyfields = array();
            foreach ($companyModel->getFields() as $key => $val) {
                if ($key == "logo") {
                    continue;
                }
                $companyfields["\$" . "Vtiger_Company_" . $key . "\$"] = $companyModel->get($key);
            }
            foreach ($varEmailContent as $var) {
                if ($var == "\$custom_proposal_link\$") {
                } else {
                    if ($var == "\$custom_user_signature\$") {
                        $keys_values["\$custom_user_signature\$"] = preg_replace("/\\v+|\\\\r\\\\n/", "<br/>", $current_user->signature);
                        $keys_values["\$custom_user_signature\$"] = preg_replace("/\\v+|\\\\n/", "<br/>", $keys_values["\$custom_user_signature\$"]);
                        $keys_values["\$custom_user_signature\$"] = preg_replace("/\\v+|\\\\r/", "<br/>", $keys_values["\$custom_user_signature\$"]);
                    }
                }
                if (array_key_exists($var, $companyfields)) {
                    $keys_values[$var] = $companyfields[$var];
                }
            }
            if (!empty($keys_values)) {
                $record->set("email_content", $quotingTool->mergeCustomTokens($record->get("email_content"), $keys_values));
            }
        }
        $full_content = base64_decode($full_content);
        $tmp_html1 = str_get_html($full_content);
        foreach ($tmp_html1->find(".widget__bound") as $container) {
            $container->outertext = "";
        }
        foreach ($tmp_html1->find(".quoting_tool-widget-signature-container") as $container) {
            $container->outertext = "";
        }
        foreach ($tmp_html1->find(".quoting_tool-widget-secondary_signature-container") as $container) {
            $container->outertext = "";
        }
        foreach ($tmp_html1->find("table") as $table) {
            $tableType = $table->getAttribute("data-table-type");
            $igoreTable = array("pricing_table", "create_related_record", "pricing_table_idc");
            $tableConfig = $table->find(".show-config");
            if (in_array($tableType, $igoreTable) || 0 < count($tableConfig)) {
                $parentTable = $table->parent->parent->parent;
                $parentTable->outertext = "";
            }
        }
        $full_content = $tmp_html1->save();
        $objSettings = $quotingToolSettingRecordModel->findByTemplateId($templateId);
        $ignoreBorderEmail = $objSettings->get("ignore_border_email");
        if (!$ignoreBorderEmail) {
            $full_content = "<div style=\"height: auto; margin: 0 auto;width: 680.321px; padding: 16mm 15mm;border: 1px solid #c3c3c3;box-shadow: 0 0 8px rgba(0, 0, 0, 0.07), 0 0 0 1px rgba(0, 0, 0, 0.06); \">" . $full_content . "</div>";
        }
        $record->set("email_content", $record->get("email_content") . $full_content);
        $multiRecord = $request->get("multiRecord");
        $email_field_list = $quotingTool->getEmailList($relModule, $recordId, $isCreateNewRecord, $multiRecord);
        $viewer->assign("MODULE", $moduleName);
        $viewer->assign("RECORDID", $recordId);
        $viewer->assign("TEMPLATEID", $templateId);
        $viewer->assign("EMAIL_FIELD_LIST", $email_field_list);
        $viewer->assign("EMAIL_SUBJECT", $record->get("email_subject"));
        $viewer->assign("EMAIL_CONTENT", $record->get("email_content"));
        $viewer->assign("CUSTOM_PROPOSAL_LINK", $link);
        $viewer->assign("TRANSACTION_ID", $transactionId);
        $viewer->assign("MULTI_RECORD", $multiRecord);
        $viewer->assign("HAS_SECONDARY_SIGNATURE", $hasSignature);
        $viewer->assign("MAX_UPLOAD_SIZE", vglobal("upload_maxsize"));
        $viewer->assign("EMAIL_MODE", "edit");
        $documentsModel = Vtiger_Module_Model::getInstance("Documents");
        $documentsURL = $documentsModel->getInternalDocumentsURL();
        $viewer->assign("DOCUMENTS_URL", $documentsURL);
        echo $viewer->view("EmailPreviewTemplate.tpl", $moduleName, true);
    }
}

?>
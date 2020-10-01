<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

require_once "include/utils/utils.php";
include "modules/QuotingTool/QuotingTool.php";
include "test/QuotingTool/resources/mpdf.php";
include_once "include/simplehtmldom/simple_html_dom.php";

class PandaDoc_MassSaveAjax_Action extends Vtiger_Mass_Action {
    
    function __construct() {
        parent::__construct();
        $this->exposeMethod('SendEmailFromList');
        $this->exposeMethod('getEmailContent');
    }
    
    function checkPermission(Vtiger_Request $request) {
        return true;
    }
    
    
    function process(Vtiger_Request $request){
        
        $mode = $request->get('mode');
        if(!empty($mode) && $mode == 'getEmailContent') {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
        
        global $adb,$site_URL;
        
        global $current_user;
        
        $moduleName = $request->getModule();
        
        if($mode == 'showSendEmailFromRelated')
            $recordIds = Vtiger_RelatedMass_Action::getRecordsListFromRequest($request);
        if($mode == 'showSendEmailForm')
            $recordIds = $request->get('selected_ids');
        if($mode == 'showSendEmailFormList')
            $recordIds = $this->getRecordsListFromRequest($request);
        
        $emailFieldList = $request->get('fields');
        
        $template = $request->get('templateid');
        
        $srcModule = $request->get('source_module');
        
        $content = $request->get('envelope_content');
        
        $pandadoc_settings_result = $adb->pquery("SELECT * FROM vtiger_pandadoc_configuration WHERE
        vtiger_pandadoc_configuration.userid = ? and ( access_token is not NULL and access_token != '' )",array($current_user->id));
        
        if($adb->num_rows($pandadoc_settings_result)){
            
            $token_data = $adb->query_result_rowdata($pandadoc_settings_result, 0);
            
            $token = array();
            
            $token['token_type'] = $token_data['token_type'];
            
            $token['expires_in'] = $token_data['expires_in'];
            
            $token['access_token'] = $token_data['access_token'];
            
            $token['refresh_token'] = $token_data['refresh_token'];
            
            try {
                
                $headers = array(
                    "Authorization: Bearer ".$token['access_token'],
                );
                
                $url = "https://api.pandadoc.com/public/v1/documents";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                
                $response = curl_exec($curl);
                $response = json_decode($response, true);
                
                $docId = $response['results'][0]['id'];
                
            } catch(Exception $e){
                
                $docId = '';
                
            }
            
            $current_time = strtotime(date("Y-m-d H:i:s"));
            
            if(!$docId || $token['expires_in'] < $current_time){
                
                try {
                    
                    $client_id = PandaDoc_Config_Connector::$clientId;
                    $client_secret = PandaDoc_Config_Connector::$clientSecret;
                    
                    
                    $token_request_data = array(
                        "grant_type" => "refresh_token",
                        "refresh_token" => $token['refresh_token'],
                        "client_id" => $client_id,
                        "client_secret" => $client_secret,
                        "scope" => "read write"
                    );
                    
                    $token_request_body = http_build_query($token_request_data);
                    $curl = curl_init('https://api.pandadoc.com/oauth2/access_token/');
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $token_request_body);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                    
                    $response = curl_exec($curl);
                    
                    $response = json_decode($response, true);
                    
                    $token['access_token'] = $response['access_token'];
                    $token['refresh_token'] = $response['refresh_token'];
                    $token['token_type'] = $response['token_type'];
                    $token['expires_in'] = $response['expires_in'];
                    
                    $this->saveToken($token);
                    
                } catch(Exception $e){
                    
                    $result = array('message' => 'Invalid Token, Please Connect Application Again!');
                    
                    $response = new Vtiger_Response();
                    
                    $response->setError($result['message']);
                    
                    $response->emit();
                    
                    exit;
                    
                }
                
            }
                    
        } else {
            
            $result = array('message' => 'Invalid Token, Please Connect Application Again!');
            
            $response = new Vtiger_Response();
            
            $response->setError($result['message']);
            
            $response->emit();
            
            exit;
            
        }
            
        foreach($recordIds as $recordId) {
            
            $content = str_replace("single_signature", "{signature*:".$recordId."_______}", $content);
            
            $templateId = $request->get('templateid');
            $qutRecordModel = new QuotingTool_Record_Model();
            $quotingToolSettingRecordModel = new QuotingTool_SettingRecord_Model();
            $record = $qutRecordModel->getById($templateId);
            $fileName = $record->get('file_name');
            
            $quotingTool = new QuotingTool();
            $varHeader = $quotingTool->getVarFromString(base64_decode($record->get("header")));
            $varFooter = $quotingTool->getVarFromString(base64_decode($record->get("footer")));
            $customFunction = json_decode(html_entity_decode($record->get("custom_function")));
            $record = $record->decompileRecord($parentRecord, array("header", "content", "footer"), array(), $customFunction);
            
            foreach ($varHeader as $var) {
                if ($var == "\$custom_proposal_link\$") {
                    $keys_values["\$custom_proposal_link\$"] = $compactLink;
                } else {
                    if ($var == "\$custom_user_signature\$") {
                        $keys_values["\$custom_user_signature\$"] = nl2br($current_user->signature);
                    }
                }
                if (array_key_exists($var, $companyfields)) {
                    $keys_values[$var] = $companyfields[$var];
                }
            }
            if (!empty($keys_values)) {
                $record->set("header", $quotingTool->mergeCustomTokens($record->get("header"), $keys_values));
            }
            foreach ($varFooter as $var) {
                if ($var == "\$custom_proposal_link\$") {
                    $keys_values["\$custom_proposal_link\$"] = $compactLink;
                } else {
                    if ($var == "\$custom_user_signature\$") {
                        $keys_values["\$custom_user_signature\$"] = nl2br($current_user->signature);
                    }
                }
                if (array_key_exists($var, $companyfields)) {
                    $keys_values[$var] = $companyfields[$var];
                }
            }
            
            if (!empty($keys_values)) {
                $record->set("footer", $quotingTool->mergeCustomTokens($record->get("footer"), $keys_values));
            }
            
            $pdf = $quotingTool->createPdf($content, $record->get("header"), $record->get("footer"), $fileName, $record->get("settings_layout"), $parentRecord, "storage/QuotingTool/", array(), array(), false);
            
            
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId);
            
            $toEmail ='';
            $fieldValue = $recordModel->get($emailFieldList);
            if(!empty($fieldValue)) {
                $toEmail = $fieldValue;
            }
            
            if(empty($toEmail)){
                $response = new Vtiger_Response();
                $response->setError('NO Valid Email Found');
                $response->emit();
                exit;
            }
            
            try {
                
                $postfields = array();
                $postfields['name'] = $fileName;
                $postfields['url'] = rtrim($site_URL).'/'.$pdf;
                $postfields['recipients'] = array(
                    array(
                        'email'      => $toEmail,
                        'first_name' => $recordModel->getName(),
                        "role" => $recordId
                    )
                );
               
                $postfields['parse_form_fields'] = false;
                $data_string = json_encode( $postfields );
                $docHeaders = array( "Authorization: Bearer ". $token['access_token'],
                    "content-type: application/json",'Content-length: '.strlen( $data_string ) );
                
                $url = "https://api.pandadoc.com/public/v1/documents/";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $docHeaders);
                curl_setopt($curl, CURLINFO_HEADER_OUT, TRUE);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
                $response = curl_exec($curl);
                
                $response = json_decode($response, true);
                
                if($response['id']){
                    $envelopeId = $response['id'];
                    if($request->get('parent_record')){
                        $adb->pquery("INSERT INTO vtiger_sync_pandadoc_records(userid, documentid, contactid) VALUES (?, ?, ?)",
                            array($current_user->id, $envelopeId, $request->get('parent_record')));
                    }else{
                        $adb->pquery("INSERT INTO vtiger_sync_pandadoc_records(userid, documentid, contactid) VALUES (?, ?, ?)",
                            array($current_user->id, $envelopeId, $recordId));
                    }
                    $result = array('success' => true);
                }else{
                    $result = array('message' => 'Unable to send the email try again later.');
                }
                
                
            } catch(Exception $e){
                $result = array('message' => $e->getMessage());
            }
            
        }
            
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
            
    }
    
    public function saveToken($token_data){
        
        global $adb, $current_user;
        
        $access_token = $token_data['access_token'];
        $refresh_token = $token_data['refresh_token'];
        $token_type = $token_data['token_type'];
        $expires_in = $token_data['expires_in'];
        
        $current_user_id = $current_user->id;
        
        $tQuery = $adb->pquery("SELECT * FROM vtiger_pandadoc_configuration WHERE vtiger_pandadoc_configuration.userid =?",
            array($current_user_id));
        
        if($adb->num_rows($tQuery)){
            
            $adb->pquery("UPDATE vtiger_pandadoc_configuration SET access_token = ?, refresh_token = ?, token_type = ?,
                    expires_in = ? WHERE userid = ?", array($access_token, $refresh_token, $token_type,
                        $expires_in, $current_user_id));
            
        }else{
            
            $adb->pquery("INSERT INTO vtiger_pandadoc_configuration(userid, access_token, refresh_token, token_type, expires_in)
                    VALUES (?, ?, ?, ?, ?)",array($current_user_id, $access_token, $refresh_token, $token_type, $expires_in));
            
        }
        
        
    }
    
    function getEmailContent(Vtiger_Request $request){
        
        $moduleName = $request->getModule();
        $templateId = $request->get('templateid');
        //$recordIds = $this->getRecordsListFromRequest($request);
        $recordId = $request->get('parent_record');
        
        $recordModel = new QuotingTool_Record_Model();
        $quotingToolSettingRecordModel = new QuotingTool_SettingRecord_Model();
        $record = $recordModel->getById($templateId);
        $relModule = $request->get("parent_module");
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
        $transactionId = $transactionRecordModel->saveTransaction(0, $templateId, $relModule, $recordId, NULL, NULL, $full_content, $record->get("description"));
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
        
        $response = new Vtiger_Response();
        $response->setResult($full_content);
        $response->emit();
    }
    
    
}

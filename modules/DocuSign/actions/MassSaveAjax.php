<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

require_once 'modules/DocuSign/vendor/autoload.php';
require_once "include/utils/utils.php";
include "modules/QuotingTool/QuotingTool.php";
include "test/QuotingTool/resources/mpdf.php";

class DocuSign_MassSaveAjax_Action extends Vtiger_Mass_Action {
    
    function __construct() {
        parent::__construct();
        $this->exposeMethod('SendEmailFromList');
    }
    
    function checkPermission(Vtiger_Request $request) {
        return true;
    }
    
    public function process(Vtiger_Request $request) {
       
        $mode = $request->get('mode');
        if(!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
        
        global $adb,$site_URL;
        
        global $current_user;
        
        $moduleName = $request->getModule();
        
        $recordId = $request->get('record');
        
        $emailFieldList = $request->get('fields');
        
        $template = $request->get('templateid');
        
        $srcModule = $request->get('source_module');
        
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
        
        $config = new \DocuSign\eSign\Configuration();
        
        if(DocuSign_Config_Connector::$server == 'Sandbox'){
            $config->setHost('https://demo.docusign.net/restapi');
            $OAuth = new \DocuSign\eSign\client\Auth\OAuth();
            $OAuth->setBasePath($config->getHost());
            $api_client = new \DocuSign\eSign\client\ApiClient($config,$OAuth);
        }
        if(DocuSign_Config_Connector::$server == 'Production')
            $api_client = new \DocuSign\eSign\client\ApiClient($config);
            
        $docuSign_settings_result = $adb->pquery("SELECT * FROM vtiger_document_designer_configuration WHERE
        vtiger_document_designer_configuration.userid = ? and ( access_token is not NULL and access_token != '' )",array($current_user->id));
        
        if($adb->num_rows($docuSign_settings_result)){
            
            $token_data = $adb->query_result_rowdata($docuSign_settings_result, 0);
            
            $token = array();
            
            $token['token_type'] = $token_data['token_type'];
            
            $token['expires_in'] = $token_data['expires_in'];
            
            $token['access_token'] = $token_data['access_token'];
            
            $token['refresh_token'] = $token_data['refresh_token'];
            try {
                $userDetail = $api_client->getUserInfo($token['access_token']);
                
                $accountId = $userDetail[0]['accounts'][0]['account_id'];
            } catch(Exception $e){
                $accountId = '';
            }
            
            
            $current_time = strtotime(date("Y-m-d H:i:s"));
            
            if(!$accountId || $token['expires_in'] < $current_time){
               
                try {
                    
                    $refreshTokenData = $api_client->generateRefreshAccessToken(DocuSign_Config_Connector::$client_id, DocuSign_Config_Connector::$client_secret, $token['refresh_token']);
                    
                    $token['access_token'] = $refreshTokenData[0]['access_token'];
                    $token['refresh_token'] = $refreshTokenData[0]['refresh_token'];
                    $token['token_type'] = $refreshTokenData[0]['token_type'];
                    $token['expires_in'] = $refreshTokenData[0]['expires_in'];
                    
                    $this->saveToken($refreshTokenData);
                    
                } catch(Exception $e){
                    
                    $result = array('message' => 'Invalid Token, Please Connect Application Again!');
                    
                    $response = new Vtiger_Response();
                    
                    $response->setError($result['message']);
                    
                    $response->emit();
                    
                    exit;
                    
                }
                
            }
            
            $config->addDefaultHeader('Authorization', 'Bearer ' . $token['access_token']);
            
            if(DocuSign_Config_Connector::$server == 'Sandbox')
                $api_client = new \DocuSign\eSign\client\ApiClient($config, $OAuth);
            if(DocuSign_Config_Connector::$server == 'Production')
                $api_client = new \DocuSign\eSign\client\ApiClient($config);
            
        } else {
            
            $result = array('message' => 'Invalid Token, Please Connect Application Again!');
            
            $response = new Vtiger_Response();
            
            $response->setError($result['message']);
            
            $response->emit();
            
            exit;
            
        }
        
        try {
            
            $fileData = $this->pdfFileContent($recordId, $template, $srcModule);
            
            $base64FileContent = $fileData['filecontent'];
            $fileName = $fileData['filename'];
            
            $document = new DocuSign\eSign\Model\Document([
                'document_base64' => $base64FileContent,
                'name' => $fileName,
                'file_extension' => 'pdf',
                'document_id' => '1' ,
            ]);
            
            
            $signer = new DocuSign\eSign\Model\Signer([
                'email' => $toEmail, 'name' => $recordModel->getName(), 'recipient_id' => "1", 'routing_order' => "1"
            ]);
            
            $signHere = new \DocuSign\eSign\Model\SignHere([
                'anchor_string' => '#SIGN_HERE#', 'anchor_units' => 'pixels',
                'anchor_y_offset' => '10', 'anchor_x_offset' => '20'
            ]);
            
            $signer->setTabs(new DocuSign\eSign\Model\Tabs(['sign_here_tabs' => [$signHere]]));
            
            $envelopeDefinition = new DocuSign\eSign\Model\EnvelopeDefinition([
                'email_subject' => "Please sign this document",
                'documents' => [$document],
                'recipients' => new DocuSign\eSign\Model\Recipients(['signers' => [$signer]]),
                'status' => "sent"
            ]);
            
            $envelopeApi = new DocuSign\eSign\Api\EnvelopesApi($api_client);
            $results = $envelopeApi->createEnvelope($accountId, $envelopeDefinition);
            if($results['status'] == 'sent'){
                $envelopeId = $results['envelope_id'];
                
                $adb->pquery("INSERT INTO vtiger_sync_docusign_records(userid, envelopeid, contactid) VALUES (?, ?, ?)",
                    array($current_user->id, $envelopeId, $recordId));
                
                $result = array('success' => true);
            }else{
                $result = array('message' => 'Unable to send the email try again later.');
            }
            
            
        } catch(Exception $e){
            $result = array('message' => $e->getMessage());
        }
        
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
    
    function SendEmailFromList(Vtiger_Request $request){
        
        global $adb,$site_URL;
        
        global $current_user;
        
        $moduleName = $request->getModule();
        
        $recordIds = $this->getRecordsListFromRequest($request);
        
        $emailFieldList = $request->get('fields');
        
        $template = $request->get('templateid');
        
        $srcModule = $request->get('source_module');
       
        $config = new \DocuSign\eSign\Configuration();
        
        if(DocuSign_Config_Connector::$server == 'Sandbox'){
            $config->setHost('https://demo.docusign.net/restapi');
            $OAuth = new \DocuSign\eSign\client\Auth\OAuth();
            $OAuth->setBasePath($config->getHost());
            $api_client = new \DocuSign\eSign\client\ApiClient($config,$OAuth);
        }
        if(DocuSign_Config_Connector::$server == 'Production')
            $api_client = new \DocuSign\eSign\client\ApiClient($config);
            
        $docuSign_settings_result = $adb->pquery("SELECT * FROM vtiger_document_designer_configuration WHERE
        vtiger_document_designer_configuration.userid = ? and ( access_token is not NULL and access_token != '' )",array($current_user->id));
        
        if($adb->num_rows($docuSign_settings_result)){
            
            $token_data = $adb->query_result_rowdata($docuSign_settings_result, 0);
            
            $token = array();
            
            $token['token_type'] = $token_data['token_type'];
            
            $token['expires_in'] = $token_data['expires_in'];
            
            $token['access_token'] = $token_data['access_token'];
            
            $token['refresh_token'] = $token_data['refresh_token'];
            
            try {
                
                $userDetail = $api_client->getUserInfo($token['access_token']);
                
                $accountId = $userDetail[0]['accounts'][0]['account_id'];
                
            } catch(Exception $e){
                
                $accountId = '';
                
            }
            
            $current_time = strtotime(date("Y-m-d H:i:s"));
            
            if(!$accountId || $token['expires_in'] < $current_time){
                
                try {
                    
                    $refreshTokenData = $api_client->generateRefreshAccessToken(DocuSign_Config_Connector::$client_id, DocuSign_Config_Connector::$client_secret, $token['refresh_token']);
                    
                    $token['access_token'] = $refreshTokenData[0]['access_token'];
                    $token['refresh_token'] = $refreshTokenData[0]['refresh_token'];
                    $token['token_type'] = $refreshTokenData[0]['token_type'];
                    $token['expires_in'] = $refreshTokenData[0]['expires_in'];
                    
                    $this->saveToken($refreshTokenData);
                    
                } catch(Exception $e){
                    
                    $result = array('message' => 'Invalid Token, Please Connect Application Again!');
                    
                    $response = new Vtiger_Response();
                    
                    $response->setError($result['message']);
                    
                    $response->emit();
                    
                    exit;
                    
                }
                
            }
            
            $config->addDefaultHeader('Authorization', 'Bearer ' . $token['access_token']);
            
            if(DocuSign_Config_Connector::$server == 'Sandbox')
                $api_client = new \DocuSign\eSign\client\ApiClient($config, $OAuth);
            if(DocuSign_Config_Connector::$server == 'Production')
                $api_client = new \DocuSign\eSign\client\ApiClient($config);
                    
        } else {
            
            $result = array('message' => 'Invalid Token, Please Connect Application Again!');
            
            $response = new Vtiger_Response();
            
            $response->setError($result['message']);
            
            $response->emit();
            
            exit;
            
        }
            
        foreach($recordIds as $recordId) {
            
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
                
                $fileData = $this->pdfFileContent($recordId, $template, $srcModule);
               
                $base64FileContent = $fileData['filecontent'];
                $fileName = $fileData['filename'];
                
                $document = new DocuSign\eSign\Model\Document([
                    'document_base64' => $base64FileContent,
                    'name' => $fileName,
                    'file_extension' => 'pdf',
                    'document_id' => '1' ,
                ]);
                
                
                $signer = new DocuSign\eSign\Model\Signer([
                    'email' => $toEmail, 'name' => $recordModel->getName(), 'recipient_id' => "1", 'routing_order' => "1"
                ]);
                
                $signHere = new \DocuSign\eSign\Model\SignHere([
                    'anchor_string' => '#SIGN_HERE#', 'anchor_units' => 'pixels',
                    'anchor_y_offset' => '10', 'anchor_x_offset' => '20'
                ]);
                
                $signer->setTabs(new DocuSign\eSign\Model\Tabs(['sign_here_tabs' => [$signHere]]));
                
                $envelopeDefinition = new DocuSign\eSign\Model\EnvelopeDefinition([
                    'email_subject' => "Please sign this document",
                    'documents' => [$document],
                    'recipients' => new DocuSign\eSign\Model\Recipients(['signers' => [$signer]]),
                    'status' => "sent"
                ]);
                
                $envelopeApi = new DocuSign\eSign\Api\EnvelopesApi($api_client);
                $results = $envelopeApi->createEnvelope($accountId, $envelopeDefinition);
                if($results['status'] == 'sent'){
                    $envelopeId = $results['envelope_id'];
                    
                    $adb->pquery("INSERT INTO vtiger_sync_docusign_records(userid, envelopeid, contactid) VALUES (?, ?, ?)",
                        array($current_user->id, $envelopeId, $recordId));
                    
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
        
        $access_token = $token_data[0]['access_token'];
        $refresh_token = $token_data[0]['refresh_token'];
        $token_type = $token_data[0]['token_type'];
        $expires_in = $token_data[0]['expires_in'];
        
        $current_user_id = $current_user->id;
        
        $result = $adb->pquery('SELECT * FROM vtiger_document_designer_configuration WHERE userid = ?',array($current_user_id));
        
        if($adb->num_rows($result)){
            
            $adb->pquery("update vtiger_document_designer_configuration set access_token = ?,
			refresh_token = ?, token_type = ?, expires_in = ? where userid = ?",
                array($access_token, $refresh_token, $token_type, $expires_in, $current_user_id));
            
        } else{
            
            $adb->pquery("insert into vtiger_document_designer_configuration(userid, access_token,
			refresh_token, token_type, expires_in) values(?,?,?,?,?)",
                array($current_user_id, $access_token, $refresh_token, $token_type, $expires_in));
            
        }
        
    }
    
    public function pdfFileContent($recordId, $template, $srcModule){
        
        global $site_URL;
        global $current_user;
        global $adb;
        $moduleName = 'QuotingTool';
        $entityId = $recordId;
        
        $templateId = $template;
       
        $recordModel = new QuotingTool_Record_Model();
        $record = $recordModel->getById($templateId);
        
        if (!$record) {
            echo vtranslate("LBL_NOT_FOUND", $moduleName);
            exit;
        }
        
        $quotingTool = new QuotingTool();
        $module = $srcModule;
        $varContent = $quotingTool->getVarFromString(base64_decode($record->get("content")));
        $varHeader = $quotingTool->getVarFromString(base64_decode($record->get("header")));
        $varFooter = $quotingTool->getVarFromString(base64_decode($record->get("footer")));
        $customFunction = json_decode(html_entity_decode($record->get("custom_function")));
        $record = $record->decompileRecord($entityId, array("header", "content", "footer"), array(), $customFunction);
        $tabId = Vtiger_Functions::getModuleId($module);
        
        $recordId = $entityId;
        if ($record->get("file_name")) {
            global $adb;
            $fileName = $record->get("file_name");
            if (strpos("\$record_no\$", $record->get("file_name")) != -1) {
                $rs = $adb->pquery("select fieldname from vtiger_field where tabid=" . $tabId . " and uitype=4");
                $nameFieldModuleNo = $adb->query_result($rs, 0, "fieldname");
                $recordResult = Vtiger_Record_Model::getInstanceById($recordId);
                $resultNo = $recordResult->get($nameFieldModuleNo);
                $fileName = str_replace("\$record_no\$", $resultNo, $fileName);
            }
            if (strpos("\$record_name\$", $record->get("file_name")) != -1) {
                $resultName = Vtiger_Util_Helper::getRecordName($recordId);
                $fileName = str_replace("\$record_name\$", $resultName, $fileName);
            }
            if (strpos("\$template_name\$", $record->get("file_name")) != -1) {
                $fileName = str_replace("\$template_name\$", $record->get("filename"), $fileName);
            }
            $dateTimeByUserCreate = DateTimeField::convertToUserTimeZone(date("Y-m-d H:i:s"));
            $dateTimeByUserFormatCreate = DateTimeField::convertToUserFormat($dateTimeByUserCreate->format("Y-m-d H:i:s"));
            list($date, $time) = explode(" ", $dateTimeByUserFormatCreate);
            $day = date("d", time($date));
            $month = date("m", time($date));
            $year = date("Y", time($date));
            if (strpos("\$day\$", $record->get("file_name")) != -1) {
                $fileName = str_replace("\$day\$", $day, $fileName);
            }
            if (strpos("\$month\$", $record->get("file_name")) != -1) {
                $fileName = str_replace("\$month\$", $month, $fileName);
            }
            if (strpos("\$year\$", $record->get("file_name")) != -1) {
                $fileName = str_replace("\$year\$", $year, $fileName);
            }
        } else {
            $fileName = $record->get("filename");
        }
        
        $fileName = $quotingTool->makeUniqueFile($fileName);
        $transactionRecordModel = new QuotingTool_TransactionRecord_Model();
        $full_content = base64_encode($record->get("content"));
        $transactionId = $transactionRecordModel->saveTransaction(0, $templateId, $module, $entityId, NULL, NULL, $full_content, $record->get("description"));
        $transactionRecord = $transactionRecordModel->findById($transactionId);
        $hash = $transactionRecord->get("hash");
        $hash = $hash ? $hash : "";
        $keys_values = array();
        $site = rtrim($site_URL, "/");
        $link = (string) $site . "/modules/" . $moduleName . "/proposal/index.php?record=" . $transactionId . "&session=" . $hash;
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
                    $keys_values["\$custom_user_signature\$"] = nl2br($current_user->signature);
                }
            }
            if (array_key_exists($var, $companyfields)) {
                $keys_values[$var] = $companyfields[$var];
            }
        }
        $full_content = $record->get("content");
        $tmp_html = str_get_html($full_content);
        foreach ($tmp_html->find("img") as $img) {
            $json_data_info = $img->getAttribute("data-info");
            $data_info = json_decode(html_entity_decode($json_data_info));
            if ($data_info) {
                $field_id = $data_info->settings_field_image_fields;
                if (0 < $field_id) {
                    $field_model = Vtiger_Field_Model::getInstance($field_id);
                    $field_name = $field_model->getName();
                    $related_record_model = Vtiger_Record_Model::getInstanceById($entityId);
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
        $record->set("content", $full_content);
        if (!empty($keys_values)) {
            $record->set("content", $quotingTool->mergeCustomTokens($record->get("content"), $keys_values));
            $full_content = base64_encode($record->get("content"));
            $transactionId = $transactionRecordModel->saveTransaction($transactionId, $templateId, $module, $entityId, NULL, NULL, $full_content, $record->get("description"));
        }
        $transactionId = $transactionRecordModel->saveTransaction($transactionId, $templateId, $module, $entityId, NULL, NULL, $full_content, $record->get("description"));
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
        $content = $record->get("content");
        $html = str_get_html($content);
        if (!$html) {
            return $content;
        }
        foreach ($html->find("table") as $table) {
            $table->removeAttribute("data-info");
        }
        $content = $html->save();
        $pdf = $quotingTool->createPdf($content, $record->get("header"), $record->get("footer"), $fileName, $record->get("settings_layout"), $entityId);
        $fileContent = "";
        if (is_readable($pdf)) {
            $fileContent = base64_encode(file_get_contents($pdf));
        }
        $pattern = "/\t|\n|\\`|\\~|\\!|\\@|\\#|\\%|\\^|\\&|\\*|\\(|\\)|\\+|\\-|\\=|\\[|\\{|\\]|\\}|\\||\\|\\'|\\<|\\,|\\.|\\>|\\?|\\/|\"|'|\\;|\\:/";
        $name = str_replace(".pdf", "", $fileName);
        $name = preg_replace($pattern, "_", html_entity_decode($name, ENT_QUOTES));
        $name = str_replace(" ", "_", $name);
        $fileName = str_replace("\$", "_", $name);
        $fileName = trim($fileName);
        $data = array('filecontent'=>$fileContent, 'filename'=>$fileName);
        
        return $data;
       
    }
}
<?php
include_once "modules/QuotingTool/QuotingTool.php";
include_once "modules/DocuSign/vendor/autoload.php";
include_once "include/simplehtmldom/simple_html_dom.php";
class DocuSign_SendEmailToRelated_Action extends Vtiger_RelatedMass_Action {
    
    function __construct() {
        parent::__construct();
        $this->exposeMethod('SendEmail');
        $this->exposeMethod('getEmailContent');
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
        
    }
    
    function SendEmail(Vtiger_Request $request){
        
        global $adb,$current_user;
        
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($request->get('source_module'));
        $recordIds = $this->getRecordsListFromRequest($request);
        $emailFieldList = $request->get('fields');
        $parentRecord = $request->get('parent_record');
        
        $toEmail = array();
        $content = $request->get('envelope_content');
        
        foreach($recordIds as $recordId) {
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId);
            $fieldValue = $recordModel->get($emailFieldList);
            if(!empty($fieldValue)) {
                $toEmail[$recordId] = $fieldValue;
            }
            
            $fullName= '';
            $COUNTER = 0;
            foreach ($moduleModel->getNameFields() as $NAME_FIELD){
                $FIELD_MODEL = $moduleModel->getField($NAME_FIELD);
                if($FIELD_MODEL->getPermissions()){
                    if($recordModel->getDisplayValue('salutationtype') && $FIELD_MODEL->getName() == 'firstname'){
                        $fullName .= $recordModel->getDisplayValue('salutationtype');
                    }
                    $fullName .= trim($recordModel->get($NAME_FIELD));
                    if($COUNTER == 0 && ($recordModel->get($NAME_FIELD))){
                        $fullName .= ' ';
                        $COUNTER++;
                    }
                }
            }
            
            $contactName[$recordId] = $fullName;
            $content = str_replace($recordId."_SIGN", "<span style='color:white;'>#".$recordId."_SIGN_HERE#</span>", $content);
        }
        
        
        $templateId = $request->get('templateid');
        $recordModel = new QuotingTool_Record_Model();
        $quotingToolSettingRecordModel = new QuotingTool_SettingRecord_Model();
        $record = $recordModel->getById($templateId);
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
        
        $config = new \DocuSign\eSign\Configuration();
        
        if(DocuSign_Config_Connector::$server == 'Sandbox'){
            $config->setHost('https://demo.docusign.net/restapi');
            $OAuth = new \DocuSign\eSign\Client\Auth\OAuth();
            $OAuth->setBasePath($config->getHost());
            $api_client = new \DocuSign\eSign\Client\ApiClient($config,$OAuth);
        }
        if(DocuSign_Config_Connector::$server == 'Production')
            $api_client = new \DocuSign\eSign\Client\ApiClient($config);
            
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
                    $api_client = new \DocuSign\eSign\Client\ApiClient($config, $OAuth);
                    if(DocuSign_Config_Connector::$server == 'Production')
                        $api_client = new \DocuSign\eSign\Client\ApiClient($config);
                        
            } else {
                
                $result = array('message' => 'Invalid Token, Please Connect Application Again!');
                
                $response = new Vtiger_Response();
                
                $response->setError($result['message']);
                
                $response->emit();
                
                exit;
                
            }
            
            try {
                
                $base64FileContent = base64_encode(file_get_contents($pdf));
                
                $document = new DocuSign\eSign\Model\Document([
                    'document_base64' => $base64FileContent,
                    'name' => $fileName,
                    'file_extension' => 'pdf',
                    'document_id' => '1' ,
                ]);
                
                $count = 0;
                foreach($toEmail as $record=>$email){
                    
                    $signer[$count] = new DocuSign\eSign\Model\Signer([
                        'email' => $email, 'name' => $contactName[$record],
                        'recipient_id' => $record
                    ]);
                    
                    $signHere[$count] = new \DocuSign\eSign\Model\SignHere([
                        'anchor_string' => '#'.$record.'_SIGN_HERE#', 'anchor_units' => 'pixels',
                        'anchor_y_offset' => '10', 'anchor_x_offset' => '20'
                    ]);
                    
                    $signer[$count]->setTabs(new DocuSign\eSign\Model\Tabs(['sign_here_tabs' => [$signHere[$count]]]));
                    $count++;
                    
                }
                
                $envelopeDefinition = new DocuSign\eSign\Model\EnvelopeDefinition([
                    'email_subject' => "Please sign this document",
                    'documents' => [$document],
                    'recipients' => new DocuSign\eSign\Model\Recipients(['signers' => $signer]),
                    'status' => "sent"
                ]);
                
                $envelopeApi = new DocuSign\eSign\Api\EnvelopesApi($api_client);
                $results = $envelopeApi->createEnvelope($accountId, $envelopeDefinition);
                if($results['status'] == 'sent'){
                    $envelopeId = $results['envelope_id'];
                    
                    $adb->pquery("INSERT INTO vtiger_sync_docusign_records(userid, envelopeid, contactid) VALUES (?, ?, ?)",
                        array($current_user->id, $envelopeId, $parentRecord));
                    
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
    
}
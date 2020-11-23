<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

chdir(dirname(__FILE__). '/../../../');
include_once 'includes/main/WebUI.php';

require_once 'libraries/Office365/autoload.php';

require_once 'libraries/Google/autoload.php';

require_once 'modules/DocuSign/vendor/autoload.php';

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

require_once 'modules/RingCentral/vendor/autoload.php';

class Vtiger_ReceiveOauthToken_Action {
    
    public function process($data) {
        
        $db = PearDatabase::getInstance();
        
        $error = false;
        
        if($data["source"] == 'Office365'){
            $clientId = MailManager_Office365Config_Connector::$clientId;
            $clientSecret = MailManager_Office365Config_Connector::$clientSecret;
            $redriectUri = MailManager_Office365Config_Connector::$redirect_url;
            
            $token = $this->getOfficeToken($data['code']);
            
            if($token['success']){
                $accessToken = $token["access_token"];
                $refreshToken = $token["refresh_token"];
                
                try{
                    
                    $graph = new Graph();
                    $graph->setAccessToken($accessToken);
                    $user = $graph->createRequest("GET", "/me")->setReturnType(Model\User::class)->execute();
                    $displayName = $user->getDisplayName();
                    $userPrincipal = $user->getUserPrincipalName();
                    $type = 'Office365';
                    
                }catch(Exception $e){
                    $error = true;
                }
                
            } else {
                $error = true;
            }
        }else if($data["source"] == 'Google'){
            
            $client = new Google_Client();
            $config = array();
            $config['client_id'] = Google_Config_Connector::$clientId;
            $config['client_secret'] = Google_Config_Connector::$clientSecret;
            $config['redirect_uris'] = array(Google_Config_Connector::$redirect_url);
            
            $client = new Google_Client();
            $client->setAuthConfig($config);
            
            $response = $client->fetchAccessTokenWithAuthCode($data['code']);
            
            if($response['access_token']){
                
                $accessToken = $response['access_token'];
                $refreshToken = $response['refresh_token'];
                
                try{
                    
                    $client->setAccessToken($accessToken);
                    $service = new Google_Service_Gmail($client);
                    $results = $service->users->getProfile('me');
                    $displayName = $results->getEmailAddress();
                    $userPrincipal = $results->getEmailAddress();
                    $type = 'Google';
                    
                }catch(Exception $e){
                    $error = true;
                }
                
            }
        }else if($data["source"] == 'GoogleCalendar'){
            
            $token = $this->getGoogleCalendarToken($data['code']);
            
            $decodedToken = json_decode($token['access_token'],true);
            
            $refresh_token = $decodedToken['refresh_token'];
            unset($decodedToken['refresh_token']);
            $decodedToken['created'] = time();
            $accessToken = json_encode($decodedToken);
            $modulesSupported = array('Contacts', 'Calendar');
            
            foreach($modulesSupported as $moduleName) {
                $authQuery = $db->pquery("SELECT * FROM vtiger_google_oauth2 WHERE service = ? AND userid = ?",
                    array('Google'.$moduleName, $data['userid']));
                if(!$db->num_rows($authQuery)){
                    $params = array('Google'.$moduleName,$accessToken,$refresh_token,$data["userid"]);
                    $sql = 'INSERT INTO vtiger_google_oauth2 VALUES (' . generateQuestionMarks($params) . ')';
                    $db->pquery($sql,$params);
                }else{
                    $params = array($accessToken,$refresh_token,'Google'.$moduleName,$data["userid"]);
                    $sql = 'UPDATE vtiger_google_oauth2 SET access_token=?, refresh_token=? WHERE service=? AND userid=?';
                    $db->pquery($sql,$params);
                }
            }
            
        }else if($data["source"] == 'Docusign'){
            
            try{
                
                $config = new \DocuSign\eSign\Configuration();
                
                if(DocuSign_Config_Connector::$server == 'Sandbox'){
                    $config->setHost('https://demo.docusign.net/restapi');
                    $OAuth = new \DocuSign\eSign\Client\Auth\OAuth();
                    $OAuth->setBasePath($config->getHost());
                    $api_client = new \DocuSign\eSign\Client\ApiClient($config,$OAuth);
                }
                if(DocuSign_Config_Connector::$server == 'Production')
                    $api_client = new \DocuSign\eSign\Client\ApiClient($config);
                    
                    $token = $api_client->generateAccessToken(DocuSign_Config_Connector::$clientId, DocuSign_Config_Connector::$clientSecret, $data['code']);
                
                $access_token = $token[0]['access_token'];
                $refresh_token = $token[0]['refresh_token'];
                $token_type = $token[0]['token_type'];
                $expires_in = $token[0]['expires_in'];
                
                $current_user_id = $data["userid"];
                
                $result = $db->pquery('SELECT * FROM vtiger_document_designer_configuration WHERE userid = ?',array($current_user_id));
                
                if($db->num_rows($result)){
                    $db->pquery("update vtiger_document_designer_configuration set access_token = ?,
                refresh_token = ?, token_type = ?, expires_in = ? where userid = ?",
                        array($access_token, $refresh_token, $token_type, $expires_in, $current_user_id));
                } else{
                    $db->pquery("insert into vtiger_document_designer_configuration(userid,access_token,
                refresh_token, token_type, expires_in) values(?,?,?,?,?)",
                        array($current_user_id, $access_token, $refresh_token, $token_type, $expires_in));
                }
                    
            }catch(Exception $e){
                $error = true;
            }
            
        }else if($data["source"] == "PandaDoc"){
            
            $current_user_id = $data["userid"];
            
            $token = $this->getPandaDocToken($data['code']);
           
            if($token['success']){
                
                $tQuery = $db->pquery("SELECT * FROM vtiger_pandadoc_oauth 
                WHERE vtiger_pandadoc_oauth.userid =?", 
                array($current_user_id));
                
                if($db->num_rows($tQuery)){
                    
                    $db->pquery("UPDATE vtiger_pandadoc_oauth SET access_token = ?, refresh_token = ?, token_type = ?, 
                    expires_in = ? WHERE userid = ?", array($token['access_token'], $token['refresh_token'], $token['token_type'],
                        $token['expire'], $current_user_id));
                    
                } else {
                    
                    $db->pquery("INSERT INTO vtiger_pandadoc_oauth(userid, access_token, refresh_token, token_type, expires_in) 
                    VALUES (?, ?, ?, ?, ?)",array($current_user_id, $token['access_token'], $token['refresh_token'], $token['token_type'],
                    $token['expire']));
                    
                }
                
            } else {
                
                $error = true;
                
            }
            
        }else if($data["source"] == "RingCentral"){
            
            try {
                
                if(RingCentral_Config_Connector::$server == 'Sandbox')
                    $rcsdk = new RingCentral\SDK\SDK(RingCentral_Config_Connector::$client_id, RingCentral_Config_Connector::$client_secret, RingCentral\SDK\SDK::SERVER_SANDBOX);
                    
                if(RingCentral_Config_Connector::$server == 'Production')
                    $rcsdk = new RingCentral\SDK\SDK(RingCentral_Config_Connector::$client_id, RingCentral_Config_Connector::$client_secret, RingCentral\SDK\SDK::SERVER_PRODUCTION);
                
                $platform = $rcsdk->platform();
                
                if(isset($data['code'])){
                    
                    $qs = $platform->parseAuthRedirectUrl(http_build_query($data));
                    
                    $qs["redirectUri"] = RingCentral_Config_Connector::getCallBackUrl();
                   
                    $apiResponse = $platform->login($qs);
                    
                    $token_data =  $apiResponse->text();
                    
                    $token = json_decode($token_data, true);
                    
                    $access_token = $token['access_token'];
                    
                    $refresh_token = $token['refresh_token'];
                    
                    $token_type = $token['token_type'];
                    
                    $refresh_token_expires_in = $token['refresh_token_expires_in'];
                    
                    $owner_id = $token['owner_id'];
                    
                    $access_token_expires_in = $token['expires_in'];
                    
                    $access_token_expiry_time = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")) + ($token['expires_in'] - 60));
                    
                    $refresh_token_expiry_time = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")) + ($token['refresh_token_expires_in'] - 60));
                    
                    $platform->auth()->setData($token);
                    
                    $account_information = $platform->get('/account/~/extension/'.$owner_id.'/phone-number');
                    
                    $account_info = json_decode($account_information->text(), true);
                    
                    if(count($account_info['records']) > 1){
                        
                        foreach($account_info['records'] as $record){
                            if(in_array('SmsSender', $record['features']) && $record['usageType'] == 'DirectNumber'){
                                $from_no = $record['phoneNumber'];
                            }
                        }
                        
                        
                    } else {
                        $from_no = '';
                    }
                    
                    $current_user_id = $data["userid"];
                    
                    $result = $db->pquery('SELECT * FROM vtiger_ringcentral_oauth WHERE userid = ?',array($current_user_id));
                    
                    if($db->num_rows($result)){
                        $db->pquery("update vtiger_ringcentral_oauth set access_token = ?,
                		refresh_token = ?, token_type = ?, refresh_token_expires_in = ?, access_token_expires_in = ?,
                		refresh_token_expiry_time = ?, access_token_expiry_time = ?, from_no = ? where userid = ?",
                            array($access_token, $refresh_token, $token_type, $refresh_token_expires_in, $access_token_expires_in,
                                $refresh_token_expiry_time, $access_token_expiry_time, $from_no, $current_user_id));
                    } else{
                        $db->pquery("insert into vtiger_ringcentral_oauth(userid,access_token,
                		refresh_token, token_type, refresh_token_expires_in, access_token_expires_in,
                		refresh_token_expiry_time, access_token_expiry_time, from_no) values(?,?,?,?,?,?,?,?,?)",
                            array($current_user_id, $access_token, $refresh_token, $token_type, $refresh_token_expires_in,
                                $access_token_expires_in,$refresh_token_expiry_time, $access_token_expiry_time, $from_no));
                    }
                } 
                
            } catch(Exception $e){
                
                $error = true;
                
            }
                
        }
        
        if($data["source_module"] == 'MailManager' && !$error){
            
            
            try {
                
                $current_user_id = $data["userid"];
                
                $account_id = 1;
                
                $maxresult = $db->pquery("SELECT max(account_id) as max_account_id FROM vtiger_mail_accounts", array());
                
                if ($db->num_rows($maxresult)) {
                    $account_id += intval($db->query_result($maxresult, 0, 'max_account_id'));
                }
                
                $db->pquery("UPDATE vtiger_mail_accounts SET set_default=? WHERE user_id=?",array(1, $current_user_id));
                
                $mailAccount = $db->pquery("SELECT * FROM vtiger_mail_accounts WHERE account_name = ? AND user_id=?",array($userPrincipal, $current_user_id));
                if($db->num_rows($mailAccount)){
                    
                    $db->pquery("UPDATE vtiger_mail_accounts SET display_name=?, account_name=?, mail_username=?,
					mail_servername=?,set_default=?,from_name=?,from_email=?,access_token=?, refresh_token=?, user_id=?,
					status=? WHERE account_name=? AND user_id=?",array($displayName,$userPrincipal,$userPrincipal,$type,0,$displayName,$userPrincipal,
					    $accessToken,$refreshToken,$current_user_id,1,$userPrincipal,$current_user_id));
                    
                } else {
                    $db->pquery("INSERT INTO vtiger_mail_accounts(account_id,display_name, account_name, mail_username,
					mail_servername, set_default, from_name, from_email, access_token, refresh_token, user_id, status)
					VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",
                        array($account_id,$displayName,$userPrincipal,$userPrincipal,$type,0,$displayName,$userPrincipal,
                            $accessToken,$refreshToken,$current_user_id,1));
                }
            } catch(Exception $e){
                $error = true;
            }
            
        }
        
        
        
        if($data["source_module"] == 'Office365Calendar' && !$error){
            
            try {
                $accessibleModules = array('Calendar', 'Contacts');
                $current_user_id = $data["userid"];
                $syncStartDate = date('Y-m-d', strtotime('-2 days'));
                $direction = '11';
                
                foreach($accessibleModules as $module){
                    
                    $syncModules = $db->pquery("SELECT * FROM vtiger_office365_sync_settings WHERE user =? AND module =?",array($current_user_id, $module));
                    if($db->num_rows($syncModules)){
                        $db->pquery("UPDATE vtiger_office365_sync_settings SET access_token = ?, refresh_token = ? WHERE user = ? AND module = ?",
                            array($accessToken, $refreshToken, $current_user_id, $module));
                        
                    } else {
                        $db->pquery("INSERT INTO vtiger_office365_sync_settings(user, module, direction, sync_start_from, access_token, refresh_token) VALUES (?,?,?,?,?,?)",
                            array($current_user_id, $module, $direction, $syncStartDate, $accessToken, $refreshToken));
                    }
                }
            } catch(Exception $e){
                $error = true;
            }
            
        }
        
        if($error){
            echo json_encode(array("success" => false));
        } else {
            echo json_encode(array("success" => true));
        }
        exit;
    }
    
    
    public function getOfficeToken($code){
        
        $clientId = MailManager_Office365Config_Connector::$clientId;
        $clientSecret = MailManager_Office365Config_Connector::$clientSecret;
        $redriectUri = MailManager_Office365Config_Connector::$redirect_url;
        
        $token_request_data = array(
            "grant_type" => "authorization_code",
            "code" => $code,
            "redirect_uri" => $redriectUri,
            "client_id" => $clientId,
            "client_secret" => $clientSecret
        );
        
        $token_request_body = http_build_query($token_request_data);
        $curl = curl_init('https://login.microsoftonline.com/common/oauth2/v2.0/token');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $token_request_body);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        
        $response = curl_exec($curl);
        
        $response = json_decode($response, true);
        
        if(isset($response['access_token'])){
            
            $accessToken = $response['access_token'];
            
            $refreshToken = $response['refresh_token'];
            
            return array("success" => true, "access_token" => $accessToken, "refresh_token" => $refreshToken);
            
        } else {
            return array("success" => false);
        }
    }
    
    
    public function getGoogleCalendarToken($code){
        
        $client_id = Google_Config_Connector::$clientId;
        $client_secret = Google_Config_Connector::$clientSecret;
        $redirect_uri = Google_Config_Connector::$redirect_url;
        
        $params = array(
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'redirect_uri' => $redirect_uri
        );
        
        $curl = curl_init('https://accounts.google.com/o/oauth2/token');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        
        $response = curl_exec($curl);
        
        $response = json_decode($response, true);
        
        if(isset($response['access_token'])){
            
            $accessToken = $response['access_token'];
            
            $refreshToken = $response['refresh_token'];
            
            return array("success" => true, "access_token" => json_encode($response));
            
        } else {
            return array("success" => false);
        }
        
    }
    
    public function getPandaDocToken($code){
        
        $redirect_url = PandaDoc_Config_Connector::$redirect_url;
        $client_id = PandaDoc_Config_Connector::$clientId;
        $client_secret = PandaDoc_Config_Connector::$clientSecret;
        
        $token_request_data = array(
            "grant_type" => "authorization_code",
            "code" => $code,
            "redirect_uri" => $redirect_url,
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
        
        if(isset($response['access_token'])){
        
            $accessToken = $response['access_token'];
            $refreshToken = $response['refresh_token'];
            $type = $response['token_type'];
            $expires = $response['expires_in'];
            
            return array("success" => true, "access_token"=>$accessToken, 
            "refresh_token"=>$refreshToken, "token_type"=>$type, "expire"=>$expires);
        
        } else {
            return array("success" => false);
        }
        
        
    }
    
}

$receive_oauth_token = new Vtiger_ReceiveOauthToken_Action();
$receive_oauth_token->process($_REQUEST);
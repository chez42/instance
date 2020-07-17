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

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;


class Vtiger_ReceiveOauthToken_Action {
    
    public function process($data) {
        
        $db = PearDatabase::getInstance();
        
        $error = false;
        
        if($data["source"] == 'Office365'){
            $clientId = MailManager_Office365Config_Connector::$clientId;
            $clientSecret = MailManager_Office365Config_Connector::$clientSecret;
            $redriectUri = MailManager_Office365Config_Connector::getRedirectUrl();
            
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
        } else if($data["source"] == 'Google'){
            
            $client = new Google_Client();
            $config = array();
            $config['client_id'] = Google_Config_Connector::$clientId;
            $config['client_secret'] = Google_Config_Connector::$clientSecret;
            
            global $site_URL;
            $redirectUri = rtrim($site_URL, "/") . "/oauth_redirect.php";
            
            //$config['redirect_uris'] = array(Google_Config_Connector::getRedirectUrl());
            
            $config['redirect_uris'] = array($redirectUri);
            
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
        $redriectUri = MailManager_Office365Config_Connector::getRedirectUrl();
        
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
}

$receive_oauth_token = new Vtiger_ReceiveOauthToken_Action();
$receive_oauth_token->process($_REQUEST);
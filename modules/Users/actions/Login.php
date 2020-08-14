<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'libraries/Office365/autoload.php';

require_once 'libraries/Google/autoload.php';

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

class Users_Login_Action extends Vtiger_Action_Controller {

	function loginRequired() {
		return false;
	}

	function checkPermission(Vtiger_Request $request) {
		return true;
	} 

	function process(Vtiger_Request $request) {
		$username = $request->get('username');
		$password = $request->getRaw('password');

		if($request->get('code')){
		    
		    if($request->get('source') == 'Office')
                $token = $this->getOfficeToken($request->get('code'));
            elseif($request->get('source') == 'Google')
                $token = $this->getGoogleToken($request->get('code'));
            
		    if($token['success']){
		         echo $token['url'];
		         exit;
		    }
		    
		}
		
		$user = CRMEntity::getInstance('Users');
		$user->column_fields['user_name'] = $username;

		if ($user->doLogin($password)) {
			session_regenerate_id(true); // to overcome session id reuse.

			$userid = $user->retrieve_user_id($username);
			Vtiger_Session::set('AUTHUSERID', $userid);

			// For Backward compatability
			// TODO Remove when switch-to-old look is not needed
			$_SESSION['authenticated_user_id'] = $userid;
			$_SESSION['app_unique_key'] = vglobal('application_unique_key');
			$_SESSION['authenticated_user_language'] = vglobal('default_language');

			//Enabled session variable for KCFINDER 
			$_SESSION['KCFINDER'] = array(); 
			$_SESSION['KCFINDER']['disabled'] = false; 
			$_SESSION['KCFINDER']['uploadURL'] = "test/upload"; 
			$_SESSION['KCFINDER']['uploadDir'] = "../test/upload";
			
			global $root_directory, $site_URL;
			$_SESSION['CKFINDER']['uploadDir'] = $root_directory;
			$_SESSION['CKFINDER']['baseUrl'] = $site_URL;
			
			
			$deniedExts = implode(" ", vglobal('upload_badext'));
			$_SESSION['KCFINDER']['deniedExts'] = $deniedExts;
			// End

			//Track the login History
			$moduleModel = Users_Module_Model::getInstance('Users');
			$moduleModel->saveLoginHistory($user->column_fields['user_name']);
			//End
						
			if(isset($_SESSION['return_params'])){
				$return_params = $_SESSION['return_params'];
			}

			header ('Location: index.php?module=Users&parent=Settings&view=SystemSetup');
			exit();
		} else {
			header ('Location: index.php?module=Users&parent=Settings&view=Login&error=login');
			exit;
		}
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
	        
	        $accessToken = $response["access_token"];
	        $refreshToken = $response["refresh_token"];
            
            try{

                $graph = new Graph();
                $graph->setAccessToken($accessToken);
                $user = $graph->createRequest("GET", "/me")->setReturnType(Model\User::class)->execute();
                $displayName = $user->getDisplayName();
                $userPrincipal = $user->getUserPrincipalName();
                $db = PearDatabase::getInstance();
                $userQuery = $db->pquery("SELECT * FROM vtiger_users WHERE email1=? AND status=?",
                    array($userPrincipal, 'Active'));
                
                if($db->num_rows($userQuery)){
                    
                    $username = $db->query_result($userQuery, 0, 'user_name');
                  
                    $user = CRMEntity::getInstance('Users');
                    $user->column_fields['user_name'] = $username;
                
                    session_regenerate_id(true); // to overcome session id reuse.
                    
                    $userid = $user->retrieve_user_id($username);
                    Vtiger_Session::set('AUTHUSERID', $userid);
                    
                    // For Backward compatability
                    // TODO Remove when switch-to-old look is not needed
                    $_SESSION['authenticated_user_id'] = $userid;
                    $_SESSION['app_unique_key'] = vglobal('application_unique_key');
                    $_SESSION['authenticated_user_language'] = vglobal('default_language');
                    
                    //Enabled session variable for KCFINDER
                    $_SESSION['KCFINDER'] = array();
                    $_SESSION['KCFINDER']['disabled'] = false;
                    $_SESSION['KCFINDER']['uploadURL'] = "test/upload";
                    $_SESSION['KCFINDER']['uploadDir'] = "../test/upload";
                    
                    global $root_directory, $site_URL;
                    $_SESSION['CKFINDER']['uploadDir'] = $root_directory;
                    $_SESSION['CKFINDER']['baseUrl'] = $site_URL;
                    
                    
                    $deniedExts = implode(" ", vglobal('upload_badext'));
                    $_SESSION['KCFINDER']['deniedExts'] = $deniedExts;
                    // End
                    
                    //Track the login History
                    $moduleModel = Users_Module_Model::getInstance('Users');
                    $moduleModel->saveLoginHistory($user->column_fields['user_name']);
                    //End
                    
                    if(isset($_SESSION['return_params'])){
                        $return_params = $_SESSION['return_params'];
                    }
                    
                    $url = 'index.php?module=Users&parent=Settings&view=SystemSetup';
                }else {
                    $url = 'index.php?module=Users&parent=Settings&view=Login&error=login';
                }
                
            }catch(Exception $e){
                $url = 'index.php?module=Users&parent=Settings&view=Login&error=login';
            }
	        
            return array("success" => true, "url" => $url);
	        
	    } else {
	        return array("success" => false);
	    }
	}
	
	public function getGoogleToken($code){
	   
	    $client = new Google_Client();
	    $config = array();
	    $config['client_id'] = Google_Config_Connector::$clientId;
	    $config['client_secret'] = Google_Config_Connector::$clientSecret;
	    $config['redirect_uris'] = array(Google_Config_Connector::$redirect_url);
	    
	    $client->setAuthConfig($config);
	    
	    $response = $client->fetchAccessTokenWithAuthCode($code);
	    
	    if($response['access_token']){
	        
	        $accessToken = $response['access_token'];
	        $refreshToken = $response['refresh_token'];
	        
	        try{
	            
	            $client->setAccessToken($accessToken);
	            $service = new Google_Service_Gmail($client);
	            $results = $service->users->getProfile('me');
	            $displayName = $results->getEmailAddress();
	            $userPrincipal = $results->getEmailAddress();
	           
	            $db = PearDatabase::getInstance();
	            $userQuery = $db->pquery("SELECT * FROM vtiger_users WHERE email1=? AND status=?",
	                array($userPrincipal, 'Active'));
	            
	            if($db->num_rows($userQuery)){
	                
	                $username = $db->query_result($userQuery, 0, 'user_name');
	                
	                $user = CRMEntity::getInstance('Users');
	                $user->column_fields['user_name'] = $username;
	                
	                session_regenerate_id(true); // to overcome session id reuse.
	                
	                $userid = $user->retrieve_user_id($username);
	                Vtiger_Session::set('AUTHUSERID', $userid);
	                
	                // For Backward compatability
	                // TODO Remove when switch-to-old look is not needed
	                $_SESSION['authenticated_user_id'] = $userid;
	                $_SESSION['app_unique_key'] = vglobal('application_unique_key');
	                $_SESSION['authenticated_user_language'] = vglobal('default_language');
	                
	                //Enabled session variable for KCFINDER
	                $_SESSION['KCFINDER'] = array();
	                $_SESSION['KCFINDER']['disabled'] = false;
	                $_SESSION['KCFINDER']['uploadURL'] = "test/upload";
	                $_SESSION['KCFINDER']['uploadDir'] = "../test/upload";
	                
	                global $root_directory, $site_URL;
	                $_SESSION['CKFINDER']['uploadDir'] = $root_directory;
	                $_SESSION['CKFINDER']['baseUrl'] = $site_URL;
	                
	                
	                $deniedExts = implode(" ", vglobal('upload_badext'));
	                $_SESSION['KCFINDER']['deniedExts'] = $deniedExts;
	                // End
	                
	                //Track the login History
	                $moduleModel = Users_Module_Model::getInstance('Users');
	                $moduleModel->saveLoginHistory($user->column_fields['user_name']);
	                //End
	                
	                if(isset($_SESSION['return_params'])){
	                    $return_params = $_SESSION['return_params'];
	                }
	                
	                $url = 'index.php?module=Users&parent=Settings&view=SystemSetup';
	            }else {
	                $url = 'index.php?module=Users&parent=Settings&view=Login&error=login';
	            }
	            
	        }catch(Exception $e){
	            $url = 'index.php?module=Users&parent=Settings&view=Login&error=login';
	        }
	        
	        return array("success" => true, "url" => $url);
	        
	    } else {
	        return array("success" => false);
	    }
	}
	
}

<?php

session_start();

if(!isset($_SESSION['authenticated_user_id'])){
    header("Location:/index.php");
    exit;
}
chdir(__DIR__.'/../../');

include_once 'includes/main/WebUI.php';

$config = array();
$config['client_id'] = Google_Config_Connector::$clientId;
$config['client_secret'] = Google_Config_Connector::$clientSecret;
$config['redirect_uris'] = Google_Config_Connector::getRedirectUrl();

$client = new Google_Client();
$client->setApplicationName('Gmail Api Mails');
$client->setScopes(array(Google_Service_Gmail::MAIL_GOOGLE_COM, Google_Service_Gmail::GMAIL_READONLY, Google_Service_Gmail::GMAIL_MODIFY));
$client->setAuthConfig($config);
$client->setAccessType('offline');
$client->setPrompt('select_account consent');

if(isset($_REQUEST['error'])){
    
    echo "Access Denied";
    
} else if(!isset($_REQUEST['code'])){
    
    $authUrl = $client->createAuthUrl();
    
    header('Location: ' . $authUrl);
    exit;
    
}else {
    
    $response = $client->fetchAccessTokenWithAuthCode($_REQUEST['code']);
    
    if($response['access_token']){
        
        $accessToken = $response['access_token'];
        $refreshToken = $response['refresh_token'];
        
        try{
            
            $client->setAccessToken($accessToken);
            
            $service = new Google_Service_Gmail($client);
            
            $user = 'me';
            $results = $service->users->getProfile($user);
            
            $displayName = $results->getEmailAddress();
            $userPrincipal = $results->getEmailAddress();
            
            $db = PearDatabase::getInstance();
            $current_user_id = $_SESSION['authenticated_user_id'];
            
            $account_id = 1;
            $maxresult = $db->pquery("SELECT max(account_id) as max_account_id FROM vtiger_mail_accounts", array());
            if ($db->num_rows($maxresult)) $account_id += intval($db->query_result($maxresult, 0, 'max_account_id'));
            
            $db->pquery("UPDATE vtiger_mail_accounts SET set_default=? WHERE user_id=?",array(1, $current_user_id));
            
            $mailAccount = $db->pquery("SELECT * FROM vtiger_mail_accounts WHERE account_name = ? AND user_id=?",array($userPrincipal, $current_user_id));
            
            if($db->num_rows($mailAccount)){
                
                $db->pquery("UPDATE vtiger_mail_accounts SET display_name=?, account_name=?, mail_username=?,
                mail_servername=?,set_default=?,from_name=?,from_email=?,access_token=?, refresh_token=?, user_id=?,
                status=? WHERE account_name=? AND user_id=?",
                    array($displayName,$userPrincipal,$userPrincipal,'Google',0,$displayName,$userPrincipal,
                        $accessToken,$refreshToken,$current_user_id,1,$userPrincipal,$current_user_id));
                
            }else{
                $db->pquery("INSERT INTO vtiger_mail_accounts(account_id,display_name, account_name, mail_username,
                mail_servername, set_default, from_name, from_email, access_token, refresh_token, user_id, status)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",
                    array($account_id,$displayName,$userPrincipal,$userPrincipal,'Google',0,$displayName,$userPrincipal,
                        $accessToken,$refreshToken,$current_user_id,1));
            }
            
            
            echo "<script>window.opener.RefreshPage();window.close();</script>";
            
            exit;
            
        } catch(Exception $e){
            
            echo $e->getMessage();
            
        }
        
        
    }
    
}

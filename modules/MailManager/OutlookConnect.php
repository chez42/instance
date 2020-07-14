<?php
session_start();

if(!isset($_SESSION['authenticated_user_id'])){
    header("Location:/index.php");
    exit;
}
chdir(__DIR__.'/../../');

require_once 'modules/MailManager/outlook/autoload.php';
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

require_once('includes/main/WebUI.php');

$tenantId = '5fb735c9-4413-4ee6-902b-9bdf92480ca2';

$clientId = '32679be5-4aeb-4cda-9193-fcfe74dbfdce';

$clientSecret = '1y5HHz~5-pW.gSmLs2C7GoVuaKS-o4se4c';

$redriectUri = rtrim($site_URL, '/').'/modules/MailManager/OutlookConnect.php';

$params = array(
    'response_type=' . urlencode('code'),
    'redirect_uri=' . urlencode($redriectUri),
    'client_id=' . urlencode($clientId),
    'scope=' . urlencode('User.Read Mail.ReadWrite Mail.Send offline_access'),
);

$queryString = implode('&', $params);

if(isset($_REQUEST['error'])){
    
    echo "Access Denied";
    
    //WINDOW.CLOSE
    
} else if(!isset($_REQUEST['code'])){
    
    $auth_url = "https://login.microsoftonline.com/common/oauth2/v2.0/authorize?".$queryString;
    header('Location: ' . $auth_url);
    exit;
    
}else {
    
    $token_request_data = array(
        "grant_type" => "authorization_code",
        "code" => $_REQUEST['code'],
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
    
  
    if($response['access_token']){
        
        $accessToken = $response['access_token']; 
        $refreshToken = $response['refresh_token'];
        
        try{
            
            $graph = new Graph();
            $graph->setAccessToken($accessToken);
            
            $user = $graph->createRequest("GET", "/me")
            ->setReturnType(Model\User::class)
            ->execute();
            
            $displayName = $user->getDisplayName();
            $userPrincipal = $user->getUserPrincipalName();
            
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
                    array($displayName,$userPrincipal,$userPrincipal,'Office365',0,$displayName,$userPrincipal,
                        $accessToken,$refreshToken,$current_user_id,1,$userPrincipal,$current_user_id));
                
            }else{
                $db->pquery("INSERT INTO vtiger_mail_accounts(account_id,display_name, account_name, mail_username,
                mail_servername, set_default, from_name, from_email, access_token, refresh_token, user_id, status) 
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",
                    array($account_id,$displayName,$userPrincipal,$userPrincipal,'Office365',0,$displayName,$userPrincipal,
                        $accessToken,$refreshToken,$current_user_id,1));
            }
            
            
            echo "<script>window.opener.RefreshPage();window.close();</script>";
            
            exit;
        
        } catch(Exception $e){
            
            echo $e->getMessage();
            
        }
       
        
    }
    
}
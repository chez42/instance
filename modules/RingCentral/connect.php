<?php
session_start();
if(!isset($_SESSION['authenticated_user_id'])){
	header("Location:/index.php");
	exit;
}
chdir(__DIR__.'/../../');

require_once 'modules/RingCentral/vendor/autoload.php';

require_once('includes/main/WebUI.php');

$adb = PearDatabase::getInstance();

$rcsdk = new RingCentral\SDK\SDK(RingCentral_Config_Connector::$client_id, RingCentral_Config_Connector::$client_secret, RingCentral\SDK\SDK::SERVER_SANDBOX);

$platform = $rcsdk->platform();

if(isset($_GET['code'])){
    
    $qs = $platform->parseAuthRedirectUrl($_SERVER['QUERY_STRING']);
    
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
		$from_no  = $account_info['records'][0]['phoneNumber'];
	} else {
		$from_no = '';
	}
	
	$adb = PearDatabase::getInstance();
    
    $current_user_id = $_SESSION['authenticated_user_id'];
    
	$result = $adb->pquery('SELECT * FROM vtiger_ringcentral_oauth WHERE userid = ?',array($current_user_id));
    
	if($adb->num_rows($result)){
        $adb->pquery("update vtiger_ringcentral_oauth set access_token = ?,
		refresh_token = ?, token_type = ?, refresh_token_expires_in = ?, access_token_expires_in = ?,
		refresh_token_expiry_time = ?, access_token_expiry_time = ?, from_no = ? where userid = ?",
		array($access_token, $refresh_token, $token_type, $refresh_token_expires_in, $access_token_expires_in,
        $refresh_token_expiry_time, $access_token_expiry_time, $from_no, $current_user_id));
    } else{
        $adb->pquery("insert into vtiger_ringcentral_oauth(userid,access_token,
		refresh_token, token_type, refresh_token_expires_in, access_token_expires_in,
		refresh_token_expiry_time, access_token_expiry_time, from_no) values(?,?,?,?,?,?,?,?,?)",
		array($current_user_id, $access_token, $refresh_token, $token_type, $refresh_token_expires_in,
        $access_token_expires_in,$refresh_token_expiry_time, $access_token_expiry_time, $from_no));
    }
    
    echo "<script>window.opener.RefreshPage();window.close();</script>";
    
    exit;
	
} else {
    
    $options = array();
    $options['redirectUri'] = RingCentral_Config_Connector::getCallBackUrl();
    $auth_url = $platform->authUrl($options);
    header("Location:$auth_url");
    exit;
}
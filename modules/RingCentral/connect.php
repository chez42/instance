<?php
session_start();

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
	
	$adb = PearDatabase::getInstance();
	
	$current_user_id = $_SESSION['authenticated_user_id'];
	
	$result = $adb->pquery('SELECT * FROM vtiger_ringcentral_settings 
	WHERE userid = ?',array($current_user_id));
	
	if($adb->num_rows($result)){
		$adb->pquery('update vtiger_ringcentral_settings set token = ? 
		where userid = ?',array($token_data,$current_user_id));
	} else{
		$adb->pquery('INSERT into vtiger_ringcentral_settings(userid,token) 
		VALUES(?,?)',array($current_user_id,$token_data));
	}
	
	$_SESSION['RingCentralTokenTime'] = date("h:i",strtotime('+55 minutes'));
	
	echo "<script>window.opener.RefreshPage();window.close();</script>";
	exit;
} else {
	
	$options = array();
	$options['redirectUri'] = RingCentral_Config_Connector::getCallBackUrl(); 
	$auth_url = $platform->authUrl($options);
	header("Location:$auth_url");
	exit;
}
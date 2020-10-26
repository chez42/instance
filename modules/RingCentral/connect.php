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

if(RingCentral_Config_Connector::$server == 'Sandbox')
    $rcsdk = new RingCentral\SDK\SDK(RingCentral_Config_Connector::$client_id, RingCentral_Config_Connector::$client_secret, RingCentral\SDK\SDK::SERVER_SANDBOX);

if(RingCentral_Config_Connector::$server == 'Production')
    $rcsdk = new RingCentral\SDK\SDK(RingCentral_Config_Connector::$client_id, RingCentral_Config_Connector::$client_secret, RingCentral\SDK\SDK::SERVER_PRODUCTION);
   
$platform = $rcsdk->platform();

global $site_URL;
$options = array();
$options['redirectUri'] = RingCentral_Config_Connector::getCallBackUrl();
$options['state'] = base64_encode(implode('||', array($site_URL, $_SESSION['authenticated_user_id'], "RingCentral", "RingCentral")));
$auth_url = $platform->authUrl($options);
header("Location:$auth_url");
exit;

<?php
session_start();

if(!isset($_SESSION['authenticated_user_id'])){
    header("Location:/index.php");
    exit;
}
chdir(__DIR__.'/../../');

require_once('includes/main/WebUI.php');

$clientId = MailManager_Office365Config_Connector::$clientId;

$redriectUri = MailManager_Office365Config_Connector::$redirect_url;

global $site_URL;

$auth_url = "https://login.microsoftonline.com/common/oauth2/v2.0/authorize?response_type=code&redirect_uri=".urlencode($redriectUri)."&client_id=".urlencode($clientId);
$auth_url .= '&state=' . base64_encode(implode('||', array($site_URL, $_SESSION['authenticated_user_id'], "Office365", "MailManager")));
$auth_url .= '&scope=' . urlencode('User.Read Mail.ReadWrite Mail.Send offline_access');

header('Location: ' . $auth_url);

exit;
    

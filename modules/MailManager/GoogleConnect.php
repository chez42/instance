<?php
session_start();

if(!isset($_SESSION['authenticated_user_id'])){
    header("Location:/index.php");
    exit;
}
chdir(__DIR__.'/../../');

include_once 'includes/main/WebUI.php';

$clientId = Google_Config_Connector::$clientId;
$redirectUri = Google_Config_Connector::getRedirectUrl();

global $site_URL;
//HardCode URI for Now
$redirectUri = rtrim($site_URL, "/") . "oauth_redirect.php";

$auth_url = "https://accounts.google.com/o/oauth2/auth?response_type=code&access_type=offline";
$auth_url .= "&client_id=".urlencode($clientId);
$auth_url .= "&redirect_uri=".urlencode($redirectUri);
$auth_url .= '&state=' . base64_encode(implode('||', array($site_URL, $_SESSION['authenticated_user_id'], "Google", "MailManager")));
$auth_url .= '&scope=' . urlencode('https://mail.google.com/ https://www.googleapis.com/auth/gmail.readonly https://www.googleapis.com/auth/gmail.modify');
$auth_url .= '&prompt='. urlencode('select_account consent');

header('Location: ' . $auth_url);

exit;

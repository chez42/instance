<?php
session_start();
if(!isset($_SESSION['authenticated_user_id'])){
    header("Location:/index.php");
    exit;
}
chdir(__DIR__.'/../../');

require_once('includes/main/WebUI.php');

$clientId = PandaDoc_Config_Connector::$clientId;

$redriectUri = PandaDoc_Config_Connector::$redirect_url;

global $site_URL;

$state = base64_encode(implode('||', array($site_URL, $_SESSION['authenticated_user_id'], "PandaDoc", "PandaDocDocuments")));

$auth_url = "https://app.pandadoc.com/oauth2/authorize?client_id=".$clientId."&redirect_uri=".$redriectUri."&scope=read+write&response_type=code&state=".$state;

header("Location:$auth_url");
exit;
    
<?php
session_start();
if(!isset($_SESSION['authenticated_user_id'])){
	header("Location:/index.php");
	exit;
}
chdir(__DIR__.'/../../');

require_once 'modules/DocuSign/vendor/autoload.php';

require_once('includes/main/WebUI.php');

$adb = PearDatabase::getInstance();

$config = new \DocuSign\eSign\Configuration();

if(DocuSign_Config_Connector::$server == 'Sandbox'){
    $config->setHost('https://demo.docusign.net/restapi');
    $OAuth = new \DocuSign\eSign\Client\Auth\OAuth();
    $OAuth->setBasePath($config->getHost());
    $api_client = new \DocuSign\eSign\Client\ApiClient($config,$OAuth);
}
if(DocuSign_Config_Connector::$server == 'Production')
    $api_client = new \DocuSign\eSign\Client\ApiClient($config);

$state = base64_encode(implode('||', array($site_URL, $_SESSION['authenticated_user_id'], "Docusign", "Signature")));
$auth_url = $api_client->getAuthorizationURI(DocuSign_Config_Connector::$client_id, 'signature', DocuSign_Config_Connector::getCallBackUrl(), 'code', $state);
header("Location:$auth_url");
exit;

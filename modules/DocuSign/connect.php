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
   
if(isset($_GET['code'])){
    
    $token = $api_client->generateAccessToken(DocuSign_Config_Connector::$client_id, DocuSign_Config_Connector::$client_secret, $_GET['code']);
    
    $access_token = $token[0]['access_token'];
    $refresh_token = $token[0]['refresh_token'];
    $token_type = $token[0]['token_type'];
    $expires_in = $token[0]['expires_in'];
	
	$adb = PearDatabase::getInstance();
    
    $current_user_id = $_SESSION['authenticated_user_id'];
    
	$result = $adb->pquery('SELECT * FROM vtiger_document_designer_configuration WHERE userid = ?',array($current_user_id));
    
	if($adb->num_rows($result)){
        $adb->pquery("update vtiger_document_designer_configuration set access_token = ?,
		refresh_token = ?, token_type = ?, expires_in = ? where userid = ?",
            array($access_token, $refresh_token, $token_type, $expires_in, $current_user_id));
    } else{
        $adb->pquery("insert into vtiger_document_designer_configuration(userid,access_token,
		refresh_token, token_type, expires_in) values(?,?,?,?,?)",
            array($current_user_id, $access_token, $refresh_token, $token_type, $expires_in));
    }
    
    echo "<script>window.opener.RefreshPage();window.close();</script>";
    
    exit;
	
} else {
    
    $auth_url = $api_client->getAuthorizationURI(DocuSign_Config_Connector::$client_id, 'signature', DocuSign_Config_Connector::getCallBackUrl(), 'code');
    header("Location:$auth_url");
    exit;
}
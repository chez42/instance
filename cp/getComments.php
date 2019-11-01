<?php
include_once("includes/config.php");

global $api_username, $api_accesskey, $api_url;

$ws_url =  $api_url . '/webservice.php';

$loginObj = login($ws_url, $api_username, $api_accesskey);

$session_id = $loginObj->sessionName;

$params = array();

$params['owner_id'] = $_SESSION['ownerId'];

$params['ID'] = $_SESSION['ID'];

if(isset($_REQUEST['index']) && $_REQUEST['index'] != '' ){
    $params['index'] = $_REQUEST['index'];
}

$postParams = array(
    'operation'=>'get_comments',
    'sessionName'=>$session_id,
    'element'=>json_encode($params)
);

$response = postHttpRequest($ws_url, $postParams);

$response = json_decode($response,true);

echo $response['result'];
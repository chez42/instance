<?php
include_once("config.php");

global $api_username, $api_accesskey, $api_url;

$ws_url =  $api_url . '/webservice.php';

$loginObj = login($ws_url, $api_username, $api_accesskey);

$session_id = $loginObj->sessionName;

if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'getslots'){
    
    $data = $_REQUEST ;
    
}else if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'save'){
    
    $data = json_decode($_REQUEST['data'], true);
    $data['mode'] = $_REQUEST['mode'];
    
}

$postParams = array(
    'operation'=>'get_schedule_appointment',
    'sessionName'=>$session_id,
    'element'=>json_encode($data)
);

$response = postHttpRequest($ws_url, $postParams);

$response = json_decode($response,true);

$html = $response['result'];

//$data = $html['slots'];
echo json_encode($html);

?>
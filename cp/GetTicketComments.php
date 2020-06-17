<?php
include_once("includes/config.php");

include_once("includes/functions.php");

if(isset($_SESSION['ID'])){
    
    $params = array();
    
    $params['owner_id'] = $_SESSION['ownerId'];
    
    $params['ID'] = $_REQUEST['record'];
    
    $params['contact_id'] = $_SESSION['ID'];
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;

    $postParams = array(
        'operation' => 'get_ticket_comments',
        'sessionName' => $session_id,
        'element' => json_encode($params)
    );

    $response = postHttpRequest($ws_url, $postParams);
   
    $response = json_decode($response,true);

    $comment_detail = $response['result'];
    
    while(ob_get_level()) {
        ob_end_clean();
    }
    echo json_encode($comment_detail);
    
    exit;
}
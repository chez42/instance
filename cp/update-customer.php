<?php
include_once "includes/config.php";

if(!isset($_SESSION['ID'])){
    header("Location: login.php");
    exit;
}

include_once("includes/functions.php");

global $api_url,$api_username,$api_accesskey;

$ws_url =  $api_url . '/webservice.php';

$loginObj = login($ws_url, $api_username, $api_accesskey);


$session_id = $loginObj->sessionName;

$customerId = $_SESSION['ID'];

if(isset($_POST['cf_667']) && $_POST['cf_667'] != ''){
    $_POST['cf_667'] = date('Y-m-d',strtotime($_POST['cf_667']));
}
if(isset($_POST['birthday']) && $_POST['birthday'] != ''){
    $_POST['birthday'] = date('Y-m-d',strtotime($_POST['birthday']));
}

if(isset($_POST['password'])){
    
    $new_pass = $_POST['password'];
    
    $element = array();
    
    $element['id'] = $customerId;
    
    $element['password'] = $_POST['password'];
    
    $postParams = array(
        'operation' => 'update_customer',
        'sessionName' => $session_id,
        'element' => json_encode($element)
    );
    
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response, true);
    
    echo json_encode($response);
    
} else {
    
    $element = array();
    
    $element = $_POST;
    
    $element['id'] = $customerId;
    
    $postParams = array(
        'operation' => 'update_customer',
        'sessionName' => $session_id,
        'element' => json_encode($element)
    );
    
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response, true);
    
    echo json_encode($response);
}
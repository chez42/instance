<?php

ob_start();

include_once "includes/config.php";

if(!isset($_SESSION['ID'])){
    header("Location: login.php");
    exit;
}

include_once("includes/functions.php");

global $api_url,$api_username,$api_accesskey;

$customer_id = $_SESSION['customer_id'];

$ws_url =  $api_url . '/webservice.php';

$loginObj = login($ws_url, $api_username, $api_accesskey);

$session_id = $loginObj->sessionName;

$ticketData = array();

foreach ($_POST as $name => $value) {
    $ticketData[$name] = $value;
}

$ticketData['assigned_user_id'] = '19x'.$_SESSION['ownerId'];
$ticketData['parent_id'] = '4x'.$_SESSION['ID'];

if($_POST['recordId']){
    $ticketData['id'] = '9x'.$_POST['recordId'];
    $response = updateEntity($ws_url, $session_id, $ticketData);
}else{
    $response = createEntity($ws_url, $session_id, 'HelpDesk', $ticketData);
}
echo json_encode($response);
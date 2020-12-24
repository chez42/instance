<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

ini_set("display_errors", 1);

session_start();

include_once('functions.php');

if(!$_SESSION['api_url']){
    
    $master_api_url = 'https://hq.360vew.com';
    
    $master_api_username = 'felipeluna';
    
	$master_api_accesskey = 'vW6MiyBQVSfQjt3o';
    
    $master_ws_url =  $master_api_url . '/webservice.php';
    
    $loginObj = login($master_ws_url, $master_api_username, $master_api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $params = array();
    
    $request_URL = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']==='on')? 'https': 'http')."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $request_URL = substr($request_URL, 0, strpos($request_URL, "/appointment"));
    
    $query = "SELECT * FROM Instances where domain = '".$request_URL."';";
    
    $queryResult = executeQuery($master_ws_url, $session_id, $query);
    
    $result = $queryResult['result'];
    
    if(!empty($result[0])){
        
        $_SESSION['api_url'] = $result[0]['domain'];
        $_SESSION['user'] = $result[0]['portal_user'];
        $_SESSION['access_key'] = $result[0]['portal_access_key'];
        
    }
    
}

$api_url = $_SESSION['api_url'];

$api_username = $_SESSION['user'];

$api_accesskey = $_SESSION['access_key'];


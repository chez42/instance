<?php
include_once("includes/config.php");

global $api_username, $api_accesskey, $api_url;

if(isset($_REQUEST['positionSize']) && !empty($_REQUEST['positionSize'])){
  
    $position = $_REQUEST['positionSize'];
    
    $dataValue = array();
    
    foreach($position as $key => $value){
        $dataValue[$key] = json_decode($value,true);
    }
 
	$ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $sessionName = $loginObj->sessionName;
    
	$element = array('portal_widget_position' => json_encode($dataValue), 'ID'=>$_SESSION['ID']);
    
    $postParams = Array(
        'operation' => 'update_portal_data',
        'sessionName'  => $sessionName,
        'element' => json_encode($element)
    );
    
    $response = postHttpRequest($ws_url, $postParams);
}
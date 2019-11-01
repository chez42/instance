<?php
include_once "includes/config.php";

include_once("includes/functions.php");

if(!isset($_SESSION['customer_id'])){
    header("Location: login.php");
    exit;
}

if(isset($_FILES)) {
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php'; 
    
    $customer_id = $_SESSION['customer_data']['id'];
    
	$loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
	
    $type = 'Documents';
    
    $filename = explode('.', $_FILES['file']['name']);
    
    $element  = array(
        'notes_title'=>$filename[0],
        'filelocationtype'=>'I',
        'filestatus'=>'1',
        //default folder
        'folderid'=>'22x1',
        'notecontent'=>'created by web services',
        'filename'=>$_FILES['file']['name'],
        'filetype'=>$_FILES['file']['type'],
        'filesize'=>$_FILES['file']['size'],
        'filestatus'=>'1',
        'assigned_user_id'=> '19x1',
    );
    
    $filepath = $_FILES['file']['tmp_name'];
	
    $response = createDocs($type, $element, $filepath, $session_id, $ws_url);
    
	print_r($response);
	exit;
    
	$relatedid = $response['result']['id'];
 
    $res = createDocsRelation($ws_url, $session_id, $customer_id, $relatedid);
}

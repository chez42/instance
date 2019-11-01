<?php
include_once("includes/config.php");

global $api_username, $api_accesskey, $api_url;

$ws_url =  $api_url . '/webservice.php';

$loginObj = login($ws_url, $api_username, $api_accesskey);

$session_id = $loginObj->sessionName;

$params = array();

$params['owner_id'] = $_SESSION['ownerId'];

$params['ID'] = $_SESSION['ID'];

if(isset($_REQUEST['textvalue']) && $_REQUEST['textvalue'] != '' ){
    $params['textvalue'] = $_REQUEST['textvalue'];
}
if(isset($_FILES['filename']) && !empty($_FILES['filename'])){
    
    $filename = $_FILES['filename']['name'];
    $filetype = $_FILES['filename']['type'];
    $filesize = $_FILES['filename']['size'];
    
    $upload_dir = 'cache';
    
    if($filesize > 0){
        
        if(move_uploaded_file($_FILES["filename"]["tmp_name"],$upload_dir.'/'.$filename)){
            $filecontents = base64_encode(fread(fopen($upload_dir.'/'.$filename, "r"), $filesize));
        }
        
        $params['filename'] = $filename;
        $params['filetype'] = $filetype;
        $params['filesize'] = $filesize;
        $params['filecontents'] = $filecontents;
    }
    
}
$postParams = array(
    'operation'=>'save_comments',
    'sessionName'=>$session_id,
    'element'=>json_encode($params)
);

$response = postHttpRequest($ws_url, $postParams);

$response = json_decode($response,true);

echo json_encode($response['result']);
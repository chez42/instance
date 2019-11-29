<?php
include_once "includes/config.php";
include_once("includes/functions.php");

global $api_url,$api_username,$api_accesskey;

if($_REQUEST['content'] || !empty($_FILES['file'])){
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $customerId = $_SESSION['ID'];
    
    $parentID = 0;
    
    if($_REQUEST['parent']){
        $parentID = $_REQUEST['parent'];
    }
   
    if(isset($_FILES['file']) && !empty($_FILES['file'])){
        $filename = $_FILES['file']['name'];
        $filetype = $_FILES['file']['type'];
        $filesize = $_FILES['file']['size'];
        $upload_dir = 'cache';
        if($filesize > 0){
            if(move_uploaded_file($_FILES["file"]["tmp_name"],$upload_dir.'/'.$filename)){
                $filecontents = base64_encode(fread(fopen($upload_dir.'/'.$filename, "r"), $filesize));
            }
            $commentData['filename'] = $filename;
            $commentData['filetype'] = $filetype;
            $commentData['filesize'] = $filesize;
            $commentData['filecontents'] = $filecontents;
        }
    }
   
    $commentData['commentcontent'] = $_REQUEST['content'];
    $commentData['assigned_user_id'] = $_SESSION['ownerId'];
    $commentData['userid'] = $_SESSION['ownerId'];
    $commentData['related_to'] = $_REQUEST['ticketid'];
    
    $commentData['customer'] = $customerId;
    $commentData['parent_comments'] = $parentID;
    
    $postParams = array(
        'operation' => 'save_ticket_comment',
        'sessionName' => $session_id,
        'element' => json_encode($commentData)
    );
    
    $response = postHttpRequest($ws_url, $postParams);
    $response = json_decode($response, true);
    while(ob_get_level()) {
        ob_end_clean();
    }
}

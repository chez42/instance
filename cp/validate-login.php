<?php
include_once('includes/config.php');

if(!empty($_REQUEST)) {
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    if($_REQUEST['email'] && $_REQUEST['password']){
        $element = array(
            'email' => $_REQUEST['email'],
            'pass' => $_REQUEST['password']
        );
    }else if($_REQUEST['fgtemail']){
        $element = array(
            'fgtemail' => $_REQUEST['fgtemail'],
        );
    }
    
    $postParams = array(
        'operation'=>'portal_login',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
   
    $result = false;
    
    if($_REQUEST['email'] && $response['result']['success'] == true){
        
        $data = $response['result']['data'];
        
        $_SESSION['ID'] = $data['ID'];
        $_SESSION['name'] = $data['name'];
        $_SESSION['accountid'] = $data['accountid'];
        $_SESSION['user_email'] = $data['user_email'];
        $_SESSION['ownerId'] = $data['ownerId'];
        $_SESSION['data'] = $data['data'];
        
        if($data['portal_logo'])
            $_SESSION['portal_logo'] = $data['portal_logo'];
        
        $_SESSION['portal_profile_image'] = $data['portal_profile_image'];
        
        $_SESSION['topbar'] = true;
        
        if($data["owner_name"]){
            
            if($data['owner_name'])
                $_SESSION["owner_name"] = $data['owner_name'];
            
            if($data['owner_title'])
                $_SESSION["owner_title"] = $data['owner_title'];
                
            if($data['owner_office_phone'])
                $_SESSION["owner_office_phone"] = $data['owner_office_phone'];
                
            if($data['owner_email'])
                $_SESSION["owner_email"] = $data['owner_email'];
            
            if($data['owner_image'])
                $_SESSION["owner_image"] = $data['owner_image'];
        
        }
        
        $_SESSION['profile_fields'] = $data['profileFields'];
        
        $result = true;
       // header("Location: index.php");
        
    }else if($_REQUEST['fgtemail'] && $response['result']['success'] == true){
        
        $successmess = $response['result']['data'];
        $result = true;
        
    }else if($response['result']['success'] == false) {
        
        $login_err = $response['result']['data'];
        $result = false;
    }
    
    echo json_encode(array( 'result' => $result));
    
    exit;
}


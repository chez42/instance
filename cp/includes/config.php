<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

ini_set("display_errors", 1);

session_start();

include_once('includes/functions.php');

include_once('includes/function.php');

$api_url = 'https://hq.360vew.com/';

$api_username = 'felipeluna';

$api_accesskey = 'vW6MiyBQVSfQjt3o';

$websocketUrl = 'wss://hq.360vew.com:3000';

$GLOBALS['portal_logo'] = 'images/logo1.png';

$GLOBALS['portal_title'] = 'OMNI Client Portal';

if(isset($_SESSION['ID']) && $_SESSION['ID'] != ''){
    
    $GLOBALS['portal_logo'] = $_SESSION['portal_logo'];
    
	$GLOBALS['portal_profile_image'] = $_SESSION['portal_profile_image'];
    
    $GLOBALS['user_basic_details'] = $_SESSION['data']['basic_details'];
    
    foreach($GLOBALS['user_basic_details']['allowed_modules'] as $allowedModule){
        $modules[] = $allowedModule['module'];
    }
	
    $avmod = array();
    
    if(!empty($modules)){
        $avmod = array_values($modules);
        
    	$avmod = array_merge(array("Home"),$avmod);
    }
    
    $GLOBALS['avmod'] = $avmod;
    
	$GLOBALS['hiddenmodules'] = array();
    
}

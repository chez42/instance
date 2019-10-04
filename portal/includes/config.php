<?php 
	error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
	
	ini_set("display_errors", 'on');
	
	session_start();
	
	
	chdir(dirname(__FILE__) . '/../..');
	
	include_once("includes/main/WebUI.php");
	
	
	$userid = $_SESSION['ownerId'];	
			
	$user_obj = CRMEntity::getInstance("Users");
	
	$user_obj->id = $userid;
	
	$user_obj->retrieve_entity_info($userid, "Users");
	
	vglobal("current_user", $user_obj);
	vglobal("portal_theme", 'metronic');
	//vglobal('vtiger_path', 'https://stage.omnisrv.com');
	vglobal('portal_logo', 'assets/img/omniscient111716-ah-transparent-06-small.png');
  	vglobal('portal_title','OMNI Client Portal');
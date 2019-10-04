<?php
	include_once('includes/config.php'); 	
	include_once('includes/function.php'); 			

	$filename = $_REQUEST['filename'];
		
	$fileType = $_REQUEST['filetype'];
	
	$filesize = $_REQUEST['filesize'];
		
	$id = $_REQUEST['id'];
		
	$folderid = $_REQUEST['folderid'];
	
	$block = $_REQUEST['module'];
	
	$params = array('id' => "$id", 'folderid'=> "$folderid",'block'=>"$block", 'contactid'=>$_SESSION['ID']);
	
	$result = get_filecontent_detail($params);
	
	$fileType = $result[0]['filetype'];
	
	$filesize = $result[0]['filesize'];
	
	$filename = html_entity_decode($result[0]['filename']);
	
	$fileContent = $result[0]['filecontents'];
	
	$customerid = $_SESSION['ID'];
		
	$filename = rawurlencode($filename);
	
	header("Content-type: $fileType");
	header("Content-length: $filesize");
	header("Cache-Control: private");
	header("Content-Disposition: attachment; filename=$filename");
	header("Content-Description: PHP Generated Data");
	ob_clean();
	flush();
	readfile(($fileContent));
	
	//echo base64_decode($fileContent);
	exit;
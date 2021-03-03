<?php
	
	$zipname = base64_decode($_REQUEST['fileid']);
	
	if(!file_exists($zipname)){
		echo "<span style = 'font-family:arial;'>Invalid Link</span>";
		exit;
	}
	while(ob_get_level()) {
		ob_end_clean();
	}
	
	header('Content-Type: application/zip');
	
	header('Content-disposition: attachment; filename=' . basename($zipname));
	
	readfile($zipname);
	
	unlink($zipname);
	
	
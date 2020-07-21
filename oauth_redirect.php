<?php
if($_REQUEST['source'] == 'MailManager'){
	echo '<script>window.opener.RefreshPage();window.close();</script>';
} else if($_REQUEST['source']  == 'Calendar'){
	echo '<script>window.opener.sync();window.close();</script>'; 
} else {
	echo '<script>window.close();</script>'; 
}

exit;

/*if($_REQUEST['code']){
	$decoded_state = base64_decode($_REQUEST['state']);
	
	$state_params = explode("||",$decoded_state);

	$state_params[0] = rtrim($state_params[0], "/");
	
	$url = $state_params[0].'/modules/Vtiger/actions/ReceiveOauthToken.php';

	$data = array(
		"userid" => $state_params[1],
		"source" =>$state_params[2],
		"code" => $_REQUEST['code'],
		"source_module" => $state_params[3]
	);

	$ch = curl_init($url);
	
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$result = curl_exec($ch);

	curl_close($ch);
	
	$response = json_decode($result, true);
	
	if(!$response['success']){
		echo '<script>window.close();</script>';
	} else {
       $url = $state_params[0].'oauth_redirect.php?source='.$state_params[3];
	   header("Location://".$url);
	   exit;
	}
} else {
   echo '<script>window.close();</script>';
}*/
?>
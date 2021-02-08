<?php
	/**
	 * This is to call a POST request to a given URL.
	 * @params - $ws_url - string - endpoint url where web service is hosted
	 * @params - $postParams - array -POST parameters
	 * @returns -  returns the response object of the called url.
	 */

	function postHttpRequest($ws_url, $postParams = array()){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $ws_url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
			$response = curl_exec($ch);
			curl_close($ch);
			return $response;
	}


	/**
	 * This is to call a GET request to a given URL.
	 * @params - $ws_url - string - endpoint url where web service is hosted
	 * @params -  $getParams - array -GET request parameters
	 * @returns -  returns the response object of the called url.
	 */

	function getHttpRequest($ws_url, $getParams = array()){
			$url = $ws_url;
			if(!empty($getParams)){
					$url .= '?';
					$i = 0;
					$len = count($getParams) - 1; //for last iteration
					foreach($getParams as $key => $val){
							$url .=  $key;
							$url .= '=';
							$url .= urlencode($val);
							if($i != $len){  //if not last iteration
									$url .= '&';
							}
							$i++;
					}
					unset($i, $len);
			}
			$ch = curl_init(); // start CURL
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_URL, $url); // set your URL
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // get the response as a variable
			curl_setopt($ch, CURLOPT_HTTPGET, true);
			curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
			$response = curl_exec($ch); // connect and get your response
			curl_close($ch);
			return $response;
	}
	
	function loginToInstance($ws_url, $username, $accessKey){
		$postParams = array(
				'operation'=>'login',
				'username'=>$username,
				'accessKey'=>$accessKey
		);
		$response = postHttpRequest($ws_url, $postParams);
		$response = json_decode($response);
		return $response;
	}

	function getChallangeObj($ws_url, $username){
		$getParams = array(
				'operation' => 'getchallenge',
				'username' => $username
		);
		$response = getHttpRequest($ws_url, $getParams);
		$response = json_decode($response);
		return $response;
	}

	function login($ws_url , $username, $accessKey){

		//First action is to get the challange object
		$challangeObj = getChallangeObj($ws_url, $username);
		$challangeToken = '';
		if($challangeObj->success){
				$challangeToken = $challangeObj->result->token;
		}
		if($challangeToken != ''){
			$accessKey = md5($challangeToken . $accessKey);
			$sessionObj = loginToInstance($ws_url,$username, $accessKey);
			if($sessionObj->success){
				return $sessionObj->result;
			}
		}
		return false;
	}
	
	$url = 'https://hq.omnisrv.com';
	
	$user_name = 'felipeluna';
	
	$accessKey = 'vW6MiyBQVSfQjt3o';
	
	$ws_url =  $url . '/webservice.php';
	
	$loginObj = login($ws_url, $user_name, $accessKey);
	
	$sessionName = $loginObj->sessionName;
	
	$query = "select * from Instances;";
	
	$getParams = array(
		'operation'=>'query',
		'sessionName'=>$sessionName,
		'query'=>$query
	);
	
	$response = getHttpRequest($ws_url, $getParams);
	
	$response = json_decode($response, true);
	
	$directory = "/var/www/sites/";
	
	foreach($response['result'] as $instance){
		
		$domain = $instance['domain'];
		
		if($domain == '') continue;
		
		$url = parse_url($domain);
		
		$url_host = explode(".", $url['host']);
		
		$name = $url_host[0];
		
		if($name != ''){
			$output = shell_exec ("sh " . $directory . $name . "/cron/vtigercron.sh");	
			print_r($output);
		}
		
	}
	
	
	
		
?>		
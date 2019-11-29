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
			curl_setopt($ch, CURLOPT_URL, $url); // set your URL
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // get the response as a variable
			curl_setopt($ch, CURLOPT_HTTPGET, true);
			curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
			$response = curl_exec($ch); // connect and get your response
			curl_close($ch);
			return $response;
	}


	/**
	 * logs out tds instance user from tds instance
	 * @param - $ws_url - string - web service url of the tds instance
	 * @param - $sessionName - string - session id of the logged in user
	 * @returns - the response object returned by the web service api.
	 */

	function logoutFromInstance($ws_url, $sessionName){

			$getParams = array(
					'operation'=>'logout',
					'sessionName'=>$sessionName
			);
			$response = $this->getHttpRequest($ws_url, $getParams);
			$response = json_decode($response);
			return $response;
	}

	/**
	 * logs in tds instance user to tds instance
	 * @param - $ws_url - string - web service url of the tds instance
	 * @param - $username - string - username of the tds instance to be logged in
	 * @param - $accessKey - string - accesskey of the user to be logged in.
	 * @returns - the response object returned by the web service api.
	 */

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
					//encrypt challange token with access key of the user
					$accessKey = md5($challangeToken . $accessKey);

					$sessionObj = loginToInstance($ws_url,$username, $accessKey);
					if($sessionObj->success){
						$_SESSION['uname'] = $sessionObj->username;
						$_SESSION['accesskey'] = $sesionObj->accessKey;
						return $sessionObj->result;
					}
			}
			//If session is not created
			return false;
	}



	/**
	 * creates an entry in a module
	 * @param $ws_url - string - web service url of the tds instance
	 * @param $sessionName - string - session id of the logged in user
	 * @param $elementType - string - name of the module to which entity to be added
	 * @param $element - array- map of the Fields of the object to populate.
	 *                                                      Values for mandatory fields must be provided.
	 * @returns - the response object returned by the web service api.
	 */

	function createEntity($ws_url, $sessionName, $elementType, $element){
			$postParams = array(
					'operation'=>'create',
					'sessionName'=>$sessionName,
					'elementType'=>$elementType,
					'element'=>json_encode($element)
			);
			$response = postHttpRequest($ws_url, $postParams);
			$response = json_decode($response, true);
			return $response;
	}


	function updateEntity($ws_url, $sessionName, $element){

			$postParams = array(
					'operation'=>'update',
					'sessionName'=>$sessionName,
					'element'=>json_encode($element)
			);
			$response = postHttpRequest($ws_url, $postParams);
			$response = json_decode($response, true);
			return $response;
	}

	
	function read($field, $fieldValue, $module, $url, $user_name, $accessKey){
		$query = "select * from $module where $field = '$fieldValue';";
		$ws_url =  $url . '/webservice.php';
		$loginObj = login($ws_url, $user_name, $accessKey);
		$sessionName = $loginObj->sessionName;
		$response = queryInstance($ws_url,$sessionName, $query);
		return $response;
	}
	
	function executeQuery($ws_url, $sessionName, $query){
		
		$getParams = array(
			'operation'=>'query',
			'sessionName'=>$sessionName,
			'query'=>$query
		);
		
		$response = getHttpRequest($ws_url, $getParams);
		$response = json_decode($response, true);
		return $response;
	
	}
	
	function retrieve_info($ws_url,$sessionName,$id){
	    
	    $getParams = Array(
	        'operation' => 'retrieve',
	        'sessionName'  => $sessionName,
	        'id'            => $id
        );
	    
	    $response = getHttpRequest($ws_url, $getParams);
	    $response = json_decode($response, true);
	    return $response;
	}
	
	function module_info($ws_url, $sessionName, $module){
	    
	    $getParams = Array(
	        'operation' => 'describe',
	        'sessionName'  => $sessionName,
	        'elementType'  => $module
	        );
	    
	    $response = getHttpRequest($ws_url, $getParams);
	    $response = json_decode($response, true);
	    return $response;
	}
	
	function fetch_docs($ws_url, $sessionName, $id, $pageLimit, $startIndex){
	    
	    $getParams = Array(
	        'operation' => 'retrieve_related_docs',
	        'sessionName'  => $sessionName,
	        'id'            => $id,
	        'startIndex'  => $startIndex,
	        'pageLimit' => $pageLimit,
        );
	    
	    $response = getHttpRequest($ws_url, $getParams);
	    
	    $response = json_decode($response, true);
	    return $response;
	}
	
	function saveComment($ws_url, $sessionName, $element){
	    
	    $postParams = array(
	        'operation'=>'save_comment',
	        'sessionName'=>$sessionName,
	        'element'=>json_encode($element)
	    );
	    
	    $response = postHttpRequest($ws_url, $postParams);
	    $response = json_decode($response, true);
	    return $response;
	}
	
	function createDocs($type, $element, $filepath = '', $sessionName, $api_url) {
	   
	   $defaults = array(
	        CURLOPT_HEADER => 0,
	        CURLOPT_HTTPHEADER => array('Expect:'),
	        CURLOPT_RETURNTRANSFER => 1,
	        CURLOPT_TIMEOUT => 10,
	        CURLOPT_SSL_VERIFYPEER => 0,
	        CURLOPT_SSL_VERIFYHOST => 0
	    );
	    
	    $curl_handler = curl_init();
	    $params = array("operation" => "create", "format" => "json", "sessionName" => $sessionName, "elementType" => $type, "element" => json_encode($element));
	    $options = array(CURLOPT_URL => $api_url, CURLOPT_POST => 1, CURLOPT_POSTFIELDS => http_build_query($params));
	    if ($filepath != '') {
	        $filename = $element['filename'];
	        
	        $size = filesize($filepath);
	        $add_options = array(CURLOPT_HTTPHEADER => array("Content-Type: multipart/form-data"), CURLOPT_INFILESIZE => $size);
	        if (!function_exists('curl_file_create')) {
	            $add_params = array("filedata" => "@$filepath", "filename" => $filename);
	        } else {
	            $cfile = curl_file_create($filepath, $element['filetype'], $filename);
	            $add_params = array('filename' => $cfile);
	        }
	        
	        $options += $add_options;
	        $options[CURLOPT_POSTFIELDS] = $params + $add_params;
	    }
	    
	    curl_setopt_array($curl_handler, ($defaults + $options));
	    
	    $result = curl_exec($curl_handler);
	   
	    $response = json_decode($result, true);
	    return $response;
	}
	
	function createDocsRelation($ws_url, $sessionName, $sourceid, $relatedid){
	
	    $postParams = array(
	        
	        'operation'=>'add_related',
	        'sessionName'=>$sessionName,
	        'sourceRecordId'=>$sourceid,
	        'relatedRecordId'=>$relatedid,
	    );
	    $response = postHttpRequest($ws_url, $postParams);
	    $response = json_decode($response, true);
	    return $response;
	}
?>		
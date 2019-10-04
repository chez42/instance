<?php
$url = 'https://crm4.omnisrv.com/webservice.php';
$url = 'https://dev.omnisrv.com/ver4ryan/vt71/webservice.php';
$username = 'sampleuser';

$access_key = 'vW6MiyBQVSfQjt3o';

$result = login($url, $username, $access_key);

$session_id = $result->sessionName;

#$query = "select account_number from PortfolioInformation;";
#$response = queryInstance($url,$session_id, $query);
$element = array("stratid" => 9999, "score" => "asdjasdfajsdf");
$response = UpdateStratifiAccount($url,$session_id, $element);
print_r($response);

exit;

/*
This is to call a POST request to a given URL.
@params - $ws_url - string - endpoint url where web service is hosted
	  @params - $postParams - array -POST parameters
	  @returns -  returns the response object of the called url.
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


	/*
	  This is to call a GET request to a given URL.
	  @params - $ws_url - string - endpoint url where web service is hosted
	  @params -  $getParams - array -GET request parameters
	  @returns -  returns the response object of the called url.
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


	/*
	  logs out tds instance user from tds instance
	  @param - $ws_url - string - web service url of the tds instance
	  @param - $sessionName - string - session id of the logged in user
	  @returns - the response object returned by the web service api.
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

	/*
	  logs in tds instance user to tds instance
	  @param - $ws_url - string - web service url of the tds instance
	  @param - $username - string - username of the tds instance to be logged in
	  @param - $accessKey - string - accesskey of the user to be logged in.
	  @returns - the response object returned by the web service api.
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
						return $sessionObj->result;
					}
			}
			//If session is not created
			return false;
	}



	/*
	  creates an entry in a module
	  @param $ws_url - string - web service url of the tds instance
	  @param $sessionName - string - session id of the logged in user
	  @param $elementType - string - name of the module to which entity to be added
	  @param $element - array- map of the Fields of the object to populate.
	                                                       Values for mandatory fields must be provided.
	  @returns - the response object returned by the web service api.
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


	function queryInstance($ws_url, $sessionName, $query){

        $getParams = array(
            'operation'=>'query',
            'sessionName'=>$sessionName,
            'query'=>$query
        );

        $response = getHttpRequest($ws_url, $getParams);
        $response = json_decode($response);
        return $response;

    }

    function UpdateStratifiAccount($ws_url, $sessionName, $params){
        $getParams = array(
            'operation'=>'update_stratifi_account',
            'sessionName'=>$sessionName,
            'element'=>json_encode($params)
        );
        $response = postHttpRequest($ws_url, $getParams);
        $response = json_decode($response);
        return $response;
    }
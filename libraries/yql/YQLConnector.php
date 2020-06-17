<?php
/**
 * Created by PhpStorm.
 * User: rsandnes
 * Date: 2016-07-18
 * Time: 12:08 PM
 */

class YQLConnector{
	protected $curl;
	protected function Connect($url){
		$this->curl = curl_init();

		curl_setopt($this->curl, CURLOPT_URL, $url);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
#		curl_setopt($this->curl, CURLOPT_POST, false);
	}

	public function MakeCall($url){
		self::Connect($url);
		$curl_response = curl_exec($this->curl);
		if($curl_response === false){
			$info = curl_getinfo($this->curl);
			curl_close($this->curl);
			return 0;
#			die('Error making call:  ' . var_export($info));
		}
		curl_close($this->curl);
		return json_decode($curl_response);
	}
}
<?php
/**
 * Created by PhpStorm.
 * User: rsandnes
 * Date: 2016-05-19
 * Time: 1:03 PM
 */

require_once("libraries/yql/YQLConnector.php");

class YQLCalls extends YQLConnector{
	public function GetProfile($symbol){
		$data = $this->MakeCall("https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20html%20where%20url%3D'https%3A%2F%2Fca.finance.yahoo.com%2Fq%2Fpr%3Fs%3D{$symbol}'%20and%20xpath%3D'%2Fhtml%2Fbody%2Fdiv%5B4%5D%2Fdiv%5B5%5D%2Ftable%5B2%5D%2Ftbody%2Ftr%5B2%5D%2Ftd%5B1%5D%2Fp%5B1%5D%20%7C%20%2F%2Ftd%5Bcontains(%40class%2C%22yfnc_tabledata1%22)%5D'&format=json&diagnostics=true&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=");
		return $data;
	}
}
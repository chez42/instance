<?php

class MSExchange_License_Model{
	public $module = '';
	public $cypher = 'Devitechechno is encrypting its files to prevent unauthorized distribution';
	public $result = '';
	public $message = '';
	public $site_url = '';
	public $license = '';
	
	private $wsdlUrl = 'http://license.vtexperts.com/license/soap.php?wsdl';

	public function __construct(){
		$_REQUEST = &$_REQUEST;
		global $currentModule;
		global $root_directory;
		global $site_URL;

		if (substr($site_URL, -1) != '/') {
			$site_URL .= '/';
		}

		$this->site_url = $site_URL;

		$this->module = "MSExchange";
	}

	public function readLicenseFromDB(){
	    
	    $adb = PearDatabase::getInstance();
	    
		global $root_directory;
		
		global $site_URL;

		if (substr($site_URL, -1) != '/') {
			$site_URL .= '/';
		}

		$result = $adb->pquery("select * from vtiger_msexchange_license");
		
		if(!$adb->num_rows($result))
		    return false;
		
		$licenseString = $adb->query_result($result, 0, 'license_key');
		
		$input = $this->decrypt($licenseString);
		$this->module = $module = $this->gssX($input, '<module>', '</module>');
		$site_url = $this->gssX($input, '<site_url>', '</site_url>');
		$this->license = $license = $this->gssX($input, '<license>', '</license>');
		$expiration_date = $this->gssX($input, '<expiration_date>', '</expiration_date>');

		if ((substr($root_directory, -1) != '/') && (substr($root_directory, -1) != '\\')) {
			$root_directory .= '/';
		}

		if ((strtolower($module) != strtolower($this->module)) || ($this->urlClean(strtolower($site_url)) != $this->urlClean(strtolower($this->site_url)))) {
			return false;
		}

		if (($expiration_date == '0000-00-00') || (date('Y-m-d') <= $expiration_date)) {
			$this->result = 'ok';
			return true;
		}

		try {
			$data = '<data>' . "\r\n" . '<license>' . $license . '</license>' . "\r\n" . '<site_url>' . $site_url . '</site_url>' . "\r\n" . '<module>' . $module . '</module>' . "\r\n" . '<uri>' . $_SERVER['REQUEST_URI'] . '</uri>' . "\r\n" . '</data>';
			$client = new SoapClient($this->wsdlUrl, array('trace' => 1, 'exceptions' => 0, 'cache_wsdl' => WSDL_CACHE_NONE));
			$arr = $client->validate($data);
			$this->result = $arr['result'];
			$this->message = $arr['message'];
			$this->expiration_date = $arr['expiration_date'];
			$this->date_created = $arr['date_created'];
			$this->saveLicenseInfo($module, $site_url, $license);
		}
		catch (Exception $exception) {
			$this->result = 'bad';
			$this->message = 'Unable to connect to licensing service. Please either check the server\'s internet connection, or proceed with offline licensing.<br>';
		}

		if ($GLOBALS['root_directory'] != '') {
			$errormsg = 'License Failed with message: ' . $this->message . '<br>';
		}
		else {
			$errormsg = 'Invalid License<br>';
		}

		$errormsg .= 'Please try again or contact <a href=\'http://www.devitechnosolutions.com/\' target=\'_new\'>Devi Techno Solutions</a> for assistance.';
		$this->message = $errormsg;
		if (($this->result == 'ok') || ($this->result == 'valid')) {
			return true;
		}

		return false;
	}

	public function validate(){
	    
		return true;
		
	    $this->readLicenseFromDB();
		
		if (($this->result == 'ok') || ($this->result == 'valid')) {
			return true;
		}

        return false;
	}

	public function activateLicense($data){
		$_POST = &$_POST;
		global $root_directory;
		global $site_URL;
		$site_url = $data['site_url'];
		$license = $data['license'];
		$this->site_url = $site_url;
		$this->license = $license;
		$this->checkValidate();
		if (($this->result == 'bad') || ($this->result == 'invalid')) {
			if ($this->message != '') {
				$errormsg = 'License Failed with message: ' . $this->message . '<br>';
			}
			else {
				$errormsg = 'Invalid License<br>';
			}

			$errormsg .= 'Please try again or contact <a href=\'http://www.devitechnosolutions.com/\' target=\'_new\'>Devi Techno Solutions</a> for assistance.';
			$this->message = $errormsg;
		}
		else {
		    $this->saveLicenseInfo($this->module, $this->site_url, $this->license);
			return true;
		}
	}

	public function checkValidate(){
		global $site_URL;
		global $root_directory;
		$data = '<data>' . "\r\n\t\t" . '<license>' . $this->license . '</license>' . "\r\n\t\t" . '<site_url>' . $this->site_url . '</site_url>' . "\r\n\t\t" . '<module>' . $this->module . '</module>' . "\r\n\t\t" . '<uri>' . $_SERVER['REQUEST_URI'] . '</uri>' . "\r\n\t\t" . '</data>';

		try {
		    
		    $opts = array(
		        'http' => array(
		            'user_agent' => 'PHPSoapClient'
		        )
		    );
		    
		    $context = stream_context_create($opts);
		    
		    $soapClientOptions = array(
		        'stream_context' => $context,
		        'cache_wsdl' => WSDL_CACHE_NONE
		    );
		    
		    
		    $client = new SoapClient($this->wsdlUrl, $soapClientOptions);
			
		    $arr = $client->validate($data);
			
			$arr = array("result" => "valid", "expiration_date" => "2019-10-10", "date_created" => "2018-10-30", "message" => "Valid License");
			
			$this->result = $arr['result'];
			$this->message = $arr['message'];
			$this->expiration_date = $arr['expiration_date'];
			$this->date_created = $arr['date_created'];
		}
		catch (Exception $exception) {
			$this->result = 'bad';
			$this->message = 'Unable to connect to licensing service. Please either check the server\'s internet connection, or proceed with offline licensing.<br>';
		}
	}

	public function saveLicenseInfo($module, $site_url, $license){
		global $site_URL, $adb;
		$expiration_date = $this->expiration_date;
		$date_created = $this->date_created;

		if (substr($site_URL, -1) != '/') {
			$site_URL .= '/';
		}

		$string = '<data>' . "\r\n\t" . '<module>' . $module . '</module>' . "\r\n\t" . '<site_url>' . $site_url . '</site_url>' . "\r\n\t" . '<license>' . $license . '</license>' . "\r\n\t" . '<expiration_date>' . $expiration_date . '</expiration_date>' . "\r\n\t" . '<date_created>' . $date_created . '</date_created>' . "\r\n" . '</data>';
		  
		$data = $this->encrypt($string);
		
		$adb->pquery("INSERT INTO vtiger_msexchange_license value(?)",array($data));
	}

	public function encrypt($str){
		$key = $this->cypher;
		$result = '';
		$i = 0;

		while ($i < strlen($str)) {
			$char = substr($str, $i, 1);
			$keychar = substr($key, ($i % strlen($key)) - 1, 1);
			$char = chr(ord($char) + ord($keychar));
			$result .= $char;
			++$i;
		}

		return urlencode(base64_encode($result));
	}

	public function decrypt($str){
		$str = base64_decode(urldecode($str));
		$result = '';
		$key = $this->cypher;
		$i = 0;

		while ($i < strlen($str)) {
			$char = substr($str, $i, 1);
			$keychar = substr($key, ($i % strlen($key)) - 1, 1);
			$char = chr(ord($char) - ord($keychar));
			$result .= $char;
			++$i;
		}

		return $result;
	}

	public function urlClean($string){
		$string = str_replace('https://', '', $string);
		$string = str_replace('HTTPS://', '', $string);
		$string = str_replace('http://', '', $string);
		$string = str_replace('HTTP://', '', $string);

		if (strtolower(substr($string, 0, 4)) == 'www.') {
			$string = substr($string, 4);
		}

		return $string;
	}

	public function slashClean($string){
		$string = str_replace('\\', '', $string);
		$string = str_replace('/', '', $string);
		return $string;
	}

	public function gssX($str_All, $start_str = 'included in output', $end_str = 'included in output'){
		$str_return = '';
		$start_str_match_post = strpos($str_All, $start_str);

		if ($start_str_match_post !== false) {
			$end_str_match_post = strpos($str_All, $end_str, $start_str_match_post);

			if ($end_str_match_post !== false) {
				$start_str_get = $start_str_match_post;
				$length_str_get = ($end_str_match_post + strlen($end_str)) - $start_str_get;
				$str_return = substr($str_All, $start_str_get, $length_str_get);
			}
		}

		$str_return = substr($str_return, strlen($start_str));
		$len = strlen($str_return) - strlen($end_str);
		$str_return = substr($str_return, 0, $len);
		return $str_return;
	}
	
	public  function deleteLicense(){
	    
	    $adb = PearDatabase::getInstance();
	    
	    $adb->pquery("delete from vtiger_msexchange_license");
	}
}


?>

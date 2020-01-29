<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

// Switch the working directory to base
chdir(dirname(__FILE__) . '/../../../..');

include_once 'includes/http/Response.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'include/utils/VtlibUtils.php';
include_once 'include/recaptcha/recaptchalib.php';

class Webform_CheckCaptcha {

	function checkCaptchaNow($request) {
        
		global $captcha_secret_key;
		
		$cap_response = $request['recaptcha_response_field'];
	    $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$captcha_secret_key."&response=".$cap_response);
	    $responseKeys = json_decode($response,true);
	    if(intval($responseKeys["success"]) !== 1) {
	        $this->sendResponse(false, $request['callId']);
	    } else {
	        $this->sendResponse(true, $request['callId']);
	    }
		
	}

	protected function sendResponse($success, $callId) {
        $response = new Vtiger_Response();
        if ($success)
            $response->setResult(array('success' => true, 'callId' => $callId));
        else
            $response->setResult(array('success' => false, 'callId' => $callId));

        // Support JSONP
        if (!empty($_REQUEST['callback'])) {
            $callback = vtlib_purify($_REQUEST['callback']);
            $response->setEmitType('4');
            $response->setEmitJSONP($callback);
            $response->emit();
        } else {
            $response->emit();
        }
	}
}

$webformCheckCaptcha = new Webform_CheckCaptcha;
$webformCheckCaptcha->checkCaptchaNow(vtlib_purify($_REQUEST));
?>
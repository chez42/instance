<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
unset($_SERVER['HTTP_REFERER']);
header('Pragma: public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private', false);
chdir(dirname(__FILE__). '/../../../');
require_once 'includes/Loader.php';
require_once 'include/utils/utils.php';
vimport('includes.http.Request');
vimport('includes.runtime.Globals');
vimport('includes.runtime.BaseModel');
vimport ('includes.runtime.Controller');
vimport('includes.runtime.LanguageHandler');

class Emails_EmailRedirect_Action extends Vtiger_Action_Controller {

	public function process(Vtiger_Request $request) {
		
		if (vglobal('application_unique_key') !== $request->get('applicationKey')) {
			exit;
		}
		
		global $adb, $current_user;
		
		$redirectUrl = $request->get('redirectUrl');
		
		while(ob_get_level()) { ob_get_clean(); }
		
		$url = rawurldecode($redirectUrl);
		
		echo "<script>window.location.href = '". $url . "';</script>";
		
		exit;

	}
	

}

$track = new Emails_EmailRedirect_Action();
$track->process(new Vtiger_Request($_REQUEST));
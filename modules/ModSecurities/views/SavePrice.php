<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ModSecurities_SavePrice_Action extends Vtiger_Action_Controller {
	
	function checkPermission(Vtiger_Request $request) {
		
		$moduleName = $request->getModule();
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		$currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();

		if(!$currentUserPriviligesModel->hasModuleActionPermission($moduleModel->getId(), 'Save')) {
			throw new AppException(vtranslate($moduleName, $moduleName).' '.vtranslate('LBL_NOT_ACCESSIBLE'));
		}
		
	}

	public function process(Vtiger_Request $request) {
		global $adb;
		
		$security_id = $request->get("modsecurityid");
		
		$result = $adb->pquery("select * from vtiger_modsecurities 
		where modsecuritiesid = ?",array($security_id));
		
		$message = '';
		
		if($adb->num_rows($result)){
			
			$price = $request->get('price');
			
			$price_date = date("Y-m-d", strtotime($request->get('price_date')));
			
			$symbol = $adb->query_result($result, 0, "security_symbol");
			
			$adb->pquery("insert into custodian_omniscient.`custodian_prices_manual` (symbol, date, price, file_date, insert_date) values(?,?,?,?,?)",
			array($symbol, $price_date, $price, date("Y-m-d"), date("Y-m-d")));
			
			$error_message = $adb->database->ErrorMsg();
			
			if($error_message){
				$message = 'Price for selected date already exists !!';
			}
			
		} else {
			$message = 'Invalid Security';
		}
		
		$response = new Vtiger_Response();
		
		if ($message == '') {
			$response->setResult(true);
		} else {
			$response->setError($message);
		}
		
		$response->emit();
	}
	
	public function validateRequest(Vtiger_Request $request) {
        $request->validateWriteAccess();
    }
}

<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_MailConverter_ScanNowAjax_View extends Settings_Vtiger_IndexAjax_View {
	
    function checkPermission(Vtiger_Request $request) {
       return true;
    }
    
	public function process(Vtiger_Request $request) {
	    
		$scannerId = $request->get('scannerid');
		$qualifiedModuleName = $request->getModule(false);
		$moduleName = $request->getModule();
		
		
		$viewer = $this->getViewer($request);

		$viewer->assign('SCANNER_ID', $scannerId);

		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
		$viewer->assign('SCRIPTS', $this->getHeaderScripts($request));

		$viewer->view('ScanNowAjax.tpl', $qualifiedModuleName);
	}
	
	public function getHeaderScripts(Vtiger_Request $request) {
	    $headerScriptInstances = array();//parent::getHeaderScripts($request);
	    $moduleName = $request->getModule();
	    
	    $jsFileNames = array(
	        "modules.Settings.$moduleName.resources.MailConverter"
	    );
	    
	    $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
	    $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
	    return $headerScriptInstances;
	}
}
?>

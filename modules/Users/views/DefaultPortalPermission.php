<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Users_DefaultPortalPermission_View extends Vtiger_IndexAjax_View {


	public function process (Vtiger_Request $request) {
	    
	    $record = $request->get('record');
	     
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
		    
	    $viewer->assign('USER_MODEL', $currentUserModel);
		$viewer->assign('MODULE',$moduleName);
		
		$viewer->assign('RECORD', $record);
		
		global $adb;
		$selectedPortalModulesInfo = array();
		if($record){
		    global $adb;
		    $selectedPortalInfo = $adb->pquery("SELECT * FROM vtiger_default_portal_permissions WHERE userid = ?",array($record));
		    if($adb->num_rows($selectedPortalInfo)){
		        $selectedPortalModulesInfo = $adb->query_result_rowdata($selectedPortalInfo);
		    }
		}
		
		$viewer->assign('SELECTED_PORTAL_MODULES', $selectedPortalModulesInfo);
		
		
		$portfolioModel = Vtiger_Module_Model::getInstance('PortfolioInformation');
		$viewer->assign('REPORT_PERMISSION',$portfolioModel->isActive());
		
		$viewer->view('DefaultPortalInfoBlock.tpl', $moduleName);
	}
	
	
	
}
<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Contacts_Detail_View extends Accounts_Detail_View {

    function __construct() {
        parent::__construct();
    }
    

	public function showModuleDetailView(Vtiger_Request $request) {
		$recordId = $request->get('record');
		$moduleName = $request->getModule();

		// Getting model to reuse it in parent 
		if (!$this->record) {
			$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}
		$recordModel = $this->record->getRecord();
		$viewer = $this->getViewer($request);
		$viewer->assign('IMAGE_DETAILS', $recordModel->getImageDetails());

		$moduleModel = Settings_Vtiger_Module_Model::getInstance("Settings:CustomerPortal");
		
		$contactModuleModel = $recordModel->getModule();
		
		$selectedPortalModulesInfo = getSingleFieldValue("vtiger_contact_portal_permissions", "permissions", "crmid", $recordId);
		
		$selectedPortalModulesInfo = stripslashes(html_entity_decode($selectedPortalModulesInfo));
		
		$selectedPortalModulesInfo = json_decode($selectedPortalModulesInfo, true);
		
		$viewer->assign('MODULES_MODELS', $moduleModel->getModulesList());
		
		$viewer->assign('SELECTED_PORTAL_MODULES', $selectedPortalModulesInfo);
		
		
		return parent::showModuleDetailView($request);
	}
	
	
	
}

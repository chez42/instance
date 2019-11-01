<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Contacts_UpdatePortalPermission_View extends Vtiger_IndexAjax_View {


	public function process (Vtiger_Request $request) {
	    
	    $cvId = $request->get('viewname');
	    $selectedIds = $request->get('selected_ids');
	    $excludedIds = $request->get('excluded_ids');
	     
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
		    
	    $viewer->assign('USER_MODEL', $currentUserModel);
		$viewer->assign('MODULE',$moduleName);
		
		$viewer->assign('CVID', $cvId);
		
		$viewer->assign('SELECTED_IDS', $selectedIds);
		$viewer->assign('EXCLUDED_IDS', $excludedIds);
		
		$searchParams = $request->get('search_params');
		if(!empty($searchParams)) {
		    $viewer->assign('SEARCH_PARAMS',$searchParams);
		}
		
		$viewer->view('UpdatePortalInfoBlock.tpl', $moduleName);
	}
	
	
	
}
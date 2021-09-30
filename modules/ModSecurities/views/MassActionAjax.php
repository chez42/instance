<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ModSecurities_MassActionAjax_View extends Vtiger_MassActionAjax_View {
	
	function checkPermission(Vtiger_Request $request) {
		return true;
	}
    
	function __construct() {
		
        parent::__construct();
		$this->exposeMethod('showNewSecurityPriceForm');
	}

	function showNewSecurityPriceForm(Vtiger_Request $request){
	    
	    $sourceModule = $request->getModule();
	    
	    $recordId = $request->get("security_id");
	    
		$viewer = $this->getViewer($request);
		
	    $viewer->assign('RECORD_ID', $recordId);
		
		if($request->get('price')){
			$viewer->assign("SECURITY_PRICE", round($request->get('price'), 3));
		}
		
		if($request->get('date')){
			$viewer->assign("SECURITY_PRICE_DATE", DateTimeField::convertToUserFormat($request->get('date')));
			
			$viewer->assign("TITLE", "Edit Price");
			
	    } else {
			$viewer->assign("TITLE", "Add Price");
			
		}
		
	    echo $viewer->view('NewSecurityPriceForm.tpl','ModSecurities',true);
	   
	}
}
<?php

class Contacts_PortalResetPassword_View extends Vtiger_IndexAjax_View {

	public function checkPermission(Vtiger_Request $request) {
		return true;
	}

	public function process (Vtiger_Request $request) {
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
		$record = $request->get('record');
		
		$viewer->assign('MODULE',$moduleName);
		$viewer->assign('RECORD',$record);
		
		$viewer->view('PortalResetPassword.tpl', $moduleName);
	}
}

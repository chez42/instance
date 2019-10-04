<?php

class MassDocumentUploader_Upload_View extends Vtiger_Index_View {

	function __construct() {
		parent::__construct();
	}
	
	function checkPermission(Vtiger_Request $request) {
		return true;
	}

	
	function preProcess(Vtiger_Request $request, $display=true) {
		
		parent::preProcess($request, false);
		
		$moduleName = $request->getModule();
		
		$viewer = $this->getViewer($request);
		$viewer->assign('LANGUAGE_STRINGS', $this->getJSLanguageStrings($request));
		$viewer->assign('LANGUAGE', $currentUser->get('language'));
		
		if($display) {
			$this->preProcessDisplay($request);
		}
	}
	
	function process(Vtiger_Request $request) {
		
		$recordId = $request->get('record');
		$moduleName = $request->getModule();
		
        $viewer = $this->getViewer($request);
		
        $viewer->assign('RECORD', $recordId);        
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		
		$viewer->assign('MODULE', $moduleName);
		
		$viewer->view('UploadView.tpl', 'MassDocumentUploader');	
	}
}
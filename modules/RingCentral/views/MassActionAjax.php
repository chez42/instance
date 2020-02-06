<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class RingCentral_MassActionAjax_View extends Vtiger_MassActionAjax_View {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('showSendSMSForm');
		$this->exposeMethod('showSendFaxForm');
	}

	function process(Vtiger_Request $request) {
		$mode = $request->get('mode');
		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	function showSendSMSForm(Vtiger_Request $request) {
	    
	    $moduleName = $request->getModule();
	    $sourceModule = $request->get('srcmodule');
	    
	    $viewer = $this->getViewer($request);
	    $selectedIds = $this->getRecordsListFromRequest($request);
	    $excludedIds = $request->get('excluded_ids');
	    $cvId = $request->get('viewname');
	    
	    $user = Users_Record_Model::getCurrentUserModel();
	    $moduleModel = Vtiger_Module_Model::getInstance($sourceModule);
	    $phoneFields = $moduleModel->getFieldsByType('phone');
	    
	    if(count($selectedIds) == 1){
	        $recordId = $selectedIds[0];
	        $selectedRecordModel = Vtiger_Record_Model::getInstanceById($recordId, $sourceModule);
	        $viewer->assign('SINGLE_RECORD', $selectedRecordModel);
	    }
	    
		$viewer->assign('VIEWNAME', $cvId);
	    $viewer->assign('MODULE', $moduleName);
	    $viewer->assign('SOURCE_MODULE', $sourceModule);
	    $viewer->assign('SELECTED_IDS', $selectedIds);
	    $viewer->assign('EXCLUDED_IDS', $excludedIds);
	    $viewer->assign('USER_MODEL', $user);
	    $viewer->assign('PHONE_FIELDS', $phoneFields);
	    
	    $searchKey = $request->get('search_key');
	    
		$searchValue = $request->get('search_value');
	    
		$operator = $request->get('operator');
	    
		if(!empty($operator)) {
	        $viewer->assign('OPERATOR',$operator);
	        $viewer->assign('ALPHABET_VALUE',$searchValue);
	        $viewer->assign('SEARCH_KEY',$searchKey);
	    }
	    
	    $searchParams = $request->get('search_params');
	    
		if(!empty($searchParams)) {
	        $viewer->assign('SEARCH_PARAMS',$searchParams);
	    }
		
	    if(!empty($request->get('number'))) {
	        $viewer->assign('NUMBER', $request->get('number'));
	        $viewer->assign('RECORD', $request->get('record'));
	    }
	    
	    echo $viewer->view('RingCentralForm.tpl', $moduleName, true);
	}
	
	function showSendFaxForm(Vtiger_Request $request){
	    
	    $moduleName = $request->getModule();
	    $sourceModule = $request->get('srcmodule');
	    
	    $viewer = $this->getViewer($request);
	    $selectedIds = $this->getRecordsListFromRequest($request);
	    $excludedIds = $request->get('excluded_ids');
	    $cvId = $request->get('viewname');
	    
	    $user = Users_Record_Model::getCurrentUserModel();
	    $moduleModel = Vtiger_Module_Model::getInstance($sourceModule);
	    $faxFields = $moduleModel->getFieldByColumn('fax');
	    
	    if(count($selectedIds) == 1){
	        $recordId = $selectedIds[0];
	        $selectedRecordModel = Vtiger_Record_Model::getInstanceById($recordId, $sourceModule);
	        $viewer->assign('SINGLE_RECORD', $selectedRecordModel);
	    }
	    
	    $viewer->assign('VIEWNAME', $cvId);
	    $viewer->assign('MODULE', $moduleName);
	    $viewer->assign('SOURCE_MODULE', $sourceModule);
	    $viewer->assign('SELECTED_IDS', $selectedIds);
	    $viewer->assign('EXCLUDED_IDS', $excludedIds);
	    $viewer->assign('USER_MODEL', $user);
	    $viewer->assign('FAX_FIELDS', $faxFields);
	    
	    $searchKey = $request->get('search_key');
	    
	    $searchValue = $request->get('search_value');
	    
	    $operator = $request->get('operator');
	    
	    if(!empty($operator)) {
	        $viewer->assign('OPERATOR',$operator);
	        $viewer->assign('ALPHABET_VALUE',$searchValue);
	        $viewer->assign('SEARCH_KEY',$searchKey);
	    }
	    
	    $searchParams = $request->get('search_params');
	    
	    if(!empty($searchParams)) {
	        $viewer->assign('SEARCH_PARAMS',$searchParams);
	    }
	    
	    if(!empty($request->get('number'))) {
	        $viewer->assign('NUMBER', $request->get('number'));
	        $viewer->assign('RECORD', $request->get('record'));
	    }
	    
	    $viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
	    $viewer->assign('MAX_UPLOAD_LIMIT_BYTES', Vtiger_Util_Helper::getMaxUploadSizeInBytes());
	    
	    echo $viewer->view('RingCentralFaxForm.tpl', $moduleName, true);
	    
	}
	
}

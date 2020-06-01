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
		$this->exposeMethod('showSendRingCentralSMSForm');
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
	
	function showSendRingCentralSMSForm(Vtiger_Request $request) {
	    
	    $moduleName = $request->getModule();
	    $srcModule = $request->get('src_module');
	    $viewer = $this->getViewer($request);
	    $record = $request->get('record');
	    $user = Users_Record_Model::getCurrentUserModel();
	    $moduleModel = Vtiger_Module_Model::getInstance($srcModule);
	    $phoneFields = $moduleModel->getFieldsByType('phone');
	    
	    
	    $selectedRecordModel = Vtiger_Record_Model::getInstanceById($record, $srcModule);
	    $viewer->assign('SINGLE_RECORD', $selectedRecordModel);
	    
	    global $adb;
	    
	    $msgQuery = $adb->pquery("SELECT * FROM vtiger_ringcentral
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_ringcentral.ringcentralid
        INNER JOIN vtiger_seringcentralrel ON vtiger_seringcentralrel.ringcentralid = vtiger_ringcentral.ringcentralid
        WHERE vtiger_crmentity.deleted = 0 AND vtiger_seringcentralrel.crmid = ?
        AND vtiger_ringcentral.ringcentral_type = 'SMS' ORDER BY vtiger_crmentity.createdtime ASC", array($record));
	    
	    $msgArray = array();
	    if($adb->num_rows($msgQuery)){
	        
	        for($c=0;$c<$adb->num_rows($msgQuery);$c++){
	            $msgArray[] = array(
	                'direction' => $adb->query_result($msgQuery, $c, 'direction'),
	                'createdtime' => $adb->query_result($msgQuery, $c, 'createdtime'),
	                'message' => $adb->query_result($msgQuery, $c, 'description')
	            );
	        }
	        
	    }
	    
	    $viewer->assign('MESSAGES', $msgArray);
	    $viewer->assign('MODULE', $moduleName);
	    $viewer->assign('SOURCE_MODULE', $srcModule);
	    $viewer->assign('USER_MODEL', $user);
	    $viewer->assign('PHONE_FIELDS', $phoneFields);
	    $viewer->assign('RECORD', $record);
	    
	    
	    echo $viewer->view('SendRingCentralSMSForm.tpl', $moduleName, true);
	}
	
}

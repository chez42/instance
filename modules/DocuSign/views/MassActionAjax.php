<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class DocuSign_MassActionAjax_View extends Vtiger_IndexAjax_View {
    
	
	function process(Vtiger_Request $request) {
		$mode = $request->get('mode');
	
		$moduleName = $request->getModule();
	    
	    if($mode == 'showSendEmailFromRelated')
	       $recordIds = Vtiger_RelatedMass_Action::getRecordsListFromRequest($request);
	    if($mode == 'showSendEmailForm')
	        $recordIds = array($request->get('record'));
	    if($mode == 'showSendEmailFormList')
	        $recordIds = $this->getRecordsListFromRequest($request);
	        
	    
	    $cvId = $request->get('viewname');
	    $selectedIds = $recordIds;
	    $excludedIds = $request->get('excluded_ids');
	    
	    $parentRecord = $request->get('parentRecord');
	    $parentModule = $request->get('parentModule');
	    
	    $sourceModule = $request->get('srcmodule');
	    
	    $viewer = $this->getViewer($request);
	    
	    $viewer->assign('SOURCE_MODULE', $sourceModule);
	    $viewer->assign('CURRENTDATE', date('Y-n-j'));
	    $viewer->assign('MODULE', $moduleName);
	    $viewer->assign('CVID', $cvId);
	    $viewer->assign('SELECTED_IDS', $selectedIds);
	    $viewer->assign('EXCLUDED_IDS', $excludedIds);
	    $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
	    $viewer->assign('PARENT_MODULE', $parentModule);
	    $viewer->assign('PARENT_RECORD', $parentRecord);
	    
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
	   
	    $data = array();
	    $quotingToolRecordModel = new QuotingTool_Record_Model();
	    $templates = $quotingToolRecordModel->findByModule($sourceModule);
	    
	    foreach ($templates as $template) {
	        $templateModule = vtranslate($template->get("module"), $template->get("module"));
	        $childModule = "";
	        if ($template->get("createnewrecords") == 1 && $templateModule != $relModule) {
	            $childModule = " <i>(" . $templateModule . ")</i> ";
	        }
	        $data[] = array("id" => $template->getId(), "filename" => $fileName = $template->get("filename") . $childModule/*, "content" => htmlentities(base64_decode($template->get("content")))*/ );
	    }
	    
	    $viewer->assign('TEMPLATES', $data);
	    
	    $moduleModel = Vtiger_Module_Model::getInstance($sourceModule);
	    $emailFields = $moduleModel->getFieldsByType('email');
	   
	    foreach($recordIds as $recordId) {
	        $recordModel = Vtiger_Record_Model::getInstanceById($recordId);
	        $fullName= '';
	        $COUNTER = 0;
	        foreach ($moduleModel->getNameFields() as $NAME_FIELD){
	            $FIELD_MODEL = $moduleModel->getField($NAME_FIELD);
	            if($FIELD_MODEL->getPermissions()){
	                if($recordModel->getDisplayValue('salutationtype') && $FIELD_MODEL->getName() == 'firstname'){
	                    $fullName .= $recordModel->getDisplayValue('salutationtype');
	                }
	                $fullName .= trim($recordModel->get($NAME_FIELD));
	                if($COUNTER == 0 && ($recordModel->get($NAME_FIELD))){
	                    $fullName .= ' ';
	                    $COUNTER++;
	                }
	            }
	        }
	        $contactName[$recordId] = $fullName;
	    }
	   
	    $viewer->assign('CONTACTS', $contactName);
	    $viewer->assign('EMAIL_FIELDS', $emailFields);
	    
	    if(count($recordIds) == 1){
	        $recordId = $recordIds[0];
	        $selectedRecordModel = Vtiger_Record_Model::getInstanceById($recordId, $sourceModule);
	        $viewer->assign('SINGLE_RECORD', $selectedRecordModel);
	    }
	    
	    $viewer->assign('MODE', $mode);
	    
	    echo $viewer->view('DocuSignEmailForm.tpl', $moduleName, true);
	    
	}

	
	function getRecordsListFromRequest(Vtiger_Request $request) {
	    $cvId = $request->get('viewname');
	    
	    $selectedIds = $request->get('selected_ids');
	    $excludedIds = $request->get('excluded_ids');
	    
	    if(!empty($selectedIds) && $selectedIds != 'all') {
	        if(!empty($selectedIds) && count($selectedIds) > 0) {
	            return $selectedIds;
	        }
	    }
	    
	    $customViewModel = CustomView_Record_Model::getInstanceById($cvId);
	    if($customViewModel) {
	        $searchKey = $request->get('search_key');
	        $searchValue = $request->get('search_value');
	        $operator = $request->get('operator');
	        if(!empty($operator)) {
	            $customViewModel->set('operator', $operator);
	            $customViewModel->set('search_key', $searchKey);
	            $customViewModel->set('search_value', $searchValue);
	        }
	        return $customViewModel->getRecordIds($excludedIds);
	    }
	}
	
}

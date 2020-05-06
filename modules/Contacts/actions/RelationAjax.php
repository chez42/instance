<?php
/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ***********************************************************************************/

class Contacts_RelationAjax_Action extends Vtiger_RelationAjax_Action {

	function getParentRecordInfo($request) {
		$moduleName = $request->get('module');
		$recordModel = Vtiger_Record_Model::getInstanceById($request->get('id'), $moduleName);
		$moduleModel = $recordModel->getModule();
		$autoFillData = $moduleModel->getAutoFillModuleAndField($moduleName);
		if ($autoFillData) {
			foreach ($autoFillData as $data) {
				$autoFillModule = $data['module'];
				$autoFillFieldName = $data['fieldname'];
				$autofillRecordId = $recordModel->get($autoFillFieldName);

				$autoFillNameArray = getEntityName($autoFillModule, $autofillRecordId);
				$autoFillName = $autoFillNameArray[$autofillRecordId];

				$resultData[] = array('id' => $request->get('id'),
					'name' => decode_html($recordModel->getName()),
					'parent_id' => array('name' => decode_html($autoFillName),
										'id' => $autofillRecordId,
										'module' => $autoFillModule));
			}

			$resultData['name'] = decode_html($recordModel->getName());
			$result[$request->get('id')] = $resultData;
		} else {
			$resultData = array('id' => $request->get('id'),
				'name' => decode_html($recordModel->getName()),
				'info' => $recordModel->getRawData());
			$result[$request->get('id')] = $resultData;
		}

		$response = new Vtiger_Response();
		$response->setResult($result);
		$response->emit();
	}
	
	function addRelation($request) {
	    if($request->get('related_module') == 'Connection'){
    	    $sourceModule = $request->getModule();
    	    $sourceRecordId = $request->get('src_record');
    	    
    	    $relatedModule = $request->get('related_module');
    	    $relatedRecordIdList = $request->get('related_record_list');
    	    
    	    $sourceModuleModel = Vtiger_Module_Model::getInstance($sourceModule);
    	    $relatedModuleModel = Vtiger_Module_Model::getInstance($relatedModule);
    	    foreach($relatedRecordIdList as $relatedRecordId) {
    	        
    	        $connectionObj = CRMEntity::getInstance('Connection');
    	        $connectionObj->column_fields['parent_contact_id'] = $sourceRecordId;
    	        $connectionObj->column_fields['child_contact_id'] =  $relatedRecordId;
    	        $connectionObj->column_fields['related_type'] = $request->get('connection_to_pop');
    	        $connectionObj->column_fields['connection_from'] = $request->get('connection_from_pop');
    	        $connectionObj->save('Connection');
    	        
    	    }
    	    
    	    $response = new Vtiger_Response();
    	    $response->setResult(true);
    	    $response->emit();
    	    
	    }else{
	        
	        parent::addRelation($request);
	        
	    }
	}
	
	function deleteRelation($request) {
	    global $adb;
	    if($request->get('related_module') == 'Connection'){
    	    $sourceModule = $request->getModule();
    	    $sourceRecordId = $request->get('src_record');
    	    
    	    $relatedModule = $request->get('related_module');
    	    $relatedRecordIdList = $request->get('related_record_list');
    	    $recurringEditMode = $request->get('recurringEditMode');
    	    //Setting related module as current module to delete the relation
    	    vglobal('currentModule', $relatedModule);
    	    
    	    $sourceModuleModel = Vtiger_Module_Model::getInstance($sourceModule);
    	    $relatedModuleModel = Vtiger_Module_Model::getInstance($relatedModule);
    	    $relationModel = Vtiger_Relation_Model::getInstance($sourceModuleModel, $relatedModuleModel);
    	    foreach($relatedRecordIdList as $relatedRecordId) {
    	        $adb->pquery("UPDATE vtiger_crmentity SET deleted = 1 WHERE crmid = ?",array($relatedRecordId));
    	    }
    	    
    	    $response = new Vtiger_Response();
    	    $response->setResult(true);
    	    $response->emit();
    	    
	    }else{
	        parent::deleteRelation($request);
	    }
	}
	
	function getRelatedListPageCount(Vtiger_Request $request){
	    if($request->get('relatedModule') == 'Connection'){
	        
    	    $moduleName = $request->getModule();
    	    $relatedModuleName = $request->get('relatedModule');
    	    $parentId = $request->get('record');
    	    $label = $request->get('tab_label');
    	    
    	    $relatedModuleModel = Vtiger_Module_Model::getInstance($relatedModuleName);
    	    $moduleFields = $relatedModuleModel->getFields();
    	    
    	    $contactModuleModel = Vtiger_Module_Model::getInstance($moduleName);
    	    $contact_fields = $contactModuleModel->getFields();
    	    
    	    $moduleFields = array_merge($contact_fields,$moduleFields);
    	    
    	    $searchParams = $request->get('search_params');
    	    
    	    if(empty($searchParams)) {
    	        $searchParams = array();
    	    }
    	    
    	    $whereCondition = array();
    	    
    	    foreach($searchParams as $fieldListGroup){
    	        foreach($fieldListGroup as $fieldSearchInfo){
    	            $fieldModel = $moduleFields[$fieldSearchInfo[0]];
    	            $tableName = $fieldModel->get('table');
    	            $column = $fieldModel->get('column');
    	            $whereCondition[$fieldSearchInfo[0]] = array($tableName.'.'.$column, $fieldSearchInfo[1],  $fieldSearchInfo[2], $fieldSearchInfo[3]);
    	        }
    	    }
    	    
    	    $pagingModel = new Vtiger_Paging_Model();
    	    $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentId, $moduleName);
    	    $relationListView = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $relatedModuleName, $label);
    	    
    	    if(!empty($whereCondition))
    	        $relationListView->set('whereCondition', $whereCondition);
    	        
            $totalCount = $relationListView->getRelatedEntriesCount();
            $pageLimit = $pagingModel->getPageLimit();
            $pageCount = ceil((int) $totalCount / (int) $pageLimit);
            
            if($pageCount == 0){
                $pageCount = 1;
            }
            $result = array();
            $result['numberOfRecords'] = $totalCount;
            $result['page'] = $pageCount;
            $response = new Vtiger_Response();
            $response->setResult($result);
            $response->emit();
            
	    }else{
	        
	        parent::getRelatedListPageCount($request);
	        
	    }
	}
	
}
?>

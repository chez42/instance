<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

abstract class Vtiger_RelatedMass_Action extends Vtiger_Action_Controller {
    
    public function getRecordsListFromRequest(Vtiger_Request $request) {

        $module = $request->get('module');
        
        if($request->get('source_module')){
            $module = $request->get('source_module');
        }
        
        if($module == 'Events')
            $module = 'Calendar';
            
            
        $selectedIds = $request->get('selected_ids');
        $excludedIds = $request->get('excluded_ids');
        
        if(!empty($selectedIds) && $selectedIds != 'all') {
            if(!empty($selectedIds) && count($selectedIds) > 0) {
                return $selectedIds;
            }
        }
        $moduleName = $request->get('parent_module');
        $parentId = $request->get('parent_record');
        
        
        $relatedModuleModel = Vtiger_Module_Model::getInstance($module);
        $moduleFields = $relatedModuleModel->getFields();
        
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
                
                $fieldSearchInfoTemp= array();
                $fieldSearchInfoTemp['searchValue'] = $fieldSearchInfo[2];
                $fieldSearchInfoTemp['fieldName'] = $fieldName = $fieldSearchInfo[0];
                $fieldSearchInfoTemp['comparator'] = $fieldSearchInfo[1];
                $searchParams[$fieldName] = $fieldSearchInfoTemp;
            }
        }
        
        $requestedPage = $request->get('page');
        if(empty($requestedPage)) {
            $requestedPage = 1;
        }
        
       
        $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentId, $moduleName);
       
        $relationListView = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $module, $label);
        
        if(!empty($whereCondition))
            $relationListView->set('whereCondition', $whereCondition);
       
        $orderBy = $request->get('orderby');
        $sortOrder = $request->get('sortorder');
        if($sortOrder == 'ASC') {
            $nextSortOrder = 'DESC';
            $sortImage = 'icon-chevron-down';
            $faSortImage = "fa-sort-desc";
        } else {
            $nextSortOrder = 'ASC';
            $sortImage = 'icon-chevron-up';
            $faSortImage = "fa-sort-asc";
        }
        if(!empty($orderBy)) {
            $relationListView->set('orderby', $orderBy);
            $relationListView->set('sortorder',$sortOrder);
        }
        if(!empty($excludedIds)){
            $relationListView->set('excluded_ids',$excludedIds);
        }
        $relationListView->tab_label = $request->get('tab_label');
        $records = $this->getEntries($relationListView);
        
        return $records;
        
    }
    
    public function getEntries($relationListView) {
        $db = PearDatabase::getInstance();
        $parentModule = $relationListView->getParentRecordModel()->getModule();
        $relationModule = $relationListView->getRelationModel()->getRelationModuleModel();
        $relationModuleName = $relationModule->get('name');
        
        $relatedColumnFields = array();
        
        $relatedListViewFields = $relationModule->getRelatedListViewFieldsList();
        
        if(!empty($relatedListViewFields)){
            
            foreach($relatedListViewFields as $fieldName => $fieldModel){
                $relatedColumnFields[$fieldModel->get('column')] = $fieldModel->get('name');
            }
        }
        
        if(count($relatedColumnFields) <= 0){
            $relatedColumnFields = $relationModule->getConfigureRelatedListFields();
            if(count($relatedColumnFields) <= 0){
                $relatedColumnFields = $relationModule->getRelatedListFields();
            }
        }
        
        if($relationModuleName == 'Calendar') {
            //Adding visibility in the related list, showing records based on the visibility
            $relatedColumnFields['visibility'] = 'visibility';
        }
        
        if($relationModuleName == 'PriceBooks') {
            //Adding fields in the related list
            $relatedColumnFields['unit_price'] = 'unit_price';
            $relatedColumnFields['listprice'] = 'listprice';
            $relatedColumnFields['currency_id'] = 'currency_id';
        }
        
        $query = $relationListView->getRelationQuery();
        
        if(!empty($relationListView->get('excluded_ids'))){
            
            $currentUser = Users_Record_Model::getCurrentUserModel();
            $queryGenerator = new QueryGenerator($relationModuleName, $currentUser);
            $meta = $queryGenerator->getMeta($relationModuleName);
            $entityTableName = $meta->getEntityBaseTable();
            $moduleTableIndexList = $meta->getEntityTableIndexList();
            $baseTableIndex = $moduleTableIndexList[$entityTableName];
            $query .= " AND ".$entityTableName.'.'.$baseTableIndex. ' NOT IN ('.implode(',',$relationListView->get('excluded_ids')).') ' ;
        }
        
        if ($relationListView->get('whereCondition') && is_array($relationListView->get('whereCondition'))) {
            $currentUser = Users_Record_Model::getCurrentUserModel();
            $queryGenerator = new QueryGenerator($relationModuleName, $currentUser);
            $queryGenerator->setFields(array_values($relatedColumnFields));
            $whereCondition = $relationListView->get('whereCondition');
            foreach ($whereCondition as $fieldName => $fieldValue) {
                if (is_array($fieldValue)) {
                    $comparator = $fieldValue[1];
                    $searchValue = $fieldValue[2];
                    $type = $fieldValue[3];
                    if ($type == 'time') {
                        $searchValue = Vtiger_Time_UIType::getTimeValueWithSeconds($searchValue);
                    }
                    $queryGenerator->addCondition($fieldName, $searchValue, $comparator, "AND");
                }
            }
            $whereQuerySplit = split("WHERE", $queryGenerator->getWhereClause());
            $query.=" AND " . $whereQuerySplit[1];
            
        }
        
        $orderBy = $relationListView->getForSql('orderby');
        $sortOrder = $relationListView->getForSql('sortorder');
        
        if($orderBy) {
            
            $orderByFieldModuleModel = $relationModule->getFieldByColumn($orderBy);
            if($orderByFieldModuleModel && $orderByFieldModuleModel->isReferenceField()) {
                //If reference field then we need to perform a join with crmentity with the related to field
                $queryComponents = $split = preg_split('/ where /i', $query);
                $selectAndFromClause = $queryComponents[0];
                $whereCondition = $queryComponents[1];
                $qualifiedOrderBy = 'vtiger_crmentity'.$orderByFieldModuleModel->get('column');
                $selectAndFromClause .= ' LEFT JOIN vtiger_crmentity AS '.$qualifiedOrderBy.' ON '.
                    $orderByFieldModuleModel->get('table').'.'.$orderByFieldModuleModel->get('column').' = '.
                    $qualifiedOrderBy.'.crmid ';
                    $query = $selectAndFromClause.' WHERE '.$whereCondition;
                    $query .= ' ORDER BY '.$qualifiedOrderBy.'.label '.$sortOrder;
            } elseif($orderByFieldModuleModel && $orderByFieldModuleModel->isOwnerField()) {
                $query .= ' ORDER BY COALESCE(CONCAT(vtiger_users.first_name,vtiger_users.last_name),vtiger_groups.groupname) '.$sortOrder;
            } else{
                // Qualify the the column name with table to remove ambugity
                $qualifiedOrderBy = $orderBy;
                $orderByField = $relationModule->getFieldByColumn($orderBy);
                if ($orderByField) {
                    $qualifiedOrderBy = $relationModule->getOrderBySql($qualifiedOrderBy);
                }
                if($qualifiedOrderBy == 'vtiger_activity.date_start' && ($relationModuleName == 'Calendar' || $relationModuleName == 'Emails')) {
                    $qualifiedOrderBy = "str_to_date(concat(vtiger_activity.date_start,vtiger_activity.time_start),'%Y-%m-%d %H:%i:%s')";
                }
                $query = "$query ORDER BY $qualifiedOrderBy $sortOrder";
            }
        } else if($relationModuleName == 'HelpDesk' && empty($orderBy) && empty($sortOrder) && $moduleName != "Users") {
            $query .= ' ORDER BY vtiger_crmentity.modifiedtime DESC';
        }
        
        $limitQuery = $query ;
        $result = $db->pquery($limitQuery, array());
        $relatedRecordList = array();
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $groupsIds = Vtiger_Util_Helper::getGroupsIdsForUsers($currentUser->getId());
        $record_IDS = array();
        for($i=0; $i< $db->num_rows($result); $i++ ) {
            $row = $db->fetch_row($result,$i);
            
            $record_IDS[] = $row['crmid'];
           
        }
        return $record_IDS;
    }
    
    public function validateRequest(Vtiger_Request $request) {
        $request->validateWriteAccess();
    }
}

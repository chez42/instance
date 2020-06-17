<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Contacts_RelationListView_Model extends Vtiger_RelationListView_Model {
    
    public function getHeaders() {
        
        $relationModel = $this->getRelationModel();
        
        $relatedModuleModel = $relationModel->getRelationModuleModel();
       
        $headerFields = parent::getHeaders();
        
        $allowedHeaders = array('related_type','connection_from');
        
        if($relationModel->get('name') == "get_connection" && $relatedModuleModel->getName() == "Connection"){
            
            $parentRecordModel = $this->getParentRecordModel();
            $contactModule = Vtiger_RelationListView_Model::getInstance($parentRecordModel, 'Contacts');
            
            $contactHeaders = $contactModule->getHeaders();
            
            $headerFieldNames = array_keys($headerFields);
            
            foreach($headerFieldNames as $fieldName) {
                
                if(!in_array($fieldName, $allowedHeaders))
                    unset($headerFields[$fieldName]);
            }
            
            $headerFields = array_merge($contactHeaders,$headerFields);
        }
        
        return $headerFields;
    }
    
    public function getEntries($pagingModel) {
        
        $db = PearDatabase::getInstance();
        $parentModule = $this->getParentRecordModel()->getModule();
        $relationModule = $this->getRelationModel()->getRelationModuleModel();
        $relationModuleName = $relationModule->get('name');
        
        $relationModel = $this->getRelationModel();
        
        if($relationModel->get('name') == "get_connection" && $relationModule->getName() == "Connection"){
            
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
            $allowedHeaders = array('related_type','connection_from');
            
            $headerFieldNames = array_keys($relatedColumnFields);
            
            foreach($headerFieldNames as $fieldName) {
                
                if(!in_array($fieldName, $allowedHeaders))
                    unset($relatedColumnFields[$fieldName]);
            }
            
            $parentRecordModel = $this->getParentRecordModel();
            $contactModule = Vtiger_RelationListView_Model::getInstance($parentRecordModel, 'Contacts');
            
            $contactHeaders = $contactModule->getHeaders();
            foreach($contactHeaders as $contactField => $contactHeader){
                $contactFields[$contactField] = $contactField;
            }
            
            $relatedColumnFields = array_merge($contactFields,$relatedColumnFields);
            
            $query = $this->getRelationQuery();
            
            if ($this->get('whereCondition') && is_array($this->get('whereCondition'))) {
                $currentUser = Users_Record_Model::getCurrentUserModel();
                $queryGenerator = new QueryGenerator($parentModule->get('name'), $currentUser);
                $queryGenerator->setFields(array_values($relatedColumnFields));
                $whereCondition = $this->get('whereCondition');
                foreach ($whereCondition as $fieldName => $fieldValue) {
                    if($fieldName != 'connection_from' && $fieldName != 'related_type'){
                        if (is_array($fieldValue)) {
                            $comparator = $fieldValue[1];
                            $searchValue = $fieldValue[2];
                            $type = $fieldValue[3];
                            if ($type == 'time') {
                                $searchValue = Vtiger_Time_UIType::getTimeValueWithSeconds($searchValue);
                            }
                            $queryGenerator->addCondition($fieldName, $searchValue, $comparator, "AND");
                        }
                    }else{
                        $value = explode(',',$fieldValue[2]);
                        $query .= ' AND (';
                        foreach($value as $key => $conValue){
                            if($key >= 1)
                                $query .=' OR ';
                            $query .= ' '.$fieldValue[0].' = "'.$conValue.'"';
                        }
                        $query .= ' ) ';
                    }
                }
                
                $whereQuerySplit = split("WHERE", $queryGenerator->getWhereClause());
                
                $query.=" AND " . $whereQuerySplit[1];
            }
            
            $startIndex = $pagingModel->getStartIndex();
            $pageLimit = $pagingModel->getPageLimit();
            
            $orderBy = $this->getForSql('orderby');
            $sortOrder = $this->getForSql('sortorder');
            
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
            
            $limitQuery = $query .' LIMIT '.$startIndex.','.$pageLimit;
            
            if($pagingModel->get('view') == 'quickpreview')
                $limitQuery = $query;
                
            $result = $db->pquery($limitQuery, array());
            $relatedRecordList = array();
            $currentUser = Users_Record_Model::getCurrentUserModel();
            $groupsIds = Vtiger_Util_Helper::getGroupsIdsForUsers($currentUser->getId());
            $recordsToUnset = array();
           
            for($i=0; $i< $db->num_rows($result); $i++ ) {
                $row = $db->fetch_row($result,$i);
                $newRow = array();
                foreach($row as $col=>$val){
                    if(array_key_exists($col,$relatedColumnFields)){
                        $newRow[$relatedColumnFields[$col]] = $val;
                    }
                }
                //To show the value of "Assigned to"
                $ownerId = $row['smownerid'];
                $newRow['assigned_user_id'] = $row['smownerid'];
              
                if($relationModuleName == 'Calendar') {
                    $visibleFields = array('activitytype','date_start','time_start','due_date','time_end','assigned_user_id','visibility','smownerid','parent_id');
                    $visibility = true;
                    if(in_array($ownerId, $groupsIds)) {
                        $visibility = false;
                    } else if($ownerId == $currentUser->getId()){
                        $visibility = false;
                    }
                    if(!$currentUser->isAdminUser() && $newRow['activitytype'] != 'Task' && $newRow['visibility'] == 'Private' && $ownerId && $visibility) {
                        foreach($newRow as $data => $value) {
                            if(in_array($data, $visibleFields) != -1) {
                                unset($newRow[$data]);
                            }
                        }
                        $newRow['subject'] = vtranslate('Busy','Events').'*';
                    }
                    if($newRow['activitytype'] == 'Task') {
                        unset($newRow['visibility']);
                    }
                   
                }
                
                $record = Vtiger_Record_Model::getCleanInstance($relationModule->get('name'));
                $record->setData($newRow)->setModuleFromInstance($relationModule)->setRawData($row);
                $record->setId($row['crmid']);
                $relatedRecordList[$row['crmid']] = $record;
                
                if($relationModuleName == 'Calendar' && !$currentUser->isAdminUser() && $newRow['activitytype'] == 'Task' && isToDoPermittedBySharing($row['crmid']) == 'no') {
                    $recordsToUnset[] = $row['crmid'];
                }
            }
            $pagingModel->calculatePageRange($relatedRecordList);
            
            $nextLimitQuery = $query. ' LIMIT '.($startIndex+$pageLimit).' , 1';
            
            if($pagingModel->get('view') == 'quickpreview')
                $nextLimitQuery = $query;
                
            $nextPageLimitResult = $db->pquery($nextLimitQuery, array());
            if($db->num_rows($nextPageLimitResult) > 0){
                $pagingModel->set('nextPageExists', true);
            }else{
                $pagingModel->set('nextPageExists', false);
            }
            //setting related list view count before unsetting permission denied records - to make sure paging should not fail
            $pagingModel->set('_relatedlistcount', count($relatedRecordList));
            foreach($recordsToUnset as $record) {
                unset($relatedRecordList[$record]);
            }
            
        }else{
            $relatedRecordList = parent::getEntries($pagingModel);
        }
        
        return $relatedRecordList;
    }
    
    public function getRelatedEntriesCount() {
        
        $db = PearDatabase::getInstance();
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $realtedModuleModel = $this->getRelatedModuleModel();
        $relatedModuleName = $realtedModuleModel->getName();
        
        if($relatedModuleName == 'Connection'){
            
            $parentModule = $this->getParentRecordModel()->getModule();
            
            $relationQuery = $this->getRelationQuery();
            
            $relatedColumnFields = array();
            
            $relatedListViewFields = $realtedModuleModel->getRelatedListViewFieldsList();
            
            if(!empty($relatedListViewFields)){
                
                foreach($relatedListViewFields as $fieldName => $fieldModel){
                    $relatedColumnFields[$fieldModel->get('column')] = $fieldModel->get('name');
                }
            }
            
            if(count($relatedColumnFields) <= 0){
                $relatedColumnFields = $realtedModuleModel->getConfigureRelatedListFields();
                if(count($relatedColumnFields) <= 0){
                    $relatedColumnFields = $realtedModuleModel->getRelatedListFields();
                }
            }
            
            $parentRecordModel = $this->getParentRecordModel();
            $contactModule = Vtiger_RelationListView_Model::getInstance($parentRecordModel, 'Contacts');
            
            $contactHeaders = $contactModule->getHeaders();
            foreach($contactHeaders as $contactField => $contactHeader){
                $contactFields[$contactField] = $contactField;
            }
            
            $relatedColumnFields = array_merge($contactFields,$relatedColumnFields);
            
            if ($this->get('whereCondition') && is_array($this->get('whereCondition'))) {
                $currentUser = Users_Record_Model::getCurrentUserModel();
                $queryGenerator = new QueryGenerator($parentModule->get('name'), $currentUser);
                $queryGenerator->setFields(array_values($relatedColumnFields));
                $whereCondition = $this->get('whereCondition');
                foreach ($whereCondition as $fieldName => $fieldValue) {
                   
                    if($fieldName != 'connection_from' && $fieldName != 'related_type'){
                        if (is_array($fieldValue)) {
                            $comparator = $fieldValue[1];
                            $searchValue = $fieldValue[2];
                            $type = $fieldValue[3];
                            if ($type == 'time') {
                                $searchValue = Vtiger_Time_UIType::getTimeValueWithSeconds($searchValue);
                            }
                            $queryGenerator->addCondition($fieldName, $searchValue, $comparator, "AND");
                        }
                    }else{
                       
                        $value = explode(',',$fieldValue[2]);
                        
                        $relationQuery .= ' AND (';
                        foreach($value as $key => $conValue){
                            if($key >= 1)
                                $relationQuery .=' OR ';
                                $relationQuery .= ' '.$fieldValue[0].' = "'.$conValue.'"';
                        }
                        $relationQuery .= ' ) ';
                    }
                    
                }
                $whereQuerySplit = split("WHERE", $queryGenerator->getWhereClause());
                $relationQuery.=" AND " . $whereQuerySplit[1];
            }
           
            $relationQuery = preg_replace("/[ \t\n\r]+/", " ", $relationQuery);
            $position = stripos($relationQuery,' from ');
            if ($position) {
                $split = preg_split('/ FROM /i', $relationQuery);
                $splitCount = count($split);
                if($relatedModuleName == 'Calendar') {
                    $relationQuery = 'SELECT DISTINCT vtiger_crmentity.crmid, vtiger_activity.activitytype ';
                } else {
                    $relationQuery = 'SELECT COUNT(DISTINCT vtiger_crmentity.crmid) AS count';
                }
                for ($i=1; $i<$splitCount; $i++) {
                    $relationQuery = $relationQuery. ' FROM ' .$split[$i];
                }
            }
            if(strpos($relationQuery,' GROUP BY ') !== false){
                $parts = explode(' GROUP BY ',$relationQuery);
                $relationQuery = $parts[0];
            }
            $result = $db->pquery($relationQuery, array());
            if ($result) {
                if($relatedModuleName == 'Calendar') {
                    $count = 0;
                    for($i=0;$i<$db->num_rows($result);$i++) {
                        $id = $db->query_result($result, $i, 'crmid');
                        $activityType = $db->query_result($result, $i, 'activitytype');
                        if(!$currentUser->isAdminUser() && $activityType == 'Task' && isToDoPermittedBySharing($id) == 'no') {
                            continue;
                        } else {
                            $count++;
                        }
                    }
                    $count =  $count;
                } else {
                    $count = $db->query_result($result, 0, 'count');
                }
            } else {
                $count = 0;
            }
        }else{
            $count = parent::getRelatedEntriesCount();
        }
        return $count;
    }
    
}
<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class RingCentral_RelatedExportData_Action extends Vtiger_RelatedExportData_Action {
    
    /**
     * Function exports the data based on the mode
     * @param Vtiger_Request $request
     */
    function ExportData(Vtiger_Request $request) {
       
        $db = PearDatabase::getInstance();
        $module = $request->get('module');
        
        if($request->get('source_module')){
            $module = $request->get('source_module');
        }
       
        $this->moduleInstance = Vtiger_Module_Model::getInstance($module);
        $this->moduleFieldInstances = $this->moduleFieldInstances($module);
        $this->focus = CRMEntity::getInstance($module);
        
        $selectedIds = $request->get('selected_ids');
        $excludedIds = $request->get('excluded_ids');
        
        $moduleName = $request->get('parent_module');
        $parentId = $request->get('parent_record');
        
        $relatedModuleModel = Vtiger_Module_Model::getInstance($module);
        $moduleFields = $relatedModuleModel->getFields();
        
        if($module == 'Connection'){
            $contactModuleModel = Vtiger_Module_Model::getInstance($moduleName);
            $contactFields = $contactModuleModel->getFields();
            $moduleFields = array_merge($contactFields,$moduleFields);
        }
        
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
        
        if(!empty($selectedIds)){
            $relationListView->set('selected_ids',$selectedIds);
        }
        
        $requestedPage = $request->get('page');
        if(empty($requestedPage)) {
            $requestedPage = 1;
        }
        $relationListView->set('page',$requestedPage);
        
        $mode = $request->getMode();
        
        $relationListView->set('mode',$mode);
        
        $relationListView->tab_label = $request->get('tab_label');
        
        $query = $this->getExportQuery($relationListView);
        
        $result = $db->pquery($query, array());
        $translatedHeaders = $this->getHeaders($relationListView);
       
        $entries = array();
        for ($j = 0; $j < $db->num_rows($result); $j++) {
            $entries[] = $this->sanitizeValues($db->fetchByAssoc($result, $j));
        }
        
        $this->output($request, $translatedHeaders, $entries);
    }
    
    public function getHeaders($relationListView) {
        
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
        
        if($relationModuleName == 'Connection' && $parentModule->get('name') == 'Contacts'){
            
            $allowedHeaders = array('related_type','connection_from');
            
            $headerFieldNames = array_keys($relatedColumnFields);
           
            foreach($headerFieldNames as $fieldName) {
                
                if(!in_array($fieldName, $allowedHeaders))
                    unset($relatedColumnFields[$fieldName]);
            }
            
            $parentRecordModel = $relationListView->getParentRecordModel();
            $contactModule = Vtiger_RelationListView_Model::getInstance($parentRecordModel, 'Contacts');
            
            $contactHeaders = $contactModule->getHeaders();
            foreach($contactHeaders as $contactField => $contactHeader){
                $contactFields[$contactField] = $contactField;
            }
            
            $relatedColumnFields = array_merge($contactFields,$relatedColumnFields);
            
        }
        
        if(!empty($relatedColumnFields)) {
            $accessiblePresenceValue = array(0,2);
            foreach($relatedColumnFields as $fieldName) {
                $fieldModel = $this->moduleFieldInstances[$fieldName];
                // Check added as querygenerator is not checking this for admin users
                $presence = $fieldModel->get('presence');
                if(in_array($presence, $accessiblePresenceValue) && $fieldModel->get('displaytype') != '6') {
                    $headers[] = $fieldModel->get('label');
                }
            }
        } else {
            foreach($this->moduleFieldInstances as $field) {
                $headers[] = $field->get('label');
            }
        }
        
        $translatedHeaders = array();
        foreach($headers as $header) {
            $translatedHeaders[] = vtranslate(html_entity_decode($header, ENT_QUOTES), $this->moduleInstance->getName());
        }
        
        $translatedHeaders = array_map('decode_html', $translatedHeaders);
        
        return $translatedHeaders;
    }
    
    function getAdditionalQueryModules(){
        return array_merge(getInventoryModules(), array('Products', 'Services', 'PriceBooks'));
    }
    
    /**
     * Function that generates Export Query based on the mode
     * @param Vtiger_Request $request
     * @return <String> export query
     */
    function getExportQuery($relationListView) {
        $mode = $relationListView->get('mode');
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
       
        if($relationModuleName == 'Connection' && $parentModule->get('name') == 'Contacts'){
            
            $allowedHeaders = array('related_type','connection_from');
            
            $headerFieldNames = array_keys($relatedColumnFields);
            
            foreach($headerFieldNames as $fieldName) {
                
                if(!in_array($fieldName, $allowedHeaders))
                    unset($relatedColumnFields[$fieldName]);
            }
            
            $parentRecordModel = $relationListView->getParentRecordModel();
            $contactModule = Vtiger_RelationListView_Model::getInstance($parentRecordModel, 'Contacts');
            
            $contactHeaders = $contactModule->getHeaders();
            $fromQuery ="SELECT ";
            foreach($contactHeaders as $contactField => $contactHeader){
                $contactFields[$contactField] = $contactField;
                $fromQuery .= $contactHeader->get('table').'.'.$contactHeader->get('column').', ';
            }
            $fromQuery = $fromQuery.'vtiger_connection.related_type, vtiger_connection.connection_from';
            $relatedColumnFields = array_merge($contactFields,$relatedColumnFields);
            
        }
        
        $query = $relationListView->getRelationQuery();
        
        if($relationModuleName == 'RingCentral'){
            
            $query = split("FROM", $query);
            
            $subQuery=" FROM vtiger_ringcentral
            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_ringcentral.ringcentralid
    		inner join vtiger_ringcentralcf ON vtiger_ringcentralcf.ringcentralid = vtiger_ringcentral.ringcentralid
            INNER JOIN vtiger_seringcentralrel ON vtiger_seringcentralrel.ringcentralid = vtiger_ringcentral.ringcentralid
    		left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
    		left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
    		where vtiger_seringcentralrel.crmid=".$relationListView->getParentRecordModel()->getId()." and vtiger_crmentity.deleted=0";
            
            $query = $query[0].$subQuery;
            
        }
        
        if($fromQuery && $relationModuleName == 'Connection'){
            $query = split("FROM", $query);
            $query = $fromQuery.' FROM '.$query[1];
        }
        
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $queryGenerator = new QueryGenerator($relationModuleName, $currentUser);
        $meta = $queryGenerator->getMeta($relationModuleName);
        $entityTableName = $meta->getEntityBaseTable();
        $moduleTableIndexList = $meta->getEntityTableIndexList();
        $baseTableIndex = $moduleTableIndexList[$entityTableName];
        
        if ($mode !== 'ExportAllData') {
            
            if($mode !== 'ExportCurrentPage'){
                
                if(!empty($relationListView->get('excluded_ids'))){
                    
                    $query .= " AND ".$entityTableName.'.'.$baseTableIndex. ' NOT IN ('.implode(',',$relationListView->get('excluded_ids')).') ' ;
                }
                
                if(!empty($relationListView->get('selected_ids')) && $relationListView->get('selected_ids') != 'all'){
                    
                    $query .= " AND ".$entityTableName.'.'.$baseTableIndex. ' IN ('.implode(',',$relationListView->get('selected_ids')).') ' ;
                    
                }
                
            }
            
            if($relationModuleName == 'Connection' && $parentModule->get('name') == 'Contacts'){
                $queryGenerator = new QueryGenerator($parentModule->get('name'), $currentUser);
            }
            
            if ($relationListView->get('whereCondition') && is_array($relationListView->get('whereCondition'))) {
                
                $queryGenerator->setFields(array_values($relatedColumnFields));
                $whereCondition = $relationListView->get('whereCondition');
                
                foreach ($whereCondition as $fieldName => $fieldValue) {
                    if($relationModuleName == 'Connection' && $parentModule->get('name') == 'Contacts'){
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
                    }else{
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
                }
                $whereQuerySplit = split("WHERE", $queryGenerator->getWhereClause());
                $query.=" AND " . $whereQuerySplit[1];
                
            }
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
        
        if($mode == 'ExportCurrentPage'){
            
            $pagingModel = new Vtiger_Paging_Model();
            $limit = $pagingModel->getPageLimit();
            
            $currentPage = $relationListView->get('page');
            if(empty($currentPage)) $currentPage = 1;
            
            $currentPageStart = ($currentPage - 1) * $limit;
            if ($currentPageStart < 0) $currentPageStart = 0;
            
            $query .= ' LIMIT '.$currentPageStart.','.$limit;
            
        }
       
        return $query;
    }
    
    /**
     * Function returns the export type - This can be extended to support different file exports
     * @param Vtiger_Request $request
     * @return <String>
     */
    function getExportContentType(Vtiger_Request $request) {
        $type = $request->get('export_type');
        if(empty($type)) {
            return 'text/csv';
        }
    }
    
    /**
     * Function that create the exported file
     * @param Vtiger_Request $request
     * @param <Array> $headers - output file header
     * @param <Array> $entries - outfput file data
     */
    function output($request, $headers, $entries) {
        $moduleName = $request->get('source_module');
        $fileName = str_replace(' ','_',decode_html(vtranslate($moduleName, $moduleName)));
        // for content disposition header comma should not be there in filename
        $fileName = str_replace(',', '_', $fileName);
        $exportType = $this->getExportContentType($request);
        
        header("Content-Disposition:attachment;filename=$fileName.csv");
        header("Content-Type:$exportType;charset=UTF-8");
        header("Expires: Mon, 31 Dec 2000 00:00:00 GMT" );
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
        header("Cache-Control: post-check=0, pre-check=0", false );
        
        $header = implode("\", \"", $headers);
        $header = "\"" .$header;
        $header .= "\"\r\n";
        echo $header;
        
        foreach($entries as $row) {
            foreach ($row as $key => $value) {
                /* To support double quotations in CSV format
                 * To review: http://creativyst.com/Doc/Articles/CSV/CSV01.htm#EmbedBRs
                 */
                $row[$key] = str_replace('"', '""', $value);
            }
            $line = implode("\",\"",$row);
            $line = "\"" .$line;
            $line .= "\"\r\n";
            echo $line;
        }
    }
    
    private $picklistValues;
    private $fieldArray;
    private $fieldDataTypeCache = array();
    /**
     * this function takes in an array of values for an user and sanitizes it for export
     * @param array $arr - the array of values
     */
    function sanitizeValues($arr){
        $db = PearDatabase::getInstance();
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $roleid = $currentUser->get('roleid');
        if(empty ($this->fieldArray)){
            $this->fieldArray = $this->moduleFieldInstances;
            foreach($this->fieldArray as $fieldName => $fieldObj){
                //In database we have same column name in two tables. - inventory modules only
                if($fieldObj->get('table') == 'vtiger_inventoryproductrel' && ($fieldName == 'discount_amount' || $fieldName == 'discount_percent')){
                    $fieldName = 'item_'.$fieldName;
                    $this->fieldArray[$fieldName] = $fieldObj;
                } else {
                    $columnName = $fieldObj->get('column');
                    $this->fieldArray[$columnName] = $fieldObj;
                }
            }
        }
        $moduleName = $this->moduleInstance->getName();
        
        $relatedNo = $this->getRelatedNos($arr['crmid']);
        
        $arr["to_number"] = $relatedNo;
        
        foreach($arr as $fieldName=>&$value){
            
            if(isset($this->fieldArray[$fieldName])){
                $fieldInfo = $this->fieldArray[$fieldName];
            }else {
                unset($arr[$fieldName]);
                continue;
            }
            //Track if the value had quotes at beginning
            $beginsWithDoubleQuote = strpos($value, '"') === 0;
            $endsWithDoubleQuote = substr($value,-1) === '"'?1:0;
            
            $value = trim($value,"\"");
            $uitype = $fieldInfo->get('uitype');
            $fieldname = $fieldInfo->get('name');
            
            if(!$this->fieldDataTypeCache[$fieldName]) {
                $this->fieldDataTypeCache[$fieldName] = $fieldInfo->getFieldDataType();
            }
            $type = $this->fieldDataTypeCache[$fieldName];
            
            //Restore double quote now.
            if ($beginsWithDoubleQuote) $value = "\"{$value}";
            if($endsWithDoubleQuote) $value = "{$value}\"";
            if($fieldname != 'hdnTaxType' && ($uitype == 15 || $uitype == 16 || $uitype == 33)){
                if(empty($this->picklistValues[$fieldname])){
                    $this->picklistValues[$fieldname] = $this->fieldArray[$fieldname]->getPicklistValues();
                }
                // If the value being exported is accessible to current user
                // or the picklist is multiselect type.
                if($uitype == 33 || $uitype == 16 || array_key_exists($value,$this->picklistValues[$fieldname])){
                    // NOTE: multipicklist (uitype=33) values will be concatenated with |# delim
                    $value = trim($value);
                } else {
                    $value = '';
                }
            } elseif($uitype == 52 || $type == 'owner') {
                //$value = Vtiger_Util_Helper::getOwnerName($value);
                $value = getUserFullName($value);
            }elseif($type == 'reference'){
                $value = trim($value);
                if(!empty($value)) {
                    $parent_module = getSalesEntityType($value);
                    $displayValueArray = getEntityName($parent_module, $value);
                    if(!empty($displayValueArray)){
                        foreach($displayValueArray as $k=>$v){
                            $displayValue = $v;
                        }
                    }
                    if(!empty($parent_module) && !empty($displayValue)){
                        $value = $parent_module."::::".$displayValue;
                    }else{
                        $value = "";
                    }
                } else {
                    $value = '';
                }
            } elseif($uitype == 72 || $uitype == 71) {
                $value = CurrencyField::convertToUserFormat($value, null, true, true);
            } elseif($uitype == 7 && $fieldInfo->get('typeofdata') == 'N~O' || $uitype == 9){
                $value = decimalFormat($value);
            } elseif($type == 'date') {
                if ($value && $value != '0000-00-00') {
                    $value = DateTimeField::convertToUserFormat($value);
                }
            } elseif($type == 'datetime') {
                if ($moduleName == 'Calendar' && in_array($fieldName, array('date_start', 'due_date'))) {
                    $timeField = 'time_start';
                    if ($fieldName === 'due_date') {
                        $timeField = 'time_end';
                    }
                    $value = $value.' '.$arr[$timeField];
                }
                if (trim($value) && $value != '0000-00-00 00:00:00') {
                    $value = Vtiger_Datetime_UIType::getDisplayDateTimeValue($value);
                }
            }
            if($moduleName == 'Documents' && $fieldname == 'description'){
                $value = strip_tags($value);
                $value = str_replace('&nbsp;','',$value);
                array_push($new_arr,$value);
            }
           
        }
       
        return $arr;
    }
    
    public function moduleFieldInstances($moduleName) {
       
        if($moduleName == 'Connection'){
            $fields = $this->moduleInstance->getFields();
            $contactModule = Vtiger_Module_Model::getInstance('Contacts');
            $contactFields = $contactModule->getFields();
            return array_merge($contactFields,$fields);
        }
            
        return $this->moduleInstance->getFields();
    }
    
    public function getRelatedNos($record_id) {
        
        global $adb;
        
        $relatedNo = array();
        
        if($record_id){
            
            $relatedIds = $adb->pquery("SELECT vtiger_seringcentralrel.to_number FROM vtiger_seringcentralrel
            INNER JOIN vtiger_crmentity crm1 ON crm1.crmid = vtiger_seringcentralrel.crmid
            INNER JOIN vtiger_crmentity crm2 ON crm2.crmid = vtiger_seringcentralrel.ringcentralid
            WHERE crm1.deleted = 0 AND crm2.deleted = 0 AND vtiger_seringcentralrel.ringcentralid = ?",array($record_id));
            
            if($adb->num_rows($relatedIds)){
                
                for($r=0;$r<$adb->num_rows($relatedIds);$r++){
                    $relatedNo[] = $adb->query_result($relatedIds,$r,'to_number');
                    
                }
                
            }
            
        }
        
        return implode(' ## ',$relatedNo);
    }
    
    
}
<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ModComments_RelatedExportData_Action extends Vtiger_RelatedExportData_Action {
    
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
        
        $moduleName = $request->get('parent_module');
        $parentId = $request->get('parent_record');
        
        $relatedModuleModel = Vtiger_Module_Model::getInstance($module);
        $moduleFields = $relatedModuleModel->getFields();
        
        $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentId, $moduleName);
        
        $relationListView = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $module, $label);
        
        if(!empty($whereCondition))
            $relationListView->set('whereCondition', $whereCondition);
            
        $mode = $request->getMode();
        
        $relationListView->set('mode',$mode);
        
        $relationListView->tab_label = $request->get('tab_label');
        
        $entries = array();
        $entries = $this->getExportQuery($relationListView);
        
        $translatedHeaders = $this->getHeaders($relationListView);
        
        $this->output($request, $translatedHeaders, $entries);
        
    }
    
    public function getHeaders($relationListView) {
        $relatedColumnFields = array();
        if(!empty($this->accessibleFields)) {
            $accessiblePresenceValue = array(0,2);
            foreach($this->accessibleFields as $fieldName) {
                $fieldModel = $this->moduleFieldInstances[$fieldName];
                
                if($fieldName == 'id')continue;
                
                preg_match('/(\w+) ; \((\w+)\) (\w+)/', $fieldName, $matches);
                if(count($matches) > 0) {
                    list($full, $referenceParentField, $referenceModule, $referenceFieldName) = $matches;
                    $referenceModuleModel = Vtiger_Module_Model::getInstance($referenceModule);
                    $referenceFieldModel = Vtiger_Field_Model::getInstance($referenceFieldName, $referenceModuleModel);
                    
                    $referenceFieldModel->set('listViewRawFieldName', $referenceParentField.$referenceFieldName);
                    
                    $referenceFieldModel->set('_name', $referenceFieldName);
                    $fieldModel = $referenceFieldModel->set('name', $fieldName);
                    $matches=null;
                }
                
                // Check added as querygenerator is not checking this for admin users
                $presence = $fieldModel->get('presence');
                if(in_array($presence, $accessiblePresenceValue) && $fieldModel->get('displaytype') != '6') {
                    $relatedColumnFields[$fieldModel->get('column')] = $fieldModel->get('label');
                }
            }
        }else{
            
            $parentModule = $relationListView->getParentRecordModel()->getModule();
            $relationModule = $relationListView->getRelationModel()->getRelationModuleModel();
            $relationModuleName = $relationModule->get('name');
            
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
            
        }
        
        $translatedHeaders = array();
        foreach($headers as $header) {
            $translatedHeaders[] = vtranslate(html_entity_decode($header, ENT_QUOTES), $this->moduleInstance->getName());
        }
        
        $translatedHeaders = array_map('decode_html', $relatedColumnFields);
        
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
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $mode = $relationListView->get('mode');
        $db = PearDatabase::getInstance();
        
        $parentModule = $relationListView->getParentRecordModel()->getModule();
        $relationModule = $relationListView->getRelationModel()->getRelationModuleModel();
        $relationModuleName = $relationModule->get('name');
        
        $customView = new CustomView();
        $cv_id = $customView->getViewId($relationModuleName);
        
        $recordId = $relationListView->getParentRecordModel()->getId();
        
        $rollup = ModComments_Module_Model::getRollupSettingsForUser($currentUser,$parentModule->getName());
        
        $rollupStatus = $rollup['rollup_status'];
        
        if($parentModule->getName() =='Contacts' || $parentModule->getName() == 'Accounts'){
            $rollupStatus = 1;
        }
        
        if($rollupStatus == 1){
            
            $relatedModuleRecordIds = Vtiger_Record_Model::getCommentEnabledRelatedEntityIds($parentModule->getName(), $recordId);
            if($parentModule->getName() == 'Accounts' || $parentModule->getName() == 'Contacts'){
                if($parentModule->getName() == 'Accounts'){
                    $account = CRMEntity::getInstance($parentModule->getName());
                    $contacts = $account->getRelatedContactsIds($recordId);
                    array_push($contacts, $recordId);
                    $ticketId = $account->getRelatedTicketIds($contacts);
                    $relatedModuleRecordIds = array_merge($relatedModuleRecordIds,$ticketId);
                }
                $portfolioIds = Vtiger_Record_Model::getRelatedPortfolioIds($parentModule->getName(),$recordId);
                $relatedModuleRecordIds = array_merge($relatedModuleRecordIds,$portfolioIds);
            }
            array_unshift($relatedModuleRecordIds, $recordId);
            
            if ($relatedModuleRecordIds) {
                
                $listView = Vtiger_ListView_Model::getInstance('ModComments');
                $queryGenerator = $listView->get('query_generator');
                $queryGenerator->initForCustomViewById($cv_id);
                $db = PearDatabase::getInstance();
                $listviewController = new ListViewController($db, $currentUser, $queryGenerator);
                $fields = array_keys($listviewController->getListViewHeaderFields());
                
                array_push($fields, 'id');
                
                $queryGenerator->setFields($fields);
                
                $this->accessibleFields = $queryGenerator->getFields();
                
                $query = $queryGenerator->getQuery();
                
                $query .= " AND vtiger_modcomments.related_to IN (" . implode(', ',$relatedModuleRecordIds)
                . ") AND vtiger_modcomments.parent_comments=0 ORDER BY vtiger_crmentity.createdtime DESC  ";
                
            }
            
        }else{
        
            $query = $relationListView->getRelationQuery();
            
            $queryGenerator = new EnhancedQueryGenerator($relationModuleName, $currentUser);
            $queryGenerator->initForCustomViewById($cv_id);
            $meta = $queryGenerator->getMeta($relationModuleName);
            $entityTableName = $meta->getEntityBaseTable();
            $moduleTableIndexList = $meta->getEntityTableIndexList();
            $baseTableIndex = $moduleTableIndexList[$entityTableName];
            
            $orderBy = $relationListView->getForSql('orderby');
            $sortOrder = $relationListView->getForSql('sortorder');
            
            $db = PearDatabase::getInstance();
            $listviewController = new ListViewController($db, $currentUser, $queryGenerator);
            $fields = array_keys($listviewController->getListViewHeaderFields());
            
            array_push($fields, 'id');
            
            $queryGenerator->setFields($fields);
            
            $this->accessibleFields = $queryGenerator->getFields();
            
            $listQuery = $queryGenerator->getQuery();
            
            $queryComponents = preg_split('/ WHERE /i', $listQuery);
            
            $relatedQuery = preg_split('/ WHERE /i', $query);
            
            $query = $queryComponents[0].' WHERE '.$relatedQuery[1];
            
            
        }
        
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId);
        if($recordModel->getModuleName() == 'HelpDesk'){
            
            global $current_user;
            $tabId = getTabid('ModComments');
            $creatorId = $recordModel->get('creator');
            $ownerId = $recordModel->get('assigned_user_id');
            $financialAdvisor = $recordModel->get('financial_advisor');
            
            $permission_result = $db->pquery("select * from vtiger_ticket_view_permission where ticketid = ?",array($parentRecordId));
            $viewUsers = array();
            if($db->num_rows($permission_result)){
                for($h=0;$h<$db->num_rows($permission_result);$h++){
                    $viewUsers[] = $db->query_result($permission_result, $h, 'view_permission_id');
                }
            }
            
            
            if( $creatorId == $current_user->id || $financialAdvisor == $current_user->id || $ownerId == $current_user->id || in_array($current_user->id, $viewUsers)){
                $tableName = 'vt_tmp_u' . $current_user->id . '_t' . $tabId;
                if(strpos($query,$tableName) !== FALSE){
                    $tableName = $tableName;
                }else{
                    $tableName = 'vt_tmp_u' . $current_user->id;
                }
                
                $db->pquery("delete from $tableName");
                $db->pquery("insert into $tableName select id from vtiger_users");
            }
        }
        
        $result = $db->pquery($query,array());
        
        $listViewEntries =  $listviewController->getListViewRecordsExport($moduleFocus,$moduleName, $result);
        
        return $listViewEntries;
        //return $query;
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
                $row[$key] = str_replace('"', '""', strip_tags($value));
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
    
    
    public function moduleFieldInstances($moduleName) {
        
        if($moduleName == 'Connection'){
            $fields = $this->moduleInstance->getFields();
            $contactModule = Vtiger_Module_Model::getInstance('Contacts');
            $contactFields = $contactModule->getFields();
            return array_merge($contactFields,$fields);
        }
        
        return $this->moduleInstance->getFields();
    }
    
    
}
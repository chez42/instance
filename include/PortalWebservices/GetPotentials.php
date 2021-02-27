<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

function vtws_get_potentials($element, $user){
    
    global $log, $adb;
    
    $id = $element['id'];
    
    $moduleName = 'Potentials';
    $customModel = CustomView_Record_Model::getAllFilterByModule($moduleName);
    $cvId = $customModel->getId();
    
    $listViewModel = Vtiger_ListView_Model::getInstance($moduleName, $cvId);
    
    $listViewHeaders = $listViewModel->getListViewHeaders();
    
    $listHeaders = array();
    
    $searchParams = array();
    
    foreach ($listViewHeaders as $key => $field){
        
        $listHeaders[$field->getName()] = array(
            'label'=> vtranslate($field->get('label'), $field->getModuleName()),
            'columnname' => $field->get('column'),
            'fieldname' => $field->get('name'),
            'datatype'=> $field->getFieldDataType(),
            'picklistVlaue' => $field->getPicklistValues()
        );
        $headers[] = $field->getName();
        $operator = 'c';
        if ($field->getFieldDataType() == "date" || $field->getFieldDataType() == "datetime") {
            if($element[$field->get('name')]){
                $element[$field->get('name')] = $element[$field->get('name')].','.$element[$field->get('name')];
            }
            $operator = 'bw';
        } else if ($field->getFieldDataType() == 'percentage' || $field->getFieldDataType() == "double" || $field->getFieldDataType() == "integer"
            || $field->getFieldDataType() == 'currency' || $field->getFieldDataType() == "number" || $field->getFieldDataType() == "boolean" ||
            $field->getFieldDataType() == "picklist") {
                $operator = 'e';
        }
        
        if($element[$field->get('name')]){
            $search[] = array($field->get('name'), $operator, $element[$field->get('name')]);
        }
    }
    
	if(!empty($search)){
		array_push($searchParams,$search);
    }
	
    if($element['mode'] == 'headers'){
        
        return $listHeaders;
        
    }else{
        
        $queryGenerator = new EnhancedQueryGenerator($moduleName, $user);
        $queryGenerator->initForCustomViewById($cvId);
        $fieldsList = $queryGenerator->getFields();
       
        if(!empty($headers) && is_array($headers) && count($headers) > 0) {
            $fieldsList = $headers;
            $fieldsList[] = 'id';
        }
        $queryGenerator->setFields($fieldsList);
        
        if(!empty($searchParams)){
            $searchParams = Vtiger_Util_Helper::transferListSearchParamsToFilterCondition($searchParams, $listViewModel->getModule());
        }
        
        $glue = "";
        if(count($queryGenerator->getWhereFields()) > 0 && (count($searchParams)) > 0) {
            $glue = QueryGenerator::$AND;
        }
        $queryGenerator->parseAdvFilterList($searchParams, $glue);
        
        $listQuery = $queryGenerator->getQuery();
        
        
        
        $permission_result = $adb->pquery("SELECT * FROM `vtiger_contact_portal_permissions` inner join
    	vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_contact_portal_permissions.crmid
    	where crmid = ?", array($id));
        
        $potential_across_org = 0;
        
        $contact_ids = array();
        
        $contact_ids[] = $id;
        
        if($adb->num_rows($permission_result)){
            $potential_across_org = $adb->query_result($permission_result, 0, "potentials_record_across_org");
            $account_id = $adb->query_result($permission_result, 0, "accountid");
            if($account_id && $potential_across_org){
                $contact_ids[] = $account_id;
                $contact_result = $adb->pquery("SELECT * FROM `vtiger_contactdetails`
    			inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.contactid
    			where accountid = ? and deleted = 0", array($account_id));
                for($i = 0; $i < $adb->num_rows($contact_result); $i++){
                    $contact_ids[] = $adb->query_result($contact_result, $i, "contactid");
                }
            }
        }
        
        $pageLimit = $element['pageLimit'];
        
        $startIndex = $element['startIndex'];
        
        if($startIndex == ''){
            $startIndex = 0;
        }
        
        
        $potentials = array();
        
        $params = array();
        
        $count = 0;
        
        $sql = $listQuery." AND vtiger_potential.contact_id IN ('" . implode("','", $contact_ids) . "') ";
       
        $sql .=" ORDER BY vtiger_crmentity.modifiedtime DESC ";
        
        $result = $adb->pquery($sql, $params);
        
        $count = $adb->num_rows($result);
        
        $potentialIds = array();
        
        if($count){
            
            for($ti=0;$ti<$adb->num_rows($result);$ti++){
                $potentialIds[] = vtws_getWebserviceEntityId($moduleName, $adb->query_result($result, $ti, 'potentialid'));
            }
            
            $sql = $listQuery." AND vtiger_potential.contact_id IN ('" . implode("','", $contact_ids) . "')";
            
            $sql .=" ORDER BY vtiger_crmentity.modifiedtime DESC LIMIT {$startIndex},{$pageLimit}";
            
            $result = $adb->pquery($sql, $params);
            
            $listViewContoller = new ListViewController($adb, $user, $queryGenerator);
            
            $moduleFocus = CRMEntity::getInstance($moduleName);
            
            $potentials =  $listViewContoller->getListViewRecords($moduleFocus,$moduleName, $result);
            
        }
        
        //echo"<pre>";print_r($potentials);echo"</pre>";
        
        $entity = $adb->pquery("SELECT * FROM vtiger_ws_entity WHERE name = ?",array('Potentials'));
        
        $entityId = '';
        if($adb->num_rows($entity)){
            $entityId = $adb->query_result($entity, 0, 'id');
        }
        
        return array("data" => $potentials, "count" => $count, 'potential_ids'=>$potentialIds, "entityid" => $entityId);
    
    }
}
?>
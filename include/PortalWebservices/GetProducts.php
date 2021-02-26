<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

function vtws_get_products($element, $user){
    
    global $log, $adb;
    
    $id = $element['id'];
    
    $moduleName = 'Products';
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
    
    array_push($searchParams,$search);
    
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
        
        $pageLimit = $element['pageLimit'];
        
        $startIndex = $element['startIndex'];
        
        if($startIndex == ''){
            $startIndex = 0;
        }
        
        
        $products = array();
        
        $params = array();
        
        $count = 0;
        
        $sql = $listQuery;
        
        $sql .=" ORDER BY vtiger_crmentity.modifiedtime DESC ";
        
        $result = $adb->pquery($sql, $params);
        
        $count = $adb->num_rows($result);
        
        $potentialIds = array();
        
        if($count){
            
            for($ti=0;$ti<$adb->num_rows($result);$ti++){
                $productIds[] = vtws_getWebserviceEntityId($moduleName, $adb->query_result($result, $ti, 'potentialid'));
            }
            
            $sql = $listQuery;
            
            $sql .=" ORDER BY vtiger_crmentity.modifiedtime DESC LIMIT {$startIndex},{$pageLimit}";
            
            $result = $adb->pquery($sql, $params);
            
            $listViewContoller = new ListViewController($adb, $user, $queryGenerator);
            
            $moduleFocus = CRMEntity::getInstance($moduleName);
            
            $products =  $listViewContoller->getListViewRecords($moduleFocus,$moduleName, $result);
            
        }
        
        $entity = $adb->pquery("SELECT * FROM vtiger_ws_entity WHERE name = ?",array('Products'));
        
        $entityId = '';
        if($adb->num_rows($entity)){
            $entityId = $adb->query_result($entity, 0, 'id');
        }
        
        return array("data" => $products, "count" => $count, 'products_ids'=>$productIds, "entityid" => $entityId);
        
    }
}
?>
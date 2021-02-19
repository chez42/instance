<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Notifications_ListView_Model extends Vtiger_ListView_Model {

	
	/**
	 * Function to get the list of Mass actions for the module
	 * @param <Array> $linkParams
	 * @return <Array> - empty array
	 */
	public function getListViewMassActions($linkParams) {
		$currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$moduleModel = $this->getModule();

		$linkTypes = array('LISTVIEWMASSACTION');
		$links = Vtiger_Link_Model::getAllByType($moduleModel->getId(), $linkTypes, $linkParams);


		if($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'Delete')) {
			$massActionLinks[] = array(
				'linktype' => 'LISTVIEWMASSACTION',
				'linklabel' => 'LBL_DELETE',
				'linkurl' => 'javascript:Vtiger_List_Js.massDeleteRecords("index.php?module='.$moduleModel->get('name').'&action=MassDelete");',
				'linkicon' => ''
			);
		}

		foreach($massActionLinks as $massActionLink) {
			$links['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
		}

		return $links;
	}
	
	public function getListViewHeaders() {
	    $listViewContoller = $this->get('listview_controller');
	    $module = $this->getModule();
	    global $adb;
	    
	    $notificationFields = $adb->pquery("SELECT * FROM vtiger_field WHERE tabid =?",
	        array($module->getId()));
	   
	    if($adb->num_rows($notificationFields)){
	        for($i=0;$i<$adb->num_rows($notificationFields);$i++){
	            $noti_fields[] = $adb->query_result($notificationFields, $i, 'fieldname');
	        }
	    }
	    
	    $fields = $this->get('query_generator')->getFields();
	    
	    $noti_fields = array_unique(array_merge($noti_fields,$fields));
	    
	    $this->get('query_generator')->setFields($noti_fields);
	   
	    $moduleFields = $this->get('query_generator')->getModuleFields();
	    
	    $fields = $this->get('query_generator')->getFields();
	    
	    $headerFields = array();
	    foreach($fields as $fieldName) {
	        if(array_key_exists($fieldName, $moduleFields)) {
	            $headerFields[$fieldName] = $moduleFields[$fieldName];
	        }
	    }
	    $headerFieldModels = array();
	    $headerFields = $listViewContoller->getListViewHeaderFields();
	    foreach($headerFields as $fieldName => $webserviceField) {
	        if($webserviceField && !in_array($webserviceField->getPresence(), array(0,2))) continue;
	        if($webserviceField && $webserviceField->parentReferenceField && !in_array($webserviceField->parentReferenceField->getPresence(), array(0,2))){
	            continue;
	        }
	        if($webserviceField->getDisplayType() == '6') continue;
	        // check if the field is reference field
	        preg_match('/(\w+) ; \((\w+)\) (\w+)/', $fieldName, $matches);
	        if(count($matches) > 0) {
	            list($full, $referenceParentField, $referenceModule, $referenceFieldName) = $matches;
	            $referenceModuleModel = Vtiger_Module_Model::getInstance($referenceModule);
	            $referenceFieldModel = Vtiger_Field_Model::getInstance($referenceFieldName, $referenceModuleModel);
	            $referenceFieldModel->set('webserviceField', $webserviceField);
	            // added tp use in list view to see the title, for reference field rawdata key is different than the actual field
	            // eg: in rawdata its account_idcf_2342 (raw column name used in querygenerator), actual field name (account_id ;(Accounts) cf_2342)
	            // When generating the title we use rawdata and from field model we have no way to find querygenrator raw column name.
	            
	            $referenceFieldModel->set('listViewRawFieldName', $referenceParentField.$referenceFieldName);
	            
	            // this is added for picklist colorizer (picklistColorMap.tpl), for fetching picklist colors we need the actual field name of the picklist
	            $referenceFieldModel->set('_name', $referenceFieldName);
	            $headerFieldModels[$fieldName] = $referenceFieldModel->set('name', $fieldName); // resetting the fieldname as we use it to fetch the value from that name
	            $matches=null;
	        } else {
	            $fieldInstance = Vtiger_Field_Model::getInstance($fieldName,$module);
	            $fieldInstance->set('listViewRawFieldName', $fieldInstance->get('column'));
	            $headerFieldModels[$fieldName] = $fieldInstance;
	        }
	    }
	    return $headerFieldModels;
	}
	
	function getListViewEntries($pagingModel) {
	    global $current_user;
	    $db = PearDatabase::getInstance();
	    
	    $moduleName = $this->getModule()->get('name');
	    $moduleFocus = CRMEntity::getInstance($moduleName);
	    $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
	    
	    $queryGenerator = $this->get('query_generator');
	    $listViewContoller = $this->get('listview_controller');
	    
	    $searchParams = $this->get('search_params');
	    if(empty($searchParams)) {
	        $searchParams = array();
	    }
	    $glue = "";
	    if(count($queryGenerator->getWhereFields()) > 0 && (count($searchParams)) > 0) {
	        $glue = QueryGenerator::$AND;
	    }
	    $queryGenerator->parseAdvFilterList($searchParams, $glue);
	    
	    $searchKey = $this->get('search_key');
	    $searchValue = $this->get('search_value');
	    $operator = $this->get('operator');
	    if(!empty($searchKey)) {
	        $queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator));
	    }
	    
	    //$orderBy = $this->get('orderby');
	    //$sortOrder = $this->get('sortorder');
	    
	    $orderBy = $this->getForSql('orderby');
	    $sortOrder = $this->getForSql('sortorder');
	    
	    if(!empty($orderBy)){
	        $queryGenerator = $this->get('query_generator');
	        $fieldModels = $queryGenerator->getModuleFields();
	        $orderByFieldModel = $fieldModels[$orderBy];
	        if($orderByFieldModel && ($orderByFieldModel->getFieldDataType() == Vtiger_Field_Model::REFERENCE_TYPE ||
	            $orderByFieldModel->getFieldDataType() == Vtiger_Field_Model::OWNER_TYPE)){
	                $queryGenerator->addWhereField($orderBy);
	        }
	    }
	    $listQuery = $this->getQuery();
	    
	    if($this->get('notificationtype') == 'comments'){
	        $query = explode('WHERE', $listQuery);
	        $listQuery = $query[0];
	        $listQuery .= " INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_notifications.related_record ";
	        $listQuery .= " WHERE ". $query[1] . " AND vtiger_crmentity.setype='ModComments' ";
	    }else if($this->get('notificationtype') == 'events'){
	        $query = explode('WHERE', $listQuery);
	        $listQuery = $query[0];
	        $listQuery .= " INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_notifications.related_to  ";
	        $listQuery .= " WHERE ". $query[1] . " AND vtiger_crmentity.setype='Calendar' ";
	    }else if($this->get('notificationtype') == 'followup'){
	        $listQuery .= " AND vtiger_notifications.notification_type = 'Follow Record' ";
	    }
	    
	    $sourceModule = $this->get('src_module');
	    if(!empty($sourceModule)) {
	        if(method_exists($moduleModel, 'getQueryByModuleField')) {
	            $overrideQuery = $moduleModel->getQueryByModuleField($sourceModule, $this->get('src_field'), $this->get('src_record'), $listQuery,$this->get('relationId'));
	            if(!empty($overrideQuery)) {
	                $listQuery = $overrideQuery;
	            }
	        }
	    }
	    
	    $startIndex = $pagingModel->getStartIndex();
	    $pageLimit = $pagingModel->getPageLimit();
	    
	    if(!empty($orderBy) && $orderByFieldModel) {
	        if($orderBy == 'roleid' && $moduleName == 'Users'){
	            $listQuery .= ' ORDER BY vtiger_role.rolename '.' '. $sortOrder;
	        } else {
	            $listQuery .= ' ORDER BY '.$queryGenerator->getOrderByColumn($orderBy).' '.$sortOrder;
	        }
	        
	        if ($orderBy == 'first_name' && $moduleName == 'Users') {
	            $listQuery .= ' , last_name '.' '. $sortOrder .' ,  email1 '. ' '. $sortOrder;
	        }
	    } else if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
	        //List view will be displayed on recently created/modified records
	        $listQuery .= ' ORDER BY vtiger_notifications.createdtime DESC';
	    }
	    
	    $viewid = ListViewSession::getCurrentView($moduleName);
	    if(empty($viewid)) {
	        $viewid = $pagingModel->get('viewid');
	    }
	    $_SESSION['lvs'][$moduleName][$viewid]['start'] = $pagingModel->get('page');
	    
	    ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);
	    
	    $listQuery .= " LIMIT $startIndex,".($pageLimit+1);
	   
	    $listResult = $db->pquery($listQuery, array());
	    
	    $listViewRecordModels = array();
	    $listViewEntries =  $listViewContoller->getListViewRecords($moduleFocus,$moduleName, $listResult);
	    
	    $pagingModel->calculatePageRange($listViewEntries);
	    
	    if($db->num_rows($listResult) > $pageLimit){
	        array_pop($listViewEntries);
	        $pagingModel->set('nextPageExists', true);
	    }else{
	        $pagingModel->set('nextPageExists', false);
	    }
	    
	    $index = 0;
	    foreach($listViewEntries as $recordId => $record) {
	        $rawData = $db->query_result_rowdata($listResult, $index++);
	        $record['id'] = $recordId;
	        $relatedId = $rawData["related_to"];
	        if (!$relatedId || !isRecordExists($relatedId)) {
	            continue;
	        }
	        $relatedRecordModel = Vtiger_Record_Model::getInstanceById($relatedId);
	        
	        $createdDate = $rawData["createdtime"];
	        
	        $relatedModule = '';
	        if(getSalesEntityType($rawData['related_record']) == 'Documents'){
	            $docRecord = Vtiger_Record_Model::getInstanceById($rawData['related_record']);
	            $detailUrl = $docRecord->getDetailViewUrl();
	            $relatedModule = 'Documents';
	        }else if(getSalesEntityType($rawData['related_record']) == 'ModComments'){
	            $detailUrl = $relatedRecordModel->getDetailViewUrl();
	            $detailUrl .= '&relatedModule=ModComments&mode=showRelatedList&tab_label=ModComments';
	            $relatedModule = 'ModComments';
	        }else if($rawData['related_to']){
	            $detailUrl = $relatedRecordModel->getDetailViewUrl();
	            $relatedModule = getSalesEntityType($rawData['related_to']);
	        }
	        
	        $accepted = false;
	        if(getSalesEntityType($relatedId) == 'Contacts'){
	            $fullName = $relatedRecordModel->get('firstname').' '.$relatedRecordModel->get('lastname');
	            $relatedToModule = 'Contacts';
	        }else if(getSalesEntityType($relatedId) == 'HelpDesk'){
	            $fullName = $relatedRecordModel->get('ticket_title');
	            $relatedToModule = 'HelpDesk';
	        }else if(getSalesEntityType($relatedId) == 'Calendar'){
	            $fullName = $relatedRecordModel->get('subject');
	            $relatedToModule = 'Events';
	            $eveRecord = Vtiger_Record_Model::getInstanceById($relatedId);
	            $detailUrl = $eveRecord->getDetailViewUrl();
	            $relatedModule = 'Events';
	            global $adb;
	            $eventQuery = $adb->pquery("SELECT * FROM vtiger_invitees WHERE activityid = ? AND inviteeid = ? AND  (status != 'accepted' AND status != 'rejected' )",
	                array($relatedId, $current_user->id));
	            
	            if(!$adb->num_rows($eventQuery)){
	                $accepted = true;
	            }
	        }
	        
	        $items = array("id" => $rawData["notificationsid"], "notificationno" => $rawData["notificationno"],
	            "description" => $rawData['notification_type'] != 'Follow Record' ? html_entity_decode($rawData["description"]) : 'N/A',
	            "thumbnail" => "layouts/vlayout/skins/images/summary_Leads.png",
	            "createdtime" => $createdDate, "full_name" => $fullName, "link" => $detailUrl,
	            "rel_id" => $relatedId, "relatedModule" => $relatedModule, "relatedRecord"=>$rawData['related_record'],
	            "relatedToModule" => $relatedToModule, "accepted" => $accepted, "title"=>$rawData['title'], "type"=>$rawData['notification_type']);
	        
	        $listViewRecordModels[$recordId] = $items;
	    }
	    
	    return $listViewRecordModels;
	    
	}
	
	function getQuery() {
	    global $current_user;
	    $query = parent::getQuery();
	    $query = str_replace("AND vtiger_notifications.notificationsid > 0", " ", $query);
	    $query = str_replace("vtiger_crmentity.deleted", "vtiger_notifications.deleted", $query);
	    $query .= ' AND vtiger_notifications.smownerid='.$current_user->id;
	    $query .= ' AND (vtiger_notifications.source != "PORTAL" OR vtiger_notifications.source IS NULL) ';
	    
	    return $query;
	}

}

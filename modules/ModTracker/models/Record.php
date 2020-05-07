<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class ModTracker_Record_Model extends Vtiger_Record_Model {

	const UPDATE = 0;
	const DELETE = 1;
	const CREATE = 2;
	const RESTORE = 3;
	const LINK = 4;
	const UNLINK = 5;

	/**
	 * Function to get the history of updates on a record
	 * @param <type> $record - Record model
	 * @param <type> $limit - number of latest changes that need to retrieved
	 * @return <array> - list of  ModTracker_Record_Model
	 */
	public static function getUpdates($parentRecordId, $pagingModel,$moduleName) {
		if($moduleName == 'Calendar') {
			if(getActivityType($parentRecordId) != 'Task') {
				$moduleName = 'Events';
			}
		}
		$db = PearDatabase::getInstance();
		$recordInstances = array();

		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();

		$listQuery = "SELECT * FROM vtiger_modtracker_basic WHERE crmid = ? AND module = ? ".
						" ORDER BY changedon DESC LIMIT $startIndex, ".($pageLimit+1);

		$result = $db->pquery($listQuery, array($parentRecordId, $moduleName));
		$rows = $db->num_rows($result);

		for ($i=0; $i<$rows; $i++) {
			$row = $db->query_result_rowdata($result, $i);
			$recordInstance = new self();
			$recordInstance->setData($row)->setParent($row['crmid'], $row['module']);
			$recordInstances[] = $recordInstance;
		}
		return $recordInstances;
	}

	function setParent($id, $moduleName) {
		if(!Vtiger_Util_Helper::checkRecordExistance($id)) {
			$this->parent = Vtiger_Record_Model::getInstanceById($id, $moduleName);
		} else {
			$this->parent = Vtiger_Record_Model::getCleanInstance($moduleName);
			$this->parent->id = $id;
			$this->parent->setId($id);
		}
	}

	function getParent() {
		return $this->parent;
	}

	function checkStatus($callerStatus) {
		$status = $this->get('status');
		if ($status == $callerStatus) {
			return true;
		}
		return false;
	}

	function isCreate() {
		return $this->checkStatus(self::CREATE);
	}

	function isUpdate() {
		return $this->checkStatus(self::UPDATE);
	}

	function isDelete() {
		return $this->checkStatus(self::DELETE);
	}

	function isRestore() {
		return $this->checkStatus(self::RESTORE);
	}

	function isRelationLink() {
		return $this->checkStatus(self::LINK);
	}

	function isRelationUnLink() {
		return $this->checkStatus(self::UNLINK);
	}

	function getModifiedBy() {
		$changeUserId = $this->get('whodid');
		return Users_Record_Model::getInstanceById($changeUserId, 'Users');
	}

	function getActivityTime() {
		return $this->get('changedon');
	}

	function getFieldInstances() {
		$id = $this->get('id');
		$db = PearDatabase::getInstance();

		$fieldInstances = array();
		if($this->isCreate() || $this->isUpdate()) {
			$result = $db->pquery('SELECT * FROM vtiger_modtracker_detail WHERE id = ?', array($id));
			$rows = $db->num_rows($result);
			for($i=0; $i<$rows; $i++) {
				$data = $db->query_result_rowdata($result, $i);
				$row = array_map('decode_html', $data);

				if($row['fieldname'] == 'record_id' || $row['fieldname'] == 'record_module') continue;

				$fieldModel = Vtiger_Field_Model::getInstance($row['fieldname'], $this->getParent()->getModule());
				if(!$fieldModel) continue;
				
				$fieldInstance = new ModTracker_Field_Model();
				$fieldInstance->setData($row)->setParent($this)->setFieldInstance($fieldModel);
				$fieldInstances[] = $fieldInstance;
			}
		}
		return $fieldInstances;
	}

	function getRelationInstance() {
		$id = $this->get('id');
		$db = PearDatabase::getInstance();

		if($this->isRelationLink() || $this->isRelationUnLink()) {
			$result = $db->pquery('SELECT * FROM vtiger_modtracker_relations WHERE id = ?', array($id));
			$row = $db->query_result_rowdata($result, 0);
			$relationInstance = new ModTracker_Relation_Model();
			$relationInstance->setData($row)->setParent($this);
		}
		return $relationInstance;
	}
        
	public function getTotalRecordCount($recordId) {
    	$db = PearDatabase::getInstance();
        $result = $db->pquery("SELECT COUNT(*) AS count FROM vtiger_modtracker_basic WHERE crmid = ?", array($recordId));
        return $db->query_result($result, 0, 'count');
    }
    
    /**
     * Function to get the history of updates on a record
     * @param <type> $record - Record model
     * @param <type> $limit - number of latest changes that need to retrieved
     * @return <array> - list of  ModTracker_Record_Model
     */
    public static function getJournalUpdates($parentRecordId, $pagingModel, $moduleName, $searchParams) {
        if($moduleName == 'Calendar') {
            if(getActivityType($parentRecordId) != 'Task') {
                $moduleName = 'Events';
            }
        }
        $db = PearDatabase::getInstance();
        
        $recordInstances = array();
        
        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();
        
        $listQuery = ' ';
        
        $dateQuery = '';
        
        if($searchParams['createddate']){
            $dateCreate = explode(',',$searchParams['createddate']['searchValue']);
            
            if(!empty($dateCreate)){
                $startDate = DateTimeField::convertToDBFormat($dateCreate[0]).' 00:00:00';
                $endDate = DateTimeField::convertToDBFormat($dateCreate[1]).' 23:59:59';
                $dateQuery = " AND ( vtiger_crmentity.createdtime BETWEEN '".$startDate."' AND '".$endDate."') ";
            }
            
        }
        
        $moduleQuery = '';
        
        if($searchParams['module']){
            $searchModule = explode(',',$searchParams['module']['searchValue']);
            if(!empty($searchModule)){
                $comma_separated = implode("','", $searchModule);
                $comma_separated = "'".$comma_separated."'";
                $moduleQuery = " AND vtiger_crmentity.setype IN (".$comma_separated.") ";
            }
        }
        
        $searchSubject = '';
        
        if($searchParams['subject']){
            $searchSubject = $searchParams['subject']['searchValue'];
        }
        
        $searchDescription = '';
        
        if($searchParams['description']){
            $searchDescription = $searchParams['description']['searchValue'];
        }
        
        $creatorQuery = '';
        
        if($searchParams['creator']){
            $searchCreator = explode(',',$searchParams['creator']['searchValue']);
            if(!empty($searchCreator)){
                $comma_separated_creator = implode("','", $searchCreator);
                $comma_separated_creator = "'".$comma_separated_creator."'";
                $creatorQuery = " AND vtiger_crmentity.smcreatorid IN (".$comma_separated_creator.") ";
            }
        }
        
        $ticketModules = array('Accounts','Contacts','Project');
        
        if(in_array($moduleName,$ticketModules)){
            
            $listQuery .= " SELECT DISTINCT vtiger_crmentity.crmid, vtiger_crmentity.createdtime as createddate, 
            vtiger_crmentity.setype as module, vtiger_troubletickets.title as subject, 
            vtiger_troubletickets.status as status, vtiger_crmentity.description as description,
            vtiger_crmentity.smcreatorid as creator
            FROM vtiger_troubletickets
            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_troubletickets.ticketid
            WHERE vtiger_crmentity.deleted = 0 ";
            
            if($dateQuery)
                $listQuery .=  $dateQuery;
                
            if($moduleQuery)
                $listQuery .=  $moduleQuery;
            
            if($searchSubject){
                $listQuery .= " AND vtiger_troubletickets.title LIKE '%$searchSubject%' ";
            }
            
            if($searchDescription){
                $listQuery .= " AND vtiger_crmentity.description LIKE '%$searchDescription%' ";
            }
            
            if($creatorQuery)
                $listQuery .= $creatorQuery;
                
            if( $moduleName == 'Accounts' || $moduleName == 'Contacts' ){
                if($moduleName == 'Accounts'){
                    $acc = CRMEntity::getInstance($moduleName);
                    $contacts = $acc->getRelatedContactsIds($parentRecordId);
                    array_push($contacts, $parentRecordId);
                    $entityTickets = implode(',',$contacts);
                    $listQuery .= "  AND vtiger_troubletickets.parent_id IN (".$entityTickets.") ";
                }else{
                    $listQuery .= "  AND vtiger_troubletickets.parent_id = ".$parentRecordId." ";
                }
            }elseif($moduleName == 'Project'){
                $listQuery .= "  AND vtiger_troubletickets.project_id = ".$parentRecordId." ";
            }
                
            $listQuery .= " UNION ";
            
        }
            
        $listQuery .= " SELECT DISTINCT vtiger_crmentity.crmid, vtiger_crmentity.createdtime as createddate, 
        vtiger_crmentity.setype as module, vtiger_modcomments.commentcontent as subject, 
        vtiger_crmentity.status as status, vtiger_modcomments.commentcontent as description,
        vtiger_crmentity.smcreatorid as creator 
        FROM vtiger_modcomments 
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_modcomments.modcommentsid
        WHERE vtiger_crmentity.deleted = 0 ";
        if($moduleName == 'Accounts'){
            $acc = CRMEntity::getInstance($moduleName);
            $entityCom = $acc->getRelatedContactsIds($parentRecordId);
            array_push($entityCom, $parentRecordId);
            $ticket_ids = $acc->getRelatedTicketIds($entityCom);
            $portfolio_ids = $acc->getRelatedPortfolioIds($parentRecordId);
            $entityCom = array_merge($entityCom, $ticket_ids, $portfolio_ids);
            $entityCom   = implode(',', $entityCom);
            $listQuery .= " AND vtiger_modcomments.related_to IN (".$entityCom.") ";
        }
        elseif($moduleName == 'Contacts'){
            $con = CRMEntity::getInstance($moduleName);
            $ticket_ids = $con->getRelatedTicketIds($parentRecordId);
            $portfolio_ids = $con->getRelatedPortfolioIds($parentRecordId);
            $entityCom = array_merge( $ticket_ids, $portfolio_ids);
            array_push($entityCom, $parentRecordId);
            $entityCom = implode(',', $entityCom);
            $listQuery .= " AND vtiger_modcomments.related_to IN (".$entityCom.") ";
        }
        else{
            $listQuery .= " AND vtiger_modcomments.related_to = ".$parentRecordId." ";
        }
        if($dateQuery)
            $listQuery .=  $dateQuery;
            
        if($moduleQuery)
            $listQuery .=  $moduleQuery;
        
        if($searchSubject){
            $listQuery .= " AND vtiger_modcomments.commentcontent LIKE '%$searchSubject%' ";
        }
        
        if($creatorQuery)
            $listQuery .= $creatorQuery;
            
        $listQuery .= " UNION ";
        
        $listQuery .= " SELECT DISTINCT vtiger_crmentity.crmid, vtiger_crmentity.createdtime as createddate, 
        vtiger_crmentity.setype as module, vtiger_crmentity.label as subject, 
        vtiger_crmentity.status as status, vtiger_crmentity.description as description,
        vtiger_crmentity.smcreatorid as creator  
        FROM vtiger_emaildetails
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_emaildetails.emailid
        INNER JOIN vtiger_seactivityrel ON vtiger_seactivityrel.activityid = vtiger_emaildetails.emailid
        WHERE vtiger_crmentity.deleted =0 ";
        if($moduleName == 'Accounts'){
            $acc = CRMEntity::getInstance($moduleName);
            $entityAccti = $acc->getRelatedContactsIds($parentRecordId);
            array_push($entityAccti, $parentRecordId);
            $ticket_ids = $acc->getRelatedTicketIds($entityAccti);
            $portfolio_ids = $acc->getRelatedPortfolioIds($parentRecordId);
            $entityAccti = array_merge($entityAccti, $ticket_ids, $portfolio_ids);
            $entityAccti = implode(',', $entityAccti);
            $listQuery .= " AND vtiger_seactivityrel.crmid IN (".$entityAccti.") ";
        }
        elseif($moduleName == 'Contacts'){
            $con = CRMEntity::getInstance($moduleName);
            $ticket_ids = $con->getRelatedTicketIds($parentRecordId);
            $portfolio_ids = $con->getRelatedPortfolioIds($parentRecordId);
            $entityAccti = array_merge( $ticket_ids, $portfolio_ids);
            array_push($entityAccti, $parentRecordId);
            $entityAccti = implode(',', $entityAccti);
            $listQuery .= " AND vtiger_seactivityrel.crmid IN (".$entityAccti.") ";
        }
        else{
            $listQuery .= " AND vtiger_seactivityrel.crmid = ".$parentRecordId." ";
        }
        
        if($dateQuery)
            $listQuery .=  $dateQuery;
            
        if($moduleQuery)
            $listQuery .=  $moduleQuery;
        
        if($searchSubject){
            $listQuery .= " AND vtiger_crmentity.label LIKE '%$searchSubject%' ";
        }
        
        if($creatorQuery)
            $listQuery .= $creatorQuery;
        
        $listQuery .= " UNION ";
        
        $listQuery .= " SELECT DISTINCT vtiger_crmentity.crmid, vtiger_crmentity.createdtime as createddate, 
        vtiger_crmentity.setype as module, vtiger_activity.subject as subject, 
        vtiger_activity.status as status, vtiger_crmentity.description as description,
        vtiger_crmentity.smcreatorid as creator  
        FROM vtiger_activity ";
        
        if( $moduleName == 'Contacts' )
            $listQuery .= " INNER JOIN vtiger_cntactivityrel ON vtiger_cntactivityrel.activityid = vtiger_activity.activityid ";
        else
            $listQuery .= " INNER JOIN vtiger_seactivityrel ON vtiger_seactivityrel.activityid = vtiger_activity.activityid ";
                
        
        $listQuery .= " INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_activity.activityid
        WHERE vtiger_crmentity.deleted =0 AND vtiger_activity.activitytype NOT IN ('Emails','Task') ";
        
        if($dateQuery)
            $listQuery .=  $dateQuery;
            
        if($moduleQuery)
            $listQuery .=  $moduleQuery;
        
        if($searchSubject){
            $listQuery .= " AND vtiger_activity.subject LIKE '%$searchSubject%' ";
        }
        
        if($searchDescription){
            $listQuery .= " AND vtiger_crmentity.description LIKE '%$searchDescription%' ";
        }
        
        if($creatorQuery)
            $listQuery .= $creatorQuery;
        
        if( $moduleName == 'Contacts' )
            $listQuery .= "    AND vtiger_cntactivityrel.contactid = ".$parentRecordId." ";
        else
            $listQuery .= "  AND vtiger_seactivityrel.crmid = ".$parentRecordId." ";
            
        
        $listQuery .= " UNION ";
        
        $listQuery .= " SELECT DISTINCT vtiger_crmentity.crmid , vtiger_crmentity.createdtime as createddate, 
        vtiger_crmentity.setype as module, vtiger_task.subject as subject, 
        vtiger_task.task_status as status, vtiger_crmentity.description as description,
        vtiger_crmentity.smcreatorid as creator 
        FROM vtiger_task
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_task.taskid
        WHERE vtiger_crmentity.deleted =0"; 
        
        if($dateQuery)
            $listQuery .=  $dateQuery;
            
        if($moduleQuery)
            $listQuery .=  $moduleQuery;
            
        if($searchSubject){
            $listQuery .= " AND vtiger_task.subject LIKE '%$searchSubject%' ";
        }
        
        if($searchDescription){
            $listQuery .= " AND vtiger_crmentity.description LIKE '%$searchDescription%' ";
        }
        
        if($creatorQuery)
            $listQuery .= $creatorQuery;
        
        if( $moduleName == 'Contacts' )
            $listQuery .= " AND vtiger_task.contact_id = ".$parentRecordId." ";
        else
            $listQuery .= "  AND vtiger_task.parent_id = ".$parentRecordId." ";
        
        $listQuery .= " UNION ";
    
        $listQuery .= " SELECT DISTINCT vtiger_crmentity.crmid, vtiger_crmentity.createdtime as createddate,
        vtiger_crmentity.setype as module, vtiger_notes.title as subject,
       	vtiger_crmentity.status as status, vtiger_crmentity.description as description,
        vtiger_crmentity.smcreatorid as creator
        FROM vtiger_notes
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_notes.notesid
        INNER JOIN vtiger_senotesrel ON vtiger_senotesrel.notesid = vtiger_notes.notesid
        WHERE vtiger_crmentity.deleted = 0 ";
        if($moduleName == 'Accounts'){
            $acc = CRMEntity::getInstance($moduleName);
            $entityDoc = $acc->getRelatedContactsIds($parentRecordId);
            array_push($entityDoc, $parentRecordId);
            $ticket_ids = $acc->getRelatedTicketIds($entityDoc);
            $portfolio_ids = $acc->getRelatedPortfolioIds($parentRecordId);
            $entityDoc = array_merge($entityDoc, $ticket_ids, $portfolio_ids);
            $entityDoc   = implode(',', $entityDoc);
            $listQuery .= " AND vtiger_senotesrel.crmid IN (".$entityDoc.") ";
        }
        elseif($moduleName == 'Contacts'){
            $con = CRMEntity::getInstance($moduleName);
            $ticket_ids = $con->getRelatedTicketIds($parentRecordId);
            $portfolio_ids = $con->getRelatedPortfolioIds($parentRecordId);
            $entityDoc = array_merge( $ticket_ids, $portfolio_ids);
            array_push($entityDoc, $parentRecordId);
            $entityDoc = implode(',', $entityDoc);
            $listQuery .= " AND vtiger_senotesrel.crmid IN (".$entityDoc.") ";
        }
        else{
            $listQuery .= " AND vtiger_senotesrel.crmid = ".$parentRecordId." ";
        }
        if($dateQuery)
            $listQuery .=  $dateQuery;
            
        if($moduleQuery)
            $listQuery .=  $moduleQuery;
            
        if($searchSubject){
            $listQuery .= " AND vtiger_notes.title LIKE '%$searchSubject%' ";
        }
        
        if($creatorQuery)
            $listQuery .= $creatorQuery;
         
        $listQuery .= " UNION ";
        
        $listQuery .= " SELECT DISTINCT vtiger_crmentity.crmid , vtiger_crmentity.createdtime as createddate,
        vtiger_crmentity.setype as module, concat(vtiger_ringcentral.direction, ' ' ,vtiger_ringcentral.ringcentral_type) as subject,
        vtiger_ringcentral.ringcentral_status as status, vtiger_crmentity.description as description,
        vtiger_crmentity.smcreatorid as creator
        FROM vtiger_ringcentral
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_ringcentral.ringcentralid
        INNER JOIN vtiger_seringcentralrel ON vtiger_seringcentralrel.ringcentralid = vtiger_ringcentral.ringcentralid
        WHERE vtiger_crmentity.deleted =0 AND vtiger_seringcentralrel.crmid = ".$parentRecordId;
        if($dateQuery)
            $listQuery .=  $dateQuery;
            
        if($moduleQuery)
            $listQuery .=  $moduleQuery;
            
        if($searchSubject){
            $listQuery .= " AND concat(vtiger_ringcentral.direction, ' ' ,vtiger_ringcentral.ringcentral_type) LIKE '%$searchSubject%' ";
        }
        
        if($searchDescription){
            $listQuery .= " AND vtiger_crmentity.description LIKE '%$searchDescription%' ";
        }
        
        if($creatorQuery)
            $listQuery .= $creatorQuery;
        
        $listQuery .=" ORDER BY createddate DESC LIMIT $startIndex, ".($pageLimit+1);
        
        $result = $db->pquery($listQuery, array());
        $rows = $db->num_rows($result);
       
        for ($i=0; $i<$rows; $i++) {
            $row = $db->query_result_rowdata($result, $i);
            $recordInstance = new self();
            $recordInstance->setData($row)->setParent($row['crmid'], $row['module']);
            $recordInstances[] = $recordInstance;
        }
        
        return $recordInstances;
    }
    
    public function getTotalJournalCount($recordId, $moduleName, $searchParams) {
        $db = PearDatabase::getInstance();
        
        $listQuery = ' ';
        
        $dateQuery = '';
        
        if($searchParams['createddate']){
            $dateCreate = explode(',',$searchParams['createddate']['searchValue']);
            
            if(!empty($dateCreate)){
                $startDate = DateTimeField::convertToDBFormat($dateCreate[0]).' 00:00:00';
                $endDate = DateTimeField::convertToDBFormat($dateCreate[1]).' 23:59:59';
                $dateQuery = " AND ( vtiger_crmentity.createdtime BETWEEN '".$startDate."' AND '".$endDate."') ";
            }
            
        }
        
        $moduleQuery = '';
        
        if($searchParams['module']){
            $searchModule = explode(',',$searchParams['module']['searchValue']);
            if(!empty($searchModule)){
                $comma_separated = implode("','", $searchModule);
                $comma_separated = "'".$comma_separated."'";
                $moduleQuery = " AND vtiger_crmentity.setype IN (".$comma_separated.") ";
            }
        }
        
        $searchSubject = '';
        
        if($searchParams['subject']){
            $searchSubject = $searchParams['subject']['searchValue'];
        }
        
        $searchDescription = '';
        
        if($searchParams['description']){
            $searchDescription = $searchParams['description']['searchValue'];
        }
        
        $creatorQuery = '';
        
        if($searchParams['creator']){
            $searchCreator = explode(',',$searchParams['creator']['searchValue']);
            if(!empty($searchCreator)){
                $comma_separated_creator = implode("','", $searchCreator);
                $comma_separated_creator = "'".$comma_separated_creator."'";
                $creatorQuery = " AND vtiger_crmentity.smcreatorid IN (".$comma_separated_creator.") ";
            }
        }
        
        $ticketModules = array('Accounts','Contacts','Project');
        
        if(in_array($moduleName,$ticketModules)){
            
            $listQuery .= " SELECT DISTINCT vtiger_crmentity.crmid, vtiger_crmentity.createdtime as createddate, 
            vtiger_crmentity.setype as module, vtiger_troubletickets.title as subject,
            vtiger_troubletickets.status as status, vtiger_crmentity.description as description
            FROM vtiger_troubletickets
            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_troubletickets.ticketid
            WHERE vtiger_crmentity.deleted = 0 ";
            
            if($dateQuery)
                $listQuery .=  $dateQuery;
            
            if($moduleQuery)    
                $listQuery .=  $moduleQuery;
               
            if($searchSubject){
                $listQuery .= " AND vtiger_troubletickets.title LIKE '%$searchSubject%' ";
            }
            
            if($searchDescription){
                $listQuery .= " AND vtiger_crmentity.description LIKE '%$searchDescription%' ";
            }
            
            if($creatorQuery)
                $listQuery .= $creatorQuery;
                
            if( $moduleName == 'Accounts' || $moduleName == 'Contacts' ){
                if($moduleName == 'Accounts'){
                    $acc = CRMEntity::getInstance($moduleName);
                    $contacts = $acc->getRelatedContactsIds($recordId);
                    array_push($contacts, $recordId);
                    $entityTickets = implode(',',$contacts);
                    $listQuery .= "  AND vtiger_troubletickets.parent_id IN (".$entityTickets.") ";
                }else{
                    $listQuery .= "  AND vtiger_troubletickets.parent_id = ".$recordId." ";
                }
            }elseif($moduleName == 'Project'){
                $listQuery .= "  AND vtiger_troubletickets.project_id = ".$recordId." ";
            }
            $listQuery .= " UNION ";
            
        }
        
        $listQuery .= " SELECT DISTINCT vtiger_crmentity.crmid, vtiger_crmentity.createdtime as createddate, 
        vtiger_crmentity.setype as module, vtiger_modcomments.commentcontent as subject,
        vtiger_crmentity.status as status, vtiger_modcomments.commentcontent as description
        FROM vtiger_modcomments
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_modcomments.modcommentsid
        WHERE vtiger_crmentity.deleted = 0";
        if($moduleName == 'Accounts'){
            $acc = CRMEntity::getInstance($moduleName);
            $entityCom = $acc->getRelatedContactsIds($recordId);
            array_push($entityCom, $recordId);
            $ticket_ids = $acc->getRelatedTicketIds($entityCom);
            $portfolio_ids = $acc->getRelatedPortfolioIds($recordId);
            $entityCom = array_merge($entityCom, $ticket_ids, $portfolio_ids);
            $entityCom = implode(',', $entityCom);
            $listQuery .= " AND vtiger_modcomments.related_to IN (".$entityCom.") ";
        }
        elseif($moduleName == 'Contacts'){
            $con = CRMEntity::getInstance($moduleName);
            $ticket_ids = $con->getRelatedTicketIds($recordId);
            $portfolio_ids = $con->getRelatedPortfolioIds($recordId);
            $entityCom = array_merge( $ticket_ids, $portfolio_ids);
            array_push($entityCom, $recordId);
            $entityCom = implode(',', $entityCom);
            $listQuery .= " AND vtiger_modcomments.related_to IN (".$entityCom.") ";
        }
        else{
            $listQuery .= " AND vtiger_modcomments.related_to = ".$recordId." ";
        }
        
        if($dateQuery)
            $listQuery .=  $dateQuery;
            
        if($moduleQuery)
            $listQuery .=  $moduleQuery;
        
        if($searchSubject){
            $listQuery .= " AND vtiger_modcomments.commentcontent LIKE '%$searchSubject%' ";
        }
        
        if($creatorQuery)
            $listQuery .= $creatorQuery;
            
        $listQuery .= " UNION ";
        
        $listQuery .= " SELECT DISTINCT vtiger_crmentity.crmid, vtiger_crmentity.createdtime as createddate, 
        vtiger_crmentity.setype as module, vtiger_crmentity.label as subject,
        vtiger_crmentity.status as status, vtiger_crmentity.description as description
        FROM vtiger_emaildetails
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_emaildetails.emailid
        INNER JOIN vtiger_seactivityrel ON vtiger_seactivityrel.activityid = vtiger_emaildetails.emailid
        WHERE vtiger_crmentity.deleted =0";  
        if($moduleName == 'Accounts'){
            $acc = CRMEntity::getInstance($moduleName);
            $entityAccti = $acc->getRelatedContactsIds($recordId);
            array_push($entityAccti, $recordId);
            $ticket_ids = $acc->getRelatedTicketIds($entityAccti);
            $portfolio_ids = $acc->getRelatedPortfolioIds($recordId);
            $entityAccti = array_merge($entityAccti, $ticket_ids, $portfolio_ids);
            $entityAccti = implode(',', $entityAccti);
            $listQuery .= " AND vtiger_seactivityrel.crmid IN (".$entityAccti.") ";
        }
        elseif($moduleName == 'Contacts'){
            $con = CRMEntity::getInstance($moduleName);
            $ticket_ids = $con->getRelatedTicketIds($recordId);
            $portfolio_ids = $con->getRelatedPortfolioIds($recordId);
            $entityAccti = array_merge( $ticket_ids, $portfolio_ids);
            array_push($entityAccti, $recordId);
            $entityAccti = implode(',', $entityAccti);
            $listQuery .= " AND vtiger_seactivityrel.crmid IN (".$entityAccti.") ";
        }
        else{
            $listQuery .= " AND vtiger_seactivityrel.crmid = ".$recordId." ";
        }
        
        if($dateQuery)
            $listQuery .=  $dateQuery;
            
        if($moduleQuery)
            $listQuery .=  $moduleQuery;
        
        if($searchSubject){
            $listQuery .= " AND vtiger_crmentity.label LIKE '%$searchSubject%' ";
        }
        
        if($creatorQuery)
            $listQuery .= $creatorQuery;
            
        $listQuery .= " UNION ";
        
        $listQuery .= " SELECT DISTINCT vtiger_crmentity.crmid, vtiger_crmentity.createdtime as createddate, 
        vtiger_crmentity.setype as module, vtiger_activity.subject as subject,
        vtiger_activity.status as status, vtiger_crmentity.description as description
        FROM vtiger_activity ";
        
        if( $moduleName == 'Contacts' )
            $listQuery .= " INNER JOIN vtiger_cntactivityrel ON vtiger_cntactivityrel.activityid = vtiger_activity.activityid ";
        else
            $listQuery .= " INNER JOIN vtiger_seactivityrel ON vtiger_seactivityrel.activityid = vtiger_activity.activityid ";
                
        $listQuery .= " INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_activity.activityid
        WHERE vtiger_crmentity.deleted =0 AND vtiger_activity.activitytype NOT IN ('Emails','Task') ";
           
        if($dateQuery)
            $listQuery .=  $dateQuery;
            
        if($moduleQuery)
            $listQuery .=  $moduleQuery;
        
        if($searchSubject){
            $listQuery .= " AND vtiger_activity.subject LIKE '%$searchSubject%' ";
        }
        
        if($searchDescription){
            $listQuery .= " AND vtiger_crmentity.description LIKE '%$searchDescription%' ";
        }
        
        if($creatorQuery)
            $listQuery .= $creatorQuery;
            
        if( $moduleName == 'Contacts' )
            $listQuery .= "    AND vtiger_cntactivityrel.contactid = ".$recordId." ";
        else
            $listQuery .= "  AND vtiger_seactivityrel.crmid = ".$recordId." ";
                        
                        
        $listQuery .= " UNION ";
                        
        $listQuery .= " SELECT DISTINCT vtiger_crmentity.crmid , vtiger_crmentity.createdtime as createddate, 
        vtiger_crmentity.setype as module, vtiger_task.subject as subject,
        vtiger_task.task_status as status, vtiger_crmentity.description as description
        FROM vtiger_task
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_task.taskid
        WHERE vtiger_crmentity.deleted =0";
         
        if($dateQuery)
            $listQuery .=  $dateQuery;
        
        if($moduleQuery)
            $listQuery .=  $moduleQuery;
            
        if($searchSubject){
            $listQuery .= " AND vtiger_task.subject LIKE '%$searchSubject%' ";
        }
        
        if($searchDescription){
            $listQuery .= " AND vtiger_crmentity.description LIKE '%$searchDescription%' ";
        }
        
        if($creatorQuery)
            $listQuery .= $creatorQuery;
            
        if( $moduleName == 'Contacts' )
            $listQuery .= " AND vtiger_task.contact_id = ".$recordId." ";
        else
            $listQuery .= "  AND vtiger_task.parent_id = ".$recordId." ";
        //$recordId
          
        $listQuery .= " UNION ";
            
        $listQuery .= " SELECT DISTINCT vtiger_crmentity.crmid, vtiger_crmentity.createdtime as createddate,
        vtiger_crmentity.setype as module, vtiger_notes.title as subject,
       	vtiger_crmentity.status as status, vtiger_crmentity.description as description
        FROM vtiger_notes
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_notes.notesid
        INNER JOIN vtiger_senotesrel ON vtiger_senotesrel.notesid = vtiger_notes.notesid
        WHERE vtiger_crmentity.deleted = 0 ";
        if($moduleName == 'Accounts'){
            $acc = CRMEntity::getInstance($moduleName);
            $entityDoc = $acc->getRelatedContactsIds($recordId);
            array_push($entityDoc, $recordId);
            $ticket_ids = $acc->getRelatedTicketIds($entityDoc);
            $portfolio_ids = $acc->getRelatedPortfolioIds($recordId);
            $entityDoc = array_merge($entityDoc, $ticket_ids, $portfolio_ids);
            $entityDoc   = implode(',', $entityDoc);
            $listQuery .= " AND vtiger_senotesrel.crmid IN (".$entityDoc.") ";
        }
        elseif($moduleName == 'Contacts'){
            $con = CRMEntity::getInstance($moduleName);
            $ticket_ids = $con->getRelatedTicketIds($recordId);
            $portfolio_ids = $con->getRelatedPortfolioIds($recordId);
            $entityDoc = array_merge( $ticket_ids, $portfolio_ids);
            array_push($entityDoc, $recordId);
            $entityDoc = implode(',', $entityDoc);
            $listQuery .= " AND vtiger_senotesrel.crmid IN (".$entityDoc.") ";
        }
        else{
            $listQuery .= " AND vtiger_senotesrel.crmid = ".$recordId." ";
        }
        if($dateQuery)
            $listQuery .=  $dateQuery;
            
        if($moduleQuery)
            $listQuery .=  $moduleQuery;
            
        if($searchSubject){
            $listQuery .= " AND vtiger_notes.title LIKE '%$searchSubject%' ";
        }
        
        if($creatorQuery)
            $listQuery .= $creatorQuery;
        
        $listQuery .= " UNION ";
        
        $listQuery .= " SELECT DISTINCT vtiger_crmentity.crmid , vtiger_crmentity.createdtime as createddate,
        vtiger_crmentity.setype as module, vtiger_ringcentral.related_to as subject, 
        vtiger_ringcentral.ringcentral_status as status, vtiger_crmentity.description as description
        FROM vtiger_ringcentral
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_ringcentral.ringcentralid
        INNER JOIN vtiger_seringcentralrel ON vtiger_seringcentralrel.ringcentralid = vtiger_ringcentral.ringcentralid
        WHERE vtiger_crmentity.deleted =0 AND vtiger_seringcentralrel.crmid = ".$recordId;
        
        if($dateQuery)
            $listQuery .=  $dateQuery;
            
        if($moduleQuery)
            $listQuery .=  $moduleQuery;
            
        if($searchDescription){
            $listQuery .= " AND vtiger_crmentity.description LIKE '%$searchDescription%' ";
        }
        
        if($creatorQuery)
            $listQuery .= $creatorQuery;
                    
        
        $result = $db->pquery($listQuery,array());   
            
        return $db->num_rows($result);
    }
}
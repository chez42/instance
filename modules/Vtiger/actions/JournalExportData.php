<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vtiger_JournalExportData_Action extends Vtiger_RelatedMass_Action {
    
    function checkPermission(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        
        $currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        if(!$currentUserPriviligesModel->hasModuleActionPermission($moduleModel->getId(), 'Export')) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }
    }
    
    /**
     * Function is called by the controller
     * @param Vtiger_Request $request
     */
    function process(Vtiger_Request $request) {
        $this->ExportData($request);
    }
    
    private $moduleInstance;
    private $focus;
    
    /**
     * Function exports the data based on the mode
     * @param Vtiger_Request $request
     */
    function ExportData(Vtiger_Request $request) {
        
        $db = PearDatabase::getInstance();
        
        $moduleName = $request->getModule();
        
        $parentId = $request->get('record');
        
        $parentRecordId = $request->get('record');
        $pageNumber = $request->get('page');
        $limit = $request->get('limit');
        $moduleName = $request->getModule();
        
        if(empty($pageNumber)) {
            $pageNumber = 1;
        }
        
        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        if(!empty($limit)) {
            $pagingModel->set('limit', $limit);
        }
      
        $searchParams = $request->get('search_params');
        
        if(empty($searchParams)) {
            $searchParams = array();
        }
        
        foreach($searchParams as $fieldListGroup){
            foreach($fieldListGroup as $fieldSearchInfo){
                
                $fieldSearchInfoTemp= array();
                $fieldSearchInfoTemp['searchValue'] = $fieldSearchInfo[2];
                $fieldSearchInfoTemp['fieldName'] = $fieldName = $fieldSearchInfo[0];
                $fieldSearchInfoTemp['comparator'] = $fieldSearchInfo[1];
                $searchParams[$fieldName] = $fieldSearchInfoTemp;
            }
        }
       
        $mode = $request->get('mode');
        
        $query = $this->getExportQuery($parentRecordId, $pagingModel,$moduleName,$searchParams,$mode);
        
        $result = $db->pquery($query, array());
        
        $translatedHeaders = $this->getHeaders($relationListView);
        
        $entries = array();
        for ($j = 0; $j < $db->num_rows($result); $j++) {
            $entries[$j]["createddate"] =  Vtiger_Datetime_UIType::getDisplayDateTimeValue($db->query_result($result, $j, "createddate"));
            $entries[$j]["module"] = $db->query_result($result, $j, "module");
            if($entries[$j]["module"] == 'Calendar'){
                $entries[$j]["module"] = 'Events';
            }
            $entries[$j]["creator"] = getUserFullName($db->query_result($result, $j, "creator"));
            $entries[$j]["subject"] = html_entity_decode($db->query_result($result, $j, "subject"), ENT_QUOTES);
            $entries[$j]["description"] = trim(strip_tags(html_entity_decode($db->query_result($result, $j, "description"), ENT_QUOTES)));
            if($entries[$j]["module"] == 'ModComments'){
                $entries[$j]["description"] = 'N/A';
            }
            $entries[$j]["status"] = $db->query_result($result, $j, "status");
            if($entries[$j]["module"] == 'ModComments' || $entries[$j]["module"] == 'Emails'
                || $entries[$j]["module"] == 'Documents'){
                $entries[$j]["status"] = 'N/A';
            }
        }
       
        $this->output($request, $translatedHeaders, $entries);
    }
    
    public function getHeaders($relationListView) {
        
        $relatedColumnFields = array('CreatedDate', 'Type', 'Creator', 'Subject', 'Description', 'Status');
        
        $translatedHeaders = array_map('decode_html', $relatedColumnFields);
        return $translatedHeaders;
    }
    
    
    /**
     * Function that generates Export Query based on the mode
     * @param Vtiger_Request $request
     * @return <String> export query
     */
    function getExportQuery($parentRecordId, $pagingModel, $moduleName, $searchParams,$mode) {
        
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
            
            $listQuery .= " SELECT DISTINCT  vtiger_crmentity.createdtime as createddate,
            vtiger_crmentity.setype as module, vtiger_troubletickets.title as subject,
            vtiger_crmentity.description,
            vtiger_troubletickets.status as status, vtiger_crmentity.smcreatorid as creator
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
        
        $listQuery .= " SELECT DISTINCT  vtiger_crmentity.createdtime as createddate,
        vtiger_crmentity.setype as module, vtiger_modcomments.commentcontent as subject,vtiger_crmentity.description,
        vtiger_crmentity.status as status, vtiger_crmentity.smcreatorid as creator
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
            $entityCom = implode(',', $entityCom);
            $listQuery .= " AND vtiger_modcomments.related_to IN (".$entityCom.") ";
        }
        elseif($moduleName = 'Contacts'){
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
        
        $listQuery .= " SELECT DISTINCT  vtiger_crmentity.createdtime as createddate,
        vtiger_crmentity.setype as module, vtiger_crmentity.label as subject,
        vtiger_crmentity.description as description,
        vtiger_crmentity.status as status, vtiger_crmentity.smcreatorid as creator
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
        
        $listQuery .= " SELECT DISTINCT  vtiger_crmentity.createdtime as createddate,
        vtiger_crmentity.setype as module, vtiger_activity.subject as subject,
        vtiger_crmentity.description,
        vtiger_activity.status as status, vtiger_crmentity.smcreatorid as creator
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
        
        $listQuery .= " SELECT DISTINCT  vtiger_crmentity.createdtime as createddate,
        vtiger_crmentity.setype as module, vtiger_task.subject as subject,
        vtiger_crmentity.description,
        vtiger_task.task_status as status, vtiger_crmentity.smcreatorid as creator
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
            
        $listQuery .= " SELECT DISTINCT  vtiger_crmentity.createdtime as createddate,
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
                
        $listQuery .=" ORDER BY createddate DESC ";
        
        if($mode == 'ExportCurrentPage'){
            $listQuery .=" LIMIT $startIndex, ".($pageLimit+1);
        }
        
        return $listQuery;
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
        $fileName = str_replace(' ','_',decode_html(vtranslate('JournalViewList_'.$moduleName, $moduleName)));
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
    
   
}
<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vtiger_Journal_View extends Vtiger_Detail_View {

    function __construct() {
        parent::__construct();
        $this->exposeMethod('recentJournals');
        $this->exposeMethod('getJournalPageCount');
        $this->exposeMethod('Export');
    }
    
    function recentJournals(Vtiger_Request $request) {
       
        $moduleName = $request->getModule();
        
        $viewer = $this->getViewer($request);
        
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
        
        $recentActivities = ModTracker_Record_Model::getJournalUpdates($parentRecordId, $pagingModel,$moduleName,$searchParams);
        $pagingModel->calculatePageRange($recentActivities);
        
        $totalCount = ModTracker_Record_Model::getTotalJournalCount($parentRecordId,$moduleName,$searchParams);
        
        if($pagingModel->getCurrentPage() ==$totalCount/$pagingModel->getPageLimit()) {
            $pagingModel->set('nextPageExists', false);
        }
        
        $pageLimit = $pagingModel->getPageLimit();
        $pageCount = ceil((int) $totalCount / (int) $pageLimit);
        
        if($pageCount == 0){
            $pageCount = 1;
        }
        
        $recordModel = Vtiger_Record_Model::getInstanceById($parentRecordId);
        $viewer = $this->getViewer($request);
        $viewer->assign('PAGE_COUNT', $pageCount);
        $viewer->assign('TOTAL_ENTRIES', $totalCount);
        
        $viewer->assign('SOURCE',$recordModel->get('source'));
        $viewer->assign('RECENT_ACTIVITIES', $recentActivities);
        $viewer->assign('RELATED_ENTIRES_COUNT', $pagingModel->getPageLimit());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('PAGING', $pagingModel);
        $viewer->assign('RECORD_ID',$parentRecordId);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('SEARCH_DETAILS', $searchParams);
        
        echo $viewer->view('Journals.tpl', $moduleName, true);
        
    }
    
    
    function getJournalPageCount(Vtiger_Request $request){
        
        $moduleName = $request->getModule();
        
        $parentRecordId = $request->get('record');
        
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
        
        $listViewCount =  ModTracker_Record_Model::getTotalJournalCount($parentRecordId,$moduleName,$searchParams);
        $pagingModel = new Vtiger_Paging_Model();
        $pageLimit = $pagingModel->getPageLimit();
        $pageCount = ceil((int) $listViewCount / (int) $pageLimit);
        
        if($pageCount == 0){
            $pageCount = 1;
        }
        $result = array();
        $result['page'] = $pageCount;
        $result['numberOfRecords'] = $listViewCount;
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
    
    function Export(Vtiger_Request $request) {
        
        $moduleName = $request->getModule();
        
        $viewer = $this->getViewer($request);
        
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
        
       
        $viewer = $this->getViewer($request);
        $viewer->assign('PAGE', $pageNumber);
        $viewer->assign('SOURCE_MODULE', $moduleName);
        $viewer->assign('MODULE','Export');
        $viewer->assign('RECORD_ID',$parentRecordId);
        $viewer->assign('SEARCH_PARAMS', $searchParams);
        $viewer->view('JournalExport.tpl', $moduleName);
        
    }
    
}

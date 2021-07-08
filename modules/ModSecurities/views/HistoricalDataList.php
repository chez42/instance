<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ModSecurities_HistoricalDataList_View extends ModSecurities_Detail_View {

    function __construct() {
        parent::__construct();
        $this->exposeMethod('recentHistoricals');
        $this->exposeMethod('getHistoricalPageCount');
    }
    
    function recentHistoricals(Vtiger_Request $request) {
       
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
        
        $price_history = $this->getHistoricalListData($parentRecordId, $pagingModel,$moduleName,$searchParams);
        
		$pagingModel->calculatePageRange($price_history);
        
        $totalCount = $this->getHistoricalListDataCount($parentRecordId,$moduleName,$searchParams);
        
        if($pagingModel->getCurrentPage() == $totalCount / $pagingModel->getPageLimit()) {
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
        
		$viewer->assign('PRICE_HISTORY', $price_history);
		
        $viewer->assign('RELATED_ENTIRES_COUNT', $pagingModel->getPageLimit());
        
		$viewer->assign('MODULE_NAME', $moduleName);
        
		$viewer->assign('PAGING', $pagingModel);
        
		$viewer->assign('RECORD_ID',$parentRecordId);
        
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        
		$viewer->assign('SEARCH_DETAILS', $searchParams);
        
        echo $viewer->view('HistoricalDataList.tpl', $moduleName, true);
        
    }
    
    
    function getHistoricalPageCount(Vtiger_Request $request){
        
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
        
        $listViewCount =  $this->getHistoricalListDataCount($parentRecordId,$moduleName,$searchParams);
		
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
    
    public function getHistoricalListData($parentRecordId, $pagingModel,$moduleName,$searchParams){
        global $adb;
        
        $record = Vtiger_Record_Model::getInstanceById($parentRecordId);
        $data = $record->getData();
        $symbol = $data['security_symbol'];
        $security_type = $data['securitytype'];
        
        $startDate = null;
        $endDate = null;
        
        if($searchParams['date']){
            $dateCreate = explode(',',$searchParams['date']['searchValue']);
            if(!empty($dateCreate)){
                $startDate = DateTimeField::convertToDBFormat($dateCreate[0]);
                $endDate = DateTimeField::convertToDBFormat($dateCreate[1]);
            }
        }
        
        if(strtoupper($security_type) == "INDEX") {
            $prices = $this->GetHistoricalListPricesForSymbol('0O7N', $startDate, $endDate, "vtiger_prices_index",$pagingModel);
        }
        else {
            $prices = $this->GetHistoricalListPricesForSymbol($symbol, $startDate, $endDate, "vtiger_prices",$pagingModel);
        }
        
        return $prices;
        
    }
    
    public function getHistoricalListDataCount($parentRecordId,$moduleName,$searchParams){
        
        $record = Vtiger_Record_Model::getInstanceById($parentRecordId);
        
		$data = $record->getData();
		
        $symbol = $data['security_symbol'];
        
	//	$security_type = $data['securitytype'];
        
        $startDate = null;
        
		$endDate = null;
        
        if($searchParams['date']){
            $dateCreate = explode(',',$searchParams['date']['searchValue']);
            if(!empty($dateCreate)){
                $startDate = DateTimeField::convertToDBFormat($dateCreate[0]);
                $endDate = DateTimeField::convertToDBFormat($dateCreate[1]);
            }
        }
        
        //if(strtoupper($security_type) == "INDEX") {
           // $prices = $this->GetHistoricalListPricesForSymbol('0O7N', $startDate, $endDate, "vtiger_prices_index");
        //}
        //else {
        $prices = $this->GetHistoricalListPricesForSymbol($symbol, $startDate, $endDate);
        //}
        
        return count($prices);
        
    }
    
    public function GetHistoricalListPricesForSymbol($symbol, $start=null, $end=null, $table=null, $pagingModel=false){
		
        global $adb;
        
        $params = array();
        $params[] = $symbol;
        $params[] = $symbol;
        $params[] = $symbol;
        $params[] = $symbol;
        
        if($start && $end) {
            $and_1 = " AND ( price_date BETWEEN '".$start."' AND '".$end."') ";
			$and_2 = " AND ( date BETWEEN '".$start."' AND '".$end."') ";
        }
       
		$query = "SELECT symbol, price, price_date as date  FROM custodian_omniscient.`custodian_prices_fidelity` where symbol = ? {$and_1}  union 
		select symbol, price, date from custodian_omniscient.`custodian_prices_td` where symbol = ? {$and_1}  union 
		SELECT symbol, price, date  FROM custodian_omniscient.`custodian_prices_manual` where symbol = ? {$and_1} union 
		SELECT symbol, price, date  FROM custodian_omniscient.`custodian_prices_schwab` where symbol = ? {$and_1} ORDER BY date DESC ";
        
        if($pagingModel){
            $startIndex = $pagingModel->getStartIndex();
            $pageLimit = $pagingModel->getPageLimit();
            $query .= " LIMIT $startIndex, ".($pageLimit+1);
        }
		
        $result = $adb->pquery($query, $params);
		
		$prices = array();
            
        if($adb->num_rows($result) > 0){
			while($v = $adb->fetchByAssoc($result)) {
                $prices[] = $v;
            }
		}
        
		return $prices;
		
    }
}
<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class RingCentral_Index_View extends Vtiger_Index_View {
    
    function process(Vtiger_Request $request) {
        
        $mode= $request->get('mode');
        
        if($mode == 'getRingCentralPageCount')
            $this->getLogsCount($request);
        
        $module = "RingCentral";
        
        $extensionModule = $request->get('extensionModule');
    
        $viewer = $this->getViewer($request);
            
        $viewer->assign('QUALIFIED_MODULE', $module);
		
		$adb = PearDatabase::getInstance();
		
		$current_user_id = Users_Record_Model::getCurrentUserModel()->getId();
		
		$result = $adb->pquery('SELECT * FROM vtiger_ringcentral_settings 
		WHERE userid = ?',array($current_user_id));
		
		if($adb->num_rows($result)){
			
			$from_no = $adb->query_result($result, 0, "from_no");
			
			$viewer->assign("FROM_NO", $from_no);
			
			$this->LogsList($request);
			
			$viewer->assign("SHOW_SETTINGS", true);
		
		} else {
		
			$viewer->assign("SHOW_SETTINGS", false);
		
		}	
		
		$viewer->assign("CONNECT_URL", RingCentral_Config_Connector::getCallBackUrl());
		$viewer->view('Extension.tpl',$extensionModule);
   
    
    }
    
    function getHeaderScripts(Vtiger_Request $request) {
        
        $headerScriptInstances = parent::getHeaderScripts($request);
        
        $jsFileNames = array(
            "modules.RingCentral.resources.RingCentral",
        );
        
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        
        return $headerScriptInstances;
    }
    
    function LogsList(Vtiger_Request $request){
      
        $moduleName = 'RingCentral';
        
        $viewer = $this->getViewer($request);
        
        $pageNumber = $request->get('page');
        $limit = $request->get('limit');
        
        if(empty($pageNumber)) {
            $pageNumber = 1;
        }
        
        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        if(!empty($limit)) {
            $pagingModel->set('limit', $limit);
        }
        
        
        $recentLogs = $this->getLogs($pagingModel);
        $pagingModel->calculatePageRange($recentLogs);
        
        $totalCount = $this->getLogsCount();
        
        if($pagingModel->getCurrentPage() ==$totalCount/$pagingModel->getPageLimit()) {
            $pagingModel->set('nextPageExists', false);
        }
        
        $pageLimit = $pagingModel->getPageLimit();
        $pageCount = ceil((int) $totalCount / (int) $pageLimit);
        
        if($pageCount == 0){
            $pageCount = 1;
        }
        
        $viewer = $this->getViewer($request);
        $viewer->assign('PAGE_COUNT', $pageCount);
        $viewer->assign('TOTAL_ENTRIES', $totalCount);
        
        $viewer->assign('RECENT_LOGS', $recentLogs);
        $viewer->assign('RELATED_ENTIRES_COUNT', $pagingModel->getPageLimit());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('PAGING', $pagingModel);
        $viewer->assign('RECORD_ID',$parentRecordId);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        
    }
    
    
    function getLogs($pagingModel){
       
        global $adb,$current_user;
        
        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();
        
        $log_query = "SELECT * FROM vtiger_ringcentral_logs WHERE user_id = ".$current_user->id." 
        ORDER BY created_date DESC LIMIT ".$startIndex.", ".($pageLimit+1);
        
        $logQuery = $adb->pquery($log_query,array());
        $rows = $adb->num_rows($logQuery);
        
        for ($i=0; $i<$rows; $i++) {
            $row[] = $adb->query_result_rowdata($logQuery, $i);
        }
        
        return $row;
        
    }
    
    function getLogsCount(){
        global $adb,$current_user;
        
        $log_query = "SELECT * FROM vtiger_ringcentral_logs WHERE user_id = ".$current_user->id."
        ORDER BY created_date DESC ";
        
        $logQuery = $adb->pquery($log_query,array());
        
        return $adb->num_rows($logQuery);
        
    }
    
}
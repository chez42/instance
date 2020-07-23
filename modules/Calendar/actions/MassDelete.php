<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Calendar_MassDelete_Action extends Vtiger_MassDelete_Action {

	public function process(Vtiger_Request $request) {
		$adb = PearDatabase::getInstance();
		$moduleName = $request->getModule();
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);

		if ($request->get('selected_ids') == 'all' && $request->get('mode') == 'FindDuplicates') {
            $recordIds = Vtiger_FindDuplicate_Model::getMassDeleteRecords($request);
        } else {
            $recordIds = $this->getRecordsListFromRequest($request);
        }
		$cvId = $request->get('viewname');
		foreach($recordIds as $recordId) {
			if(Users_Privileges_Model::isPermitted($moduleName, 'Delete', $recordId)) {
				$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleModel);
				$parentRecurringId = $recordModel->getParentRecurringRecord();
				$adb->pquery('DELETE FROM vtiger_activity_recurring_info WHERE activityid=? AND recurrenceid=?', array($parentRecurringId, $recordId));
				$recordModel->delete();
				deleteRecordFromDetailViewNavigationRecords($recordId, $cvId, $moduleName);
			}
		}
		$response = new Vtiger_Response();
		$response->setResult(array('viewname'=>$cvId, 'module'=>$moduleName));
		$response->emit();
	}
	
	protected function getRecordsListFromRequest(Vtiger_Request $request) {
	    $cvId = $request->get('viewname');
	    $module = $request->get('module');
	    if(!empty($cvId) && $cvId=="undefined"){
	        $sourceModule = $request->get('sourceModule');
	        $cvId = CustomView_Record_Model::getAllFilterByModule($sourceModule)->getId();
	    }
	    $selectedIds = $request->get('selected_ids');
	    $excludedIds = $request->get('excluded_ids');
	    
	    if(!empty($selectedIds) && $selectedIds != 'all') {
	        if(!empty($selectedIds) && count($selectedIds) > 0) {
	            return $selectedIds;
	        }
	    }
	    
	    $customViewModel = CustomView_Record_Model::getInstanceById($cvId);
	    if($customViewModel) {
	        $searchKey = $request->get('search_key');
	        $searchValue = $request->get('search_value');
	        $operator = $request->get('operator');
	        if(!empty($operator)) {
	            $customViewModel->set('operator', $operator);
	            $customViewModel->set('search_key', $searchKey);
	            $customViewModel->set('search_value', $searchValue);
	        }
	        
	        /**
	         *  Mass action on Documents if we select particular folder is applying on all records irrespective of
	         *  seleted folder
	         */
	        if ($module == 'Documents') {
	            $customViewModel->set('folder_id', $request->get('folder_id'));
	            $customViewModel->set('folder_value', $request->get('folder_value'));
	        }
	        
	        $customViewModel->set('search_params',$request->get('search_params'));
	        return $customViewModel->getEventsRecordIds($excludedIds,$module);
	    }
	}
}

<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Documents_SaveAjax_Action extends Vtiger_SaveAjax_Action {

    public function checkPermission(Vtiger_Request $request) {
        $record = $request->get('record');
        if($record){
            
            $check = Documents_Record_Model::checkPermission($request->get('action'),$record);
            if(!$check)
                throw new AppException('LBL_PERMISSION_DENIED');
        }else{
            parent::checkPermission($request);
        }
    }
	/**
	 * Function to get the record model based on the request parameters
	 * @param Vtiger_Request $request
	 * @return Vtiger_Record_Model or Module specific Record Model instance
	 */
	public function getRecordModelFromRequest(Vtiger_Request $request) {
		
	    $recordModel = parent::getRecordModelFromRequest($request);
	    
	    $moduleName = $request->getModule();
		
		$recordId = $request->get('record');

		if(empty($recordId)) {
			
			$moduleModel = Vtiger_Module_Model::getInstance($moduleName);

			$recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
			$recordModel->set('mode', '');

			
			$fieldModelList = $moduleModel->getFields();
			foreach ($fieldModelList as $fieldName => $fieldModel) {
				if ($request->has($fieldName)) {
					$fieldValue = $request->get($fieldName, null);
				} else {
					$fieldValue = $fieldModel->getDefaultFieldValue();
				}
				$fieldDataType = $fieldModel->getFieldDataType();
				if ($fieldDataType == 'time') {
					$fieldValue = Vtiger_Time_UIType::getTimeValueWithSeconds($fieldValue);
				}
				if ($fieldValue !== null) {
					if (!is_array($fieldValue)) {
						$fieldValue = trim($fieldValue);
					}
					$recordModel->set($fieldName, $fieldValue);
				}
			}
			
			if(!$request->get('doc_folder_id')){
			    
			    global $adb;
			    
			    $doc_fol_id = '';
			    
			    $result = $adb->pquery("SELECT * FROM vtiger_documentfolder
                INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_documentfolder.documentfolderid
                WHERE vtiger_crmentity.deleted = 0 AND 
				vtiger_documentfolder.folder_name = BINARY 'Default'", array());
			    
			    if($adb->num_rows($result)) {
                    $doc_fol_id = $adb->query_result($result,0,'documentfolderid');
			    	$recordModel->set('doc_folder_id', $doc_fol_id);
				}
				
			}
			
		}

		return $recordModel;
	}
}

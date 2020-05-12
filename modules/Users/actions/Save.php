<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Users_Save_Action extends Vtiger_Save_Action {

	public function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$record = $request->get('record');
		$recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		if(!Users_Privileges_Model::isPermitted($moduleName, 'Save', $record) || ($recordModel->isAccountOwner() && 
							$currentUserModel->get('id') != $recordModel->getId() && !$currentUserModel->isAdminUser())) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
	}

	/**
	 * Function to get the record model based on the request parameters
	 * @param Vtiger_Request $request
	 * @return Vtiger_Record_Model or Module specific Record Model instance
	 */
	public function getRecordModelFromRequest(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$recordId = $request->get('record');
		$currentUserModel = Users_Record_Model::getCurrentUserModel();

		if(!empty($recordId)) {
			$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
			$modelData = $recordModel->getData();
			$recordModel->set('id', $recordId);
			$sharedType = $request->get('sharedtype');
			if(!empty($sharedType))
				$recordModel->set('calendarsharedtype', $request->get('sharedtype'));
			$recordModel->set('mode', 'edit');
		} else {
			$recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
			$modelData = $recordModel->getData();
			$recordModel->set('mode', '');
		}

		foreach ($modelData as $fieldName => $value) {
			$requestFieldExists = $request->has($fieldName);
			if(!$requestFieldExists){
				continue;
			}
			$fieldValue = $request->get($fieldName, null);
			if ($fieldName === 'is_admin' && (!$currentUserModel->isAdminUser() || !$fieldValue)) {
				$fieldValue = 'off';
			}
			//to not update is_owner from ui
			if ($fieldName == 'is_owner') {
				$fieldValue = null;
			}
			if($fieldValue !== null) {
				if(!is_array($fieldValue)) {
					$fieldValue = trim($fieldValue);
				}
				$recordModel->set($fieldName, $fieldValue);
			}
		}
		$homePageComponents = $recordModel->getHomePageComponents();
		$selectedHomePageComponents = $request->get('homepage_components', array());
		foreach ($homePageComponents as $key => $value) {
			if(in_array($key, $selectedHomePageComponents)) {
				$request->setGlobal($key, $key);
			} else {
				$request->setGlobal($key, '');
			}
		}
		if($request->has('tagcloudview')) {
			// Tag cloud save
			$tagCloud = $request->get('tagcloudview');
			if($tagCloud == "on") {
				$recordModel->set('tagcloud', 0);
			} else {
				$recordModel->set('tagcloud', 1);
			}
		}
		return $recordModel;
	}

	public function process(Vtiger_Request $request) {
		
		//$result = Vtiger_Util_Helper::transformUploadedFiles($_FILES, true);
		//$_FILES = $result['imagename'];

		$_FILES = Vtiger_Util_Helper::transformUploadedFiles($_FILES, true);
		
		$recordId = $request->get('record');
		if (!$recordId) {
			$module = $request->getModule();
			$userName = $request->get('user_name');
			$userModuleModel = Users_Module_Model::getCleanInstance($module);
			$status = $userModuleModel->checkDuplicateUser($userName);
			if ($status == true) {
				throw new AppException(vtranslate('LBL_DUPLICATE_USER_EXISTS', $module));
			}
		}
		$recordModel = $this->saveRecord($request);

		if ($request->get('relationOperation')) {
			$parentRecordModel = Vtiger_Record_Model::getInstanceById($request->get('sourceRecord'), $request->get('sourceModule'));
			$loadUrl = $parentRecordModel->getDetailViewUrl();
		} else if ($request->get('isPreference')) {
			$loadUrl =  $recordModel->getPreferenceDetailViewUrl();
		} else if ($request->get('returnmodule') && $request->get('returnview')){
			$loadUrl = 'index.php?'.$request->getReturnURL();
		} else if($request->get('mode') == 'Calendar'){
			$loadUrl = $recordModel->getCalendarSettingsDetailViewUrl();
		}else {
			$loadUrl = $recordModel->getDetailViewUrl();
		}
		
		global $adb;
		
		$params = array();
        
		if($request->get('user_principal_name')){
		    
		    if($request->get('sync_direction')){
    		  
		        $check = $adb->pquery("SELECT * FROM vtiger_msexchange_sync_settings WHERE user = ? and module = ?",
    		        array($request->get('record'), 'Calendar'));
    		    
    		    if($adb->num_rows($check)){
        		  
    		        $query = "UPDATE vtiger_msexchange_sync_settings SET impersonation_identifier = ?";
        		    $params[] = $request->get('user_principal_name');
        		    
        		    if($request->get('sync_direction')){
        		        $query .= ', direction = ?';
        		        $params[] = $request->get('sync_direction');
        		    }
        		    if($request->get('automatic_calendar_sync')){
        		        $query .= ', enable_cron = ?';
        		        $params[] = ($request->get('automatic_calendar_sync') == 'on') ? 1 : 0;
        		    }
        		    $query .= ', sync_start_from = ?';
        		    $params[] = $request->get('calendar_sync_start_from');
        		    
        		    $query .= ' WHERE user = ? ';
        		    $params[] = $request->get('record');
        		    
    		    }else{
    		        
    		        $query = "INSERT INTO vtiger_msexchange_sync_settings(id, user, module, direction, impersonation_identifier, sync_start_from, enable_cron) VALUES (?,?,?,?,?,?,?)";
    		        
    		        $syncId = $adb->getUniqueID("vtiger_msexchange_sync_settings");
    		        $enable = ($request->get('automatic_calendar_sync') == 'on') ? 1 : 0;
    		        $params = array($syncId, $request->get('record'), 'Calendar', $request->get('sync_direction'), $request->get('user_principal_name'), 
    		            $request->get('calendar_sync_start_from'), $enable);
    		        
    		    }
    		    
                $adb->pquery($query,$params);
                
		    }
		    if($request->get('task_sync_direction')){
    		    $check = $adb->pquery("SELECT * FROM vtiger_msexchange_sync_settings WHERE user = ? and module = ?",
    		        array($request->get('record'), 'Task'));
    		    
    		    if($adb->num_rows($check)){
    		        
    		        $query = "UPDATE vtiger_msexchange_sync_settings SET impersonation_identifier = ?";
    		        $params[] = $request->get('user_principal_name');
    		        
    		        if($request->get('task_sync_direction')){
    		            $query .= ', direction = ?';
    		            $params[] = $request->get('task_sync_direction');
    		        }
    		        if($request->get('automatic_task_sync')){
    		            $query .= ', enable_cron = ?';
    		            $params[] = ($request->get('automatic_task_sync') == 'on') ? 1 : 0;
    		        }
    		        $query .= ', sync_start_from = ?';
    		        $params[] = $request->get('task_sync_start_from');
    		        
    		        $query .= ' WHERE user = ? ';
    		        $params[] = $request->get('record');
    		        
    		    }else{
    		        
    		        $query = "INSERT INTO vtiger_msexchange_sync_settings(id, user, module, direction, impersonation_identifier, sync_start_from, enable_cron) VALUES (?,?,?,?,?,?,?)";
    		        
    		        $syncId = $adb->getUniqueID("vtiger_msexchange_sync_settings");
    		        $enable = ($request->get('automatic_task_sync') == 'on') ? 1 : 0;
    		        $params = array($syncId, $request->get('record'), 'Task', $request->get('task_sync_direction'), $request->get('user_principal_name'),
    		            $request->get('task_sync_start_from'), $enable);
    		        
    		    }
    		    
    		    $adb->pquery($query,$params);
		    }
		    if($request->get('contact_sync_direction')){
    		    $check = $adb->pquery("SELECT * FROM vtiger_msexchange_sync_settings WHERE user = ? and module = ?",
    		        array($request->get('record'), 'Contacts'));
    		    
    		    if($adb->num_rows($check)){
    		        
    		        $query = "UPDATE vtiger_msexchange_sync_settings SET impersonation_identifier = ?";
    		        $params[] = $request->get('user_principal_name');
    		        
    		        if($request->get('contact_sync_direction')){
    		            $query .= ', direction = ?';
    		            $params[] = $request->get('contact_sync_direction');
    		        }
    		        
    		        $query .= ', sync_start_from = ?';
    		        $params[] = $request->get('contact_sync_start_from');
    		        
    		        $query .= ' WHERE user = ? ';
    		        $params[] = $request->get('record');
    		        
    		    }else{
    		        
    		        $query = "INSERT INTO vtiger_msexchange_sync_settings(id, user, module, direction, impersonation_identifier, sync_start_from, enable_cron) VALUES (?,?,?,?,?,?,?)";
    		        
    		        $syncId = $adb->getUniqueID("vtiger_msexchange_sync_settings");
    		        $enable =  0;
    		        $params = array($syncId, $request->get('record'), 'Contacts', $request->get('contact_sync_direction'), $request->get('user_principal_name'), 
    		            $request->get('contact_sync_start_from'), $enable);
    		        
    		    }
    		    
    		    $adb->pquery($query,$params);
		    }
		}
		
		
		if(!empty($request->get('time'))){
            
		    $adb->pquery("UPDATE vtiger_users SET business_hours = ? WHERE id = ?",
		        array(json_encode($request->get('time')), $request->get('record')));
		    
		}
		
		header("Location: $loadUrl");
	}
}

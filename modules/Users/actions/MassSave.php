<?php

vimport('~~/include/Webservices/Custom/DeleteUser.php');

class Users_MassSave_Action extends Vtiger_MassSave_Action {
	
	function __construct() {
		parent::__construct();
		$this->exposeMethod('changeStatus');
		$this->exposeMethod("MassDeleteUsers");
	}

	public function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		if(!Users_Privileges_Model::isPermitted($moduleName, 'Save') && !$currentUserModel->isAdminUser()) {
			throw new AppException('LBL_PERMISSION_DENIED');
		}
	}
		
	function process(Vtiger_Request $request) {
	    
		$mode = $request->get('mode');
		
		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
		$moduleName = $request->getModule();
		$recordModels = $this->getRecordModelsFromRequest($request);
        $allRecordSave= true;
		foreach($recordModels as $recordId => $recordModel) {
			if(Users_Privileges_Model::isPermitted($moduleName, 'Save', $recordId)) {
				$recordModel->save();
			}
            else {
                $allRecordSave= false;
            }
		}
        
        $response = new Vtiger_Response();
        if($allRecordSave) {
           $response->setResult(true);
        } else {
           $response->setResult(false);
        }
   		$response->emit();
	}
	
	public function changeStatus(Vtiger_Request $request) {
	   
		$adb = PearDatabase::getInstance();
		
		$moduleName = $request->getModule();
		
		$recordModels = $this->getRecordModelsFromRequest($request);
		
        $allRecordSave= true;
		foreach($recordModels as $recordId => $recordModel) {
		   
			if(Users_Privileges_Model::isPermitted($moduleName, 'Save', $recordId)) {
				$recordModel->save();
				if($request->get("status") == 'Active'){
					$adb->pquery("UPDATE vtiger_users SET deleted=? WHERE id=?", array(0,$recordId));
				}				
	   		} else {
                $allRecordSave= false;
            }
		}
			
		$response = new Vtiger_Response();
        if($allRecordSave) {
           $response->setResult(true);
        } else {
           $response->setResult(false);
        }
   		$response->emit();
   	}

	public function MassDeleteUsers(Vtiger_Request $request){
	   
		$adb = PearDatabase::getInstance();
		
		$moduleName = $request->getModule();
		
		$recordModels = $this->getRecordModelsFromRequest($request);
		
		$newOwnerId = $request->get('transfer_user_id');
		
		$transformUserId = vtws_getWebserviceEntityId($moduleName, $newOwnerId);

        $userModel = Users_Record_Model::getCurrentUserModel();
       
        foreach($recordModels as $ownerId => $ownerRecordModel) {
        	
        	if($request->get("userRequestMode") == '1' && ($ownerRecordModel->get("deleted") == 1 || $ownerRecordModel->get("status") == "Inactive"))
                Users_Record_Model::deleteUserPermanently($ownerId, $newOwnerId);
        	else{
        	   
				$userId = vtws_getWebserviceEntityId($moduleName, $ownerId);
				
				vtws_deleteUser($userId, $transformUserId, $userModel);
	
	            if($request->get('permanent') == '1' || $request->get("userRequestMode") == '1')
	                Users_Record_Model::deleteUserPermanently($ownerId, $newOwnerId);
        	}
        }
        
        if(count($recordIds) > 1)
        	$message = vtranslate('LBL_USERS_DELETED_SUCCESSFULLY', $moduleName);
        else
        	$message = vtranslate('LBL_USER_DELETED_SUCCESSFULLY', $moduleName);
        
		$response = new Vtiger_Response();
		$response->setResult(array('message'=>$message));
		$response->emit();
	}
	
	public function getRecordsListFromRequest(Vtiger_Request $request) {
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
		
		$customViewModel = Users_CustomView_Model::getInstanceById($cvId);
		if($customViewModel) {
            $searchKey = $request->get('search_key');
            $searchValue = $request->get('search_value');
            $operator = $request->get('operator');
            if(!empty($operator)) {
                $customViewModel->set('operator', $operator);
                $customViewModel->set('search_key', $searchKey);
                $customViewModel->set('search_value', $searchValue);
            }

            $customViewModel->set('search_params',$request->get('search_params'));
			
            return $customViewModel->getRecordIds($excludedIds,$module);
		}
	}
	
}
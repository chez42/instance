<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Documents_SaveDocumentSettings_Action extends Vtiger_Save_Action {

    
    public function checkPermission(Vtiger_Request $request) {
        
        $moduleName = $request->get('sourcemodule');
        $record = $request->get('record');
       
        $recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
        
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        
        if(!Users_Privileges_Model::isPermitted($moduleName, 'Save', $record) || 
            ($recordModel->isAccountOwner() && $currentUserModel->get('id') != $recordModel->getId() &&
            !$currentUserModel->isAdminUser())) {
                
                throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
                
        }
            
    }
   
    
	public function process(Vtiger_Request $request) {
	    
	    $record = $request->get('record');
	    
	    $folder_id = $request->get('documents_folder');
	    
	    if($record){
	        
	       global $adb;
	       
	       $adb->pquery("UPDATE vtiger_users SET default_documents_folder_id = ? WHERE id = ?",array($folder_id,$record));
	    
	       require_once('modules/Users/CreateUserPrivilegeFile.php');
	       createUserPrivilegesfile($record);
	       
	       header("Location: index.php?module=Documents&view=List");
	       
	    }
	}


}

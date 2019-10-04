<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Documents_UpdateFolder_Action extends Vtiger_Action_Controller {
    
    function __construct() {
        parent::__construct();
        $this->exposeMethod('save');
       
    }
    
    public function checkPermission(Vtiger_Request $request) {
        $moduleName = $request->get('module');
        $record = $request->get('itemId');
        
        $recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
        
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        
        if(!Users_Privileges_Model::isPermitted($moduleName, 'Save', $record) ||
        ($currentUserModel->get('id') != $recordModel->getId() && !$currentUserModel->isAdminUser())) {
            
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
            
        }
    }
    
    public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
        }
    }
    
    public function save($request) {
        
        $folderId = $request->get('folderId');
        $itemId = $request->get('itemId');
        $result = array();
        
        if (!empty ($folderId)) {
            
            global $adb;
            
            $adb->pquery("UPDATE vtiger_notes SET doc_folder_id = ? WHERE notesid = ?",array($folderId,$itemId));
            
            $result = array('success'=>true, 'message'=>vtranslate('Document Move Successfully', $moduleName));
            
            $response = new Vtiger_Response();
            $response->setResult($result);
            $response->emit();
        }
    }
    
    public function validateRequest(Vtiger_Request $request) {
        $request->validateWriteAccess();
    }
}

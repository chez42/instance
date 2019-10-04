<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Documents_RelatedExportZip_View extends Vtiger_IndexAjax_View {
    
    public function checkPermission(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        
        $currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        if(!$currentUserPriviligesModel->hasModuleActionPermission($moduleModel->getId(), 'Export')) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }
    }
    
    public function process (Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $viewer = $this->getViewer($request);
        
        $parentRecord = $request->get('parentRecord');
        $parentModule = $request->get('parentModule');
        
        $viewer->assign('PARENT_MODULE', $parentModule);
        $viewer->assign('PARENT_RECORD', $parentRecord);
        
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('FOLDERS', $moduleModel->getAllFolders());
        $viewer->assign('SELECTED_IDS', $request->get('selected_ids'));
        $viewer->assign('EXCLUDED_IDS', $request->get('excluded_ids'));
        $viewer->assign('VIEWNAME',$request->get('viewname'));
        $viewer->assign('FOLDER_ID',$request->get('folder_id'));
        $viewer->assign('FOLDER_VALUE',$request->get('folder_value'));
        $viewer->assign('SEARCH_PARAMS', $request->get('search_params'));
        
        $searchKey = $request->get('search_key');
        $searchValue = $request->get('search_value');
        $operator = $request->get('operator');
        if(!empty($operator)) {
            $viewer->assign('OPERATOR',$operator);
            $viewer->assign('ALPHABET_VALUE',$searchValue);
            $viewer->assign('SEARCH_KEY',$searchKey);
        }
        
        $viewer->view('RelatedExportZip.tpl', $moduleName);
    }
}
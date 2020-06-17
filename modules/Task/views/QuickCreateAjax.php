<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Task_QuickCreateAjax_View extends Vtiger_QuickCreateAjax_View {
    
    public function checkPermission(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        
        if (!(Users_Privileges_Model::isPermitted($moduleName, 'CreateView'))) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED', $moduleName));
        }
    }
    
    public function process(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        
        $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
        $moduleModel = $recordModel->getModule();
        
        $recordId = $request->get('record','');
        $mode = $request->get('mode');
        if($mode === 'edit' && !empty($recordId)) {
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId,$moduleName);
        }
        
        $fieldList = $moduleModel->getFields();
        $requestFieldList = array_intersect_key($request->getAll(), $fieldList);
        
        foreach($requestFieldList as $fieldName => $fieldValue){
            $fieldModel = $fieldList[$fieldName];
            if($fieldModel->isEditable()) {
                $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
            }
        }
        
        $fieldsInfo = array();
        foreach($fieldList as $name => $model){
            $fieldsInfo[$name] = $model->getFieldInfo();
        }
        
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_QUICKCREATE);
        $picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
        
        $viewer = $this->getViewer($request);
        $viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', Vtiger_Functions::jsonEncode($picklistDependencyDatasource));
        $viewer->assign('CURRENTDATE', date('Y-n-j'));
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('SINGLE_MODULE', 'SINGLE_'.$moduleName);
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('FIELDS_INFO', json_encode($fieldsInfo));
        
        $viewer->assign('SCRIPTS', $this->getHeaderScripts($request));
        
        $viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
        $viewer->assign('MAX_UPLOAD_LIMIT_BYTES', Vtiger_Util_Helper::getMaxUploadSizeInBytes());
        $viewer->assign('RECORD_ID', $recordId);
        $viewer->assign('MODE', $mode);
        echo $viewer->view('QuickCreate.tpl',$moduleName,true);
        
    }
    
    
    public function getHeaderScripts(Vtiger_Request $request) {
        
        $moduleName = $request->getModule();
        
        $jsFileNames = array(
            "modules.Calendar.resources.Edit",
            "modules.$moduleName.resources.Edit"
        );
        
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }
    
}
<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vtiger_RelatedMassActionAjax_View extends Vtiger_IndexAjax_View {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('showMassEditForm');
        $this->exposeMethod('showAddCommentForm');
    }
    
    function process(Vtiger_Request $request) {
        
        $mode = $request->get('mode');
        if(!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }
    
    /**
     * Function returns the mass edit form
     * @param Vtiger_Request $request
     */
    function showMassEditForm(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $viewer = $this->getViewer($request);
        $this->initMassEditViewContents($request);
       
        echo $viewer->view('RelatedMassEditForm.tpl', $moduleName, true);
    }
    
    function initMassEditViewContents(Vtiger_Request $request) {
        
        $moduleName = $request->getModule();
        $cvId = $request->get('viewname');
        $selectedIds = $request->get('selected_ids');
        $excludedIds = $request->get('excluded_ids');
       
        $parentRecord = $request->get('parentRecord');
        $parentModule = $request->get('parentModule');
        
        $viewer = $this->getViewer($request);
        
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_MASSEDIT);
        $fieldInfo = array();
        $fieldList = $moduleModel->getFields();
        foreach ($fieldList as $fieldName => $fieldModel) {
            $fieldInfo[$fieldName] = $fieldModel->getFieldInfo();
        }
        $picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
        $recordStructure = $recordStructureInstance->getStructure();
        foreach($recordStructure as $blockName => $fields) {
            if(empty($fields)) {
                unset($recordStructure[$blockName]);
            }
        }
        
        $viewer->assign('PICKIST_DEPENDENCY_DATASOURCE',Vtiger_Functions::jsonEncode($picklistDependencyDatasource));
        $viewer->assign('SOURCE_MODULE', $moduleName);
        $viewer->assign('CURRENTDATE', date('Y-n-j'));
        $viewer->assign('MODE', 'massedit');
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('CVID', $cvId);
        $viewer->assign('SELECTED_IDS', $selectedIds);
        $viewer->assign('EXCLUDED_IDS', $excludedIds);
        $viewer->assign('VIEW_SOURCE','MASSEDIT');
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('MODULE_MODEL',$moduleModel);
        $viewer->assign('MASS_EDIT_FIELD_DETAILS',$fieldInfo);
        $viewer->assign('RECORD_STRUCTURE', $recordStructure);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('PARENT_MODULE', $parentModule);
        $viewer->assign('PARENT_RECORD', $parentRecord);
        //do not show any image details in mass edit form
        $viewer->assign('IMAGE_DETAILS', array());
        $searchKey = $request->get('search_key');
        $searchValue = $request->get('search_value');
        $operator = $request->get('operator');
        if(!empty($operator)) {
            $viewer->assign('OPERATOR',$operator);
            $viewer->assign('ALPHABET_VALUE',$searchValue);
            $viewer->assign('SEARCH_KEY',$searchKey);
        }
        $searchParams = $request->get('search_params');
        if(!empty($searchParams)) {
            $viewer->assign('SEARCH_PARAMS',$searchParams);
        }
    }
    
    /**
     * Function returns the Add Comment form
     * @param Vtiger_Request $request
     */
    function showAddCommentForm(Vtiger_Request $request){
        $sourceModule = $request->getModule();
        $moduleName = 'ModComments';
        $cvId = $request->get('viewname');
        $selectedIds = $request->get('selected_ids');
        $excludedIds = $request->get('excluded_ids');
        
        $parentRecord = $request->get('parentRecord');
        $parentModule = $request->get('parentModule');
        
        $viewer = $this->getViewer($request);
        $viewer->assign('SOURCE_MODULE', $sourceModule);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('CVID', $cvId);
        $viewer->assign('SELECTED_IDS', $selectedIds);
        $viewer->assign('EXCLUDED_IDS', $excludedIds);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        
        $modCommentsModel = Vtiger_Module_Model::getInstance($moduleName);
        $fileNameFieldModel = Vtiger_Field::getInstance("filename", $modCommentsModel);
        $fileFieldModel = Vtiger_Field_Model::getInstanceFromFieldObject($fileNameFieldModel);
        
        
        $searchKey = $request->get('search_key');
        $searchValue = $request->get('search_value');
        $operator = $request->get('operator');
        if(!empty($operator)) {
            $viewer->assign('OPERATOR',$operator);
            $viewer->assign('ALPHABET_VALUE',$searchValue);
            $viewer->assign('SEARCH_KEY',$searchKey);
        }
        
        $searchParams = $request->get('search_params');
        if(!empty($searchParams)) {
            $viewer->assign('SEARCH_PARAMS',$searchParams);
        }
        $viewer->assign('FIELD_MODEL', $fileFieldModel);
        $viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
        $viewer->assign('MAX_UPLOAD_LIMIT_BYTES', Vtiger_Util_Helper::getMaxUploadSizeInBytes());
        $viewer->assign('PARENT_MODULE', $parentModule);
        $viewer->assign('PARENT_RECORD', $parentRecord);
        
        echo $viewer->view('RelatedAddCommentForm.tpl',$moduleName,true);
    }
    
    
  
}

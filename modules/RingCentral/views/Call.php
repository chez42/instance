<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class RingCentral_Call_View extends Vtiger_IndexAjax_View {
    
    function process(Vtiger_Request $request) {
          
        global $site_URL;
        $qualifiedModuleName = $request->getModule();
        $viewer = $this->getViewer($request);
        
        $recordId = $request->get('record');
        $number = $request->get('number');
        
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId);
        
        $MODULE_MODEL = Vtiger_Module_Model::getInstance(getSalesEntityType($recordId));
        
        $fieldData = array();
        
        $FIELDS_MODELS_LIST = $MODULE_MODEL->getFields();
        
        foreach($FIELDS_MODELS_LIST as $FIELD_MODEL){
            $FIELD_DATA_TYPE = $FIELD_MODEL->getFieldDataType();
            $FIELD_NAME = $FIELD_MODEL->getName();
            if($FIELD_MODEL->isHeaderField() && $FIELD_MODEL->isActiveField() && $recordModel->get($FIELD_NAME) && $FIELD_MODEL->isViewable()){
                $fieldData[$FIELD_MODEL->get('label')] = $recordModel->getDisplayValue($FIELD_NAME);
            }
        }
        $imagePath = '';
        
        $imagedetails = $recordModel->getImageDetails();
        foreach($imagedetails as $key => $image){
            if(!empty($image['path']) && !empty($image['orgname'])){
                $imagePath = $image['path'].'_'.$image['orgname'];
            }
        }
        
        $fullName= '';
        $COUNTER = 0;
        foreach ($MODULE_MODEL->getNameFields() as $NAME_FIELD){
            $FIELD_MODEL = $MODULE_MODEL->getField($NAME_FIELD);
            if($FIELD_MODEL->getPermissions()){
                if($recordModel->getDisplayValue('salutationtype') && $FIELD_MODEL->getName() == 'firstname'){
                    $fullName .= $recordModel->getDisplayValue('salutationtype');
                }
                $fullName .= trim($recordModel->get($NAME_FIELD));
                if($COUNTER == 0 && ($recordModel->get($NAME_FIELD))){
                    $fullName .= ' ';
                    $COUNTER++;
                }
            }
        }
        
        $viewer->assign('RECORD', $recordId);
        $viewer->assign('FIELDS', $fieldData);
        $viewer->assign('IMAGE', $imagePath);
        $viewer->assign('FULLNAME', $fullName);
        $viewer->assign('SITE_URL', $site_URL);
        $viewer->assign('NUMBER', $number);
        
        $viewer->assign('SCRIPTS', $this->getHeaderScripts($request));
        
        $viewer->view('RingCentralCall.tpl', $qualifiedModuleName);
        
       /* echo "<script>
        window.opener.sync();
		function sendNotes(){
			var comment = document.getElementsByName('callnotes')[0].value;
			var record = document.getElementsByName('record')[0].value;
			window.opener.hangup(comment,record);
            window.close();
		}
	   </script>";*/
        
    }
    
    
    /**
     * Function to get the list of Script models to be included
     * @param Vtiger_Request $request
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $headerScriptInstances = array();
        $moduleName = $request->getModule();
        
        $jsFileNames = array(
            '~layouts/'.Vtiger_Viewer::getDefaultLayoutName().'/lib/jquery/jquery.min.js',
            '~layouts/'.Vtiger_Viewer::getDefaultLayoutName().'/lib/jquery/jquery.class.min.js',
            "modules.$moduleName.resources.RingcentralCall"
        );
        
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
    
}
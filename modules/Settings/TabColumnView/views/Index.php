<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_TabColumnView_Index_View extends Settings_Vtiger_Index_View {
    
    
    public function process(Vtiger_Request $request) {
        
        $sourceModule = $request->get('sourceModule');
        $supportedModulesList = Settings_TabColumnView_Module_Model::getSupportedModules();
        $supportedModulesList = array_flip($supportedModulesList);
        ksort($supportedModulesList);
        
        $viewer = $this->getViewer($request);
        $viewer->assign('MODE', $mode);
        $viewer->assign('SELECTED_TAB', $selectedTab);
        $viewer->assign('SUPPORTED_MODULES', $supportedModulesList);
        $viewer->assign('REQUEST_INSTANCE', $request);
        
        if ($sourceModule) {
            $viewer->assign('SELECTED_MODULE_NAME', $sourceModule);
        }
        
        $this->showFieldLayout($request);
        
    }
    
    public function showFieldLayout(Vtiger_Request $request) {
        $sourceModule = $request->get('sourceModule');
        $supportedModulesList = Settings_TabColumnView_Module_Model::getSupportedModules();
        $supportedModulesList = array_flip($supportedModulesList);
        ksort($supportedModulesList);
        
        if(empty($sourceModule)) {
            //To get the first element
            $sourceModule = reset($supportedModulesList);
        }
        $moduleModel = Settings_TabColumnView_Module_Model::getInstanceByName($sourceModule);
        $blockModels = $moduleModel->getBlocks();
        
        $qualifiedModule = $request->getModule(false);
        $viewer = $this->getViewer($request);
        $viewer->assign('REQUEST_INSTANCE', $request);
        $viewer->assign('SELECTED_MODULE_NAME', $sourceModule);
        $viewer->assign('SELECTED_MODULE_MODEL', $moduleModel);
        $viewer->assign('BLOCKS',$blockModels);
        $viewer->assign('SUPPORTED_MODULES',$supportedModulesList);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('QUALIFIED_MODULE', $qualifiedModule);
        
        global $adb;
        
        $blocktab = $adb->pquery("SELECT * FROM vtiger_module_tab_view WHERE module_name = ?",array($sourceModule));
        $isTab = '';
        if($adb->num_rows($blocktab)){
            for($i=0;$i<$adb->num_rows($blocktab);$i++){
                $isTab = $adb->query_result($blocktab, $i, 'is_tab');
            }
        }
        
        $customTab = $adb->pquery("SELECT *, vtiger_module_tab.sequence as tab_sequence, vtiger_module_tab_blocks.blockid as block_id FROM vtiger_module_tab 
        LEFT JOIN vtiger_module_tab_blocks ON vtiger_module_tab_blocks.tabid = vtiger_module_tab.id        
        LEFT JOIN vtiger_blocks ON vtiger_blocks.blockid = vtiger_module_tab_blocks.blockid        
        WHERE module_name = ? ORDER BY vtiger_module_tab.sequence,vtiger_module_tab_blocks.blocksequence ASC",array($sourceModule));
        
        $customTabData = array();
        if($adb->num_rows($customTab)){
            for($i=0;$i<$adb->num_rows($customTab);$i++){
                $customTabData[$adb->query_result($customTab, $i, 'id')][] = $adb->query_result_rowdata($customTab,$i);
                $tabName[$adb->query_result($customTab, $i, 'id')] = $adb->query_result($customTab, $i, 'tab_name');
                $TabSequence[$adb->query_result($customTab, $i, 'id')] = $adb->query_result($customTab, $i, 'tab_sequence');
                $blockColumns[$adb->query_result($customTab, $i, 'block_id')] = $adb->query_result($customTab, $i, 'columns');
            }
        }
        
        $viewer->assign('COLUMNS',$blockColumns);
        
        $viewer->assign('SEQUENCE',$TabSequence);
        
        $viewer->assign('TABNAME',$tabName);
        
        $viewer->assign('IS_TAB', $isTab);
        
        $viewer->assign('customTabData',$customTabData);
        
        $viewer->view('Index.tpl', $qualifiedModule);
    }
    
    /**
     * Function to get the list of Script models to be included
     * @param Vtiger_Request $request
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        
        $jsFileNames = array(
            '~/libraries/jquery/bootstrapswitch/js/bootstrap-switch.min.js',
        );
        
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
    
    public function getHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
            '~/libraries/jquery/bootstrapswitch/css/bootstrap2/bootstrap-switch.min.css',
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);
        return $headerCssInstances;
    }
    
}

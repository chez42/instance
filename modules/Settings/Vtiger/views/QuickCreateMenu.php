<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_Vtiger_QuickCreateMenu_View extends Settings_Vtiger_Index_View {
    
    public function process(Vtiger_Request $request) {
        
        global $adb;
        
        $viewer = $this->getViewer($request);
        $qualifiedName = $request->getModule(false);
        
        $restrictedModulesList = array('Emails', 'ModComments', 'Integration', 'PBXManager', 'Dashboard', 
            'Home', 'Google', 'Transactions', 'DocumentFolder', 'PositionInformation', 'PortfolioInformation', 
            'ModSecurities', 'Reports', 'RingCentral', 'Events', 'OmniCal');
        
        $quickSeq = $adb->pquery("SELECT * FROM vtiger_tab WHERE presence IN ('0','2') 
            AND isentitytype = 1 AND name NOT IN (".generateQuestionMarks($restrictedModulesList).")
            ORDER by ISNULL(quick_create_seq), quick_create_seq ASC",
            array($restrictedModulesList));
        
        if($adb->num_rows($quickSeq)){
            for($i=0;$i<$adb->num_rows($quickSeq);$i++){
                $module = Vtiger_Module_Model::getInstance($adb->query_result($quickSeq, $i, 'name'));
                
                if($module->isQuickCreateSupported()){
                
                    $moduleList[$adb->query_result($quickSeq, $i, 'tabid')] = array(
                        'name' => $adb->query_result($quickSeq, $i, 'name'),
                        'seq' => $adb->query_result($quickSeq, $i, 'quick_create_seq')
                    );
                
                }
            }
        }
            
        $viewer->assign('MODULE',$request->getModule());
        $viewer->assign('MODULELIST',$moduleList);
        $viewer->assign('QUALIFIED_MODULE', $qualifiedName);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->view('QuickCreateMenu.tpl',$qualifiedName);
    }
    
    function getPageTitle(Vtiger_Request $request) {
        $qualifiedModuleName = $request->getModule(false);
        return vtranslate('Quick Create Menu',$qualifiedModuleName);
    }
    
    /**
     * Function to get the list of Script models to be included
     * @param Vtiger_Request $request
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();
        
        $jsFileNames = array(
            "modules.Settings.$moduleName.resources.QuickCreateMenu"
        );
        
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
}
<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class OwnCloud_Settings_View extends Vtiger_Index_View {
    
    function __construct() {
        parent::__construct();
        $this->exposeMethod('userPrefrenceSettings');
    }
    
    function process(Vtiger_Request $request) {
        
        $mode = $request->get('submode');
        if(!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
        
        $qualifiedModuleName = $request->getModule();
        $viewer = $this->getViewer($request);
        
        $mode = $request->get('mode');
        if(!$mode)
            $mode = 'detail';
        
        global $adb,$current_user;
        
        $conQuery = $adb->pquery("SELECT * FROM vtiger_owncloud_configration ",array());
        
        if($adb->num_rows($conQuery)){
            
            $url = $adb->query_result($conQuery, 0, 'url');
            
            $viewer->assign('LOGINURL', $url);
            
        }
        
        $viewer->assign('mode', $mode);
        $viewer->assign('MODULE', $request->getModule());
        
        $url = "index.php?module=OwnCloud&parent=Settings&view=Settings";
        $viewer->assign('URL', $url);
        $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->view('OwnCloudSettings.tpl', $qualifiedModuleName);
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
            "modules.$moduleName.resources.Settings"
        );
        
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
    
    public function userPrefrenceSettings(Vtiger_Request $request){
        
        $qualifiedModuleName = $request->getModule();
        $viewer = $this->getViewer($request);
            
        global $adb,$current_user;
        
        $conQuery = $adb->pquery("SELECT * FROM vtiger_owncloud_credentials WHERE userid=?",array($current_user->id));
        
        if($adb->num_rows($conQuery)){
            
            $userName = $adb->query_result($conQuery, 0, 'username');
            $password = $adb->query_result($conQuery, 0, 'password');
            
            $viewer->assign('USERNAME', $userName);
            $viewer->assign('PASSWORD', $password);
            
        }
        
        $viewer->assign('MODULE', $request->getModule());
        $viewer->assign('RECORD', $request->get('record'));
        
        $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->view('OwnCloudCredentialsSetting.tpl', $qualifiedModuleName);
    }
    
}
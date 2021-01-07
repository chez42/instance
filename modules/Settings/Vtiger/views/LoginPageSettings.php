<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_Vtiger_LoginPageSettings_View extends Settings_Vtiger_Index_View {
    
    public function process(Vtiger_Request $request) {
        
        global $adb;
        
        $viewer = $this->getViewer($request);
        $qualifiedName = $request->getModule(false);
     
        $settings = $adb->pquery("SELECT * FROM vtiger_login_page_settings");
        if($adb->num_rows($settings)){
            
            $loginLogo = $adb->query_result($settings, 0, 'login_logo');
            $loginBackground = $adb->query_result($settings, 0, 'login_background');
            $copyrightText = $adb->query_result($settings , 0, 'copyright_text');
            $facebookLink = $adb->query_result($settings, 0, 'facebook_link');
            $twitterLink = $adb->query_result($settings, 0, 'twitter_link');
            $linkedinLink = $adb->query_result($settings, 0, 'linkedin_link');
            $youtubeLink = $adb->query_result($settings, 0, 'youtube_link');
            $instagramLink = $adb->query_result($settings, 0, 'instagram_link');

            $viewer->assign('LOGIN_LOGO', $loginLogo);
            $viewer->assign('LOGIN_BACKGROUND', $loginBackground);
            $viewer->assign('COPYRIGHT', $copyrightText);
            $viewer->assign('FACEBOOKLINK', $facebookLink);
            $viewer->assign('TWITTERLINK', $twitterLink);
            $viewer->assign('LINKEDINLINK', $linkedinLink);
            $viewer->assign('YOUTUBELINK', $youtubeLink);
            $viewer->assign('INSTAGRAMLINK', $instagramLink);
        }
        
        $viewer->assign('MODULE',$request->getModule());
        $viewer->assign('QUALIFIED_MODULE', $qualifiedName);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->view('LoginPageSettings.tpl',$qualifiedName);
    }
    
    function getPageTitle(Vtiger_Request $request) {
        $qualifiedModuleName = $request->getModule(false);
        return vtranslate('Login Page Settings',$qualifiedModuleName);
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
            "modules.Settings.$moduleName.resources.LoginPageSettings"
        );
        
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
}
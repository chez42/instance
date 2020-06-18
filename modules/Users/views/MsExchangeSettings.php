<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
class Users_MsExchangeSettings_View extends Vtiger_Detail_View {
    
    function __construct() {
        parent::__construct();
        $this->exposeMethod('MsSettingsEdit');
        $this->exposeMethod('MsSettingsDetail');
    }
    
    
    public function checkPermission(Vtiger_Request $request) {
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $record = $request->get('record');
        
        if($currentUserModel->isAdminUser() == true || $currentUserModel->get('id') == $record) {
            return true;
        } else {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }
        
    }
    
    /**
     * Function to returns the preProcess Template Name
     * @param <type> $request
     * @return <String>
     */
    public function preProcessTplName(Vtiger_Request $request) {
        return 'CalendarDetailViewPreProcess.tpl';
    }
    
    public function preProcess(Vtiger_Request $request, $display=true) {
        if($this->checkPermission($request)) {
            $qualifiedModuleName = $request->getModule(false);
            $currentUser = Users_Record_Model::getCurrentUserModel();
            $recordId = $request->get('record');
            $moduleName = $request->getModule();
            $detailViewModel = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
            $recordModel = $detailViewModel->getRecord();
            
            $detailViewLinkParams = array('MODULE'=>$moduleName,'RECORD'=>$recordId);
            $detailViewLinks = $detailViewModel->getDetailViewLinks($detailViewLinkParams);
            
            $viewer = $this->getViewer($request);
            $viewer->assign('RECORD', $recordModel);
            
            $viewer->assign('MODULE_MODEL', $detailViewModel->getModule());
            $viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);
            $viewer->assign('MODULE_BASIC_ACTIONS', array());
            
            $viewer->assign('IS_EDITABLE', $detailViewModel->getRecord()->isEditable($moduleName));
            $viewer->assign('IS_DELETABLE', $detailViewModel->getRecord()->isDeletable($moduleName));
            
            $linkParams = array('MODULE'=>$moduleName, 'ACTION'=>$request->get('view'));
            $linkModels = $detailViewModel->getSideBarLinks($linkParams);
            $viewer->assign('QUICK_LINKS', $linkModels);
            $viewer->assign('PAGETITLE', $this->getPageTitle($request));
            $viewer->assign('SCRIPTS',$this->getHeaderScripts($request));
            $viewer->assign('STYLES',$this->getHeaderCss($request));
            $viewer->assign('LANGUAGE_STRINGS', $this->getJSLanguageStrings($request));
            $viewer->assign('SEARCHABLE_MODULES', Vtiger_Module_Model::getSearchableModules());
            
            $menuModelsList = Vtiger_Menu_Model::getAll(true);
            $selectedModule = $request->getModule();
            $menuStructure = Vtiger_MenuStructure_Model::getInstanceFromMenuList($menuModelsList, $selectedModule);
            
            // Order by pre-defined automation process for QuickCreate.
            uksort($menuModelsList, array('Vtiger_MenuStructure_Model', 'sortMenuItemsByProcess'));
            
            $companyDetails = Vtiger_CompanyDetails_Model::getInstanceById();
            $companyLogo = $companyDetails->getLogo();
            
            $viewer->assign('CURRENTDATE', date('Y-n-j'));
            $viewer->assign('MODULE', $selectedModule);
            $viewer->assign('PARENT_MODULE', $request->get('parent'));
            $viewer->assign('VIEW', $request->get('view'));
            $viewer->assign('MENUS', $menuModelsList);
            $viewer->assign('QUICK_CREATE_MODULES', Vtiger_Menu_Model::getAllForQuickCreate());
            $viewer->assign('MENU_STRUCTURE', $menuStructure);
            $viewer->assign('MENU_SELECTED_MODULENAME', $selectedModule);
            $viewer->assign('MENU_TOPITEMS_LIMIT', $menuStructure->getLimit());
            $viewer->assign('COMPANY_LOGO',$companyLogo);
            $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
            
            $homeModuleModel = Vtiger_Module_Model::getInstance('Home');
            $viewer->assign('HOME_MODULE_MODEL', $homeModuleModel);
            $viewer->assign('HEADER_LINKS',$this->getHeaderLinks());
            $viewer->assign('ANNOUNCEMENT', $this->getAnnouncement());
            $viewer->assign('CURRENT_VIEW', $request->get('view'));
            $viewer->assign('SKIN_PATH', Vtiger_Theme::getCurrentUserThemePath());
            $viewer->assign('LANGUAGE', $currentUser->get('language'));
            $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
            $viewer->assign('SELECTED_MENU_CATEGORY', 'MARKETING');
            $settingsModel = Settings_Vtiger_Module_Model::getInstance();
            $menuModels = $settingsModel->getMenus();
            
            if(!empty($selectedMenuId)) {
                $selectedMenu = Settings_Vtiger_Menu_Model::getInstanceById($selectedMenuId);
            } elseif(!empty($moduleName) && $moduleName != 'Vtiger') {
                $fieldItem = Settings_Vtiger_Index_View::getSelectedFieldFromModule($menuModels,$moduleName);
                if($fieldItem){
                    $selectedMenu = Settings_Vtiger_Menu_Model::getInstanceById($fieldItem->get('blockid'));
                    $fieldId = $fieldItem->get('fieldid');
                } else {
                    reset($menuModels);
                    $firstKey = key($menuModels);
                    $selectedMenu = $menuModels[$firstKey];
                }
            } else {
                reset($menuModels);
                $firstKey = key($menuModels);
                $selectedMenu = $menuModels[$firstKey];
            }
            
            $settingsMenItems = array();
            foreach($menuModels as $menuModel) {
                $menuItems = $menuModel->getMenuItems();
                foreach($menuItems as $menuItem) {
                    $settingsMenItems[$menuItem->get('name')] = $menuItem;
                }
            }
            $viewer->assign('SETTINGS_MENU_ITEMS', $settingsMenItems);
            
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
            
            $moduleFields = $moduleModel->getFields();
            foreach($moduleFields as $fieldName => $fieldModel){
                $fieldsInfo[$fieldName] = $fieldModel->getFieldInfo();
            }
            $eventsModuleModel = Vtiger_Module_Model::getInstance('Events');
            $eventFields = array('defaulteventstatus' => 'eventstatus', 'defaultactivitytype' => 'activitytype');
            foreach($eventFields as $userField => $eventField) {
                $fieldsInfo[$userField]['picklistvalues'] = $eventsModuleModel->getField($eventField)->getPicklistValues();
            }
            $viewer->assign('FIELDS_INFO', json_encode($fieldsInfo));
            
            $activeBLock = Settings_Vtiger_Module_Model::getActiveBlockName($request);
            $viewer->assign('ACTIVE_BLOCK', $activeBLock);
            
            if($display) {
                $this->preProcessDisplay($request);
            }
        }
    }
    
    protected function preProcessDisplay(Vtiger_Request $request) {
        $viewer = $this->getViewer($request);
        $viewer->view($this->preProcessTplName($request), $request->getModule());
    }
    
    public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if($mode == 'Edit'){
            $this->invokeExposedMethod('MsSettingsEdit',$request);
        } else {
            $this->invokeExposedMethod('MsSettingsDetail',$request);
        }
    }
    
    public function MsSettingsEdit(Vtiger_Request $request){
       
        $viewer = $this->getViewer($request);

        $module = $request->getModule();
        global $adb;
        
        $check = $adb->pquery("SELECT * FROM vtiger_msexchange_sync_settings WHERE module = 'Task' AND user = ?",
            array($request->get('record')));
        
        $syncData = array();
        if($adb->num_rows($check)){
            $syncData = $adb->query_result_rowdata($check);
            $taskPrincipal = $adb->query_result($check, 0, 'impersonation_identifier');
        }
        $viewer->assign('SYNCDATA', $syncData);
        
        $checkCal = $adb->pquery("SELECT * FROM vtiger_msexchange_sync_settings WHERE module = 'Calendar' AND user = ?",
            array($request->get('record')));
        $syncCalData = array();
        if($adb->num_rows($checkCal)){
            $syncCalData = $adb->query_result_rowdata($checkCal);
            $calPrincipal = $adb->query_result($checkCal, 0, 'impersonation_identifier');
        }
        $viewer->assign('CALENDARSYNCDATA', $syncCalData);
        
        $checkCon = $adb->pquery("SELECT * FROM vtiger_msexchange_sync_settings WHERE module = 'Contacts' AND user = ?",
            array($request->get('record')));
        $syncConData = array();
        if($adb->num_rows($checkCon)){
            $syncConData = $adb->query_result_rowdata($checkCon);
            $conPrincipal = $adb->query_result($checkCon, 0, 'impersonation_identifier');
        }
        $viewer->assign('CONTACTSYNCDATA', $syncConData);
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $viewer->assign('CURRENTUSER_MODEL',$currentUserModel);
        $principal = '';
        if($calPrincipal){
            $principal = $calPrincipal;
        }else if($taskPrincipal){
            $principal = $taskPrincipal;
        }else if($conPrincipal){
            $principal = $conPrincipal;
        }
        
        $viewer->assign('PRINCIPAL', $principal);
        
        $viewer->view('MsExchangeSettingsEditView.tpl', $request->getModule());
    }
    
    
    
    public function MsSettingsDetail(Vtiger_Request $request){
        
        $viewer = $this->getViewer($request);
        
        global $adb,$site_URL;
        $check = $adb->pquery("SELECT * FROM vtiger_msexchange_sync_settings WHERE user = ? and module = ?",
            array($request->get('record'), 'Task'));
        $syncData = array();
        if($adb->num_rows($check)){
            $syncData = $adb->query_result_rowdata($check);
        }
        $viewer->assign('SYNCDATA', $syncData);
        
        $checkCal = $adb->pquery("SELECT * FROM vtiger_msexchange_sync_settings WHERE user = ? and module = ?",
            array($request->get('record'), 'Calendar'));
        $syncCalData = array();
        if($adb->num_rows($checkCal)){
            $syncCalData = $adb->query_result_rowdata($checkCal);
        }
        $viewer->assign('CALENDARSYNCDATA', $syncCalData);
        
        $checkCon = $adb->pquery("SELECT * FROM vtiger_msexchange_sync_settings WHERE user = ? and module = ?",
            array($request->get('record'), 'Contacts'));
        $syncConData = array();
        if($adb->num_rows($checkCon)){
            $syncConData = $adb->query_result_rowdata($checkCon);
        }
        $viewer->assign('CONTACTSYNCDATA', $syncConData);
        $viewer->view('MsExchangeSettingsDetailView.tpl', $request->getModule());
    }
    
    public function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = array();
        $moduleName = $request->getModule();
        
        $jsFileNames = array(
            'modules.'.$moduleName.'.resources.MsExchange',
            'modules.Vtiger.resources.List',
            'modules.Vtiger.resources.Popup',
        );
        
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
    
    /*
     * HTTP REFERER check was removed in Parent class Vtiger_Detail_View, because of
     * CRM Detail View URL option in Workflow Send Mail task.
     * But here http referer check is required.
     */
    public function validateRequest(Vtiger_Request $request) {
        $request->validateReadAccess();
    }
    
}

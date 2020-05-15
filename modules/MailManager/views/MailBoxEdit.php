<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class MailManager_MailBoxEdit_View extends Vtiger_Index_View {
    
    function __construct() {
        parent::__construct();
        $this->exposeMethod('step1');
        $this->exposeMethod('step2');
        $this->exposeMethod('step3');
        $this->exposeMethod('mailBoxList');
    }
    
    public function process(Vtiger_Request $request) {
        $mode = $request->get('mode');
        $recordId = $request->get('record');
        $mode = $request->get('mode');
        
        if($mode != 'mailBoxList'){
            if (!$mode)
                $mode = "step1";
            if($mode == 'step1'){
                $moduleName = $request->getModule();
                $qualifiedModuleName = 'Settings:MailConverter';
                if ($recordId) {
                    $recordModel = Settings_MailConverter_Record_Model::getInstanceById($recordId);
                } else {
                    $recordModel = Settings_MailConverter_Record_Model::getCleanInstance();
                }
                $viewer = $this->getViewer($request);
                
                if ($recordId) {
                    $viewer->assign('RECORD_ID', $recordId);
                }
                $viewer->assign('CREATE', $request->get('create'));
                $viewer->assign('RECORD_MODEL', $recordModel);
                $viewer->assign('MODULE_MODEL', $recordModel->getModule());
                $viewer->assign('STEP', $mode);
                $viewer->assign('MODULE_NAME', $moduleName);
                $viewer->assign('QUALIFIED_MODULE_NAME', $qualifiedModuleName);
                $viewer->assign('SCRIPTS', $this->getHeaderScripts($request));
                $viewer->view('MailBoxEditHeader.tpl', $moduleName);
            }
        }
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }
    
   
    
    public function step1(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $qualifiedModuleName = 'Settings:MailConverter';
        $viewer = $this->getViewer($request);
        $viewer->assign('QUALIFIED_MODULE_NAME', $qualifiedModuleName);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->view('Step1.tpl', $moduleName);
    }
    
    public function step2(Vtiger_Request $request) {
        $recordId = $request->get('record');
        $moduleName = $request->getModule();
        $folders = Settings_MailConverter_Module_Model::getFolders($recordId);
        $qualifiedModuleName = 'Settings:MailConverter';
        $viewer = $this->getViewer($request);
        if (is_array($folders)) {
            $viewer->assign('FOLDERS', $folders);
        } else if ($folders) {
            $viewer->assign('IMAP_ERROR', $folders);
        } else {
            $viewer->assign('CONNECTION_ERROR', true);
        }
        $viewer->assign('QUALIFIED_MODULE_NAME', $qualifiedModuleName);
        $viewer->view('Step2.tpl', $moduleName);
    }
    
    public function step3(Vtiger_Request $request) {
        $scannerId = $request->get('record');
        $moduleName = $request->getModule();
        $recordModel = Settings_MailConverter_RuleRecord_Model::getCleanInstance($scannerId);
        
        global $current_user;
        $currentUserId = $current_user->id;
        $viewer = $this->getViewer($request);
        $qualifiedModuleName = 'Settings:MailConverter';
        $viewer->assign('RECORD_MODEL', $recordModel);
        $viewer->assign('DEFAULT_MATCH', "AND");
        $viewer->assign('MODULE_MODEL', new Settings_MailConverter_Module_Model());
        
        $viewer->assign('SCANNER_ID', $scannerId);
        $viewer->assign('SCANNER_MODEL', Settings_MailConverter_Record_Model::getInstanceById($scannerId));
        
        $viewer->assign('DEFAULT_OPTIONS', Settings_MailConverter_RuleRecord_Model::getDefaultConditions());
        $viewer->assign('DEFAULT_ACTIONS', Settings_MailConverter_RuleRecord_Model::getDefaultActions());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('ASSIGNED_USER', $currentUserId);
        $viewer->assign('QUALIFIED_MODULE_NAME', $qualifiedModuleName);
        $viewer->view('Step3.tpl', $moduleName);
    }
    
    public function mailBoxList(Vtiger_Request $request) {
        
        $moduleName = $request->getModule();
        $scannerId = $request->get('record');
        
        if ($scannerId == '') {
            $scannerId = Settings_MailConverter_Module_Model::getDefaultId();
        }
        $qualifiedModuleName = 'Settings:MailConverter';
        
        $recordExists = Settings_MailConverter_Module_Model::MailBoxExists();
        $recordModel = Settings_MailConverter_Record_Model::getAll();
        $viewer = $this->getViewer($request);
        
        $viewer->assign('MODULE_MODEL', Settings_Vtiger_Module_Model::getInstance($qualifiedModuleName));
        $viewer->assign('MAILBOXES', Settings_MailConverter_Module_Model::getMailboxes());
        $viewer->assign('SCRIPTS', $this->getHeaderScripts($request));
        $viewer->assign('FIELDS_INFO', json_encode(array()));
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('QUALIFIED_MODULE_NAME', $qualifiedModuleName);
        $viewer->assign('CRON_RECORD_MODEL', Settings_CronTasks_Record_Model::getInstanceByName('MailScanner'));
        $viewer->assign('RECORD_EXISTS', $recordExists);
        
        if ($scannerId) {
            $viewer->assign('SCANNER_ID', $scannerId);
            $viewer->assign('RECORD', $recordModel[$scannerId]);
            $viewer->assign('SCANNER_MODEL', Settings_MailConverter_Record_Model::getInstanceById($scannerId));
            $viewer->assign('RULE_MODELS_LIST', Settings_MailConverter_RuleRecord_Model::getAll($scannerId));
            $viewer->assign('FOLDERS_SCANNED', Settings_MailConverter_Module_Model::getScannedFolders($scannerId));
        }
        $viewer->view('RulesList.tpl', $moduleName);
        
    }
    
    public function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = array();//parent::getHeaderScripts($request);
        
        $jsFileNames = array(
            'modules.MailManager.resources.MailConverter'
        );
        
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
    
}

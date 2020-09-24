<?php
class Settings_Vtiger_PortalConfiguration_View extends Settings_Vtiger_Index_View {
    
    
    public function __construct() {
        parent::__construct();
        $this->exposeMethod('chatWidget');
    }
    
    public function process (Vtiger_Request $request) {
        
        $mode = $request->get('mode');
        if(!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
        
        global $adb;
       
        $qualifiedModule = $request->getModule(false);
        $sourceModuleName = 'Contacts';
        $moduleModel = Vtiger_Module_Model::getInstance($sourceModuleName);
        
        $recordModel = Vtiger_Record_Model::getCleanInstance('Contacts');
        
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
        $tabid = $moduleModel->getId();
        
        $fields = $recordStructureInstance->getStructure();
        
        $portalField = $adb->pquery("SELECT * FROM vtiger_portal_configuration");
        
        if($adb->num_rows($portalField)){
            $portalFields  = json_decode(html_entity_decode($adb->query_result($portalField, 0, 'portal_fields')));
        }
        
        $viewer = $this->getViewer($request);
        $viewer->assign('FIELDS', $fields);
        $viewer->assign('PORTAL_FIELDS', $portalFields);
        $viewer->assign('SOURCE_MODULE', $sourceModuleName);
        $viewer->assign('QUALIFIED_MODULE', $qualifiedModule);
        $viewer->assign('SOURCE_MODULE_MODEL', $moduleModel);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->view('PortalConfiguration.tpl', $qualifiedModule);
    }
    
    function getPageTitle(Vtiger_Request $request) {
        $qualifiedModuleName = $request->getModule(false);
        return vtranslate('Configure Portal Fields',$qualifiedModuleName);
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
            "modules.Settings.$moduleName.resources.PortalConfiguration"
        );
        
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
    
    
    function chatWidget(Vtiger_Request $request){
        
        global $adb;
        
        $qualifiedModule = $request->getModule(false);
        
        $portalChatWidget = $adb->pquery("SELECT * FROM vtiger_portal_configuration");
        
        if($adb->num_rows($portalChatWidget)){
            $portalChatWidgetCode  = $adb->query_result($portalChatWidget, 0, 'portal_chat_widget_code');
        }
        
        $viewer = $this->getViewer($request);
        $viewer->assign('PORTAL_WIDGET_CODE', $portalChatWidgetCode);
        $viewer->assign('QUALIFIED_MODULE', $qualifiedModule);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODE', $request->get('mode'));
        
        $viewer->view('PortalConfiguration.tpl', $qualifiedModule);
        
    }
    
    
}
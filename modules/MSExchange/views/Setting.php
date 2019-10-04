<?php

class MSExchange_Setting_View extends Vtiger_PopupAjax_View {
    
    public function process(Vtiger_Request $request) {
        switch ($request->get('sourcemodule')) {
            case "Contacts" : $this->emitContactsSyncSettingUI($request);
            break;
            case "Calendar" : $this->emitCalendarSyncSettingUI($request);
            break;
        }
    }
    
    public function emitCalendarSyncSettingUI(Vtiger_Request $request) {
        
        $user = Users_Record_Model::getCurrentUserModel();
        
        $moduleModel = Vtiger_Module_Model::getInstance($request->get('sourcemodule'));
        
        $moduleFields = $moduleModel->getFields();
        
        $calendarFields = MSExchange_Utils_Helper::getFieldMappingDetails($user, 'Calendar');
        
        $moduleFieldsArray = array();
        
        foreach($moduleFields as $fieldModel){
            $moduleFieldsArray[$fieldModel->get("name")] = $fieldModel->get("label");
        }
        
        $viewer = $this->getViewer($request);
        
        $viewer->assign('MODULENAME', $request->getModule());
        
        $viewer->assign('SOURCE_MODULE', $request->get('sourcemodule'));
        
        $viewer->assign('FIELD_MAPPING', $calendarFields);
        
        $viewer->assign("MODULE_FIELDS_LABEL", $moduleFieldsArray);
        
        echo $viewer->view('CalendarSyncSettings.tpl', $request->getModule(), true);
    }
    
    public function emitContactsSyncSettingUI(Vtiger_Request $request) {
        
        $module = $request->getModule();
        
        $moduleModel = Vtiger_Module_Model::getInstance($request->get('sourcemodule'));
        
        $moduleFields = $moduleModel->getFields();
        
        $user = Users_Record_Model::getCurrentUserModel();
        
        $viewer = $this->getViewer($request);
        
        $viewer->assign("MSEXCHANGE_MAPPING", $msExchangeFieldMapping);
        
        $viewer->assign('MODULENAME', $request->getModule());
        
        $mappingFields = MSExchange_Utils_Helper::getFieldMappingDetails($user, $request->get('sourcemodule'));
        
        $viewer->assign('FIELD_MAPPING', $mappingFields);
        
        $viewer->assign('SOURCE_MODULE', $request->get('sourcemodule'));
        
        $viewer->assign('EXCHANGE_FIELDS', MSExchange_Utils_Helper::$exchangeContactFields);
        
        $viewer->assign("MODULE_FIELDS", $moduleFields);
        
        $moduleFieldsArray = array();
        
        foreach($moduleFields as $fieldModel){
            $moduleFieldsArray[$fieldModel->get("name")] = $fieldModel->get("label");
        }
        
        $viewer->assign("MODULE_FIELDS_LABEL", $moduleFieldsArray);
        
        echo $viewer->view('ContactsSyncSettings.tpl', $module, true);
    }   
    
}

?>
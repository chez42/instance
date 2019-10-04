<?php

class Omniscient_EditExchangeUsers_View extends Vtiger_BasicAjax_View{
    public function process(Vtiger_Request $request) {
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        if($currentUserModel->isAdminUser())
            $can_edit_sync = true;
        
        $model = new Omniscient_EditExchangeUsers_Model();
        $record = $request->get('record');
        $isEnabled = $model->IsEnabled($record);
        $sync_info = OmniCal_CRMExchangeHandler_Model::GetSyncInfo($record);
        
        if($sync_info == 0 && $isEnabled){
            OmniCal_CRMExchangeHandler_Model::CreateSyncInfo($record, 'Contact');
            OmniCal_CRMExchangeHandler_Model::CreateSyncInfo($record, 'Task');
            OmniCal_CRMExchangeHandler_Model::CreateSyncInfo($record, 'CalendarItem');
            OmniCal_CRMExchangeHandler_Model::CreateSyncInfo($record, 'Email');
            $sync_info = OmniCal_CRMExchangeHandler_Model::GetSyncInfo($record);
        }

        $viewer = $this->getViewer($request);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $viewer->assign("EDIT_SYNC", $can_edit_sync);
        $viewer->assign("SYNC_INFO", $sync_info);
        $viewer->assign("ENABLED", $isEnabled);
        
        echo $viewer->view("EditExchangeUsers.tpl", 'Omniscient', true);
    }
    
    public function getHeaderScripts(Vtiger_Request $request) {
        $jsFileNames = array(
            "modules.Omniscient.resources.EditExchangeUsers",
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }
}

?>
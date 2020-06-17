<?php
class Users_CheckExchange_Action extends Vtiger_Save_Action {
    
    public function checkPermission(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $record = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        if(!Users_Privileges_Model::isPermitted($moduleName, 'Save', $record) || ($recordModel->isAccountOwner() &&
            $currentUserModel->get('id') != $recordModel->getId() && !$currentUserModel->isAdminUser())) {
                throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }
    }
    
    public function process(Vtiger_Request $request) {
        
        global $adb;
        
        $msExchange = $adb->pquery("SELECT * FROM vtiger_msexchange_global_settings");
        
        if($adb->num_rows($msExchange)){
            
            $host = $adb->query_result($msExchange, 0, "url");
            
            $username = $adb->query_result($msExchange, 0, "username");
            
            $password = $adb->query_result($msExchange, 0, "password");
            
            $version = $adb->query_result($msExchange, 0, "exchange_version");
            
            $impersonationType = $adb->query_result($msExchange, 0, "impersonation_type");
            
            $impersonating_field_value = $request->get('user_principal_name');
            
            if (!MSExchange_Utils_Helper::isProtectedText($password)) {
                $password = MSExchange_Utils_Helper::toProtectedText($password);
            }
            
            $request->set('ms_exchange_url', $host);
            $request->set('ms_exchange_username', $username);
            $request->set('ms_exchange_password', $password);
            $request->set('ms_exchange_version', $version);
            $request->set('ms_exchange_user_impersonation_type', $impersonationType);
            $request->set('ms_exchange_user_impersonation_field_value', $impersonating_field_value);
            
            $MSExchangeModel = new MSExchange_MSExchange_Model();
            
            $userImpersonation = $MSExchangeModel->checkImpersonation($request);
            
            $result = $userImpersonation;
            
        }else{
            $result = array('success'=>false);
        }
        
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
        
    }
    
    
}
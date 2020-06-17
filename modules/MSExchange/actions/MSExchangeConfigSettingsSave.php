<?php

class MSExchange_MSExchangeConfigSettingsSave_Action extends Vtiger_BasicAjax_Action {

	public function process(Vtiger_Request $request) {
		
	    $adb = PearDatabase::getInstance();
	    
	    $module = $request->getModule();
	    
	    $host = $request->get("ms_exchange_url");
	    $username = $request->get("ms_exchange_username");
	    $password = $request->get("ms_exchange_password");
	    $impersonation = $request->get("ms_exchange_user_impersonation");
	    $version = $request->get("ms_exchange_version");
	    
	    if($impersonation == 'on')
	        $impersonation = 1;
	    else 
	        $impersonation = 0;
	    
        if($impersonation){
            
            if (!MSExchange_Utils_Helper::isProtectedText($password)) {
                $password = MSExchange_Utils_Helper::toProtectedText($password);
            }
            
            $MSExchangeModel = new MSExchange_MSExchange_Model();
            
            $userImpersonation = $MSExchangeModel->checkImpersonation($request);
            
            if($userImpersonation['success']){
                
                $impersonationType = $request->get("ms_exchange_user_impersonation_type");
                
                $result = $adb->pquery("select * from vtiger_msexchange_global_settings",array());
                
                if($adb->num_rows($result)){
                    $adb->pquery("update vtiger_msexchange_global_settings set url = ?, username = ?, password = ?, exchange_version = ?, impersonate_user_account = ?, impersonation_type= ?",
                        array($host, $username, $password, $version, $impersonation, $impersonationType));
                }else{
                    $adb->pquery("insert into vtiger_msexchange_global_settings values(?,?,?,?,?,?)",array($host, $username, $password, $version, $impersonation, $impersonationType));
                }
            }
        } else {
            
            $result = $adb->pquery("select * from vtiger_msexchange_global_settings",array());
            
            if($adb->num_rows($result)){
                $adb->pquery("update vtiger_msexchange_global_settings set url = ?, exchange_version = ?, impersonate_user_account = ?",
                    array($host, $version, $impersonation));
            }else{
                $adb->pquery("insert into vtiger_msexchange_global_settings (url, exchange_version, impersonate_user_account) values(?,?,?)",array($host, $version, $impersonation));
            }
            
            $userImpersonation = array("success" => true);
        }
	    
	    $response = new Vtiger_Response();
	    $response->setResult($userImpersonation);
	    $response->emit();
    }

  
}

<?php
class RingCentral_GetUserActions_Action extends Vtiger_Mass_Action {
    
    function __construct() {
        parent::__construct();
        $this->exposeMethod('checkConnection');
        $this->exposeMethod('revokeToken');
    }
    
    function checkPermission(Vtiger_Request $request) {
        return true;
    }
    
    public function process(Vtiger_Request $request) {
        
        $mode = $request->getMode();
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
        }
        
    }
    
    public function checkConnection(Vtiger_Request $request){
        
        global $adb ;
        
        $userId = $request->get('record');
        
        $result = array('success'=> false);
        
        $check = $adb->pquery("SELECT * FROM vtiger_ringcentral_oauth WHERE userid = ?",array($userId));
        
        if($adb->num_rows($check)){
            $result = array('success'=> true);
        }
        
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
        
    }
    
    public function revokeToken(Vtiger_Request $request){
        
        global $adb;
        
        $userId = $request->get('record');
        
        $result = array('success'=> false);
        
        require_once 'modules/RingCentral/vendor/autoload.php';
        
        if(RingCentral_Config_Connector::$server == 'Sandbox')
            $rcsdk = new RingCentral\SDK\SDK(RingCentral_Config_Connector::$client_id, RingCentral_Config_Connector::$client_secret, RingCentral\SDK\SDK::SERVER_SANDBOX);
            if(RingCentral_Config_Connector::$server == 'Production')
                $rcsdk = new RingCentral\SDK\SDK(RingCentral_Config_Connector::$client_id, RingCentral_Config_Connector::$client_secret, RingCentral\SDK\SDK::SERVER_PRODUCTION);
                
                $platform = $rcsdk->platform();
                
                $ringCentral_settings_result = $adb->pquery("SELECT * FROM vtiger_ringcentral_oauth WHERE
		vtiger_ringcentral_oauth.userid = ?",array($userId));
                
                $token_data = $adb->query_result_rowdata($ringCentral_settings_result, 0);
                
                $token = array();
                
                $token['token_type'] = $token_data['token_type'];
                
                $token['expires_in'] = $token_data['access_token_expires_in'];
                
                $token['access_token'] = $token_data['access_token'];
                
                $token['refresh_token'] = $token_data['refresh_token'];
                
                $token['refresh_token_expires_in'] = $token_data['refresh_token_expires_in'];
                
                $token['access_token_expiry_time'] = strtotime($token_data['access_token_expiry_time']);
                
                $token['refresh_token_expiry_time'] = strtotime($token_data['refresh_token_expiry_time']);
                
                $platform->auth()->setData($token);
                
                $response = $platform->logout();
                
                if(!$platform->auth()->tokenType()){
                    $adb->pquery("update vtiger_ringcentral_oauth set access_token = ''
	                where userid = ?",array($userId));
                    $result = array('success'=> true);
                }
                
                $response = new Vtiger_Response();
                $response->setResult($result);
                $response->emit();
                
    }
}
?>
<?php
require_once 'modules/RingCentral/vendor/autoload.php';

class RingCentral_ValidateTokenAndGetSIP_Action extends Vtiger_Action_Controller{
    
    public function checkPermission(Vtiger_Request $request){}
    
    public function process(Vtiger_Request $request){
        
        global $adb, $current_user;
        
        if(RingCentral_Config_Connector::$server == 'Sandbox')
            $rcsdk = new RingCentral\SDK\SDK(RingCentral_Config_Connector::$client_id, RingCentral_Config_Connector::$client_secret, RingCentral\SDK\SDK::SERVER_SANDBOX);
            if(RingCentral_Config_Connector::$server == 'Production')
                $rcsdk = new RingCentral\SDK\SDK(RingCentral_Config_Connector::$client_id, RingCentral_Config_Connector::$client_secret, RingCentral\SDK\SDK::SERVER_PRODUCTION);
                
                $platform = $rcsdk->platform();
                
                $ringCentral_settings_result = $adb->pquery("SELECT * FROM vtiger_ringcentral_oauth WHERE
		vtiger_ringcentral_oauth.userid = ? and ( access_token is not NULL and access_token != '' )",array($current_user->id));
                
                if($adb->num_rows($ringCentral_settings_result)){
                    
                    $token_data = $adb->query_result_rowdata($ringCentral_settings_result, 0);
                    
                    $token = array();
                    
                    $token['token_type'] = $token_data['token_type'];
                    
                    $token['expires_in'] = $token_data['access_token_expires_in'];
                    
                    $token['access_token'] = $token_data['access_token'];
                    
                    $token['refresh_token'] = $token_data['refresh_token'];
                    
                    $token['refresh_token_expires_in'] = $token_data['refresh_token_expires_in'];
                    
                    $token['access_token_expiry_time'] = strtotime($token_data['access_token_expiry_time']);
                    
                    $token['refresh_token_expiry_time'] = strtotime($token_data['refresh_token_expiry_time']);
                    
                    $from_no = $token_data['from_no'];
                    
                    $current_time = strtotime(date("Y-m-d H:i:s"));
                    
                    $auth = $platform->auth()->setData($token);
                    
                    if($token['access_token_expiry_time'] < $current_time && $token['refresh_token_expiry_time'] > $current_time){
                        
                        try {
                            
                            $api_response = $platform->refresh();
                            
                            $token_data =  $api_response->text();
                            
                            $this->saveToken($token_data);
                            
                            $result = array('success' => true);
                            
                        } catch(Exception $e){
                            
                            $result = array('message' => 'Invalid Token');
                            
                        }
                        
                    } else {
                        
                        $result = array('success' => true);
                        
                    }
                    
                    
                } else {
                    
                    $result = array('message' => 'No Token Found');
                    
                }
                
                $response = new Vtiger_Response();
                
                if(!$result['success']){
                    
                    $response->setError($result['message']);
                    
                } else {
                    
                    $sip_request = array(array(
                        'transport'  =>  'WSS'
                    ));
                    
                    $sip_information = $platform->post('/client-info/sip-provision', array('sipInfo' => $sip_request));
                    
                    $sip = $sip_information->text();
                    
                    $sip = json_decode($sip, true);
                    
                    $response->setResult(array("sip" => $sip, "from_no" => $from_no, "client_id" => RingCentral_Config_Connector::$client_id));
                }
                
                $response->emit();
                
    }
    
    public function saveToken($token_data){
        
        global $adb, $current_user;
        
        $token = json_decode($token_data, true);
        
        $access_token = $token['access_token'];
        
        $refresh_token = $token['refresh_token'];
        
        $token_type = $token['token_type'];
        
        $refresh_token_expires_in = $token['refresh_token_expires_in'];
        
        $access_token_expires_in = $token['expires_in'];
        
        $access_token_expiry_time = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")) + ($token['expires_in'] - 60));
        
        $refresh_token_expiry_time = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")) + ($token['refresh_token_expires_in'] - 60));
        
        $current_user_id = $current_user->id;
        
        $result = $adb->pquery('SELECT * FROM vtiger_ringcentral_oauth WHERE userid = ?',array($current_user_id));
        
        if($adb->num_rows($result)){
            
            $adb->pquery("update vtiger_ringcentral_oauth set access_token = ?,
			refresh_token = ?, token_type = ?, refresh_token_expires_in = ?, access_token_expires_in = ?,
			refresh_token_expiry_time = ?, access_token_expiry_time = ? where userid = ?",
                array($access_token, $refresh_token, $token_type, $refresh_token_expires_in, $access_token_expires_in,
                    $refresh_token_expiry_time, $access_token_expiry_time, $current_user_id));
            
        } else {
            $adb->pquery("insert into vtiger_ringcentral_oauth(userid,access_token,
			refresh_token, token_type, refresh_token_expires_in, access_token_expires_in,
			refresh_token_expiry_time, access_token_expiry_time) values(?,?,?,?,?,?,?,?)",
                array($current_user_id, $access_token, $refresh_token, $token_type, $refresh_token_expires_in,
                    $access_token_expires_in,$refresh_token_expiry_time, $access_token_expiry_time));
        }
    }
    
}
?>
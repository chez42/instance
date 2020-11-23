<?php
class PandaDoc_ValidateToken_Action extends Vtiger_Action_Controller{
    
    public function checkPermission(Vtiger_Request $request){}
    
    public function process(Vtiger_Request $request){
        
        global $adb, $current_user;
        
        $pandaDoc_settings_result = $adb->pquery("SELECT * FROM vtiger_pandadoc_oauth WHERE
        vtiger_pandadoc_oauth.userid = ? and ( access_token is not NULL and access_token != '' )",
        array($current_user->id));
        
        if($adb->num_rows($pandaDoc_settings_result)){
            
            $token_data = $adb->query_result_rowdata($pandaDoc_settings_result, 0);
            
            $token = array();
            
            $token['token_type'] = $token_data['token_type'];
            
            $token['expires_in'] = $token_data['expires_in'];
            
            $token['access_token'] = $token_data['access_token'];
            
            $token['refresh_token'] = $token_data['refresh_token'];
            
            $headers = array(
                "Authorization: Bearer ".$token['access_token'],
            );
            
            $url = "https://api.pandadoc.com/public/v1/documents";
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            
            $response = curl_exec($curl);
            
            $response = json_decode($response, true);
            
            if(!isset($response['results'])){

                $client_id = PandaDoc_Config_Connector::$clientId;
                
                $client_secret = PandaDoc_Config_Connector::$clientSecret;
                
                $token_request_data = array(
                    "grant_type" => "refresh_token",
                    "refresh_token" => $token['refresh_token'],
                    "client_id" => $client_id,
                    "client_secret" => $client_secret,
                    "scope" => "read write"
                );
                
                $token_request_body = http_build_query($token_request_data);
                
                $curl = curl_init('https://api.pandadoc.com/oauth2/access_token/');
                
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                
                curl_setopt($curl, CURLOPT_POST, true);
                
                curl_setopt($curl, CURLOPT_POSTFIELDS, $token_request_body);
                
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                
                $response = curl_exec($curl);
                   
                $response = json_decode($response, true);
                
                if(isset($response['access_token'])){
                    
                    $token['access_token'] = $response['access_token'];
                    $token['refresh_token'] = $response['refresh_token'];
                    $token['token_type'] = $response['token_type'];
                    $token['expires_in'] = $response['expires_in'];
                    $this->saveToken($token);
                    $result = array('success' => true);
                    
                } else {
                    $result = array('message' => 'No Token Found');
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
            
            $response->setResult(array('success'=>true));
        }
        
        $response->emit();
                
    }
    
    
    public function saveToken($token_data){
        
        global $adb, $current_user;
        
        $access_token = $token_data['access_token'];
        
        $refresh_token = $token_data['refresh_token'];
        
        $token_type = $token_data['token_type'];
        
        $expires_in = $token_data['expires_in'];
        
        $current_user_id = $current_user->id;
        
        $tQuery = $adb->pquery("SELECT * FROM vtiger_pandadoc_oauth WHERE 
        vtiger_pandadoc_configuration.userid =?", array($current_user_id));
        
        if($adb->num_rows($tQuery)){
            
            $adb->pquery("UPDATE vtiger_pandadoc_oauth SET access_token = ?, refresh_token = ?, token_type = ?,
            expires_in = ? WHERE userid = ?", array($access_token, $refresh_token, $token_type,
            $expires_in, $current_user_id));
            
        } else {
            
            $adb->pquery("INSERT INTO vtiger_pandadoc_oauth(userid, access_token, refresh_token, token_type, expires_in)
            VALUES (?, ?, ?, ?, ?)",array($current_user_id, $access_token, $refresh_token, $token_type, $expires_in));
            
        }
        
    }
    
}
?>
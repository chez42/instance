<?php
require_once 'modules/DocuSign/vendor/autoload.php';

class DocuSign_ValidateToken_Action extends Vtiger_Action_Controller{
    
    public function checkPermission(Vtiger_Request $request){}
    
    public function process(Vtiger_Request $request){
        
        global $adb, $current_user;
        
        $config = new \DocuSign\eSign\Configuration();
        
        if(DocuSign_Config_Connector::$server == 'Sandbox'){
            $config->setHost('https://demo.docusign.net/restapi');
            $OAuth = new \DocuSign\eSign\Client\Auth\OAuth();
            $OAuth->setBasePath($config->getHost());
            $api_client = new \DocuSign\eSign\Client\ApiClient($config,$OAuth);
        }
		
        if(DocuSign_Config_Connector::$server == 'Production')
            $api_client = new \DocuSign\eSign\Client\ApiClient($config);
            
                
        $docuSign_settings_result = $adb->pquery("SELECT * FROM vtiger_document_designer_configuration WHERE
        vtiger_document_designer_configuration.userid = ? and ( access_token is not NULL and access_token != '' )",array($current_user->id));
            
        if($adb->num_rows($docuSign_settings_result)){
            
            $token_data = $adb->query_result_rowdata($docuSign_settings_result, 0);
            
            $token = array();
            
            $token['token_type'] = $token_data['token_type'];
            
            $token['expires_in'] = $token_data['expires_in'];
            
            $token['access_token'] = $token_data['access_token'];
            
            $token['refresh_token'] = $token_data['refresh_token'];
            
            $current_time = strtotime(date("Y-m-d H:i:s"));
            
            try {
                
                $userDetail = $api_client->getUserInfo($token['access_token']);
                
                $accountId = $userDetail[0]['accounts'][0]['account_id'];
                
            } catch(Exception $e){
                
                $accountId = '';
                
            }
            
            if(!$accountId || $token['expires_in'] < $current_time){
                
                try {
                    
                    $refreshTokenData = $api_client->generateRefreshAccessToken(DocuSign_Config_Connector::$client_id, DocuSign_Config_Connector::$client_secret, $token['refresh_token']);
                    
                    $token['access_token'] = $refreshTokenData[0]['access_token'];
                    $token['refresh_token'] = $refreshTokenData[0]['refresh_token'];
                    $token['token_type'] = $refreshTokenData[0]['token_type'];
                    $token['expires_in'] = $refreshTokenData[0]['expires_in'];
                    
                    $this->saveToken($refreshTokenData);
                    
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
            
            $response->setResult(array('success'=>true));
        }
        
        $response->emit();
                
    }
    
    
    public function saveToken($token_data){
        
        global $adb, $current_user;
        
        $access_token = $token_data[0]['access_token'];
        $refresh_token = $token_data[0]['refresh_token'];
        $token_type = $token_data[0]['token_type'];
        $expires_in = $token_data[0]['expires_in'];
        
        $current_user_id = $current_user->id;
        
        $result = $adb->pquery('SELECT * FROM vtiger_document_designer_configuration WHERE userid = ?',array($current_user_id));
        
        if($adb->num_rows($result)){
            
            $adb->pquery("update vtiger_document_designer_configuration set access_token = ?,
			refresh_token = ?, token_type = ?, expires_in = ? where userid = ?",
                array($access_token, $refresh_token, $token_type, $expires_in, $current_user_id));
            
        } else{
            
            $adb->pquery("insert into vtiger_document_designer_configuration(userid, access_token,
			refresh_token, token_type, expires_in) values(?,?,?,?,?)",
                array($current_user_id, $access_token, $refresh_token, $token_type, $expires_in));
            
        }
        
    }
    
}
?>
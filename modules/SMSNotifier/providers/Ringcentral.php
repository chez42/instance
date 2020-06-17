<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'modules/RingCentral/vendor/autoload.php';
class SMSNotifier_Ringcentral_Provider implements SMSNotifier_ISMSProvider_Model {
    
  
    /**
     * Function to get provider name
     * @return <String> provider name
     */
    public function getName() {
        return 'Ringcentral';
    }
    
    /**
     * Function to get required parameters other than (userName, password)
     * @return <array> required parameters list
     */
    public function getRequiredParams() {
      
    }
    
    /**
     * Function to get service URL to use for a given type
     * @param <String> $type like SEND, PING, QUERY
     */
    public function getServiceURL($type = false) {
                                            
    }
    
    /**
     * Function to set authentication parameters
     * @param <String> $userName
     * @param <String> $password
     */
    public function setAuthParameters($userName, $password) {
        
    }
    
    /**
     * Function to set non-auth parameter.
     * @param <String> $key
     * @param <String> $value
     */
    public function setParameter($key, $value) {
        
    }
    
    /**
     * Function to get parameter value
     * @param <String> $key
     * @param <String> $defaultValue
     * @return <String> value/$default value
     */
    public function getParameter($key, $defaultValue = false) {
        
    }
    
    /**
     * Function to prepare parameters
     * @return <Array> parameters
     */
    protected function prepareParameters() {
       
    }
    
    /**
     * Function to handle SMS Send operation
     * @param <String> $message
     * @param <Mixed> $toNumbers One or Array of numbers
     */
    public function send($message, $toNumbers) {
        
        global $current_user, $adb;
        
        if(!is_array($toNumbers)) {
            $toNumbers = array($toNumbers);
        }
        
        if(RingCentral_Config_Connector::$server == 'Sandbox')
            $rcsdk = new RingCentral\SDK\SDK(RingCentral_Config_Connector::$client_id, RingCentral_Config_Connector::$client_secret, RingCentral\SDK\SDK::SERVER_SANDBOX);
        if(RingCentral_Config_Connector::$server == 'Production')
            $rcsdk = new RingCentral\SDK\SDK(RingCentral_Config_Connector::$client_id, RingCentral_Config_Connector::$client_secret, RingCentral\SDK\SDK::SERVER_PRODUCTION);
            
        $platform = $rcsdk->platform();
        
        $ringCentral_settings_result = $adb->pquery("SELECT * FROM vtiger_ringcentral_oauth WHERE
        vtiger_ringcentral_oauth.userid = ? and ( access_token is not NULL and access_token != '' )",array($current_user->id));
        
        if($adb->num_rows($ringCentral_settings_result)){
            
            $token_data = $adb->query_result_rowdata($ringCentral_settings_result, 0);
            
            $fromNo = $adb->query_result($ringCentral_settings_result, 0, 'from_no');
            
            $token = array();
            
            $token['token_type'] = $token_data['token_type'];
            
            $token['expires_in'] = $token_data['access_token_expires_in'];
            
            $token['access_token'] = $token_data['access_token'];
            
            $token['refresh_token'] = $token_data['refresh_token'];
            
            $token['refresh_token_expires_in'] = $token_data['refresh_token_expires_in'];
            
            $token['access_token_expiry_time'] = strtotime($token_data['access_token_expiry_time']);
            
            $token['refresh_token_expiry_time'] = strtotime($token_data['refresh_token_expiry_time']);
            
            $platform->auth()->setData($token);
            
            $current_time = strtotime(date("Y-m-d H:i:s"));
            
            if($token['access_token_expiry_time'] < $current_time && $token['refresh_token_expiry_time'] > $current_time){
                
                try {
                    
                    $api_response = $platform->refresh();
                    
                    $token_data =  $api_response->text();
                    
                    $this->saveToken($token_data);
                    
                } catch(Exception $e){
                    
                    $result['error'] = true;
                    $result['statusmessage'] = 'Invalid Token, Please Connect Application Again!';
                    $results[] = $result;
                    
                }
                
            }
            
        } else {
            
            $result['error'] = true;
            $result['statusmessage'] = 'Invalid Token, Please Connect Application Again!';
            $results[] = $result;
            
        }
    
        foreach($toNumbers as $toNumber) {
            
            $phone_no = preg_replace("/[^0-9]/", "", $toNumber );
            
            if (strlen($phone_no) >= 10){
                
                if(strlen($phone_no) == 10) {
                    $phone_no = '1' . $phone_no;
                }
                
                $numbers[] = array('phoneNumber' => $phone_no);
                
            }
            try {
                $apiResponse = $platform->post('/account/~/extension/~/sms', array(
                    'from' => array('phoneNumber' => $fromNo),
                    'to'   => $numbers,
                    'text' => $message,
                ));
                
                $response = json_decode(html_entity_decode($apiResponse->text()), true);          
                
                $result = array();
           
                $result['id'] = $response['id'];
                $status = $response['messageStatus'];
                $result['status'] = $response['messageStatus'];
                $result['to'] = $toNumber;
                
                switch($status) {
                    case 'queued'		:
                    case 'sending'		:	$status = self::MSG_STATUS_PROCESSING;
                    break;
                    case 'sent'			:	$status = self::MSG_STATUS_DISPATCHED;
                    break;
                    case 'delivered'	:	$status = self::MSG_STATUS_DELIVERED;
                    break;
                    case 'deliveryfailed'	:
                    case 'sendingfailed'		:	$status = self::MSG_STATUS_FAILED;
                    break;
                }
                $results[] = $result;
                
            } catch(Exception $e){
                $result['error'] = true;
                $result['statusmessage'] = $e->getMessage();
                $result['to'] = $toNumber;
                $results[] = $result;
            }
        }
        return $results;
    }
    
    /**
     * Function to get query for status using messgae id
     * @param <Number> $messageId
     */
    public function query($messageId) {
        
        $result = array();
        $result['error'] = false;
      
        
        $result['status'] = '';
        $result['statusmessage'] = '';
        
        return $result;
    }
    
    function getProviderEditFieldTemplateName() {
        return 'Ringcentral.tpl';
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
            
        } else{
            
            $adb->pquery("insert into vtiger_ringcentral_oauth(userid,access_token,
			refresh_token, token_type, refresh_token_expires_in, access_token_expires_in,
			refresh_token_expiry_time, access_token_expiry_time) values(?,?,?,?,?,?,?,?)",
                array($current_user_id, $access_token, $refresh_token, $token_type, $refresh_token_expires_in,
                    $access_token_expires_in,$refresh_token_expiry_time, $access_token_expiry_time));
            
        }
        
        
    }
    
}
?>
<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

require_once 'modules/RingCentral/vendor/autoload.php';

class RingCentral_MassSaveAjax_Action extends Vtiger_Mass_Action {
    
    function checkPermission(Vtiger_Request $request) {
        return true;
    }
    
    public function process(Vtiger_Request $request) {
        
        global $adb,$site_URL;
        
        global $current_user;
        
        $moduleName = $request->getModule();
        
        $recordIds = $this->getRecordsListFromRequest($request);
        
        $phoneFieldList = $request->get('fields');
        
        $message = $request->get('message');
        
        $all_num = array();
        
        if(!$request->get('number')){
            
            foreach($recordIds as $recordId) {
                
                $recordModel = Vtiger_Record_Model::getInstanceById($recordId);
                
                $numberSelected = false;
                
                foreach($phoneFieldList as $fieldname) {
                    $fieldValue = $recordModel->get($fieldname);
                    if(!empty($fieldValue)) {
                        $toNumbers[] = $fieldValue;
                        $numberSelected = true;
                    }
                }
                
                if($numberSelected) {
                    $all_num[$recordId] = $toNumbers;
                    unset($toNumbers);
                }
                
            }
            
        }else if($request->get('number')){
            $all_num[$request->get('record')] = array($request->get('number'));
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
                    
                    $result = array('message' => 'Invalid Token, Please Connect Application Again!');
                    
                    $response = new Vtiger_Response();
                    
                    $response->setError($result['message']);
                    
                    $response->emit();
                    
                    exit;
                    
                }
                
            }
            
        } else {
            
            $result = array('message' => 'Invalid Token, Please Connect Application Again!');
            
            $response = new Vtiger_Response();
            
            $response->setError($result['message']);
            
            $response->emit();
            
            exit;
            
        }
        
        $crmid_phone_no_mapping = array();
        
        foreach($all_num as $crmid => $phoneNos){
           
            foreach($phoneNos as $phoneNo){
               
                $phone_no = preg_replace("/[^0-9]/", "", $phoneNo );
                
                if (strlen($phone_no) >= 10){
                    
                    if(strlen($phone_no) == 10) {
                        $phone_no = '1' . $phone_no;
                    }
                    
                    $numbers[] = array('phoneNumber' => $phone_no);
                    
                    $crmid_phone_no_mapping[$phone_no] = $crmid;
                    
                }
            }
        }
       
        
        if(empty($numbers)){
            $response = new Vtiger_Response();
            $response->setError('NO Valid No Found');
            $response->emit();
            exit;
        }
        
        
        try {
            
            if($request->get('type') == 'sms'){
                
                $apiResponse = $platform->post('/account/~/extension/~/sms', array(
                    'from' => array('phoneNumber' => $fromNo),
                    'to'   => $numbers,
                    'text' => $message,
                ));
                
            } else if($request->get('type') == 'fax'){
                
                $_FILES = Vtiger_Util_Helper::transformUploadedFiles($_FILES, true);
                
                $filePath = 'cache/faxfiles/';
                
                if(!is_dir('cache/faxfiles')){
                    mkdir($filePath);
                }
                
                $faxfilePath = '';
                
                if(!empty($_FILES['faxfile'])){
                    
                    $files = $_FILES['faxfile'];
                    
                    if($files['name'] != '' && $files['size'] > 0){
                        
                        if (!is_dir($filePath)) {
                            
                            mkdir($filePath);
                            
                            $upload_file_path = $filePath . '/';
                            
                        } else {
                            
                            $upload_file_path = $filePath . '/';
                            
                        }
                        
                        $faxfilePath = $upload_file_path . strtotime(date("Y-m-d H:i:s")) . '_' . from_html(preg_replace('/\s+/', '_',$files['name']));
                        
                        move_uploaded_file($files['tmp_name'], $faxfilePath);
                        
                    }
                    
                }
                
                if($faxfilePath){
                    
                    $faxRequest = $rcsdk->createMultipartBuilder()->setBody(array(
                        'to'         => $numbers,
                        'faxResolution' => 'High',
                    ))
                    ->add(fopen($site_URL.$faxfilePath, 'r'))
                    ->request('/account/~/extension/~/fax');
                    
                } else {
                    
                    $faxRequest = $rcsdk->createMultipartBuilder()->setBody(array(
                        'to'         => $numbers,
                        'faxResolution' => 'High',
                    ))->add($message, 'file.txt')->request('/account/~/extension/~/fax');
                    
                    
                }
                
                $apiResponse = $platform->sendRequest($faxRequest);
                
                unlink($faxfilePath);
                
            }
            
            $response = json_decode(html_entity_decode($apiResponse->text()), true);
            
            
            $result = array('success' => true);
            
        } catch(Exception $e){
            $result = array('message' => $e->getMessage());
        }
        
        $response = new Vtiger_Response();
        $response->setResult($result);
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
            
        } else{
            
            $adb->pquery("insert into vtiger_ringcentral_oauth(userid,access_token,
			refresh_token, token_type, refresh_token_expires_in, access_token_expires_in,
			refresh_token_expiry_time, access_token_expiry_time) values(?,?,?,?,?,?,?,?)",
                array($current_user_id, $access_token, $refresh_token, $token_type, $refresh_token_expires_in,
                    $access_token_expires_in,$refresh_token_expiry_time, $access_token_expiry_time));
            
        }
        
        
    }
}
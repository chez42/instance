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
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        
        $currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        if(!$currentUserPriviligesModel->hasModuleActionPermission($moduleModel->getId(), 'Save')) {
            throw new AppException(vtranslate($moduleName, $moduleName).' '.vtranslate('LBL_NOT_ACCESSIBLE'));
        }
    }
    
    public function process(Vtiger_Request $request) {
        global $adb,$site_URL;
        
        $moduleName = $request->getModule();
        
        $recordIds = $this->getRecordsListFromRequest($request);
        $phoneFieldList = $request->get('fields');
        $message = $request->get('message');
        
        $all_num = array();
        
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
        
        $rcsdk = new RingCentral\SDK\SDK(RingCentral_Config_Connector::$client_id, RingCentral_Config_Connector::$client_secret, RingCentral\SDK\SDK::SERVER_SANDBOX);
        
        $platform = $rcsdk->platform();
        
        global $adb,$current_user;
        
        $accessToken = '';
        
        $fromNo = '';
        
        $ringCentral = $adb->pquery("SELECT * FROM vtiger_ringcentral_settings WHERE
        vtiger_ringcentral_settings.userid = ?",array($current_user->id));
        
        if($adb->num_rows($ringCentral)){
            $accessToken = $adb->query_result($ringCentral, 0, 'token');
            $fromNo = $adb->query_result($ringCentral, 0, 'from_no');
        }
        
        if($fromNo){
            
            $crmid_phone_no_mapping = array();
            
            $token = json_decode(html_entity_decode($accessToken), true);
            
            $auth = $platform->auth()->setData($token);
            
            if($_SESSION['RingCentralTokenTime'] < date("h:i")){
           
                $api_response = $platform->refresh();
                
                $token = $api_response->text();
                
                $token = json_decode($token, true);
                
                $auth = $platform->auth()->setData($token);
                
                $current_user_id = $_SESSION['authenticated_user_id'];
                
                $adb->pquery('update vtiger_ringcentral_settings set token = ?
    			where userid = ?',array(json_encode($token),$current_user_id));
                
                $_SESSION['RingCentralTokenTime'] = date("h:i",strtotime('+55 minutes'));
                
            }
            
            foreach($all_num as $crmid => $phoneNos){
                
                foreach($phoneNos as $phoneNo){
                    
                    $phone_no = preg_replace("/[^0-9]/", "", $phoneNo );
                    
                    if (strlen($phone_no) == 10 || strlen($phone_no) == 11){
                        
                        if(strlen($phone_no) == 11) {
                            
                            // If a Non US no then Ignore
                            if($phone_no[0] != 1){
                                continue;
                            }
                            
                        } else if(strlen($phone_no) == 10) {
                            $phone_no = '1' . $phone_no;
                        }
                        
                        $numbers[] = array('phoneNumber' => $phone_no);
                        
                        $crmid_phone_no_mapping[$phone_no] = $crmid;
                        
                    }
                    
                }
                
            }
           
            try {
                
                if($request->get('type') == 'sms'){
                    
                    $apiResponse = $rcsdk->platform()->post('/account/~/extension/~/sms', array(
                        'from' => array('phoneNumber' => $fromNo),
                        'to'   => $numbers,
                        'text' => $message,
                    ));
                    
                }else if($request->get('type') == 'fax'){
                    
                    $_FILES = Vtiger_Util_Helper::transformUploadedFiles($_FILES, true);
                    
                    $filePath = 'storage/faxfiles/';
                    if(!is_dir('storage/faxfiles')){
                        mkdir($filePath);
                    }
                    
                    $faxfilePath = '';
                    if(!empty($_FILES['faxfile'])){
                        $files = $_FILES['faxfile'];
                        
                        if($files['name'] != '' && $files['size'] > 0){
                            
                            if (!is_dir($filePath . $current_user->id)) {
                                mkdir($filePath . $current_user->id);
                                $upload_file_path = $filePath.$current_user->id.'/';
                            }else{
                                $upload_file_path = $filePath.$current_user->id.'/';
                            }
                            
                            $faxfilePath = $upload_file_path . implode('_',$recordIds). "_fax_" . $files['name'];
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
                    }else{
                        $faxRequest = $rcsdk->createMultipartBuilder()->setBody(array(
                            'to'         => $numbers,
                            'faxResolution' => 'High',
                        ))
                        ->add($message, 'file.txt')
                        ->request('/account/~/extension/~/fax');
                    }
                    $apiResponse = $rcsdk->platform()->sendRequest($faxRequest);
                   
                }
                
                $response = json_decode(html_entity_decode($apiResponse->text()), true);
                
                foreach($response['to'] as $to){
                    
                    $toNo = str_replace('+', '', $to['phoneNumber']);
                    
                    $crmId = $crmid_phone_no_mapping[$toNo];
                    
                    $type = $response['type'];
                    
                    $ringId = $response['id'];
                    
                    $createdTime = $response['creationTime'];
                    
                    $status = $response['messageStatus'];
                    
                    $content = $message;
                    if($faxfilePath)
                        $content = $site_URL.$faxfilePath;
                    
                    $adb->pquery("INSERT INTO vtiger_ringcentral_logs ( crmid, type, ringcentral_id, status, content, created_date, tono, user_id)
					VALUES (?,?,?,?,?,?,?,?)",array($crmId, $type, $ringId, $status, $content, $createdTime, $toNo, $current_user->id));
                    
                }
               
                $result = array('success' => true,'message' => 'Message Send Successfully');
                
            } catch(Exception $e){
                $result = array('success' => false,'message' => $e->getMessage());
                
            }
            
        } else {
            
            $result = array('success' => false, 'message' => 'From No Cant be Empty');
            
        }
        
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
        
    }
    
    
}

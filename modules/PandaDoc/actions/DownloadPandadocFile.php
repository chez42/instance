<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
header("Access-Control-Allow-Origin: *");
class PandaDoc_DownloadPandadocFile_Action extends Vtiger_Action_Controller {
    
    function __construct() {
        parent::__construct();
        $this->exposeMethod("SyncWithCrm");
    }
    
    function checkPermission(Vtiger_Request $request) {
        return true;
    }
    
    public function process(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        
        $mode = $request->get('mode');
        if($mode) {
            $this->invokeExposedMethod($mode, $request);
            return;
        } 
        
        $this->downloadFile($request->get('record'), $request->get('name'));
        
    }
    
    function documentDocument($headers, $id){
        
        $url = "https://api.pandadoc.com/public/v1/documents/$id/download";
        
        $curl = curl_init($url);
        
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($curl);
        
        return $response;
        
    }
    
    public function downloadFile($attachmentId = false, $name=false) {
        
        global $adb, $current_user;
        
        $fileContent = false;
        
        $pandadoc_settings_result = $adb->pquery("SELECT * FROM vtiger_pandadoc_oauth WHERE
        (access_token is not NULL and access_token != '' ) AND userid = ?",array($current_user->id));
        
        if($adb->num_rows($pandadoc_settings_result)){
            
            
            $token_data = $adb->query_result_rowdata($pandadoc_settings_result, 0);
            
            $user_id = $token_data['userid'];
            
            $token = $this->validateToken($token_data);
            
            if($token){
                
                $headers = array(
                    "Authorization: Bearer ".$token['access_token']
                );
                
                $docFile = $this->documentDocument($headers, $attachmentId);
                
                $path = 'cache/'.$name.'.pdf';
                
                if($docFile){
                    file_put_contents($path, $docFile);
                    
                    while(ob_get_level()) {
                        ob_end_clean();
                    }
                    $fileSize = filesize($path);
                    
                    $fileSize = $fileSize + ($fileSize % 1024);
                    $fileName = $name.'.pdf';
                    if (fopen($path, "r")) {
                        $fileContent = fread(fopen($path, "r"), $fileSize);
                        header("Content-type: application/pdf");
                        header("Pragma: public");
                        header("Cache-Control: private");
                        header("Content-Disposition: attachment; filename=\"$fileName\"");
                        header("Content-Description: PHP Generated Data");
                        header("Content-Encoding: none");
                    }
                }
            }
            
        }
        
        echo $fileContent;
    }
    
    function SyncWithCrm(Vtiger_Request $request){
        
        global $adb, $current_user;
        
        $docId = $request->get('record'); 
        
        $name = $request->get('name');
        
        $record = $request->get('src_record');
        
        $crm_reference = $request->get('crm_reference');
        
        $success = false;
        
        $docQuery = $adb->pquery("SELECT documentid FROM vtiger_pandadocdocument_reference WHERE  crmid=? AND crm_reference = ?",
            array($record, $crm_reference));
        $crmDocId = '';
        if($adb->num_rows($docQuery)){
            $crmDocId = $adb->query_result($docQuery, 0, 'documentid');    
        }
        
        if(!$crmDocId){
            $pandadoc_settings_result = $adb->pquery("SELECT * FROM vtiger_pandadoc_oauth WHERE
            (access_token is not NULL and access_token != '' ) AND userid = ?",array($current_user->id));
            
            if($adb->num_rows($pandadoc_settings_result)){
                
                $token_data = $adb->query_result_rowdata($pandadoc_settings_result, 0);
                
                $user_id = $token_data['userid'];
                
                $token = $this->validateToken($token_data);
                
                if($token){
                    
                    $headers = array(
                        "Authorization: Bearer ".$token['access_token']
                    );
                    
                    $docFile = $this->documentDocument($headers, $docId);
                    
                    if($docFile){
                        
                        $upload_file_path = decideFilePath();
                        
                        $current_id = $adb->getUniqueID("vtiger_crmentity");
                        
                        $filename = $upload_file_path.$current_id.'_'.str_replace(' ','_', $name).'.pdf';
                        
                        file_put_contents($filename, $docFile);
                        
                        $fileSize = filesize($filename);
                        
                        $fileSize = $fileSize + ($fileSize % 1024);
                        
                        $focus = CRMEntity::getInstance('Documents');
                        
                        $focus->column_fields['notes_title'] = $name;
                        
                        $focus->column_fields['assigned_user_id'] = $user_id;
                        
                        $focus->column_fields['filename'] = str_replace(' ','_',$name).'.pdf';
                        
                        $focus->column_fields['filetype'] = 'application/pdf';
                        
                        $focus->column_fields['filelocationtype'] = 'I';
                        
                        $focus->column_fields['filesize'] = $fileSize;
                        
                        $focus->column_fields['filestatus'] = 1;
                        
                        $focus->saveentity('Documents');
                        
                        if($focus->id){
                            
                            $adb->pquery("INSERT INTO vtiger_senotesrel(crmid, notesid) VALUES (?, ?)",array($record, $focus->id));
                            
                            $date_var = date("Y-m-d H:i:s");
                            
                            $sql1 = "INSERT INTO vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) VALUES (?, ?, ?, ?, ?, ?, ?)";
                            $params1 = array($current_id, $user_id, $user_id, 'Documents Attachment', 'PandaDoc Document', $adb->formatDate($date_var, true), $adb->formatDate($date_var, true));
                            $adb->pquery($sql1, $params1);
                            
                            $sql2 = "INSERT INTO vtiger_attachments(attachmentsid, name, description, type, path) values(?, ?, ?, ?, ?)";
                            $params2 = array($current_id, str_replace(' ','_',$name).'.pdf' , 'PandaDoc Document', 'application/pdf', $upload_file_path);
                            $adb->pquery($sql2, $params2);
                            
                            $sql3 = 'INSERT INTO vtiger_seattachmentsrel VALUES(?,?)';
                            $params3 = array($focus->id, $current_id);
                            $adb->pquery($sql3, $params3);
                            
                            $adb->pquery("UPDATE vtiger_pandadocdocument_reference SET documentid=? WHERE crm_reference=? and crmid=?",
                                array($focus->id, $crm_reference, $record));
                           
                            $success = true;
                        }
                    }
                }
            }
        }
        
        $response = new Vtiger_Response();
        $response->setResult(array('success'=>$success));
        $response->emit();
        
    }
    
    function validateToken($token){
        
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
                
                $token['userid'] = $token['userid'];
                
                $this->saveToken($token);
                
                return $token;
                
            } else {
                return '';
            }
            
        } else {
            return $token;
        }
        
    }
    
    function saveToken($token_data){
        
        global $adb, $current_user;
        
        $access_token = $token_data['access_token'];
        
        $refresh_token = $token_data['refresh_token'];
        
        $token_type = $token_data['token_type'];
        
        $expires_in = $token_data['expires_in'];
        
        $current_user_id = $token_data['userid'];
        
        $tQuery = $adb->pquery("SELECT * FROM vtiger_pandadoc_oauth WHERE
        vtiger_pandadoc_oauth.userid =?", array($current_user_id));
        
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
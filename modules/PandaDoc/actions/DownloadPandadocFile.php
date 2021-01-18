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
    
    function checkPermission(Vtiger_Request $request) {
        return true;
    }
    
    public function process(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        echo"<pre>";print_r($request);echo"</pre>";
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
            
            if(!$token)
                continue;
                
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
                if (fopen($path.$savedFile, "r")) {
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
       
        echo $fileContent;
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
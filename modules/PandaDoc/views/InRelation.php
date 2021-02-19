<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class PandaDoc_InRelation_View extends Vtiger_RelatedList_View {
   
    function process(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $relatedModuleName = $request->get('relatedModule');
        $parentId = $request->get('record');
        $label = $request->get('tab_label');
        
        $documentList = $this->getPandaDocDocuments($request);
        
        $relatedModuleModel = Vtiger_Module_Model::getInstance($relatedModuleName);
        $moduleFields = $relatedModuleModel->getFields();
        $fieldsInfo = array();
        foreach ($moduleFields as $fieldName => $fieldModel) {
            $fieldsInfo[$fieldName] = $fieldModel->getFieldInfo();
        }
        
        $fieldsInfo = array_merge($contactFieldInfo,$fieldsInfo);
        
        $viewer = $this->getViewer($request);
        $viewer->assign('RELATED_FIELDS_INFO', json_encode($fieldsInfo));
        $viewer->assign('RELATED_RECORDS', $documentList);
        $viewer->assign('RELATED_MODULE', $relatedModuleModel);
        $viewer->assign('VIEW', $request->get('view'));
        $viewer->assign('PARENT_ID', $parentId);
      
        $viewer->assign('TAB_LABEL', $request->get('tab_label'));
        
        $customView = new CustomView();
        $cvId = $customView->getViewIdByName('All',$relatedModuleName);
        
        $viewer->assign('CVID', $cvId);

		return $viewer->view('PandaDocRelatedList.tpl', $relatedModuleName, 'true');
	}
	
	function getPandaDocDocuments($request){
	    
	    $record = $request->get('record');
	    
	    global $adb, $current_user;
	    
	    $documentsArray = array();
	    
	    $pandadoc_settings_result = $adb->pquery("SELECT * FROM vtiger_pandadoc_oauth WHERE
        (access_token is not NULL and access_token != '' ) AND userid = ?",array($current_user->id));
	    
	    if($adb->num_rows($pandadoc_settings_result)){
	        
	        for($i = 0; $i < $adb->num_rows($pandadoc_settings_result); $i++){
	            
	            $token_data = $adb->query_result_rowdata($pandadoc_settings_result, $i);
	            
	            $user_id = $token_data['userid'];
	            
	            $token = $this->validateToken($token_data);
	            
	            if(!$token)
	                continue;
	                
                $headers = array(
                    "Authorization: Bearer ".$token['access_token']
                );
                
                $reference_result = $adb->pquery("select * from vtiger_pandadocdocument_reference
                where userid = ? AND crmid =?", array($user_id, $record));
                
                if($adb->num_rows($reference_result)){
                    
                    for($index = 0; $index < $adb->num_rows($reference_result); $index++){
                        
                        $meta_data_reference = $adb->query_result($reference_result, $index, "crm_reference");
                        
                        $url = "https://api.pandadoc.com/public/v1/documents?metadata_reference=".$meta_data_reference;
                        
                        $curl = curl_init($url);
                        
                        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                        
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        
                        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                        
                        $response = curl_exec($curl);
                        
                        $response = json_decode($response, true);
                        
                        if(isset($response['results'][0]['id'])){
                            
                            $docId = $response['results'][0]['id'];
                            
                            $details = $this->getDocumentDetails($headers, $docId);
                            
                            $crm_reference = $details['metadata']['CRM_REFERENCE'];
                            
                            $recipient = array();
                            foreach($details['recipients'] as $recipients){
                                $recipient[] = $recipients['email'];
                            }
                            $documentsArray[] = array(
                                'crm_reference' => $meta_data_reference,
                                'doc_id' => $details['id'],
                                'name' => $details['name'],
                                'status' => $details['status'],
                                'recipient' => implode(', ', $recipient),
                                'sent_by' => $details['sent_by']['first_name'] . ' ' . $details['sent_by']['last_name'],
                                'date_created' => Vtiger_Datetime_UIType::getDisplayValue(date('Y-m-d H:i:s',strtotime($details['date_created']))),
                                'date_modified' => Vtiger_Datetime_UIType::getDisplayValue(date('Y-m-d H:i:s',strtotime($details['date_modified'])))
                            );
                        }
                    }
                }
	        }
	    }
	    return $documentsArray;
	}
	
	function getDocumentDetails($headers, $id){
	    
	    $crm_reference = '';
	    
	    $url = "https://api.pandadoc.com/public/v1/documents/$id/details";
	    
	    $curl = curl_init($url);
	    
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	    
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    
	    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	    
	    $response = curl_exec($curl);
	    
	    $response = json_decode($response, true);
	    
	    if(isset($response['id'])){
	        $crm_reference = $response['metadata']['CRM_REFERENCE'];
	    }
	    
	    return $response;
	    
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

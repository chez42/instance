<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

require_once('modules/com_vtiger_workflow/VTEntityCache.inc');
require_once('modules/com_vtiger_workflow/VTWorkflowUtils.php');
require_once('modules/com_vtiger_workflow/VTSimpleTemplate.inc');
require_once 'modules/RingCentral/vendor/autoload.php';

class RingcentralSMSTask extends VTTask {
	public $executeImmediately = true; 
	
	public function getFieldNames(){
		return array('content', 'sms_recepient');
	}
	
	public function doTask($entity){
		
	    global $current_user, $adb;
	    
	    $userIdCom = vtws_getIdComponents($entity->get('assigned_user_id'));
	    
	    $ringCentral_settings_result = $adb->pquery("SELECT * FROM vtiger_ringcentral_oauth WHERE
        vtiger_ringcentral_oauth.userid = ? and ( access_token is not NULL and access_token != '' )",array($userIdCom[1]));
	    
	    if($adb->num_rows($ringCentral_settings_result)){
			
			global $adb, $current_user,$log;
			
			$util = new VTWorkflowUtils();
			$admin = $util->adminUser();
			$ws_id = $entity->getId();
			$entityCache = new VTEntityCache($admin);
			
			$et = new VTSimpleTemplate($this->sms_recepient);
			$recepient = $et->render($entityCache, $ws_id);
			$recepients = explode(',',$recepient);
			$relatedIds = $this->getRelatedIdsFromTemplate($this->sms_recepient, $entityCache, $ws_id);
			$relatedIds = explode(',', $relatedIds);
			$relatedIdsArray = array();
			foreach ($relatedIds as $entityId) {
				if (!empty($entityId)) {
					list($moduleId, $recordId) = vtws_getIdComponents($entityId);
					if (!empty($recordId)) {
						$relatedIdsArray[] = $recordId;
					}
				}
			}

			$ct = new VTSimpleTemplate($this->content);
			$content = $ct->render($entityCache, $ws_id);
			$relatedCRMid = substr($ws_id, stripos($ws_id, 'x')+1);
			$relatedIdsArray[] = $relatedCRMid;
			
			$relatedModule = $entity->getModuleName();
			
			/** Pickup only non-empty numbers */
			$tonumbers = array();
			foreach($recepients as $tonumber) {
				if(!empty($tonumber)) $tonumbers[] = $tonumber;
			}
			
			//As content could be sent with HTML tags.
			$content = strip_tags(br2nl($content));

			$this->smsNotifierId = $this->sendsms($content, $tonumbers, $userIdCom[1]);
			$util->revertUser();
		}
		
	}

	public function getRelatedIdsFromTemplate($template, $entityCache, $entityId) {
		$this->template = $template;
		$this->cache = $entityCache;
		$this->parent = $this->cache->forId($entityId);
		return preg_replace_callback('/\\$(\w+|\((\w+) : \(([_\w]+)\) (\w+)\))/', array($this,"matchHandler"), $this->template);
	}

	public function matchHandler($match) {
		preg_match('/\((\w+) : \(([_\w]+)\) (\w+)\)/', $match[1], $matches);
		// If parent is empty then we can't do any thing here
		if(!empty($this->parent)){
			if(count($matches) != 0){
				list($full, $referenceField, $referenceModule, $fieldname) = $matches;
				$referenceId = $this->parent->get($referenceField);
				if($referenceModule==="Users" || $referenceId==null){
					$result ="";
				} else {
					$result = $referenceId;
				}
			}
		}
		return $result;
	}
	
	
	public function sendsms($message, $toNumbers, $userId) {
	    
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
        vtiger_ringcentral_oauth.userid = ? and ( access_token is not NULL and access_token != '' )",
            array($userId));
        
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
                    
                    $this->saveToken($token_data, $userId);
                    
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
	
	public function saveToken($token_data, $userId){
	    
	    global $adb, $current_user;
	    
	    $token = json_decode($token_data, true);
	    
	    $access_token = $token['access_token'];
	    
	    $refresh_token = $token['refresh_token'];
	    
	    $token_type = $token['token_type'];
	    
	    $refresh_token_expires_in = $token['refresh_token_expires_in'];
	    
	    $access_token_expires_in = $token['expires_in'];
	    
	    $access_token_expiry_time = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")) + ($token['expires_in'] - 60));
	    
	    $refresh_token_expiry_time = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")) + ($token['refresh_token_expires_in'] - 60));
	    
	    $current_user_id = $userId;
	    
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

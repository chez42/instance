<?php
include("includes/main/WebUI.php");

error_reporting(0);
ini_set('display_errors',0); 

global $site_URL;



class ewsService {
    
	public function SendNotification( $arg ) {
       		
		global $site_URL;
		
		$adb = PearDatabase::getInstance();
		
		$events = new OmniCal_ExchangeEvent_Model();
	
		$is_subscribed = $userid = false;
		
		$response = json_decode(json_encode($arg),true);
		
		$subscriptionId = $response['ResponseMessages']['SendNotificationResponseMessage']['Notification']['SubscriptionId'];
			
		$sub_result = $adb->pquery("select userid from vtiger_user_subscription where subscriptionid = ?",array($subscriptionId));
		
		if($adb->num_rows($sub_result) == 1){
			
			$userid = $adb->query_result($sub_result,0,'userid');
			
			$is_subscribed = true;

		} else	
			$is_subscribed = false;
		
		if($adb->num_rows($sub_result) == 1){
			file_put_contents("/var/www/vt71/store/".$userid."_logfile_".time().".txt", print_r($arg,1));	
		}
		
		
		if($is_subscribed) {
		
			$postParams = array('userid' => $userid, "data" => json_encode($arg));
			
			$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_URL, $site_URL."/ews_notification_sync.php");
			
			curl_setopt($ch, CURLOPT_POST, true);
					
			curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
			
			$response = curl_exec($ch);
			
			curl_close($ch);
			
			
		}

		$result = new EWSType_SendNotificationResultType();
       	
		if($is_subscribed)
			$result->SubscriptionStatus = 'OK';
		else
			$result->SubscriptionStatus = 'Unsubscribe';
			
        return $result;
    }
}

$server = new SoapServer( 'libraries/exchange_ews/wsdl/NotificationService.wsdl', array(    'uri' => $site_URL.'/ews_notification_listener.php'));
$server->setObject( $service = new ewsService() );
$server->handle();
?>

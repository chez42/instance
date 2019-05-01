<?php

require_once 'modules/RingCentral/vendor/autoload.php';

class RingCentral_UpdateStatus_Action extends Vtiger_Action_Controller{
    
    public function checkPermission(Vtiger_Request $request){}
    
    public function process(Vtiger_Request $request){
        
        global $adb,$current_user;
        
        $ringId = $request->get('ringid');
        $crmId = $request->get("crmid");
       
        $rcsdk = new RingCentral\SDK\SDK(RingCentral_Config_Connector::$client_id, RingCentral_Config_Connector::$client_secret, RingCentral\SDK\SDK::SERVER_SANDBOX);
        
        $platform = $rcsdk->platform();
        $accessToken = '';
        $fromNo='';
        $ringCentral = $adb->pquery("SELECT * FROM vtiger_ringcentral_settings WHERE vtiger_ringcentral_settings.userid = ?",array($current_user->id));
        if($adb->num_rows($ringCentral)){
            $accessToken = $adb->query_result($ringCentral, 0, 'token');
        }
        
        $token = json_decode(html_entity_decode($accessToken), true);
        
        $auth = $platform->auth()->setData($token);
        
        $respon = $platform->get("/account/~/extension/~/message-store/{$ringId}" );
        
        $msgStatus = $respon->json()->messageStatus;
        
        $success = array('success'=>false,'message'=>'Something went wrong try again');
        
        
        $result = $adb->pquery('SELECT * FROM vtiger_ringcentral_logs
		WHERE ringcentral_id = ? AND crmid = ? AND user_id = ?',array($ringId, $crmId, $current_user->id));
        
        if($adb->num_rows($result)){
            $logId = $adb->query_result($result, 0, 'id');
            $adb->pquery('UPDATE vtiger_ringcentral_logs SET status=? WHERE id=?',array($msgStatus,$logId));
            $success = array('success'=>true,'message'=>'Status Update successfully','status'=>$msgStatus);
        }
        
        $response = new Vtiger_Response();
        $response->setResult($success);
        $response->emit();
        
    }
}
?>

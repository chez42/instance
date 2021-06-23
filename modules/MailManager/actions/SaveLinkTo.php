<?php
class MailManager_SaveLinkTo_Action extends Vtiger_Action_Controller {
    
   
    function checkPermission(Vtiger_Request $request) {
        return true;
    }
    
    public function process(Vtiger_Request $request) {
        
        $response = new Vtiger_Response();
        $folderName = $request->get('folder');
        $idList = explode(',',$request->get('idList'));
        $linkto = $request->get('parent_id');
        
        $model = MailManager_Mailbox_Model::activeInstance();
        $connector = MailManager_Connector_Connector::connectorWithModel($model, $folderName);
       
        if(!empty($idList)){
            
            foreach ($idList as $msgno){
                
                $mail = $connector->openMail($msgno, $folderName);
                $mail->attachments(); 
                
                $linkedto = MailManager_Relate_Action::associate($mail, $linkto);
                
            }
            
        }
        
        $response->setResult(array("success"=>true,"message"=>"Successfully Linked Mails"));
        $response->emit();
    }
    
    
    
}
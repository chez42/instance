<?php
require_once("libraries/PHPMailer/cOmniMailer.php");

class AdvisorDirect_Send_Action extends Vtiger_SaveAjax_Action{
    public function process(Vtiger_Request $request) {
        $model = new AdvisorDirect_Module_Model();
        $custodians = $model->getCustodianInfoFromFax($request->get('to'));
        foreach($custodians AS $k => $v){
            $to = array("" => $v['fax_number'] . $v['to_fax_number']);
            $from = array($request->get('from') => $v['from_fax_number']);
            $reply = array($request->get('from') => $v['from_fax_number']);
        }
        $attachment_id = $request->get('attachment_id');
        $mail = new cOmniMailer();
        $subjects = $request->get('subject');
        $body = $request->get('body');
        
        if($attachment_id){
            
            $db = PearDatabase::getInstance();
            $params = array($attachment_id);

            $query = "SELECT attachmentsid, name, path FROM vtiger_attachments 
                      WHERE attachmentsid=(SELECT attachmentsid FROM vtiger_seattachmentsrel WHERE crmid=?)";
            $result = $db->pquery($query, $params);
                        
            if($db->num_rows($result) > 0)
            {              
                $attachments_id = $db->query_result($result, 0, "attachmentsid");
                $path = $db->query_result($result, 0, "path");
                $filename = $db->query_result($result, 0, "name");
                $mail->AddAttachment("{$path}{$attachments_id}_{$filename}");
            }
        }
        
        $m = $mail->SendEmail($to, $from, $reply, null, $subject, $body);
        
        $result = array("status"=>$m);
        $result = json_encode($result);
        echo $result;
    }
    
    public function sendFax(Vtiger_Request $request){
        
    }
}
/*
require_once('include/utils/PHPMailer/cOmniMailer.php');
require_once("modules/AdvisorDirect/classes/cAdvisorDirect.php");
require_once('Smarty_setup.php');

$mail = new cOmniMailer();
$ad = new cAdvisorDirect();

$result = $ad->GetCustodianInfoFromFax($_REQUEST['to']);

foreach($result AS $k => $v){
    $to = array("" => $v['fax_number'] . $v['to_fax_number']);
    $from = array($_REQUEST['from'] => $v['from_fax_number']);
    $reply = array($_REQUEST['from'] => $v['from_fax_number']);
}

$subject = $_REQUEST['subject'];
$body = $_REQUEST['body'];

$attachment_id = $_REQUEST['attachment_id'];

if($attachment_id){
    global $adb;
    $query = "SELECT attachmentsid, name, path FROM vtiger_attachments 
              WHERE attachmentsid=(SELECT attachmentsid FROM vtiger_seattachmentsrel WHERE crmid=?)";
    $result = $adb->pquery($query, array($attachment_id));
    if($adb->num_rows($result) > 0)
    {
        $attachments_id = $adb->query_result($result, 0, "attachmentsid");
        $path = $adb->query_result($result, 0, "path");
        $filename = $adb->query_result($result, 0, "name");
        $mail->AddAttachment("{$path}{$attachments_id}_{$filename}");
    }
}

$smarty = new vtigerCRM_Smarty;

$smarty->assign("ATTACHMENT_ID", $attachment_id);
$mail->SendEmail($to, $from, $reply, null, $subject, $body);

$smarty->display('AdvisorDirectConfirmation.tpl');
 */
?>

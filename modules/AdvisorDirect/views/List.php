<?php

class AdvisorDirect_List_View extends Vtiger_Index_View {
        // We are overriding the default SideBar UI to list our feeds.
        public function preProcess(Vtiger_Request $request, $display = true) {
                return parent::preProcess($request, $display);
        }
        
        public function process(Vtiger_Request $request) {
            global $current_user;
            $model = new AdvisorDirect_Module_Model();
            $custodians = $model->GetCustodianList();
            $user = Users_Record_Model::getCurrentUserModel();
            $name = $user->getName();
            $email = $user->get('email1');

            $attachment_id = $request->get("attachments");

            $viewer = $this->getViewer($request);
            $viewer->assign("CUSTODIANS", $custodians);
            $viewer->assign("ATTACHMENT_ID", $attachment_id);
            $viewer->assign("EMAIL", $email);
            $viewer->assign("USER_NAME", $name);
            $viewer->assign("AUTHORIZED", 1);
            $viewer->view('EmailForm.tpl', $request->getModule());
            
            parent::process($request);
        }

        // Injecting custom javascript resources
        public function getHeaderScripts(Vtiger_Request $request) {
                $headerScriptInstances = parent::getHeaderScripts($request);
                $moduleName = $request->getModule();
                $jsFileNames = array(
                        "modules.$moduleName.resources.advisordirect", // . = delimiter
                );
                $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
                $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
                return $headerScriptInstances;
        }
}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/*
require_once('libraries/PHPMailer/cOmniMailer.php');
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

$smarty->display('AdvisorDirectConfirmation.tpl');*/
?>

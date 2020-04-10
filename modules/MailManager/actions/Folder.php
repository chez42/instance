<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class MailManager_Folder_Action extends Vtiger_Action_Controller {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('showMailContent');
		$this->exposeMethod('emailContentForEmail');
	}

	function checkPermission(Vtiger_Request $request) {
		return true;
	}

	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if (!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	/**
	 * Function to show body of all the mails in a folder
	 * @param Vtiger_Request $request
	 */
	public function showMailContent(Vtiger_Request $request) {
		$mailIds = $request->get("mailids");
		$folderName = $request->get("folderName");

		$model = MailManager_Mailbox_Model::activeInstance();
		$connector = MailManager_Connector_Connector::connectorWithModel($model, $folderName);

		$mailContents = array();
		foreach ($mailIds as $msgNo) {
			$message = $connector->openMail($msgNo, $folderName);
			$mailContents[$msgNo] = $message->getInlineBody();
		}
		$response = new Vtiger_Response();
		$response->setResult($mailContents);
		$response->emit();
	}
	
	public function emailContentForEmail(Vtiger_Request $request){
	    
	    $response = new Vtiger_Response();
	    $mailId = $request->get('msgno');
	    $foldername = $request->get('folder');
	    
	    $model = MailManager_Mailbox_Model::activeInstance();
	    $connector = MailManager_Connector_Connector::connectorWithModel($model, $foldername);
	    
	    $folder = $connector->folderInstance($foldername);
	    
	    $mail = $connector->openMail($mailId, $foldername);
	    
	    $folderName = $folder;
	    $mailIns = $mail;
	    $userName = $model->mUsername;
	    $attachments =  $mail->attachments(false);
	    $body = $mail->body();
	    $inlineAttachments = $mail->inlineAttachments();
	    if(is_array($inlineAttachments)) {
	        foreach($inlineAttachments as $index => $att) {
	            $cid = $att['cid'];
	            $attch_name = Vtiger_MailRecord::__mime_decode($att['filename']);
	            $id = $mail->muid();
	            $src = "index.php?module=MailManager&view=Index&_operation=mail&_operationarg=attachment_dld&_muid=$id&_atname=".urlencode($attch_name);
	            $body = preg_replace('/cid:'.$cid.'/', $src, $body);
	            $inline_cid[$attch_name] = $cid;
	        }
	    }
	    $inline_att = $inline_cid;
	    
	    $metainfo  = array(
	        'from' => implode(',', $mail->from()),
	        'subject' => Vtiger_Functions::jsonEncode($mail->subject()),
	        'msgno' => $mail->msgNo(), 
	        'msguid' => $mail->uniqueid(),
	        'folder' => $foldername, 
	        'to' => implode(',', $mail->to()),
	        'cc' => implode(',', $mail->cc()), 
	        'bcc' => implode(',', $mail->bcc()),
	        'date' =>$mail->date(), 
	        'body' => $body,
	        'userName' => $userName,
	        'att' => $inline_cid, 
	        'attachments' => $attachments,
	        'att_count' => (count($attachments) - count($inline_cid))
	    );
	    
	    $response->isJson(true);
	    $response->setResult($metainfo);
	   
	    $response->emit();
	    
	}

}

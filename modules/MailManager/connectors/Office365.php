<?php
ini_set('display_errors','off'); 
require_once 'modules/MailManager/outlook/autoload.php';
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;


class MailManager_Office365_Connector {
    
    static $DB_CACHE_CLEAR_INTERVAL = "-1 day";
    
    public $mBoxUrl;
    
    public $mBox;
    
    protected $mError;
    
    protected $mFolders = false;
    
    protected $mBoxBaseUrl;
    
    protected $username;
    
    protected $token;
    
    protected $user_id;
    
    public $serverType = 'Office365';
    
    protected $mailFilter = false;
    
    public function __construct($accountid = false, $accessToken = false, $refreshToken = false, $username = false) {
        
        global $adb;
        
        $graph = new Graph();
        
        try{
            
            $graph->setAccessToken($accessToken);
            
            $user = $graph->createRequest("GET", "/me")->setReturnType(Model\User::class)->execute();
            
        } catch(Exception $e){
            
            $clientId = '32679be5-4aeb-4cda-9193-fcfe74dbfdce';
            
            $clientSecret = '1y5HHz~5-pW.gSmLs2C7GoVuaKS-o4se4c';
            
            $token_request_data = array(
                "grant_type" => "refresh_token",
                "refresh_token" => $refreshToken,
                "client_id" => $clientId,
                "client_secret" => $clientSecret
            );
            
            $token_request_body = http_build_query($token_request_data);
            
            $curl = curl_init('https://login.microsoftonline.com/common/oauth2/v2.0/token');
            
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            
            curl_setopt($curl, CURLOPT_POST, true);
            
            curl_setopt($curl, CURLOPT_POSTFIELDS, $token_request_body);
            
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            
            $response = curl_exec($curl);
            
            $response = json_decode($response, true);
            
            $graph->setAccessToken($response['access_token']);
            
            $adb->pquery("UPDATE vtiger_mail_accounts SET access_token=?, refresh_token=? WHERE account_id=?",
                array($response['access_token'], $response['refresh_token'], $accountid));
            
        }
        
        $this->mBox = $graph;
        
    }
    
    public function __destruct() {
        $this->close();
    }
    
    
    public function close() {
        if (!empty($this->mBox)) {
            $this->mBox = null;
        }
    }
    
    public function isConnected() {
        return !empty($this->mBox);
    }
    
    public function isError($result) {
        $this->mError = '';
        if( isset($result['error']) ){
            $this->mError = $result['error']['message'];
        } else if( empty($result) ){
            $this->mError = 'Unable to connect to Office Outlook Server';
        }
        return $this->hasError();
    }
    
    public function hasError() {
        return !empty($this->mError);
    }
    
    public function lastError() {
        return $this->mError;
    }
    
    public function folders($ref="{folder}") {
        
        if ($this->mFolders) return $this->mFolders;
        
        $graph = $this->mBox;
        
        $all_folders = $graph->createCollectionRequest("GET", '/me/MailFolders')->setReturnType(Model\MailFolder::class);
        
        $folder_data = array();
        
        while (!$all_folders->isEnd()){
            
            $folders = $all_folders->getPage();
            
            foreach($folders as $folder){
                
                $folderInstance = $this->folderInstance($folder->getDisplayName());
                
                $folderInstance->setFromArray($folder);
                
                $folderInstance->folderId = base64_encode($folder->getId());
                
                $folder_data[] = $folderInstance;
                
            }
        }
        
        $this->mFolders = $folder_data;
        
        return $folder_data;
        
    }
    
    /**
    
    * Used to update the folders optionus
    
    * @param flag $options
    
    */
    
    public function updateFolders($options=SA_UNSEEN) {
        
        $this->folders(); // Initializes the folder Instance
        
        if(!empty($this->mFolders)){
            
            foreach($this->mFolders as $folder) {
                
                if( strtolower($folder->name()) == 'inbox' ){
                    
                    $this->updateFolder($folder, $options);
                    
                }
                
            }
            
        }
        
    }
    
    /**
    
    * Updates the mail box's folder
    
    * @param MailManager_Model_Folder $folder - folder instance
    
    * @param $options flags like SA_UNSEEN, SA_MESSAGES etc
    
    */
    
    public function updateFolder($folder, $options) {
        
        $graph = $this->mBox;
        
        $folder->setCount('0');
        
        $folderid = '';
        
        foreach($this->getFolderList() as $key => $officeFolder){
            if(strtoupper($officeFolder) == strtoupper($folder->name())){
                $folderid = $key;
            }
        }
        
        $messageIterator = $graph->createCollectionRequest("GET", '/me/MailFolders/'.base64_decode($folderid).'/messages?$filter=IsRead%20ne%20true')->setReturnType(Model\Message::class);
        
        $folder->setUnreadCount('0');
        
        if ($messageIterator->count()) {
            
            $folder->setUnreadCount($messageIterator->count());
            
        }
        
    }
    
    
    public function folderInstance($val) {
        $instance = new MailManager_Office365Folder_Model($val);
        return $instance;
    }
    
    
    
    public function folderInstanceByArray($object_array){
        
        vimport('modules/MailManager/models/Office365Folder.php');
        
        if(!empty($object_array)){
            
            $folderins = new MailManager_Office365Folder_Model($object_array['DisplayName']);
            
            $folderins->setFromArray($object_array);
            
            return $folderins;
            
        }
        
    }
    
    /**
     * Sets a list of mails with paging
     * @param String $folder - MailManager_Model_Folder Instance
     * @param Integer $page_number  - Page number
     * @param Integer $maxLimit - Number of mails
     */
    
    public function folderMails($folder, $page_number, $maxLimit) {
        
        $graph = $this->mBox;
        
        foreach($this->getFolderList() as $key => $officeFolder){
            if(strtoupper($officeFolder) == strtoupper($folder->name())){
                $folderid = $key;
            }
        }
        if(!$folderid)
            return false;
            
        $folderid = base64_decode($folderid);
        
        $folder->setNextLink('');
        
        $folder->setPreviousLink('');
        
        if(!($maxLimit>0)){
            
            $maxLimit = 20;
            
        }
        
        $skip_records = $page_number*$maxLimit;
        
        $top = $maxLimit;
        
        $start = $page_number*$maxLimit + 1;
        
        $end = $start;
        
        if ($start < 1) $start = 1;
        
        if(!($start <= 1)) $folder->setPreviousLink('true');
        
        $messageIterator = $graph->createCollectionRequest("GET", '/me/MailFolders/'.$folderid.'/messages?$expand=attachments&$orderby=receivedDateTime%20DESC&$skip='.$skip_records)
        ->setReturnType(Model\Message::class)->setPageSize($top);
        
        $totalCount = $messageIterator->count();
        
        $folder_messages = $messageIterator->getPage();
        
        if (!empty($folder_messages)) {
            
            $end = $start + count($folder_messages) - 1;
            
            $mails = array();
            
            if(!empty($folder_messages)){
                
                foreach($folder_messages as $result) {
                    
                    $mails[] = MailManager_Office365Message_Model::parseOverview($result);
                    $mailIds[] = $result->getId();
                }
                
            }
            
            $folder->setMails($mails);
            
            $folder->setMailIds($mailIds);
            
            $folder->setPaging($start, $end, $maxLimit, $totalCount, $page_number);
            
            if( !$messageIterator->isEnd()){
                
                $folder->setNextLink('true');
                
            }
        }
            
    }
    
    
    /**
    
    * Return the cache interval
    
    */
    
    public function clearDBCacheInterval() {
        
        // TODO Provide configuration option.
        
        if (self::$DB_CACHE_CLEAR_INTERVAL) {
            
            return strtotime(self::$DB_CACHE_CLEAR_INTERVAL);
            
        }
        
        return false;
        
    }
    
    /**
    
    * Clears the cache data
    
    */
    
    public function clearDBCache() {
        
        // Trigger purne any older mail saved in DB first
        
        $interval = $this->clearDBCacheInterval();
        
        $timenow = strtotime("now");
        
        // Optimization to avoid trigger for ever mail open (with interval specified)
        
        $lastClearTimeFromSession = false;
        
        if ($interval && isset($_SESSION) && isset($_SESSION['mailmanager_clearDBCacheIntervalLast'])) {
            
            $lastClearTimeFromSession = intval($_SESSION['mailmanager_clearDBCacheIntervalLast']);
            
            if (($timenow - $lastClearTimeFromSession) < ($timenow - $interval)) {
                
                $interval = false;
                
            }
            
        }
        
        if ($interval) {
            
            MailManager_Message_Model::pruneOlderInDB($interval);
            
            $_SESSION['mailmanager_clearDBCacheIntervalLast'] = $timenow;
            
        }
        
    }
    
    /**
    
    * Function which deletes the mails
    
    * @param String $msgno - List of message number seperated by commas.
    
    */
    
    public function deleteMail($msgno) {
        
        $msgno = trim($msgno,',');
        
        $msgno = explode(',',$msgno);
        
        $graph = $this->mBox;
        
        for($i = 0;$i<count($msgno);$i++) {
            
            $response = $graph->createRequest("DELETE", '/me/messages/'.$msgno[$i])
            ->execute();
            
        }
        
    }
    
    /**
    
    * Function which moves mail to another folder
    
    * @param String $msgno - List of message number separated by commas
    
    * @param String $folderName - folder name
    
    */
    
    public function moveMail($msgno, $folderName) {
        
        $graph = $this->mBox;
        
        $folderid = '';
        
        foreach($this->getFolderList() as $key => $officeFolder){
            if(strtoupper($officeFolder) == strtoupper($folderName)){
                $folderid = $key;
            }
        }
        
        $msgno = trim($msgno,',');
        
        $msgno = explode(',',$msgno);
        
        $folderName = base64_decode($folderid);
        
        $data = array('destinationId' => $folderName);
        
        for($i = 0;$i<count($msgno);$i++) {
            
            $response = $graph->createRequest("POST", '/me/messages/'.$msgno[$i].'/move')
            ->attachBody($data)
            ->execute();
            
        }
        
    }
    
    
    public function openMail($msgno, $folder) {
        
        $this->clearDBCache();
        
        $graph = $this->mBox;
        
        $folderid = '';
        
        foreach($this->getFolderList() as $key => $officeFolder){
            if(strtoupper($officeFolder) == strtoupper($folder)){
                $folderid = $key;
            }
        }
        
        $messageIterator = $graph->createCollectionRequest("GET", '/me/MailFolders/'.$folderid.'/messages/'.$msgno.'?$expand=attachments')
        ->setReturnType(Model\Message::class);
        
        $messages = $messageIterator->getPage();
        
        if(!empty($messages)){
            
            $message_instance = new MailManager_Office365Message_Model($this->mBox, $msgno, true, $messages);
            
        } else {
            
            $message_instance = new MailManager_Office365Message_Model($this->mBox, $msgno, true);
            
        }
        
        return $message_instance;
        
    }
    
    
    
    
    
    /**
    
    * Marks the mail as Unread
    
    * @param <String> $msgno - Message Number
    
    */
    
    public function markMailUnread($msgno) {
        
        $graph = $this->mBox;
        
        $response = $graph->createRequest("PATCH", '/me/messages/'.$msgno)
        ->attachBody(array("isRead" => false))
        ->execute();
        
        $this->mModified = true;
        
    }
    
    
    
    
    
    /**
    
    * Marks the mail as Read
    
    * @param String $msgno - Message Number
    
    */
    
    public function markMailRead($msgno) {
        
        $graph = $this->mBox;
        
        $response = $graph->createRequest("PATCH", '/me/messages/'.$msgno)
        ->attachBody(array("isRead" => true))
        ->execute();
        
        $this->mModified = true;
    }
    
    
    
    
    
    /**
    
    * Searches the Mail Box with the query
    
    * @param String $query - imap search format
    
    * @param MailManager_Model_Folder $folder - folder instance
    
    * @param Integer $page_number - Page number
    
    * @param Integer $maxLimit - Number of mails
    
    */
    
    
    public function searchMails($query, $folder, $page_number, $maxLimit) {
        
        $graph = $this->mBox;
        
        $result = array();
        
        $folder->setNextLink('');
        
        $folder->setPreviousLink('');
        
        $maxLimit = 100;
        
        $folderId = '';
        
        foreach($this->getFolderList() as $key => $officeFolder){
            if(strtoupper($officeFolder) == strtoupper($folder->name())){
                $folderId = $key;
            }
        }
        
        if(base64_decode($folderId))
            $folderId = base64_decode($folderId);
            
            if ($query != '') {
                
                
                $messageIterator = $graph->createCollectionRequest("GET", '/me/MailFolders/'.$folderId.'/messages?$search="'.$query.'"&$expand=attachments')
                ->setReturnType(Model\Message::class)->setPageSize($top);
                
                $result = $messageIterator->getPage();
                
                if( !empty($result) ){
                    
                    $start = 1;
                    
                    $nmsgs = count($result);
                    
                    $end = $start + $nmsgs - 1;
                    
                    $mails = array();
                    
                    foreach($result as $email) {
                        
                        $mails[] = MailManager_Office365Message_Model::parseOverview($email);
                        $mailIds[] = $email->getId();
                        
                    }
                    
                    $folder->setMails($mails);
                    
                    $folder->setMailIds($mailIds);
                    
                    $folder->setPaging($start, $end, $maxLimit, $nmsgs, $page_number);
                    
                }
                
            }
            
    }
    
    
    
    /**
    
    * Returns list of Folder for the Mail Box
    
    * @return Array folder list
    
    */
    
    public function getFolderList() {
        
        $folders = $this->folders();
        
        $folderLists = array();
        
        if(!empty($folders)){
            foreach ($folders as $folder){
                $folderLists[$folder->folderId] = $folder->name();
            }
        }
        
        return $folderLists;
    }
    
    
    
    public function convertCharacterEncoding($value, $toCharset, $fromCharset) {
        
        if (function_exists('mb_convert_encoding')) {
            
            $value = mb_convert_encoding($value, $toCharset, $fromCharset);
            
        } else {
            
            $value = iconv($toCharset, $fromCharset, $value);
            
        }
        
        return $value;
        
    }
    
    public function SendMail($mailer){
        
        $graph = $this->mBox;
        
        $message = new Model\Message();
        $message->setSubject($mailer->Subject);
        
        $body = new Model\ItemBody();
        $body->setContentType('HTML');
        $body->setContent($mailer->Body);
        $message->setBody($body);
        
        $recipients = array();
        
        $emails = $mailer->to;
        
        foreach($emails as $email){
            foreach($email as $toemail){
                if($toemail){
                    $emailAddress = new Model\EmailAddress();
                    $emailAddress->setAddress($toemail);
                    $recipient = new Model\Recipient();
                    $recipient->setEmailAddress($emailAddress);
                    $recipients[] = $recipient;
                }
            }
        }
        $message->setToRecipients($recipients);
        
        $emails = $mailer->cc;
        
        $recipients = array();
        
        foreach($emails as $email){
            foreach($email as $toemail){
                if($toemail){
                    $emailAddress = new Model\EmailAddress();
                    $emailAddress->setAddress($toemail);
                    $recipient = new Model\Recipient();
                    $recipient->setEmailAddress($emailAddress);
                    $recipients[] = $recipient;
                }
            }
        }
        
        $message->setCcRecipients($recipients);
        
        $emails = $mailer->bcc;
        
        $recipients = array();
        
        foreach($emails as $email){
            foreach($email as $toemail){
                if($toemail){
                    $emailAddress = new Model\EmailAddress();
                    $emailAddress->setAddress($toemail);
                    $recipient = new Model\Recipient();
                    $recipient->setEmailAddress($emailAddress);
                    $recipients[] = $recipient;
                }
            }
        }
        
        $message->setBccRecipients($recipients);
        
        
        $emails = $mailer->ReplyTo;
        
        $recipients = array();
        
        foreach($emails as $email){
            $emailAddress = new Model\EmailAddress();
            $emailAddress->setAddress($email);
            $recipient = new Model\Recipient();
            $recipient->setEmailAddress($emailAddress);
            $recipients[] = $recipient;
        }
        if(!empty($recipients))
            $message->setReplyTo($recipient);
            
            foreach($mailer->attachment as $attachmentData){
                $attachment = new Model\FileAttachment();
                $attachment->setName($attachmentData[2]);
                $attachment->setContentBytes(base64_encode(file_get_contents($attachmentData[0])));
                $attachment->setODataType("#microsoft.graph.fileAttachment");
                $attachments[]=$attachment;
            }
            $message->setAttachments($attachments);
            $body = array("message" => $message);
            
            try{
                $response = 	$graph->createRequest("POST", "/me/sendmail")
                ->attachBody($body)
                ->execute();
                
                return true;
                
            } catch(Exception $e){
                return $e->getMessage();
            }
            
            exit;
            
    }
    
   
}

?>

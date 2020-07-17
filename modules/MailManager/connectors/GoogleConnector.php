<?php

require_once 'libraries/Google/autoload.php';

class MailManager_GoogleConnector_Connector {
    
    static $DB_CACHE_CLEAR_INTERVAL = "-1 day";
    
    public $mBoxUrl;
    
    public $mBox;
    
    protected $mError;
    
    protected $mFolders = false;
    
    protected $mBoxBaseUrl;
    
    protected $username;
    
    protected $token;
    
    protected $user_id;
    
    public $serverType = 'Google';
    
    protected $mailFilter = false;
    
    public function __construct($accountid = false, $accessToken = false, $refreshToken = false, $username = false) {
        
        global $adb;
        
        $graph = new Google_Client();
       
        $graph->setAccessToken($accessToken);
        
        if($graph->isAccessTokenExpired()){
            
            $config = array();
            $config['client_id'] = Google_Config_Connector::$clientId;
            $config['client_secret'] = Google_Config_Connector::$clientSecret;
            $graph->setAuthConfig($config);
            $response = $graph->fetchAccessTokenWithRefreshToken($refreshToken);
    
            $graph->setAccessToken($response['access_token']);
            $adb->pquery("UPDATE vtiger_mail_accounts SET access_token=? WHERE account_id=?",
                array($response['access_token'], $accountid));
            
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
        
        $service = new Google_Service_Gmail($this->mBox);
        
        $results = $service->users_labels->listUsersLabels('me');
        
        $skipFolders = array("CATEGORY_PERSONAL", "CATEGORY_SOCIAL","CATEGORY_FORUMS", "CHAT",
            "CATEGORY_PROMOTIONS","CATEGORY_UPDATES");
        
        $folder_data = array();
       
        foreach ($results->getLabels() as $label) {
            
            if(in_array($label->getId(), $skipFolders)) continue;
                
            $folderInstance = $this->folderInstance($label->getName());
            
            $folderInstance->setFromArray($label);
            
            $folderInstance->folderId = $label->getId();
            
            $folder_data[] = $folderInstance;
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
        
        $this->mBox->setUseBatch(false);
        
        $folder->setCount('0');
        
        $folderid = '';
        
        foreach($this->getFolderList() as $key => $officeFolder){
            if(strtoupper($officeFolder) == strtoupper($folder->name())){
                $folderid = $key;
            }
        }
        
        $folder->setUnreadCount('0');
        
        $service = new Google_Service_Gmail($this->mBox);
        $response = $service->users_labels->get('me',$folderid);
        
        
        if (isset($response->messagesUnread)) $folder->setUnreadCount($response->messagesUnread);
        if (isset($response->messagesTotal)) $folder->setCount($response->messagesTotal);
        
    }
    
    
    public function folderInstance($val) {
        $instance = new MailManager_GoogleFolder_Model($val);
        return $instance;
    }
    
    
    /**
     * Sets a list of mails with paging
     * @param String $folder - MailManager_Model_Folder Instance
     * @param Integer $page_number  - Page number
     * @param Integer $maxLimit - Number of mails
     */
    
    public function folderMails($folder, $start, $maxLimit) {
        
        $response = array();
        
        $page_token = false;
       
        $folderid = '';
        
        $folder->setNextLink('');
        
        $folder->setPreviousLink('');
        
        foreach($this->getFolderList() as $key => $officeFolder){
            if(strtoupper($officeFolder) == strtoupper($folder->name())){
                $folderid = $key;
                $folder->setId($folderid);
            }
        }
        
        try {
            
            $this->mBox->setUseBatch(true);
            
            $batch = new Google_Http_Batch($this->mBox);
           
            $params = array(
                'labelIds' => $folderid,
                'maxResults' => $maxLimit,
            );
            
            
            if($start == 0){
                $page = 1;
            } else {
                $page = $start + 1;
            }
            
            if($page == 1){
                unset($_SESSION['mail_manager_paging']);
            }
            
            if(!($page <= 1)) $folder->setPreviousLink('true');
            
            if(isset($_SESSION['mail_manager_paging'][$page])){
                $params['pageToken'] = $_SESSION['mail_manager_paging'][$page];
            }
            
            $service = new Google_Service_Gmail($this->mBox);
            $request = $service->users_messages->listUsersMessages('me', $params);
            
            $batch->add($request, 'messages');
            
            $request = $service->users_labels->get('me', $folderid);
            
            $batch->add($request, 'folder');
            
            $results = $batch->execute();
            
            if(($results['response-messages'] instanceof Google_Service_Exception)){
                $this->mError = 'Invalid Token';
                return false;
            }
            
            $this->mBox->setUseBatch(false);
            
            foreach($results['response-messages']['messages'] as $message){
                $response['messages'][] = array("id" => $message->getId());
            }
            
            $page_token = $results['response-messages']->nextPageToken;
            
            if(isset($results['response-folder'])){
                if (isset($results['response-folder']->messagesUnread)) $folder->setUnreadCount($results['response-folder']->messagesUnread);
                if (isset($results['response-folder']->messagesTotal)) $folder->setCount($results['response-folder']->messagesTotal);
            }
            
        } catch(Exception $e) {}
        
        if (
            !empty($response) &&
            isset($response['messages']) &&
            !empty($response['messages'])
            ) {
                
                $records = $response['messages'];
                
                $mails = array();
                
                $mailIds = array();
                
                $this->mBox->setUseBatch(true);
                
                $batch = new Google_Http_Batch($this->mBox);
                
                $service = new Google_Service_Gmail($this->mBox);
                
                foreach($records as $result) {
                    $request = $service->users_messages->get('me', $result['id']);
                    $batch->add($request, $result['id']);
                }
                
                $results = $batch->execute();
                
                $this->mBox->setUseBatch(false);
                
                $attachments = array();
                
                $messageModel = new MailManager_GoogleMessage_Model();
                
                foreach($records as $result) {
                    if(isset($results['response-'.$result['id']])){
                        
                        $loaded = $messageModel->readFromDB($result['id'], $folderid);
                        
                        $messageDetail = $this->getGmailMessageById($results['response-'.$result['id']], $loaded);
                        
                        if(isset($messageDetail->_attachments)){
                            foreach($messageDetail->_attachments as $attachment_id => $attachment_info){
                                $attachments[$attachment_id] = array(
                                    'messageid' => $attachment_info->messageid,
                                    'filename' => $attachment_info->filename,
                                    'cid' => $attachment_info->cid
                                );
                            }
                        }
                        
                        $message = MailManager_GoogleMessage_Model::parseOverview($messageDetail,false);
                        
                        $message->setMsgNo($result['id']);
                        
                        //$message->mUid = $result['id'];
                        
                        $mailIds[] = $result['id'];
                        
                        $mails[] = $message;
                        
                        if(!$loaded) {
                            
                            $messageModel->saveToDB($messageDetail, $folder);
                        }
                    }
                }
                
                // Sync Attachments
                $this->mBox->setUseBatch(true);
                
                $batch = new Google_Http_Batch($this->mBox);
                
                $service = new Google_Service_Gmail($this->mBox);
                
                foreach($attachments as $attachment_id => $attachment_info){
                    
                    $request = $service->users_messages_attachments->get('me', $attachment_info['messageid'],
                        $attachment_id);
                    
                    $batch->add($request, "attachment_".$attachment_id);
                }
                
                $results = $batch->execute();
                
                foreach($attachments as $attachment_id => $attachment_info){
                    $data = strtr($results['response-attachment_'. $attachment_id]->getData(), array('-' => '+', '_' => '/'));
                    $sanitizedData = strtr($data,'-_', '+/');
                    $attachment_info['data'] = base64_decode($sanitizedData);
                    $messageModel->__saveAttachment($attachment_info);
                }
                $this->mBox->setUseBatch(false);
                // Sync Attachments End
                
                if($page_token){
                    $folder->nextPageToken = $page_token;
                    $_SESSION['mail_manager_paging'][$page+1] = $page_token;
                    $folder->setNextLink('true');
                }
                
                $folder->setMails($mails);
                
                $folder->setMailIds($mailIds);
                
                $reverse_start = $folder->count() - ($start * $maxLimit);
                
                $reverse_end = $reverse_start - $maxLimit + 1;
                
                if ($reverse_start < 1) $reverse_start = 1;
                
                if ($reverse_end < 1) $reverse_end = 1;
                
                $folder->setPaging($reverse_end, $reverse_start, $maxLimit, $folder->count(), $start);
            }
    }
    
    function getGmailMessageById($response, $loaded){
        
        $messageDetail = array();
        
        if(!empty($response)){
            
            if(
                isset($response->labelIds) && !empty($response->labelIds)
                ){
                    
                if(in_array("UNREAD", $response->labelIds))
                    $messageDetail['seen'] = false;
                else
                    $messageDetail['seen'] = true;
                            
            }
            
            $payload = $response->getPayload();
            
            $messageDetail['uid'] = $response->id;
            
            $messageDetail['msgno'] = $response->id;
            
            $messageDetail['snippet'] = $response->snippet;
            
            $allowedFields = array(
                'From' => 'from',
                'To' => 'to',
                'Subject' => 'subject',
                'Date' => 'date',
                'Message-ID' => '_uniqueid',
                'Delivered-To' => 'in_reply_to',
                'Bcc' => '_bcc',
            );
            
            $headers = $payload->getHeaders();
            
            foreach($headers as $header){
                if(isset($allowedFields[$header->name])){
                    $messageDetail[$allowedFields[$header->name]] = $header->value;
                }
            }
            
            $body = $payload->getBody();
            
            $FOUND_BODY = $this->decodeBody($body['data']);
            
            if(!$FOUND_BODY) {
                
                $parts = $payload->getParts();
                
                if(!$FOUND_BODY) {
                    
                    foreach ($parts  as $part) {
                        if($part['body'] && $part['mimeType'] == 'text/html') {
                            $FOUND_BODY = $this->decodeBody($part['body']->data);
                            break;
                        }
                    }
                    
                }
                
                if(!$FOUND_BODY) {
                    
                    foreach ($parts  as $part) {
                        
                        if($part['parts'] && !$FOUND_BODY) {
                            
                            foreach ($part['parts'] as $p) {
                                
                                // replace 'text/html' by 'text/plain' if you prefer
                                if($p['mimeType'] === 'text/html' && $p['body']) {
                                    $FOUND_BODY = $this->decodeBody($p['body']->data);
                                    break;
                                }
                                
                                if(isset($p['parts']) && !$FOUND_BODY){
                                    foreach ($p['parts'] as $sub_part){
                                        if($sub_part['mimeType'] === 'text/html' && $sub_part['body']) {
                                            $FOUND_BODY = $this->decodeBody($sub_part['body']->data);
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        if($FOUND_BODY) {
                            break;
                        }
                    }
                }
            }
            
            $attachments = array();
            
            // Fetch Attachments
            
            if(!$loaded) {
                foreach ($parts  as $part) {
                    
                    if($part['filename'] != ''){
                        
                        $inline_attachment = false;
                        
                        $part_headers = $part->getHeaders();
                        
                        $headers = array();
                        
                        foreach($part_headers as $header){
                            $headers[$header->name] = $header->value;
                        }
                        
                        if(strpos($headers['Content-Disposition'], 'inline') !== FALSE){
                            $inline_attachment = true;
                        }
                        
                        $cid = $headers['X-Attachment-Id'];
                        
                        $data64 = '';
                        
                        if(!$inline_attachment) {
                            $attachments[$part['body']->attachmentId] = array('messageid' => $response->id, "id" => $part['body']->attachmentId, "data" => $data64,
                                'filename' => $part['filename'], 'cid' => 0);
                        } else {
                            $attachments[$part['body']->attachmentId] = array('messageid' => $response->id, "id" => $part['body']->attachmentId, "data" => $data64,
                                'filename' => $part['filename'], 'cid' => $cid);
                        }
                        
                    }
                }
                
                
                foreach ($parts  as $part) {
                    
                    foreach ($part['parts'] as $p) {
                        
                        if($p['filename'] != ''){
                            
                            $inline_attachment = false;
                            
                            $part_headers = $p->getHeaders();
                            
                            $headers = array();
                            
                            foreach($part_headers as $header){
                                $headers[$header->name] = $header->value;
                            }
                            
                            if(strpos($headers['Content-Disposition'], 'inline') !== FALSE){
                                $inline_attachment = true;
                            }
                            
                            $cid = $headers['X-Attachment-Id'];
                            
                            $data64 = '';
                            
                            if(!$inline_attachment) {
                                $attachments[$p['body']->attachmentId] = array('messageid' => $response->id, "id" => $p['body']->attachmentId, "data" => $data64,
                                    'filename' => $p['filename'], 'cid' => 0);
                            } else {
                                $attachments[$p['body']->attachmentId] = array('messageid' => $response->id, "id" => $p['body']->attachmentId, "data" => $data64,
                                    'filename' => $p['filename'], 'cid' => $cid) ;
                            }
                        }
                    }
                }
            }
            
            $messageDetail['_attachments'] = $attachments;
            
            $messageDetail['_body'] = $FOUND_BODY;
            
            $messageDetail['_plainmessage'] = $response->snippet;
            
            $messageDetail['_charset'] = "UTF-8";
            
            $messageDetail['_bodyparsed'] = 1;
            
        }
        
        $messageDetail = json_decode(json_encode($messageDetail));
        
        return $messageDetail;
        
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
        
        $service = new Google_Service_Gmail($this->mBox);
        
        for($i = 0;$i<count($msgno);$i++) {
            
            $service->users_messages->delete('me', $msgno[$i]);
            
        }
        
    }
    
    /**
    
    * Function which moves mail to another folder
    
    * @param String $msgno - List of message number separated by commas
    
    * @param String $folderName - folder name
    
    */
    
    public function moveMail($msgno, $folderName, $fromFolder) {
        
        $service = new Google_Service_Gmail($this->mBox);
        
        $folderid = '';
        
        foreach($this->getFolderList() as $key => $officeFolder){
            if(strtoupper($officeFolder) == strtoupper($folderName)){
                $folderid = $key;
            }
        }
        
        foreach($this->getFolderList() as $key => $officeFolder){
            if(strtoupper($officeFolder) == strtoupper($fromFolder)){
                $fromFolder = $key;
            }
        }
        
        
        $mods = new Google_Service_Gmail_ModifyMessageRequest();
        $mods->setAddLabelIds($folderid);
        $mods->setRemoveLabelIds($fromFolder);
        
        $msgno = trim($msgno,',');
        
        $msgno = explode(',',$msgno);
        
        for($i = 0;$i<count($msgno);$i++) {
           
            $message = $service->users_messages->modify('me', $msgno, $mods);
            
        }
        
    }
    
    
    public function openMail($msgno, $folder) {
        
        $this->clearDBCache();
        
        return new MailManager_GoogleMessage_Model($msgno, true, $folder);
        
    }
    
    /**
    
    * Marks the mail as Unread
    
    * @param <String> $msgno - Message Number
    
    */
    
    public function markMailUnread($msgno) {
        
        $service = new Google_Service_Gmail($this->mBox);
        
        $mods = new Google_Service_Gmail_ModifyMessageRequest();
        $mods->setAddLabelIds('UNREAD');
        
        $message = $service->users_messages->modify('me', $msgno, $mods);
        
        $this->mModified = true;
        
    }
    
    
    
    
    
    /**
    
    * Marks the mail as Read
    
    * @param String $msgno - Message Number
    
    */
    
    public function markMailRead($msgno) {
        
        $service = new Google_Service_Gmail($this->mBox);
        
        $mods = new Google_Service_Gmail_ModifyMessageRequest();
        $mods->setRemoveLabelIds('UNREAD');
        
        $message = $service->users_messages->modify('me', $msgno, $mods);
            
        $this->mModified = true;
    }
    
    
    
    
    
    /**
    
    * Searches the Mail Box with the query
    
    * @param String $query - imap search format
    
    * @param MailManager_Model_Folder $folder - folder instance
    
    * @param Integer $page_number - Page number
    
    * @param Integer $maxLimit - Number of mails
    
    */
    
    public function searchMails($query, $folder, $start, $maxLimit) {
        
        $folderId = ''; 
        
        foreach($this->getFolderList() as $key => $officeFolder){
            if(strtoupper($officeFolder) == strtoupper($folder->name())){
                $folderId = $key;
            }
        }
        
        $folder->setPreviousLink('');
        
        $folder->setNextLink(''); 
        
        /*$query = explode(" ",$query);
        
        $type = strtolower($query[0]);
        
        preg_match('/"(.*?)"/', $query[1], $match);
        */
//         echo"<pre>";print_r($query);echo"</pre>";exit;
        
//         $string = $match['1'];
        
        $response = array();
        
        $page_token = false;
        
        try {
            
            $this->mBox->setUseBatch(true);
            
            $batch = new Google_Http_Batch($this->mBox);
            
            $params = array(
                'labelIds' => $folderId,
                'maxResults' => $maxLimit,
            );
            
            $params['q'] = $query;
            
            if($start == 0){
                $page = 1;
            } else {
                $page = $start + 1;
            }
            
            if($page == 1){
                unset($_SESSION['mail_manager_paging']);
            }
            if(!($page <= 1)) $folder->setPreviousLink('true');
            
            if(isset($_SESSION['mail_manager_paging'][$page])){
                $params['pageToken'] = $_SESSION['mail_manager_paging'][$page];
            }
            
            $service = new Google_Service_Gmail($this->mBox);
            $request = $service->users_messages->listUsersMessages('me', $params);
            $batch->add($request, 'messages');
            
            $results = $batch->execute();
            $this->mBox->setUseBatch(false);
            
            foreach($results['response-messages']['messages'] as $message){
                $response['messages'][] = array("id" => $message->getId());
            }
           
            $page_token = $results['response-messages']->nextPageToken;
            
            $folder->setCount($results['response-messages']->resultSizeEstimate);
            
            if(isset($results['response-folder'])){
                if (isset($results['response-folder']->messagesUnread)) $folder->setUnreadCount($results['response-folder']->messagesUnread);
                if (isset($results['response-folder']->messagesTotal)) $folder->setCount($results['response-folder']->messagesTotal);
            }
            
        } catch(Exception $e) {}
        
        if(!empty($response)){
            
            $records = $response['messages'];
            
            $mails = array();
            
            $mailIds = array();
            
            $this->mBox->setUseBatch(true);
            
            $batch = new Google_Http_Batch($this->mBox);
            $service = new Google_Service_Gmail($this->mBox);
            foreach($records as $result) {
                $request = $service->users_messages->get('me', $result['id']);
                $batch->add($request, $result['id']);
            }
            
            $results = $batch->execute();
            
            $this->mBox->setUseBatch(false);
            
            $attachments = array();
            
            $messageModel = new MailManager_GoogleMessage_Model();
            
            foreach($records as $result) {
                
                if(isset($results['response-'.$result['id']])){
                    
                    $loaded = $messageModel->readFromDB($result['id'], $folder->folderId);
                    
                    $messageDetail = $this->getGmailMessageById($results['response-'.$result['id']], $loaded);
                    
                    if(isset($messageDetail->_attachments)){
                        foreach($messageDetail->_attachments as $attachment_id => $attachment_info){
                            $attachments[$attachment_id] = array(
                                'messageid' => $attachment_info->messageid,
                                'filename' => $attachment_info->filename,
                                'cid' => $attachment_info->cid
                            );
                        }
                    }
                    
                    $message = MailManager_GoogleMessage_Model::parseOverview($messageDetail,false);
                    
                    $message->setMsgNo($result['id']);
                    
                    $mailIds[] = $result['id'];
                    
                    $mails[] = $message;
                    
                    if(!$loaded) {
                        $messageModel->saveToDB($messageDetail, $folder);
                    }
                }
            }
            
            // Sync Attachments
            $this->mBox->setUseBatch(true);
            
            $batch = new Google_Http_Batch($this->mBox);
            $service = new Google_Service_Gmail($this->mBox);
            
            foreach($attachments as $attachment_id => $attachment_info){
                
                $request = $service->users_messages_attachments->get('me', $attachment_info['messageid'],
                    $attachment_id);
                
                $batch->add($request, "attachment_".$attachment_id);
            }
            
            $results = $batch->execute();
            
            foreach($attachments as $attachment_id => $attachment_info){
                $data = strtr($results['response-attachment_'. $attachment_id]->getData(), array('-' => '+', '_' => '/'));
                $attachment_info['data'] = $data;
                $messageModel->__saveAttachment($attachment_info);
            }
            $this->mBox->setUseBatch(false);
            // Sync Attachments End
            
            if($page_token){
                $folder->nextPageToken = $page_token;
                $_SESSION['mail_manager_paging'][$page+1] = $page_token;
                $folder->setNextLink('true');
            }
            
            $folder->setMails($mails);
            $folder->setMailIds($mailIds);
            
            $reverse_start = $folder->count() - ($start * $maxLimit);
            
            $reverse_end = $reverse_start - $maxLimit + 1;
                
            if ($reverse_start < 1) $reverse_start = 1;
            
            if ($reverse_end < 1) $reverse_end = 1;
            
            $folder->setPaging($reverse_end, $reverse_start, $maxLimit, $folder->count(), $start);
            
            $folderCount = vtranslate('many')."&nbsp;";
            
            $folder->setCount($folder->count());
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
        
        $service = new Google_Service_Gmail($this->mBox);
        
        $strRawMessage = "";
        $boundary = uniqid(rand(), true);
        $subjectCharset = $charset = 'utf-8';
        
        $strSesFromName = $mailer->FromName;
        $strSesFromEmail = $mailer->From;
        $strSubject = $mailer->Subject;
        
        $emails = $mailer->to;
        
        foreach($emails as $email){
            foreach($email as $toemail){
                if($toemail){
                    $strRawMessage .= 'To: ' .$toemail . " <" . $toemail . ">" . "\r\n";
                }
            }
        }
        
        $emails = $mailer->cc;
        
        $recipients = array();
        
        foreach($emails as $email){
            foreach($email as $toemail){
                if($toemail){
                    $strRawMessage .= 'Cc: ' .$toemail . " <" . $toemail . ">" . "\r\n";
                }
            }
        }
        
        $emails = $mailer->bcc;
        
        foreach($emails as $email){
            foreach($email as $toemail){
                if($toemail){
                    $strRawMessage .= 'Bcc: ' .$toemail . " <" . $toemail . ">" . "\r\n";
                }
            }
        }
        
        $emails = $mailer->ReplyTo;
        
        $recipients = array();
        
        foreach($emails as $email){
            $strRawMessage .= 'ReplyTo: ' .$toemail . " <" . $toemail . ">" . "\r\n";
        }
        
        $strRawMessage .= 'From: '.$strSesFromName . " <" . $strSesFromEmail . ">" . "\r\n";
        
        $strRawMessage .= 'Subject: =?' . $subjectCharset . '?B?' . base64_encode($strSubject) . "?=\r\n";
        $strRawMessage .= 'MIME-Version: 1.0' . "\r\n";
        $strRawMessage .= 'Content-type: Multipart/Mixed; boundary="' . $boundary . '"' . "\r\n";
        $strRawMessage .= "\r\n--{$boundary}\r\n";
        $strRawMessage .= 'Content-Type: text/html; charset=' . $charset . "\r\n";
        $strRawMessage .= "Content-Transfer-Encoding: base64" . "\r\n\r\n";
        $strRawMessage .= $mailer->Body . "\r\n";
        $strRawMessage .= "--{$boundary}\r\n";
        
        foreach ($mailer->attachment as $key => $filePath) {
            
            $array = explode('/', $filePath[0]);
            $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
            $mimeType = finfo_file($finfo, $filePath[0]);
            $fileName = $attachmentData[2];
            $fileData = base64_encode(file_get_contents($filePath[0]));
            
            $strRawMessage .= "\r\n--{$boundary}\r\n";
            $strRawMessage .= 'Content-Type: '. $mimeType .'; name="'. $fileName .'";' . "\r\n";
            $strRawMessage .= 'Content-Description: ' . $fileName . ';' . "\r\n";
            $strRawMessage .= 'Content-Disposition: attachment; filename="' . $fileName . '"; size=' . filesize($filePath[0]). ';' . "\r\n";
            $strRawMessage .= 'Content-Transfer-Encoding: base64' . "\r\n\r\n";
            $strRawMessage .= chunk_split(base64_encode(file_get_contents($filePath[0])), 76, "\n") . "\r\n";
            $strRawMessage .= "--{$boundary}\r\n";
        }
        
        
        try {
            
            $mime = rtrim(strtr(base64_encode($strRawMessage), '+/', '-_'), '=');
            $msg = new Google_Service_Gmail_Message();
            $msg->setRaw($mime);
            $objSentMsg = $service->users_messages->send("me", $msg);
            
            return true;
            
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    
    
    function decodeBody($body) {
        
        $rawData = $body;
        
        $sanitizedData = strtr($rawData,'-_', '+/');
        
        $decodedMessage = base64_decode($sanitizedData);
        
        return $decodedMessage;
        
    }
    
}

?>

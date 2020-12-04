<?php
require_once "modules/MSExchange/vendor/autoload.php";

use garethp\ews\API;
use garethp\ews\API\Type;
use garethp\ews\API\Type\ConnectingSIDType;
use garethp\ews\API\Type\ExchangeImpersonation;
use garethp\ews\MailAPI;

class MSExchange_MSExchange_Model{
    
    private $host;
    private $username;
    private $password;
    private $version;
    protected $ews;
    protected $maxEntriesReturned = 100;
    
    function __construct($host = false, $username = false, $password = false, $version = false){
        
        if($host){
            $this->host = $host;
        }
            
        if($username){
            $this->username = $username;
        }
        
        if($password){
            $this->setPassword($password);
        }
        
        if($version){
            $this->version = $version;
        }
    }
    
    public function setPassword($exchange_password){
        
        if (!MSExchange_Utils_Helper::isProtectedText($exchange_password)) {
            $exchange_password = MSExchange_Utils_Helper::toProtectedText($exchange_password);
        }
        
        $this->password = $exchange_password;
    }
    
    public function getPassword(){
        $exchange_password = $this->password;
        $password = MSExchange_Utils_Helper::fromProtectedText($exchange_password);
        return $password;
    }
    
    public function setImpersonation($impersonationType, $value){
        
        $connectingSID = new ConnectingSIDType();
        
        if($impersonationType == "upn"){
            $connectingSID->setPrincipalName($value);
        } else if($impersonationType == 'smtp_address'){
            $connectingSID->setPrimarySmtpAddress($value);
        } else {
            $connectingSID->setSID($value);
        }
        
        $impersonation = new ExchangeImpersonation();
        
        $impersonation->setConnectingSID($connectingSID);
        
        $this->ews = API::withUsernameAndPassword(
            $this->host,
            $this->username,
            $this->getPassword(),
            [
                'version' => $this->version,
                'impersonation' => $impersonation
            ]
        );
        
        return $this->ews;
    }
    
    public function checkImpersonation($request){
        
        $host = $request->get("ms_exchange_url");
        
        $username = $request->get("ms_exchange_username");
        
        $password = $request->get("ms_exchange_password");
        
        $version = $request->get("ms_exchange_version");
        
        $impersonationType = $request->get("ms_exchange_user_impersonation_type");
        
        $impersonating_field_value = $request->get("ms_exchange_user_impersonation_field_value");
    
        $this->host = $host;
        $this->username = $username;
        $this->version = $version;
        $this->setPassword($password);
            
        $api = $this->setImpersonation($impersonationType, $impersonating_field_value);
           
        $request = array(
            'FolderShape' => array(
                'BaseShape' => array('_' => 'Default')
            ),
            'FolderIds' => array(
                'DistinguishedFolderId' => array(
                    'Id' => 'tasks'
                )
            )
        );
        
        $request = Type::buildFromArray($request);
        
        try{
            
            $response = $api->getClient()->GetFolder($request);
            
            if($response->getFolderId()->getId()){
                return array("success" => true);
            }
            
        } catch (Exception $e){
            return array("success" => false, "message" => "Invalid Credentials", "error" => $e->getMessage());
        }
    }
   
    /**
     * Get a folder by it's distinguishedId
     *
     * @param string $distinguishedId
     * @return Type\BaseFolderType
     */
    public function getFolderByDistinguishedId($distinguishedId)
    {
        return $this->ews->getFolder(array(
            'DistinguishedFolderId' => array(
                'Id' => $distinguishedId,
            )
        ));
    }
    
    static function getInstance($host, $username, $password, $version){
        
        $modelClassName = get_called_class();
        
        $model = new $modelClassName($host, $username, $password, $version);
        
        $model->ews = API::withUsernameAndPassword(
            $model->host,
            $model->username,
            $model->getPassword(),
            [
                'version' => $model->version,
            ]
        );
        return $model;
    }  
    
    function getFolder($folderName){
        
        $folder = $this->getFolderByDistinguishedId($folderName);
        
        $folderId = $folder->getFolderId();
        
        return $folderId;
    }
    
    function getItems($itemIds){
        
        $Items = array();
        
        foreach($itemIds as $id){
            $Item = new Type\ItemIdType($id);
            $Items[] = $Item->toArray();
        }
        
        $options = array(
            'ItemShape' => array(
                'BaseShape' => 'AllProperties',
                'BodyType' => 'Text'
            )
        );
        
        return $this->ews->getItem($Items, $options);
    }
    
    public function updateItems($items, $options = array()){
        $request = array(
            'ItemChanges' => $items,
            'MessageDisposition' => 'SaveOnly',
            'ConflictResolution' => 'AlwaysOverwrite'
        );
        
        $request = array_replace_recursive($request, $options);
        
        $request = Type::buildFromArray($request);
        
        $response = $this->ews->getClient()->UpdateItem($request);
        
        return $response;
    }
    
    function ensureIsArray($input, $checkAssoc = false){
        if (!is_array($input)) {
            return [$input];
        }
        
        if ($checkAssoc && Type::arrayIsAssoc($input)) {
            return [$input];
        }
        
        return $input;
    }
    
    function deleteItems($items, $options = array()){
        try{
            return $this->ews->deleteItems($items, $options);
        } catch(Exception $e){
            return false;//$e->getMessage();
        }
    }
    
    function isValidCredentials(){
        
        $request = array(
            'FolderShape' => array(
                'BaseShape' => array('_' => 'Default')
            ),
            'FolderIds' => array(
                'DistinguishedFolderId' => array(
                    'Id' => 'contacts'
                )
            )
        );
        
        $request = Type::buildFromArray($request);
        
        try{
            
            $response = $this->ews->getClient()->GetFolder($request);
            
            if($response->getFolderId()->getId()){
                return true;
            }
            
        } catch (Exception $e){
            return false;
        }
    }
    
    
    function sendMailUsingEws($mailer){
        
        
        global $adb;
        
        $host = $mailer->type;
        $username = $mailer->Username;
        $password = $mailer->Password;
        
        
        $setFrom = new  Type\EmailAddressType();
        $setFrom->setName($mailer->FromName);
        $setFrom->setEmailAddress($mailer->From);
        
        $from = new Type\SingleRecipientType();
        $from->setMailbox($setFrom);
        
        $api = MailAPI::withUsernameAndPassword($host, $username, $password);
        
        $message = new Type\MessageType();
        $message->setFrom($from);
        $message->setBody($mailer->Body);
        $message->setSubject($mailer->Subject);
        
        $emails = $mailer->to;
        $toemails = array();
        foreach($emails as $email){
            foreach($email as $toemail){
                if($toemail){
                    $toemails[] = $toemail;
                }
            }
        }
        
        $emails = $mailer->cc;
        $ccMails = array();
        foreach($emails as $email){
            foreach($email as $ccemail){
                if($ccemail){
                    $ccMails[] = $ccemail;
                }
            }
        }
        
        $emails = $mailer->bcc;
        $bccMails = array();
        foreach($emails as $email){
            foreach($email as $bccemail){
                if($bccemail){
                    $bccMails[] = $bccemail;
                }
            }
        }
        $attachments = array();
        foreach ($mailer->attachment as $key => $filePath) {
            $attachments[] = array(
                'Name' => $filePath[2],
                'Content' => file_get_contents($filePath[0])
            );
        }
        if(empty($attachments)){
            $attachments[] = array(
                'Name' => '',
                'Content' => ''
            );
        }
        
        $message->setToRecipients($toemails);
        $message->setCcRecipients($ccMails);
        $message->setBccRecipients($bccMails);
        
        $emails = $mailer->ReplyTo;
        $recipients = array();
        foreach($emails as $email){
            $recipients[] = $email;
        }
        
        $message->setReplyTo($recipients);
        
        $mailId = $return = $api->sendMail($message, array('MessageDisposition' => 'SaveOnly'));
        
        $api->getClient()->CreateAttachment(array (
            'ParentItemId' => $mailId->toArray(),
            'Attachments' => array (
                'FileAttachment' => $attachments,
            ),
        ));
        
        $mailId = $api->getItem($mailId)->getItemId();
        
        $status = $api->getClient()->SendItem(array (
            'SaveItemToFolder' => true,
            'ItemIds' => array (
                'ItemId' => $mailId->toArray()
            )
        ));
        
        if($status && $status->getResponseClass() == 'Success')
            return true;
            else
                return false;
    }
    
}
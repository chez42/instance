<?php

class MailManager_GoogleMessage_Model extends MailManager_Message_Model  {
    
     public $mUid;
    
    public $token = false;
    
    public function __construct($msgno = false, $fetchbody = false, $folder = '') {
        
        if ($msgno) {
            
            $this->mMsgNo = $msgno;
            
            $this->mUid = $msgno; 
            
            $loaded = false;
            
            if ($fetchbody) {
                $loaded = $this->readFromDB($msgno, $folder);
            }
            
            if ($loaded) {
                $this->setMsgNo($msgno);
            }
        }
    }
    
    public function body(){
        return $this->getBodyHTML($safehtml);
    }
    
    public function getBodyHTML($safehtml=true) {
        $bodyhtml = parent::getBodyHTML();
        if ($safehtml) {
            $bodyhtml = MailManager_Utils_Helper::safe_html_string($bodyhtml);
        }
        return $bodyhtml;
    }
    
    public function getInlineBody() {
        $bodytext = $this->body();
        
        $bodytext = preg_replace("/<br>/", " ", $bodytext);
        $bodytext = strip_tags($bodytext);
        $bodytext = preg_replace("/\n/", " ", $bodytext);
        
        return $bodytext;
    }
    
    function __saveAttachment($attachment){
        
        $db = PearDatabase::getInstance();
        
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        
        $savedtime = strtotime("now");
        
        $attachInfo = $this->__SaveAttachmentFile($attachment['filename'], $attachment['data']);
        
        if(is_array($attachInfo) && !empty($attachInfo)) {
            
            if(isset($attachment['cid']) && $attachment['cid'] ){
                
                $db->pquery("INSERT INTO vtiger_mailmanager_mailattachments (userid, muid, attachid, aname, path, lastsavedtime, cid) VALUES (?, ?, ?, ?, ?, ?, ?)",
                array($currentUserModel->getId(), $attachment['messageid'], $attachInfo['attachid'],
                @self::__mime_decode($attachInfo['name']), $attachInfo['path'], $savedtime, $attachment['cid']));
                
            } else {
                
                $db->pquery("INSERT INTO vtiger_mailmanager_mailattachments (userid, muid, attachid, aname, path, lastsavedtime) VALUES (?, ?, ?, ?, ?, ?)",
                array($currentUserModel->getId(), $attachment['messageid'], $attachInfo['attachid'], $attachInfo['name'], $attachInfo['path'], $savedtime));
            
            }
            
        }
    }

    public function msgNo() {
        
        return $this->mMsgNo;
        
    }
    
    public static function parseOverview($result, $mbox = false) {
        
        if($mbox) {
            $instance = new self($mbox, $result->msgno, true);
        } else {
            $instance = new self();
        }
        
        $instance->setSubject($result->subject);
        
        $instance->setFrom($result->from);
        
        $instance->setDate($result->date);
        
        $instance->setRead($result->seen);
        
        $instance->setMsgNo($result->msgno);
        
        $instance->setTo($result->to);
        
        return $instance;
        
    }
    
    public function readFromDB($uid, $folder = '') {
        
        $db = PearDatabase::getInstance();
        
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        
        $query = "SELECT * FROM vtiger_mailmanager_mailrecord WHERE userid = ? AND muid = ?";
        
        $params = array($currentUserModel->getId(), $uid);
        
        if($folder) {
            $query .= " AND mfolder = ?";
            array_push($params, $folder);
        }
        
        $result = $db->pquery($query, $params);
        
        if ($db->num_rows($result)){
            
            $resultrow = $db->fetch_array($result);
            
            $this->mUid  = decode_html($resultrow['muid']);
            
            $this->_from = Zend_Json::decode(decode_html($resultrow['mfrom']));
            
            $this->_to   = Zend_Json::decode(decode_html($resultrow['mto']));
            
            $this->_cc   = Zend_Json::decode(decode_html($resultrow['mcc']));
            
            $this->_bcc  = Zend_Json::decode(decode_html($resultrow['mbcc']));
            
            $this->_date = $resultrow['mdate'];
            
            $subject = str_replace("_"," ",decode_html($resultrow['msubject']));
            
            $this->_subject = @self::__mime_decode($subject);
            
            $this->_body    = decode_html($resultrow['mbody']);
            
            $this->_charset = decode_html($resultrow['mcharset']);
            
            $this->_isbodyhtml   = intval($resultrow['misbodyhtml'])? true : false;
            
            $this->_plainmessage = intval($resultrow['mplainmessage'])? true:false;
            
            $this->_htmlmessage  = intval($resultrow['mhtmlmessage'])? true :false;
            
            $this->_uniqueid     = decode_html($resultrow['muniqueid']);
            
            $this->_bodyparsed   = intval($resultrow['mbodyparsed'])? true : false;
            
            return true;
            
        } else {
            return false;
        }
    }
    
    public function saveToDB($msgData, $folder) {
        
        $db = PearDatabase::getInstance();
        
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        
        $savedtime = strtotime("now");
        
        if(!$msgData->cc)
            $msgData->cc = array();
        
        if(!$msgData->_bcc)
            $msgData->_bcc = array();
            
        if($msgData->from){
            
            $from_name = $msgData->from;
            
            preg_match('/<(.*?)>/', $msgData->from, $match);
            
            $msgData->from = $match[1];
            
            $msgData->mfromname = str_replace('<'.$match[1].'>', '', $from_name);
        }
        
        if($msgData->to){
            $header_value = explode(",",$msgData->to);
            $header_value = array_map(function($value){
                preg_match('/<(.*?)>/', $value, $match);
                if(!empty($match))
                    return $match[1];
                return trim($value);
            },$header_value);
            $msgData->to = $header_value;
         }
                
        if($msgData->_bcc){
            $header_value = explode(",",$msgData->_bcc);
            $header_value = array_map(function($value){
                preg_match('/<(.*?)>/', $value, $match);
                if(!empty($match))
                    return $match[1];
                    return trim($value);
            },$header_value);
            $msgData->_bcc = $header_value;
        }
        
        if($msgData->date){
            $msgData->date = strtotime($msgData->date);
        }
        
        $params = array($currentUserModel->getId());
        $params[] = $msgData->uid;
        $params[] = Zend_Json::encode(array($msgData->from));
        $params[] = Zend_Json::encode($msgData->to);
        $params[] = Zend_Json::encode($msgData->cc);
        $params[] = Zend_Json::encode($msgData->_bcc);
        $params[] = $msgData->date;
        $params[] = $msgData->subject;
        $params[] = $msgData->_body;
        $params[] = $msgData->_charset;
        $params[] = $msgData->_uniqueid;
        $params[] = $msgData->_bodyparsed;
        $params[] = $savedtime;
        $params[] = $folder->getId();
        $params[] = !preg_match('#(?<=<)\w+(?=[^<]*?>)#', $msgData->_body) ? true : false;
        $params[] = preg_match('#(?<=<)\w+(?=[^<]*?>)#', $msgData->_body) ? true : false;
        $params[] = false;
        
        $db->pquery("INSERT INTO vtiger_mailmanager_mailrecord (userid, muid, mfrom, mto, mcc, mbcc, 
        mdate, msubject, mbody, mcharset, muniqueid,
        mbodyparsed, lastsavedtime, mfolder, mplainmessage, misbodyhtml, mhtmlmessage) VALUES (".generateQuestionMarks($params).")", $params);
        
        /*if (!empty($msgData->_attachments)) {
            
            foreach($msgData->_attachments as $index => $aValue) {
                
                $attachInfo = $this->__SaveAttachmentFile($aValue->filename, $aValue->data);
                
                if(is_array($attachInfo) && !empty($attachInfo)) {
                    
                    $db->pquery("INSERT INTO vtiger_mailmanager_mailattachments
			         (userid, muid, attachid, aname, path, lastsavedtime) VALUES (?, ?, ?, ?, ?, ?)",
                        array($currentUserModel->getId(), $msgData->uid, 
                        $attachInfo['attachid'], $attachInfo['name'], $attachInfo['path'], $savedtime));
                }
            }
        }
                
        if(is_array($msgData->_inline_attachments)) {
            
            foreach($msgData->_inline_attachments as $index => $info) {
                $attachInfo = $this->__SaveAttachmentFile($info->filename, $info->data);
                
                if(is_array($attachInfo) && !empty($attachInfo)) {
                    
                    $db->pquery("INSERT INTO vtiger_mailmanager_mailattachments (userid, muid, attachid, aname, path, lastsavedtime, cid) VALUES (?, ?, ?, ?, ?, ?, ?)",
                    array($currentUserModel->getId(), $msgData->uid, $attachInfo['attachid'], 
                    @self::__mime_decode($attachInfo['name']), $attachInfo['path'],
                    $savedtime, $info->cid));
                }
            }
        }*/
                
    }    
}

?>
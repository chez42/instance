<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

vimport('~~/modules/MailManager/helpers/Office365Record.php');

class MailManager_Office365Message_Model extends MailManager_Office365Record  {

	/**
	 * Sets the Imap connection
	 * @var String
	 */
	protected $mBox;

	/**
	 * Marks the mail Read/UnRead
	 * @var Boolean
	 */
	protected $mRead = false;

	protected $hasAttachments = false;
	
	/**
	 * Sets the Mail Message Number
	 * @var Integer
	 */
	protected $mMsgNo;

	/**
	 * Sets the Mail Unique Number
	 * @var Integer
	 */
	protected $mUid;

	/**
	 * Constructor which gets the Mail details from the server
	 * @param String $mBox - Mail Box Connection string
	 * @param Integer $msgno - Mail Message Number
	 * @param Boolean $fetchbody - Used to save the mail information to DB
	 */	//16.01.2019 wd Outh	public function __construct($mBox=false, $msgno=false, $fetchbody=false, $mail_data = array()) {	    	    if ($mBox && $msgno) {	        	        $this->mBox = $mBox;	        	        $this->mMsgNo = $msgno;	        	        $loaded = false;	        	        $this->mUid = $this->mMsgNo;	        	        if ($fetchbody) {	            $loaded = $this->readFromDB($this->mUid);	        }	        	        if (!$loaded) {	            	            if( !empty($mail_data) ){	                	                $from_address = array();	                	                $fromName = $mail_data->getFrom()->getEmailAddress()->getName();	                $fromAddress = $mail_data->getFrom()->getEmailAddress()->getAddress();	                	                $from_address[] = $fromAddress;	                	                $to_address = array();	                	                $to = $mail_data->getToRecipients();	                	                foreach($to as $key => $eaddress){	                    if( isset($eaddress['emailAddress']['address']) && $eaddress['emailAddress']['address'] != '' ){	                        $to_address[] = $eaddress['emailAddress']['address'];	                    }	                }	                	                $cc = array();	                	                $cc_all = $mail_data->getCcRecipients();	                	                foreach($cc_all as $key => $eaddress){	                    	                    if( isset($eaddress['emailAddress']['address']) && $eaddress['emailAddress']['address'] != '' ){	                        	                        $cc[] = $eaddress['emailAddress']['address'];	                    }	                }	                	                $bcc = array();	                	                $bcc_all = $mail_data->getBccRecipients();	                	                foreach($bcc_all as $key => $eaddress){	                    	                    if( isset($eaddress['emailAddress']['address']) && $eaddress['emailAddress']['address'] != '' ){	                        	                        $bcc[] = $eaddress['emailAddress']['address'];	                    }	                }	                	                $this->_from = $from_address;	                	                $this->_to = $to_address;	                	                $this->_uniqueid = $mail_data->getId();	                	                $this->_cc   = $cc;	                	                $this->_bcc  = $bcc;	                	                $date = $mail_data->getReceivedDateTime();	                	                $this->_date = strtotime($date->format('Y-m-d H:i:s'));	                	                $this->_subject = self::__mime_decode($mail_data->getSubject());	                	                if(!$this->_subject) $this->_subject = 'Untitled';                                   	                if($fetchbody){	                    	                    $this->_plainmessage = '';	                    	                    $this->_htmlmessage = '';	                    	                    $this->_body = '';	                    	                    $this->_isbodyhtml = false;	                    	                    $body = $mail_data->getBody();	                    	                    $content_type = $body->getContentType();	                    	                    $data = $body->getContent();	                    	                    $data = self::__convert_encoding($data, 'UTF-8');	                    	                    if (strtolower($content_type)=='text') $this->_plainmessage .= trim($data) ."\n\n";	                    	                    else $this->_htmlmessage .= $data ."<br><br>";	                    	                    if($this->_htmlmessage != '') {	                        	                        $this->_body = $this->_htmlmessage;	                        	                        $this->_isbodyhtml = true;	                        	                    } else {	                        	                        $this->_body = $this->_plainmessage;	                    }	                    	                    if( $mail_data->getAttachments() ){	                        	                        $this->setAttachments('true');	                        	                        $all_attach = $mail_data->getAttachments();	                        	                        for($k=0; $k<count($all_attach); $k++){	                            	                            $this->_attachments[$all_attach[$k]['name']] = base64_decode($all_attach[$k]['contentBytes']);	                        }	                    }	                    	                    $this->_bodyparsed = true;	                }	            }	            if ($fetchbody) {	                $loaded = $this->saveToDB($this->mUid);	            }	        }	        	        if ($loaded) {	            $this->setRead(true);	            $this->setMsgNo($msgno);	        }	    }	}	

	/**
	 * Gets the Mail Body and Attachments
	 * @param String $imap - Mail Box connection string
	 * @param Integer $messageid - Mail Number
	 * @param Object $p
	 * @param Integer $partno
	 */
	// Modified: http://in2.php.net/manual/en/function.imap-fetchstructure.php#85685
	public function __getpart($imap, $messageid, $p, $partno) {
		// $partno = '1', '2', '2.1', '2.1.3', etc if multipart, 0 if not multipart

		if($partno) {
			$maxDownLoadLimit = MailManager_Config_Model::get('MAXDOWNLOADLIMIT');
			if($p->bytes < $maxDownLoadLimit) {
				$data = imap_fetchbody($imap,$messageid,$partno);  // multipart
			}
		} else {
			$data = imap_body($imap,$messageid); //not multipart
		}
		// Any part may be encoded, even plain text messages, so check everything.
    	if ($p->encoding==4) $data = quoted_printable_decode($data);
		elseif ($p->encoding==3) $data = base64_decode($data);
		// no need to decode 7-bit, 8-bit, or binary

    	// PARAMETERS
	    // get all parameters, like charset, filenames of attachments, etc.
    	$params = array();
	    if ($p->parameters) {
			foreach ($p->parameters as $x) $params[ strtolower( $x->attribute ) ] = $x->value;
		}
	    if ($p->dparameters) {
			foreach ($p->dparameters as $x) $params[ strtolower( $x->attribute ) ] = $x->value;
		}

		// ATTACHMENT
    	// Any part with a filename is an attachment,
	    // so an attached text file (type 0) is not mistaken as the message.
    	if ($params['filename'] || ($params['name'] && $p->ifid == 0 && empty($p->id))) {
        	// filename may be given as 'Filename' or 'Name' or both
	        $filename = ($params['filename'])? $params['filename'] : $params['name'];
			// filename may be encoded, so see imap_mime_header_decode()
			if(!$this->_attachments) $this->_attachments = Array();
			$this->_attachments[$filename] = $data;  // TODO: this is a problem if two files have same name
	    }
		// embedded images right now are treated as attachments
		elseif ($p->ifdisposition && $p->disposition == "INLINE" && $p->bytes > 0 &&
                $p->subtype != 'PLAIN' && $p->subtype != 'HTML') {
			$this->_attachments["noname".$partno. "." .$p->subtype] = $data;
		} elseif($p->ifid && !empty($p->id)) {
			$filename = ($params['filename'])? $params['filename'] : $params['name'];
			$this->_inline_attachments[] = array('cid'=>substr($p->id, 1,strlen($p->id)-2), 'filename'=>$filename, 'data' => $data);
		}
	    // TEXT
    	elseif ($p->type==0 && $data) {
    		$this->_charset = $params['charset'];  // assume all parts are same charset
    		$data = self::__convert_encoding($data, 'UTF-8', $this->_charset);

        	// Messages may be split in different parts because of inline attachments,
	        // so append parts together with blank row.
    	    if (strtolower($p->subtype)=='plain') $this->_plainmessage .= trim($data) ."\n\n";
	        else $this->_htmlmessage .= $data ."<br><br>";
		}

	    // EMBEDDED MESSAGE
    	// Many bounce notifications embed the original message as type 2,
	    // but AOL uses type 1 (multipart), which is not handled here.
    	// There are no PHP functions to parse embedded messages,
	    // so this just appends the raw source to the main message.
    	elseif ($p->type==2 && $data) {
			$this->_plainmessage .= trim($data) ."\n\n";
	    }

    	// SUBPART RECURSION
	    if ($p->parts) {
        	foreach ($p->parts as $partno0=>$p2)
            	$this->__getpart($imap,$messageid,$p2,$partno.'.'.($partno0+1));  // 1.2, 1.2.1, etc.
    	}
	}

	/**
	 * Clears the cache data
	 * @global PearDataBase Instance $db
	 * @global Users Instance $currentUserModel
	 * @param Integer $waybacktime
	 */
	public static function pruneOlderInDB($waybacktime) {
		$db = PearDatabase::getInstance();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();

		//remove the saved attachments
		self::removeSavedAttachmentFiles($waybacktime);

		$db->pquery("DELETE FROM vtiger_mailmanager_mailrecord
		WHERE userid=? AND lastsavedtime < ?", array($currentUserModel->getId(), $waybacktime));
		$db->pquery("DELETE FROM vtiger_mailmanager_mailattachments
		WHERE userid=? AND lastsavedtime < ?", array($currentUserModel->getId(), $waybacktime));
	}

	/**
	 * Used to remove the saved attachments
	 * @global Users Instance $currentUserModel
	 * @global PearDataBase Instance $db
	 * @param Integer $waybacktime - timestamp
	 */
	public static function removeSavedAttachmentFiles($waybacktime) {
		$db = PearDatabase::getInstance();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();

		$mailManagerAttachments = $db->pquery("SELECT attachid, aname, path FROM vtiger_mailmanager_mailattachments
			WHERE userid=? AND lastsavedtime < ?", array($currentUserModel->getId(), $waybacktime));

		for($i=0; $i<$db->num_rows($mailManagerAttachments); $i++) {
			$atResultRow = $db->raw_query_result_rowdata($mailManagerAttachments, $i);

			$db->pquery("UPDATE vtiger_crmentity set deleted = 1 WHERE crmid = ?", array($atResultRow['attachid']));

			$filepath = $atResultRow['path'] ."/". $atResultRow['attachid'] ."_". $atResultRow['aname'];
			if(file_exists($filepath)) {
				unlink($filepath);
			}
		}
	}

	/**
	 * Reads the Mail information from the Database
	 * @global PearDataBase Instance $db
	 * @global User Instance $currentUserModel
	 * @param Integer $uid
	 * @return Boolean
	 */

	public function readFromDB($uid) {
		$db = PearDatabase::getInstance();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$result = $db->pquery("SELECT * FROM vtiger_mailmanager_mailrecord
			WHERE userid=? AND muid=?", array($currentUserModel->getId(), $uid));
		if ($db->num_rows($result)) {
			$resultrow = $db->fetch_array($result);
			$this->mUid  = decode_html($resultrow['muid']);

			$this->_from = Zend_Json::decode(decode_html($resultrow['mfrom']));
			$this->_to   = Zend_Json::decode(decode_html($resultrow['mto']));
			$this->_cc   = Zend_Json::decode(decode_html($resultrow['mcc']));
			$this->_bcc  = Zend_Json::decode(decode_html($resultrow['mbcc']));

			$this->_date	= decode_html($resultrow['mdate']);
			$this->_subject = str_replace("_"," ",decode_html($resultrow['msubject']));
			$this->_body    = decode_html($resultrow['mbody']);
			$this->_charset = decode_html($resultrow['mcharset']);

			$this->_isbodyhtml   = intval($resultrow['misbodyhtml'])? true : false;
			$this->_plainmessage = intval($resultrow['mplainmessage'])? true:false;
			$this->_htmlmessage  = intval($resultrow['mhtmlmessage'])? true :false;
			$this->_uniqueid     = decode_html($resultrow['muniqueid']);
			$this->_bodyparsed   = intval($resultrow['mbodyparsed'])? true : false;

			return true;
		}
		return false;
	}

	/**
	 * Loads the Saved Attachments from the DB
	 * @global PearDataBase Instance$db
	 * @global Users Instance $currentUserModel
	 * @global Array $upload_badext - List of bad extensions
	 * @param Boolean $withContent - Used to load the Attachments with/withoud content
	 * @param String $aName - Attachment Name
	 */
	protected function loadAttachmentsFromDB($withContent, $aName=false, $aId=false) {	    $db = PearDatabase::getInstance();	    $currentUserModel = Users_Record_Model::getCurrentUserModel();	    	    if (empty($this->_attachments)) {	        $this->_attachments = array();	        	        $params = array($currentUserModel->getId(), $this->muid());	        	        $filteredColumns = "aname, attachid, path, cid";	        	        $whereClause = "";	        if ($aName) { $whereClause .= " AND aname=?"; $params[] = $aName; }	        if ($aId)   { $whereClause .= " AND aid=?"; $params[] = $aId; }	        	        $atResult = $db->pquery("SELECT {$filteredColumns} FROM vtiger_mailmanager_mailattachments						WHERE userid=? AND muid=? $whereClause", $params);	        	        if ($db->num_rows($atResult)) {	            for($atIndex = 0; $atIndex < $db->num_rows($atResult); ++$atIndex) {	                $atResultRow = $db->raw_query_result_rowdata($atResult, $atIndex);	                if($withContent) {	                    $binFile = sanitizeUploadFileName($atResultRow['aname'], vglobal('upload_badext'));	                    $saved_filename = $atResultRow['path'] . $atResultRow['attachid']. '_' .$binFile;	                    if(file_exists($saved_filename)) $fileContent = @fread(fopen($saved_filename, "r"), filesize($saved_filename));	                }	                if(!empty($atResultRow['cid'])) {	                    $this->_inline_attachments[] = array('filename'=>$atResultRow['aname'], 'cid'=>$atResultRow['cid']);	                }	                $filePath = $atResultRow['path'].$atResultRow['attachid'].'_'.sanitizeUploadFileName($atResultRow['aname'], vglobal('upload_badext'));	                $fileSize = $this->convertFileSize(filesize($filePath));	                $data = ($withContent? $fileContent: false);	                $this->_attachments[] = array('filename'=>$atResultRow['aname'], 'data' => $data, 'size' => $fileSize, 'path' => $filePath, 'attachid' => $atResultRow['attachid']);	                unset($fileContent); // Clear immediately	            }	            	            $atResult->free();	            unset($atResult); // Indicate cleanup	        }	    }	}

	/**
	 * Save the Mail information to DB
	 * @global PearDataBase Instance $db
	 * @global Users Instance $currentUserModel
	 * @param Integer $uid - Mail Unique Number
	 * @return Boolean
	 */
	protected function saveToDB($uid) {
		$db = PearDatabase::getInstance();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();		$savedtime = strtotime("now");
		$params = array($currentUserModel->getId());
		$params[] = $uid;
		$params[] = Zend_Json::encode($this->_from);
		$params[] = Zend_Json::encode($this->_to);
		$params[] = Zend_Json::encode($this->_cc);
		$params[] = Zend_Json::encode($this->_bcc);
		$params[] = $this->_date;
		$params[] = $this->_subject;
		$params[] = $this->_body;
		$params[] = $this->_charset;
		$params[] = $this->_isbodyhtml;
		$params[] = $this->_plainmessage;
		$params[] = $this->_htmlmessage;
		$params[] = $this->_uniqueid;
		$params[] = $this->_bodyparsed;
		$params[] = $savedtime;

		$db->pquery("INSERT INTO vtiger_mailmanager_mailrecord (userid, muid, mfrom, mto, mcc, mbcc,
				mdate, msubject, mbody, mcharset, misbodyhtml, mplainmessage, mhtmlmessage, muniqueid,
				mbodyparsed, lastsavedtime) VALUES (".generateQuestionMarks($params).")", $params);

		// Take care of attachments...
		if (!empty($this->_attachments)) {
			foreach($this->_attachments as $aName => $aValue) {

				$attachInfo = $this->__SaveAttachmentFile($aName, $aValue);

				if(is_array($attachInfo) && !empty($attachInfo)) {
					$db->pquery("INSERT INTO vtiger_mailmanager_mailattachments
					(userid, muid, attachid, aname, path, lastsavedtime) VALUES (?, ?, ?, ?, ?, ?)",
					array($currentUserModel->getId(), $uid, $attachInfo['attachid'], $attachInfo['name'], $attachInfo['path'], $savedtime));

					unset($this->_attachments[$aName]);					// This is needed first when we save attachment with invalid file extension,
					$this->_attachments[$attachInfo['name']] = $aValue; // so the file name has to renamed.
				}
				unset($aValue);
			}
		}

		if(is_array($this->_inline_attachments))
		foreach($this->_inline_attachments as $index => $info) {
			$attachInfo = $this->__SaveAttachmentFile($info['filename'], $info['data']);
			if(is_array($attachInfo) && !empty($attachInfo)) {
				$db->pquery("INSERT INTO vtiger_mailmanager_mailattachments
				(userid, muid, attachid, aname, path, lastsavedtime, cid) VALUES (?, ?, ?, ?, ?, ?, ?)",
				array($currentUserModel->getId(), $uid, $attachInfo['attachid'], $attachInfo['name'], $attachInfo['path'], $savedtime, $info['cid']));

				$this->_attachments[$info['filename']] = $info['data']; // so the file name has to renamed.
			}
			unset($aValue);
		}

		return true;
	}

	/**
	 * Save the Mail Attachments to DB
	 * @global PearDataBase Instance $db
	 * @global Users Instance $currentUserModel
	 * @global Array $upload_badext
	 * @param String $filename - name of the file
	 * @param Text $filecontent
	 * @return Array with attachment information
	 */
	public function __SaveAttachmentFile($filename, $filecontent) {
		require_once 'modules/Settings/MailConverter/handlers/MailAttachmentMIME.php';

		$db = PearDatabase::getInstance();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();

		$filename = imap_utf8($filename);
		$dirname = decideFilePath();
		$usetime = $db->formatDate(date('ymdHis'), true);
		$binFile = sanitizeUploadFileName($filename, vglobal('upload_badext'));

		$attachid = $db->getUniqueId('vtiger_crmentity');
		$saveasfile = "$dirname/$attachid". "_" .$binFile;

		$fh = fopen($saveasfile, 'wb');
		fwrite($fh, $filecontent);
		fclose($fh);

		$mimetype = MailAttachmentMIME::detect($saveasfile);

		$db->pquery("INSERT INTO vtiger_crmentity(crmid, smcreatorid, smownerid,
				modifiedby, setype, description, createdtime, modifiedtime, presence, deleted)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
				Array($attachid, $currentUserModel->getId(), $currentUserModel->getId(), $currentUserModel->getId(), "MailManager Attachment", $binFile, $usetime, $usetime, 1, 0));

		$db->pquery("INSERT INTO vtiger_attachments SET attachmentsid=?, name=?, description=?, type=?, path=?",
			Array($attachid, $binFile, $binFile, $mimetype, $dirname));

		$attachInfo = array('attachid'=>$attachid, 'path'=>$dirname, 'name'=>$binFile, 'type'=>$mimetype, 'size'=>filesize($saveasfile));

		return $attachInfo;
	}

	/**
	 * Gets the Mail Attachments
	 * @param Boolean $withContent
	 * @param String $aName
	 * @return List of Attachments
	 */
	public function attachments($withContent=true, $aName=false, $aId=false) {
	    $this->loadAttachmentsFromDB($withContent, $aName, $aId);
		return $this->_attachments;
	}

	public function inlineAttachments() {
		return $this->_inline_attachments;
	}

	/**
	 * Gets the Mail Subject
	 * @param Boolean $safehtml
	 * @return String
	 */
	public function subject($safehtml=true) {
		$mailSubject = str_replace("_", " ", $this->_subject);
		if ($safehtml==true) {
			return MailManager_Utils_Helper::safe_html_string($mailSubject);
		}
		return $mailSubject;
	}

	/**
	 * Sets the Mail Subject
	 * @param String $subject
	 */
	public function setSubject($subject) {
		$mailSubject = str_replace("_", " ", $subject);
		$this->_subject = @self::__mime_decode($mailSubject);
	}

	/**
	 * Gets the Mail Body
	 * @param Boolean $safehtml
	 * @return String
	 */
	public function body($safehtml=true) {
		return $this->getBodyHTML($safehtml);
	}

	/**
	 * Gets the Mail Body
	 * @param Boolean $safehtml
	 * @return String
	 */
	public function getBodyHTML($safehtml=true) {
		$bodyhtml = parent::getBodyHTML();
		if ($safehtml) {
			$bodyhtml = MailManager_Utils_Helper::safe_html_string($bodyhtml);
		}
		return $bodyhtml;
	}

	/**
	 * Gets the Mail From
	 * @param Integer $maxlen
	 * @return string
	 */
	public function from($maxlen = 0) {
		$fromString = $this->_from;
		/*if(is_array($fromString)){
			$fromString = $fromString['Name'];
		}*/
		if ($maxlen && strlen($fromString) > $maxlen) {
			$fromString = substr($fromString, 0, $maxlen-3).'...';
		}
		return $fromString;
	}

	/**
	 * Sets the Mail From Email Address
	 * @param Email $from
	 */
	public function setFrom($from) {
		$mailFrom = str_replace("_", " ", $from);
		$this->_from = @self::__mime_decode($mailFrom);
	}

	/**
	 * Gets the Mail To Email Addresses
	 * @return Email(s)
	 */
	public function to() {
		return $this->_to;
	}

	/**
	 * Gets the Mail CC Email Addresses
	 * @return Email(s)
	 */
	public function cc() {
		return $this->_cc;
	}

	/**
	 * Gets the Mail BCC Email Addresses
	 * @return Email(s)
	 */
	public function bcc() {
		return $this->_bcc;
	}

	/**
	 * Gets the Mail Unique Identifier
	 * @return String
	 */
	public function uniqueid() {
		return $this->_uniqueid;
	}

	/**
	 * Gets the Mail Unique Number
	 * @return Integer
	 */
	public function muid() {
		// unique message sequence id = imap_uid($msgno)
		return $this->mUid;
	}

	/**
	 * Gets the Mail Date
	 * @param Boolean $format
	 * @return Date
	 */
	public function date($format = false) {
		$date = $this->_date;
		if ($date) {
			if ($format) {
				$dateTimeFormat = Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat(date('Y-m-d H:i:s', strtotime($date)));
				list($date, $time, $AMorPM) = explode(' ', $dateTimeFormat);

				$pos = strpos($dateTimeFormat, date(DateTimeField::getPHPDateFormat()));
				if ($pos === false) {
					return $date;
				} else {
					return $time. ' ' .$AMorPM;
				}
			} else {
				return Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat(date('Y-m-d H:i:s', $date));
			}
		}
		return '';
	}

	/**
	 * Sets the Mail Date
	 * @param Date $date
	 */
	public function setDate($date) {
		$this->_date = $date;
	}

	/**
	 * Checks if the Mail is read
	 * @return Boolean
	 */
	public function isRead() {
		return $this->mRead;
	}

	/**
	 * Sets if the Mail is read
	 * @param Boolean $read
	 */
	public function setRead($read) {
		$this->mRead = $read;
	}

	/**
	 * Gets the Mail Message Number
	 * @param Integer $offset
	 * @return Integer
	 */
	public function msgNo($offset=0) {
		//return $this->mMsgNo + $offset;
		return $this->mMsgNo;
	}

	/**
	 * Sets the Mail Message Number
	 * @param Integer $msgno
	 */
	public function setMsgNo($msgno) {
		$this->mMsgNo = $msgno;
	}
	
	public function hasAttachments() {
		return $this->hasAttachments;
	}
	
	public function setAttachments($val) {
		$this->hasAttachments = $val;
	}

	/**
	 * Sets the Mail Headers
	 * @param Object $result
	 * @return self
	 */
	public static function parseOverview($result) {	    	    $instance = new self();	    	    $instance->setSubject($result->getSubject());	    	    $fromName = !empty($result->getFrom())?$result->getFrom()->getEmailAddress()->getName():'';	    $fromAddress = !empty($result->getFrom())?$result->getFrom()->getEmailAddress()->getAddress():'';	    	    $instance->setFrom($fromName.' <'.$fromAddress.'>');	    	    $date = $result->getReceivedDateTime();	    	    $instance->setDate($date->format('Y-m-d H:i:s'));	    	    if($result->getIsRead()){	        $instance->setRead(true);	    }	     	    if($result->getHasAttachments()){	        $instance->setAttachments(true);	    }	    	    $instance->mUid($result->getId());	    	    $instance->setMsgNo($result->getId());	    	    return $instance;	}	public function getInlineBody() {	    $bodytext = $this->body();	    $bodytext = preg_replace("/<br>/", " ", $bodytext);	    $bodytext = strip_tags($bodytext);	    $bodytext = preg_replace("/\n/", " ", $bodytext);	    return $bodytext;	}		function convertFileSize($size) {	    $type = 'Bytes';	    if($size > 1048575) {	        $size = round(($size/(1024*1024)), 2);	        $type = 'MB';	    } else if($size > 1023) {	        $size = round(($size/1024), 2);	        $type = 'KB';	    }	    return $size.' '.$type;	}		public function getAttachmentIcon($fileName) {	    $ext = pathinfo($fileName, PATHINFO_EXTENSION);	    $icon = '';	    switch(strtolower($ext)) {	        case 'txt' : $icon = 'fa-file-text';	        break;	        case 'doc' :	        case 'docx' : $icon = 'fa-file-word-o';	        break;	        case 'zip' :	        case 'tar' :	        case '7z' :	        case 'apk' :	        case 'bin' :	        case 'bzip' :	        case 'bzip2' :	        case 'gz' :	        case 'jar' :	        case 'rar' :	        case 'xz' : $icon = 'fa-file-archive-o';	        break;	        case 'jpeg' :	        case 'jfif' :	        case 'rif' :	        case 'gif' :	        case 'bmp' :	        case 'jpg' :	        case 'png' : $icon = 'fa-file-image-o';	        break;	        case 'pdf' : $icon = 'fa-file-pdf-o';	        break;	        case 'mp3' :	        case 'wma' :	        case 'wav' :	        case 'ogg' : $icon = 'fa-file-audio-o';	        break;	        case 'xls' :	        case 'xlsx' : $icon = 'fa-file-excel-o';	        break;	        case 'webm' :	        case 'mkv' :	        case 'flv' :	        case 'vob' :	        case 'ogv' :	        case 'ogg' :	        case 'avi' :	        case 'mov' :	        case 'mp4' :	        case 'mpg' :	        case 'mpeg' :	        case '3gp' : $icon = 'fa-file-video-o';	        break;	        default : $icon = 'fa-file-o';	        break;	    }	    	    return $icon;	}
}
?>
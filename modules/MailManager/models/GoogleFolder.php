<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class MailManager_GoogleFolder_Model {
    
    protected $mName;
    protected $mCount;
    protected $mUnreadCount;
    protected $mMails;
    protected $mPageCurrent;
    protected $mPageStart;
    protected $mPageEnd;
    protected $mPageLimit;
    
    protected $mPreviousLink;
    protected $mNextLink;
    
    protected $mId = '';
    protected $mChildFolderCount = 0;
    
    protected $folderLevel = 0;
    
    public $folderId = false;  //16.01.2019 wd Outh
    
    public function __construct($name='') {
        $this->setName($name);
    }
    
    public function setFromArray($array_val = array()){
        
        if($array_val->getName()){
            $this->setName($array_val->getName());
        }
        
        if($array_val->getId()){
            $this->setId($array_val->getId());
        }
        
        if($array_val->getMessagesUnread()){
            $this->setUnreadCount($array_val->getMessagesUnread());
        }
    }
    
    public function name($prefix='') {
        $endswith = false;
        if (!empty($prefix)) {
            $endswith = (strrpos($prefix, $this->mName) === strlen($prefix)-strlen($this->mName));
        }
        if ($endswith) {
            return $prefix;
        } else {
            return $prefix.$this->mName;
        }
    }
    
    public function isSentFolder() {
        $mailBoxModel = MailManager_Mailbox_Model::activeInstance();
        $folderName = $mailBoxModel->folder();
        if($this->mName == $folderName) {
            return true;
        }
        return false;
    }
    
    public function setName($name) {
        $this->mName = $name;
    }
    
    public function getId(){
        return $this->mId;
    }
    
    public function setId($id = ''){
        $this->mId = $id;
    }
    
    public function mailIds(){
        return $this->mMailIds;
    }
    
    public function setMailIds($ids){
        $this->mMailIds = $ids;
    }
    
    public function childFolderCount(){
        return $this->mChildFolderCount;
    }
    
    public function setChildFolderCount($val = 0){
        $this->mChildFolderCount = $val;
    }
    
    public function folderLevel(){
        return $this->folderLevel;
    }
    
    public function setfolderLevel($val = 0){
        $this->folderLevel = $val;
    }
    
    public function setPreviousLink($val) {
        $this->mPreviousLink = $val;
    }
    
    public function setNextLink($val) {
        $this->mNextLink = $val;
    }
    
    public function mails() {
        return $this->mMails;
    }
    
    public function setMails($mails) {
        $this->mMails = $mails;
    }
    
    public function setPaging($start, $end, $limit, $total, $current) {
        $this->mPageStart = intval($start);
        $this->mPageEnd = intval($end);
        $this->mPageLimit = intval($limit);
        $this->mCount = intval($total);
        $this->mPageCurrent = intval($current);
    }
    
    public function pageStart() {
        return $this->mPageStart;
    }
    
    public function pageEnd() {
        return $this->mPageEnd;
    }
    
    public function pageInfo() {
        $offset = 0;
        if($this->mPageCurrent != 0) {	// this is needed as set the start correctly
            $offset = 1;
        }
        $s = max(1, $this->mPageCurrent * $this->mPageLimit + $offset);
        
        $st = ($s==1)? 0 : $s-1;  // this is needed to set end page correctly
        
        $e = min($st + $this->mPageLimit, $this->mCount);
        $t = $this->mCount;
        
        $this->startCount = $s;
        $this->endCount = $e;
        
        return sprintf("%s - %s of %s", $s, $e, $t);
    }
    
    public function pageCurrent($offset=0) {
        return $this->mPageCurrent + $offset;
    }
    
    public function hasNextPage() {
        return ($this->mNextLink != '');
    }
    
    public function hasPrevPage() {
        return ($this->mPreviousLink != '');
    }
    
    public function count() {
        return $this->mCount;
    }
    
    public function setCount($count) {
        $this->mCount = $count;
    }
    
    public function unreadCount() {
        return $this->mUnreadCount;
    }
    
    public function setUnreadCount($unreadCount) {
        $this->mUnreadCount = $unreadCount;
    }
    
    public function getStartCount() {
        return $this->startCount;
    }
    
    public function getEndCount() {
        return $this->endCount;
    }
}

?>
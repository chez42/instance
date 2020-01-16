<?php
include_once 'modules/Vtiger/CRMEntity.php';

class DocuSign extends Vtiger_CRMEntity {
	

    function vtlib_handler($moduleName, $eventType) {
        
        $adb = PearDatabase::getInstance();
        
		$displayLabel = 'DocuSign Settings';
        
		if($eventType == 'module.postinstall') {
            
			$this->addLinks($adb,$displayLabel);
            
			$this->DocuSignTables($adb);
        
		} else if($eventType == 'module.disabled') {
            
			$adb->pquery("DELETE FROM vtiger_settings_field WHERE name=?", array($displayLabel));
			
			$tab_id = Vtiger_Functions::getModuleId('Contacts');
			$linkurl = 'javascript:DocuSign_Js.triggerDocusignEmail("index.php?module=DocuSign&view=MassActionAjax&mode=showSendEmailForm")';
			Vtiger_Link::deleteLink($tab_id, 'DETAILVIEWBASIC', 'Send email with signer', $linkurl);
            
			$tab_id = Vtiger_Functions::getModuleId('DocuSign');
			$linkurl = 'layouts/v7/modules/DocuSign/resources/DocuSign.js';
			Vtiger_Link::deleteLink($tab_id, 'HEADERSCRIPT', 'DocuSignJS', $linkurl);
			
			Vtiger_Cron::deregister('DocuSign');
			
			$tab_id = Vtiger_Functions::getModuleId('Contacts');
			$linkurl = 'javascript:DocuSign_Js.triggerDocusignEmailList("index.php?module=DocuSign&view=MassActionAjax&mode=showSendEmailFormList")';
			Vtiger_Link::deleteLink($tab_id, 'LISTVIEWMASSACTION', 'Send Envelope', $linkurl);
			
			
		} else if($eventType == 'module.enabled') {
            
			$this->addLinks($adb,$displayLabel);
            
			$this->DocuSignTables($adb);
        
		} else if($eventType == 'module.preuninstall') {
			
		} else if($eventType == 'module.preupdate') {
        
		} else if($eventType == 'module.postupdate') {}
    }
    
    
    function addLinks($adb,$displayLabel) {
        
        $tab_id = Vtiger_Functions::getModuleId('DocuSign');
        $linkurl = 'layouts/v7/modules/DocuSign/resources/DocuSign.js';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ?",array($linkurl));
        if(!$adb->num_rows($result)){
            Vtiger_Link::addLink($tab_id, 'HEADERSCRIPT', 'DocuSignJS', $linkurl, '', '0', '', '', '');
        }
       
        $tab_id = Vtiger_Functions::getModuleId('Contacts');
        $linkurl = 'javascript:DocuSign_Js.triggerDocusignEmail("index.php?module=DocuSign&view=MassActionAjax&mode=showSendEmailForm")';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ? AND tabid = ?",array($linkurl, $tab_id));
        if(!$adb->num_rows($result)){
            Vtiger_Link::addLink($tab_id, 'DETAILVIEWBASIC', 'Send email with signer', $linkurl, '', '0', '', '', '');
        }
		$blockid = $adb->query_result(
		$adb->pquery("SELECT blockid FROM vtiger_settings_blocks WHERE label='LBL_OTHER_SETTINGS'",array()),0, 'blockid');
        $sequence = (int)$adb->query_result($adb->pquery("SELECT max(sequence)
			as sequence FROM vtiger_settings_field WHERE blockid=?",array($blockid)),
            0, 'sequence') + 1;
        $fieldid = $adb->getUniqueId('vtiger_settings_field');
        $adb->pquery("INSERT INTO vtiger_settings_field (fieldid,blockid,sequence,name,iconpath,description,linkto)
		VALUES (?,?,?,?,?,?,?)", array($fieldid, $blockid,$sequence,$displayLabel,'','', 'index.php?parent=Settings&module=DocuSign&view=Settings'));
		
        Vtiger_Cron::register('DocuSign', 'cron/modules/DocuSign/SyncDocuSignFiles.service', 0);
        
        $tab_id = Vtiger_Functions::getModuleId('Contacts');
        $linkurl = 'javascript:DocuSign_Js.triggerDocusignEmailList("index.php?module=DocuSign&view=MassActionAjax&mode=showSendEmailFormList")';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ? AND tabid = ?",array($linkurl, $tab_id));
        if(!$adb->num_rows($result)){
            Vtiger_Link::addLink($tab_id, 'LISTVIEWMASSACTION', 'Send Envelope', $linkurl, '', '0', '', '', '');
        }
         
        
	}
	
	function DocuSignTables($adb){

	    $adb->pquery("CREATE TABLE IF NOT EXISTS vtiger_document_designer_auth_settings ( 
        clientid VARCHAR(250) NULL , 
        clientsecret VARCHAR(250) NULL , 
        server VARCHAR(200) NULL );");
	    
	    $adb->pquery("CREATE TABLE IF NOT EXISTS vtiger_document_designer_configuration ( 
        userid INT(19) NULL , 
        access_token TEXT NULL , 
        refresh_token TEXT NULL , 
        token_type VARCHAR(250) NULL ,
        expires_in VARCHAR(250) NULL );");
	    
	    $adb->pquery("CREATE TABLE IF NOT EXISTS vtiger_sync_docusign_records ( 
        userid INT(11) NULL , 
        envelopeid VARCHAR(250) NULL,
        contactid INT(19) NULL );");
	    
	}
    
}
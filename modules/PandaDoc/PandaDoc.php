<?php
include_once 'modules/Vtiger/CRMEntity.php';

class PandaDoc extends Vtiger_CRMEntity {
    
    function vtlib_handler($moduleName, $eventType) {
        
        $adb = PearDatabase::getInstance();
        
        $displayLabel = 'PandaDoc Settings';
        
        if($eventType == 'module.postinstall') {
            
            $this->addLinks($adb,$displayLabel);
            
            $this->PandaDocTables($adb);
            
        } else if($eventType == 'module.disabled') {
            
            $adb->pquery("DELETE FROM vtiger_settings_field WHERE name=?", array($displayLabel));
            
            $tab_id = Vtiger_Functions::getModuleId('Contacts');
            $linkurl = 'javascript:PandaDoc_Js.triggerPandaDocEmail("index.php?module=PandaDoc&view=MassActionAjax&mode=showSendEmailForm")';
            Vtiger_Link::deleteLink($tab_id, 'DETAILVIEWBASIC', 'Send with PandaDoc', $linkurl);
            
            $tab_id = Vtiger_Functions::getModuleId('PandaDoc');
            $linkurl = 'layouts/v7/modules/PandaDoc/resources/PandaDoc.js';
            Vtiger_Link::deleteLink($tab_id, 'HEADERSCRIPT', 'PandaDocJS', $linkurl);
            
            Vtiger_Cron::deregister('DocuSign');
            
            $tab_id = Vtiger_Functions::getModuleId('Contacts');
            $linkurl = 'javascript:PandaDoc_Js.triggerPandaDocEmailList("index.php?module=PandaDoc&view=MassActionAjax&mode=showSendEmailFormList")';
            Vtiger_Link::deleteLink($tab_id, 'LISTVIEWMASSACTION', 'Send with PandaDoc', $linkurl);
            
            
        } else if($eventType == 'module.enabled') {
            
            $this->addLinks($adb,$displayLabel);
            
            $this->PandaDocTables($adb);
            
        } else if($eventType == 'module.preuninstall') {
            
        } else if($eventType == 'module.preupdate') {
            
        } else if($eventType == 'module.postupdate') {}
    }
    
    
    function addLinks($adb,$displayLabel) {
        
        $tab_id = Vtiger_Functions::getModuleId('PandaDoc');
        $linkurl = 'layouts/v7/modules/PandaDoc/resources/PandaDoc.js';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ?",array($linkurl));
        if(!$adb->num_rows($result)){
            Vtiger_Link::addLink($tab_id, 'HEADERSCRIPT', 'PandaDocJS', $linkurl, '', '0', '', '', '');
        }
        
        $tab_id = Vtiger_Functions::getModuleId('Contacts');
        
        $linkurl = 'javascript:PandaDoc_Js.triggerPandaDocEmail("index.php?module=PandaDoc&view=MassActionAjax&mode=showSendEmailForm")';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ? AND tabid = ?",array($linkurl, $tab_id));
        if(!$adb->num_rows($result)){
            Vtiger_Link::addLink($tab_id, 'DETAILVIEWBASIC', 'Send with PandaDoc', $linkurl, '', '0', '', '', '');
        }
        
        
        $blockid = $adb->query_result(
            $adb->pquery("SELECT blockid FROM vtiger_settings_blocks WHERE label='LBL_OTHER_SETTINGS'",array()),0, 'blockid');
        $sequence = (int)$adb->query_result($adb->pquery("SELECT max(sequence)
			as sequence FROM vtiger_settings_field WHERE blockid=?",array($blockid)),
            0, 'sequence') + 1;
        
        $fieldid = $adb->getUniqueId('vtiger_settings_field');
        $adb->pquery("INSERT INTO vtiger_settings_field (fieldid,blockid,sequence,name,iconpath,description,linkto)
        VALUES (?,?,?,?,?,?,?)", array($fieldid, $blockid,$sequence,$displayLabel,'','', 'index.php?parent=Settings&module=PandaDoc&view=Settings'));
        
        $linkurl = 'javascript:PandaDoc_Js.triggerPandaDocEmailList("index.php?module=PandaDoc&view=MassActionAjax&mode=showSendEmailFormList")';
        
        $result = $adb->pquery("select * from vtiger_links where linkurl = ? 
        AND tabid = ?",array($linkurl, $tab_id));
        
        if(!$adb->num_rows($result)){
            Vtiger_Link::addLink($tab_id, 'LISTVIEWMASSACTION', 'Send with PandaDoc', $linkurl, '', '0', '', '', '');
        }
        
        Vtiger_Cron::register('PandaDoc', 'cron/modules/PandaDoc/SyncPandaDocFiles.service', 0);
    
    }
    
    function PandaDocTables($adb){
        
        $adb->pquery("CREATE TABLE IF NOT EXISTS vtiger_pandadoc_configuration (
            userid INT(19) NULL ,
            access_token TEXT NULL ,
            refresh_token TEXT NULL ,
            token_type VARCHAR(250) NULL ,
            expires_in VARCHAR(250) NULL );
        ");
        
        $adb->pquery("CREATE TABLE IF NOT EXISTS vtiger_sync_pandadoc_records (
            userid INT(11) NULL ,
            documentid VARCHAR(250) NULL,
            contactid INT(19) NULL );");
        
    }
    
}
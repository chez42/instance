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
            $linkurl = 'javascript:PandaDoc_Js.sendPandaDocDocument("index.php?module=PandaDoc&view=MassActionAjax&mode=sendPandaDocDocument")';
            Vtiger_Link::deleteLink($tab_id, 'DETAILVIEW', 'Send Document with PandaDoc', $linkurl);
            
            $tab_id = Vtiger_Functions::getModuleId('PandaDoc');
            $linkurl = 'layouts/v7/modules/PandaDoc/resources/PandaDoc.js';
            Vtiger_Link::deleteLink($tab_id, 'HEADERSCRIPT', 'PandaDocJS', $linkurl);
            
            $linkurl = 'modules/PandaDoc/resources/pandadoc-js-sdk.css';
            Vtiger_Link::deleteLink($tab_id, 'HEADERCSS', 'PandaDocCSS', $linkurl);
            
            $linkurl = 'modules/PandaDoc/resources/pandadoc-js-sdk.min.js';
            Vtiger_Link::deleteLink($tab_id, 'HEADERSCRIPT', 'PandaDocSDKJS', $linkurl);
            
            Vtiger_Cron::deregister('PandaDoc');
            
            
        } else if($eventType == 'module.enabled') {
            
            $this->addLinks($adb,$displayLabel);
            
            $this->PandaDocTables($adb);
            
        } else if($eventType == 'module.preuninstall') {
            
        } else if($eventType == 'module.preupdate') {
            
        } else if($eventType == 'module.postupdate') {}
    }
    
    
    function addLinks($adb, $displayLabel) {
        
        $tab_id = Vtiger_Functions::getModuleId('PandaDoc');
        $linkurl = 'layouts/v7/modules/PandaDoc/resources/PandaDoc.js';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ?",array($linkurl));
        if(!$adb->num_rows($result)){
            Vtiger_Link::addLink($tab_id, 'HEADERSCRIPT', 'PandaDocJS', $linkurl, '', '0', '', '', '');
        }
        
        $linkurl = 'modules/PandaDoc/resources/pandadoc-js-sdk.css';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ?",array($linkurl));
        if(!$adb->num_rows($result)){
            Vtiger_Link::addLink($tab_id, 'HEADERCSS', 'PandaDocCSS', $linkurl, '', '0', '', '', '');
        }
        
        $linkurl = 'modules/PandaDoc/resources/pandadoc-js-sdk.min.js';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ?",array($linkurl));
        if(!$adb->num_rows($result)){
            Vtiger_Link::addLink($tab_id, 'HEADERSCRIPT', 'PandaDocSDKJS', $linkurl, '', '0', '', '', '');
        }
        
        
        $tab_id = Vtiger_Functions::getModuleId('Contacts');
        
        $linkurl = 'javascript:PandaDoc_Js.sendPandaDocDocument("index.php?module=PandaDoc&view=MassActionAjax&mode=sendPandaDocDocument")';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ? AND tabid = ?",array($linkurl, $tab_id));
        if(!$adb->num_rows($result)){
            Vtiger_Link::addLink($tab_id, 'DETAILVIEW', 'Send Document with PandaDoc', $linkurl, '', '0', '', '', '');
        }
        
        $blockid = $adb->query_result(
            $adb->pquery("SELECT blockid FROM vtiger_settings_blocks WHERE label='LBL_OTHER_SETTINGS'",array()),0, 'blockid');
        $sequence = (int)$adb->query_result($adb->pquery("SELECT max(sequence)
			as sequence FROM vtiger_settings_field WHERE blockid=?",array($blockid)),
            0, 'sequence') + 1;
        
        $fieldid = $adb->getUniqueId('vtiger_settings_field');
        $adb->pquery("INSERT INTO vtiger_settings_field (fieldid,blockid,sequence,name,iconpath,description,linkto)
        VALUES (?,?,?,?,?,?,?)", array($fieldid, $blockid,$sequence,$displayLabel,'','', 'index.php?parent=Settings&module=PandaDoc&view=Settings'));
        
        Vtiger_Cron::register('PandaDoc', 'cron/modules/PandaDoc/SyncPandaDocFiles.service', 0);
    
    }
    
    function PandaDocTables($adb){
        
        $adb->pquery("CREATE TABLE IF NOT EXISTS vtiger_pandadoc_oauth (
         `userid` int(19) DEFAULT NULL,
         `access_token` text,
         `refresh_token` text,
         `token_type` varchar(250) DEFAULT NULL,
         `expires_in` varchar(250) DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1");
        
        $adb->pquery("CREATE TABLE IF NOT EXISTS `vtiger_pandadocdocument_reference` (
 `crm_reference` varchar(100) DEFAULT NULL,
 `userid` int(11) DEFAULT NULL,
 UNIQUE KEY `crmid` (`crm_reference`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");
        
    }
    
}
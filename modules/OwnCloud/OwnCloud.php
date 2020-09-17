<?php
include_once 'modules/Vtiger/CRMEntity.php';

class OwnCloud extends Vtiger_CRMEntity {
    
    function vtlib_handler($moduleName, $eventType) {
        
        $adb = PearDatabase::getInstance();
        
        $displayLabel = 'OwnCloud Settings';
        
        if($eventType == 'module.postinstall') {
            
            $this->addLinks($adb,$displayLabel);
            
            $this->OwnCloudTables($adb);
            
        } else if($eventType == 'module.disabled') {
            
            $tab_id = Vtiger_Functions::getModuleId('OwnCloud');
            
            $linkurl = 'layouts/v7/modules/OwnCloud/resources/OwnCloud.js';
            
            Vtiger_Link::deleteLink($tab_id, 'HEADERSCRIPT', 'OwnCloudJS', $linkurl);
            
            $adb->pquery("DELETE FROM vtiger_settings_field WHERE name=?", array($displayLabel));
            
            $tab_id = Vtiger_Functions::getModuleId('Documents');
            
            $linkurl = 'javascript:OwnCloud_Js.triggerGetOwnCloudFolders("index.php?module=OwnCloud&view=GetFolders")';
            
            Vtiger_Link::deleteLink($tab_id, 'LISTVIEWMASSACTION', 'Sync with OwnCloud', $linkurl);
            
            
        } else if($eventType == 'module.enabled') {
            
            $this->addLinks($adb,$displayLabel);
            
            $this->OwnCloudTables($adb);
            
        } else if($eventType == 'module.preuninstall') {
            
        } else if($eventType == 'module.preupdate') {
            
        } else if($eventType == 'module.postupdate') {}
    }
    
    
    function addLinks($adb,$displayLabel) {
        
        $tab_id = Vtiger_Functions::getModuleId('OwnCloud');
        
        $linkurl = 'layouts/v7/modules/OwnCloud/resources/OwnCloud.js';
        
        $result = $adb->pquery("select * from vtiger_links where linkurl = ?",array($linkurl));
        if(!$adb->num_rows($result)){
            Vtiger_Link::addLink($tab_id, 'HEADERSCRIPT', 'OwnCloudJS', $linkurl, '', '0', '', '', '');
        }
        
        $blockid = $adb->query_result(
            $adb->pquery("SELECT blockid FROM vtiger_settings_blocks WHERE label='LBL_OTHER_SETTINGS'",array()),0, 'blockid');
        
        $sequence = (int)$adb->query_result($adb->pquery("SELECT max(sequence)
			as sequence FROM vtiger_settings_field WHERE blockid=?",array($blockid)),
            0, 'sequence') + 1;
        
        $fieldid = $adb->getUniqueId('vtiger_settings_field');
        
        $adb->pquery("INSERT INTO vtiger_settings_field (fieldid,blockid,sequence,name,iconpath,description,linkto)
        VALUES (?,?,?,?,?,?,?)", array($fieldid, $blockid,$sequence,$displayLabel,'','', 'index.php?parent=Settings&module=OwnCloud&view=Settings'));
        
        $tab_id = Vtiger_Functions::getModuleId('Documents');
        
        $linkurl = 'javascript:OwnCloud_Js.triggerGetOwnCloudFolders("index.php?module=OwnCloud&view=GetFolders")';
        
        $result = $adb->pquery("select * from vtiger_links where linkurl = ? AND tabid = ?",array($linkurl, $tab_id));
        
        if(!$adb->num_rows($result)){
            Vtiger_Link::addLink($tab_id, 'LISTVIEWMASSACTION', 'Sync with OwnCloud', $linkurl, '', '0', '', '', '');
        }
            
    }
    
    function OwnCloudTables($adb){
        
        $adb->pquery("CREATE TABLE IF NOT EXISTS vtiger_owncloud_credentials ( 
        id INT(11) NOT NULL AUTO_INCREMENT, 
        userid INT(19) NOT NULL, 
        username VARCHAR(255) NOT NULL, 
        password VARCHAR(255) NOT NULL, 
        url VARCHAR(255) NOT NULL, 
        PRIMARY KEY (id));");
        
    }
    
}
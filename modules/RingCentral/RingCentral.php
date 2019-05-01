<?php
class RingCentral {
     
    /**
     * Invoked when special actions are performed on the module.
     * @param String Module name
     * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
     */
    function vtlib_handler($moduleName, $eventType) {
        
        $adb = PearDatabase::getInstance();
        $displayLabel = 'RingCentral Settings';
        if($eventType == 'module.postinstall') {
            $this->addLinks($adb,$displayLabel);
            $this->ringCentralTables($adb);
        } else if($eventType == 'module.disabled') {
            $adb->pquery("DELETE FROM vtiger_settings_field
			WHERE name=?", array($displayLabel));
            $this->removeLinks($adb);
        } else if($eventType == 'module.enabled') {
            $this->addLinks($adb,$displayLabel);
            $this->ringCentralTables($adb);
        }
        else if($eventType == 'module.preuninstall') {}
        else if($eventType == 'module.preupdate') {}
        else if($eventType == 'module.postupdate') {}
    }
    
    function removeLinks($adb) {

        $moduleName = 'Contacts';
        
		$tab_id = Vtiger_Functions::getModuleId($moduleName);
        
        $linkurl = 'index.php?module=Contacts&view=Extension&extensionModule=RingCentral&extensionView=Index';
        Vtiger_Link::deleteLink($tab_id, 'EXTENSIONLINK', 'RingCentral', $linkurl);
		
        $linkurl = 'javascript:RingCentral_List_Js.triggerRingCental("index.php?module=RingCentral&view=MassActionAjax&mode=showSendSMSForm")';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ?",array($linkurl));
        if($adb->num_rows($result)){
            Vtiger_Link::deleteLink($tab_id, 'LISTVIEWMASSACTION', 'Ring Central', $linkurl);
        }
        
        $linkurl = 'javascript:RingCentral_List_Js.triggerRingCental("index.php?module=RingCentral&view=MassActionAjax&mode=showSendFaxForm")';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ?",array($linkurl));
        if($adb->num_rows($result)){
            Vtiger_Link::deleteLink($tab_id, 'LISTVIEWMASSACTION', 'Send Fax With RingCentral', $linkurl);
        }
        
        $linkurl = 'layouts/v7/modules/RingCentral/resources/List.js';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ?",array($linkurl));
        if($adb->num_rows($result)){
            Vtiger_Link::deleteLink($tab_id, 'HEADERSCRIPT', 'RingCentralJS', $linkurl);
        }
        
    }
    
    function addLinks($adb,$displayLabel) {
        
        $moduleName = 'Contacts';
        
        $tab_id = Vtiger_Functions::getModuleId($moduleName);
        $linkurl = 'index.php?module=Contacts&view=Extension&extensionModule=RingCentral&extensionView=Index';
        
        $result = $adb->pquery("select * from vtiger_links where linkurl = ?",array($linkurl));
        if($adb->num_rows($result) < 1){
            Vtiger_Link::addLink($tab_id, 'EXTENSIONLINK', 'RingCentral', $linkurl, '', '0', '', '', '');
        }
		
        $linkurl = 'javascript:RingCentral_List_Js.triggerRingCental("index.php?module=RingCentral&view=MassActionAjax&mode=showSendSMSForm")';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ?",array($linkurl));
        if($adb->num_rows($result) < 1){
            Vtiger_Link::addLink($tab_id, 'LISTVIEWMASSACTION', 'Ring Central',
                $linkurl, '', '0', '', '', '');
        }
        
        $linkurl = 'javascript:RingCentral_List_Js.triggerRingCental("index.php?module=RingCentral&view=MassActionAjax&mode=showSendFaxForm")';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ?",array($linkurl));
        if($adb->num_rows($result) < 1){
            Vtiger_Link::addLink($tab_id, 'LISTVIEWMASSACTION', 'Send Fax With RingCentral',
                $linkurl, '', '0', '', '', '');
        }
        
        $linkurl = 'layouts/v7/modules/RingCentral/resources/List.js';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ?",array($linkurl));
        if($adb->num_rows($result) < 1){
            Vtiger_Link::addLink($tab_id, 'HEADERSCRIPT', 'RingCentralJS',
                $linkurl, '', '0', '', '', '');
        }
        
        $blockid = $adb->query_result(
            $adb->pquery("SELECT blockid FROM vtiger_settings_blocks WHERE label='LBL_OTHER_SETTINGS'",array()),0, 'blockid');
        
        $sequence = (int)$adb->query_result($adb->pquery("SELECT max(sequence)
			as sequence FROM vtiger_settings_field WHERE blockid=?",array($blockid)),
            0, 'sequence') + 1;
            
        $fieldid = $adb->getUniqueId('vtiger_settings_field');
        
        $adb->pquery("INSERT INTO vtiger_settings_field (fieldid,blockid,sequence,name,iconpath,description,linkto)
		VALUES (?,?,?,?,?,?,?)", array($fieldid, $blockid,$sequence,$displayLabel,'','', 'index.php?parent=Settings&module=RingCentral&view=Settings'));
		
    
	}
	
	function ringCentralTables($adb){
	    
	    $adb->pquery("CREATE TABLE IF NOT EXISTS vtiger_ringcentral_settings ( 
            userid INT(19) NOT NULL, 
            token TEXT NOT NULL, 
            from_no VARCHAR(250) NULL) ;");
	    
	    $adb->pquery("CREATE TABLE IF NOT EXISTS vtiger_ringcentral_logs (
            id INT(19) NOT NULL AUTO_INCREMENT,
            crmid INT(19) NULL,
            user_id INT(19) NULL,
            type VARCHAR(150) NULL,
            ringcentral_id VARCHAR(150) NULL,
            status VARCHAR(250) NULL,
            content TEXT NULL,
            created_date VARCHAR(250) NULL,
            tono VARCHAR(19) NULL,
            PRIMARY KEY (id)) ;");
	    
	    $adb->pquery("CREATE TABLE IF NOT EXISTS vtiger_ringcentral_oauth_settings ( 
        user_id INT(19) NOT NULL , 
        clientid VARCHAR(250) NOT NULL ,
        clientsecret VARCHAR(500) NOT NULL ) ;");
	    
	}
    
}
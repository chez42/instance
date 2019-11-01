<?php
include_once 'modules/Vtiger/CRMEntity.php';

class RingCentral extends Vtiger_CRMEntity {
	
	var $table_name = 'vtiger_ringcentral';
	
	var $table_index= 'ringcentralid';

	/**
	 * Mandatory table for supporting custom fields.
	 */
	
	var $customFieldTable = Array('vtiger_ringcentralcf', 'ringcentralid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	
	var $tab_name = Array('vtiger_crmentity', 'vtiger_ringcentral', 'vtiger_ringcentralcf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	
	var $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_ringcentral' => 'ringcentralid',
		'vtiger_ringcentralcf'=>'ringcentralid'
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
		'Assigned To' => Array('crmentity','smownerid')
	);
	
	var $list_fields_name = Array (
		'Assigned To' => 'assigned_user_id',
	);

	// Make the field link to detail view
	var $list_link_field = '';

	var $search_fields = Array(
		'Assigned To' => Array('vtiger_crmentity','assigned_user_id'),
	);
	
	var $search_fields_name = Array (
		'Assigned To' => 'assigned_user_id',
	);

	var $popup_fields = Array ();

	var $def_basicsearch_col = '';

	var $def_detailview_recname = '';
	
	var $mandatory_fields = Array('assigned_user_id');

	var $default_order_by = '';
	
	var $default_sort_order = 'ASC';
	
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
            
			$adb->pquery("DELETE FROM vtiger_settings_field WHERE name=?", array($displayLabel));
            
			$this->removeLinks($adb);
        
		} else if($eventType == 'module.enabled') {
            
			$this->addLinks($adb,$displayLabel);
            
			$this->ringCentralTables($adb);
        
		} else if($eventType == 'module.preuninstall') {
			
		} else if($eventType == 'module.preupdate') {
        
		} else if($eventType == 'module.postupdate') {}
    }
    
    function removeLinks($adb) {
        
        $contact_module_model = Vtiger_Module::getInstance( 'Contacts' );
		
        $ringcentral_module_model = Vtiger_Module::getInstance( 'RingCentral' );
        
		$relList = $adb->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid =? AND related_tabid = ?",
        array($contact_module_model->getId(),$ringcentral_module_model->getId()));
        
		if($adb->num_rows($relList)){
            $relationLabel = 'RingCentral';
            $contact_module_model->unsetRelatedList( $ringcentral_module_model , $relationLabel);
        }
       

	   
		$contact_tab_id = Vtiger_Functions::getModuleId('Contacts');
       
	    $linkurl = 'javascript:RingCentral_Js.triggerRingCentral("index.php?module=RingCentral&view=MassActionAjax&mode=showSendSMSForm")';
        Vtiger_Link::deleteLink($contact_tab_id, 'LISTVIEWMASSACTION', 'Send SMS through Ring Central', $linkurl);
        
		$linkurl = 'javascript:RingCentral_Js.triggerRingCentral("index.php?module=RingCentral&view=MassActionAjax&mode=showSendFaxForm")';
		Vtiger_Link::deleteLink($contact_tab_id, 'LISTVIEWMASSACTION', 'Send Fax through RingCentral', $linkurl);
        
        $linkurl = 'javascript:RingCentral_Js.triggerRingCentralDetail("index.php?module=RingCentral&view=MassActionAjax&mode=showSendSMSForm")';
        Vtiger_Link::deleteLink($contact_tab_id, 'DETAILVIEWBASIC', 'Ring Central', $linkurl);
        
		$ringcentral_tab_id = Vtiger_Functions::getModuleId('RingCentral');
        $linkurl = 'layouts/v7/modules/RingCentral/resources/RingCentral.js';
        Vtiger_Link::deleteLink($ringcentral_tab_id, 'HEADERSCRIPT', 'RingCentralJS', $linkurl);
        
    }
    
    function addLinks($adb,$displayLabel) {
        
        
        $contact_module_model = Vtiger_Module::getInstance( 'Contacts' );
        $ringcentral_module_model = Vtiger_Module::getInstance( 'RingCentral' );
        
		$relList = $adb->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid =? AND related_tabid = ?",
        array($contact_module_model->getId(),$ringcentral_module_model->getId()));
        
		if(!$adb->num_rows($relList)){
            $relationLabel = 'RingCentral';
            $contact_module_model->setRelatedList( $ringcentral_module_model , $relationLabel, Array( ));
        }
        
        
        $tab_id = Vtiger_Functions::getModuleId('Contacts');
		$linkurl = 'javascript:RingCentral_Js.triggerRingCentral("index.php?module=RingCentral&view=MassActionAjax&mode=showSendSMSForm")';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ?",array($linkurl));
        if(!$adb->num_rows($result)){
            Vtiger_Link::addLink($tab_id, 'LISTVIEWMASSACTION', 'Send SMS through Ring Central', $linkurl, '', '0', '', '', '');
        }
        
		// Disable SMS Through Detail View for now
        /*$linkurl = 'javascript:RingCentral_Js.triggerRingCentralDetail("index.php?module=RingCentral&view=MassActionAjax&mode=showSendSMSForm")';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ?",array($linkurl));
        if($adb->num_rows($result) < 1){
            Vtiger_Link::addLink($tab_id, 'DETAILVIEWBASIC', 'Ring Central', $linkurl, '', '0', '', '', '');
        }*/
		
        
        $linkurl = 'javascript:RingCentral_Js.triggerRingCentral("index.php?module=RingCentral&view=MassActionAjax&mode=showSendFaxForm")';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ?",array($linkurl));
        if($adb->num_rows($result) < 1){
            Vtiger_Link::addLink($tab_id, 'LISTVIEWMASSACTION', 'Send Fax through RingCentral', $linkurl, '', '0', '', '', '');
        }
        
        $tab_id = Vtiger_Functions::getModuleId('RingCentral');
        $linkurl = 'layouts/v7/modules/RingCentral/resources/RingCentral.js';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ?",array($linkurl));
        if(!$adb->num_rows($result)){
            Vtiger_Link::addLink($tab_id, 'HEADERSCRIPT', 'RingCentralJS', $linkurl, '', '0', '', '', '');
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

	    /*$adb->pquery("CREATE TABLE IF NOT EXISTS vtiger_ringcentral_oauth_settings ( 
        user_id INT(19) NOT NULL , 
        clientid VARCHAR(250) NOT NULL ,
        clientsecret VARCHAR(500) NOT NULL ) ;");
	    */
	}
    
}
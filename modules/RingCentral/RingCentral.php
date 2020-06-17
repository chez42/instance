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
			
			self::installWorkflows($moduleName);
        
		} else if($eventType == 'module.disabled') {
            
			$adb->pquery("DELETE FROM vtiger_settings_field WHERE name=?", array($displayLabel));
            
			$this->removeLinks($adb);
			
			self::removeWorkflows($moduleName);
        
		} else if($eventType == 'module.enabled') {
            
			$this->addLinks($adb,$displayLabel);
            
			$this->ringCentralTables($adb);
			
			self::installWorkflows($moduleName);
        
		} else if($eventType == 'module.preuninstall') {
			
		    self::removeWorkflows($moduleName);
		    
		} else if($eventType == 'module.preupdate') {
        
		} else if($eventType == 'module.postupdate') {
		    self::installWorkflows($moduleName);
		}
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
        
        $account_module_model = Vtiger_Module::getInstance( 'Accounts' );
        
        $accrelList = $adb->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid =? AND related_tabid = ?",
            array($account_module_model->getId(),$ringcentral_module_model->getId()));
        
        if($adb->num_rows($accrelList)){
            $relationLabel = 'RingCentral';
            $account_module_model->unsetRelatedList( $ringcentral_module_model , $relationLabel);
        }
        
        $lead_module_model = Vtiger_Module::getInstance( 'Leads' );
        
        $learelList = $adb->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid =? AND related_tabid = ?",
            array($lead_module_model->getId(),$ringcentral_module_model->getId()));
        
        if($adb->num_rows($learelList)){
            $relationLabel = 'RingCentral';
            $lead_module_model->unsetRelatedList( $ringcentral_module_model , $relationLabel);
        }

	   
		$contact_tab_id = Vtiger_Functions::getModuleId('Contacts');
       
	    $linkurl = 'javascript:RingCentral_Js.triggerRingCentral("index.php?module=RingCentral&view=MassActionAjax&mode=showSendSMSForm")';
        Vtiger_Link::deleteLink($contact_tab_id, 'LISTVIEWMASSACTION', 'Send SMS through Ring Central', $linkurl);
        
		$linkurl = 'javascript:RingCentral_Js.triggerRingCentral("index.php?module=RingCentral&view=MassActionAjax&mode=showSendFaxForm")';
		Vtiger_Link::deleteLink($contact_tab_id, 'LISTVIEWMASSACTION', 'Send Fax through RingCentral', $linkurl);
        
        $linkurl = 'javascript:RingCentral_Js.triggerRingCentralDetail("index.php?module=RingCentral&view=MassActionAjax&mode=showSendSMSForm")';
        Vtiger_Link::deleteLink($contact_tab_id, 'DETAILVIEWBASIC', 'Ring Central', $linkurl);
        
        $acc_tab_id = Vtiger_Functions::getModuleId('Accounts');
        
        $acclinkurl = 'javascript:RingCentral_Js.triggerRingCentral("index.php?module=RingCentral&view=MassActionAjax&mode=showSendSMSForm")';
        Vtiger_Link::deleteLink($acc_tab_id, 'LISTVIEWMASSACTION', 'Send SMS through Ring Central', $acclinkurl);
        
        $acclinkurl = 'javascript:RingCentral_Js.triggerRingCentral("index.php?module=RingCentral&view=MassActionAjax&mode=showSendFaxForm")';
        Vtiger_Link::deleteLink($acc_tab_id, 'LISTVIEWMASSACTION', 'Send Fax through RingCentral', $acclinkurl);
        
        $acclinkurl = 'javascript:RingCentral_Js.triggerRingCentralDetail("index.php?module=RingCentral&view=MassActionAjax&mode=showSendSMSForm")';
        Vtiger_Link::deleteLink($acc_tab_id, 'DETAILVIEWBASIC', 'Ring Central', $acclinkurl);
        
        $lead_tab_id = Vtiger_Functions::getModuleId('Leads');
        
        $leadlinkurl = 'javascript:RingCentral_Js.triggerRingCentral("index.php?module=RingCentral&view=MassActionAjax&mode=showSendSMSForm")';
        Vtiger_Link::deleteLink($lead_tab_id, 'LISTVIEWMASSACTION', 'Send SMS through Ring Central', $leadlinkurl);
        
        $leadlinkurl = 'javascript:RingCentral_Js.triggerRingCentral("index.php?module=RingCentral&view=MassActionAjax&mode=showSendFaxForm")';
        Vtiger_Link::deleteLink($lead_tab_id, 'LISTVIEWMASSACTION', 'Send Fax through RingCentral', $leadlinkurl);
        
        $leadlinkurl = 'javascript:RingCentral_Js.triggerRingCentralDetail("index.php?module=RingCentral&view=MassActionAjax&mode=showSendSMSForm")';
        Vtiger_Link::deleteLink($lead_tab_id, 'DETAILVIEWBASIC', 'Ring Central', $leadlinkurl);
        
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
        
        $acc_module_model = Vtiger_Module::getInstance( 'Accounts' );
        $accList = $adb->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid =? AND related_tabid = ?",
            array($acc_module_model->getId(),$ringcentral_module_model->getId()));
        
        if(!$adb->num_rows($accList)){
            $relationLabel = 'RingCentral';
            $acc_module_model->setRelatedList( $ringcentral_module_model , $relationLabel, Array( ));
        }
        
        $lead_module_model = Vtiger_Module::getInstance( 'Leads' );
        $ledList = $adb->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid =? AND related_tabid = ?",
            array($lead_module_model->getId(),$ringcentral_module_model->getId()));
        
        if(!$adb->num_rows($ledList)){
            $relationLabel = 'RingCentral';
            $lead_module_model->setRelatedList( $ringcentral_module_model , $relationLabel, Array( ));
        }
        
        $tab_id = Vtiger_Functions::getModuleId('Contacts');
		$linkurl = 'javascript:RingCentral_Js.triggerRingCentral("index.php?module=RingCentral&view=MassActionAjax&mode=showSendSMSForm")';
		$result = $adb->pquery("select * from vtiger_links where linkurl = ? AND tabid = ?",array($linkurl, $tab_id));
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
        $result = $adb->pquery("select * from vtiger_links where linkurl = ? AND tabid = ? ",array($linkurl, $tab_id));
        if($adb->num_rows($result) < 1){
            Vtiger_Link::addLink($tab_id, 'LISTVIEWMASSACTION', 'Send Fax through RingCentral', $linkurl, '', '0', '', '', '');
        }
        
        $tab_id = Vtiger_Functions::getModuleId('Accounts');
        $linkurl = 'javascript:RingCentral_Js.triggerRingCentral("index.php?module=RingCentral&view=MassActionAjax&mode=showSendSMSForm")';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ? AND tabid = ?",array($linkurl, $tab_id));
        if(!$adb->num_rows($result)){
            Vtiger_Link::addLink($tab_id, 'LISTVIEWMASSACTION', 'Send SMS through Ring Central', $linkurl, '', '0', '', '', '');
        }
        
        $linkurl = 'javascript:RingCentral_Js.triggerRingCentral("index.php?module=RingCentral&view=MassActionAjax&mode=showSendFaxForm")';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ? AND tabid = ? ",array($linkurl,$tab_id));
        if($adb->num_rows($result) < 1){
            Vtiger_Link::addLink($tab_id, 'LISTVIEWMASSACTION', 'Send Fax through RingCentral', $linkurl, '', '0', '', '', '');
        }
        
        $tab_id = Vtiger_Functions::getModuleId('Leads');
        $linkurl = 'javascript:RingCentral_Js.triggerRingCentral("index.php?module=RingCentral&view=MassActionAjax&mode=showSendSMSForm")';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ? AND tabid = ?",array($linkurl, $tab_id));
        if(!$adb->num_rows($result)){
            Vtiger_Link::addLink($tab_id, 'LISTVIEWMASSACTION', 'Send SMS through Ring Central', $linkurl, '', '0', '', '', '');
        }
        
        $linkurl = 'javascript:RingCentral_Js.triggerRingCentral("index.php?module=RingCentral&view=MassActionAjax&mode=showSendFaxForm")';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ? AND tabid = ?",array($linkurl, $tab_id));
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
	
	public $workflows = array("RingcentralSmsTask" => "Send Sms with Ringcentral");
	
	public function installWorkflows($moduleName)
	{
	    require_once "modules/com_vtiger_workflow/include.inc";
	    global $adb;
	    global $vtiger_current_version;
	    if (version_compare($vtiger_current_version, "7.0.0", "<")) {
	        $template_folder = "layouts/vlayout";
	    } else {
	        $template_folder = "layouts/v7";
	    }
	    foreach ($this->workflows as $name => $label) {
	        
	        $dest1 = "modules/com_vtiger_workflow/tasks/" . $name . ".inc";
	        $source1 = "modules/" . $moduleName . "/workflow/" . $name . ".inc";
	        @shell_exec("rm -f modules/com_vtiger_workflow/tasks/" . $name . ".inc");
	        @shell_exec("rm -f " . $template_folder . "/modules/Settings/Workflows/Tasks/" . $name . ".tpl");
	        $file_exist1 = false;
	        $file_exist2 = false;
	        if (file_exists($dest1)) {
	            $file_exist1 = true;
	        } else {
	            if (copy($source1, $dest1)) {
	                $file_exist1 = true;
	            }
	        }
	        $dest2 = (string) $template_folder . "/modules/Settings/Workflows/Tasks/" . $name . ".tpl";
	        $source2 = (string) $template_folder . "/modules/" . $moduleName . "/task/" . $name . ".tpl";
	        $templatepath = "modules/" . $moduleName . "/task/" . $name . ".tpl";
	        if (file_exists($dest2)) {
	            $file_exist2 = true;
	        } else {
	            if (copy($source2, $dest2)) {
	                $file_exist2 = true;
	            }
	        }
	        if ($file_exist1 && $file_exist2) {
	            $sql1 = "SELECT * FROM com_vtiger_workflow_tasktypes WHERE tasktypename = ?";
	            $result1 = $adb->pquery($sql1, array($name));
	            if ($adb->num_rows($result1) == 0) {
	                $taskType = array("name" => $name, "label" => $label, "classname" => $name, "classpath" => $source1, "templatepath" => $templatepath, "modules" => array("include" => array(), "exclude" => array()), "sourcemodule" => $moduleName);
	                VTTaskType::registerTaskType($taskType);
	            }
	        }
	    }
	}
	
	private function removeWorkflows($moduleName)
	{
	   
	    global $adb;
	    global $vtiger_current_version;
	    if (version_compare($vtiger_current_version, "7.0.0", "<")) {
	        $template_folder = "layouts/vlayout";
	    } else {
	        $template_folder = "layouts/v7";
	    }
	    
	    $sql1 = "DELETE FROM com_vtiger_workflow_tasktypes WHERE sourcemodule = ?";
	    $adb->pquery($sql1, array($moduleName)); 
	    foreach ($this->workflows as $name => $label) {
	        $likeTasks = "%:\"" . $name . "\":%";
	        $sql2 = "DELETE FROM com_vtiger_workflowtasks WHERE task LIKE ?";
	        $adb->pquery($sql2, array($likeTasks));
	        @shell_exec("rm -f modules/com_vtiger_workflow/tasks/" . $name . ".inc");
	        @shell_exec("rm -f " . $template_folder . "/modules/Settings/Workflows/Tasks/" . $name . ".tpl");
	    }
	    
	}
    
}
<?php

class OmniMail extends CRMEntity{
    var $table_name = "vtiger_activity";
    var $table_index = 'activityid';
    var $rel_contacts_table = "vtiger_cntactivityrel";
    var $rel_serel_table = "vtiger_seactivityrel";
    var $tab_name = Array('vtiger_crmentity', 'vtiger_activity', 'vtiger_emaildetails');
    var $tab_name_index = Array('vtiger_crmentity' => 'crmid', 
                                'vtiger_activity' => 'activityid',
                                'vtiger_seactivityrel' => 'activityid', 
                                'vtiger_cntactivityrel' => 'activityid', 
                                'vtiger_email_track' => 'mailid', 
                                'vtiger_emaildetails' => 'emailid');
    var $list_fields = Array(
        'Subject' => Array('activity' => 'subject'),
        'Related to' => Array('seactivityrel' => 'parent_id'),
        'Date Sent' => Array('activity' => 'date_start'),
        'Assigned To' => Array('crmentity', 'smownerid')
    );
    var $list_fields_name = Array(
        'Subject' => 'subject',
        'Related to' => 'parent_id',
        'Assigned To' => 'assigned_user_id',
        'Date Sent' => 'date_start'
    );
    
    var $list_link_field = 'subject';
    var $column_fields = Array();
    var $sortby_fields = Array('subject', 'date_start');
    //Added these variables which are used as default order by and sortorder in ListView
    var $default_order_by = 'date_start';
    var $default_sort_order = 'ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('subject', 'assigned_user_id');
    
    /** This function will set the columnfields for Email module
     */
    function OmniMail() {
            $this->log = LoggerManager::getLogger('email');
            $this->log->debug("Entering Emails() method ...");
            $this->log = LoggerManager::getLogger('email');
            $this->db = PearDatabase::getInstance();
            $this->column_fields = getColumnFields('Emails');
            $this->log->debug("Exiting Email method ...");
    }
    
    /*
    * Function to get the relation tables for related modules
    * @param - $secmodule secondary module name
    * returns the array with table names and fieldnames storing relations between module and this module
    */
    function setRelationTables($secmodule) {
            $rel_tables = array (
                            "Leads" => array("vtiger_seactivityrel" => array("activityid", "crmid"), "vtiger_activity" => "activityid"),
                            "Vendors" => array("vtiger_seactivityrel" => array("activityid", "crmid"), "vtiger_activity" => "activityid"),
                            "Contacts" => array("vtiger_seactivityrel" => array("activityid", "crmid"), "vtiger_activity" => "activityid"),
                            "Accounts" => array("vtiger_seactivityrel" => array("activityid", "crmid"), "vtiger_activity" => "activityid"),
            );
            return $rel_tables[$secmodule];
    }    
    function vtlib_handler($moduleName, $eventType) {

    }
}

?>
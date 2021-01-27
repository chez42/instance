<?php
include_once 'modules/Vtiger/CRMEntity.php';

class Billing extends Vtiger_CRMEntity {
    
    var $table_name = 'vtiger_billing';
    var $table_index= 'billingid';
    
    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array('vtiger_billingcf', 'billingid');
    
    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    var $tab_name = Array('vtiger_crmentity', 'vtiger_billing', 'vtiger_billingcf');
    
    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_billing' => 'billingid',
        'vtiger_billingcf'=>'billingid');
    
    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array (
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Billing No' => Array('billing', 'billingno'),
        'Assigned To' => Array('crmentity','smownerid')
        );
    var $list_fields_name = Array (
        /* Format: Field Label => fieldname */
        'Billing No' => 'billingno',
        'Assigned To' => 'assigned_user_id',
        );
    
    // Make the field link to detail view
    var $list_link_field = 'billingno';
    
    // For Popup listview and UI type support
    var $search_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Billing No' => Array('billing', 'billingno'),
        'Assigned To' => Array('vtiger_crmentity','assigned_user_id'),
        );
    var $search_fields_name = Array (
        /* Format: Field Label => fieldname */
        'Billing No' => 'billingno',
        'Assigned To' => 'assigned_user_id',
        );
    
    // For Popup window record selection
    var $popup_fields = Array ('billingno');
    
    // For Alphabetical search
    var $def_basicsearch_col = 'billingno';
    
    // Column value to use on detail view record text display
    var $def_detailview_recname = 'billingno';
    
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('billingno','assigned_user_id');
    
    var $default_order_by = 'billingno';
    var $default_sort_order='ASC';
    
    function vtlib_handler($moduleName, $eventType) {
        
        $adb = PearDatabase::getInstance();
        
        if($eventType == 'module.postinstall') {
            
            $this->addLinks($adb,$displayLabel);
            $this->customTables($adb);
            
        } else if($eventType == 'module.disabled') {
            
            $tab_id = Vtiger_Functions::getModuleId('Billing');
            $linkurl = 'layouts/v7/modules/Billing/resources/Billing.js';
            Vtiger_Link::deleteLink($tab_id, 'HEADERSCRIPT', 'BillingJS', $linkurl);
            
            $tab_id = Vtiger_Functions::getModuleId('PortfolioInformation');
            $linkurl = 'javascript:Billing_Js.triggerBillingReportPdf("index.php?module=Billing&view=BillingReportPdf&mode=GenrateLink");';
            Vtiger_Link::deleteLink($tab_id, 'LISTVIEWMASSACTION', 'Get Statement', $linkurl);
            
        } else if($eventType == 'module.enabled') {
            
            $this->addLinks($adb,$displayLabel);
            $this->customTables($adb);
            
        } else if($eventType == 'module.preuninstall') {
            
        } else if($eventType == 'module.preupdate') {
            
        } else if($eventType == 'module.postupdate') {}
    }
    
    
    function addLinks($adb,$displayLabel) {
        
        $tab_id = Vtiger_Functions::getModuleId('Billing');
        $linkurl = 'layouts/v7/modules/Billing/resources/Billing.js';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ?",array($linkurl));
        if(!$adb->num_rows($result)){
            Vtiger_Link::addLink($tab_id, 'HEADERSCRIPT', 'BillingJS', $linkurl, '', '0', '', '', '');
        }
        
        $tab_id = Vtiger_Functions::getModuleId('PortfolioInformation');
        $linkurl = 'javascript:Billing_Js.triggerBillingReportPdf("index.php?module=Billing&view=BillingReportPdf&mode=GenrateLink");';
        
        $result = $adb->pquery("select * from vtiger_links where linkurl = ?
        AND tabid = ?",array($linkurl, $tab_id));
        
        if(!$adb->num_rows($result)){
            Vtiger_Link::addLink($tab_id, 'LISTVIEWMASSACTION', 'Get Statement', $linkurl, '', '0', '', '', '');
        }
        
    }
    
    function customTables($adb){
        
        $adb->pquery("CREATE TABLE IF NOT EXISTS vtiger_billing_capitalflows ( 
            capitalflowsid INT(19) NOT NULL AUTO_INCREMENT , 
            billingid INT(19) NULL , 
            trade_date VARCHAR(255) NULL , 
            diff_days VARCHAR(255) NULL , 
            totalamount VARCHAR(255) NULL , 
            totaldays VARCHAR(255) NULL , 
            transactionamount VARCHAR(255) NULL , 
            transactiontype VARCHAR(255) NULL , 
            trans_fee VARCHAR(255) NULL , 
            totaladjustment VARCHAR(255) NULL , 
            PRIMARY KEY (capitalflowsid)
        );");
        
    }
    
    
}
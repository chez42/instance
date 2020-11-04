<?php
include_once 'modules/Vtiger/CRMEntity.php';

class Billing extends Vtiger_CRMEntity {
    
    function vtlib_handler($moduleName, $eventType) {
        
        $adb = PearDatabase::getInstance();
        
        if($eventType == 'module.postinstall') {
            
            $this->addLinks($adb,$displayLabel);
            
        } else if($eventType == 'module.disabled') {
            
            $tab_id = Vtiger_Functions::getModuleId('Billing');
            $linkurl = 'layouts/v7/modules/Billing/resources/Billing.js';
            Vtiger_Link::deleteLink($tab_id, 'HEADERSCRIPT', 'BillingJS', $linkurl);
            
            $tab_id = Vtiger_Functions::getModuleId('PortfolioInformation');
            $linkurl = 'javascript:Billing_Js.triggerBillingReportPdf("index.php?module=Billing&view=BillingReportPdf&mode=GenrateLink");';
            Vtiger_Link::deleteLink($tab_id, 'LISTVIEWMASSACTION', 'Get Statement', $linkurl);
            
        } else if($eventType == 'module.enabled') {
            
            $this->addLinks($adb,$displayLabel);
            
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
    
    
}
<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Group_Record_Model extends Vtiger_Record_Model {

    function getRelatedItemsDetails(){
        
        $adb = PearDatabase::getInstance();
        
        $result = $adb->pquery("SELECT vtiger_group_items.itemid, vtiger_group_items.portfolioid, vtiger_group_items.billingspecificationid, 
        vtiger_group_items.active, vtiger_portfolioinformation.account_number as portfolioname, vtiger_billingspecifications.name as billingspecificationname  
        FROM vtiger_group_items 
        LEFT JOIN vtiger_portfolioinformation ON vtiger_portfolioinformation.portfolioinformationid = vtiger_group_items.portfolioid
        LEFT JOIN vtiger_billingspecifications ON vtiger_billingspecifications.billingspecificationsid = vtiger_group_items.billingspecificationid
        WHERE vtiger_group_items.groupid = ?", array($this->getId()));
        
        $itemDetails = array();
        
        if($adb->num_rows($result)){
            
            while($item = $adb->fetchByAssoc($result)){
                $itemDetails[] = $item;
            }
        }
        
        return $itemDetails;
    }

}

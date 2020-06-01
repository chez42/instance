<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class BillingSpecifications_Record_Model extends Vtiger_Record_Model {

    function getRelatedScheduleDetails(){
        
        $adb = PearDatabase::getInstance();
        
        $result = $adb->pquery("SELECT `rangeid`, `from`, `to`, `type`, `value` FROM vtiger_billing_range 
        WHERE billingid = ?", array($this->getId()));
        
        $scheduletDetails = array();
        
        if($adb->num_rows($result)){
            
            while($schedule = $adb->fetchByAssoc($result)){
                $scheduletDetails[] = $schedule;
            }
        }
        
        return $scheduletDetails;
    }

}

<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Billing_Record_Model extends Vtiger_Record_Model {

    function getRelatedCaptialFlowsDetails(){
        
        $adb = PearDatabase::getInstance();
        
        $result = $adb->pquery("SELECT capitalflowsid, trade_date, diff_days, totalamount, totaldays, transactionamount, 
        transactiontype, trans_fee, totaladjustment FROM vtiger_billing_capitalflows WHERE billingid = ?", 
            array($this->getId()));
        
        $scheduletDetails = array();
        
        if($adb->num_rows($result)){
            
            while($schedule = $adb->fetchByAssoc($result)){
                $scheduletDetails[] = $schedule;
            }
        }
        
        return $scheduletDetails;
    }
    
    public function isEditable() {
        return false;
    }
    
    public function getRelatdPortfolios(){
        $adb = PearDatabase::getInstance();
        
        $result = $adb->pquery("SELECT billing_portfolio_id, portfolioid, portfolio_amount, bill_amount FROM vtiger_billing_portfolio_accounts WHERE billingid = ?",
            array($this->getId()));
        
        $portfolioDetails = array();
        
        if($adb->num_rows($result)){
            
            while($schedule = $adb->fetchByAssoc($result)){
                $portfolioDetails[] = $schedule;
            }
        }
        
        return $portfolioDetails;
        
    }

}

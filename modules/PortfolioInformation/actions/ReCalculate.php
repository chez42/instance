<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class PortfolioInformation_ReCalculate_Action extends Vtiger_Mass_Action {
    
    public function checkPermission(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        
        $currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        if(!$currentUserPriviligesModel->hasModuleActionPermission($moduleModel->getId(), 'EditView')) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }
    }
    
    public function process(Vtiger_Request $request) {
        
        $moduleName = $request->getModule();
        
        $RecordIds = $this->getRecordsListFromRequest($request);
        
        global $adb;
     
        $result = $adb->pquery("SELECT account_number,portfolioinformationid from vtiger_portfolioinformation 
		where vtiger_portfolioinformation.portfolioinformationid IN (".implode(',',$RecordIds).")");
		
		for($i = 0; $i < $adb->num_rows($result); $i++){
			 
			
			$account_number = $adb->query_result($result, $i, "account_number");
			
			$portfolio = PortfolioInformation_Record_Model::getInstanceById($adb->query_result($result, $i, "portfolioinformationid"));
			
			if(
				$portfolio->get("origination") != 'MANUAL' && 
				$portfolio->get("origination") != 'Millenium' && 
				$portfolio->get("origination") != 'EQUITY'
			){
				$account_number = array($account_number);
				
				$integrity = new cIntegrity($account_number);
				$differences = $integrity->GetDifferences();
		
				foreach($differences AS $k => $v) {
					if (!empty($differences) && abs($v['dif']) > 10)
						$integrity->RepairDifferences();
				}
				$tmp = new CustodianClassMapping($account_number);
				$tmp->portfolios::UpdateAllPortfoliosForAccounts($account_number);
				$tmp->positions::CreateNewPositionsForAccounts($account_number);
				$tmp->positions::UpdateAllCRMPositionsAtOnceForAccounts($account_number);
				$tmp->transactions::CreateNewTransactionsForAccounts($account_number);
				$tmp->transactions::UpdateTransactionsForAccounts($account_number);

				$weight = new cWeight($portfolio->get('account_number'));
				$weight->UpdatePortfolioWeight();
				$weight->UpdateContactWeightAndValue();
				$weight->UpdateHouseholdWeightAndValue();
				
			}
			
		}
		
	
	}
    
    public function validateRequest(Vtiger_Request $request) {
        return true;
    }
}
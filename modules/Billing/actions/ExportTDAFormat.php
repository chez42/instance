<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Billing_ExportTDAFormat_Action extends Vtiger_Mass_Action {
    
    public function checkPermission(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        
        $currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        if(!$currentUserPriviligesModel->hasModuleActionPermission($moduleModel->getId(), 'Export')) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }
    }
    
    public function process(Vtiger_Request $request) {
        
        $moduleName = $request->getModule();
        
        $RecordIds = $this->getRecordsListFromRequest($request);
        
        global $adb;
     
        $result = $adb->pquery("SELECT billing_frequency, account_number, portfolio_amount FROM vtiger_billing
		
		INNER JOIN vtiger_billingspecifications on vtiger_billingspecifications.billingspecificationsid = vtiger_billing.billingspecificationid
		
		INNER JOIN vtiger_portfolioinformation on vtiger_portfolioinformation.portfolioinformationid = vtiger_billing.portfolioid
		
		
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_billing.billingid
		
        WHERE vtiger_crmentity.deleted = 0 AND vtiger_billing.billingid IN (".implode(',',$RecordIds).")");
      
		while(ob_get_level()) {
            ob_end_clean();
        }
		
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=' . $request->get('filename'). '.csv');

		$output = fopen('php://output', 'w');
		
        if($adb->num_rows($result)){

            for($i = 0; $i < $adb->num_rows($result); $i++){
				
				if($adb->query_result($result, $i, 'billing_frequency') == 'Quaterly'){
					$billing_frequency = 'Q';
				} else {
					$billing_frequency = '';
				}
				
                $data = array(
					$adb->query_result($result, $i, 'account_number'),
					$billing_frequency,
					CurrencyField::convertToUserFormat($adb->query_result($result, $i, 'portfolio_amount'))
				);
				
				fputcsv($output, $data);
			
			}
        }
	
	}
    
    public function validateRequest(Vtiger_Request $request) {
        return true;
    }
}
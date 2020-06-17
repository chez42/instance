<?php

class ModSecuritiesHandler extends VTEventHandler{
    function GetExchangeResponseMessage($response){
        
    }
    
    function handleEvent($eventName, $entityData) {
    	if ($eventName == 'vtiger.entity.beforesave'){
    		if($entityData->get('factor') == 0)
    			$entityData->set('factor', 1);
			if($entityData->get('dividend_pay_date') == '0000-00-00')
				$entityData->set('dividend_pay_date', '1900-01-01');
			if($entityData->get('ex_dividend_date') == '0000-00-00')
				$entityData->set('ex_dividend_date', '1900-01-01');
		}
		if ($eventName == 'vtiger.entity.aftersave') {
###			PositionInformation_Module_Model::UpdatePositionInformationPrice($entityData->get('security_symbol'), $entityData->get('security_price'));
			PositionInformation_Module_Model::UpdateIndividualPositionBasedOnModSecurities($entityData->get('security_symbol'));
###			$accounts = PositionInformation_Module_Model::GetAccountNumbersThatHaveSymbol($entityData->get('security_symbol'));
###			if(sizeof($accounts) >= 1)
###				PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesFromPositions(null, $accounts);
		}
/*
    	echo 'here'; exit;return;
        $recordId = $entityData->getId();
        $security_id = $entityData->get('security_id');
        $price = $entityData->get('security_price');
        $update_pc = $entityData->get('update_pc');
        $price_id = ModSecurities_SecurityBridge_Model::GetLatestPriceIDForSecurity($security_id);
        ModSecurities_SecurityBridge_Model::UpdatePricingTablePrice($price_id, $price, $update_pc);
        $asset_code_id = ModSecurities_SecurityBridge_Model::GetCodeIDByAssetClassName($entityData->get('asset_class'));
        ModSecurities_SecurityBridge_Model::UpdateSecurityCodeID($security_id, 20, $asset_code_id, $update_pc);//Update the asset class
        $sector_code_id = ModSecurities_SecurityBridge_Model::GetCodeIDBySectorClassName($entityData->get('sector'));
        ModSecurities_SecurityBridge_Model::UpdateSecurityCodeID($security_id, 10, $sector_code_id, $update_pc);//Update the asset class
        $frequency_id = ModSecurities_SecurityBridge_Model::GetPayFrequencyIDByFrequencyName($entityData->get('pay_frequency'));
        ModSecurities_SecurityBridge_Model::UpdatePayFrequencyCodeID($security_id, $frequency_id, $update_pc);//Update the asset class
        $security_type_id = ModSecurities_SecurityBridge_Model::GetSecurityTypeIDByTypeName($entityData->get('security_type'));
        ModSecurities_SecurityBridge_Model::UpdateSecurityTypeID($security_id, $security_type_id, $update_pc);//Update the asset class
        $this->UpdatePositionInformation($entityData);
*/
    }
    
    function UpdatePositionInformation($entityData){
        global $adb;
        $query = "UPDATE vtiger_positioninformation p
                  JOIN vtiger_positioninformationcf cf ON p.positioninformationid = cf.positioninformationid
                  SET cf.asset_class = ?, cf.security_type = ?
                  WHERE p.security_symbol = ?";
        $adb->pquery($query, array($entityData->get('asset_class'),
                                   $entityData->get('security_type'),
                                   $entityData->get('security_symbol')));
    }
}

?>
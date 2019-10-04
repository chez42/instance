<?php

class ModSecuritiesHandler extends VTEventHandler{
    function GetExchangeResponseMessage($response){
        
    }
    
    function handleEvent($eventName, $entityData) {return;
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
    }
}

?>
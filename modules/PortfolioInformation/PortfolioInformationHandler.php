<?php
/**
 * Created by PhpStorm.
 * User: rsandnes
 * Date: 2016-10-06
 * Time: 1:23 PM
 */

class PortfolioInformationHandler extends VTEventHandler{
    function handleEvent($eventName, $entityData) {
        global $adb;
        $recordId = $entityData->getId();
        $moduleName = $entityData->getModuleName();

        switch($eventName){
            case 'vtiger.entity.beforesave.modifiable':{
                $data = $entityData->getData();
                $symbol = $data['security_symbol'];
                $account = $data['account_number'];

#            $record = PositionInformation_Module_Model::GetPositionEntityIDForAccountNumberAndSymbol($account, $symbol);

                if($entityData->isNew()){//If the record exists
                    if(strlen($data['contact_link']) > 0){
                        $contact_record = Contacts_Record_Model::getInstanceById($data['contact_link']);
                        $contact_data = $contact_record->getData();

                        $entityData->set('tax_id', $contact_data['ssn']);
                        $entityData->set('email_address', $contact_data['email']);
                        $entityData->set('city', $contact_data['mailingcity']);
                        $entityData->set('state', $contact_data['mailingstate']);
                        $entityData->set('zip', $contact_data['mailingzip']);
                        $entityData->set('address1', $contact_data['mailingstreet']);
                    }
                }
            }
            break;
            case 'vtiger.entity.aftersave':
            {
                switch(strtolower($moduleName)){
                    case "positioninformation":
                        $data = $entityData->getData();
                        $isclosed = $data['accountclosed'];
                        $id = ModSecurities_Module_Model::GetModSecuritiesIdBySymbol($data['security_symbol']);
                        if ($id == 0) {//If the ID doesn't exist and a Position has been created, we need a security created to go with it
                            $record = ModSecurities_Record_Model::getCleanInstance("ModSecurities");

                            $record->set('security_symbol', $data['security_symbol']);
                            $record->set('security_name', $data['description']);
                            $record->set('security_price',  $data['last_price']);
                            $record->set('cusip',  $data['cusip']);
                            $record->set('security_price_adjustment', $data['multiplier']);
                            $record->set('aclass', $data['base_asset_class']);
                            $record->set('securitytype', $data['security_type']);
                            $record->set('label', $data['description']);
                            $record->set('mode', 'create');
                            $record->save();
                        }
                        if($isclosed){
                            PositionInformation_Module_Model::ClosePositions($data['account_number']);
                        }
                        break;
                    case "portfolioinformation":
                        $portfolio_record = PortfolioInformation_Record_Model::getInstanceById($recordId);
                        $account_number = $portfolio_record->get("account_number");
                        $repcode = $portfolio_record->get("production_number");
                        $custodian = $portfolio_record->get("origination");

                        if(strlen($repcode) > 0) {
                            require_once("libraries/custodians/cCustodianUpdater.php");
                            $update = new cCustodianUpdater("custodian_omniscient");
                            $update->UpdateTable("custodian_portfolios_{$custodian}", array("rep_code"), array($repcode, $account_number), "account_number = ?");
                        }
                        break;
                }
            }
        }//AFTER SAVE:  Set all positions for given account to 'closed' if accountclosed flag is 1
    }
}
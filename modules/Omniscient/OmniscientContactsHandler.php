<?php

class OmniscientContactsHandler extends VTEventHandler{
    
	function handleEvent($eventName, $entityData) {
        
		$moduleName = $entityData->getModuleName();
	
		if($eventName == 'vtiger.entity.aftersave' && $moduleName == 'Contacts'){
            $adb = PearDatabase::getInstance();

            include_once("libraries/Stratifi/StratContacts.php");
            $strat = new StratContacts();
            $id = $entityData->get('id');
            $result = $strat->SaveStratifiContact($id);
/*
#            $t = Contacts_Record_Model::getInstanceById(33);
            $data = $entityData->getData();

            $contact_id = $entityData->getId();
			$household_id = $entityData->get('account_id');

			PortfolioInformation_Module_Model::UpdateHouseholdLinkForContact($contact_id, $household_id);

            /*
			$adb = PearDatabase::getInstance();

			$recordId = $entityData->getId();
		
			$data = $entityData->getData();
            
			if($data['sync_outlook'] != 'on' && $data['sync_outlook'] != 1){
                return;
            }
			            
            if(isset($_REQUEST['ignore_exchange_update']) && $_REQUEST['ignore_exchange_update'] == 1)
                return;

			$user_name = getUserName($data['assigned_user_id']);
            
            $contact = new OmniCal_ExchangeContacts_Model('lanserver33', 'ConcertAdmin', 'Consec1', 'Exchange2007_SP1' );
            
			$contact->SetImpersonation($user_name);

			$is_impersonated = false;
				
			try {
				
				$ews_folder = $contact->getExchangeFolderDetail();
				
				if(!empty($ews_folder))
					$is_impersonated = true;
				
			} catch (Exception $e) {
				$is_impersonated = false;
			} 
			
			if(!$is_impersonated)
				return true;
				
			if(isset($data['contact_exchange_item_id']) && $data['contact_exchange_item_id'] != '')
				$exchange_info = array('id' => $data['contact_exchange_item_id'], 'changekey' => $data['contact_exchange_change_key']);
			else 
				$exchange_info = OmniCal_CRMExchangeHandler_Model::GetContactIdAndChangeKey($recordId);
            
			if(isset($data['birthday']) && $data['birthday'] != '')
				$data['birthday'] = getValidDBInsertDateValue($data['birthday']);
				
            if(!empty($exchange_info)){
                
				$phone_info = $contact->GetPhoneInfoFromData($data);
                
				$addresses = $contact->GetAddressInfoFromData($data);
                
				$emails = $contact->GetEmailInfoFromData($data);
            
				$response = $contact->UpdateContactInExchange($exchange_info['id'], $exchange_info['changekey'], $data['firstname'], $data['lastname'], $data['title'], 
                                                              $data['assistant'], $data['nickname'], $data['birthday'], $emails, $phone_info, $addresses);
                if($response->ResponseMessages->UpdateItemResponseMessage->ResponseClass == 'Success'){
                    $id = $response->ResponseMessages->UpdateItemResponseMessage->Items->Contact->ItemId->Id;
                    $changeKey = $response->ResponseMessages->UpdateItemResponseMessage->Items->Contact->ItemId->ChangeKey;
                    $contact->UpdateCRMExchangeIDAndChangeKey($recordId, $id, $changeKey);
                }
				
            } else {
				
				$phone_info = $contact->GetPhoneInfoFromData($data);
                
				$addresses = $contact->GetAddressInfoFromData($data);
                
				$emails = $contact->GetEmailInfoFromData($data);
                
                $response = $contact->CreateContactInExchange($data['firstname'], $data['lastname'], $data['title'], $data['assistant'], $data['nickname'], $data['birthday'],
                                                              $emails, $phone_info, $addresses);
                
                if($response->ResponseMessages->CreateItemResponseMessage->ResponseClass == 'Success'){
                    $id = $response->ResponseMessages->CreateItemResponseMessage->Items->Contact->ItemId->Id;
                    $changeKey = $response->ResponseMessages->CreateItemResponseMessage->Items->Contact->ItemId->ChangeKey;
                    $contact->UpdateCRMExchangeIDAndChangeKey($recordId, $id, $changeKey);
                }
			}*/
        }
    }
}

?>

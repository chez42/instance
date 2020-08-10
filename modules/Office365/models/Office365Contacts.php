<?php
use Microsoft\Graph\Model;
class Office365_Office365Contacts_Model extends Office365_Office365_Model{
    
    function getContacts($lastUpdatedTime = false, $offset = 0){
        
        $graph = $this->ews;
        
        $contacts = array();
        
        try{
            
            //?$filter=lastModifiedDateTime ge '.$lastUpdatedTime
            $all_contacts = $graph->createCollectionRequest("GET", '/me/contacts')
            ->setReturnType(Model\Contact::class);
            
            while (!$all_contacts->isEnd()){
                foreach($all_contacts->getPage() as $calEvent){
                    $contacts[] = $calEvent;
                }
            }
            
            return $contacts;
            
        } catch (Exception $e){
            
            $error = $e->getMessage();
            return false;
            
        }
        
    }
    
    function createContact($contactData, $options = array()){

        $graph = $this->ews;
        
        $contact =  new Model\Contact();
        
        
        
        echo"<pre>";print_r($contactData);echo"</pre>";exit;
        $request = $this->formatContactRequestedData($contact);
       
        $request = array('Contact' => $request);
        
        $defaultOptions = array(
            'MessageDisposition' => 'SaveOnly',
            'SavedItemFolderId' => array('FolderId' => $folder->toArray())
        );
        
        $options = array_replace_recursive($defaultOptions, $options);
        
        try{
            
            $items = $this->ews->createItems($request, $options);
            
        } catch (Exception $e){
            $error = $e->getMessage();
        }
        
        return $items;
    }
    
    function formatContactRequestedData($contact){
        
        if(isset($contact['EmailAddresses']) && !empty($contact['EmailAddresses'])){
            
            $emails = $contact['EmailAddresses'];
            
            $contact['EmailAddresses'] = array();
            
            if(count($emails) > 1){
                $contact['EmailAddresses']['Entry'] = array(
                    array('Key' => Enumeration\EmailAddressKeyType::EMAIL_ADDRESS_1, '_value' => $emails[0]),
                    array('Key' => Enumeration\EmailAddressKeyType::EMAIL_ADDRESS_2, '_value' => $emails[1])
                );
            } else {
                $contact['EmailAddresses'] = array(
                    'Entry' => array('Key' => Enumeration\EmailAddressKeyType::EMAIL_ADDRESS_1, '_value' => $emails[0])
                );
            }
        }
        
        if(isset($contact['PhoneNumbers']) && !empty($contact['PhoneNumbers'])){
            
            $phones = $contact['PhoneNumbers'];
            
            $contact['PhoneNumbers'] = array();
            
            foreach($phones as $type => $phone){
                
                if(!empty($phone)){
                    if($type == 'mobile'){
                        $contact['PhoneNumbers']['Entry'][] = array(
                            'Key' => Enumeration\PhoneNumberKeyType::MOBILE_PHONE, 
                            '_value' => $phone
                        );
                    } else if($type == 'home'){
                        $contact['PhoneNumbers']['Entry'][] = array(
                            'Key' => Enumeration\PhoneNumberKeyType::HOME_PHONE,
                            '_value' => $phone
                        );
                    } else {
                        $contact['PhoneNumbers']['Entry'][] = array(
                            'Key' => Enumeration\PhoneNumberKeyType::BUSINESS_PHONE,
                            '_value' => $phone
                        );
                    }
                }
            }
            
            if(empty($contact['PhoneNumbers']))
                unset($contact['PhoneNumbers']);
        }
        
        if(isset($contact['PhysicalAddresses']) && !empty($contact['PhysicalAddresses'])){
            
            $addresses = $contact['PhysicalAddresses'];
            
            $contact['PhysicalAddresses'] = array();
            
            foreach($addresses as $type => $address){
                
                if(!empty(array_filter($address))){
                    
                    if($type == 'home')
                        $key = Enumeration\PhysicalAddressKeyType::HOME;
                    else if($type == 'other')
                        $key = Enumeration\PhysicalAddressKeyType::OTHER;
                    else 
                        $key = Enumeration\PhysicalAddressKeyType::BUSINESS;
                    
                    $contact['PhysicalAddresses']['Entry'][] = array(
                        'Key' => $key,
                        'street' => $address['street'],
                        'city' => $address['city'],
                        'state' => $address['state'],
                        'countryOrRegion' => $address['countryOrRegion'],
                        'postalCode' => $address['postalCode'],
                    );
                }
            }
            
            if(empty($contact['PhysicalAddresses']))
                unset($contact['PhysicalAddresses']);
        }
        return $contact;
    }
    
    function getContactById($itemId){
        
        $options = array(
            'ItemShape' => array(
                'BaseShape' => 'AllProperties',
                'BodyType' => 'Text'
            )
        );
        
        try{
            return $this->ews->getItem(new Type\ItemIdType($itemId), $options);
        } catch (Exception $e){
            $error = $e->getMessage();
            return false;
        }
    }
    
    function updateContact($itemId, $changes){
        
        $changes = $this->formatContactData($changes);
        
        $request = array(
            'ItemChange' => array(
                'ItemId' => $itemId->toArray(),
                'Updates' => API\ItemUpdateBuilder::buildUpdateItemChanges('Contact', 'contacts', $changes)
            )
        );
        
        $response = $this->ews->updateItems($request);
        
        return $response->getContact();
    }
    
    function formatContactData($contactData){
     
        if(isset($contactData['EmailAddresses']) && !empty($contactData['EmailAddresses'])){
            
            $emails = $contactData['EmailAddresses'];
            
            unset($contactData['EmailAddresses']);
            
            $index = 1;
            
            foreach($emails as $email){
                
                if(!empty($email)){
                    $contactData['EmailAddress:EmailAddress'.$index] = array(
                        'EmailAddresses' => array(
                            'Entry' => array('Key' => 'EmailAddress'.$index, '_value' => $email)
                        )
                    );
                    $index++;
                }
            }
        }
        
        if(isset($contactData['PhoneNumbers'])){
            
            $phones = $contactData['PhoneNumbers'];
            
            unset($contactData['PhoneNumbers']);
            
            $homePhone = $phones['home'];
            $mobilePhone = $phones['mobile'];
            $businessPhone = $phones['business'];
            
            if(!empty($homePhone)){
                $contactData['PhoneNumber:HomePhone'] = array(
                    'PhoneNumbers' => array(
                        'Entry' => array('Key' => 'HomePhone', '_value' => $homePhone)
                    )
                );
            }
            if(!empty($mobilePhone)){
                $contactData['PhoneNumber:MobilePhone'] = array(
                    'PhoneNumbers' => array(
                        'Entry' => array('Key' => 'MobilePhone', '_value' => $mobilePhone)
                    )
                );
            }
            if(!empty($businessPhone)){
                $contactData['PhoneNumber:BusinessPhone'] = array(
                    'PhoneNumbers' => array(
                        'Entry' => array('Key' => 'BusinessPhone', '_value' => $businessPhone)
                    )
                );
            }
        }
        
        if(isset($contactData['PhysicalAddresses']) && !empty($contactData['PhysicalAddresses'])){
           
            $addresses = $contactData['PhysicalAddresses'];
            
            unset($contactData['PhysicalAddresses']);
            
            if(isset($addresses['business'])){
                
                $businessAddress = $addresses['business'];
                
                $exchangeBAdd = array('Key' => 'Business');
                
                if(!empty($businessAddress['street'])) $exchangeBAdd['street'] = $businessAddress['street'];
                if(!empty($businessAddress['city'])) $exchangeBAdd['city'] = $businessAddress['city'];
                if(!empty($businessAddress['state'])) $exchangeBAdd['state'] = $businessAddress['state'];
                if(!empty($businessAddress['countryOrRegion'])) $exchangeBAdd['countryOrRegion'] = $businessAddress['countryOrRegion'];
                if(!empty($businessAddress['postalCode'])) $exchangeBAdd['postalCode'] = $businessAddress['postalCode'];
                
                $contactData['PhysicalAddress:Business'] = array(
                    'PhysicalAddresses' => array(
                        'Entry' => $exchangeBAdd
                    )
                );
            }
                
            if(isset($addresses['home'])){
                
                $homeAddress = $addresses['home'];
                
                $exchangeHAdd = array('Key' => 'Home');
                
                if(!empty($homeAddress['street'])) $exchangeHAdd['street'] = $homeAddress['street'];
                if(!empty($homeAddress['city'])) $exchangeHAdd['city'] = $homeAddress['city'];
                if(!empty($homeAddress['state'])) $exchangeHAdd['state'] = $homeAddress['state'];
                if(!empty($homeAddress['countryOrRegion'])) $exchangeHAdd['countryOrRegion'] = $homeAddress['countryOrRegion'];
                if(!empty($homeAddress['postalCode'])) $exchangeHAdd['postalCode'] = $homeAddress['postalCode'];
                
                $contactData['PhysicalAddress:Home'] = array(
                    'PhysicalAddresses' => array(
                        'Entry' => $exchangeHAdd
                    )
                );
            }
            
            if(isset($addresses['other'])){
                
                $otherAddress = $addresses['other'];
                
                $exchangeOAdd = array('Key' => 'Other');
                
                if(!empty($otherAddress['street'])) $exchangeOAdd['street'] = $otherAddress['street'];
                if(!empty($otherAddress['city'])) $exchangeOAdd['city'] = $otherAddress['city'];
                if(!empty($otherAddress['state'])) $exchangeOAdd['state'] = $otherAddress['state'];
                if(!empty($otherAddress['countryOrRegion'])) $exchangeOAdd['countryOrRegion'] = $otherAddress['countryOrRegion'];
                if(!empty($otherAddress['postalCode'])) $exchangeOAdd['postalCode'] = $otherAddress['postalCode'];
                
                $contactData['PhysicalAddress:Other'] = array(
                    'PhysicalAddresses' => array(
                        'Entry' => $exchangeOAdd
                    )
                );
            }
        }
        
        return $contactData;
    }
    
    function updateContactItems($updateContacts, $options = array()){
        
        foreach($updateContacts as $itemId => $changes){
            
            $itemId = $changes['itemId'];
            
            unset($changes['id']);
            unset($changes['itemId']);
            
            $changes = $this->formatContactData($changes);
            
            $request['ItemChange'][] = [
                'ItemId' => $itemId->toArray(),
                'Updates' => API\ItemUpdateBuilder::buildUpdateItemChanges('Contact', 'contacts', $changes)
            ];
        }
        
        try{
            $items = $this->updateItems($request, $options);
            $items = $this->ensureIsArray($items);
        } catch (Exception $e){
            $error = $e->getMessage();
        }
        
        return $items;
    }
}
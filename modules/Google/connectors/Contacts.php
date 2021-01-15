<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

vimport('~~/modules/WSAPP/synclib/connectors/TargetConnector.php');
require_once 'vtlib/Vtiger/Net/Client.php';

Class Google_Contacts_Connector extends WSAPP_TargetConnector {
    
    protected $apiConnection;
    protected $totalRecords;
    protected $createdRecords;
    protected $maxResults = 2000;
    
    const CONTACTS_URI = 'https://people.googleapis.com/v1/people/me/connections';
    
    const CONTACTS_GROUP_URI = 'https://people.googleapis.com/v1/contactGroups/all';
    
    const CONTACTS_BATCH_URI = 'https://people.googleapis.com/v1/people:batchGet';
    
    const USER_PROFILE_INFO = 'https://www.googleapis.com/oauth2/v1/userinfo';
    
    const CREATE_CONTACT_URI = 'https://people.googleapis.com/v1/people:createContact';
    
    const GET_CONTACT_URI = "https://people.googleapis.com/v1/";
    
    protected $apiVersion = '3.0';
    
    private $groups = null;
    
    private $selectedGroup = null;
    
    private $fieldMapping = null;
    
    private $maxBatchSize = 100;
    
    protected $fields = array(
        'salutationtype' => array(
            'name' => 'gd:namePrefix'
        ),
        'firstname' => array(
            'name' => 'gd:givenName'
        ),
        'lastname' => array(
            'name' => 'gd:familyName'
        ),
        'title' => array(
            'name' => 'gd:orgTitle'
        ),
        'organizationname' => array(
            'name' => 'gd:orgName'
        ),
        'birthday' => array(
            'name' => 'gContact:birthday'
        ),
        'email' => array(
            'name' => 'gd:email',
            'types' => array('home','work','custom')
        ),
        'phone' => array(
            'name' => 'gd:phoneNumber',
            'types' => array('mobile','home','work','main','work_fax','home_fax','pager','custom')
        ),
        'address' => array(
            'name' => 'gd:structuredPostalAddress',
            'types' => array('home','work','custom')
        ),
        'date' => array(
            'name' => 'gContact:event',
            'types' => array('anniversary','custom')
        ),
        'description' => array(
            'name' => 'content'
        ),
        'custom' => array(
            'name' => 'gContact:userDefinedField'
        ),
        'url' => array(
            'name' => 'gContact:website',
            'types' => array('profile','blog','home-page','work','custom')
        )
    );
    
    public function __construct($oauth2Connection) {
        $this->apiConnection = $oauth2Connection;
    }
    
    /**
     * Get the name of the Google Connector
     * @return string
     */
    public function getName() {
        return 'GoogleContacts';
    }
    
    /**
     * Function to get Fields
     * @return <Array>
     */
    public function getFields() {
        return $this->fields;
    }
    
    /**
     * Function to get the mapped value
     * @param <Array> $valueSet
     * @param <Array> $mapping
     * @return <Mixed>
     */
    public function getMappedValue($valueSet,$mapping) {
        $key = $mapping['google_field_type'];
        if($key == 'custom')
            $key = $mapping['google_custom_label'];
            return $valueSet[decode_html($key)];
    }
    
    /**
     * Function to get field value of google field
     * @param <Array> $googleFieldDetails
     * @param <Google_Contacts_Model> $user
     * @return <Mixed>
     */
    public function getGoogleFieldValue($googleFieldDetails, $googleRecord, $user) {
        $googleFieldValue = '';
        switch ($googleFieldDetails['google_field_name']) {
            case 'gd:namePrefix' :
                $googleFieldValue = $googleRecord->getNamePrefix();
                break;
            case 'gd:givenName' :
                $googleFieldValue = $googleRecord->getFirstName();
                break;
            case 'gd:familyName' :
                $googleFieldValue = $googleRecord->getLastName();
                break;
            case 'gd:orgTitle' :
                $googleFieldValue = $googleRecord->getTitle();
                break;
            case 'gd:orgName' :
                $googleFieldValue = $googleRecord->getAccountName($user->id);
                break;
            case 'gContact:birthday' :
                $googleFieldValue = $googleRecord->getBirthday();
                break;
            case 'gd:email' :
                $emails = $googleRecord->getEmails();
                $googleFieldValue = $this->getMappedValue($emails, $googleFieldDetails);
                break;
            case 'gd:phoneNumber' :
                $phones = $googleRecord->getPhones();
                $googleFieldValue = $this->getMappedValue($phones, $googleFieldDetails);
                break;
            case 'gd:structuredPostalAddress' :
                $addresses = $googleRecord->getAddresses();
                $googleFieldValue = $this->getMappedValue($addresses, $googleFieldDetails);
                break;
            case 'content' :
                $googleFieldValue = $googleRecord->getDescription();
                break;
            case 'gContact:userDefinedField' :
                $userDefinedFields = $googleRecord->getUserDefineFieldsValues();
                $googleFieldValue = $this->getMappedValue($userDefinedFields, $googleFieldDetails);
                break;
            case 'gContact:website' :
                $websites = $googleRecord->getUrlFields();
                $googleFieldValue = $this->getMappedValue($websites, $googleFieldDetails);
                break;
        }
        return $googleFieldValue;
    }
    
    /**
     * Tarsform Google Records to Vtiger Records
     * @param <array> $targetRecords
     * @return <array> tranformed Google Records
     */
    public function transformToSourceRecord($targetRecords, $user = false) {
        $entity = array();
        $contacts = array();
      
        if(!isset($this->fieldMapping)) {
            $this->fieldMapping = Google_Utils_Helper::getFieldMappingForUser($user);
        }
        
        foreach ($targetRecords as $googleRecord) {
            if ($googleRecord->getMode() != WSAPP_SyncRecordModel::WSAPP_DELETE_MODE) {
                if(!$user) $user = Users_Record_Model::getCurrentUserModel();
                
                $entity = Vtiger_Functions::getMandatoryReferenceFields('Contacts');
                $entity['assigned_user_id'] = vtws_getWebserviceEntityId('Users', $user->id);
                
                foreach($this->fieldMapping as $vtFieldName => $googleFieldDetails) {
                    $googleFieldValue = $this->getGoogleFieldValue($googleFieldDetails, $googleRecord, $user);
                    
                    if($vtFieldName == 'mailingaddress') {
                        $address = $googleFieldValue;
                        $entity['mailingstreet'] = $address['street'];
                        $entity['mailingpobox'] = $address['pobox'];
                        $entity['mailingcity'] = $address['city'];
                        $entity['mailingstate'] = $address['region'];
                        $entity['mailingzip'] = $address['postcode'];
                        $entity['mailingcountry'] = $address['country'];
                        if(empty($entity['mailingstreet'])) {
                            $entity['mailingstreet'] = $address['formattedAddress'];
                        }
                    } else if($vtFieldName == 'otheraddress') {
                        $address = $googleFieldValue;
                        $entity['otherstreet'] = $address['street'];
                        $entity['otherpobox'] = $address['pobox'];
                        $entity['othercity'] = $address['city'];
                        $entity['otherstate'] = $address['region'];
                        $entity['otherzip'] = $address['postcode'];
                        $entity['othercountry'] = $address['country'];
                        if(empty($entity['otherstreet'])) {
                            $entity['otherstreet'] = $address['formattedAddress'];
                        }
                    } else {
                        $entity[$vtFieldName] = $googleFieldValue;
                    }
                }
                
                if (empty($entity['lastname'])) {
                    if (!empty($entity['firstname'])) {
                        $entity['lastname'] = $entity['firstname'];
                    } else if(empty($entity['firstname']) && !empty($entity['email'])) {
                        $entity['lastname'] = $entity['email'];
                    } else if( !empty($entity['mobile']) || !empty($entity['mailingstreet'])) {
                        $entity['lastname'] = 'Google Contact';
                    } else {
                        continue;
                    }
                }
            }
            $contact = $this->getSynchronizeController()->getSourceRecordModel($entity);
            
            $contact = $this->performBasicTransformations($googleRecord, $contact);
           
            $contact = $this->performBasicTransformationsToSourceRecords($contact, $googleRecord);
            $contacts[] = $contact;
        }
        
        return $contacts;
    }
    
    /**
     * Pull the contacts from google
     * @param <object> $SyncState
     * @return <array> google Records
     */
    public function pull($SyncState, $user = false) {
        return $this->getContacts($SyncState, $user);
    }
    
    /**
     * Helper to send http request using NetClient
     * @param <String> $url
     * @param <Array> $headers
     * @param <Array> $params
     * @param <String> $method
     * @return <Mixed>
     */
    protected function fireRequest($url,$headers,$params=array(),$method='POST') {
        $httpClient = new Vtiger_Net_Client($url);
        if(count($headers)) $httpClient->setHeaders($headers);
        switch ($method) {
            case 'POST':
                $response = $httpClient->doPost($params);
                break;
            case 'GET':
                $response = $httpClient->doGet($params);
                break;
        }
        return $response;
    }
    
    function fetchContactsFeed($query) {
        $query['alt'] = 'json';
        if($this->apiConnection->isTokenExpired()) $this->apiConnection->refreshToken();
        $headers = array(
            'GData-Version' => $this->apiVersion,
            'Authorization' => $this->apiConnection->token['access_token']['token_type'] . ' ' .
            $this->apiConnection->token['access_token']['access_token'],
        );
        $response = $this->fireRequest(self::CONTACTS_URI, $headers, $query, 'GET');
       
        return $response;
    }
    
    function getContactListFeed($query) {
        $feed = $this->fetchContactsFeed($query);
        $decoded_feed = json_decode($feed,true);
        return $decoded_feed;
    }
    
    function googleFormat($date) {
        return str_replace(' ', 'T', $date);
    }
    
    /**
     * Pull the contacts from google
     * @param <object> $SyncState
     * @return <array> google Records
     */
    public function getContacts($SyncState, $user = false) {
        if(!$user) $user = Users_Record_Model::getCurrentUserModel();
        $query = array(
            'pageSize' => $this->maxResults,
            'requestSyncToken' => true,
            'personFields' => 'urls,biographies,userDefined,metadata,names,organizations,emailAddresses,clientData,birthdays,phoneNumbers,genders,addresses',
            'sortOrder' => 'LAST_MODIFIED_ASCENDING',
            
        );
        
        if (Google_Utils_Helper::getNextSyncToken('Contacts', $user)) {
            $query['syncToken'] = Google_Utils_Helper::getNextSyncToken('Contacts', $user);
        }
        
        $feed = $this->getContactListFeed($query);
        
        if(isset($feed['nextSyncToken']))
            $nextSyncToken = $feed['nextSyncToken'];
            
        $this->totalRecords = $feed['totalItems'];
      
        $contactRecords = array();
        if (count($feed['connections']) > 0) {
            $lastEntry = end($feed['connections']);
            $maxModifiedTime = date('Y-m-d H:i:s', strtotime(Google_Contacts_Model::vtigerFormat($lastEntry['metadata']['sources'][0]['updateTime'])) + 1);
            
            if ($this->totalRecords > $this->maxResults) {
                if (!Google_Utils_Helper::getSyncTime('Contacts', $user)) {
                    $query['pageToken'] = $feed['nextPageToken'];
                }
                
                $query['pageSize'] = (2000);
                if(isset($feed['nextPageToken']))
                    $query['pageToken'] = $feed['nextPageToken'];
               
                $extendedFeed = $this->getContactListFeed($query);
                
                if(isset($extendedFeed['nextSyncToken']))
                    $nextSyncToken = $extendedFeed['nextSyncToken'];
                
                if(is_array($extendedFeed['connections'])) {
                    $contactRecords = array_merge($feed['connections'], $extendedFeed['connections']);
                } else {
                    $contactRecords = $feed['connections'];
                }
            } else {
                $contactRecords = $feed['connections'];
            }
        }
        
        $googleRecords = array();
       
        foreach ($contactRecords as $i => $contact) {
            
            $recordModel = Google_Contacts_Model::getInstanceFromValues(array('entity' => $contact));
            
            $deleted = false;
           
            if($contact['metadata']['deleted']) {
                $deleted = true;
            }
            if (!$deleted) {
                $recordModel->setType($this->getSynchronizeController()->getSourceType())->setMode(WSAPP_SyncRecordModel::WSAPP_UPDATE_MODE);
            } else {
                $recordModel->setType($this->getSynchronizeController()->getSourceType())->setMode(WSAPP_SyncRecordModel::WSAPP_DELETE_MODE);
            }
            $googleRecords[$contact['resourceName']] = $recordModel;
        }
        
        $this->createdRecords = count($googleRecords);
        if (isset($maxModifiedTime) && isset($nextSyncToken)) {
            Google_Utils_Helper::updateSyncTime('Contacts', $maxModifiedTime, $user, $nextSyncToken);
        }else if (isset($maxModifiedTime) ) { 
            Google_Utils_Helper::updateSyncTime('Contacts', $maxModifiedTime, $user, false);
        }else if(isset($nextSyncToken)) {
            Google_Utils_Helper::updateSyncTime('Contacts', false, $user, $nextSyncToken);
        }else {
            Google_Utils_Helper::updateSyncTime('Contacts', false, $user, false);
        }
        
        return $googleRecords;
    }
    
   
    public function mbEncode($str) {
        global $default_charset;
        $convmap = array(0x080, 0xFFFF, 0, 0xFFFF);
        return mb_encode_numericentity(htmlspecialchars($str), $convmap, $default_charset);
    }
    
    function  addEntityDetailsToEntry($entity,$user){
         
        $enterData = array();
        $names = array();
        $organizations = array();
        if($entity->get('salutationtype')) $name["honorificPrefix"] = $entity->get('salutationtype');
        if($entity->get('firstname')) $name["givenName"] = $entity->get('firstname');
        if($entity->get('lastname')) $name["familyName"] = $entity->get('lastname');
        $names[]= $name;
        
        if($entity->get('account_id') || $entity->get('title')) {
            if($entity->get('account_id')) $organization["name"] = getAccountName($entity->get('account_id'));
            if($entity->get('title')) $organization["title"] = $entity->get('title');
            $organizations[] = $organization;
        }
        
        if(!isset($this->fieldMapping)) {
            $this->fieldMapping = Google_Utils_Helper::getFieldMappingForUser($user);
        }
        
        $emailAddresses = array();
        $phoneNumbers = array();
        $birthdays = array();
        $addresses = array();
        $biographies = array();
        $userDefinedField = array();
        $urls = array();
        
        foreach($this->fieldMapping as $vtFieldName => $googleFieldDetails) {
            if(in_array($googleFieldDetails['google_field_name'],array('gd:givenName','gd:familyName','gd:orgTitle','gd:orgName','gd:namePrefix')))
                continue;
                
            switch ($googleFieldDetails['google_field_name']) {
                case 'gd:email' :
                    if($entity->get($vtFieldName)) {
                        $emailAddresse['type'] = $googleFieldDetails['google_field_type'];
                        $emailAddresse["value"] = $entity->get($vtFieldName);
                        if($vtFieldName == 'email')
                            $emailAddresse['metadata']=array("primary"=>'true');
                        else
                            $emailAddresse['metadata']=array("primary"=>'false');
                        $emailAddresses[] = $emailAddresse;
                    }
                    break;
                case 'gContact:birthday' :
                    if($entity->get('birthday')) {
                        $birthday['date'] = array(
                            "day" => date('d',strtotime($entity->get('birthday'))),
                            "month" => date('m',strtotime($entity->get('birthday'))),
                            "year" => date('Y',strtotime($entity->get('birthday'))),
                        );
                        $birthdays[] = $birthday;
                    }
                    break;
                case 'gd:phoneNumber' :
                    if($entity->get($vtFieldName)) {
                        $phoneNumber['value'] = $entity->get($vtFieldName);
                        $phoneNumber['type'] = $googleFieldDetails['google_field_type'];
                        $phoneNumbers[] = $phoneNumber;
                    }
                    break;
                case 'gd:structuredPostalAddress' :
                    
                    $mailaddress = array();
                    $otheraddress = array();
                    if($vtFieldName == 'mailingaddress') {
                        if($entity->get('mailingstreet') || $entity->get('mailingpobox') || $entity->get('mailingzip') ||
                            $entity->get('mailingcity') || $entity->get('mailingstate') || $entity->get('mailingcountry')) {
                                $mailaddress['type'] = $googleFieldDetails['google_field_type'];
                                if($entity->get('mailingstreet')) $mailaddress["streetAddress"] = $entity->get('mailingstreet');
                                if($entity->get('mailingpobox')) $mailaddress["poBox"] =  $entity->get('mailingpobox');
                                if($entity->get('mailingzip')) $mailaddress['postalCode'] = $entity->get('mailingzip');
                                if($entity->get('mailingcity')) $mailaddress['city'] = $entity->get('mailingcity');
                                if($entity->get('mailingstate')) $mailaddress['region'] = $entity->get('mailingstate');
                                if($entity->get('mailingcountry')) $mailaddress['country'] = $entity->get('mailingcountry');
                                $addresses[] = $mailaddress;
                            }
                    } else {
                        if($entity->get('otherstreet') || $entity->get('otherpobox') || $entity->get('otherzip') ||
                            $entity->get('othercity') || $entity->get('otherstate') || $entity->get('othercountry')) {
                                $otheraddress['type'] = $googleFieldDetails['google_field_type'];
                                if($entity->get('otherstreet')) $otheraddress["streetAddress"] = $entity->get('otherstreet');
                                if($entity->get('otherpobox')) $otheraddress["poBox"] = $entity->get('otherpobox');
                                if($entity->get('otherzip')) $otheraddress['postalCode'] = $entity->get('otherzip');
                                if($entity->get('othercity')) $otheraddress['city'] = $entity->get('othercity');
                                if($entity->get('otherstate')) $otheraddress['region'] = $entity->get('otherstate');
                                if($entity->get('othercountry')) $otheraddress['country'] = $entity->get('othercountry');
                                $addresses[] = $otheraddress;
                            }
                    }
                    break;
                case 'content' :
                    if($entity->get($vtFieldName)){
                        $biographie['value'] = $entity->get($vtFieldName);
                        $biographie["contentType"] = "TEXT_HTML";
                        $biographies[] = $biographie;
                    }
                    break;
                case 'gContact:userDefinedField' :
                    if($entity->get($vtFieldName) && $googleFieldDetails['google_custom_label']) {
                        $userDefinedFields['key'] = $this->mbEncode(decode_html($googleFieldDetails['google_custom_label']));
                        $userDefinedFields['value'] = $this->mbEncode($entity->get($vtFieldName));
                        $userDefinedField[] = $userDefinedFields;
                    }
                    break;
                case 'gContact:website' :
                    if($entity->get($vtFieldName)) {
                        $url['type'] = $googleFieldDetails['google_field_type'];
                        $url['value'] = $this->mbEncode($entity->get($vtFieldName));
                        $urls[] = $url;
                    }
                    break;
                }
        }
        
        $returndata = array(
            "names" => $names,
            "addresses" => $addresses,
            "birthdays" => $birthdays,
            "emailAddresses" => $emailAddresses,
            "biographies" => $biographies,
            "urls" => $urls,
            "organizations" => $organizations,
            "phoneNumbers" => $phoneNumbers,
            "userDefined" => $userDefinedField
        );
        
        return $returndata;
       
    }
    
    function createContactFeed($query,$data) {
        $query['alt'] = 'json';
        if($this->apiConnection->isTokenExpired()) $this->apiConnection->refreshToken();
        $headers = array(
            'GData-Version' => $this->apiVersion,
            'Authorization' => $this->apiConnection->token['access_token']['token_type'] . ' ' .
            $this->apiConnection->token['access_token']['access_token'],
            'Content-Type' => 'application/json',
        );
        $response = $this->fireRequest(self::CREATE_CONTACT_URI.'?'.http_build_query($query), $headers, $data, 'POST');
        
        return $response;
    }
    
    function updateContactFeed($query, $id, $data) {
        $query['alt'] = 'json';
        if($this->apiConnection->isTokenExpired()) $this->apiConnection->refreshToken();
        $getQuery = array(
            'alt'=>'json',
            'personFields' => 'names'
        );
        $headers = array(
            'GData-Version' => $this->apiVersion,
            'Authorization' => $this->apiConnection->token['access_token']['token_type'] . ' ' .
            $this->apiConnection->token['access_token']['access_token'],
        );
        $response = $this->fireRequest(self::GET_CONTACT_URI.$id, $headers, $getQuery, 'GET');
        
        $personData = json_decode($response, true);
        $data['etag'] = $personData['etag'];
        
        $curlheaders = array(
            'GData-Version : '. $this->apiVersion,
            'Authorization: '. $this->apiConnection->token['access_token']['token_type'] . ' ' .
            $this->apiConnection->token['access_token']['access_token'],
            'Content-Type :application/json',
        );
        $updateUrl = "https://people.googleapis.com/v1/".$id.":updateContact";
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $updateUrl.'?'.http_build_query($query));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_HTTPHEADER, $curlheaders);
        $response = curl_exec($curl);
        curl_close($curl);
        
        return $response;
    }
    
    function deleteContactEntry($id){
        $query['alt'] = 'json';
        if($this->apiConnection->isTokenExpired()) $this->apiConnection->refreshToken();
        $curlheaders = array(
            'GData-Version : '. $this->apiVersion,
            'Authorization: '. $this->apiConnection->token['access_token']['token_type'] . ' ' .
            $this->apiConnection->token['access_token']['access_token'],
        );
        $deleteUrl = "https://people.googleapis.com/v1/".$id.":deleteContact";
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $deleteUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $curlheaders);
        $response = curl_exec($curl);
        curl_close($curl);
        
        return $response;
    }
    
    /**
     * Function to push records in a batch
     * https://developers.google.com/google-apps/contacts/v3/index#batch_operations
     * @global <String> $default_charset
     * @param <Array> $records
     * @param <Users_Record_Model> $user
     * @return <Array> - pushedRecords
     */
    protected function pushChunk($records,$user) {
        global $default_charset;
        
        foreach ($records as $record) {
            $entity = $record->get('entity');
            
            $data = $this->addEntityDetailsToEntry($entity,$user);
           
            try {
                if ($record->getMode() == WSAPP_SyncRecordModel::WSAPP_UPDATE_MODE) {
                    $query = array(
                        'updatePersonFields' => 'urls,biographies,userDefined,names,organizations,emailAddresses,clientData,birthdays,phoneNumbers,genders,addresses',
                    );
                    $response = $this->updateContactFeed($query, $entity->get('_id'), $data);
                } else if ($record->getMode() == WSAPP_SyncRecordModel::WSAPP_DELETE_MODE) {
                    $response = $this->deleteContactEntry($entity->get('_id'));
                } else {
                    $query = array(
                        'personFields' => 'urls,biographies,userDefined,metadata,names,organizations,emailAddresses,clientData,birthdays,phoneNumbers,genders,addresses',
                    );
                    $response = $this->createContactFeed($query, json_encode($data));
                }
            } catch (Exception $e) {
                continue;
            }
            
            $record->set('entity', json_decode($response,true));
           
        }
      
        return $records;
    }
    
    /**
     * Function to push records in batch of maxBatchSize
     * @param <Array Google_Contacts_Model> $records
     * @param <Users_Record_Model> $user
     * @return <Array> - pushed records
     */
    protected function batchPush($records,$user) {
        $chunks = array_chunk($records, $this->maxBatchSize);
        $mergedRecords = array();
        foreach($chunks as $chunk) {
            $pushedRecords = $this->pushChunk($chunk, $user);
            $mergedRecords = array_merge($mergedRecords,$pushedRecords);
        }
        
        return $mergedRecords;
    }
    
    /**
     * Push the vtiger records to google
     * @param <array> $records vtiger records to be pushed to google
     * @return <array> pushed records
     */
    public function push($records, $user = false) {
        if(!$user) $user = Users_Record_Model::getCurrentUserModel();
        
        if(!isset($this->selectedGroup))
            $this->selectedGroup = Google_Utils_Helper::getSelectedContactGroupForUser($user);
            
        if($this->selectedGroup != '' && $this->selectedGroup != 'all') {
            if($this->selectedGroup == 'none') return array();
            if(!isset($this->groups)) {
                $this->groups = $this->pullGroups(TRUE);
            }
            if(!in_array($this->selectedGroup, $this->groups['entry']))
                return array();
        }
        
        $updateRecords = $deleteRecords = $addRecords = array();
        foreach($records as $record) {
            if ($record->getMode() == WSAPP_SyncRecordModel::WSAPP_UPDATE_MODE) {
                $updateRecords[] = $record;
            } else if ($record->getMode() == WSAPP_SyncRecordModel::WSAPP_DELETE_MODE) {
                $deleteRecords[] = $record;
            } else {
                $addRecords[] = $record;
            }
        }
       
        if(count($deleteRecords)) {
            $deletedRecords = $this->batchPush($deleteRecords, $user);
        }
       
        if(count($updateRecords)) {
            $updatedRecords = $this->batchPush($updateRecords, $user);
        }
        
        if(count($addRecords)) {
            $addedRecords = $this->batchPush($addRecords, $user);
        }
        
        $i = $j = $k = 0;
        foreach($records as $record) {
            if ($record->getMode() == WSAPP_SyncRecordModel::WSAPP_UPDATE_MODE) {
                $uprecord = $updatedRecords[$i++];
                $newEntity = $uprecord->get('entity');
                $record->set('entity',$newEntity);
            } else if ($record->getMode() == WSAPP_SyncRecordModel::WSAPP_DELETE_MODE) {
                $delrecord = $deletedRecords[$j++];
                $newEntity = $delrecord->get('entity');
                $record->set('entity',$newEntity);
            } else {
                $adrecord = $addedRecords[$k++];
                $newEntity = $adrecord->get('entity');
                $record->set('entity',$newEntity);
            }
        }
        return $records;
    }
    
    /**
     * Tarsform  Vtiger Records to Google Records
     * @param <array> $vtContacts
     * @return <array> tranformed vtiger Records
     */
    public function transformToTargetRecord($vtContacts) {
        $records = array();
        foreach ($vtContacts as $vtContact) {
            $recordModel = Google_Contacts_Model::getInstanceFromValues(array('entity' => $vtContact));
            $recordModel->setType($this->getSynchronizeController()->getSourceType())->setMode($vtContact->getMode())->setSyncIdentificationKey($vtContact->get('_syncidentificationkey'));
            $recordModel = $this->performBasicTransformations($vtContact, $recordModel);
            $recordModel = $this->performBasicTransformationsToTargetRecords($recordModel, $vtContact);
            $records[] = $recordModel;
        }
        return $records;
    }
    
    /**
     * returns if more records exits or not
     * @return <boolean> true or false
     */
    public function moreRecordsExits() {
        return ($this->totalRecords - $this->createdRecords > 0) ? true : false;
    }
    
    
    /**
     * Function to get user profile info
     * @return <Mixed>
     */
    public function getUserProfileInfo() {
        if($this->apiConnection->isTokenExpired()) $this->apiConnection->refreshToken();
        $headers = array(
            'GData-Version' => $this->apiVersion,
            'Authorization' => $this->apiConnection->token['access_token']['token_type'] . ' ' .
            $this->apiConnection->token['access_token']['access_token'],
            'If-Match' => '*',
            'Content-Type' => 'application/json',
        );
        $response = $this->fireRequest(self::USER_PROFILE_INFO, $headers, array(), 'GET');
        return $response;
    }
}

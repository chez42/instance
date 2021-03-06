<?php
/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ***********************************************************************************/

global $adb;

$client_id = '';

$client_secret = '';

$query = "SELECT * FROM vtiger_ringcentral_configuration_settings";

$result = $adb->pquery($query , array());

if($adb->num_rows($result)){
    $client_id = $adb->query_result($result, 0, 'clientid');
    $client_secret = $adb->query_result($result, 0, 'clientsecret');
    $server = $adb->query_result($result, 0, 'server');
}

define("CLIENT_ID",$client_id);

define("CLIENT_SECRET",$client_secret);

define("SERVER", $server);

Class RingCentral_Config_Connector {
    
    static $client_id = CLIENT_ID;
    
    static $client_secret = CLIENT_SECRET;
    
    static $server = SERVER;
    
    static function getCallBackUrl() {
        global $site_URL;
        return 'https://oauth.360vew.com';
    }
	
    
}

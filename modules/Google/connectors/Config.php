<?php
/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ***********************************************************************************/

Class Google_Config_Connector {
	
    static $clientId = null;
	
	static $clientSecret = null;
	
	static $redirect_url = null;
	
	public static function init(){
	    
	    global $adb;
	    
	    $query = "SELECT * FROM vtiger_oauth_configuration WHERE type = 'Google'";
	    
	    $result = $adb->pquery($query , array());
	    
	    if($adb->num_rows($result)){
	        self::$clientId = $adb->query_result($result, 0, 'client_id');
	        self::$clientSecret = $adb->query_result($result, 0, 'client_secret');
	        self::$redirect_url = $adb->query_result($result, 0, 'redirect_url');
	    }
	}
}
Google_Config_Connector::init();

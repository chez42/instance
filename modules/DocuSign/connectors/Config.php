<?php
/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ***********************************************************************************/

Class DocuSign_Config_Connector {
    
    static $clientId = null;
    
    static $clientSecret = null;
    
    static $redirect_url = null;
    
    static $server = null;
    
    public static function init(){
        global $adb;
        
        $result = $adb->pquery("SELECT * FROM vtiger_document_designer_auth_settings" , array());
        
        if($adb->num_rows($result)){
            self::$clientId = $adb->query_result($result, 0, 'clientid');
            self::$clientSecret = $adb->query_result($result, 0, 'clientsecret');
            self::$server = $adb->query_result($result, 0, 'server');
            self::$redirect_url = $adb->query_result($result, 0, 'redirect_url');
        }
    }
}

DocuSign_Config_Connector::init();





<?php
/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ***********************************************************************************/

Class OwnCloud_Config_Connector {
    
    static $username = null;
    
    static $password = null;
    
    static $url = null;
    
    public static function init(){
        
        global $adb, $current_user;
        
        $resultConfig = $adb->pquery("SELECT * FROM vtiger_owncloud_configration" , array());
        
        if($adb->num_rows($resultConfig)){
            
            $url = $adb->query_result($resultConfig, 0, 'url');
            
            $url = rtrim($url, "/");
            
            self::$url = $url .'/';
            
            $result = $adb->pquery("SELECT * FROM vtiger_owncloud_credentials 
            WHERE userid=?" , array($current_user->id));
            
            if($adb->num_rows($result)){
                self::$username = $adb->query_result($result, 0, 'username');
                self::$password = $adb->query_result($result, 0, 'password');
            }
        }
    }
}

OwnCloud_Config_Connector::init();





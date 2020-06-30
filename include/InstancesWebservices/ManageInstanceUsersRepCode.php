<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************ */

function vtws_getinstanceusers($element) {
    
    global $adb;
    
    $data = array();
    
    if($element['mode'] != 'cust_update'){
    
        $users = array();
    
        $userQuery = $adb->pquery("SELECT * FROM vtiger_users WHERE vtiger_users.deleted = 0");
        
        if($adb->num_rows($userQuery)){
            for($u=0;$u<$adb->num_rows($userQuery);$u++){
                $users[] = $adb->query_result_rowdata($userQuery, $u);
            }
        }
        
        $data =  $users;
        
    }else if($element['mode'] == 'cust_update'){
        
        $custValue = $element['cust_value'];
        
        foreach($custValue as $userid => $cust){
            
            $adb->pquery("UPDATE vtiger_users SET advisor_control_number = ? WHERE id = ?",
                array($cust, $userid));
            
        }
        
        $data = array('success'=>true);
        
    }
    return $data;
}
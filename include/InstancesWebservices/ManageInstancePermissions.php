<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************ */

function vtws_manageinstancepermissions($element) {
    
    global $adb;
    
    $data = array();
    
    if($element['mode'] == 'get_permissions'){
        
		$result = $adb->pquery("select * from vtiger_instance_permissions");
		
		if($adb->num_rows($result)){
			
			$portfolio_reports = $adb->query_result($result, 0, "portfolio_reports");
			
		} else {
			$adb->pquery("insert into  vtiger_instance_permissions(portfolio_reports) values(1)");
			$portfolio_reports = 1;
		}
        
        $data['portfolio_reports'] = $portfolio_reports;
        
        
    } else if($element['mode'] == 'save_permissions'){
        
		if($element['portfolio_reports']){
			$portfolio_reports = 1;
		} else {
			$portfolio_reports = 0;
		}
		
        $adb->pquery("update vtiger_instance_permissions set 
		portfolio_reports = ?", array($portfolio_reports));
        
        $data = array('success'=>true);
        
    }
    
    return $data;
}
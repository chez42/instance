<?php

function vtws_updateportaldata($element,$user){
    
    global $adb,$site_URL;
    
	$result = array();
	
	if(isset($element['ID']) && $element['ID'] != ''){
	
		if(isset($element['portal_widget_position']) && $element['portal_widget_position'] != ''){
	
			$adb->pquery("UPDATE vtiger_contactdetails SET portal_widget_position = ? WHERE contactid =?",array($element['portal_widget_position'],$element['ID']));
			$result['success'] = true;

		}
	
	}
	
	return $result;
}
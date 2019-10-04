<?php

include_once('config.php');
global $adb;

if(isset($_REQUEST['positionSize']) && $_REQUEST['positionSize'] != ''){
  
    $position = $_REQUEST['positionSize'];
    $dataValue = array();
    foreach($position as $key => $value){
        $dataValue[$key] = json_decode($value,true);
    }
    $adb->pquery("UPDATE vtiger_contactdetails SET portal_widget_position = ? WHERE vtiger_contactdetails.contactid = ?",
        array(json_encode($dataValue), $_SESSION['ID']));
    
}
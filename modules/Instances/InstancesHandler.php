<?php

function SyncMasterPasswordInInstance($entityData){
    
    $url = $entityData->get('domain');
    
    require_once "vtlib/Vtiger/Net/Client.php";
    
    $httpc = new Vtiger_Net_Client(rtrim('/',$url).'/webservice.php');
    
    $params = array();

    $element = array(); 
    $element['master_password'] = $entityData->get('master_password');
    
    $params = array(
        "operation"=>'sync_master_password',
        "element" => json_encode($element)
    );
    
    $response = $httpc->doPost($params);
   
}
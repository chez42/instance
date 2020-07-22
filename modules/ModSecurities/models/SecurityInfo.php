<?php

class ModSecurities_SecurityInfo_Model extends Vtiger_Module_Model {
    private $eod, $omni;

    public function __construct(){

    }

    public function GetEodSymbolData($symbol){
        $guz = new cEodGuzzle();
        $security_id = ModSecurities_Module_Model::GetSecurityIdBySymbol($symbol);
        $security_instance = ModSecurities_Record_Model::getInstanceById($security_id);
        $security_type = $security_instance->get('securitytype');

        switch(strtolower($security_type)){
            case 'bonds':
                    $rawData = $guz->getBonds($symbol);
                break;

        }
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2017-03-30
 * Time: 3:40 PM
 */

require_once("libraries/javaBridge/JavaConnector.php");

class Parsing extends ParseConnector{
    public function WriteFiles($custodian, $operation, $skipDays, $dontIgnoreFileIfExists, $extension=null, $repcode=null){
        StatusUpdate::UpdateMessage("PARSING{$custodian}", "Beginning Parsing Process");
        $data = array();
        $data['custodian'] = $custodian;
        $data['operation'] = $operation;
        $data['skipDays'] = $skipDays;
        $data['dontIgnoreFileIfExists'] = $dontIgnoreFileIfExists;
        if(!is_null($extension))
            $data['extension'] = $extension;
        /*        $data = array("custodian" => $custodian,
                    "operation" => $operation,
                    "skipDays" => $skipDays,
                    "dontIgnoreFileIfExists" => $dontIgnoreFileIfExists);*/
        if(!is_null($repcode))
            $data['repcode'] = $repcode;
        $result = $this->MakeCall("http://lanserver24/OmniServ/AutoParse", $data);
        StatusUpdate::UpdateMessage("PARSING{$custodian}", "Finished Parsing");
        return $result;
    }
/*
    public function UpdatePortfolios($custodian, $vtigerDBName){
        StatusUpdate::UpdateMessage("PARSING{$custodian}", "Parsing Portfolio Files");
        $data = array("custodian" => $custodian,
            "operation" => "updateportfolios",
            "vtigerDBName" => $vtigerDBName);
        $result = $this->MakeCall("http://lanserver24/OmniServ/AutoParse", $data);
        return $result;
    }

    public function UpdateSecurities($custodian, $vtigerDBName){
        StatusUpdate::UpdateMessage("PARSING{$custodian}", "Parsing Security Files");
        $data = array("custodian" => $custodian,
            "operation" => "updatesecurities",
            "vtigerDBName" => $vtigerDBName);
        $result = $this->MakeCall("http://lanserver24/OmniServ/AutoParse", $data);
        return $result;
    }

    public function UpdatePositions($custodian, $vtigerDBName){
        StatusUpdate::UpdateMessage("PARSING{$custodian}", "Parsing Position Files");
        $data = array("custodian" => $custodian,
            "operation" => "updatepositions",
            "vtigerDBName" => $vtigerDBName);
        $result = $this->MakeCall("http://lanserver24/OmniServ/AutoParse", $data);
        return $result;
    }

    public function UpdateTransactions($custodian, $vtigerDBName){
        $data = array("custodian" => $custodian,
            "operation" => "updatetransactions",
            "vtigerDBName" => $vtigerDBName);
        $result = $this->MakeCall("http://lanserver24/OmniServ/AutoParse", $data);
        return $result;
    }

    public function VerifyDirectories($custodian, $vtigerDBName, $directory){
        $data = array("custodian" => $custodian,
            "operation" => "verifydirectories",
            "vtigerDBName" => $vtigerDBName,
            "directory" => $directory);
        $result = $this->MakeCall("http://lanserver24/OmniServ/AutoParse", $data);
        return $result;
    }

    public function CalculateWeight($vtigerDBName){
        $data = array("operation" => "updatepositionweight",
            "custodian" => "td",
            "vtigerDBName" => $vtigerDBName);
        $result = $this->MakeCall("http://lanserver24/OmniServ/AutoParse", $data);
        return $result;
    }*/
}
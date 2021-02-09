<?php


class AIWidgets{
    public $rep_codes, $account_numbers;

    public function __construct(){
        $this->rep_codes = PortfolioInformation_Module_Model::GetRepCodeListFromUsersTable();
        $this->account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersFromRepCodeOpenAndClosed($this->rep_codes);
    }

    public function CleanAUM(){
        global $adb;
echo sizeof($this->account_numbers);exit;
        foreach($this->account_numbers AS $k => $v){
            echo "'{$v}',";
        }
        $query = "";
    }
}
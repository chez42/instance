<?php


require_once("libraries/reports/cReports.php");
require_once('libraries/reports/cTransactions.php');
require_once("libraries/reports/cPholdingsInfo.php");
require_once("libraries/reports/cPortfolioDetails.php");
require_once("libraries/reports/pdf/cPDFDBAccess.php");

class PortfolioInformation_Messages_Model extends Vtiger_Module {
    public $messages = array();

    public function GenerateMessagesFromMainCategories($categories){
        foreach($categories AS $k => $v){
            if($k == "Options")
                $this->messages[] = "<span style='color:red;'>*Notice, this account contains options which show as quantity * 100</span>";
        }
    }
}
?>
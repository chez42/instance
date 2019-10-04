<?php
require_once("include/utils/omniscientCustom.php");
require_once("libraries/reports/cTransactions.php");
require_once('libraries/reports/cPortfolioDetails.php');
require_once("libraries/reports/cPholdingsInfo.php");
require_once("libraries/reports/pdf/cPDFDBAccess.php");
require_once("libraries/reports/cReports.php");

class PortfolioInformation_ReportBottom_View extends Vtiger_BasicAjax_View{
    
    function process(Vtiger_Request $request) {
        if(strlen($request->get("account_number") > 0)){
//            $holdings = new PortfolioInformation_Positions_Model();
//            $holdings->GenerateReport($request->get("account_number"));
//            return $holdings->GenerateReport($request->get("account_number"));
        } else
            return "<div class='ReportBottom'></div>";
    }
    
}

?>
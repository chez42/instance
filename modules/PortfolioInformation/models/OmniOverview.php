<?php
require_once("libraries/reports/cPOverview.php");
require_once("libraries/reports/cTWR.php");
require_once("libraries/reports/cReturn.php");
require_once("libraries/reports/calculateReturn.php");

require_once("libraries/reports/cReports.php");
require_once("libraries/reports/cReportGlobals.php");
require_once('libraries/reports/cTransactions.php');
require_once("libraries/reports/cPholdingsInfo.php");
require_once("libraries/reports/cPortfolioDetails.php");
require_once("libraries/reports/pdf/cPDFDBAccess.php");

require_once("modules/PortfolioInformation/PortfolioInformation.php");


class PortfolioInformation_OmniOverview_Model extends Vtiger_Module {

    public function PortfolioInformation_OmniOverview_Model(array $account_numbers, $start_date, $end_date){

    }

}
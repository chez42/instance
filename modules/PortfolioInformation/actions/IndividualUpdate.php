<?php
if (ob_get_level() == 0) ob_start();

require_once("libraries/reports/cTransactions.php");
require_once("libraries/reports/Portfolios.php");
require_once("include/utils/cron/cPortfolioAccess.php");
require_once("include/utils/cron/cPricingAccess.php");
require_once("include/utils/cron/cSecuritiesAccess.php");
require_once("include/utils/cron/cAdvisorAccess.php");

class PortfolioInformation_IndividualUpdate_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        global $adb;
        set_time_limit (0);
        ini_set('memory_limit', '2048M');
        $port = new cPortfolioAccess();

        $account_number = $_REQUEST['account_number'];
        
        $query = "SELECT portfolio_id FROM vtiger_portfolios WHERE portfolio_account_number = ?";
        $result = $adb->pquery($query, array($account_number));
        $pids = $adb->query_result($result, 0, 'portfolio_id');

        echo "<table>
            <tr><td>Steps</td><td>Description</td></tr>
            <tr><td>1</td><td>Variable <strong>skippricing</strong> is off by default for this individual update.</td></tr>
            <tr><td>2</td><td>Get all transactions from PC and copy them to the crm. We do not use modified date here, we pull all transactions for the given portfolio.</td></tr>
            <tr><td>3</td><td>Get the latest security list from PC and upate the CRM Securities</td></tr>
            <tr><td>4</td><td>Calculate the prices and cost basis into the transactions table. This will save having to calculate these individually in the future every time for portfolios/positions</td></tr>

        </table>";

        echo "Getting the advisors for linking<br />\r\n";
        $advisors = new cAdvisorAccess();
        $advisors->CreateAdvisors();
        $advisors->CreateAdvisorLinking();

        echo "1) Copying portfolios from PC to the CRM for portfolio id(s): {$pids}, Account: {$account_number} " .  date('m-d-Y H:i:s') . "<br />\r\n";
        $port->CopyPortfoliosFromPCToCRM($pids);
        echo "Done copying portfolios from PC to the CRM for portfolio id(s): {$pids} " .  date('m-d-Y H:i:s') . "<br />\r\n";

        if(!$_REQUEST['date'])
            $date = date('Y-m-d');//Set the date to today
        else 
            $date = $_REQUEST['date'];

        echo "<strong>START TIME: " . date("m-d-Y H:i:s") . "</strong><br />";

        $transactions = new cTransactionsAccess();
        $securities = new cSecuritiesAccess();

        echo "About to start copying transactions " . date('m-d-Y H:i:s') . "<br />\r\n";
        $rows = $transactions->CopyTransactionsFromPCToCRM($pids, $date);
        echo "Transactions complete, there were {$rows} rows " . date('m-d-Y H:i:s') . "<br /><br />\r\n";

        echo "Updating security list in the CRM " . date('m-d-Y H:i:s') . "<br />\r\n";
        $securities->UpdateCRMSecurities();
        echo "Done updating security list " . date('m-d-Y H:i:s') . "<br /><br />\r\n";

        echo "Updating transactions prices/cost_basis to make life easier " . date('m-d-Y H:i:s') . "<br />\r\n";
        $transactions->CalculatePriceAndCostBasis($pids, $date);
        echo "Finished updating prices/cost_basis " . date('m-d-Y H:i:s') . "<br /><br />\r\n";
/*
        echo "Updating CRM Securities based on last modified date from the CRM" . date('m-d-Y H:i:s') . "<br />\r\n";
        $securities->UpdateCRMSecurities();
        echo "Done Updating CRM Securities" . date('m-d-Y H:i:s') . "<br /><br />\r\n";
*/
        echo "Updating positions summary info.  Only cost basis and quantity update here right now, it does not calculate current value for the summary table yet " . date('m-d-Y H:i:s') . "<br />\r\n";
        $securities->UpdatePositionsSummary($pids, 1900-01-01);
        echo "Done updating positions summary table" . date('m-d-Y H:i:s') . "<br /><br />\r\n";

        echo "Calculating positions summary values section" . date('m-d-Y H:i:s') . "<br />\r\n";
        $securities->CalculatePositionsSummary($account_number);
        echo "Done updating positions summary values section" . date('m-d-Y H:i:s') . "<br /><br />\r\n";

        echo "Updating positions information module with position_summary values" . date('m-d-Y H:i:s') . "<br />\r\n";
        $securities->UpdatePositionInformationModule($account_number);
        echo "Done updating positions information module " . date('m-d-Y H:i:s') . "<br /><br />\r\n";

        echo "Updating Portfolio Summaries " . date('m-d-Y H:i:s') . "<br />\r\n";
        $port->UpdatePortfolioSummary($pids);
        echo "Done updating Portfolio Summaries " . date('m-d-Y H:i:s') . "<br /><br />\r\n";

        echo "Updating Portfolio Information Module " . date('m-d-Y H:i:s') . "<br />\r\n";
        $port->UpdatePortfolioInformationModule($account_number);
        echo "Done updating Portfolio Information Module " . date('m-d-Y H:i:s') . "<br /><br />\r\n";

        echo "<strong>END TIME: " . date("m-d-Y H:i:s") . "</strong><br />";

        set_time_limit (120);
        echo "PORTFOLIO CRON JOB FINISHED\n\r";
    }
}
?>

<?php
if (ob_get_level() == 0) ob_start();

require_once("libraries/reports/cTransactions.php");
require_once("libraries/reports/Portfolios.php");
require_once("libraries/reports/editing/TransactionsBridge.php");
require_once("include/utils/cron/cPortfolioAccess.php");
require_once("include/utils/cron/cPricingAccess.php");
require_once("include/utils/cron/cSecuritiesAccess.php");
require_once("include/utils/cron/cAdvisorAccess.php");

class PortfolioInformation_Cron_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        global $adb;
        set_time_limit (0);
        ini_set('memory_limit', '2048M');
        $port = new cPortfolioAccess();

        $advisor_id = $request->get('advisor_id');
        $override = $request->get('override');
        $reset = $request->get('reset');

        echo "<table>
            <tr><td>Steps</td><td>Description</td></tr>
            <tr><td>1</td><td>If variable <strong>skippricing</strong> is set, skip the pricing update otherwise we do it based on 1 week ago.</td></tr>
            <tr><td>2</td><td>Get all transactions from PC and copy them to the crm where modified date >= today (or the passed in </strong>date</strong>)</td></tr>
            <tr><td>3</td><td>Get the latest security list from PC and upate the CRM Securities</td></tr>
            <tr><td>4</td><td>Calculate the prices and cost basis into the transactions table. This will save having to calculate these individually in the future every time for portfolios/positions</td></tr>

        </table>";

        echo "<strong>START TIME: " . date("m-d-Y H:i:s") . "</strong><br />";
        ob_flush();
        flush();

        $advisors = new cAdvisorAccess();
        $advisors->CreateAdvisors();
        $advisors->CreateAdvisorLinking();

        if($request->get('skippricing'))
            echo "Skipping pricing update<br />\r\n";
        else{
            echo "About to go into pricing update<br />\r\n";
            ob_flush();
            flush();
            $pricing = new cPricingAccess();
            $pricing_result = $pricing->UpdatePrices();
            echo $pricing_result;
        }


        $port->CopyPortfoliosFromPCToCRM();
 
        //$result = $port->GetPortfolioIdAndAccountNumber($override, $reset, $advisor_id);
        if(!$request->get('date'))
            $date = date("Y-m-d",strtotime("-1 week"));//Set the date to today
        else 
            $date = $request->get('date');

        $transactions = new cTransactionsAccess(true);
        $securities = new cSecuritiesAccess();

        echo "About to start copying transactions " . date('m-d-Y H:i:s') . "<br />\r\n";
        ob_flush();
        flush();
        $rows = $transactions->CopyTransactionsFromPCToCRM(null, $date);
        echo "Transactions complete, there were {$rows} rows " . date('m-d-Y H:i:s') . "<br /><br />\r\n";

        echo "Pulling and matching securities to new module " . date('m-d-Y H:i:s') . "<br />\r\n";
        ob_flush();
        flush();
        $list = ModSecurities_SecurityBridge_Model::PullAllSecurities();
        ModSecurities_SecurityBridge_Model::WriteListToModSecurities($list);
        echo "List updated, Pricing calculating....<br />";
        echo "UPDATING PRICES...";
        ModSecurities_SecurityBridge_Model::UpdateAllModSecuritiesPrices();
        echo "<strong>Finished inserting new securities " . date('m-d-Y H:i:s') . "<br />\r\n";

        echo "<strong>Copying transactions from -1 week to Transactions module " . date('m-d-Y H:i:s') . "<br />\r\n";
        ob_flush();
        flush();
        $weekago_date = date("Y-m-d",strtotime("-1 week"));
        cTransactionsBridge::CreateTransactionsEntitiesFromPCTransactions($weekago_date);
        echo "Transactions copied to new module.  <strong>Note, prices not yet inserted</strong>";

        echo "Updating security list in the CRM " . date('m-d-Y H:i:s') . "<br />\r\n";
        ob_flush();
        flush();

        $securities->UpdateCRMSecurities();
        echo "Done updating security list " . date('m-d-Y H:i:s') . "<br /><br />\r\n";

        echo "Updating transactions prices/cost_basis to make life easier " . date('m-d-Y H:i:s') . "<br />\r\n";
        ob_flush();
        flush();

        $transactions->CalculatePriceAndCostBasis(null, $date);
        echo "Finished updating prices/cost_basis " . date('m-d-Y H:i:s') . "<br /><br />\r\n";

        echo "Updating CRM Securities based on last modified date from the CRM" . date('m-d-Y H:i:s') . "<br />\r\n";
        ob_flush();
        flush();

        $securities->UpdateCRMSecurities();
        echo "Done Updating CRM Securities" . date('m-d-Y H:i:s') . "<br /><br />\r\n";

        echo "Updating positions summary info.  Only cost basis and quantity update here right now, it does not calculate current value for the summary table yet " . date('m-d-Y H:i:s') . "<br />\r\n";
        ob_flush();
        flush();

        $securities->UpdatePositionsSummary(null, 1900-01-01);
        echo "Done updating positions summary table" . date('m-d-Y H:i:s') . "<br /><br />\r\n";

        echo "Calculating positions summary values section" . date('m-d-Y H:i:s') . "<br />\r\n";
        ob_flush();
        flush();

        $securities->CalculatePositionsSummary();
        echo "Done updating positions summary values section" . date('m-d-Y H:i:s') . "<br /><br />\r\n";

        echo "Updating positions information module with position_summary values" . date('m-d-Y H:i:s') . "<br />\r\n";
        ob_flush();
        flush();

        $securities->UpdatePositionInformationModule();
        echo "Done updating positions information module " . date('m-d-Y H:i:s') . "<br /><br />\r\n";

        echo "Updating Portfolio Summaries.. This is where the mammoth is called and is a ~45 minutes query, got get a coffee/lunch/sleep/something...  " . date('m-d-Y H:i:s') . "<br />\r\n";
        ob_flush();
        flush();

        $port->UpdatePortfolioSummary();
        echo "Done updating Portfolio Summaries " . date('m-d-Y H:i:s') . "<br /><br />\r\n";

        echo "Updating Portfolio Information Module " . date('m-d-Y H:i:s') . "<br />\r\n";
        ob_flush();
        flush();
        
        $port->UpdatePortfolioInformationModule();

        echo "<strong>END TIME: " . date("m-d-Y H:i:s") . "</strong><br />";
        ob_flush();
        flush();

        echo "Inserting into account_details_pdf for quick view access " . date("m-d-Y H:i:s") . "<br /><br />\r\n";
        ob_flush();
        flush();
        $query = "INSERT INTO account_details_pdf (account_number, account_name, master_account, custodian, account_type, management_fee, market_value, cash_value, annual_management_fee, total_value, last_update)
        (SELECT account_number, CONCAT(first_name, ' ', last_name), master_account, origination, account_type, 1, market_value, cash_value, annual_management_fee, total_value, last_modified FROM vtiger_portfolio_summary WHERE total_value != 0 AND total_value IS NOT NULL)
        ON DUPLICATE KEY UPDATE account_name=VALUES(account_name), master_account=VALUES(master_account), custodian=VALUES(custodian), account_type=VALUES(account_type), management_fee=VALUES(management_fee), 
        market_value=VALUES(market_value), cash_value=VALUES(cash_value), annual_management_fee=VALUES(annual_management_fee), total_value=VALUES(total_value), last_update=VALUES(last_update)";
        $adb->pquery($query, array());
        
        echo "Inserting into account_other_accounts_pdf for quick view access " . date("m-d-Y H:i:s") . "<br /><br />\r\n";
        ob_flush();
        flush();
        $query = "INSERT INTO account_other_accounts_pdf (primary_account, account_number, total_value, market_value, cash_value, last_update)
        (SELECT account_number AS primary_account, account_number, total_value, market_value, cash_value, last_modified FROM vtiger_portfolio_summary WHERE total_value != 0 AND total_value IS NOT NULL)
        ON DUPLICATE KEY UPDATE total_value=VALUES(total_value), market_value=VALUES(market_value), cash_value=VALUES(cash_value), last_update=VALUES(last_update)";
        $adb->pquery($query, array());
        
        echo "<br />RESETTING BATCH PROCESS<br />";
        $query = "UPDATE batch_process SET last_update_id = 0, last_reset=NOW() WHERE name='PortfolioInformation'";
        $adb->pquery($query, array());

        ob_flush();
        flush();
        echo "<br />Closing Portfolio Accounts<br />";
        $request->set('todo', 'AutoCloseAccounts');
        $close = new PortfolioInformation_ManualInteractions_Action();
        $close->process($request);

        echo "Beginning the Purge Process " . date('m-d-Y H:i:s') . "<br />\r\n";
        ob_flush();
        flush();
        $asset_allocation = new PortfolioInformation_AssetAllocation_Action();
        $asset_allocation->WriteComparisonTable();

        $query = "UPDATE vtiger_portfolioinformation p
                    JOIN vtiger_portfolioinformation_current pc ON p.account_number = pc.account_number
                    JOIN vtiger_portfolioinformationcf cf ON p.portfolioinformationid = cf.portfolioinformationid
                    SET p.total_value = pc.total_value, 
                    p.market_value = pc.market_value, 
                    p.cash_value = pc.cash_value,
                    cf.equities = pc.equities,
                    cf.fixed_income = pc.fixed_income
                    WHERE p.account_number = pc.account_number";

        $adb->pquery($query, array());
        echo "Finished the purge<br />";
        echo "<strong>END TIME: " . date("m-d-Y H:i:s") . "</strong><br />";
        ob_flush();
        flush();

        set_time_limit (120);
        echo "PORTFOLIO CRON JOB FINISHED\n\r";
    }
}
?>

<?php
if (ob_get_level() == 0) ob_start();

require_once("libraries/reports/cTransactions.php");
require_once("libraries/reports/Portfolios.php");
require_once("libraries/reports/editing/TransactionsBridge.php");
require_once("include/utils/cron/cPortfolioAccess.php");
require_once("include/utils/cron/cPricingAccess.php");
require_once("include/utils/cron/cSecuritiesAccess.php");
require_once("include/utils/cron/cAdvisorAccess.php");

class PortfolioInformation_ManualAccountsPC_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        global $adb;
        set_time_limit (0);
        ini_set('memory_limit', '2048M');
        $port = new cPortfolioAccess();

        echo "<table>
            <tr><td>Steps</td><td>Description</td></tr>
            <tr><td>1</td>Pull all Manual Accounts<td></td></tr>
            <tr><td>2</td><td>Insert/Update Portfolios Omniscient</td></tr>
            <tr><td>3</td><td>Get Securities associated with manual accounts</td></tr>
            <tr><td>4</td>Get Positions associated with Manual Accounts<td></td></tr>
            <tr><td>5</td>Get Prices for positions<td></td></tr>
        </table>";

        echo "<strong>START TIME: " . date("m-d-Y H:i:s") . "</strong><br />";
        ob_flush();
        flush();

        $accounts = $port->GetManualAccountsFromPC();
        PortfolioInformation_PCAsCustodian_Model::CreateAndUpdatePortfolios($accounts);//Create the accounts that don't exist, and update the ones that do

        $balances = $port->GetManualBalancesFromPC();
        PortfolioInformation_PCAsCustodian_Model::UpdatePortfolioBalances($balances);

/*
        if($request->get('skippricing'))
            echo "Skipping pricing update<br />\r\n";
        else{
            echo "About to go into pricing update<br />\r\n";
            ob_flush();
            flush();
            $pricing = new cPricingAccess();
            $pricing_result = $pricing->UpdatePrices(null, $price_override);

            echo $pricing_result;
        }

        if(!$override){//We are not overriding, so make sure there are prices for today minus one day
            $price_count = $pricing->GetNumberOfPricesForDate();
            if($price_count == 0) {
                echo "<h2><strong>No new prices found! Add &price_override=1 to the URL if you want to run the full cron anyways</strong></h2><br /><br />";
//                exit;
            }else
                echo "<h2><strong>{$price_count} new prices, carrying on!</strong></h2><br /><br />";
        }

        echo "<strong>Copy portfolios from PC into CRM table (not the module)</strong><br />";
        ob_flush();
        flush();

        $port->CopyPortfoliosFromPCToCRM();

        echo "<strong>Finished copying portfolios into CRM</strong><br />";
        ob_flush();
        flush();

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
        /*        echo "List updated, Pricing calculating....<br />";
                echo "UPDATING PRICES...";
                ModSecurities_SecurityBridge_Model::UpdateAllModSecuritiesPrices();*/
#        echo "<strong>Finished inserting new securities " . date('m-d-Y H:i:s') . "<br />\r\n";
        /*
                echo "<strong>Copying transactions from -1 week to Transactions module " . date('m-d-Y H:i:s') . " DISABLED<br />\r\n";
                ob_flush();
                flush();
                $weekago_date = date("Y-m-d",strtotime("-1 week"));
        //        cTransactionsBridge::CreateTransactionsEntitiesFromPCTransactions($weekago_date);
                echo "Transactions copied to new module.  <strong>Note, prices not yet inserted</strong>";

                echo "Updating security list in the CRM " . date('m-d-Y H:i:s') . "<br />\r\n";
                ob_flush();
                flush();

                $securities->UpdateCRMSecurities();
                echo "Done updating security list " . date('m-d-Y H:i:s') . "<br /><br />\r\n";
        */
/*        echo "<strong>About to run the Asset Allocation Portion (calculating Portfolio Information numbers)</strong> " . date('m-d-Y H:i:s') . "<br />\r\n";
        ob_flush();
        flush();
        $allocations = new PortfolioInformation_CronNew_Action();
        $allocations->process($request);

        echo "<strong>Connecting securities to households and contacts based on their account numbers and values in PortfolioInformation</strong> " . date('m-d-Y H:i:s') . "<br />\r\n";
        ob_flush();
        flush();
        ModSecurities_SecurityBridge_Model::ConnectSecuritiesToHHAndContact();*/
    }
}

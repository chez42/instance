<?php
if (ob_get_level() == 0) ob_start();
class PortfolioInformation_Purge_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        global $adb;
        set_time_limit (0);

        echo "Beginning the Purge Process " . date('m-d-Y H:i:s') . "<br />\r\n";
        ob_flush();
        flush();
        $asset_allocation = new PortfolioInformation_AssetAllocation_Action();
        $asset_allocation->WriteComparisonTable();

/*        $query = "UPDATE vtiger_portfolioinformation p
                  JOIN vtiger_portfolioinformation_current pc ON p.account_number = pc.account_number
                  SET p.total_value = pc.total_value, 
                  p.market_value = pc.market_value, 
                  p.cash_value = pc.cash_value
                  WHERE p.account_number = pc.account_number";*/
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

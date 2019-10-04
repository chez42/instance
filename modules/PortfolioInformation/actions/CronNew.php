<?php
if (ob_get_level() == 0) ob_start();

require_once("libraries/reports/new/nAuditing.php");

/*
require_once("libraries/reports/cTransactions.php");
require_once("libraries/reports/Portfolios.php");
require_once("libraries/reports/editing/TransactionsBridge.php");
require_once("include/utils/cron/cPortfolioAccess.php");
require_once("include/utils/cron/cPricingAccess.php");
require_once("include/utils/cron/cSecuritiesAccess.php");
require_once("include/utils/cron/cAdvisorAccess.php");
*/

class PortfolioInformation_CronNew_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        global $adb;
        set_time_limit (0);
        ini_set('memory_limit', '2048M');
/*
        $asset_allocation = new PortfolioInformation_AssetAllocation_Action();
        $accounts = PortfolioInformation_Module_Model::GetAllActiveAccountNumbers();
//        foreach($accounts AS $a => $account_number){
            $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber('657-402486');
            $asset_allocation->UpdateIndividualAccount($crmid);
//        }
*/
        $audit = new nAuditing();
        $good_accounts = $audit->GetActiveAccountsFromPC();
        $undelete = $audit->GetAccountsToUnDelete($good_accounts);
###        $delete = $audit->GetAccountsToDelete($good_accounts);
          
        if(sizeof($undelete) > 0){
            echo "undeleting...";
            print_r($undelete);
            echo "<br /><br />";
            ob_flush();
            flush();
#            PortfolioInformation_Module_Model::SetAccountAsUnDeleted($undelete);
        }
        
/*        if(sizeof($delete) > 0){
            echo "deleted...";
            print_r($delete);
            echo "<br /><br />";
            ob_flush();
            flush();
//            PortfolioInformation_Module_Model::SetAccountAsDeleted($delete);
        }*/

        $missing = $audit->GetMissingAccountNumbers($good_accounts);
        if(sizeof($missing) > 0){
            echo "missing...";
            print_r($missing);
            echo "<br /><br />";
            exit;
            ob_flush();
            flush();
            $accounts = $audit->GetPortfolioInformationFromPC($missing);
            if(sizeof($accounts) > 0){
                foreach($accounts as $k => $v){
#                    $audit->CreatePortfolioInformationAccount($v);
#                    PortfolioInformation_Module_Model::UpdateInceptionDate($v['AccountNumber']);
                }
            }
        }
        ob_flush();
        flush();

###        PortfolioInformation_Module_model::FindAndFixEmptyInceptionDates();

        $asset_allocation = new PortfolioInformation_AssetAllocation_Action();
        $accounts = PortfolioInformation_Module_Model::GetAllActiveAccountNumbers();
        foreach($accounts AS $a => $account_number){
            $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($account_number);
#            $asset_allocation->UpdateIndividualAccount($crmid);
            echo "Updated {$account_number}<br />";
            ob_flush();
            flush();
        }
        
####        PortfolioInformation_Module_Model::SetAllProductionNumbers();
        PortfolioInformation_GlobalSummary_Model::UpdatePortfolioDailyValues();
        PortfolioInformation_GlobalSummary_Model::UpdatePortfolioDailyIndividualValues();
        
        echo "Portfolio New Cron Finished<br />";
    }
}
?>

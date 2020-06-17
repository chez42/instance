<?php

class Omniscient_ManualInteractions_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        $interaction = new PortfolioInformation_ManualInteractions_Model();
        switch($request->get('todo')){
            case 'CopySecurityCodes':
                $interaction->UpdateSecurityCodes();
                echo "Security Codes Done Copying";
                break;
            case 'ResetAccountTransactions':
                require_once("include/utils/cron/cTransactionsAccess.php");
                $result = $interaction->ResetAccountTransactions($request->get('account_number'));
                echo "Inserted {$result} transactions";
                break;
            case "HistoricalUpdate":
                $historical = new PortfolioInformation_HistoricalUpdate_Action();
                echo "MEMORY USAGE BEGIN: " . memory_get_usage() . "<br />";
                gc_enable();
                $date = $request->get('historical_date');
                if(strlen($date < 10)){
                    echo "Invalid Date";
                }else{
                    $historical->UpdateAllHistoricalAccounts($date);
                }
                gc_collect_cycles();
                break;
            case "AllCalendarSharing":
                $i = new Omniscient_ManualInteractions_Model();
                $i->RunAllCalendarSharing();
                echo "Finished Resetting All Non Admin Users";
                break;
            case "IndividualCalendarSharing":
                $i = new Omniscient_ManualInteractions_Model();
                echo $i->RunIndividualCalendarSharing($request->get('username'));
                break;
        }
    }
}

?>
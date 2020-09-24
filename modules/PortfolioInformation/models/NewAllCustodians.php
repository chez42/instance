<?php
include_once("libraries/javaBridge/JavaCloudToCRM.php");

class PortfolioInformation_NewAllCustodians_Model extends Vtiger_Module{
    private $cloud;

    function SendMail($to, $subject, $message)
    {
        $headers = 'From: cron_job@omnisrv.com' . "\r\n" .
            'Reply-To: no-reply@omnisrv.com' . "\r\n" .
            'Content-Type: text/html; charset=UTF-8' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);
    }

    function JavaCustodianRun($custodian)
    {
        try {
            global $adb;
            $query = "INSERT INTO vtiger_cloud_updates (note, time) VALUES (?, NOW())";
            if ($this->cloud->IsNextStepReady() && !$this->cloud->HasAlreadyRun($custodian, "files_finished")) {
                $date = date("Y-m-d H:i:s");
                $this->cloud->UpdateCustodianCronStatus($custodian, "custodian_start_time", $date);
                $note = "Reading {$custodian} Files Using Java";
                $adb->pquery($query, array($note));
                $this->cloud->UpdateCustodianCronStatus($custodian, "files_started", "1");
                $this->cloud->SetStepStatus("next_step_ready", "0");
                $this->cloud->WriteFiles($custodian, "writefiles", "7", "0");
                $this->cloud->UpdateCustodianCronStatus($custodian, "files_finished", "1");
                $note = "Finished {$custodian} Files Reading Using Java";
                $adb->pquery($query, array($note));
                $this->cloud->SetStepStatus("next_step_ready", "1");
            }

            if($custodian == 'schwab'){
                $this->DetermineSchwabTransactionDupes();
            }

            if ($this->cloud->IsNextStepReady() && !$this->cloud->HasAlreadyRun($custodian, "portfolios_finished")) {
                $note = "Updating {$custodian} Portfolios/Balances Using Java";
                $adb->pquery($query, array($note));
                $this->cloud->UpdateCustodianCronStatus($custodian, "portfolios_started", "1");
                $this->cloud->SetStepStatus("next_step_ready", "0");
                $this->cloud->UpdatePortfolios($custodian, "live_omniscient");
                $this->cloud->UpdateCustodianCronStatus($custodian, "portfolios_finished", "1");
                $note = "Finished {$custodian} Portfolios/Balances Using Java";
                $adb->pquery($query, array($note));
                $this->cloud->SetStepStatus("next_step_ready", "1");
            }

            if ($this->cloud->IsNextStepReady() && !$this->cloud->HasAlreadyRun($custodian, "securities_finished")) {
                $note = "Updating {$custodian} Securities Using Java";
                $adb->pquery($query, array($note));
                $this->cloud->UpdateCustodianCronStatus($custodian, "securities_started", "1");
                $this->cloud->SetStepStatus("next_step_ready", "0");
                $this->cloud->UpdateSecurities($custodian, "live_omniscient");
                $this->cloud->UpdateCustodianCronStatus($custodian, "securities_finished", "1");
                $note = "Finished {$custodian} Securities Using Java";
                $adb->pquery($query, array($note));
                $this->cloud->SetStepStatus("next_step_ready", "1");
            }

            if ($this->cloud->IsNextStepReady() && !$this->cloud->HasAlreadyRun($custodian, "positions_finished")) {
                $note = "Updating {$custodian} Positions Using Java";
                $adb->pquery($query, array($note));
                $this->cloud->UpdateCustodianCronStatus($custodian, "positions_started", "1");
                $this->cloud->SetStepStatus("next_step_ready", "0");
                $this->cloud->UpdatePositions($custodian, "live_omniscient");
                $this->cloud->UpdateCustodianCronStatus($custodian, "positions_finished", "1");
                $note = "Finished {$custodian} Positions Using Java";
                $adb->pquery($query, array($note));
                $this->cloud->SetStepStatus("next_step_ready", "1");
            }

            if ($this->cloud->IsNextStepReady() && !$this->cloud->HasAlreadyRun($custodian, "transactions_finished")) {
                $note = "Updating {$custodian} Transactions Using Java";
                $adb->pquery($query, array($note));
                $this->cloud->UpdateCustodianCronStatus($custodian, "transactions_started", "1");
                $this->cloud->SetStepStatus("next_step_ready", "0");
                $this->cloud->UpdateTransactions($custodian, "live_omniscient");
                $this->cloud->UpdateCustodianCronStatus($custodian, "transactions_finished", "1");
                $note = "Finished {$custodian} Transactions Using Java";
                $adb->pquery($query, array($note));
                $this->cloud->SetStepStatus("next_step_ready", "1");
                $date = date("Y-m-d H:i:s");
                $this->cloud->UpdateCustodianCronStatus($custodian, "custodian_end_time", $date);
            }
        } catch (Exception $e) {
            $this->SendMail("rsandnes@glenmerrybowl.com", "Cron Job Custodian Error", "The cron job failed with an exception: " . $e->getMessage());
            $this->SendMail("felipe.luna@omnisrv.com", "Cron Job Custodian Error", "The cron job failed with an exception: " . $e->getMessage());
        }
    }

    public function RunCron()
    {
        $section = 'About to run custodians';
        $this->cloud = new JavaCloudToCRM("omniscient", "syncuser", "Concert222", "192.168.102.229", "custodian_omniscient");
#        $this->cloud->SetStepStatus("next_step_ready", "1");
/*        $this->JavaCustodianRun("fidelity");
        $this->JavaCustodianRun("schwab");
        $this->JavaCustodianRun("td");
        $this->JavaCustodianRun("pershing");
        $this->JavaCustodianRun("folio");
*/
        try {
            $section = 'About to assign portfolios';
            if ($this->cloud->GetStepStatus("assign_portfolio") == 0) {
                PortfolioInformation_Module_Model::AssignPortfolioBasedOnRepCodes();
                $this->cloud->SetStepStatus("assign_portfolio", "1");
            }

            $section = 'About to link contacts';
            if ($this->cloud->GetStepStatus("link_contacts") == 0) {
                PortfolioInformation_ConvertCustodian_Model::LinkContactsToPortfolios();
                $this->cloud->SetStepStatus("link_contacts", "1");
            }

            $section = 'About to link households';
            if ($this->cloud->GetStepStatus("link_housholds") == 0) {
                PortfolioInformation_ConvertCustodian_Model::LinkHouseholdsToPortfolios();
                $this->cloud->SetStepStatus("link_housholds", "1");
            }

            #PortfolioInformation_ConvertCustodian_Model::CreateContactFromPortfolio();
            #$this->cloud->SetStepStatus("next_step_ready", "1");

            $section = 'About to update contact values and inception dates';
            if ($this->cloud->GetStepStatus("update_contact_values and inception dates") == 0) {
                PortfolioInformation_ManualInteractions_Model::UpdateAllContactTotalValue();
                PortfolioInformation_Module_Model::UpdateOmniInceptionDate();
                $this->cloud->SetStepStatus("update_contact_values and inception dates", "1");
            }

            $section = 'About to update household values';
            if ($this->cloud->GetStepStatus("update_household_values") == 0) {
                PortfolioInformation_ManualInteractions_Model::UpdateAllHouseholdTotalValue();
                $this->cloud->SetStepStatus("update_household_values", "1");
            }

            #Transactions_ConvertCustodian_Model::ReassignTransactions();//Assign transactions that currently belong to admin to the owner of the portfolio they are associated with
            #$this->cloud->SetStepStatus("assign_transactions", "1");
            $section = 'About to assign transactions';
            if ($this->cloud->GetStepStatus("assign_transactions") == 0) {
                Transactions_ConvertCustodian_Model::AssignTransactionsBasedOnPortfolioMinusDays(7);
                $this->cloud->SetStepStatus("assign_transactions", "1");
            }

            $section = 'About to assign positions';
            if ($this->cloud->GetStepStatus("assign_positions") == 0) {
                Transactions_ConvertCustodian_Model::AssignPositionsBasedOnPortfolio();
                $this->cloud->SetStepStatus("assign_positions", "1");
            }

            $section = 'About to update rep codes';
            if ($this->cloud->GetStepStatus("update_rep_codes") == 0) {
                Transactions_ConvertCustodian_Model::UpdateRepCodes();
                $this->cloud->SetStepStatus("update_rep_codes", "1");
            }

            $section = 'About to update YTD Fees';
            if ($this->cloud->GetStepStatus("update_ytd_fees") == 0) {
                PortfolioInformation_Module_Model::UpdateYTDManagementFees();
                $this->cloud->SetStepStatus("update_ytd_fees", "1");
            }

            $section = 'About to update trailing 12 fees';
            if ($this->cloud->GetStepStatus("update_trailing12_fees") == 0) {
                PortfolioInformation_Module_Model::UpdateTrailing12ManagementFees();
                $this->cloud->SetStepStatus("update_trailing12_fees", "1");
            }

            $section = 'About to update contact fees';
            if ($this->cloud->GetStepStatus("update_contact_fees") == 0) {
                PortfolioInformation_Module_Model::UpdateContactFees();
                $this->cloud->SetStepStatus("update_contact_fees", "1");
            }

            $section = 'About to update household fees';
            if ($this->cloud->GetStepStatus("update_household_fees") == 0) {
                PortfolioInformation_Module_Model::UpdateHouseholdFees();
                $this->cloud->SetStepStatus("update_household_fees", "1");
            }

            $section = 'About to transfer consolidated balances';
            if ($this->cloud->GetStepStatus("xfer_consolidated_balances") == 0) {
                $start = date('Y-m-d', strtotime('-7 days'));
                $end = date("Y-m-d");
                PortfolioInformation_HistoricalInformation_Model::TransferConsolidatedBalancesFromCloud($start, $end);
                $this->cloud->SetStepStatus("xfer_consolidated_balances", "1");
                $page = $this->GeneratedSucceededPage();
                $this->SendMail("rsandnes@glenmerrybowl.com", "Cron Finished", $page);
                $this->SendMail("felipe.luna@omnisrv.com", "Cron Finished", $page);

            }
        } catch (Exception $e) {
            $this->SendMail("rsandnes@glenmerrybowl.com", "Cron Job Update Error", "The cron job failed with an exception: " . $e->getMessage() . ".  Section: " . $section);
            $this->SendMail("felipe.luna@omnisrv.com", "Cron Job Update Error", "The cron job failed with an exception: " . $e->getMessage() . ".  Section: " . $section);
        }
    }

    function GeneratedSucceededPage(){
#        $viewer = new Vtiger_Viewer();
        $date = Date("Y-m-d");
        $r = PortfolioInformation_CronJobFiles_Model::GetFilesGreaterEqualThanDate($date);
        $files = PortfolioInformation_CronJobFiles_Model::DetermineMatchColorsForDirectories($r, $date);
#        $viewer->assign("FILES", $files);
#        return $viewer->fetch('layouts/vlayout/modules/PortfolioInformation/CronJobFiles.tpl');
        $script = "";
        include_once('modules/PortfolioInformation/views/SucceededPage.php');
        return $script;
    }

    /*
     * Schwab potentially can have the same transactions coming in from 2 different sources.  The individual rep code, and a
     * master account for example.
     */
    function DetermineSchwabTransactionDupes(){
        global $adb;
        $date = date('Y-m-d',(strtotime ( 'TODAY -10 days' ) ));

        $query = "DROP TABLE IF EXISTS schwab_transactions_valid_ids";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE schwab_transactions_valid_ids (transaction_id BIGINT NOT NULL, account_number VARCHAR(50),
                             PRIMARY KEY(transaction_id), 
                             INDEX(transaction_id))";
        $adb->pquery($query, array());

        $query = "INSERT INTO schwab_transactions_valid_ids
        SELECT transaction_id, account_number FROM custodian_omniscient.custodian_transactions_schwab 
        WHERE (account_number, master_account_number, trade_date) IN(SELECT account_number, MIN(master_account_number) AS master_number, trade_date
                       FROM custodian_omniscient.custodian_transactions_schwab 
                       WHERE trade_date >= ?
                       GROUP BY account_number, trade_date)";
        $adb->pquery($query, array($date));

        $query = "UPDATE custodian_omniscient.custodian_transactions_schwab t
        JOIN schwab_transactions_valid_ids v ON t.transaction_id = v.transaction_id
        SET t.dupe_flag = 2";
        $adb->pquery($query, array());
    }
}
<?php

class PortfolioInformation_ManualInteractions_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        $interaction = new PortfolioInformation_ManualInteractions_Model();
        switch($request->get('todo')){
            case 'UpdateAllInceptionDates':
                PortfolioInformation_Module_Model::UpdateAllPortfolioInceptionDates();
                echo "All Account Inceptions Updated";
                break;
            case 'UpdateAdvisorControlNumber':
                if($interaction->UpdateAdvisorControlNumber($request->get('account_number'))){
                    echo "Control Number Updated";
                    return;
                }
                echo "Error updating control number";
                break;
            case 'UpdateAccountInceptionDate':
                $r = $interaction->UpdateAccountInceptionDate($request->get('account_number'));
                if($r){
                    echo "Inception Date Updated";
                    return;
                }
                echo "Error Updating Inception Date";
                break;
            case 'CopySecurityCodes':
                $interaction->UpdateSecurityCodes();
                $interaction->UpdateCodeDescriptions();
                echo "Security Codes Done Copying";
                break;
            case 'PortfolioInformationNumbersReset':
                $accounts = $interaction->GetAccountNumbersFromControlNumber($request->get('control_number'));
                if($accounts == 0){
                    echo "There was an error with the advisor control number";
                    return;
                }
                $asset_allocation = new PortfolioInformation_AssetAllocation_Action();
                $asset_allocation->WriteComparisonTableWithAccountsArray($accounts);
                echo "Should be all done!";
                break;
            case 'RestAccountTransactionsFromControlNumber':
//                global $bb;
                require_once("include/utils/cron/cTransactionsAccess.php");
                ini_set('max_execution_time', 9000);
                ini_set('memory_limit','2048M');
//                $bb->SendInfo("message", "Max execution set to 9000, memory limit 2048M");
                $accounts = $interaction->GetAccountNumbersFromOmniControlNumber($request->get('control_number'));
                if($accounts == 0){
                    echo "There was an error with the advisor control number";
                    return;
                }
                $transaction_count = 0;
                $failed = array();
#                $asset_allocation = new PortfolioInformation_AssetAllocation_Action();
                foreach($accounts AS $k => $v){
                    $r = $interaction->ResetAccountTransactions($v);
                    PortfolioInformation_Module_Model::SetPCTransactionsTransferredToNo(str_replace("-", '', $v));
                    if(is_numeric($r)){
#                        $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($v);
#                        $asset_allocation->UpdateIndividualAccount($crmid);
//                        $bb->SendInfo("message","Reset: {$v}, Asset Allocation Calculated For {$crmid}");
                        $transaction_count += $r;
                    }
                    else{
                        $failed[] = "{$v} FAILED WITH MESSAGE: {$r}\r\n";
                    }
                }
                $msg = "Transactions Successfully Copied Over: {$transaction_count}\r\n";
                if(sizeof($failed) > 0){
                    foreach($failed AS $k => $v){
                        $msg .= $v;
                    }
                } else{
                    $msg .= " 0 Failed";
                }
                echo $msg;
                break;
            case 'ResetAccountTransactions':
                require_once("include/utils/cron/cTransactionsAccess.php");
                $result = $interaction->ResetAccountTransactions($request->get('account_number'));
###				PositionInformation_Module_Model::UndeleteAllPositionsForAccounts($request->get('account_number'));
				PortfolioInformation_Module_Model::SetPCTransactionsTransferredToNo(str_replace("-", '', $request->get('account_number')));
#                $asset_allocation = new PortfolioInformation_AssetAllocation_Action();
                if(is_numeric($result)) {
#                    $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($request->get('account_number'));
#                    $asset_allocation->UpdateIndividualAccount($crmid);
#					UpdatePositionInformation($request->get('account_number'));
                    echo "Inserted {$result} transactions";
                }else{
                    echo "Error: {$result}";
                }
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
            case 'ControlNumberHistoricalUpdate':
                $ids = $interaction->GetPortfolioInformationIDsFromControlNumber($request->get('control_number'));
                if($ids == 0){
                    echo "There was an error with the advisor control number (Not finding any PortfolioInformation ID's)";
                    return;
                }
                $date = $request->get('date');
                if(strlen($date) < 10){
                    echo "Double check that date... Gotta be spot on";
                    return;
                }
                $historical = new PortfolioInformation_HistoricalUpdate_Action();
                foreach($ids AS $k => $v){
                    $historical->HistoricalUpdateIndividualAccount($v, $date);
                }
                echo "Should be all done.. in theory.. we know how shoulds work";
                break;
            case 'AccountNumberHistoricalUpdate':
                $id = $interaction->GetPortfolioInformationIDFromAccountNumber($request->get('account_number'));
                if($id == 0){
                    echo "There was an error with the account number (Not finding any PortfolioInformation ID)";
                    return;
                }
                $date = $request->get('date');
                if(strlen($date) < 10){
                    echo "Double check that date... Gotta be spot on";
                    return;
                }
                $historical = new PortfolioInformation_AssetAllocation_Action();
                $historical->HistoricalUpdateIndividualAccount($id, $date);
                echo "Should be all done.. in theory.. we know how shoulds work";
                break;
            case 'ResetPortfolioTransactions':
                require_once("include/utils/cron/cTransactionsAccess.php");
                $result = $interaction->ResetPortfolioTransactions($request->get('portfolio_id'));
                if($result)
                    echo "Inserted {$result} transactions";
                else
                    echo "No transactions to insert";
                break;
            case 'CopyPositions':
                $date = $request->get('date');
                $symbol = $request->get('symbol');
                if(strlen($symbol) > 3)
                    $date = "2000-01-01";
                if(!$date)
                    $date = '2015-04-07';
                $result = $interaction->UpdatePositions($date);
                echo "Positions Updated From {$date}";
                break;
            case 'TotalAnnihilation':
                echo $interaction->TotalAnnihilation($request->get('account_number'));
                break;
            case "PullIndividualSecurity":
                $list = ModSecurities_SecurityBridge_Model::PullSecurity($request->get('symbol'));
                ModSecurities_SecurityBridge_Model::WriteListToModSecurities($list);
                ModSecurities_SecurityBridge_Model::UpdateModSecuritiesSecurityIDWhenEmpty();
                ModSecurities_SecurityBridge_Model::UpdateEmptySecurityTypes();
                echo $request->get('symbol') . ' added';
                break;
            case "CopyIndividualSecurityFromPC":
                require_once("include/utils/cron/cSecuritiesAccess.php");
                $symbol = $request->get('symbol');
                $securities = new cSecuritiesAccess();
                $r = $securities->UpdateCRMSecuritiesBySymbol($symbol);
                if(!$r)
                    echo "No security found";
                else
                    echo "{$symbol} Inserted/Updated";
                break;
            case "CopyCurrentPortfolioInformation":
                global $adb;
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
                break;
            case "PullAllSecurities":
                require_once("include/utils/cron/cSecuritiesAccess.php");
                require_once("include/utils/cron/cPricingAccess.php");

                $securities = new cSecuritiesAccess();
                $not_in_list = $securities->GetSecuritiesNotInList($securities->GetAllSecuritiesFromPC());
/*                $query = "INSERT INTO MissingSecurities (id) VALUES (?) ON DUPLICATE KEY update id=VALUES(id)";
                global $adb;
                foreach($not_in_list AS $k => $v){
                    $adb->pquery($query, array($v));
                }
                print_r($not_in_list);exit;*/
                $pricing = new cPricingAccess();
                foreach($not_in_list AS $k => $v){
                    $securities->UpdateCRMSecuritiesByID($v);
                    $pricing->UpdateSecurityPriceBySecurityID($v);
                }
/*
                $security_name = $request->get('security_name');
                $pricing = new cPricingAccess();
                $pricing_result = $pricing->PullSecurityPrice($security_name);

//                print_r($list);
/*                ini_set('max_execution_time', 300);
                $list = ModSecurities_SecurityBridge_Model::PullAllSecurities();
                ModSecurities_SecurityBridge_Model::WriteListToModSecurities($list);
                ModSecurities_SecurityBridge_Model::UpdateModSecuritiesSecurityIDWhenEmpty();
                ModSecurities_SecurityBridge_Model::UpdateEmptySecurityTypes();*/
                echo "Security Pull And Pricing Updated";
                break;
            case "UpdateAllPrices":
                ModSecurities_SecurityBridge_Model::UpdateAllModSecuritiesPrices();
                echo "Done!";
                break;
            case "UpdateIndividualPrice":
                $symbol = $request->get('symbol');
                $security_id = ModSecurities_Module_Model::GetSecurityIdBySymbol($symbol);
                ModSecurities_SecurityBridge_Model::UpdateIndividualModSecurityPrice($security_id);
                echo $symbol . " Updated";
                break;
            case "AutoCloseAccounts":
                include_once("include/utils/cron/cPortfolioAccess.php");
                include_once("include/utils/cron/cClosePortfolios.php");
                $close = new cPortfolioAccess();
                $close->CloseAccounts();
                $del = new cClosePortfolios();
                $accounts = $del->GetClosedCRMAccounts();
                foreach($accounts AS $k => $v){
                    $del->SetPortfolioInformationAsDeleted($v);
                    $del->SetPositionInformationAsDeleted($v);
                    $del->RemoveAccountFromSummaryTable($v);
                }
                echo "Accounts Closed";
                break;
            case "CalculateAssetAllocation":
                $asset_allocation = new PortfolioInformation_AssetAllocation_Action();
                $accounts = PortfolioInformation_Module_Model::GetAllActiveAccountNumbers();
                foreach($accounts AS $a => $account_number){
                    $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($account_number);
                    $asset_allocation->UpdateIndividualAccount($crmid);
                }
                echo "All Asset Allocations Written";
                break;
            case "SSNReset":
                $account_number = $request->get('account_number');
                include_once("libraries/reports/new/nAuditing.php");
                $audit = new nAuditing();
                $info = $audit->GetPortfolioInformationFromPC(array($account_number));
                if($info != 0){
                    $tax_id = $info[0]['TaxID'];
                    PortfolioInformation_Module_Model::UpdatePortfolioTableSSNFromAccountNumber($tax_id, $account_number);
                    PortfolioInformation_Module_Model::SetAccountTaxID($account_number, $tax_id);
#                    $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($account_number);
#                    $asset_allocation = new PortfolioInformation_AssetAllocation_Action();
#                    $asset_allocation->UpdateIndividualAccount($crmid);
                    echo 'SSN Reset Complete';
                }else{
                    echo "Account Number returned a 0 result";
                }
                break;
            case "CalculateAccountAssetAllocation":
                $account_number = $request->get('account_number');
                $asset_allocation = new PortfolioInformation_AssetAllocation_Action();
                $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($account_number);
                $asset_allocation->UpdateIndividualAccount($crmid);
                echo "PortfolioInformation Updated";
                break;
            case "PullSecurityPrices":
                require_once("include/utils/cron/cPricingAccess.php");
                $security_name = $request->get('security_name');
                $pricing = new cPricingAccess();    
                $pricing_result = $pricing->PullSecurityPrice($security_name);
                echo $pricing_result;
                break;
            case "InsertSecurityPrice":
                require_once("include/utils/cron/cPricingAccess.php");
                require_once("include/utils/cron/cSecuritiesAccess.php");
                $info = cSecuritiesAccess::GetSecurityIDsBySymbol($request->get('security_name'));
                if($info){
                    $dataset = $info[0]['security_data_set_id'];
                    $security_id = $info[0]['security_id'];
                    $date = $request->get('date');
                    $price = $request->get('price');
                    cPricingAccess::CreateCustomPrice($security_id, $dataset, $price, $date);
                    echo $request->get('security_name') . " price updated";
                }else{
                    echo "No security found";
                }
                break;
            case "PullPrices":
                require_once("include/utils/cron/cPricingAccess.php");
                $pricing = new cPricingAccess();
                $pricing_result = $pricing->UpdatePrices();
                echo $pricing_result;
                break;
            case "RemoveBadPortfolios":
                require_once("include/utils/cron/cPortfolioAccess.php");
                $p = new cPortfolioAccess();
				$p->EnableAndDisablePortfoliosToMatchPC();
//                $ids = $p->MatchPCAndRemoveFromCRM();
                echo "Check the bad_portfolios table";
                break;
            case "ClientContacts":
                SetIsClientContacts($request->get('reset'));
                echo "Client Contacts Set";
                break;
            case "ClientHouseholds":
                SetIsClientHouseholds($request->get('reset'));
                echo "Client Households Set";
                break;
            case "SMAAccountDescription":
                $interaction->UpdateSMAAccountDescription();
                echo "SMA Account Descriptions Set";
                break;
            case "RemoveUndefinedSecurityType":
                //First insert new mod securities
                $list = ModSecurities_SecurityBridge_Model::PullAllSecurities();
                ModSecurities_SecurityBridge_Model::WriteListToModSecurities($list);
                ModSecurities_SecurityBridge_Model::UpdateModSecuritiesSecurityIDWhenEmpty();
                ModSecurities_SecurityBridge_Model::UpdateEmptySecurityTypes();
                echo 'Go confirm the magic';
                break;
            case "FixNullInceptions":
                PortfolioInformation_Module_Model::FindAndFixEmptyInceptionDates();
                echo "Von Schmigle";
                break;
        }
    }
}

?>
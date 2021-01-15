<?php

require_once("libraries/custodians/cCustodian.php");
require_once('modules/ModSecurities/actions/ConvertCustodian.php');
include_once("include/utils/omniscientCustom.php");
include_once("libraries/statusupdates/StatusUpdate.php");

class cFidelityUpdater{
    public function __construct(array $readyData){
        switch($readyData['ready_type']){
            case 1:
/*                $fidelity = new cSchwabPortfolios("SCHWAB", "custodian_omniscient", "portfolios",
                    "custodian_portfolios_fidelity", "custodian_balances_fidelity", array());
                $data = $fidelity->GetExistingCRMAccounts();//Get accounts already in the CRM
                $missing = $fidelity->GetMissingCRMAccounts();//Compare CRM accounts to Custodian accounts and return what the CRM doesn't have
                $fidelity->CreateNewPortfoliosFromPortfolioData($missing);//Create the accounts that are missing into the CRM
                $existing = $fidelity->GetExistingCRMAccounts();//Get accounts already in the CRM
                $fidelity->UpdatePortfoliosFromPortfolioData($existing);

                if(!PortfolioInformation_Module_Model::DoesAccountExist($readyData['account_number'])) {//The account doesn't exist
                    $fidelity->CreateNewPortfoliosFromPortfolioData(array($readyData['account_number']));//create account
                }
                $fidelity->UpdatePortfoliosFromPortfolioData(array($readyData['account_number']));//Update the account*/
                break;
            case 2:
                break;
            case 3:
                break;
        }
    }

    public function UpdateAll(){
        include("cron/modules/Custodian/DataPull.service");
    }
}
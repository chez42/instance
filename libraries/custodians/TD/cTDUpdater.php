<?php
class cTDUpdater{
    public function __construct(array $readyData){
        switch($readyData['ready_type']){
            case 1:
                $td = new cTDPortfolios("TD", "custodian_omniscient", "portfolios",
                    "custodian_portfolios_td", "custodian_balances_td", array());
                if(!PortfolioInformation_Module_Model::DoesAccountExist($readyData['account_number'])) {//The account doesn't exist
                    $td->CreateNewPortfoliosFromPortfolioData(array($readyData['account_number']));//Create the account
                }
                $td->UpdatePortfoliosFromPortfolioData(array($readyData['account_number']));//Update the account
            break;
            case 2:
                break;
            case 3:
                break;
        }
    }
}
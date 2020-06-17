<?php
include_once("libraries/reports/new/nAccount.php");

class nCombinedAccounts
{
    private $accounts = array();
    private $asset_pie = array();
    private $trailing_12_revenue = array();
    private $trailing_12_aum = array();
    
    public function AddAccount(nAccount $nAccount){
        $this->accounts[] = $nAccount;
        
    }

    /**
     * Returns account numbers in array format that have been added to the class
     */
    public function GetAccountNumbers(){
        $accounts = array();
        foreach($this->accounts AS $index => $account){
            $accounts[] = $account->number;
        }
        return $accounts;
    }
    
    /**
     * Return the trailing 12 aum chart
     * @return type
     */
    public function GetTrailing12AUM(){
        return $this->trailing_12_aum;
    }
    
    /**
     * Return the trailing 12 revenue chart
     * @return type
     */
    public function GetTrailing12Revenue(){
        return $this->trailing_12_revenue;
    }    
    
    /**
     * Return the asset pie data
     * @return type
     */
    public function GetAssetPie(){
        return $this->asset_pie;
    }
    
    public function CombineTrailing12Revenue(){
        $accounts = $this->GetAccountNumbers();
        $this->trailing_12_revenue = PortfolioInformation_HistoricalInformation_Model::GetTrailing12Revenue($accounts);
    }
    
    /**
     * Add the pie charts of all known accounts together
     */
    public function CombineAssetPie(){
        $accounts = $this->GetAccountNumbers();
        $this->asset_pie = PortfolioInformation_HistoricalInformation_Model::GetAssetPie($accounts);
    }
    
    public function CombineTrailing12AUM(){
        $accounts = $this->GetAccountNumbers();
        $this->trailing_12_aum = PortfolioInformation_HistoricalInformation_Model::GetTrailing12AUM($accounts);
    }
}

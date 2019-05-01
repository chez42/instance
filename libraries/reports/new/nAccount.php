<?php
include_once("libraries/reports/new/nPosition.php");

class nAccount{
    public $number, $name, $nickname;
    private $positions = array();
    private $asset_pie = array();
    private $trailing_12_revenue = array();
    private $trailing_12_aum = array();
    
    public function __construct($account_number) {
        $this->number = $account_number;
        $this->SetNickname();
    }
    /**
     * Set the account nickname
     * @global type $adb
     * @param type $override_nickname
     * @return type
     */
    private function SetNickname($override_nickname = ''){
        global $adb;
        if(strlen($override_nickname) > 0){
            $this->nickname = $override_nickname;
            return;
        }
        
        $query = "SELECT nickname FROM vtiger_pc_account_custom WHERE account_number = ?";
        $result = $adb->pquery($query, array($this->number));
        if($adb->num_rows($result) > 0)
            $this->nickname = $adb->query_result($result, 0, 'nickname');
        else
            $this->nickname = '';        
    }
    
    public function LoadPositions(){
        
    }
    
    public function GetNickname(){
        return $this->nickname;
    }
    
    public function SetAssetPie(){
        $this->asset_pie = PortfolioInformation_HistoricalInformation_Model::GetAssetPie($this->number);
    }
    
    public function GetAssetPie(){
        return $this->asset_pie;
    }
    
    public function SetTrailing12Revenue(){
        $this->trailing_12_revenue = PortfolioInformation_HistoricalInformation_Model::GetTrailing12Revenue($this->number);
    }
    
    public function GetTrailing12Revenue(){
        return $this->trailing_12_revenue;
    }
    
    public function SetTrailing12AUM(){
        $this->trailing_12_aum = PortfolioInformation_HistoricalInformation_Model::GetTrailing12AUM($this->number);
    }
    
    public function GetTrailing12AUM(){
        return $this->trailing_12_aum;
    }

}
?>

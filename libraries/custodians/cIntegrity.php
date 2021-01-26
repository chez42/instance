<?php

class cIntegrity{
    private $differences, $tdAccounts, $fidelityAccounts, $schwabAccounts, $pershingAccounts;

    public function __construct(array $account){
        if(empty($account)) {
            return;
            #$differences = $this->GetPortfolioToPositionDifferencesList();
        }
        else {
            $differences = $this->GetPortfolioToPositionDifferencesListForAccounts($account);
        }

        $this->differences = $differences;//Save a copy of differences for outside use if necessary
        foreach($differences AS $k => $v) {
            switch(strtoupper($v['origination'])){
                case "TD":
                    $this->tdAccounts[] = $v['account_number'];
                    break;
                case "FIDELITY":
                    $this->fidelityAccounts[] = $v['account_number'];
                    break;
                case "SCHWAB":
                    $this->schwabAccounts[] = $v['account_number'];
                    break;
                case "PERSHING":
                    $this->pershingAccounts[] = $v['account_number'];
                    break;
            }
        }
    }

    public function GetDifferences(){
        return $this->differences;
    }

    private function FixTD($start, $end){
        PortfolioInformation_Module_Model::TDBalanceCalculationsMultiple($this->tdAccounts, $start, $end);
        $copy = new CustodianToOmniTransfer($this->tdAccounts);
        $copy->UpdatePortfolios();
        $copy->CreateSecurities();
        $copy->CreatePositions();

        PortfolioInformation_Module_Model::ConsolidatedBalances($this->tdAccounts, $start, $end);
    }

    private function FixFidelity($start, $end){
        $copy = new CustodianToOmniTransfer($this->fidelityAccounts);
        $copy->UpdatePortfolios();
        $copy->CreateSecurities();
        $copy->CreatePositions();
        $copy->CreateTransactions();

#        PortfolioInformation_Module_Model::ConsolidatedBalances($this->fidelityAccounts, $start, $end);
    }

    private function FixSchwab($start, $end){
        $copy = new CustodianToOmniTransfer($this->schwabAccounts);
        $copy->UpdatePortfolios();
        $copy->CreateSecurities();
        $copy->CreatePositions();

#        PortfolioInformation_Module_Model::ConsolidatedBalances($this->fidelityAccounts, $start, $end);
    }

    public function RepairDifferences(){
        $end = date('Y-m-d');
        $start = date('Y-m-d', strtotime('-4 days'));
        foreach($this->differences AS $k => $v) {
            switch(strtoupper($v['origination'])){
                case "TD":
                    $this->FixTD($start, $end);
                    break;
                case "FIDELITY":
                    $this->FixFidelity($start, $end);
                    break;
                case "SCHWAB":
                    $this->FixSchwab($start, $end);
                    break;
                case "PERSHING":
                    break;
            }
        }
    }

    public function GetPortfolioToPositionDifferencesListForAccounts(array $account){
        global $adb;
        $questions = generateQuestionMarks($account);

        $query = "SELECT p.account_number, CASE WHEN p.total_value IS NULL THEN 0 ELSE p.total_value END AS total_value, 
                                           CASE WHEN SUM(pos.current_value) IS NULL THEN 0 ELSE SUM(pos.current_value) END AS PositionValue, 
                                           p.origination, CASE WHEN p.total_value IS NULL THEN 0 ELSE p.total_value END - CASE WHEN SUM(pos.current_value) IS NULL THEN 0 ELSE SUM(pos.current_value) END AS dif
                  FROM vtiger_portfolioinformation p
                  JOIN vtiger_positioninformation pos USING (account_number)
                  WHERE p.account_number IN ({$questions})
                  GROUP BY p.account_number";
        $result = $adb->pquery($query, array($account));

        if($adb->num_rows($result )> 0){
            $differences = array();
            while($v = $adb->fetchByAssoc($result)){
                $dif = abs($v['positionvalue']) - abs($v['total_value']);
                $latest_balance = PortfolioInformation_Module_Model::GetLatestBalanceForAccount($v['account_number']);
                if( $dif > 10 || ((int)$latest_balance == (int)$v['positionvalue'])){
                    $differences[] = $v;
                }
            }
            return $differences;
        }
        return null;
    }

    public function GetPortfolioToPositionDifferencesList(){
        global $adb;

        $query = "SELECT p.account_number, CASE WHEN p.total_value IS NULL THEN 0 ELSE p.total_value END AS total_value, 
                                           CASE WHEN SUM(pos.current_value) IS NULL THEN 0 ELSE SUM(pos.current_value) END AS PositionValue, 
                                           p.origination, CASE WHEN p.total_value IS NULL THEN 0 ELSE p.total_value END - CASE WHEN SUM(pos.current_value) IS NULL THEN 0 ELSE SUM(pos.current_value) END AS dif
                  FROM vtiger_portfolioinformation p
                  JOIN vtiger_positioninformation pos USING (account_number)
                  GROUP BY p.account_number";
        $result = $adb->pquery($query, array());

        if($adb->num_rows($result )> 0){
            $differences = array();
            while($v = $adb->fetchByAssoc($result)){
                $dif = abs($v['positionvalue']) - abs($v['total_value']);
                if( $dif > 10){
                    $differences[] = $v;
                }
            }
            return $differences;
        }
        return null;
    }
}
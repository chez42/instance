<?php
class cReady{
    public function __construct(){
    }

    public function GetReadyDataViaRepCode(array $rep_code){
        if(empty($rep_code))
            return;

        global $adb;
        $data = array();
        $params = array();
        $params[] = $rep_code;

        $questions = generateQuestionMarks($rep_code);
        $query = "SELECT rep_code, account_number, ready_type, custodian, timestamp
                  FROM custodian_omniscient.readyforupdate
                  WHERE rep_code IN ({$questions})";
        $result = $adb->pquery($query, $params, true);
        if($adb->num_rows($result) > 0){
            while($row = $adb->fetchByAssoc($result)){
                $data[] = $row;
            }
            return $data;
        }else{
            return 0;
        }
    }

    public function GetReadyDataViaAccountNumber(array $account_number){
        if(empty($account_number))
            return;

        global $adb;
        $questions = generateQuestionMarks($account_number);
        $query = "SELECT rep_code, account_number, ready_type 
                  FROM custodian_omniscient.readyforupdate
                  WHERE account_number IN ({$questions})";
        $result = $adb->pquery($query, array($account_number), true);
        if($adb->num_rows($result) > 0){
            while($row = $adb->fetchByAssoc($result)){
                $data[] = $row;
            }
            return $data;
        }else{
            return 0;
        }
    }

    public function PullDataTD($account_number, $readyType){
        $tdPortfolios = new cTDPortfolios("TD", "custodian_omniscient", "portfolios",
                                          "custodian_portfolios_td", "custodian_balances_td", array());
        $tdPortfolios->SetAccountNumbers(array($account_number));

        print_r($tdPortfolios);exit;
        if(!PortfolioInformation_Module_Model::DoesAccountExist($account_number)) {//The account doesn't exist
            $tdPortfolios->CreateNewPortfoliosFromPortfolioData(array($account_number));//Create the account
        }

        switch(readyType){
            case 1://Portfolios
                $tdPortfolios->UpdatePortfoliosFromPortfolioData($account_number);//Update the existing accounts with the latest data from the custodian
        }
    }
}
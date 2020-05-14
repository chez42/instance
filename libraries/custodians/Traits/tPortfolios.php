<?php
trait tPortfolios{
    protected $custodian_accounts, $existing_accounts, $missing_accounts;

    /**
     * Sets up the variables for determining which accounts exist with the custodian, which exist in the CRM, and which are missing
     */
    protected function SetupPortfolioComparisons(){
        global $adb;
        $params = array();
        $custodian_accounts = array();//List of custodian accounts
        $existing_accounts = array();//Accounts that exist in the crm
        foreach($this->portfolio_data AS $k => $v){
            $custodian_accounts[] = $k;
        }
        if(!empty($custodian_accounts)){
            $questions = generateQuestionMarks($custodian_accounts);
            $params[] = $custodian_accounts;
            $query = "SELECT account_number FROM vtiger_portfolioinformation WHERE account_number IN ({$questions})";
            $result = $adb->pquery($query, $params);
            if($adb->num_rows($result) > 0){
                while ($r = $adb->fetchByAssoc($result)) {
                    $existing_accounts[] = $r['account_number'];
                }
            }
        }
        $this->custodian_accounts = $custodian_accounts;
        $this->existing_accounts = $existing_accounts;
        $this->missing_accounts = array_diff($this->custodian_accounts, $this->existing_accounts);
    }

    /**
     * Returns the list of custodian accounts
     * @return mixed
     */
    public function GetCustodianAccounts(){
        return $this->custodian_accounts;
    }

    /**
     * Returns a list of accounts in the CRM that currently exist compared to the custodian accounts
     * @return mixed
     */
    public function GetExistingCRMAccounts(){
        return $this->existing_accounts;
    }

    /**
     * Returns a list of accounts that exist in the custodian table, but not in the CRM
     * @return mixed
     */
    public function GetMissingCRMAccounts(){
        return $this->missing_accounts;
    }

}
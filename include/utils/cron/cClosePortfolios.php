<?php
require_once("include/utils/cron/cPortfolioCenter.php");

class cClosePortfolios extends cPortfolioCenter{

    /**
     * Remove all deleted accounts from the summary tables using PC data
     * @global type $adb
     */
    public function RemoveFromSummaryTablesPC(){
        $closed = $this->GetClosedAccounts();
        global $adb;
        $query = "DELETE FROM account_other_accounts_pdf
                  WHERE primary_account = ?
                  OR account_number = ?";
        $q2 = "DELETE FROM vtiger_portfolio_summary
               WHERE account_number = ?";
        foreach($closed AS $k => $v){
            $adb->pquery($query, array($v['AccountNumber'], $v['AccountNumber']));
            $adb->pquery($q2, array($v['AccountNumber']));
        }
    }
    
    /**
     * Remove all deleted accounts from the vtiger_portfolios table using PC data
     * @global type $adb
     */
    public function RemoveFromPortfoliosTablePC(){
        $closed = $this->GetClosedAccounts();
        global $adb;
        $query = "DELETE FROM vtiger_portfolios
                  WHERE portfolio_account_number = ?";
        foreach($closed AS $k => $v){
            $adb->pquery($query, array($v['AccountNumber']));
        }
    }
    
    /**
     * Set entity deleted for Portfolio Information module using PC data
     * @global type $adb
     */
    public function RemoveFromPortfolioInformationPC(){
        $closed = $this->GetClosedAccounts();
        global $adb;
        $query = "UPDATE vtiger_crmentity SET deleted = 1
                  WHERE crmid = (SELECT portfolioinformationid FROM vtiger_portfolioinformation WHERE account_number=?)";
        foreach($closed AS $k => $v){
            $adb->pquery($query, array($v['AccountNumber']));
        }
    }

    /**
     * Returns an array of all accounts set as closed in the CRM's vtiger_portfolios table
     */
    static public function GetClosedCRMAccounts(){
        global $adb;
        $query = "SELECT portfolio_account_number FROM vtiger_portfolios WHERE account_closed = 1";
        $result = $adb->pquery($query, array());
        $accounts = array();
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $accounts[] = $v['portfolio_account_number'];
}
        }
        return $accounts;
    }
    
    /**
     * Sets the PortfolioInformation entity as deleted
     * @global type $adb
     * @param type $account_number
     */
    public function SetPortfolioInformationAsDeleted($account_number){
        global $adb;
        $query = "UPDATE vtiger_crmentity SET deleted = 1 WHERE crmid IN (SELECT portfolioinformationid FROM vtiger_portfolioinformation WHERE account_number=?)";
        $adb->pquery($query, array($account_number));
    }
    
    public function SetPositionInformationAsDeleted($account_number){
        global $adb;
        $query = "UPDATE vtiger_crmentity SET deleted = 1 WHERE crmid IN (SELECT positioninformationid FROM vtiger_positioninformation WHERE account_number=?)";
        $adb->pquery($query, array($account_number));
    }
    
    /**
     * Remove all deleted accounts from the summary tables using PC data
     * @global type $adb
     */
    public function RemoveAccountFromSummaryTable($account_number){
        global $adb;
        $query = "DELETE FROM account_other_accounts_pdf
                  WHERE primary_account = ?
                  OR account_number = ?";
        $q2 = "DELETE FROM vtiger_portfolio_summary
               WHERE account_number = ?";
        $adb->pquery($query, array($account_number, $account_number));
        $adb->pquery($q2, array($account_number));
        $query = "DELETE FROM vtiger_position_summary WHERE account_number=?";
        $adb->pquery($query, array($account_number));
        $query = "DELETE FROM vtiger_portfolio_summary WHERE account_number=?";
        $adb->pquery($query, array($account_number));
    }
}
?>
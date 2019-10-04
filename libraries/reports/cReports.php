<?php
include_once("include/utils/omniscientCustom.php");
include_once("libraries/reports/cTransactions.php");
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of cReports
 *
 * @author theshado
 */
class cReports {
    public $transactions;
    public $module_name;
    public $pids = array();
	public $ssn;
    
    /**
     * Fills in the portfolio ID's based on the source (contacts, household, account_number)
     * @param type $source
     */
    public function __construct($source = null, $record=null) {
        $this->transactions = new cTransactions();
        switch($source){
            case "contacts":
                $this->module = "Contacts";
                $this->pids =  GetPortfolioIDsFromContactID($record);
				$contact = Contacts_Record_Model::getInstanceById($record);
				$this->ssn = $contact->get('ssn');
                break;
            case "household":
                $this->module = "Accounts";
                $this->pids = GetPortfolioIDsFromHHID($record);
                $this->ssn = GetSSNsForHousehold($record);
                break;
            case "account_number":
                $module = "PortfolioInformation";
                $this->pids = GetPortfolioIDsFromPortfolioAccountNumbers($record);
                break;                
        }
    }
    
    public function __destruct() {
        ;
    }
    
    /**
     * Return a comma separated list of portfolio ids
     * @return type
     */
    public function GetCSVPids(){
        $pids = SeparateArrayWithCommas($this->pids);
        return $pids;
    }
    
    /**
     * Get the array of portfolio id's
     * @return type
     */
    public function GetPortfolioIds(){
        return $this->pids;
    }
    
    public function GetPortfolioIdsFromAccountNumber($account_numbers){
        return GetPortfolioIDsFromPortfolioAccountNumbers($account_numbers);
    }
}

?>

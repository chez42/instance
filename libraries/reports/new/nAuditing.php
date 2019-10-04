<?php

require_once('include/utils/cron/cPortfolioCenter.php');

class nAuditing extends cPortfolioCenter{
    private $datasets;
    
    public function __construct($server = "lanserver2n", $user = "syncuser", $pass = "Consec11", $db = "PortfolioCenter") {
        parent::__construct($server, $user, $pass, $db);
        $this->datasets = cPortfolioCenter::GetDatasets();//"1, 28";
    }
    
    /**
     * Get all active account numbers from PC
     */
    public function GetActiveAccountsFromPC($manual_only = true){
        if($manual_only)
            $and = " AND p.AccountNumber LIKE ('M%') ";
        $query = "SELECT p.AccountNumber
                  FROM PortfolioCenter.dbo.Portfolios p 
                  WHERE p.DataSetID IN ({$this->datasets}) AND PortfolioTypeID = 16 AND ClosedAccountFlag=0 AND AccountNumber != '' {$and}";
        if(!$this->connect())//Try connecting
            return "Error Connecting to PC";
        
	$result = mssql_query($query);
        $info = array();//Holds all row info
        if(mssql_num_rows($result) > 0)
            while($row = mssql_fetch_array($result))
                $info[] = $row['AccountNumber'];
        return $info;
    }
    
    /**
     * Get portfolios from PC based on passed in account numbers
     * @param type $account_numbers
     */
    public function GetPortfolioInformationFromPC($account_numbers){
        if(!$this->connect())
            return "Error Connecting to PC";
        
        $query = "SELECT PortfolioID, FirstName, LastName, DataSetID, CompanyName, TaxID, AccountNumber, AdvisorID FROM PortfolioCenter.dbo.Portfolios p WHERE AccountNumber IN (";
//        $result = mssql_query($query);
        foreach($account_numbers AS $k => $v){
            $account_number = mysql_real_escape_string($v);
            $account_number = str_replace("-", "-", $account_number);
            $values[] = "'{$account_number}'";
        }
        $query .= implode(',', $values);
        $query .= ")";
        $result = mssql_query($query);
        $info = array();
        if(mssql_num_rows($result) > 0){
            while($row = mssql_fetch_array($result))
                $info[] = $row;
            return $info;
        }
        return 0;
    }
    
    public function GetAccountCloseDateFromPC($account_number){
        
    }

    public function GetContactIDByTaxID($tax_id){
        if(!$tax_id)
            return 0;
        global $adb;
        $query = "SELECT contactid FROM vtiger_contactscf WHERE ssn = ?";
        $result = $adb->pquery($query, array($tax_id));
        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, 'contactid');
        }
        return 0;
    }
    
    public function GetHouseholdByContactID($contact_id){
        if(!$contact_id)
            return 0;
        global $adb;
        $query = "SELECT accountid FROM vtiger_contactdetails WHERE contactid = ?";
        $result = $adb->pquery($query, array($contact_id));
        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, 'accountid');
        }
        return 0;
    }
    
    public function CreatePortfolioInformationAccount($account_info, $manual = true){
        $t = Vtiger_Record_Model::getCleanInstance("PortfolioInformation");
        $data = $t->getData();

        if($manual)
            $data['origination'] = 'Manual';
        $data['first_name'] = $account_info['FirstName'];
        $data['last_name'] = $account_info['LastName'];
        $data['account_number'] = $account_info['AccountNumber'];
        $data['advisor_id'] = $account_info['AdvisorID'];
        $data['tax_id'] = $account_info['TaxID'];
        $data['contact_link'] = $this->GetContactIDByTaxID($account_info['TaxID']);
//        $data['household_account'] = $this->GetHouseholdByContactID($data['contact_link']);
        if($data['contact_link']){
            $contact = Contacts_Record_Model::getInstanceById($data['contact_link']);
            $d = $contact->getData();
            $data['household_account'] = $d['account_id'];
            $data['assigned_user_id'] = $d['assigned_user_id'];
        }else{
            $data['assigned_user_id'] = 1;
            $data['household_account'] = 0;
        }
//        print_r($data);
//        echo "<br /><br />";
//        exit;
#		PortfolioInformation_Module_Model::
        $t->setData($data);
        $t->save();
//        exit;
//        $t = null;
    }
    
    /**
     * Return a list of accounts that are set to deleted in the CRM but are not deleted in PC
     * @global type $adb
     * @param type $good_pc_accounts
     * @return type
     */
    public function GetAccountsToUnDelete($good_pc_accounts){
        global $adb;
        $questions = generateQuestionMarks($good_pc_accounts);
        $query = "SELECT account_number
                  FROM vtiger_portfolioinformation p
                  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
                  WHERE e.deleted = 1 AND p.account_number IN ({$questions})";
        $result = $adb->pquery($query, array($good_pc_accounts));
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $accounts[] = $v['account_number'];
            }
        }
        return $accounts;
    }
    
    /**
     * Get a list of accounts that we are showing as active that should be set as deleted
     * @global type $adb
     * @param type $good_pc_accounts
     */
    public function GetAccountsToDelete($good_pc_accounts){
        global $adb;
        $questions = generateQuestionMarks($good_pc_accounts);
        $query = "SELECT account_number
                  FROM vtiger_portfolioinformation p
                  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
                  WHERE e.deleted = 0 AND p.account_number NOT IN ({$questions})";
        $result = $adb->pquery($query, array($good_pc_accounts));
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $accounts[] = $v['account_number'];
            }
        }
        return $accounts;
    }
    
    /**
     * This returns a list of account numbers that are dupes (exist in both data set 1 and 28 for example)
     * @global type $adb
     * @param type $good_pc_accounts
     */
    public function GetDuplicateAccountNumbersInPC($good_pc_accounts){
        global $adb;
        $query = "SELECT valid_account_list.account_number AS account_number, count(valid_account_list.account_number) as occurrences FROM valid_account_list
                  INNER JOIN (SELECT account_number FROM valid_account_list
                  GROUP BY account_number HAVING count(account_number) > 1) dup ON valid_account_list.account_number = dup.account_number
                  GROUP BY valid_account_list.account_number
                  ORDER BY valid_account_list.account_number";
        $result = $adb->pquery($query, array());
        $dupes = array();
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $dupes[] = $v;
            }
        }
        return $dupes;
    }
    
    /**
     * Returns a list of missing account numbers
     * @global type $adb
     * @param type $good_pc_accounts
     * @return type
     */
    public function GetMissingAccountNumbers($good_pc_accounts){
        global $adb;
        $query = "drop table if exists valid_account_list";
        $adb->pquery($query, array());
        $query = "create table valid_account_list (account_number varchar(25) unique)";
        $adb->pquery($query, array());
        $query = "INSERT INTO valid_account_list (account_number) VALUES ";
        $values = array();
        foreach($good_pc_accounts AS $k => $v){
            $account_number = mysql_real_escape_string($v);
            $values[] = "('{$account_number}')";
        }
        $query .= implode(',', $values);
        $query .= " ON DUPLICATE KEY UPDATE account_number = VALUES(account_number)";
        
        $adb->pquery($query, array());
        $query = "SELECT account_number 
                  FROM valid_account_list 
                  WHERE account_number NOT IN (SELECT p.account_number 
                                               FROM vtiger_portfolioinformation p
                                               JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
                                               WHERE e.deleted = 0)";

        $result = $adb->pquery($query, array());
        $missing = array();
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $missing[] = $v['account_number'];
            }
        }
        return $missing;
    }
    
}
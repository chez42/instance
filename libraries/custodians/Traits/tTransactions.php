<?php
trait tTransactions{
    protected $custodian_transactions, $existing_transactions, $missing_transactions;

    /**
     * Sets up the variables for determining which transactions exist with the custodian, which exist in the CRM, and which are missing
     */
    protected function SetupTransactionComparisons(){
        global $adb;
        $params = array();
        $custodian_transactions = array();//List of custodian accounts
        $existing_transactions = array();//Accounts that exist in the crm

        foreach($this->transactions_data AS $k => $v){
            $transactions = array();
            foreach($v AS $info){
                $transactions[] = $info['transaction_id'];
            }
            $custodian_transactions[$k] = $transactions;
            $tmp_accounts[] = $k;
        }

        if(!empty($tmp_accounts)){
            $questions = generateQuestionMarks($tmp_accounts);
            $params[] = $tmp_accounts;
            $query = "SELECT account_number, cloud_transaction_id FROM vtiger_transactions WHERE account_number IN ({$questions})";
            $result = $adb->pquery($query, $params);
            if($adb->num_rows($result) > 0){
                while ($r = $adb->fetchByAssoc($result)) {
                    $existing_transactions[$r['account_number']][] = $r['cloud_transaction_id'];
                }
            }
        }
        $this->custodian_transactions = $custodian_transactions;
        $this->existing_transactions = $existing_transactions;
        $this->missing_transactions = array();

        foreach($tmp_accounts AS $k => $v){
            if(empty($this->existing_transactions[$v]))//If we don't do this, the array_diff below fails because transactions[$x] doesn't exist, this creates it
                $this->existing_transactions[$v] = array();
            $tmp = array_diff($this->custodian_transactions[$v], $this->existing_transactions[$v]);
            if(!empty($tmp)) {
                $this->missing_transactions[$v] = $tmp;//Missing transactions now holds any transaction id's we don't have that the custodian does
            }
        }
    }

    /**
     * Returns the list of custodian transactions
     * @return mixed
     */
    public function GetCustodianTransactions(){
        return $this->custodian_transactions;
    }

    /**
     * Returns a list of transactions in the CRM that currently exist compared to the custodian transactions
     * @return mixed
     */
    public function GetExistingCRMTransactions(){
        return $this->existing_transactions;
    }

    /**
     * Returns a list of transactions that exist in the custodian table, but not in the CRM
     * @return mixed
     */
    public function GetMissingCRMTransactions(){
        return $this->missing_transactions;
    }

    /**
     * Get all cloud transaction ID's for given custodian
     * @param array|null $account_number
     * @return array|null
     */
    public function GetCloudTransactionIDs(array $account_number = null, $custodian){
        global $adb;
        $params = array();
        $params[] = $custodian;
        $where = " WHERE origination = ? ";
        if(!empty($account_number)){
            $questions = generateQuestionMarks($account_number);
            $where .= " AND account_number IN (?) ";
            $params[] = $account_number;
        }

        $query = "SELECT cloud_transaction_id 
                  FROM vtiger_transactions
                  {$where}";
        $result = $adb->pquery($query, $params, true);
        if($adb->num_rows($result) > 0){
            $ids = array();
            while ($r = $adb->fetchByAssoc($result)) {
                $ids[] = $r['cloud_transaction_id'];
            }
            return $ids;
        }
        return null;
    }

    /**
     * Return the owner ID for the passed in account number
     * @param string $account_number
     * @return int|string|string[]|null
     * @throws Exception
     */
    public function GetAccountOwnerFromAccountNumber(string $account_number){
        global $adb;
        $query = "SELECT smownerid 
                  FROM vtiger_crmentity e 
                  JOIN vtiger_portfolioinformation p ON p.portfolioinformationid = e.crmid
                  WHERE p.account_number = ?";
        $result = $adb->pquery($query, array($account_number));
        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0,"smownerid");
        }
        return 1;
    }


    public function GetTransactionsymbolsFromDate(array $account_numbers, $date){
        global $adb;
        $questions = generateQuestionMarks($account_numbers);
        $query = "SELECT symbol FROM {$this->database}.{$this->table}
                  WHERE account_number IN ({$questions}) AND date = ? 
                  GROUP BY symbol";
        $result = $adb->pquery($query, array($account_numbers, $date));
        $symbols = array();
        if($adb->num_rows($result) > 0){
            while ($r = $adb->fetchByAssoc($result)) {
                $symbols[] = $r['symbol'];
            }
        }
        return $symbols;
    }

    /**
     * Returns all transaction custodian ID's
     * @param array $account_numbers
     */
    public function GetAllOldAndNewTransactionsymbols(array $account_numbers){
        global $adb;
        $questions = generateQuestionMarks($account_numbers);
        $query = "SELECT cloud_transaction_id FROM {$this->database}.{$this->table} 
                  WHERE account_number IN ({$questions}) 
                  GROUP BY symbol";
        $result = $adb->pquery($query, array($account_numbers));
        $symbols = array();
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $symbols[strtoupper(TRIM($v['symbol']))] = $v['symbol'];
            }
        }

#        $symbols = $this->GetRemappedSymbols($symbols);
        return $symbols;
    }

    /**
     * Determine if the transaction exists in the CRM already
     * @param $account_number
     * @param $symbol
     * @return bool
     */
    public function DoesTransactionExistInCRM($custodian_transaction_id){
        global $adb;
        $query = "SELECT account_number 
                  FROM vtiger_transactions 
                  WHERE cloud_transaction_id = ?";
        $result = $adb->pquery($query, array($custodian_transaction_id));
        if($adb->num_rows($result) > 0)
            return true;
        return false;
    }
    /*
    public function GetCustodianTransactionIDsOnly($custodian){
        global $adb;
        $query = "SELECT cloud_transaction_id
                  FROM vtiger_transactions
                  WHERE origination = ?";
        $result = $adb->pquery($query, array($custodian));
        $ids = array();
        if($adb->num_rows($result) > 0){
            while($r = $adb->fetchByAssoc($result)){
                $ids[] = $r['cloud_transaction_id'];
            }
        }
        return $ids;
    }*/
}
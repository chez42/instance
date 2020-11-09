<?php
trait tPositions{
    protected $custodian_positions, $existing_positions, $missing_positions;

    /**
     * Sets up the variables for determining which positions exist with the custodian, which exist in the CRM, and which are missing
     */
    protected function SetupPositionComparisons(){
        global $adb;
        $params = array();
        $custodian_positions = array();//List of custodian positions
        $existing_positions = array();//Positions that exist in the crm
        $tmp_accounts = array();//An array of just account numbers for querying with
        foreach($this->positions_data AS $k => $v){
            $symbols = array();
            foreach($v AS $a => $symbol_info){
                $symbols[] = $symbol_info['symbol'];
            }
            $custodian_positions[$k] = $symbols;//Gets a list of account numbers with a list of their positions
            $tmp_accounts[] = $k;
        }
        if(!empty($tmp_accounts)){
            $questions = generateQuestionMarks($tmp_accounts);
            $params[] = $tmp_accounts;
            $query = "SELECT account_number, TRIM(security_symbol) AS security_symbol FROM vtiger_positioninformation WHERE account_number IN ({$questions})";
            $result = $adb->pquery($query, $params);
            if($adb->num_rows($result) > 0){
                while ($r = $adb->fetchByAssoc($result)) {//When pulling our symbols, make them compatible with what the custodian table has
                    if(array_key_exists($r['security_symbol'], $this->symbol_replacements))//If we have TDCASH for example, match it to 'Cash' on the custodian
                        $existing_positions[trim($r['account_number'])][] = $this->symbol_replacements[$r['security_symbol']];
                    else
                        $existing_positions[trim($r['account_number'])][] = $r['security_symbol'];
                }
            }
        }
        $this->custodian_positions = $custodian_positions;
        $this->existing_positions = $existing_positions;

        foreach($tmp_accounts AS $k => $v){
            if(empty($this->existing_positions[$v]))//If we don't do this, the array_diff below fails because positions[$x] doesn't exist, this creates it
                $this->existing_positions[$v] = array();

            $tmp = array_udiff($this->custodian_positions[$v], $this->existing_positions[$v], 'strcasecmp');

            if(!empty($tmp)) {
                $this->missing_positions[$v] = $tmp;//Missing positions now holds any symbols we don't have that the custodian does
            }
        }
    }

    /**
     * Returns the list of custodian positions
     * @return mixed
     */
    public function GetCustodianPositions(){
        return $this->custodian_positions;
    }

    /**
     * Returns a list of positions in the CRM that currently exist compared to the custodian positions
     * @return mixed
     */
    public function GetExistingCRMPositions(){
        return $this->existing_positions;
    }

    /**
     * Returns a list of positions that exist in the custodian table, but not in the CRM
     * @return mixed
     */
    public function GetMissingCRMPositions(){
        return $this->missing_positions;
    }

    /**
     * Determine if the position exists in the CRM already
     * @param $account_number
     * @param $symbol
     * @return bool
     */
    public function DoesPositionExistInCRM(string $account_number, string $symbol){
        global $adb;
        $query = "SELECT account_number 
                  FROM vtiger_positioninformation 
                  WHERE account_number = ? AND security_symbol = ?";
        $result = $adb->pquery($query, array($account_number, $symbol));
        if($adb->num_rows($result) > 0)
            return true;
        return false;
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

    /**
     * Sets the position quantity and value to 0 for passed in account numbers
     * @param array $account_numbers
     */
    public function ClearPositionDataForAccounts(array $account_numbers){
        global $adb;
        $questions = generateQuestionMarks($account_numbers);
        $query = "UPDATE vtiger_positioninformation p 
                  SET p.quantity = 0, p.current_value = 0
                  WHERE account_number IN ({$questions})";
        $adb->pquery($query, array($account_numbers));
    }

    public function GetPositionSymbolsFromDate(array $account_numbers, $date){
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
     * Returns all securities for
     * @param array $account_numbers
     */
    public function GetAllOldAndNewPositionSymbols(array $account_numbers){
        global $adb;
        $questions = generateQuestionMarks($account_numbers);
        $query = "SELECT TRIM(symbol) AS symbol FROM {$this->database}.{$this->table} 
                  WHERE account_number IN ({$questions}) 
                  GROUP BY symbol";
        $result = $adb->pquery($query, array($account_numbers));
        $symbols = array();
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $symbols[strtoupper(TRIM($v['symbol']))] = TRIM($v['symbol']);
            }
        }

        $symbols = $this->GetRemappedSymbols($symbols);
        return $symbols;
    }
}
<?php

spl_autoload_register(function ($className) {
    if (file_exists("libraries/custodians/TD/$className.php")) {
        include_once "libraries/custodians/TD/$className.php";
    }elseif (file_exists("libraries/custodians/Schwab/$className.php")){
        include_once "libraries/custodians/Schwab/$className.php";
    }elseif (file_exists("libraries/custodians/Pershing/$className.php")){
        include_once "libraries/custodians/Pershing/$className.php";
    }elseif (file_exists("libraries/custodians/Fidelity/$className.php")){
        include_once "libraries/custodians/Fidelity/$className.php";
    }
});

class cCustodian{
    protected $custodian_name, $module, $database, $rep_codes, $account_numbers, $table, $portfolio_table, $columns;

/*
    public function __construct($custodian_name = "TD", $database = "custodian_omniscient", $module = "transactions",
                                $table = "custodian_transactions_td", $portfolio_table="custodian_portfolios_td"){
*/
    /**
     * The table parameter refers to the "main table" that will join with the portfolios table when necessary.  It is the table to retrieve data from
     * cCustodian constructor.
     * @param string $custodian_name
     * @param string $database
     * @param string $module
     * @param string $portfolio_table
     * @param string $table (REFERS TO THE MAIN TABLE)
     * @param array $columns (The columns we want returned)
     */
    public function __construct(string $custodian_name, string $database, string $module,
                                string $portfolio_table, string $table, array $columns){
        $this->name = $custodian_name;
        $this->database = $database;
        $this->module = $module;
        $this->portfolio_table = $portfolio_table;
        $this->table = $table;
        $this->columns = $columns;
    }

    /**
     * Set the rep codes list
     * @param array $rep_codes
     */
    public function SetRepCodes(array $rep_codes){
        $this->rep_codes = $rep_codes;
        $this->FillAccountNumbersFromRepCodes();
    }

    public function GetAccountNumbers(){
        return $this->account_numbers;
    }

    public function SetAccountNumbers(array $account_numbers){
        $this->account_numbers = $account_numbers;
    }

    public function SetColumns(array $columns){
        $this->columns = $columns;
    }

    protected function FillAccountNumbersFromRepCodes(){
        global $adb;
        $params = array();
        $questions = generateQuestionMarks($this->rep_codes);
        $params[] = $this->rep_codes;

        $query = "SELECT account_number 
                  FROM {$this->database}.{$this->portfolio_table} 
                  WHERE rep_code IN ({$questions})
                  ORDER BY file_date DESC";
        $result = $adb->pquery($query, array($this->rep_codes), true);
        if($adb->num_rows($result) > 0)
            while($r = $adb->fetchByAssoc($result)){
                $this->account_numbers[] = $r;
            }
    }

    /**
     * Checks if rep codes have been set or not
     * @return bool
     */
    protected function HaveRepcodesBeenDefined(){
        if(empty($this->rep_codes))
            return false;
        return true;
    }

    /**
     * Get the latest balance date for the provided rep codes.  This is a MAX date for all passed in and only returns the highest date
     * between them.
     * @param string $portfolio_table
     * @param string $balance_table
     * @param string $as_of_field
     */
    public function GetLatestBalanceDate($as_of_field="as_of_date"){
        global $adb;
        $params = array();
        $questions = generateQuestionMarks($this->account_numbers);
        $params[] = $this->account_numbers;

        $query = "SELECT MAX({$as_of_field}) AS date 
                  FROM {$this->database}.{$this->table} 
                  WHERE account_number IN ({$questions})";
        $result = $adb->pquery($query, array($this->account_numbers), true);
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'date');

        return 0;
    }

    /**
     * Get the latest positions date for the provided rep codes.  This is a MAX date for all passed in and only returns the highest date
     * between them.
     * @param string $as_of_field
     */
    public function GetLatestPositionsDate($as_of_field="date"){
        global $adb;
        $params = array();
        $accounts = array_slice($this->account_numbers, 0, 50);
        $questions = generateQuestionMarks($accounts);
        $params[] = $accounts;

        $query = "SELECT MAX({$as_of_field}) AS date 
                  FROM {$this->database}.{$this->table} 
                  WHERE account_number IN ({$questions})";
        $result = $adb->pquery($query, $params, true);

        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'date');

        return 0;
    }

    /**
     * Get the latest transactions date for the provided rep codes.  This is a MAX date for all passed in and only returns the highest date
     * between them.
     * @param string $as_of_field
     */
    public function GetLatestTransactionsDate($trade_field="trade_date"){
        global $adb;
        $params = array();
        $questions = generateQuestionMarks($this->account_numbers);
        $params[] = $this->account_numbers;

        $query = "SELECT MAX({$trade_field}) AS date 
                  FROM {$this->database}.{$this->table} 
                  WHERE account_number IN ({$questions})";
        $result = $adb->pquery($query, $params, true);

        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'date');

        return 0;
    }
}
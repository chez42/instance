<?php

spl_autoload_register(function ($className) {
    if (file_exists("libraries/custodians/TD/$className.php")) {
        include_once "libraries/custodians/TD/$className.php";
    }elseif (file_exists("libraries/custodians/SCHWAB/$className.php")){
        include_once "libraries/custodians/SCHWAB/$className.php";
    }elseif (file_exists("libraries/custodians/PERSHING/$className.php")){
        include_once "libraries/custodians/PERSHING/$className.php";
    }elseif (file_exists("libraries/custodians/FIDELITY/$className.php")){
        include_once "libraries/custodians/FIDELITY/$className.php";
    }
});

class cCustodian{
    protected $name, $module, $database, $rep_codes;
    public function __construct(){
    }

    /**
     * Set the rep codes list
     * @param array $rep_codes
     */
    public function SetRepCodes(array $rep_codes){
        $this->rep_codes = $rep_codes;
    }

    /**
     * Checks if rep codes have been set or not
     * @return bool
     */
    private function HaveRepcodesBeenDefined(){
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
    public function GetLatestBalanceDate($portfolio_table="custodian_portfolios_td", $balance_table="custodian_balances_td", $as_of_field="as_of_date"){
        global $adb;
        $params = array();
        $questions = generateQuestionMarks($this->rep_codes);
        $params[] = $this->rep_codes;

        $query = "SELECT MAX(as_of_date) AS date 
                  FROM {$this->database}.{$balance_table} 
                  WHERE account_number IN (SELECT account_number 
			                               FROM {$this->database}.{$portfolio_table}
			                               WHERE rep_code IN ({$questions}))";
        $result = $adb->pquery($query, array($this->rep_codes));
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'date');

        return 0;
    }


}
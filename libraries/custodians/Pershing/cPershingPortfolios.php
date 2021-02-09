<?php
require_once("libraries/custodians/cCustodian.php");

/**
 * Class cPershingPortfolios
 * This class allows the pulling of data from the custodian database
 */
class cPershingPortfolios extends cCustodian {
    private $portfolio_data;//Holds both personal and balance information

    /**
     * cPershingPortfolios constructor.
     * @param string $custodian_name
     * @param string $database
     * @param string $module
     * @param string $portfolio_table
     * @param string $table (REFERS TO BALANCE TABLE)
     */
    public function __construct(string $custodian_name, string $database, string $module,
                                string $portfolio_table, string $balance_table, array $rep_codes){
        $this->name = $custodian_name;
        $this->database = $database;
        $this->module = $module;
        $this->portfolio_table = $portfolio_table;
        $this->table = $balance_table;
        if(!empty($rep_codes)) {
            $this->SetRepCodes($rep_codes);
            $this->GetPortfolioPersonalData();
            $this->GetPortfolioBalanceData();
        }
    }

    public function GetPortfolioPersonalData(){
        global $adb;
        $params = array();
        $questions = generateQuestionMarks($this->account_numbers);
        $params[] = $this->account_numbers;

        if(empty($this->columns))
            $fields = "*";
        else{
            $fields = implode ( ", ", $this->columns );
        }

        $query = "SELECT {$fields} FROM {$this->database}.{$this->portfolio_table} WHERE account_number IN ({$questions})";
        $result = $adb->pquery($query, $params, true);

        if($adb->num_rows($result) > 0){
            while($r = $adb->fetchByAssoc($result)){
                $this->portfolio_data[$r['account_number']]['personal'] = $r;
            }
        }
        return $this->portfolio_data;
    }

    public function GetPortfolioBalanceData($date=null){
        global $adb;
        $params = array();
        $questions = generateQuestionMarks($this->account_numbers);
        $params[] = $this->account_numbers;

        if(empty($this->columns))
            $fields = "*";
        else{
            $fields = implode ( ", ", $this->columns );
        }

        if(!$date)
            $date = $this->GetLatestBalanceDate("date");

        $params[] = $date;
        $query = "SELECT {$fields} FROM {$this->database}.{$this->table} 
                  WHERE account_number IN ({$questions}) AND date = ?";
        $result = $adb->pquery($query, $params, true);

        if($adb->num_rows($result) > 0){
            while($r = $adb->fetchByAssoc($result)){
                $this->portfolio_data[$r['account_number']]['balance'] = $r;
            }
        }
        return $this->portfolio_data;
    }

    public function GetPortfolioData(){
        return $this->portfolio_data;
    }

    static public function GetLatestBalance($account_number){
        global $adb;
        $query = "SELECT net_worth
                  FROM custodian_omniscient.custodian_balances_pershing 
                  WHERE account_number = ?
                  ORDER BY as_of_date 
                  DESC LIMIT 1";
        $result = $adb->pquery($query, array($account_number));

        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, 'net_worth');
        }
        return null;
    }

    static public function BalanceBetweenDates(array $account_number, $sdate, $edate){
        global $adb;
        $questions = generateQuestionMarks($account_number);
        $params = array();
        $params[] = $account_number;
        $params[] = $sdate;
        $params[] = $edate;

        $query = "SELECT account_number, net_worth AS value, as_of_date AS date
                  FROM custodian_omniscient.custodian_balances_fidelity 
                  WHERE account_number IN ({$questions}) 
                  AND as_of_date BETWEEN ? AND ?
                  ORDER BY as_of_date";
        $result = $adb->pquery($query, $params);

        $data = array();
        if($adb->num_rows($result) > 0){
            while($r = $adb->fetchByAssoc($result)){
                $data[$r['account_number']][] = $r;
            }
        }
        return $data;
    }

}
<?php
require_once("libraries/custodians/cCustodian.php");

/**
 * Class cTDPortfolios
 * This class allows the pulling of data from the custodian database
 */
class cTDPortfolios extends cCustodian {
    private $portfolio_table, $balance_table;
    private $portfolio_data;//Holds both personal and balance information

    public function __construct($name = "TD", $database = "custodian_omniscient", $module = "portfolios",
                                $portfolio_table="custodian_portfolios_td", $balance_table="custodian_balances_td"){
        $this->name = $name;
        $this->database = $database;
        $this->module = $module;
        $this->portfolio_table = $portfolio_table;
        $this->balance_table = $balance_table;
    }

    public function CalculatePortfolioPersonalData(array $columns, $table="custodian_portfolios_td"){
        global $adb;
        $params = array();
        $questions = generateQuestionMarks($this->rep_codes);
        $params[] = $this->rep_codes;

        if(empty($columns))
            $fields = "*";
        else{
            $fields = "'" . implode ( "', '", $columns ) . "'";
        }

        $query = "SELECT {$fields} FROM {$this->database}.{$table} WHERE rep_code IN ({$questions})";
        $result = $adb->pquery($query, $params, true);

        if($adb->num_rows($result) > 0){
            while($r = $adb->fetchByAssoc($result)){
                $this->portfolio_data[$r['account_number']]['personal'] = $r;
            }
        }
        return $this->portfolio_data;
    }

    public function CalculatePortfolioBalanceData(array $columns, $table = "custodian_balances_td", $date=null){
        global $adb;
        $params = array();
        $questions = generateQuestionMarks($this->rep_codes);
        $params[] = $this->rep_codes;

        if(empty($columns))
            $fields = "*";
        else{
            $fields = "'" . implode ( "', '", $columns ) . "'";
        }

        if(!$date)
            $date = $this->GetLatestBalanceDate($this->portfolio_table, $this->balance_table, "as_of_date");

        $params[] = $date;
        $query = "SELECT {$fields} FROM {$this->database}.{$table} 
                  JOIN {$this->database}.{$this->portfolio_table} USING (account_number)
                  WHERE rep_code IN ({$questions}) AND as_of_date = ?";
        $result = $adb->pquery($query, $params, true);

        if($adb->num_rows($result) > 0){
            while($r = $adb->fetchByAssoc($result)){
                $this->portfolio_data[$r['account_number']]['balance'] = $r;
            }
        }
        return $this->portfolio_data;
    }

    public function GetPortfolioAndBalanceData(){
        $this->CalculatePortfolioPersonalData(null, $this->balance_table, null);
        $this->CalculatePortfolioBalanceData(null, $this->balance_table, null);
        return $this->portfolio_data;
    }

    public function GetPortfolioData(){
        return $this->portfolio_data;
    }

}
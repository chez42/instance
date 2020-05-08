<?php
require_once("libraries/custodians/cCustodian.php");

/**
 * Class cTDPortfolios
 * This class allows the pulling of data from the custodian database
 */
class cTDPortfolios extends cCustodian {
    private $portfolio_data;//Holds both personal and balance information

    /**
     * cTDPortfolios constructor.
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
            $date = $this->GetLatestBalanceDate("as_of_date");

        $params[] = $date;
        $query = "SELECT {$fields} FROM {$this->database}.{$this->table} 
                  WHERE account_number IN ({$questions}) AND as_of_date = ?";
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

}
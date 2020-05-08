<?php
require_once("libraries/custodians/cCustodian.php");

/**
 * Class cTDPortfolios
 * This class allows the pulling of data from the custodian database
 */
class cTDPositions extends cCustodian {
    private $positions_data;//Holds both personal and balance information

    /**
     * cTDPortfolios constructor.
     * @param string $custodian_name
     * @param string $database
     * @param string $module
     * @param string $positions_table
     * @param string $table (REFERS TO BALANCE TABLE)
     */
    public function __construct(string $custodian_name, string $database, string $module,
                                string $portfolio_table, string $positions_table, array $rep_codes){
        $this->name = $custodian_name;
        $this->database = $database;
        $this->module = $module;
        $this->portfolio_table = $portfolio_table;
        $this->table = $positions_table;
        if(!empty($rep_codes)) {
            $this->SetRepCodes($rep_codes);
        }
    }

    /**
     * Returns an associative array of all accounts and their positions
     * @param string $table
     * @param null $date
     * @return mixed
     */
    public function GetPositionsData($date=null){
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
            $date = $this->GetLatestPositionsDate("date");
        $params[] = $date;

        $query = "SELECT {$fields} FROM {$this->database}.{$this->table} 
                  WHERE account_number IN ({$questions}) AND date = ?";
        $result = $adb->pquery($query, $params, true);

        if($adb->num_rows($result) > 0){
            while($r = $adb->fetchByAssoc($result)){
                $this->positions_data[$r['account_number']][] = $r;
            }
        }
        return $this->positions_data;
    }

    public function GetSavedPositionsData(){
        return $this->positions_data;
    }
}
<?php

require_once("libraries/custodians/cCustodian.php");

/**
 * Class cTDPortfolios
 * This class allows the pulling of data from the custodian database
 */
class cTDTransactions extends cCustodian
{
    protected $transactions_data;//Holds the pricing information

    /**
     * cTDPortfolios constructor.
     * @param string $custodian_name
     * @param string $database
     * @param string $module
     * @param string $transactions_table
     * @param string $table (REFERS TO BALANCE TABLE)
     */
    public function __construct(string $custodian_name, string $database, string $module,
                                string $portfolio_table, string $transactions_table, array $rep_codes){
        $this->name = $custodian_name;
        $this->database = $database;
        $this->module = $module;
        $this->portfolio_table = $portfolio_table;
        $this->table = $transactions_table;
        if(!empty($rep_codes)) {
            $this->SetRepCodes($rep_codes);
        }
    }
    /**
     * Returns an associative array of all requested transactions as of the given date
     * @param null $date
     * @return mixed
     */
    public function GetTransactionsDataForDate($date=null){
        global $adb;
        $params = array();
        $questions = generateQuestionMarks($this->account_numbers);
        $params[] = $this->account_numbers;

        if (empty($this->columns))
            $fields = "*";
        else {
            $fields = implode ( ", ", $this->columns );
        }

        if(!$date)
            $date = $this->GetLatestTransactionsDate("trade_date");
        $params[] = $date;

        $query = "SELECT {$fields} FROM {$this->database}.{$this->table} t
                  WHERE account_number IN ({$questions}) AND trade_date = ?";
        $result = $adb->pquery($query, $params, true);

        if ($adb->num_rows($result) > 0) {
            while ($r = $adb->fetchByAssoc($result)) {
                $this->transactions_data[$r['account_number']] = $r;
            }
        }

        return $this->transactions_data;
    }

    /**
     * Returns an associative array of all requested transactions between the given dates
     * @param null start
     * @param null end
     * @return mixed
     */
    public function GetTransactionsDataBetweenDates($start, $end){
        global $adb;
        $params = array();
        $questions = generateQuestionMarks($this->account_numbers);
        $params[] = $this->account_numbers;
        $params[] = $start;
        $params[] = $end;

        if (empty($this->columns))
            $fields = "*";
        else {
            $fields = implode ( ", ", $this->columns );
        }

        $query = "SELECT {$fields} FROM {$this->database}.{$this->table} 
                  WHERE account_number IN ({$questions}) AND trade_date BETWEEN ? AND ?";
        $result = $adb->pquery($query, $params, true);

        if ($adb->num_rows($result) > 0) {
            while ($r = $adb->fetchByAssoc($result)) {
                $this->transactions_data[$r['account_number']][] = $r;
            }
        }
        return $this->transactions_data;
    }

    /**
     * Returns the transactions_data variable that was filled in from the last retrieve
     * @return mixed
     */
    public function GetSavedTransactionsData(){
        return $this->transactions_data;
    }
}
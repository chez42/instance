<?php
require_once("libraries/custodians/cCustodian.php");

/**
 * Class cFidelityPortfolios
 * This class allows the pulling of data from the custodian database
 */
class cFidelitySecurities extends cCustodian {
    private $securities_data;//Holds the security information

    public function __construct($name = "Fidelity", $database = "custodian_omniscient", $module = "securities",
                                $securities_table="custodian_securities_fidelity"){
        $this->name = $name;
        $this->database = $database;
        $this->module = $module;
        $this->table = $securities_table;
    }

    /**
     * Returns an associative array of all requested securities
     * @param string $table
     * @param null $date
     * @return mixed
     */
    public function RetrieveSecuritiesData(array $symbols){
        global $adb;
        $params = array();
        $where = "";

        if(empty($this->columns))
            $fields = "*";
        else{
            $fields = implode ( ", ", $this->columns );
        }

        if(!empty($symbols)){
            $symbol_q = generateQuestionMarks($symbols);
            $where .= " WHERE symbol IN ({$symbol_q}) ";
            $params[] = $symbols;
        }

        $query = "SELECT {$fields} FROM {$this->database}.{$this->table} 
                  {$where} ";
        $result = $adb->pquery($query, $params, true);

        if($adb->num_rows($result) > 0){
            while($r = $adb->fetchByAssoc($result)){
                $this->securities_data[$r['symbol']] = $r;
            }
        }
        return $this->securities_data;
    }

    public function GetSecuritiesData($symbols){
        return $this->RetrieveSecuritiesData($symbols);
    }
}
<?php

require_once("libraries/custodians/cCustodian.php");

/**
 * Class cTDPortfolios
 * This class allows the pulling of data from the custodian database
 */
class cTDPrices extends cCustodian
{
    private $prices_data;//Holds the pricing information

    /**
     * Returns an associative array of all requested prices as of the given date
     * @param string $table
     * @param null $date
     * @return mixed
     */
    public function GetPricesDataForDate(string $date, array $symbols)
    {
        if(empty($symbols))
            return 0;

        global $adb;
        $params = array();
        $params[] = $date;

        if (empty($this->columns))
            $fields = "*";
        else {
            $fields = implode ( ", ", $this->columns );
        }

        $symbol_q = generateQuestionMarks($symbols);
        $params[] = $symbols;

        $query = "SELECT {$fields} FROM {$this->database}.{$this->table} 
                  WHERE price_date = ? AND symbol IN ({$symbol_q})";
        $result = $adb->pquery($query, $params, true);

        if ($adb->num_rows($result) > 0) {
            while ($r = $adb->fetchByAssoc($result)) {
                $this->prices_data[$r['symbol']] = $r;
            }
        }

        return $this->prices_data;
    }

    /**
     * Returns an associative array of all requested prices between the given dates
     * @param string $table
     * @param null $date
     * @return mixed
     */
    public function GetPricesDataBetweenDates(array $symbols, $start, $end)
    {
        if(empty($symbols))
            return 0;

        global $adb;
        $params = array();
        $params[] = $start;
        $params[] = $end;

        if (empty($this->columns))
            $fields = "*";
        else {
            $fields = implode ( ", ", $this->columns );
        }

        $symbol_q = generateQuestionMarks($symbols);
        $params[] = $symbols;

        $query = "SELECT {$fields} FROM {$this->database}.{$this->table} 
                  WHERE price_date BETWEEN ? AND ? AND symbol IN ({$symbol_q})";
        $result = $adb->pquery($query, $params, true);

        if ($adb->num_rows($result) > 0) {
            while ($r = $adb->fetchByAssoc($result)) {
                $this->prices_data[$r['symbol']][] = $r;
            }
        }
        return $this->prices_data;
    }

    /**
     * Returns the prices_data variable that was filled in from the last retrieve
     * @return mixed
     */
    public function GetSavedPricingData(){
        return $this->prices_data;
    }

    static public function GetBestKnownPriceBeforeDate($symbol, $date){
        global $adb;

        $query = "SELECT price 
                  FROM custodian_omniscient.custodian_prices_td 
                  WHERE date < ?
                  AND symbol = ?
                  ORDER BY date
                  DESC LIMIT 1";
        $result = $adb->pquery($query, array($date, $symbol), true);

        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, 'price');
        }
        return false;
    }

    static public function GetPriceAsOfDate($symbol, $date){
        global $adb;

        $query = "SELECT price 
                  FROM custodian_omniscient.custodian_prices_td 
                  WHERE date = ?
                  AND symbol = ?
                  ORDER BY date
                  DESC LIMIT 1";
        $result = $adb->pquery($query, array($date, $symbol), true);

        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, 'price');
        }
        return false;
    }
}
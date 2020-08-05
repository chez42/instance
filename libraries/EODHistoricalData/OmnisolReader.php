<?php

class OmnisolReader{
    static public function DoesFieldDataExist($field_name, $table_name, $value){
        global $adb;
        $query = "SELECT {$field_name} FROM {$table_name} WHERE {$field_name} = ?";
        $result = $adb->pquery($query, array($value));
        if($adb->num_rows($result) > 0){
            return true;
        }
        return false;
    }

    /**
     * Determine Security Type given to us by EOD.  This can be used to figure out which API we should call from
     * @param $symbol
     * @return string|string[]|null
     * @throws Exception
     */
    static public function DetermineSecurityTypeGivenByEOD($symbol, $country=null){
        global $adb;
        $params = array();
        $params[] = $symbol;
        if($country){
            $and = " AND country_name = ? ";
            $params[] = $country;
        }
        $query = "SELECT st.type AS type
                  FROM omnisol.securities s
                  JOIN omnisol.security_type st ON s.security_type_id = st.id
                  JOIN omnisol.security_country sc ON sc.id = s.country_id
                  WHERE s.symbol = ? {$and}";
        $result = $adb->pquery($query, $params, true);
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'type');
        return null;
    }

    /**
     * Pass in a list of symbols cared about with the type expected.  For example:  Passing in AGG, AAPL, MSFT with type ETF will return
     * AGG only, as that is an ETF.  AAPL/MSFT are of type "Common Stock"
     * @param $input_symbols
     * @param $type
     */
    static public function MatchSymbolsOfSecurityType(array $input_symbols, $type, $exchange=null, $country=null){
        global $adb;
        $questions = generateQuestionMarks($input_symbols);

        $params = array();
        $params[] = $type;

        $join_q = $exchange_q = "";
        $and = "";

        if($exchange) {
            $params[] = $exchange;
            $join_q .= " JOIN omnisol.security_exchange se ON se.id = s.exchange_id ";
            $and .= " AND se.code = ? ";
        }

        if($country) {
            $params[] = $country;
            $join_q .= " JOIN omnisol.security_country co ON co.id = s.country_id ";
            $and .= " AND se.code = ? ";
        }

        $params[] = $input_symbols;

        $query = "SELECT symbol 
                  FROM omnisol.securities s 
                  JOIN omnisol.security_type st ON st.id = s.security_type_id
                  {$join_q}
                  WHERE st.type = ? {$exchange} AND s.symbol IN ({$questions})";
        $result = $adb->pquery($query, $params, true);
        if($adb->num_rows($result) > 0) {
            while ($x = $adb->fetchByAssoc($result)) {
                $symbols[] = $x['symbol'];
            }
        }

        return $symbols;
    }
}
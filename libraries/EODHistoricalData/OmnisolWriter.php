<?php
spl_autoload_register(function ($className) {
    if (file_exists("libraries/EODHistoricalData/$className.php")) {
        include_once "libraries/EODHistoricalData/$className.php";
    }
});

require_once("libraries/EODHistoricalData/EODGuzzle.php");
require_once("libraries/EODHistoricalData/OmnisolReader.php");

class OmnisolWriter{
    private $guz;

    public function __construct(){
        $this->guz = new cEodGuzzle();
    }

    /**
     * Returns JSON Decoded exchanges
     * @return mixed
     */
    public function GetAndWriteExchangeData(){
        $exchanges = json_decode($this->guz->GetExchanges());
        foreach($exchanges AS $k => $v){
            $this->WriteNewExchangeData($v);
        }
        return $exchanges;
    }

    public function GetAndWriteTickers($exchange_code){
        echo "Writing " . $exchange_code . '<br />';
        $res = $this->guz->getTickers($exchange_code);//Returns the CSV file of tickers for the exchange code
        $this->WriteNewTickerData($res);
    }

    public function GetTickers($exchange_code){
        return $this->guz->getTickers($exchange_code);//Returns the CSV file of tickers for the exchange code
    }

    private function TickerToArray($data){
        global $adb;
        $separator = "\r\n";
        $line = strtok($data, $separator);//Separate the string into lines

        //The following takes the individual lines passed in from the CSV and puts them into an array by their category
        while ($line !== false) {
            $line = strtok( $separator );//Get the line
            $params = str_getcsv($line);//Separate the line's CSV into an array
            $params[2] = $this->WriteTableFieldGetID("security_country", "country_name", $params[2]);
            $params[3] = $this->WriteTableFieldGetID("security_exchange", "code", $params[3]);
            $params[4] = $this->WriteTableFieldGetID("security_currency", "currency", $params[4]);
            $params[5] = $this->WriteTableFieldGetID("security_type", "type", $params[5]);
            $questions = generateQuestionMarks($params);
            if($this->IsInputArrayValid($params)) {
                $query = "INSERT INTO omnisol.securities (symbol, name, country_id, exchange_id, currency_id, security_type_id)
                          VALUES ({$questions})
                          ON DUPLICATE KEY UPDATE symbol = VALUES(symbol), name = VALUES(name), country_id = VALUES(country_id), 
                                           exchange_id = VALUES(exchange_id), currency_id = VALUES(currency_id), 
                                           security_type_id = VALUES(security_type_id)";
                $adb->pquery($query, $params, true);
            }
        }
    }

    private function WriteNewTickerData($data){
        global $adb;
        $separator = "\r\n";
        $line = strtok($data, $separator);//Separate the string into lines

        //The following takes the individual lines passed in from the CSV and puts them into an array by their category
        while ($line !== false) {
            $line = strtok( $separator );//Get the line
            $params = str_getcsv($line);//Separate the line's CSV into an array
            $params[2] = $this->WriteTableFieldGetID("security_country", "country_name", $params[2]);
            $params[3] = $this->WriteTableFieldGetID("security_exchange", "code", $params[3]);
            $params[4] = $this->WriteTableFieldGetID("security_currency", "currency", $params[4]);
            $params[5] = $this->WriteTableFieldGetID("security_type", "type", $params[5]);
            $questions = generateQuestionMarks($params);
            if($this->IsInputArrayValid($params)) {
                $query = "INSERT INTO omnisol.securities (symbol, name, country_id, exchange_id, currency_id, security_type_id)
                          VALUES ({$questions})
                          ON DUPLICATE KEY UPDATE symbol = VALUES(symbol), name = VALUES(name), country_id = VALUES(country_id), 
                                           exchange_id = VALUES(exchange_id), currency_id = VALUES(currency_id), 
                                           security_type_id = VALUES(security_type_id)";
                $adb->pquery($query, $params, true);
            }
        }
    }

    /**
     * Takes the data within the stock class and writes it to the new table
     * @param TypeStock $data
     */
    public function WriteNewStockData(TypeStock $data){
        global $adb;
        $params = array();
/*
 * Anything with "WriteTableFieldGetID" will check the field passed in with the data passed in.  If the field already exists, it will
 * return the ID.  If it doesn't exist, it will create and return the unique ID.  Anything else is just straight written data and is not
 * associative
 */
        $params[] = $data->Code;
        $params[] = $this->WriteTableFieldGetID("security_type", "type", $data->Type);
        $params[] = $data->Name;
        $params[] = $this->WriteTableFieldGetID("security_exchange", "code", $data->Exchange);
        $params[] = $this->WriteTableFieldGetID("security_currency", "currency", $data->CurrencyCode);
        $params[] = $this->WriteTableFieldGetID("security_country", "country_name", $data->CountryName);
        $params[] = $data->ISIN;
        $params[] = $data->CUSIP;
        $params[] = $data->CIK;
        $params[] = $data->EmployerIdNumber;
        $params[] = $data->FiscalYearEnd;
        $params[] = $data->IPODate;
        $params[] = $data->InternationalDomestic;
        $params[] = $this->WriteTableFieldGetID("security_sector", "sector_name", $data->Sector);
        $params[] = $this->WriteTableFieldGetID("security_industry", "industry_name", $data->Industry);
        $params[] = $this->WriteTableFieldGetID("security_gic_sector", "gic_sector_name", $data->GicSector);
        $params[] = $this->WriteTableFieldGetID("security_gic_group", "gic_group_name", $data->GicGroup);
        $params[] = $this->WriteTableFieldGetID("security_gic_industry", "gic_industry", $data->GicIndustry);
        $params[] = $this->WriteTableFieldGetID("security_gic_sub_industry", "gic_sub_industry", $data->GicSubIndustry);
        $params[] = $this->WriteTableFieldGetID("security_home_category", "home_category", $data->HomeCategory);
        $params[] = $data->IsDelisted;
        $params[] = $data->Description;
        $params[] = $data->Address;
        $params[] = $data->Phone;
        $params[] = $data->WebURL;
        $params[] = $data->LogoURL;
        $params[] = $data->FullTimeEmployees;
        $params[] = $data->UpdatedAt;

        $questions = generateQuestionMarks($params);
        $query = "INSERT INTO omnisol.securities (symbol, security_type_id, name, exchange_id, currency_id, country_id, isin, cusip, cik,
                                          employer_id, fiscal_year_end, ipo_date, international_domestic, sector_id, industry_id, gic_sector_id,
                                          gic_group_id, gic_industry, gic_sub_industry, home_category_id, is_delisted, description, address, phone, 
                                          web_url, logo_url, full_time_employees, updated_at)
                  VALUES ({$questions})
                  ON DUPLICATE KEY UPDATE security_id = VALUES(security_id), symbol = VALUES(symbol), security_type_id = VALUES(security_type_id), name = VALUES(name), 
                                          exchange_id = VALUES(exchange_id), currency_id = VALUES(currency_id), country_id = VALUES(country_id), 
                                          isin = VALUES(isin), cusip = VALUES(cusip), cik = VALUES(cik), employer_id = VALUES(employer_id), 
                                          fiscal_year_end = VALUES(fiscal_year_end), ipo_date = VALUES(ipo_date), international_domestic = VALUES(international_domestic), 
                                          sector_id = VALUES(sector_id), industry_id = VALUES(industry_id), gic_sector_id = VALUES(gic_sector_id), 
                                          gic_group_id = VALUES(gic_group_id), gic_industry = VALUES(gic_industry), gic_sub_industry = VALUES(gic_sub_industry), 
                                          home_category_id = VALUES(home_category_id), is_delisted = VALUES(is_delisted), description = VALUES(description), 
                                          address = VALUES(address), phone = VALUES(phone), web_url = VALUES(web_url), logo_url = VALUES(logo_url), 
                                          full_time_employees = VALUES(full_time_employees), updated_at = VALUES(updated_at)";
        $adb->pquery($query, $params, true);
    }

    /**
     * Write the exchange data (name, code, operating_mic, country, currency_id)
     * @param $data
     */
    public function WriteNewExchangeData($data){
        global $adb;
        $params[] = $data->Name;
        $params[] = $data->Code;
        $params[] = $data->OperatingMIC;
        $params[] = $this->WriteTableFieldGetID("security_currency", "currency", $data->Currency);
        $params[] = $this->WriteTableFieldGetID("security_country", "country_name", $data->Country);

        $query = "INSERT INTO omnisol.security_exchange (name, code, operating_mic, country_id, currency_id)
                  VALUES (?, ?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE code = VALUES(code), operating_mic = VALUES(operating_mic), country_id = VALUES(country_id),
                                          currency_id = VALUES(currency_id)";
        $adb->pquery($query, $params, true);
    }

    /**
     * Writes in the passed in value and returns its unique ID
     * @param $value
     * @return string|string[]|null
     * @throws Exception
     */
    public function WriteTableFieldGetID($table_name, $field, $value, $return_field = "id"){
        global $adb;
        $query = "INSERT IGNORE INTO omnisol.{$table_name} ({$field}) VALUES (?)";
        $adb->pquery($query, array($value));

        $query = "SELECT {$return_field} FROM omnisol.{$table_name} WHERE {$field} = ?";
        $result = $adb->pquery($query, array($value));

        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, $return_field);
        }
        return null;//Should never get here seeing as we insert then return
    }

    /**
     * Checks if the input array is valid or not.  If any of the values in the array aren't filled in, this will return false
     * @param $input_array
     * @return bool
     */
    public function IsInputArrayValid($input_array){
        if(count(array_filter($input_array)) == count($input_array)) {
            return true;//The array is valid
        } else {
            return false;//Something hasn't been filled in
        }
    }
}
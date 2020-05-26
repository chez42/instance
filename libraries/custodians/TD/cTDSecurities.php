<?php
require_once("libraries/custodians/cCustodian.php");

class cTDSecuritiesData{
    public $symbol, $description, $security_type, $price, $maturity, $annual_income_amount, $interest_rate, $multiplier,
           $omni_base_asset_class, $first_coupon, $call_date, $call_price, $issue_date, $share_per_contact, $factor, $filename;

    public function __construct($data){
        $this->symbol = $data['symbol'];
        $this->description = $data['description'];
        $this->security_type = $data['security_type'];
        $this->mapped_security_type = $data['mapped_security_type'];
        $this->price = $data['price'];
        $this->maturity = $data['maturity'];
        $this->annual_income_amount = $data['annual_income_amount'];
        $this->interest_rate = $data['interest_rate'];
        $this->multiplier = $data['mutiplier'];
        $this->omni_base_asset_class = $data['omni_base_asset_class'];
        $this->first_coupon = $data['first_coupon'];
        $this->call_date = $data['call_date'];
        $this->call_price = $data['call_price'];
        $this->issue_date = $data['issue_date'];
        $this->share_per_contact = $data['share_per_contact'];
        $this->factor = $data['factor'];
        $this->filename = $data['filename'];
    }
}

/**
 * Class cTDSecurities
 * This class allows the pulling of data from the custodian database
 */
class cTDSecurities extends cCustodian {
    use tSecurities;
    private $securities_data;//Holds the security information
    private $symbol_replacements;//Holds key value pairing for replacing symbols.  IE:  "TDCASH" => "Cash" will replace "TDCASH" from the CRM with "Cash" while checking if it exists or not

    public function __construct($name = "TD", $database = "custodian_omniscient", $module = "securities",
                                $securities_table="custodian_securities_td", array $symbols, array $symbol_replacements, array $columns){
        $this->name = $name;
        $this->database = $database;
        $this->module = $module;
        $this->table = $securities_table;
        $this->symbol_replacements = $symbol_replacements;
        $this->columns = $columns;
        $this->RetrieveSecuritiesData($symbols);
        $this->SetupSecuritiesComparisons();
    }

    /**
     * Returns an associative array of all requested securities
     * @param string $table
     * @param null $date
     * @return mixed
     */
    protected function RetrieveSecuritiesData(array $symbols){
        if(!empty($symbols)) {
            global $adb;
            $params = array();
            $where = "";

            if (empty($this->columns))
                $fields = "*";
            else {
                $fields = implode(", ", $this->columns);
            }

            if (!empty($symbols)) {
                $symbol_q = generateQuestionMarks($symbols);
                $where .= " WHERE f.symbol IN ({$symbol_q}) ";
                $params[] = $symbols;
            }

            $query = "SELECT {$fields} FROM {$this->database}.{$this->table} f
                      LEFT JOIN {$this->database}.custodian_prices_td pr ON pr.symbol = f.symbol AND pr.date = (SELECT MAX(date) FROM {$this->database}.custodian_prices_td WHERE symbol = f.symbol)
                      LEFT JOIN securities_mapping_td acm ON acm.code = f.security_type
                      {$where} ";
            $result = $adb->pquery($query, $params, true);

            if ($adb->num_rows($result) > 0) {
                while ($r = $adb->fetchByAssoc($result)) {
                    $this->securities_data[strtoupper(TRIM($r['symbol']))] = $r;
                }
            }
        }
    }

    public function GetSymbolReplacements(){
        return $this->symbol_replacements;
    }

    public function GetSecuritiesData(){
        return $this->securities_data;
    }

    /**
     * Using the cTDSecuritiesData class, create the securities.  Used with a pre-filled in cTDSecuritiesoData class (done manually)
     * @param cTDPortfolioData $data
     * @throws Exception
     */
    public function CreateNewSecuritiesUsingcTDSecuritiesData(cTDSecuritiesData $data){
        if(!$this->DoesSecurityExistInCRM($data->symbol)) {//If the security doesn't exist yet, create it
            $crmid = $this->UpdateEntitySequence();

            $this->FillEntityTable($crmid, $data);
            $this->FillSecuritiesTable($crmid, $data);
            $this->FillSecuritiesCFTable($crmid, $data);
        }
    }

    public function UpdateSecuritiesUsingcTDSecuritiesData(cTDSecuritiesData $data){
        echo 'updating - ' . $data->symbol . '<br />';
        if($this->DoesSecurityExistInCRM($data->symbol)) {
            global $adb;
            $params = array();
            $params[] = $data->price;
            $params[] = $this->ConvertMDYtoYMD($data->maturity);
            $params[] = $data->annual_income_amount;
            $params[] = $data->interest_rate;
            $params[] = $data->interest_rate;
            $params[] = $data->multiplier;
            $params[] = $data->omni_base_asset_class;
            $params[] = $data->factor;
            $params[] = $data->filename;
            $params[] = $this->ConvertMDYtoYMD($data->first_coupon);
            $params[] = $this->ConvertMDYtoYMD($data->call_date);
            $params[] = $data->call_price;
            $params[] = $this->ConvertMDYtoYMD($data->issue_date);
            $params[] = $data->share_per_contact;
            $params[] = $data->symbol;

            $query = "UPDATE vtiger_modsecurities m 
                      JOIN vtiger_modsecuritiescf cf USING (modsecuritiesid)
                      JOIN vtiger_crmentity e ON e.crmid = m.modsecuritiesid
                      SET m.security_price = ?, m.last_update = NOW(), m.maturity_date = ?, 
                          cf.dividend_share = ?, cf.interest_rate = ?, m.interest_rate = ?, cf.security_price_adjustment = ?, cf.aclass = ?, 
                          m.asset_backed_factor = ?, m.source = ?, 
                          cf.first_coupon_date = ?, cf.call_date = ?, cf.call_price = ?, cf.issue_date = ?, cf.share_per_contract = ?
                      WHERE m.security_symbol = ?";
            $adb->pquery($query, $params, true);
        }
    }

    /**
     * Auto creates the securities based on the data loaded into the $securities_data member.  If the security exists in this data, it will be created
     * @param array $account_numbers
     */
    public function CreateNewSecuritiesFromSecurityData($symbols = null){
        if(empty($symbols)){//If no symbols passed in, then we try and use the filled in securities
            if(!empty($this->securities_data)){//If there are filled in securities, set the symbols
                $symbols = array();
                foreach ($this->securities_data AS $k => $v) {
                    $symbols[] = $k;
                }
            }
        }

        if(!empty($symbols)) {
            foreach ($symbols AS $k => $v) {
                $data = $this->securities_data[$v];
                if (!empty($data)) {
                    $tmp = new cTDSecuritiesData($data);
                    $this->CreateNewSecuritiesUsingcTDSecuritiesData($tmp);
                }
            }
        }
    }

    public function SetSecurities(array $symbols){
        if(!empty($symbols)) {
            $this->RetrieveSecuritiesData($symbols);
            $this->SetupSecuritiesComparisons();
        }
    }

    /**
     * Auto updates the securities based on the data loaded into the $securities_data member.
     * @param array $account_numbers
     */
    public function UpdateSecuritiesFromSecuritiesData(array $symbols = null){
        if(empty($symbols)){//If no symbols passed in, then we try and use the filled in securities
            if(!empty($this->securities_data)){//If there are filled in securities, set the symbols
                $symbols = array();
                foreach ($this->securities_data AS $k => $v) {
                    $symbols[] = $k;
                }
            }
        }

        if(!empty($symbols)) {
            foreach ($symbols AS $k => $v) {
                $data = $this->securities_data[$v];
                if (!empty($data)) {
                    $tmp = new cTDSecuritiesData($data);
                    $this->UpdateSecuritiesUsingcTDSecuritiesData($tmp);
                }
            }
        }
    }

    /**
     * Create the new entity in the crmentity table
     * @param $crmid
     * @param $owner
     * @param cTDPositionsData $data
     */
    protected function FillEntityTable($crmid, cTDSecuritiesData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;
        $params[] = 1;
        $params[] = 1;
        $params[] = 1;
        $params[] = 'ModSecurities';
        $params[] = $data->description;
        $query = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, createdtime, modifiedtime, label)
                  VALUES (?, ?, ?, ?, ?, NOW(), NOW(), ?)";
        $adb->pquery($query, $params, true);
    }

    /**
     * Creates data in the vtiger_modsecurities table
     * @param $crmid
     * @param cTDPositionsData $data
     */
    protected function FillSecuritiesTable($crmid, cTDSecuritiesData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;
        $swap = array_flip($this->symbol_replacements);//Flip the replacements because we want CRM symbols
        if(array_key_exists($data->symbol, $swap))
            $params[] = $swap[$data->symbol];
        else
            $params[] = $data->symbol;
        $params[] = $data->description;
        $params[] = $data->security_type;
        $params[] = $data->price;
        $params[] = $this->ConvertMDYtoYMD($data->maturity);
        $params[] = $data->interest_rate;
        $params[] = $data->security_type;
        $params[] = $data->factor;

        $questions = generateQuestionMarks($params);
        $query = "INSERT INTO vtiger_modsecurities (modsecuritiesid, security_symbol, security_name, prod_code, security_price, maturity_date,
                                                    interest_rate, securitytype, asset_backed_factor)
                  VALUES ({$questions})";
        $adb->pquery($query, $params, true);
    }

    /**
     * Creates data in the vtiger_positioninformationcf table
     * @param $crmid
     * @param cTDPositionsData $data
     */
    protected function FillSecuritiesCFTable($crmid, cTDSecuritiesData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;
        $params[] = "TD";
        $params[] = $data->interest_rate;
        $params[] = $data->multiplier;
        $params[] = $data->omni_base_asset_class;
        $params[] = $this->ConvertMDYtoYMD($data->first_coupon);
        $params[] = $data->annual_income_amount;
        $params[] = $this->ConvertMDYtoYMD($data->call_date);
        $params[] = $data->call_price;
        $params[] = $this->ConvertMDYtoYMD($data->issue_date);
        $params[] = $data->share_per_contact;

        $questions = generateQuestionMarks($params);
        $query = "INSERT INTO vtiger_modsecuritiescf (modsecuritiesid, provider, interest_rate, security_price_adjustment, 
                                                      aclass, first_coupon_date, dividend_share, call_date, call_price, issue_date, 
                                                      share_per_contract)
                  VALUES ({$questions})";
        $adb->pquery($query, $params, true);
    }

    protected function ConvertMDYtoYMD($date){
        if(!empty($date))
            return date("Y-m-d", strtotime($date));
        return null;
    }
}
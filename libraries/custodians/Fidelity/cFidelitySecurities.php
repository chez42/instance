<?php
require_once("libraries/custodians/cCustodian.php");

class cFidelitySecuritiesData{
    public  $symbol, $type, $description, $cusip, $dividend_yield, $option_expiration_date, $strike_price, $option_symbol,
            $interest_rate, $maturity_date, $issue_date, $first_coupon_date, $zero_coupon_indicator, $abbreviated_fund_name,
            $accrual_method, $as_of_date, $asset_class_code, $asset_class_type_code, $bond_class, $close_price, $close_price_unfactored,
            $current_factor_inflation_factor, $current_factor_date, $dividend_rate, $exchange, $expiration_date, $fixed_income_call_put_date,
            $fixed_income_call_put_price, $floor_symbol, $foreign_security, $fund_family, $fund_family_id, $fund_number, $host_type_code,
            $interest_frequency, $issue_state, $margin, $mmkt_fund_designation, $operation_code, $options_symbol_underlying_security,
            $pricing_factor, $security_group, $security_id, $security_type_description, $sic_code, $tradable, $yield_to_maturity, $file_date,
            $filename, $insert_date, $latest_price, $multiplier;

    public function __construct($data){
        $this->symbol = $data['symbol'];
        $this->type = $data['type'];
        $this->description = $data['description'];
        $this->cusip = $data['cusip'];
        $this->dividend_yield = $data['dividend_yield'];
        $this->option_expiration_date = $data['option_expiration_date'];
        $this->strike_price = $data['strike_price'];
        $this->option_symbol = $data['option_symbol'];
        $this->interest_rate = $data['interest_rate'];
        $this->maturity_date = $data['maturity_date'];
        $this->issue_date = $data['issue_date'];
        $this->first_coupon_date = $data['first_coupon_date'];
        $this->zero_coupon_indicator = $data['zero_coupon_indicator'];
        $this->abbreviated_fund_name = $data['abbreviated_fund_name'];
        $this->accrual_method = $data['accrual_method'];
        $this->as_of_date = $data['as_of_date'];
        $this->asset_class_code = $data['asset_class_code'];
        $this->asset_class_type_code = $data['asset_class_type_code'];
        $this->bond_class = $data['bond_class'];
        $this->close_price = $data['close_price'];
        $this->close_price_unfactored = $data['close_price_unfactored'];
        $this->current_factor_inflation_factor = $data['current_factor_inflation_factor'];
        $this->current_factor_date = $data['current_factor_date'];
        $this->dividend_rate = $data['dividend_rate'];
        $this->exchange = $data['exchange'];
        $this->expiration_date = $data['expiration_date'];
        $this->fixed_income_call_put_date = $data['fixed_income_call_put_date'];
        $this->fixed_income_call_put_price = $data['fixed_income_call_put_price'];
        $this->floor_symbol = $data['floor_symbol'];
        $this->foreign_security = $data['foreign_security'];
        $this->fund_family = $data['fund_family'];
        $this->fund_family_id = $data['fund_family_id'];
        $this->fund_number = $data['fund_number'];
        $this->host_type_code = $data['host_type_code'];
        $this->interest_frequency = $data['interest_frequency'];
        $this->issue_state = $data['issue_state'];
        $this->margin = $data['margin'];
        $this->mmkt_fund_designation = $data['mmkt_fund_designation'];
        $this->operation_code = $data['operation_code'];
        $this->options_symbol_underlying_security = $data['options_symbol_underlying_security'];
        $this->pricing_factor = $data['pricing_factor'];
        $this->security_group = $data['security_group'];
        $this->security_id = $data['security_id'];
        $this->security_type_description = $data['security_type_description'];
        $this->sic_code = $data['sic_code'];
        $this->tradable = $data['tradable'];
        $this->yield_to_maturity = $data['yield_to_maturity'];
        $this->file_date = $data['file_date'];
        $this->filename = $data['filename'];
        $this->insert_date = $data['insert_date'];
        $this->latest_price = $data['latest_price'];
        $this->multiplier = $data['multiplier'];
        $this->omni_base_asset_class = $data['omni_base_asset_class'];
        $this->security_type = $data['security_type'];
    }
}

/**
 * Class cFidelityPortfolios
 * This class allows the pulling of data from the custodian database
 */
class cFidelitySecurities extends cCustodian {
    use tSecurities;
    private $securities_data;//Holds the security information

    public function __construct($name = "Fidelity", $database = "custodian_omniscient", $module = "securities",
                                $securities_table="custodian_securities_fidelity", array $symbols, array $symbol_replacements, array $columns){
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
            $where .= " WHERE f.symbol IN ({$symbol_q}) AND f.symbol != ''";
            $params[] = $symbols;
        }

        $query = "SELECT {$fields} FROM {$this->database}.{$this->table} f
                      LEFT JOIN {$this->database}.custodian_prices_fidelity pr ON pr.symbol = f.symbol AND pr.price_date = (SELECT MAX(price_date) FROM {$this->database}.custodian_prices_fidelity WHERE symbol = f.symbol)
                      LEFT JOIN {$this->database}.securities_mapping_fidelity map ON map.type = f.type AND map.asset_class_code = f.asset_class_code AND map.asset_class_type_code = f.asset_class_type_code
                      {$where} ";
        $result = $adb->pquery($query, $params, true);

        if($adb->num_rows($result) > 0){
            while($r = $adb->fetchByAssoc($result)){
                $this->securities_data[$r['symbol']] = $r;
            }
        }
        return $this->securities_data;
    }

    public function GetSymbolReplacements(){
        return $this->symbol_replacements;
    }

    public function GetSecuritiesData(){
        return $this->securities_data;
    }

    /**
     * Using the cFidelitySecuritiesData class, create the securities.  Used with a pre-filled in cFidelitySecuritiesoData class (done manually)
     * @param cFidelityPortfolioData $data
     * @throws Exception
     */
    public function CreateNewSecuritiesUsingcFidelitySecuritiesData(cFidelitySecuritiesData $data){
        if(!$this->DoesSecurityExistInCRM($data->symbol)) {//If the security doesn't exist yet, create it
            $crmid = $this->UpdateEntitySequence();

            $this->FillEntityTable($crmid, $data);
            $this->FillSecuritiesTable($crmid, $data);
            $this->FillSecuritiesCFTable($crmid, $data);
        }
    }

    public function UpdateSecuritiesUsingcFidelitySecuritiesData(cFidelitySecuritiesData $data){
        if($this->DoesSecurityExistInCRM($data->symbol)) {
            global $adb;
            $params = array();
            $params[] = $data->cusip;
            $params[] = $data->description;
            $params[] = $data->latest_price;
            $params[] = $data->multiplier;
            $params[] = $data->omni_base_asset_class;
            $params[] = $data->security_type;
            $params[] = $data->interest_rate;
            $params[] = $data->maturity_date;
            $params[] = $data->filename;
            $params[] = $data->interest_rate;
            $params[] = $data->interest_rate;
            $params[] = $data->interest_frequency;
            $params[] = $data->interest_frequency;
            $params[] = $data->interest_frequency;
            $params[] = $data->interest_frequency;
            $params[] = $data->symbol;

            $query = "UPDATE vtiger_modsecurities m 
                      JOIN vtiger_modsecuritiescf cf USING (modsecuritiesid)
                      JOIN vtiger_crmentity e ON e.crmid = m.modsecuritiesid                      
                      SET cf.cusip = ?, m.security_name = ?, m.security_price = ?,
                      cf.security_price_adjustment = ?, cf.aclass = CASE WHEN cf.aclass = '' AND cf.ignore_auto_update = 0 THEN ? ELSE cf.aclass END, 
                      m.securitytype = ?, m.interest_rate = ?, m.maturity_date = ?, cf.provider = 'Fidelity', 
                      m.last_update = NOW(), e.modifiedtime = NOW(), m.source = ?, cf.interest_rate = CASE WHEN ? > 0 THEN ? ELSE cf.interest_rate END,
                      m.pay_frequency = CASE WHEN ? = 'A' THEN 'annual' WHEN ? = 'M' THEN 'monthly' WHEN ? = 'S' THEN 'SemiAnnual' WHEN ? = 'Q' THEN 'quarterly' ELSE m.pay_frequency END
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
                    $tmp = new cFidelitySecuritiesData($data);
                    $this->CreateNewSecuritiesUsingcFidelitySecuritiesData($tmp);
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
                $data = $this->securities_data[strtoupper($v)];
                if (!empty($data)) {
                    $tmp = new cFidelitySecuritiesData($data);
#                    echo '<br />';
                    $this->UpdateSecuritiesUsingcFidelitySecuritiesData($tmp);
                }
            }
        }
    }

    /**
     * Create the new entity in the crmentity table
     * @param $crmid
     * @param $owner
     * @param cFidelitySecuritiesData $data
     */
    protected function FillEntityTable($crmid, cFidelitySecuritiesData $data){
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
     * @param cFidelitySecuritiesData $data
     */
    protected function FillSecuritiesTable($crmid, cFidelitySecuritiesData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;
        //We need to make sure the keys are checking the same thing, so make them upper case
        $swap = array_change_key_case(array_flip($this->symbol_replacements), CASE_UPPER);//Flip the replacements because we want CRM symbols
        if(array_key_exists(strtoupper($data->symbol), $swap))//If we have a match, then we need to change the security symbol
            $params[] = $swap[$data->symbol];
        else
            $params[] = $data->symbol;

        $params[] = $data->description;

        $questions = generateQuestionMarks($params);
        $query = "INSERT INTO vtiger_modsecurities (modsecuritiesid, security_symbol, security_name)
                  VALUES ({$questions})";
        $adb->pquery($query, $params, true);
    }

    /**
     * Creates data in the vtiger_positioninformationcf table
     * @param $crmid
     * @param cFideligySecuritiesData $data
     */
    protected function FillSecuritiesCFTable($crmid, cFidelitySecuritiesData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;
        $params[] = "FIDELITY";
        $params[] = $data->us_stock;
        $params[] = $data->intl_stock;
        $params[] = $data->us_bond;
        $params[] = $this->intl_bond;
        $params[] = $data->preferred_net;
        $params[] = $this->convertible_net;
        $params[] = $data->cash_net;
        $params[] = $this->other_net;
        $params[] = $data->unclassified_net;

        $questions = generateQuestionMarks($params);
        $query = "INSERT INTO vtiger_modsecuritiescf (modsecuritiesid, provider, us_stock, intl_stock, us_bond, intl_bond, preferred_net, 
                              convertible_net, cash_net, other_net, unclassified_net)
                  VALUES ({$questions})";
        $adb->pquery($query, $params, true);
    }

    protected function ConvertMDYtoYMD($date){
        if(!empty($date))
            return date("Y-m-d", strtotime($date));
        return null;
    }

    static public function CreateNewSecurities(array $symbols){
        global $adb;
        $questions = generateQuestionMarks($symbols);

        $query = "DROP TABLE IF EXISTS CreateSecurities";
        $adb->pquery($query, array(), true);

        $query = "SELECT symbol, description, 0 AS crmid, us_stock, intl_stock, us_bond, intl_bond, preferred_net, convertible_net, cash_net, other_net, unclassified_net 
                  FROM custodian_omniscient.custodian_securities_fidelity sec
                  LEFT JOIN vtiger_global_asset_class_mapping m ON m.fidelity_asset_class_code = sec.asset_class_type_code
                  WHERE symbol IN ({$questions})
                  AND symbol != ''";
        $adb->pquery($query, array($symbols), true);

        $securities_result = $adb->pquery($query, array($symbols), true);
        if($adb->num_rows($securities_result) > 0) {
            while($v = $adb->fetchByAssoc($securities_result)) {
                $new_id_result = $adb->pquery("SELECT IncreaseAndReturnCrmEntitySequence() AS crmid", array());
                $id = $adb->query_result($new_id_result, 0, 'crmid');

                $query = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, createdtime, modifiedtime, label)
                          VALUES (?, 1, 1, 1, 'ModSecurities', NOW(), NOW(), ?)";
                $adb->pquery($query, array($id,$v['description']), true);

                $query = "INSERT INTO vtiger_modsecurities (modsecuritiesid, security_symbol, security_name, security_price)
                          VALUES (?, ?, ?, ?)";
                $adb->pquery($query, array($id, $v['symbol'], $v['description'], $v['price']), true);

                $query = "INSERT INTO vtiger_modsecuritiescf (modsecuritiesid, security_price_adjustment)
                          VALUES (?, ?)";
                $adb->pquery($query, array($id, $v['multiplier']), true);
            }
        }
/*        $query = "UPDATE CreateSecurities SET crmid = IncreaseAndReturnCrmEntitySequence()";
        $adb->pquery($query, array(), true);
/*
        $query = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, createdtime, modifiedtime, label)
                  SELECT crmid, 1, 1, 1, 'ModSecurities', NOW(), NOW(), description 
                  FROM CreateSecurities";
        $adb->pquery($query, array(), true);

        $query = "INSERT INTO vtiger_modsecurities (modsecuritiesid, security_symbol, security_name)
                  SELECT crmid, symbol, description 
                  FROM CreateSecurities";
        $adb->pquery($query, array(), true);

        $query = "INSERT INTO vtiger_modsecuritiescf (modsecuritiesid, us_stock, intl_stock, us_bond, intl_bond, preferred_net, convertible_net, cash_net, other_net, unclassified_net)
                  SELECT crmid, us_stock, intl_stock, us_bond, intl_bond, preferred_net, convertible_net, cash_net, other_net, unclassified_net 
                  FROM CreateSecurities";
        $adb->pquery($query, array(), true);
*/
    }

    static public function UpdateAllSymbolsAtOnce(array $symbols){
        global $adb;
        $questions = generateQuestionMarks($symbols);

        $query = "SELECT f.cusip, f.symbol, f.description, pr.price, map.multiplier, f.close_price,
                         CASE WHEN cf.aclass = '' AND cf.ignore_auto_update = 0 THEN map.omni_base_asset_class ELSE cf.aclass END AS asset_class, 
                         m.securitytype = map.security_type, f.interest_rate, f.maturity_date, 'Fidelity' AS provider,
                         NOW() AS last_update, CASE WHEN f.interest_rate > 0 THEN f.interest_rate ELSE cf.interest_rate END AS interest_rate,
                         CASE WHEN f.interest_frequency = 'A' THEN 'annual' WHEN f.interest_frequency = 'M' THEN 'monthly' WHEN f.interest_frequency = 'S' THEN 'SemiAnnual' WHEN f.interest_frequency = 'Q' THEN 'quarterly' ELSE m.pay_frequency END AS pay_frequency,
                         NOW() AS modified_time, f.filename AS source, m.modsecuritiesid
                  FROM custodian_omniscient.custodian_securities_fidelity f 
                  JOIN vtiger_modsecurities m ON m.security_symbol = f.symbol 
                  JOIN vtiger_modsecuritiescf cf ON m.modsecuritiesid = cf.modsecuritiesid 
                  JOIN vtiger_crmentity e ON e.crmid = m.modsecuritiesid 
                  JOIN custodian_omniscient.securities_mapping_fidelity map ON map.type = f.type AND map.asset_class_code = f.asset_class_code AND map.asset_class_type_code = f.asset_class_type_code 
                  JOIN custodian_omniscient.custodian_prices_fidelity pr ON pr.symbol = f.symbol AND pr.price_date = (SELECT MAX(price_date) FROM custodian_omniscient.custodian_prices_fidelity WHERE symbol = f.symbol) 
                  WHERE f.symbol IN ({$questions})";
        $result = $adb->pquery($query, array($symbols), true);

        $query = "UPDATE vtiger_modsecurities m
                  JOIN vtiger_modsecuritiescf cf ON m.modsecuritiesid = cf.modsecuritiesid
                  JOIN vtiger_crmentity e ON e.crmid = m.modsecuritiesid
                  SET cf.cusip = ?, m.security_symbol = ?, m.security_name = ?, m.security_price = ?,
                      cf.security_price_adjustment = ?, m.security_price = ?, cf.aclass = ?,
                      m.interest_rate = ?, m.maturity_date = ?, cf.provider = ?, m.last_update = ?,
                      cf.interest_rate = ?,
                      m.pay_frequency = ?,
                      e.modifiedtime = ?, m.source = ? 
                  WHERE m.modsecuritiesid = ?";
        while($v = $adb->fetchByAssoc($result)){
            $adb->pquery($query, $v, true);
        }
    }
}
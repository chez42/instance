<?php
require_once("libraries/custodians/cCustodian.php");


class cSchwabSecuritiesData{
    public $header, $custodian_id, $master_account_number, $master_account_name, $business_date, $prod_code, $prod_catg_code, $tax_code, $ly,
           $symbol, $industry_ticker_symbol, $cusip, $sec_nbr, $reorg_sec_nbr, $item_issue_id, $rulst_sufid, $isin, $sedol,
           $options_display_symbol, $description1, $description2, $description3, $scrty_des, $underlying_ticker_symbol,
           $underlying_industry_ticker_symbol, $underlying_cusip, $underly_schwab, $underlying_itm_iss_id, $unrul_sufid, $underlying_isin,
           $underly_sedol, $mnymk_code, $last_update, $s_f, $closing_price, $secprice_lstupd, $security_valuation_unit, $optnrt_symbol,
           $opt_expr_date, $c_p, $strike_price, $interest_rate, $maturity_date, $tips_factor, $asset_backed_factor, $face_value_amt, $st_cd,
           $vers_mrkr_1, $p_i, $o_i, $vers_mrkr_2, $closing_price_unfactored, $factor, $factor_date, $product_code, $product_code_category,
           $legacy_security_type, $ticker_symbol, $schwab_security_number, $re_org_schwab_internal_security_number, $rule_set_suffix,
           $security_description_line1, $security_description_line2, $security_description_line3, $security_description_line4,
           $underlying_schwab_security_number, $underlying_item_issue_id, $underlying_rule_set_suffix_id, $underlying_sedol,
           $money_market_code, $last_update_date, $sweep_fund_indicator, $security_price_update_date, $option_root_symbol,
           $option_expiration_date, $option_call_or_put_code, $strike_price_amount, $face_value_amount, $issuer_state,
           $version_marker_number, $schwab_proprietary_indicator, $schwab_one_source_indicator, $version_marker2, $file_date, $filename,
           $insert_date, $multiplier, $latest_price, $omni_base_asset_class;
    public $us_stock, $intl_stock, $us_bond, $intl_bond, $preferred_net, $convertible_net, $cash_net, $other_net, $unclassified_net,
           $security_price_adjustment, $security_type;

    public function __construct($data){
        $this->header = $data['header'];
        $this->custodian_id = $data['custodian_id'];
        $this->master_account_number = $data['master_account_number'];
        $this->master_account_name = $data['master_account_name'];
        $this->business_date = $data['business_date'];
        $this->prod_code = $data['prod_code'];
        $this->prod_catg_code = $data['prod_catg_code'];
        $this->tax_code = $data['tax_code'];
        $this->ly = $data['ly'];
        $this->symbol = ($data['symbol'] == '') ? $data['cusip'] : $data['symbol'];
        $this->industry_ticker_symbol = $data['industry_ticker_symbol'];
        $this->cusip = $data['cusip'];
        $this->sec_nbr = $data['sec_nbr'];
        $this->reorg_sec_nbr = $data['reorg_sec_nbr'];
        $this->item_issue_id = $data['item_issue_id'];
        $this->rulst_sufid = $data['rulst_sufid'];
        $this->isin = $data['isin'];
        $this->sedol = $data['sedol'];
        $this->options_display_symbol = $data['options_display_symbol'];
        $this->description1 = $data['description1'];
        $this->description2 = $data['description2'];
        $this->description3 = $data['description3'];
        $this->scrty_des = $data['scrty_des'];
        $this->underlying_ticker_symbol = $data['underlying_ticker_symbol'];
        $this->underlying_industry_ticker_symbol = $data['underlying_industry_ticker_symbol'];
        $this->underlying_cusip = $data['underlying_cusip'];
        $this->underly_schwab = $data['underly_schwab'];
        $this->underlying_itm_iss_id = $data['underlying_itm_iss_id'];
        $this->unrul_sufid = $data['unrul_sufid'];
        $this->underlying_isin = $data['underlying_isin'];
        $this->underly_sedol = $data['underly_sedol'];
        $this->mnymk_code = $data['mnymk_code'];
        $this->last_update = $data['last_update'];
        $this->s_f = $data['s_f'];
        $this->closing_price = $data['closing_price'];
        $this->secprice_lstupd = $data['secprice_lstupd'];
        $this->security_valuation_unit = $data['security_valuation_unit'];
        $this->optnrt_symbol = $data['optnrt_symbol'];
        $this->opt_expr_date = $data['opt_expr_date'];
        $this->c_p = $data['c_p'];
        $this->strike_price = $data['strike_price'];
        $this->interest_rate = $data['interest_rate'];
        $this->maturity_date = $data['maturity_date'];
        $this->tips_factor = $data['tips_factor'];
        $this->asset_backed_factor = $data['asset_backed_factor'];
        $this->face_value_amt = $data['face_value_amt'];
        $this->st_cd = $data['st_cd'];
        $this->vers_mrkr_1 = $data['vers_mrkr_1'];
        $this->p_i = $data['p_i'];
        $this->o_i = $data['o_i'];
        $this->vers_mrkr_2 = $data['vers_mrkr_2'];
        $this->closing_price_unfactored = $data['closing_price_unfactored'];
        $this->factor = $data['factor'];
        $this->factor_date = $data['factor_date'];
        $this->product_code = $data['product_code'];
        $this->product_code_category = $data['product_code_category'];
        $this->legacy_security_type = $data['legacy_security_type'];
        $this->ticker_symbol = $data['ticker_symbol'];
        $this->schwab_security_number = $data['schwab_security_number'];
        $this->re_org_schwab_internal_security_number = $data['re_org_schwab_internal_security_number'];
        $this->rule_set_suffix = $data['rule_set_suffix'];
        $this->security_description_line1 = $data['security_description_line1'];
        $this->security_description_line2 = $data['security_description_line2'];
        $this->security_description_line3 = $data['security_description_line3'];
        $this->security_description_line4 = $data['security_description_line4'];
        $this->underlying_schwab_security_number = $data['underlying_schwab_security_number'];
        $this->underlying_item_issue_id = $data['underlying_item_issue_id'];
        $this->underlying_rule_set_suffix_id = $data['underlying_rule_set_suffix_id'];
        $this->underlying_sedol = $data['underlying_sedol'];
        $this->money_market_code = $data['money_market_code'];
        $this->last_update_date = $data['last_update_date'];
        $this->sweep_fund_indicator = $data['sweep_fund_indicator'];
        $this->security_price_update_date = $data['security_price_update_date'];
        $this->option_root_symbol = $data['option_root_symbol'];
        $this->option_expiration_date = $data['option_expiration_date'];
        $this->option_call_or_put_code = $data['option_call_or_put_code'];
        $this->strike_price_amount = $data['strike_price_amount'];
        $this->face_value_amount = $data['face_value_amount'];
        $this->issuer_state = $data['issuer_state'];
        $this->version_marker_number = $data['version_marker_number'];
        $this->schwab_proprietary_indicator = $data['schwab_proprietary_indicator'];
        $this->schwab_one_source_indicator = $data['schwab_one_source_indicator'];
        $this->version_marker2 = $data['version_marker2'];
        $this->file_date = $data['file_date'];
        $this->filename = $data['filename'];
        $this->insert_date = $data['insert_date'];
        $this->multiplier = $data['multiplier'];
        $this->latest_price = $data['price'];
        $this->omni_base_asset_class = $data['omni_base_asset_class'];

        $this->us_stock = $data['us_stock'];
        $this->intl_stock = $data['intl_stock'];
        $this->us_bond = $data['us_bond'];
        $this->intl_bond = $data['intl_bond'];
        $this->preferred_net = $data['preferred_net'];
        $this->convertible_net = $data['convertible_net'];
        $this->cash_net = $data['cash_net'];
        $this->other_net = $data['other_net'];
        $this->unclassified_net = $data['unclassified_net'];
        $this->security_price_adjustment = ($data['multiplier'] == '') ? $data['security_price_adjustment'] : $data['multiplier'];
        $this->security_type = $data['security_type'];
    }
}
/**
 * Class cSchwabPortfolios
 * This class allows the pulling of data from the custodian database
 */
class cSchwabSecurities extends cCustodian {
    use tSecurities;
    private $securities_data;//Holds the security information

    public function __construct($name = "Schwab", $database = "custodian_omniscient", $module = "securities",
                                $securities_table="custodian_securities_Schwab", array $symbols, array $symbol_replacements,
                                array $columns){
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
            $where .= " WHERE f.symbol IN ({$symbol_q}) ";
            $params[] = $symbols;
        }

        $query = "SELECT {$fields} FROM {$this->database}.{$this->table} f
                  LEFT JOIN {$this->database}.custodian_prices_schwab pr ON pr.symbol = f.symbol AND pr.date = (SELECT MAX(date) FROM {$this->database}.custodian_prices_schwab WHERE symbol = f.symbol)
                  LEFT JOIN {$this->database}.securities_mapping_schwab map ON map.code = product_code
                  LEFT JOIN {$this->database}.global_asset_class_mapping m ON m.schwab_asset_class_code LIKE CONCAT('%',f.legacy_security_type,'%')
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

    public function GetSecuritiesData($symbols){
        return $this->RetrieveSecuritiesData($symbols);
    }

    /**
     * Using the cSchwabSecuritiesData class, create the securities.  Used with a pre-filled in cSchwabSecuritiesoData class (done manually)
     * @param cSchwabPortfolioData $data
     * @throws Exception
     */
    public function CreateNewSecuritiesUsingcSchwabSecuritiesData(cSchwabSecuritiesData $data){
        if(!$this->DoesSecurityExistInCRM($data->symbol)) {//If the security doesn't exist yet, create it
            $crmid = $this->UpdateEntitySequence();

            $this->FillEntityTable($crmid, $data);
            $this->FillSecuritiesTable($crmid, $data);
            $this->FillSecuritiesCFTable($crmid, $data);
        }
    }

    public function UpdateSecuritiesUsingcSchwabSecuritiesData(cSchwabSecuritiesData $data){
        if($this->DoesSecurityExistInCRM($data->symbol)) {
            global $adb;
            $params = array();
            $params[] = $data->cusip;
            $params[] = $data->symbol;
            $params[] = $data->security_description_line1;
            $params[] = $data->security_description_line1;
            $params[] = $data->multiplier;
            $params[] = $data->latest_price;
            $params[] = $data->security_type;
            $params[] = ($data->omni_base_asset_class == '') ? "Funds" : $data->omni_base_asset_class;
            $params[] = $data->strike_price_amount;
            $params[] = $data->option_expiration_date;
            $params[] = $data->option_root_symbol;
            $params[] = $data->option_call_or_put_code;
            $params[] = $data->interest_rate;
            $params[] = $data->product_code;
            $params[] = $data->maturity_date;
            $params[] = "SCHWAB";
            $params[] = $data->filename;
            $params[] = $data->symbol;
            $query = "UPDATE vtiger_modsecurities m 
                      JOIN vtiger_modsecuritiescf cf USING (modsecuritiesid)
                      JOIN vtiger_crmentity e ON e.crmid = m.modsecuritiesid                      
                      SET cf.cusip = ?, m.security_symbol = ?, m.security_name = ?, m.description1 = ?, cf.security_price_adjustment = ?, 
                          m.security_price = ?, m.securitytype = ?, cf.aclass = ?, m.last_update = NOW(), m.strike_price = ?, 
                          m.opt_expr_date = ?, cf.option_root_symbol = ?, cf.option_call_put = ?, m.interest_rate = ?, m.prod_code = ?, 
                          m.maturity_date = ?, cf.provider = ?, m.source = ?
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
                    $tmp = new cSchwabSecuritiesData($data);
                    $this->CreateNewSecuritiesUsingcSchwabSecuritiesData($tmp);
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
                    $tmp = new cSchwabSecuritiesData($data);
                    $this->UpdateSecuritiesUsingcSchwabSecuritiesData($tmp);
                }
            }
        }
    }

    /**
     * Create the new entity in the crmentity table
     * @param $crmid
     * @param $owner
     * @param cSchwabSecuritiesData $data
     */
    protected function FillEntityTable($crmid, cSchwabSecuritiesData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;
        $params[] = 1;
        $params[] = 1;
        $params[] = 1;
        $params[] = 'ModSecurities';
        $params[] = $data->description1;
        $query = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, createdtime, modifiedtime, label)
                  VALUES (?, ?, ?, ?, ?, NOW(), NOW(), ?)";
        $adb->pquery($query, $params, true);
    }

    /**
     * Creates data in the vtiger_modsecurities table
     * @param $crmid
     * @param cSchwabSecuritiesData $data
     */
    protected function FillSecuritiesTable($crmid, cSchwabSecuritiesData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;
        //We need to make sure the keys are checking the same thing, so make them upper case
        $swap = array_change_key_case(array_flip($this->symbol_replacements), CASE_UPPER);//Flip the replacements because we want CRM symbols
        if(array_key_exists(strtoupper($data->symbol), $swap))//If we have a match, then we need to change the security symbol
            $params[] = $swap[$data->symbol];
        else
            $params[] = $data->symbol;

        $params[] = $data->description1;
        $params[] = $data->description1;
        $params[] = $data->latest_price;
        $params[] = $data->security_type;
        $params[] = $data->last_update;
        $params[] = $data->strike_price;
        $params[] = $data->opt_expr_date;
        $params[] = $data->interest_rate;
        $params[] = $data->product_code;
        $params[] = $data->maturity_date;
        $params[] = 'SCHWAB';

        $questions = generateQuestionMarks($params);
        $query = "INSERT INTO vtiger_modsecurities (modsecuritiesid, security_symbol, security_name, description1, security_price, securitytype, 
                                                    last_update, strike_price, opt_expr_date, interest_rate, prod_code, maturity_date, source)
                  VALUES ({$questions})";
        $adb->pquery($query, $params, true);
    }

    /**
     * Creates data in the vtiger_positioninformationcf table
     * @param $crmid
     * @param cFideligySecuritiesData $data
     */
    protected function FillSecuritiesCFTable($crmid, cSchwabSecuritiesData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;
        $params[] = $data->us_stock;
        $params[] = $data->intl_stock;
        $params[] = $data->us_bond;
        $params[] = $data->intl_bond;
        $params[] = $data->preferred_net;
        $params[] = $data->convertible_net;
        $params[] = $data->cash_net;
        $params[] = $data->other_net;
        $params[] = $data->unclassified_net;
        $params[] = $data->cusip;
        $params[] = $data->multiplier;
        $params[] = $data->omni_base_asset_class;
        $params[] = $data->option_root_symbol;
        $params[] = $data->option_call_or_put_code;
        $params[] = 'SCHWAB';

        $questions = generateQuestionMarks($params);
        $query = "INSERT INTO vtiger_modsecuritiescf (modsecuritiesid, us_stock, intl_stock, us_bond, intl_bond, preferred_net, convertible_net, 
                                                      cash_net, other_net, unclassified_net, cusip, security_price_adjustment, aclass, 
                                                      option_root_symbol, option_call_put, provider)
                  VALUES ({$questions})";
        $adb->pquery($query, $params, true);
    }

    protected function ConvertMDYtoYMD($date){
        if(!empty($date))
            return date("Y-m-d", strtotime($date));
        return null;
    }
}
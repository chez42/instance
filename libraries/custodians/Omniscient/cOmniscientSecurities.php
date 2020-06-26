<?php
require_once("libraries/custodians/cCustodian.php");

class cOmniscientSecuritiesData{
    public $modsecuritiesid, $security_name, $security_symbol, $security_price, $security_id, $update_pc, $asset_class_old, $sectorpl,
           $pay_frequency, $securitytype, $header, $custodian_id, $master_account_number, $master_account_name, $business_date,
           $prod_code, $prod_catg_code, $tax_code, $ly, $industry_ticker_symbol, $sec_nbr, $reorg_sec_nbr, $item_issue_id, $rulst_sufid,
           $isin, $sedol, $options_display_symbol, $description1, $description2, $description3, $scrty_des, $underlying_ticker_symbol,
           $underlying_industry_ticker_symbol, $underlying_cusip, $underly_schwab, $underlying_itm_iss_id, $unrul_sufid, $underlying_isin,
           $underly_sedol, $mnymk_code, $last_update, $s_f, $closing_price, $secprice_lstupd, $security_valuation_unit, $optnrt_symbol,
           $opt_expr_date, $c_p, $strike_price, $interest_rate, $maturity_date, $tips_factor, $asset_backed_factor, $face_value_amt,
           $st_cd, $vers_mrkr_1, $p_i, $o_i, $vers_mrkr_2, $closing_price_unfactored, $schwab_factor, $factor_date, $source, $td_type,
           $benchmark_name, $yahoo_finance_last_update, $raw_eod;

    public  $security_price_adjustment, $cusip, $factor, $aclass, $industrypl,
            $type_override, $average_daily_volume, $book_value, $dividend_share, $earnings_share, $eps_estimate_current_year,
            $eps_estimate_next_year, $eps_estimate_next_quarter, $year_high, $year_low, $market_capitalization, $ebitda,
            $fifty_day_moving_average, $two_hundred_day_moving_average, $two_hundred_day_change, $two_hundred_day_percent_change,
            $fifty_day_change, $fifty_day_percent_change, $price_sales, $price_book, $ex_dividend_date, $peratio, $dividend_pay_date,
            $pegratio, $price_eps_estimate_current_year, $price_eps_estimate_next_year, $short_ratio, $one_year_target_price, $year_range,
            $stock_exchange, $dividend_yield, $summary, $us_stock, $intl_stock, $us_bond, $intl_bond, $preferred_net, $convertible_net,
            $cash_net, $other_net, $unclassified_net, $ignore_auto_update, $cash_instrument, $provider, $cf_2515, $cf_2517,
            $Morning_Star_Category, $beta, $first_coupon_date, $cf_2559, $cf_2561, $cf_2588, $last_eod, $etf, $cf_2612, $cf_2616, $cf_2618,
            $cf_2620, $cf_2622, $preferred, $cf_2626, $cf_2628, $cf_2630, $cf_2632, $cf_2634, $cf_2636, $cf_2638, $cf_2640, $cf_2642,
            $cf_2644, $cf_2646, $cf_2648, $cf_2654, $security_sector, $cf_2715, $cf_2723, $country, $fund_family, $nav, $net_assets,
            $morning_star_rating, $Morning_Star_Risk_Rating, $inception_date, $domicile, $basic_materials_weight, $consumer_cyclical_weight,
            $financial_services_weight, $real_estate_weight, $consumer_defensive_weight, $healthcare_weight, $utilities_weight,
            $energy_weight, $industrials_weight, $communication_services_weight, $us_equity, $canada_equity, $latin_america_equity,
            $uk_equity, $europe_ex_euro_equity, $europe_emerging_equity, $africa_equity, $middle_east_equity, $japan_equity,
            $australasia_equity, $asia_developed_equity, $asia_emerging_equity, $currency_code, $technology_weight, $eod_pricing, $cf_2519,
            $call_date, $issue_date, $call_price, $share_per_contract, $cf_3343, $cf_3345, $cf_3393, $ignore_gain_loss, $option_call_put,
            $option_root_symbol;

    public function __construct($data){
        $this->modsecuritiesid = $data['modsecuritiesid'];
        $this->security_name = $data['security_name'];
        $this->security_symbol = $data['security_symbol'];
        $this->security_price = $data['security_price'];
        $this->security_id = $data['security_id'];
        $this->update_pc = $data['update_pc'];
        $this->asset_class_old = $data['asset_class_old'];
        $this->sectorpl = $data['sectorpl'];
        $this->pay_frequency = $data['pay_frequency'];
        $this->securitytype = $data['securitytype'];
        $this->header = $data['header'];
        $this->custodian_id = $data['custodian_id'];
        $this->master_account_number = $data['master_account_number'];
        $this->master_account_name = $data['master_account_name'];
        $this->business_date = $data['business_date'];
        $this->prod_code = $data['prod_code'];
        $this->prod_catg_code = $data['prod_catg_code'];
        $this->tax_code = $data['tax_code'];
        $this->ly = $data['ly'];
        $this->industry_ticker_symbol = $data['industry_ticker_symbol'];
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
        $this->schwab_factor = $data['schwab_factor'];
        $this->factor_date = $data['factor_date'];
        $this->source = $data['source'];
        $this->td_type = $data['td_type'];
        $this->benchmark_name = $data['benchmark_name'];
        $this->yahoo_finance_last_update = $data['yahoo_finance_last_update'];
        $this->raw_eod = $data['raw_eod'];

        $this->modsecuritiesid = $data['modsecuritiesid'];
        $this->security_price_adjustment = $data['security_price_adjustment'];
        $this->cusip = $data['cusip'];
        $this->interest_rate = $data['interest_rate'];
        $this->maturity_date = $data['maturity_date'];
        $this->factor = $data['factor'];
        $this->aclass = $data['aclass'];
        $this->industrypl = $data['industrypl'];
        $this->type_override = $data['type_override'];
        $this->average_daily_volume = $data['average_daily_volume'];
        $this->book_value = $data['book_value'];
        $this->dividend_share = $data['dividend_share'];
        $this->earnings_share = $data['earnings_share'];
        $this->eps_estimate_current_year = $data['eps_estimate_current_year'];
        $this->eps_estimate_next_year = $data['eps_estimate_next_year'];
        $this->eps_estimate_next_quarter = $data['eps_estimate_next_quarter'];
        $this->year_high = $data['year_high'];
        $this->year_low = $data['year_low'];
        $this->market_capitalization = $data['market_capitalization'];
        $this->ebitda = $data['ebitda'];
        $this->fifty_day_moving_average = $data['fifty_day_moving_average'];
        $this->two_hundred_day_moving_average = $data['two_hundred_day_moving_average'];
        $this->two_hundred_day_change = $data['two_hundred_day_change'];
        $this->two_hundred_day_percent_change = $data['two_hundred_day_percent_change'];
        $this->fifty_day_change = $data['fifty_day_change'];
        $this->fifty_day_percent_change = $data['fifty_day_percent_change'];
        $this->price_sales = $data['price_sales'];
        $this->price_book = $data['price_book'];
        $this->ex_dividend_date = $data['ex_dividend_date'];
        $this->peratio = $data['peratio'];
        $this->dividend_pay_date = $data['dividend_pay_date'];
        $this->pegratio = $data['pegratio'];
        $this->price_eps_estimate_current_year = $data['price_eps_estimate_current_year'];
        $this->price_eps_estimate_next_year = $data['price_eps_estimate_next_year'];
        $this->short_ratio = $data['short_ratio'];
        $this->one_year_target_price = $data['one_year_target_price'];
        $this->year_range = $data['year_range'];
        $this->stock_exchange = $data['stock_exchange'];
        $this->dividend_yield = $data['dividend_yield'];
        $this->summary = $data['summary'];
        $this->us_stock = $data['us_stock'];
        $this->intl_stock = $data['intl_stock'];
        $this->us_bond = $data['us_bond'];
        $this->intl_bond = $data['intl_bond'];
        $this->preferred_net = $data['preferred_net'];
        $this->convertible_net = $data['convertible_net'];
        $this->cash_net = $data['cash_net'];
        $this->other_net = $data['other_net'];
        $this->unclassified_net = $data['unclassified_net'];
        $this->ignore_auto_update = $data['ignore_auto_update'];
        $this->cash_instrument = $data['cash_instrument'];
        $this->provider = $data['provider'];
        $this->cf_2515 = $data['cf_2515'];
        $this->cf_2517 = $data['cf_2517'];
        $this->Morning_Star_Category = $data['Morning_Star_Category'];
        $this->beta = $data['beta'];
        $this->first_coupon_date = $data['first_coupon_date'];
        $this->cf_2559 = $data['cf_2559'];
        $this->cf_2561 = $data['cf_2561'];
        $this->cf_2588 = $data['cf_2588'];
        $this->last_eod = $data['last_eod'];
        $this->etf = $data['etf'];
        $this->cf_2612 = $data['cf_2612'];
        $this->cf_2616 = $data['cf_2616'];
        $this->cf_2618 = $data['cf_2618'];
        $this->cf_2620 = $data['cf_2620'];
        $this->cf_2622 = $data['cf_2622'];
        $this->preferred = $data['preferred'];
        $this->cf_2626 = $data['cf_2626'];
        $this->cf_2628 = $data['cf_2628'];
        $this->cf_2630 = $data['cf_2630'];
        $this->cf_2632 = $data['cf_2632'];
        $this->cf_2634 = $data['cf_2634'];
        $this->cf_2636 = $data['cf_2636'];
        $this->cf_2638 = $data['cf_2638'];
        $this->cf_2640 = $data['cf_2640'];
        $this->cf_2642 = $data['cf_2642'];
        $this->cf_2644 = $data['cf_2644'];
        $this->cf_2646 = $data['cf_2646'];
        $this->cf_2648 = $data['cf_2648'];
        $this->cf_2654 = $data['cf_2654'];
        $this->security_sector = $data['security_sector'];
        $this->cf_2715 = $data['cf_2715'];
        $this->cf_2723 = $data['cf_2723'];
        $this->country = $data['country'];
        $this->fund_family = $data['fund_family'];
        $this->nav = $data['nav'];
        $this->net_assets = $data['net_assets'];
        $this->morning_star_rating = $data['morning_star_rating'];
        $this->Morning_Star_Risk_Rating = $data['Morning_Star_Risk_Rating'];
        $this->inception_date = $data['inception_date'];
        $this->domicile = $data['domicile'];
        $this->basic_materials_weight = $data['basic_materials_weight'];
        $this->consumer_cyclical_weight = $data['consumer_cyclical_weight'];
        $this->financial_services_weight = $data['financial_services_weight'];
        $this->real_estate_weight = $data['real_estate_weight'];
        $this->consumer_defensive_weight = $data['consumer_defensive_weight'];
        $this->healthcare_weight = $data['healthcare_weight'];
        $this->utilities_weight = $data['utilities_weight'];
        $this->energy_weight = $data['energy_weight'];
        $this->industrials_weight = $data['industrials_weight'];
        $this->communication_services_weight = $data['communication_services_weight'];
        $this->us_equity = $data['us_equity'];
        $this->canada_equity = $data['canada_equity'];
        $this->latin_america_equity = $data['latin_america_equity'];
        $this->uk_equity = $data['uk_equity'];
        $this->europe_ex_euro_equity = $data['europe_ex_euro_equity'];
        $this->europe_emerging_equity = $data['europe_emerging_equity'];
        $this->africa_equity = $data['africa_equity'];
        $this->middle_east_equity = $data['middle_east_equity'];
        $this->japan_equity = $data['japan_equity'];
        $this->australasia_equity = $data['australasia_equity'];
        $this->asia_developed_equity = $data['asia_developed_equity'];
        $this->asia_emerging_equity = $data['asia_emerging_equity'];
        $this->currency_code = $data['currency_code'];
        $this->technology_weight = $data['technology_weight'];
        $this->eod_pricing = $data['eod_pricing'];
        $this->cf_2519 = $data['cf_2519'];
        $this->call_date = $data['call_date'];
        $this->issue_date = $data['issue_date'];
        $this->call_price = $data['call_price'];
        $this->share_per_contract = $data['share_per_contract'];
        $this->cf_3343 = $data['cf_3343'];
        $this->cf_3345 = $data['cf_3345'];
        $this->cf_3393 = $data['cf_3393'];
        $this->ignore_gain_loss = $data['ignore_gain_loss'];
        $this->option_call_put = $data['option_call_put'];
        $this->option_root_symbol = $data['option_root_symbol'];
    }
}

/**
 * Class cOmniscientPortfolios
 * This class allows the pulling of data from the custodian database
 */
class cOmniscientSecurities extends cCustodian {
    use tSecurities;
    private $securities_data;//Holds the security information

    public function __construct($name = "Omniscient", $database = "live_omniscient", $module = "securities",
                                $securities_table="vtiger_modsecurities", array $symbols, array $symbol_replacements, array $columns){
        $this->name = $name;
        $this->database = $database;
        $this->module = $module;
        $this->table = $securities_table;
        $this->symbol_replacements = $symbol_replacements;
        $this->columns = $columns;
        if(!empty($symbols)) {
            $this->RetrieveSecuritiesData($symbols);
            $this->SetupSecuritiesComparisons();
        }
    }

    public function GetAllSecuritiesByAssetClass(array $aclass){
        global $adb;
        if(empty($aclass))
            return;

        $questions = generateQuestionMarks($aclass);
        $query = "SELECT security_symbol 
                  FROM {$this->database}.vtiger_modsecurities m 
                  JOIN {$this->database}.vtiger_modsecuritiescf cf USING (modsecuritiesid) 
                  WHERE aclass IN ({$questions})";

        $result = $adb->pquery($query, array($aclass));
        if($adb->num_rows($result) > 0){
            while($r = $adb->fetchByAssoc($result)){
                $symbols[] = $r['security_symbol'];
            }

            $this->RetrieveSecuritiesData($symbols);
            $this->SetupSecuritiesComparisons();
        }
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
            $where .= " WHERE f.security_symbol IN ({$symbol_q}) AND f.security_symbol != ''";
            $params[] = $symbols;
        }

        $query = "SELECT {$fields} FROM {$this->database}.{$this->table} f
                  JOIN {$this->database}.vtiger_modsecuritiescf cf USING (modsecuritiesid)
                  {$where} ";
        $result = $adb->pquery($query, $params, true);

        if($adb->num_rows($result) > 0){
            while($r = $adb->fetchByAssoc($result)){
                $this->securities_data[TRIM($r['security_symbol'])] = $r;
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
     * Using the cOmniscientSecuritiesData class, create the securities.  Used with a pre-filled in cOmniscientSecuritiesoData class (done manually)
     * @param cOmniscientPortfolioData $data
     * @throws Exception
     */
    public function CreateNewSecuritiesUsingcOmniscientSecuritiesData(cOmniscientSecuritiesData $data){
        if(!$this->DoesSecurityExistInCRM($data->security_symbol)) {//If the security doesn't exist yet, create it
            $crmid = $this->UpdateEntitySequence();

            $this->FillEntityTable($crmid, $data);
            $this->FillSecuritiesTable($crmid, $data);
            $this->FillSecuritiesCFTable($crmid, $data);
        }
    }

    public function UpdateSecuritiesDirectJoin($fields){
        global $adb;
        $set = "";

        foreach($fields AS $k => $v){
            $set .= $v . " = ?, ";
        }
        $set = rtrim($set, ', ');

        $query = "UPDATE vtiger_modsecurities m 
                  JOIN vtiger_modsecuritiescf mcf USING (modsecuritiesid)
                  SET {$set}
                  WHERE m.modsecuritiesid = ?";
        $adb->pquery($query, array($fields, $fields['m.modsecuritiesid']), true);
    }

    public function UpdateSecuritiesUsingcOmniscientSecuritiesData(cOmniscientSecuritiesData $data){
#        echo 'updating - ' . $data->security_symbol . '<br />';
        if($this->DoesSecurityExistInCRM($data->security_symbol)) {
            global $adb;
            $params = array();
            $params[] = $data->aclass;
            $params[] = $data->security_price_adjustment;
            $params[] = $data->security_symbol;

            $query = "UPDATE vtiger_modsecurities m 
                      JOIN vtiger_modsecuritiescf cf USING (modsecuritiesid)
                      SET cf.aclass = ?, security_price_adjustment = ?
                      WHERE m.modsecurities = ?";
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
                    $tmp = new cOmniscientSecuritiesData($data);
                    $this->CreateNewSecuritiesUsingcOmniscientSecuritiesData($tmp);
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
                    $tmp = new cOmniscientSecuritiesData($data);
#                    echo '<br />';
                    $this->UpdateSecuritiesUsingcOmniscientSecuritiesData($tmp);
                }
            }
        }
    }

    /**
     * Create the new entity in the crmentity table
     * @param $crmid
     * @param $owner
     * @param cOmniscientSecuritiesData $data
     */
    protected function FillEntityTable($crmid, cOmniscientSecuritiesData $data){
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
     * @param cOmniscientSecuritiesData $data
     */
    protected function FillSecuritiesTable($crmid, cOmniscientSecuritiesData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;
        //We need to make sure the keys are checking the same thing, so make them upper case
        $swap = array_change_key_case(array_flip($this->symbol_replacements), CASE_UPPER);//Flip the replacements because we want CRM symbols
        if(array_key_exists(strtoupper($data->security_symbol), $swap))//If we have a match, then we need to change the security symbol
            $params[] = $swap[$data->security_symbol];
        else
            $params[] = $data->security_symbol;

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
    protected function FillSecuritiesCFTable($crmid, cOmniscientSecuritiesData $data){
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
}
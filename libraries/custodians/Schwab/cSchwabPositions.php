<?php
require_once("libraries/custodians/cCustodian.php");


class cSchwabPositionsData{
    public $symbol, $account_number, $account_type, $long_short, $quantity, $date, $filename, $original_symbol, $record_type, $custodian_id,
           $master_account_number, $master_account_name, $business_date, $product_code, $product_category_code, $tax_code,
           $legacy_security_type, $ticker_symbol, $industry_ticker_symbol, $cusip, $schwab_security_number, $item_issue_id,
           $rule_set_suffix_id, $isin, $sedol, $options_display_symbol, $security_description_line1, $security_description_line2,
           $security_description_line3, $security_description_line4, $underlying_ticker_symbol, $underlying_industry_ticker_symbol,
           $underlying_cusip, $underlying_schwab_security, $underlying_item_issue_id, $underlying_rule_set_suffix_id, $underlying_isin,
           $underlying_sedol, $money_market_code, $dividend_reinvest, $capital_gains_reinvest, $closing_price, $security_price_update_date,
           $quantity_settled_and_unsettled, $long_short_indicator, $market_value_settled_and_unsettled, $accounting_rule_code,
           $quantity_settled, $quantity_unsettled_long, $quantity_unsettled_short, $version_marker1, $tips_factor, $asset_backed_factor,
           $version_marker2, $closing_price_unfactored, $factor, $factor_date, $file_date, $insert_date, $omni_base_asset_class;

    public function __construct($data){
        $this->symbol = $data['symbol'];
        $this->account_number = $data['account_number'];
        $this->account_type = $data['account_type'];
        $this->long_short = $data['long_short'];
        $this->quantity = $data['quantity'];
        $this->date = $data['date'];
        $this->filename = $data['filename'];
        $this->original_symbol = $data['original_symbol'];
        $this->record_type = $data['record_type'];
        $this->custodian_id = $data['custodian_id'];
        $this->master_account_number = $data['master_account_number'];
        $this->master_account_name = $data['master_account_name'];
        $this->business_date = $data['business_date'];
        $this->product_code = $data['product_code'];
        $this->product_category_code = $data['product_category_code'];
        $this->tax_code = $data['tax_code'];
        $this->legacy_security_type = $data['legacy_security_type'];
        $this->ticker_symbol = $data['ticker_symbol'];
        $this->industry_ticker_symbol = $data['industry_ticker_symbol'];
        $this->cusip = $data['cusip'];
        $this->schwab_security_number = $data['schwab_security_number'];
        $this->item_issue_id = $data['item_issue_id'];
        $this->rule_set_suffix_id = $data['rule_set_suffix_id'];
        $this->isin = $data['isin'];
        $this->sedol = $data['sedol'];
        $this->options_display_symbol = $data['options_display_symbol'];
        $this->security_description_line1 = $data['security_description_line1'];
        $this->security_description_line2 = $data['security_description_line2'];
        $this->security_description_line3 = $data['security_description_line3'];
        $this->security_description_line4 = $data['security_description_line4'];
        $this->underlying_ticker_symbol = $data['underlying_ticker_symbol'];
        $this->underlying_industry_ticker_symbol = $data['underlying_industry_ticker_symbol'];
        $this->underlying_cusip = $data['underlying_cusip'];
        $this->underlying_schwab_security = $data['underlying_schwab_security'];
        $this->underlying_item_issue_id = $data['underlying_item_issue_id'];
        $this->underlying_rule_set_suffix_id = $data['underlying_rule_set_suffix_id'];
        $this->underlying_isin = $data['underlying_isin'];
        $this->underlying_sedol = $data['underlying_sedol'];
        $this->money_market_code = $data['money_market_code'];
        $this->dividend_reinvest = $data['dividend_reinvest'];
        $this->capital_gains_reinvest = $data['capital_gains_reinvest'];
        $this->closing_price = $data['closing_price'];
        $this->security_price_update_date = $data['security_price_update_date'];
        $this->quantity_settled_and_unsettled = $data['quantity_settled_and_unsettled'];
        $this->long_short_indicator = $data['long_short_indicator'];
        $this->market_value_settled_and_unsettled = $data['market_value_settled_and_unsettled'];
        $this->accounting_rule_code = $data['accounting_rule_code'];
        $this->quantity_settled = $data['quantity_settled'];
        $this->quantity_unsettled_long = $data['quantity_unsettled_long'];
        $this->quantity_unsettled_short = $data['quantity_unsettled_short'];
        $this->version_marker1 = $data['version_marker1'];
        $this->tips_factor = $data['tips_factor'];
        $this->asset_backed_factor = $data['asset_backed_factor'];
        $this->version_marker2 = $data['version_marker2'];
        $this->closing_price_unfactored = $data['closing_price_unfactored'];
        $this->factor = $data['factor'];
        $this->factor_date = $data['factor_date'];
        $this->file_date = $data['file_date'];
        $this->insert_date = $data['insert_date'];
        $tmp = $data['market_value_settled_and_unsettled'] / $data['quantity_settled_and_unsettled'];
        $this->last_price = (is_nan($tmp)) ? 1 : $tmp;
    }
}

/**
 * Class cSchwabPortfolios
 * This class allows the pulling of data from the custodian database
 */
class cSchwabPositions extends cCustodian {
    use tPositions;
    private $positions_data;//Holds both personal and balance information
    private $symbol_replacements;//Holds key value pairing for replacing symbols.  IE:  "SCHWABCASH" => "Cash" will replace "SCHWABCASH" from the CRM with "Cash" while checking if it exists or not
    protected $columns;

    /**
     * cSchwabPortfolios constructor.
     * @param string $custodian_name
     * @param string $database
     * @param string $module
     * @param string $positions_table
     * @param string $table (REFERS TO BALANCE TABLE)
     */
    public function __construct(string $custodian_name, string $database, string $module,
                                string $portfolio_table, string $positions_table, array $rep_codes, array $symbol_replacements,
                                $auto_setup = true, $columns=array("*")){
        $this->name = $custodian_name;
        $this->database = $database;
        $this->module = $module;
        $this->portfolio_table = $portfolio_table;
        $this->table = $positions_table;
        $this->symbol_replacements = $symbol_replacements;
        $this->columns = $columns;
        if(!empty($rep_codes)) {
            $this->SetRepCodes($rep_codes);
            if($auto_setup) {
                $this->GetPositionsData();
                $this->SetupPositionComparisons();
            }
        }
    }

    /**
     * Returns an associative array of all accounts and their positions
     * @param string $table
     * @param null $date
     * @return mixed
     */
    public function GetPositionsData($date=null, $group_by_symbol=false){
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

        if($group_by_symbol)
            $group_by_symbol = " GROUP BY SYMBOL ";

        $query = "DROP TABLE IF EXISTS BeforeMapping";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE BeforeMapping
                  SELECT {$fields} FROM {$this->database}.{$this->table}
                  WHERE account_number IN ({$questions}) AND DATE = ? {$group_by_symbol}";
        $adb->pquery($query, $params, true);

        $query = "UPDATE BeforeMapping bm
                  JOIN {$this->database}.schwab_remap_securities map ON bm.symbol = map.symbol AND bm.security_type = map.security_type 
                  SET bm.symbol = map.new_symbol, bm.security_type = map.new_security_type";
        $adb->pquery($query, array());

        $query = "SELECT {$fields} FROM BeforeMapping";
        $result = $adb->pquery($query, array(), true);

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

    public function GetSymbolReplacements(){
        return $this->symbol_replacements;
    }

    /**
     * This checks the symbols passed in against the mapping table.  If it finds a match, it adds the "new symbol" to the list as well.
     * An example here is Cash to TDCASH.  Cash may very well be valid in itself, but it could also be TDCASH if it is of type MF.  This code
     * will pull in both Cash the EQ AND Cash the MF so when we request data for the symbols from securities for example, it will pull both info
     * in
     * @param $symbols
     * @return mixed
     */
    public function GetRemappedSymbols($symbols){
        global $adb;
        $query = "SELECT UPPER(symbol) AS symbol, UPPER(new_symbol) AS new_symbol FROM {$this->database}.schwab_remap_securities";
        $result = $adb->pquery($query, array(), true);

        if($adb->num_rows($result) > 0){
            while($r = $adb->fetchByAssoc($result)){
                if(!empty($symbols[$r['symbol']])){
                    $symbols[$r['new_symbol']] = $r['new_symbol'];
                }
            }
        }
        return $symbols;
    }

    /**
     * Using the cSchwabPositionsData class, create the portfolios.  Used with a pre-filled in cSchwabPortfolioData class (done manually)
     * @param cSchwabPortfolioData $data
     * @throws Exception
     */
    public function CreateNewPositionUsingcSchwabPositionsData(cSchwabPositionsData $data){
        if(!$this->DoesPositionExistInCRM($data->account_number, $data->symbol)) {//If the account number doesn't exist yet, create it
            $crmid = $this->UpdateEntitySequence();
            $owner = $this->GetAccountOwnerFromAccountNumber($data->account_number);

            $this->FillEntityTable($crmid, $owner, $data);
            $this->FillPositionTable($crmid, $data);
            $this->FillPositionCFTable($crmid, $data);
        }
    }

    /**
     * Auto creates the position's based on the data loaded into the $positions_data member.  If the position exists in this data, it will be created
     * @param array $account_numbers
     */
    public function CreateNewPositionsFromPositionData(array $missing_account_data){
        if(!empty($missing_account_data)) {
            foreach ($missing_account_data AS $k => $v) {
                foreach ($v AS $a => $position) {
                    $data = $this->positions_data[$k][$a];
                    if (!empty($data)) {
                        $tmp = new cSchwabPositionsData($data);
                        $this->CreateNewPositionUsingcSchwabPositionsData($tmp);
                    }
                }
            }
        }
    }

    /**
     * Create the new entity in the crmentity table
     * @param $crmid
     * @param $owner
     * @param cSchwabPositionsData $data
     */
    protected function FillEntityTable($crmid, $owner, cSchwabPositionsData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;
        $params[] = 1;
        $params[] = $owner;
        $params[] = 1;
        $params[] = 'PositionInformation';
        $params[] = $data->symbol;
        $query = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, createdtime, modifiedtime, label)
                  VALUES (?, ?, ?, ?, ?, NOW(), NOW(), ?)";
        $adb->pquery($query, $params, true);
    }

    /**
     * Creates data in the vtiger_positioninformation table
     * @param $crmid
     * @param cSchwabPositionsData $data
     */
    protected function FillPositionTable($crmid, cSchwabPositionsData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;
        $params[] = $data->account_number;
        $swap = array_flip($this->symbol_replacements);//Flip the replacements because we want CRM symbols
        if(array_key_exists($data->symbol, $swap))
            $params[] = $swap[$data->symbol];
        else
            $params[] = $data->symbol;
        $params[] = $data->security_description_line1;
        $params[] = $data->quantity_settled_and_unsettled;
        $params[] = $data->market_value_settled_and_unsettled;
        $params[] = $data->last_price;

        $questions = generateQuestionMarks($params);
        $query = "INSERT INTO vtiger_positioninformation (positioninformationid, account_number, security_symbol, description, quantity,
                                                          current_value, last_price)
                  VALUES ({$questions})";
        $adb->pquery($query, $params, true);
    }

    /**
     * Creates data in the vtiger_positioninformationcf table
     * @param $crmid
     * @param cSchwabPositionsData $data
     */
    protected function FillPositionCFTable($crmid, cSchwabPositionsData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;
        $params[] = $data->security_price_update_date;
        $params[] = $data->filename;
        $params[] = 'SCHWAB';
        $params[] = $data->security_type;
        $params[] = $data->omni_base_asset_class;

        $questions = generateQuestionMarks($params);
        $query = "INSERT INTO vtiger_positioninformationcf (positioninformationid, last_update, custodian_source, custodian, security_type,
                                                            base_asset_class)
                  VALUES ({$questions})";
        $adb->pquery($query, $params, true);
    }

    /**
     * Auto updates the position's based on the data loaded into the $position_data member.
     * @param array $account_numbers
     */
    public function UpdatePositionsFromPositionsData(array $position_account_data){
        if(!empty($position_account_data)) {
            foreach ($position_account_data AS $k => $v) {
                $this->ResetAccountPositions($k);
                foreach ($v AS $a => $position) {
                    $data = $this->positions_data[$k][$a];
                    if (!empty($data)) {
                        $tmp = new cSchwabPositionsData($data);
                        $this->UpdatePositionsUsingcSchwabPositionsData($tmp);
                    }
                }
                PortfolioInformation_GlobalSummary_Model::CalculateAllAccountAssetAllocationValuesForAccount(array($k));
            }
        }
    }

    /**
     * Update the position in the CRM using the cSchwabPositionsData class
     * @param cSchwabPositionsData $data
     */
    public function UpdatePositionsUsingcSchwabPositionsData(cSchwabPositionsData $data){
        global $adb;
        $params = array();
        $params[] = $data->quantity_settled_and_unsettled;
        $params[] = $data->market_value_settled_and_unsettled;
        $params[] = $data->last_price;
        $params[] = $data->date;
        $params[] = "SCHWAB";
        $params[] = $data->account_number;
        $params[] = $data->symbol;

        $query = "UPDATE vtiger_positioninformation p 
                  JOIN vtiger_positioninformationcf cf USING (positioninformationid)
                  LEFT JOIN vtiger_modsecurities m ON m.security_symbol = p.security_symbol
                  LEFT JOIN vtiger_modsecuritiescf mcf USING (modsecuritiesid)
                  SET p.quantity = ?, p.current_value = ?, p.last_price = ?,
                      cf.last_update = ?, cf.custodian_source = ?, cf.security_type = m.securitytype, cf.base_asset_class = mcf.aclass
                  WHERE account_number = ? AND p.security_symbol = ?";
        $adb->pquery($query, $params, true);
    }

    static public function GetSymbolListFromCustodian(array $account_numbers, $max_only=true){
        global $adb;
        $questions = generateQuestionMarks($account_numbers);

        $query = "SELECT symbol 
                  FROM custodian_omniscient.custodian_positions_schwab 
                  WHERE account_number IN ({$questions}) 
                  AND date = (SELECT MAX(date) FROM custodian_omniscient.custodian_positions_schwab WHERE account_number IN ({$questions}))
                  GROUP BY symbol";

        $result = $adb->pquery($query, array($account_numbers, $account_numbers), true);
        if($adb->num_rows($result) > 0){
            $symbols = array();
            while($v = $adb->fetchByAssoc($result)){
                $symbols[] = $v['symbol'];
            }
            return $symbols;
        }
        return null;
    }

    /**
     * Return cash value information for passed in account number.  This is needed because Schwab doesn't actually give us cash as a position...
     * And why would they when everyone else does.  This also writes the balance date value as position in Schwab if it doesn't already exist
     * @param String $account_number
     * @param null $date_override
     * @return array|mixed|null
     */
    static public function GetCashValueForAccount(String $account_number, $date_override = null){
        global $adb;
        $params = array();

        if($date_override == null)
            $date = 'lpd.last_position_date';//Use the latest date
        else {
            $date = '?';
            $params[] = $date_override;
        }
        $params[] = $account_number;

        $query = "SELECT s.account_number, '\$CASH' AS symbol, 'CASH' AS account_type, (s.margin_balance + s.net_credit_debit) AS quantity, as_of_date, 
                         '\$CASH' AS ticker_symbol, '\$CASH' AS industry_ticker_symbol, '\$CASH' AS cusip, 'Free Cash' AS description, 1 AS price, 
                         NOW() AS security_price_update_date, (s.margin_balance + s.net_credit_debit) AS quantity_settled_and_unsettled, 
                         (s.margin_balance + s.net_credit_debit) AS market_value_settled_and_unsettled, 'CASH' AS accounting_rule_code,
                         NOW() AS insert_date, cf.production_number
                  FROM custodian_omniscient.custodian_balances_schwab s
                  JOIN vtiger_portfolioinformation p ON p.account_number = s.account_number
                  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
                  JOIN custodian_omniscient.latestpositiondates lpd ON lpd.rep_code = cf.production_number
                  WHERE s.as_of_date = {$date}
                  AND s.account_number = ?";
        $result = $adb->pquery($query, $params);
        if($adb->num_rows($result) > 0){
            $query = "INSERT INTO custodian_omniscient.custodian_positions_schwab (account_number, symbol, account_type, quantity, date, ticker_symbol, 
						                                      industry_ticker_symbol, cusip, security_description_line1, 
						                                      closing_price, security_price_update_date, quantity_settled_and_unsettled, 
						                                      market_value_settled_and_unsettled, accounting_rule_code, insert_date)
                      VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                      ON DUPLICATE KEY UPDATE account_type = VALUES(account_type)";
            while($v = $adb->fetchByAssoc($result)){
                $adb->pquery($query, array($v['account_number'], $v['symbol'], $v['account_type'], $v['quantity'], $v['as_of_date'], $v['ticker_symbol'],
                                           $v['industry_ticker_symbol'], $v['cusip'], $v['security_description_line1'], $v['closing_price'], $v['security_price_update_date'],
                                           $v['quantity_settled_and_unsettled'], $v['market_value_settled_and_unsettled'], $v['accounting_rule_code'], $v['insert_date']), true);
                return $v;
            }
        }
        return null;
    }

    static public function ExtraCashPositionsSchwabAccounts(array $account_number){
        foreach($account_number AS $k => $v){
            if(!self::DoesPositionExistInCRM($v, '$CASH')){
                $position = Vtiger_Record_Model::getCleanInstance("PositionInformation");
                $data = $position->getData();
                $data['security_symbol'] = '$CASH';
                $data['description'] = "Free Cash";
                $data['account_number'] = $v;
                $position->setData($data);
                $position->set('mode', 'create');
                $position->save();
            }
        }
    }

    static public function UpdateAllCRMPositionsAtOnceForAccounts(array $account_number){
        global $adb;
        $questions = generateQuestionMarks($account_number);
        $params = array();

        foreach($account_number AS $k => $v){
            $cash = self::GetCashValueForAccount($v);//This returns the cash value or each account, but also creates the cash position in schwab if it doesn't already exist
        }

        $query = "SELECT pos.symbol, pos.account_number, por.account_type, long_short, SUM(pos.quantity) AS quantity, date, pos.filename, original_symbol, pos.record_type, 
                         pos.custodian_id, pos.master_account_number, pos.master_account_name, pos.business_date, product_code, product_category_code, 
                         pos.tax_code, legacy_security_type, ticker_symbol, pos.industry_ticker_symbol, pos.cusip, schwab_security_number, pos.item_issue_id, 
                         rule_set_suffix_id, pos.isin, pos.sedol, pos.options_display_symbol, security_description_line1, security_description_line2, 
                         security_description_line3, security_description_line4, pos.underlying_ticker_symbol, pos.underlying_industry_ticker_symbol, 
                         pos.underlying_cusip, underlying_schwab_security, underlying_item_issue_id, underlying_rule_set_suffix_id, pos.underlying_isin, 
                         underlying_sedol, money_market_code, dividend_reinvest, capital_gains_reinvest, pos.closing_price, 
                         security_price_update_date, SUM(quantity_settled_and_unsettled) AS quantity_settled_and_unsettled, long_short_indicator, 
                         SUM(market_value_settled_and_unsettled) AS market_value_settled_and_unsettled, accounting_rule_code, SUM(quantity_settled) AS quantity_settled, SUM(quantity_unsettled_long) AS quantity_unsettled_long, 
                         SUM(quantity_unsettled_short) AS quantity_unsettled_short, version_marker1, pos.tips_factor, pos.asset_backed_factor, version_marker2, 
                         pos.closing_price_unfactored, pos.factor, pos.factor_date, pos.file_date, pos.insert_date, pinfo.positioninformationid, ms.securitytype, 
                         mscf.aclass
                  FROM custodian_omniscient.custodian_positions_schwab pos
                  JOIN custodian_omniscient.custodian_portfolios_schwab por ON por.account_number = pos.account_number
                  JOIN custodian_omniscient.latestpositiondates lpd ON lpd.rep_code = por.rep_code
                  JOIN vtiger_positioninformation pinfo ON pinfo.account_number = pos.account_number AND pinfo.security_symbol = pos.symbol
                  JOIN vtiger_modsecurities ms ON ms.security_symbol = pinfo.security_symbol
                  JOIN vtiger_modsecuritiescf mscf USING (modsecuritiesid)
                  WHERE date = lpd.last_position_date
                  AND pos.account_number IN ({$questions})
                  GROUP BY pos.account_number, pos.symbol";
        $result = $adb->pquery($query, array($account_number));

        if($adb->num_rows($result) > 0){
            $query = "UPDATE vtiger_positioninformation p
                      JOIN vtiger_positioninformationcf pcf ON pcf.positioninformationid = p.positioninformationid 
                      LEFT JOIN vtiger_modsecurities m ON p.security_symbol = m.security_symbol 
                      LEFT JOIN vtiger_modsecuritiescf mcf ON m.modsecuritiesid = mcf.modsecuritiesid 
                      SET p.quantity = ?, 
                          p.current_value = ?, 
                          p.description = ?, 
                          p.last_price = ?, 
                          pcf.last_update = ?, 
                          pcf.custodian_source = ?, 
                          pcf.custodian = ?, 
                          pcf.security_type = ?, 
                          pcf.base_asset_class = ?
                      WHERE p.positioninformationid = ?";
            while($v = $adb->fetchByAssoc($result)){
                $adb->pquery($query, array($v['quantity'], $v['market_value_settled_and_unsettled'], $v['security_description_line1'],
                                           $v['closing_price'], $v['date'], $v['filename'], 'Schwab', $v['securitytype'], $v['aclass'],
                                           $v['positioninformationid']));
            }
        }
    }

    static public function CreateNewPositionsForAccounts(array $account_number)
    {
        global $adb;
        $questions = generateQuestionMarks($account_number);

        self::ExtraCashPositionsSchwabAccounts($account_number);

        $query = "SELECT pos.symbol, pos.account_number, security_description_line1 AS description 
                  FROM custodian_omniscient.custodian_positions_schwab pos 
                  JOIN custodian_omniscient.custodian_portfolios_schwab por ON por.account_number = pos.account_number
                  JOIN custodian_omniscient.latestpositiondates lpd ON lpd.rep_code = por.rep_code
                  WHERE pos.account_number IN ({$questions}) 
                  AND (pos.account_number, pos.symbol) NOT IN (SELECT account_number, security_symbol 
                                                       FROM vtiger_positioninformation 
                                                       WHERE security_symbol != '' 
                                                       AND account_number IN ({$questions})) 
                  AND pos.date = lpd.last_position_date 
                  AND pos.symbol != '' 
                  GROUP BY pos.symbol, pos.account_number";
        $result = $adb->pquery($query, array($account_number, $account_number), true);

        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $v['crmid'] = $adb->getUniqueID("vtiger_crmentity");

                $query = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, createdtime, modifiedtime, label)
                          VALUES(?, 1, 1, 1, 'PositionInformation', NOW(), NOW(), ?)";
                $adb->pquery($query, array($v['crmid'], $v['symbol']), true);

                $query = "INSERT INTO vtiger_positioninformation (positioninformationid, security_symbol, description, account_number)
                          VALUES(?, ?, ?, ?)";
                $adb->pquery($query, array($v['crmid'], $v['symbol'], $v['description'], $v['account_number']), true);

                $query = "INSERT INTO vtiger_positioninformationcf (positioninformationid)
                          VALUES(?)";
                $adb->pquery($query, array($v['crmid']), true);
            }
        }
    }

    static public function GetClosestPositionDate(array $account_numbers, $date){
        global $adb;

        if(empty($account_numbers))
            return null;

        $questions = generateQuestionMarks($account_numbers);

        $query = "SELECT MAX(date) AS date
                  FROM custodian_omniscient.custodian_positions_schwab 
                  WHERE account_number IN ({$questions})
                  AND date <= ?";
        $result = $adb->pquery($query, array($account_numbers, $date));

        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'date');

        return null;
    }

    /**
     * This is extremely specific.  This is not <= date, but requires a specific date to check against.  Request a saturday or holiday, you won't
     * get a response
     * @param array $account_number
     * @param $date
     * @return array|null
     */
    static public function GetPositionDataAsOfDate(array $account_number, $date){
        global $adb;
        $questions = generateQuestionMarks($account_number);
        $date = self::GetClosestPositionDate($account_number, $date);


        $query = "SELECT p.symbol, SUM(market_value_settled_and_unsettled) AS market_value, aclass, security_sector,
                         cf.cusip, m.security_name, account_number, quantity, p.closing_price, securitytype
                  FROM custodian_omniscient.custodian_positions_schwab p
                  JOIN vtiger_modsecurities m ON m.security_symbol = p.symbol
                  JOIN vtiger_modsecuritiescf cf USING (modsecuritiesid)
                  WHERE account_number IN ({$questions})
                  AND date = (SELECT MAX(date) FROM custodian_omniscient.custodian_positions_schwab 
				  WHERE date = ? AND account_number IN ({$questions}))
                  GROUP BY security_symbol, aclass, security_sector";
		$result = $adb->pquery($query, array($account_number, $date, $account_number));
		
        if($adb->num_rows($result) > 0){
            $data = array();
            while($v = $adb->fetchByAssoc($result)){
                $data[$v['account_number']][] = $v;
            }
            return $data;
        }

        return null;
    }
}

/*
        $query = "SELECT account_number, '\$CASH' AS symbol, 'CASH' AS account_type, (margin_balance + net_credit_debit) AS quantity, as_of_date,
                         '\$CASH' AS ticker_symbol, '\$CASH' AS industry_ticker_symbol, '\$CASH' AS cusip, 'Free Cash' AS description, 1 AS price,
                         NOW() AS security_price_update_date, (margin_balance + net_credit_debit) AS quantity_settled_and_unsettled,
                         (margin_balance + net_credit_debit) AS market_value_settled_and_unsettled, 'CASH' AS accounting_rule_code,
                         NOW() AS insert_date, cf.production_number
                  FROM custodian_omniscient.custodian_balances_schwab s
                  JOIN vtiger_portfolioinformation p ON p.account_number = s.account_number
                  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
                  JOIN custodian_omniscient.latestpositiondates lpd ON lpd.rep_code = cf.production_number
                  WHERE s.as_of_date = lpd.last_position_date
                  AND s.account_number IN ({$questions})";
 */
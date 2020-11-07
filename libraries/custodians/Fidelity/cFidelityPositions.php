<?php
require_once("libraries/custodians/cCustodian.php");

class cFidelityPositionsData{
    public  $account_number, $account_type, $cusip, $symbol, $trade_date_quantity, $settle_date_quantity, $close_price, $description,
            $as_of_date, $current_factor, $original_face_amount, $factored_clean_price, $factored_indicator, $security_type_code,
            $option_symbol, $registered_rep_1, $registered_rep_2, $filename, $zero_percent_shares, $one_percent_shares, $two_percent_shares,
            $three_percent_shares, $account_source, $account_type_description, $accrual_amount, $asset_class_type_code,
            $capital_gain_instruction_long_term, $capital_gain_instruction_short_term, $clean_price, $closing_market_value,
            $core_fund_indicator, $cost, $cost_basis_indicator, $cost_basis_per_share, $cost_method, $current_face, $custom_short_name,
            $dividend_instruction, $exchange, $fbsi_short_name, $floor_symbol, $fund_number, $host_type_code, $lt_shares, $maturity_date,
            $money_source_id, $money_source, $operation_code, $plan_name, $plan_number, $pool_id, $position_type, $pricing_factor,
            $primary_account_owner, $product_name, $product_type, $registration, $security_asset_class, $security_group, $security_id,
            $security_type_description, $st_shares, $unrealized_gain_loss_amount, $unsettled_cash, $file_date, $insert_date;

    public function __construct($data){
        $this->account_number = $data['account_number'];
        $this->account_type = $data['account_type'];
        $this->cusip = $data['cusip'];
        $this->symbol = $data['symbol'];
        $this->trade_date_quantity = $data['trade_date_quantity'];
        $this->settle_date_quantity = $data['settle_date_quantity'];
        $this->close_price = $data['close_price'];
        $this->description = $data['description'];
        $this->as_of_date = $data['as_of_date'];
        $this->current_factor = $data['current_factor'];
        $this->original_face_amount = $data['original_face_amount'];
        $this->factored_clean_price = $data['factored_clean_price'];
        $this->factored_indicator = $data['factored_indicator'];
        $this->security_type_code = $data['security_type_code'];
        $this->option_symbol = $data['option_symbol'];
        $this->registered_rep_1 = $data['registered_rep_1'];
        $this->registered_rep_2 = $data['registered_rep_2'];
        $this->filename = $data['filename'];
        $this->zero_percent_shares = $data['zero_percent_shares'];
        $this->one_percent_shares = $data['one_percent_shares'];
        $this->two_percent_shares = $data['two_percent_shares'];
        $this->three_percent_shares = $data['three_percent_shares'];
        $this->account_source = $data['account_source'];
        $this->account_type_description = $data['account_type_description'];
        $this->accrual_amount = $data['accrual_amount'];
        $this->asset_class_type_code = $data['asset_class_type_code'];
        $this->capital_gain_instruction_long_term = $data['capital_gain_instruction_long_term'];
        $this->capital_gain_instruction_short_term = $data['capital_gain_instruction_short_term'];
        $this->clean_price = $data['clean_price'];
        $this->closing_market_value = $data['closing_market_value'];
        $this->core_fund_indicator = $data['core_fund_indicator'];
        $this->cost = $data['cost'];
        $this->cost_basis_indicator = $data['cost_basis_indicator'];
        $this->cost_basis_per_share = $data['cost_basis_per_share'];
        $this->cost_method = $data['cost_method'];
        $this->current_face = $data['current_face'];
        $this->custom_short_name = $data['custom_short_name'];
        $this->dividend_instruction = $data['dividend_instruction'];
        $this->exchange = $data['exchange'];
        $this->fbsi_short_name = $data['fbsi_short_name'];
        $this->floor_symbol = $data['floor_symbol'];
        $this->fund_number = $data['fund_number'];
        $this->host_type_code = $data['host_type_code'];
        $this->lt_shares = $data['lt_shares'];
        $this->maturity_date = $data['maturity_date'];
        $this->money_source_id = $data['money_source_id'];
        $this->money_source = $data['money_source'];
        $this->operation_code = $data['operation_code'];
        $this->plan_name = $data['plan_name'];
        $this->plan_number = $data['plan_number'];
        $this->pool_id = $data['pool_id'];
        $this->position_type = $data['position_type'];
        $this->pricing_factor = $data['pricing_factor'];
        $this->primary_account_owner = $data['primary_account_owner'];
        $this->product_name = $data['product_name'];
        $this->product_type = $data['product_type'];
        $this->registration = $data['registration'];
        $this->security_asset_class = $data['security_asset_class'];
        $this->security_group = $data['security_group'];
        $this->security_id = $data['security_id'];
        $this->security_type_description = $data['security_type_description'];
        $this->st_shares = $data['st_shares'];
        $this->unrealized_gain_loss_amount = $data['unrealized_gain_loss_amount'];
        $this->unsettled_cash = $data['unsettled_cash'];
        $this->file_date = $data['file_date'];
        $this->insert_date = $data['insert_date'];
    }
}

/**
 * Class cFidelityPortfolios
 * This class allows the pulling of data from the custodian database
 */
class cFidelityPositions extends cCustodian {
    use tPositions;
    private $positions_data;//Holds both personal and balance information
    private $symbol_replacements;//Holds key value pairing for replacing symbols.  IE:  "FIDELITYCASH" => "Cash" will replace "FIDELITYCASH" from the CRM with "Cash" while checking if it exists or not
    protected $columns;

    /**
     * cFidelityPortfolios constructor.
     * @param string $custodian_name
     * @param string $database
     * @param string $module
     * @param string $positions_table
     * @param string $table (REFERS TO BALANCE TABLE)
     */
    public function __construct(string $custodian_name, string $database, string $module,
                                string $portfolio_table, string $positions_table, array $rep_codes, array $symbol_replacements,
                                $auto_setup = true, $columns=array("*")){
        $this->CreateCashPosition();
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
                $this->GetPositionsData(null, true);
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
            $date = $this->GetLatestPositionsDate("as_of_date");
        $params[] = $date;

        if($group_by_symbol)
            $group_by_symbol = " GROUP BY account_number, symbol";

        $query = "DROP TABLE IF EXISTS BeforeMapping";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE BeforeMapping
                  SELECT {$fields} FROM {$this->database}.{$this->table}
                  WHERE account_number IN ({$questions}) AND as_of_date = ? {$group_by_symbol}";
        $adb->pquery($query, $params, true);

        $query = "UPDATE BeforeMapping bm
                  JOIN {$this->database}.fidelity_remap_securities map ON bm.symbol = map.symbol AND bm.security_type = map.security_type 
                  SET bm.symbol = map.new_symbol, bm.security_type = map.new_security_type";
        $adb->pquery($query, array());

        $query = "SELECT {$fields} FROM BeforeMapping {$group_by_symbol}";
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
     * An example here is Cash to FCASH.  Cash may very well be valid in itself, but it could also be FCASH if it is of type MF.  This code
     * will pull in both Cash the EQ AND Cash the MF so when we request data for the symbols from securities for example, it will pull both info
     * in
     * @param $symbols
     * @return mixed
     */
    public function GetRemappedSymbols($symbols){
        global $adb;
        $query = "SELECT UPPER(symbol) AS symbol, UPPER(new_symbol) AS new_symbol FROM {$this->database}.fidelity_remap_securities";
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
     * Using the cFidelityPositionsData class, create the portfolios.  Used with a pre-filled in cFidelityPortfolioData class (done manually)
     * @param cFidelityPortfolioData $data
     * @throws Exception
     */
    public function CreateNewPositionUsingcFidelityPositionsData(cFidelityPositionsData $data){
        if(!$this->DoesPositionExistInCRM($data->account_number, $data->symbol)) {//If the account number doesn't exist yet, create it
            $crmid = $this->UpdateEntitySequence();
            $owner = $this->GetAccountOwnerFromAccountNumber($data->account_number);

            $this->FillEntityTable($crmid, $owner, $data);
            $this->FillPositionTable($crmid, $data);
            $this->FillPositionCFTable($crmid, $data);
        }
    }

    /*This may be better suited to do during parsing!*/
    public function CreateCashPosition(){
        global $adb;
        $query = "CALL EXTRA_CASH_POSITIONS_FIDELITY();";
        $adb->pquery($query, array());
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
                        $tmp = new cFidelityPositionsData($data);
                        $this->CreateNewPositionUsingcFidelityPositionsData($tmp);
                    }
                }
            }
        }
    }

    /**
     * Create the new entity in the crmentity table
     * @param $crmid
     * @param $owner
     * @param cFidelityPositionsData $data
     */
    protected function FillEntityTable($crmid, $owner, cFidelityPositionsData $data){
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
     * @param cFidelityPositionsData $data
     */
    protected function FillPositionTable($crmid, cFidelityPositionsData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;
        $params[] = $data->account_number;
        $swap = array_flip($this->symbol_replacements);//Flip the replacements because we want CRM symbols
        if(array_key_exists($data->symbol, $swap))
            $params[] = $swap[$data->symbol];
        else
            $params[] = $data->symbol;
        $params[] = $data->account_number;

        $questions = generateQuestionMarks($params);
        $query = "INSERT INTO vtiger_positioninformation (positioninformationid, account_number, security_symbol, description)
                  VALUES ({$questions})";
        $adb->pquery($query, $params, true);
    }

    /**
     * Creates data in the vtiger_positioninformationcf table
     * @param $crmid
     * @param cFidelityPositionsData $data
     */
    protected function FillPositionCFTable($crmid, cFidelityPositionsData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;

        $questions = generateQuestionMarks($params);
        $query = "INSERT INTO vtiger_positioninformationcf (positioninformationid)
                  VALUES ({$questions})";
        $adb->pquery($query, $params, true);
    }

    public function ResetAccountPositions($account_number){
        global $adb;
        $params = array();
        $params[] = $account_number;

        $query = "UPDATE vtiger_positioninformation p 
                  JOIN vtiger_positioninformationcf pcf ON pcf.positioninformationid = p.positioninformationid 
                  SET p.quantity = 0, p.current_value = 0 
                  WHERE account_number = ?";
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
                        $tmp = new cFidelityPositionsData($data);
                        $this->UpdatePositionsUsingcFidelityPositionsData($tmp);
                    }
                }
            }
        }
    }

    /**
     * Update the position in the CRM using the cFidelityPositionsData class
     * @param cFidelityPositionsData $data
     */
    public function UpdatePositionsUsingcFidelityPositionsData(cFidelityPositionsData $data){
        global $adb;
        $params = array();
        $params[] = $data->trade_date_quantity;
        $params[] = $data->closing_market_value;
        $params[] = $data->as_of_date;
        $params[] = $data->unrealized_gain_loss_amount;
        $params[] = $data->cost;
        $params[] = $data->unrealized_gain_loss_amount;
        $params[] = $data->cost;
        $params[] = $data->filename;
        $params[] = $data->symbol;
        $params[] = $data->account_number;

        $query = "UPDATE vtiger_positioninformation p 
                  JOIN vtiger_positioninformationcf pcf ON pcf.positioninformationid = p.positioninformationid
                  JOIN vtiger_modsecurities m ON p.security_symbol = m.security_symbol
                  JOIN vtiger_modsecuritiescf mcf ON m.modsecuritiesid = mcf.modsecuritiesid
                  SET  p.quantity = ?, p.current_value = ?, p.description = m.security_name, 
                       p.last_price = m.security_price * mcf.security_price_adjustment, pcf.last_update = ?, 
                       pcf.security_type = m.securitytype, pcf.base_asset_class = mcf.aclass, pcf.custodian = 'Fidelity', 
                       p.unrealized_gain_loss = ?, p.cost_basis = ?, 
                       p.gain_loss_percent = (? / ? * 100), pcf.custodian_source = ?
                  WHERE p.security_symbol = ? AND account_number=?";
        $adb->pquery($query, $params, true);
    }

    public function ManualSetupPositionComparisons(){
        $this->SetupPositionComparisons();
    }

    public function SetAccountNumbers(array $account_numbers)
    {
        parent::SetAccountNumbers($account_numbers); // TODO: Change the autogenerated stub
        $this->GetPositionsData(null, true);
        $this->SetupPositionComparisons();
    }

}
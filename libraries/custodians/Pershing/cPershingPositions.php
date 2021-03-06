<?php
require_once("libraries/custodians/cCustodian.php");

/**
 * Class cPershingPortfolios
 * This class allows the pulling of data from the custodian database
 */
class cPershingPositions extends cCustodian {
    use tPositions;
    private $positions_data;//Holds both personal and balance information
    private $symbol_replacements;//Holds key value pairing for replacing symbols.  IE:  "FIDELITYCASH" => "Cash" will replace "FIDELITYCASH" from the CRM with "Cash" while checking if it exists or not
    protected $columns;

    /**
     * cPershingPortfolios constructor.
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
            $date = $this->GetLatestPositionsDate("as_of_date");
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
                  JOIN {$this->database}.pershing_remap_securities map ON bm.symbol = map.symbol AND bm.security_type = map.security_type 
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
     * Using the cPershingPositionsData class, create the portfolios.  Used with a pre-filled in cPershingPortfolioData class (done manually)
     * @param cPershingPortfolioData $data
     * @throws Exception
     */
    public function CreateNewPositionUsingcPershingPositionsData(cPershingPositionsData $data){
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
                        $tmp = new cPershingPositionsData($data);
                        $this->CreateNewPositionUsingcPershingPositionsData($tmp);
                    }
                }
            }
        }
    }

    /**
     * Create the new entity in the crmentity table
     * @param $crmid
     * @param $owner
     * @param cPershingPositionsData $data
     */
    protected function FillEntityTable($crmid, $owner, cPershingPositionsData $data){
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
     * @param cPershingPositionsData $data
     */
    protected function FillPositionTable($crmid, cPershingPositionsData $data){
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
     * @param cPershingPositionsData $data
     */
    protected function FillPositionCFTable($crmid, cPershingPositionsData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;

        $questions = generateQuestionMarks($params);
        $query = "INSERT INTO vtiger_positioninformationcf (positioninformationid)
                  VALUES ({$questions})";
        $adb->pquery($query, $params, true);
    }

    /**
     * Update the position in the CRM using the cPershingPositionsData class
     * @param cPershingPositionsData $data
     */
    public function UpdatePositionsUsingcPershingPositionsData(cPershingPositionsData $data){
        global $adb;
        $params = array();
        $params[] = $data->quantity_amount_combo;
        $params[] = $data->quantity_amount_combo;
        $params[] = $data->insert_date;
        $params[] = $data->filename;
        $params[] = $data->account_number;
        $params[] = $data->symbol;

#        if($data->account_number == '943475910' AND $data->symbol == '83369EC81') {
#            echo 'here';
#            echo $data->account_number . '<br />';
#            print_r($data);
#            exit;


        $query = "UPDATE vtiger_positioninformation p 
              JOIN vtiger_positioninformationcf cf USING (positioninformationid)
              LEFT JOIN vtiger_modsecurities m ON m.security_symbol = p.security_symbol 
              LEFT JOIN vtiger_modsecuritiescf mcf ON m.modsecuritiesid = mcf.modsecuritiesid
              SET p.quantity = ?, p.current_value = ? * m.security_price * CASE WHEN mcf.security_price_adjustment > 0 
                                                                                THEN mcf.security_price_adjustment ELSE 1 END 
                                                                                * CASE WHEN m.asset_backed_factor > 0 
                                                                                THEN m.asset_backed_factor ELSE 1 END,
              p.description = m.security_name, cf.security_type = m.securitytype, cf.base_asset_class = mcf.aclass, cf.custodian = 'Pershing',
              p.last_price = m.security_price * CASE WHEN mcf.security_price_adjustment > 0 THEN mcf.security_price_adjustment ELSE 1 END,
              cf.last_update = ?, cf.custodian_source = ?
              WHERE account_number = ? AND p.security_symbol = ?";
        $adb->pquery($query, $params, true);
    }
}
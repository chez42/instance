<?php
require_once("libraries/custodians/cCustodian.php");

class cTDPositionsData{
    public $account_number, $symbol, $quantity, $amount, $quantity_amount_combo;//PositionInformation, quantity amount combo is quantity + amount
    public $custodian, $filename;//PositionInformationCF
    public $insert_date;//Entity table

    public function __construct($data){
        $this->account_number = $data['account_number'];
        $this->custodian = 'TD';
        $this->symbol = $data['symbol'];
        $this->quantity = $data['quantity'];
        $this->amount = $data['amount'];
        $this->filename = $data['filename'];
        $this->quantity_amount_combo = $data['quantity'] + $data['amount'];
        $this->insert_date = $data['insert_date'];
    }
}

/**
 * Class cTDPortfolios
 * This class allows the pulling of data from the custodian database
 */
class cTDPositions extends cCustodian {
    use tPositions;
    private $positions_data;//Holds both personal and balance information
    private $symbol_replacements;//Holds key value pairing for replacing symbols.  IE:  "TDCASH" => "Cash" will replace "TDCASH" from the CRM with "Cash" while checking if it exists or not

    /**
     * cTDPortfolios constructor.
     * @param string $custodian_name
     * @param string $database
     * @param string $module
     * @param string $positions_table
     * @param string $table (REFERS TO BALANCE TABLE)
     */
    public function __construct(string $custodian_name, string $database, string $module,
                                string $portfolio_table, string $positions_table, array $rep_codes, array $symbol_replacements){
        $this->name = $custodian_name;
        $this->database = $database;
        $this->module = $module;
        $this->portfolio_table = $portfolio_table;
        $this->table = $positions_table;
        $this->symbol_replacements = $symbol_replacements;
        if(!empty($rep_codes)) {
            $this->SetRepCodes($rep_codes);
            $this->GetPositionsData();
            $this->SetupPositionComparisons();
        }
    }

    /**
     * Returns an associative array of all accounts and their positions
     * @param string $table
     * @param null $date
     * @return mixed
     */
    public function GetPositionsData($date=null){
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

        $query = "SELECT {$fields} FROM {$this->database}.{$this->table} 
                  WHERE account_number IN ({$questions}) AND date = ?";
        $result = $adb->pquery($query, $params, true);

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
     * Using the cTDPositionsData class, create the portfolios.  Used with a pre-filled in cTDPortfolioData class (done manually)
     * @param cTDPortfolioData $data
     * @throws Exception
     */
    public function CreateNewPositionUsingcTDPositionsData(cTDPositionsData $data){
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
    public function CreateNewPositionsFromPositionData(array $account_numbers){
        if(!empty($account_numbers)) {
            foreach ($account_numbers AS $k => $v) {
                foreach ($v AS $a => $position) {
                    $data = $this->positions_data[$k][$a];
                    if (!empty($data)) {
                        $tmp = new cTDPositionsData($data);
                        $this->CreateNewPositionUsingcTDPositionsData($tmp);
                    }
                }
            }
        }
    }

    /**
     * Auto updates the position's based on the data loaded into the $position_data member.
     * @param array $account_numbers
     */
    public function UpdatePositionsFromPositionsData(array $account_numbers){
        if(!empty($account_numbers)) {
            foreach ($account_numbers AS $k => $v) {
                foreach ($v AS $a => $position) {
                    $data = $this->positions_data[$k][$a];
                    if (!empty($data)) {
                        $tmp = new cTDPositionsData($data);
                        $this->UpdatePositionsUsingcTDPositionsData($tmp);
                    }
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
    protected function FillEntityTable($crmid, $owner, cTDPositionsData $data){
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
     * @param cTDPositionsData $data
     */
    protected function FillPositionTable($crmid, cTDPositionsData $data){
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
     * @param cTDPositionsData $data
     */
    protected function FillPositionCFTable($crmid, cTDPositionsData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;

        $questions = generateQuestionMarks($params);
        $query = "INSERT INTO vtiger_positioninformationcf (positioninformationid)
                  VALUES ({$questions})";
        $adb->pquery($query, $params, true);
    }

    /**
     * Update the position in the CRM using the cTDPositionsData class
     * @param cTDPositionsData $data
     */
    public function UpdatePositionsUsingcTDPositionsData(cTDPositionsData $data){
        global $adb;
        $params = array();
        $params[] = $data->quantity_amount_combo;
        $params[] = $data->quantity_amount_combo;
        $params[] = $data->insert_date;
        $params[] = $data->filename;
        $params[] = $data->account_number;
        $params[] = $data->symbol;

        $query = "UPDATE vtiger_positioninformation p 
                  JOIN vtiger_positioninformationcf cf USING (positioninformationid)
                  LEFT JOIN vtiger_modsecurities m ON m.security_symbol = p.security_symbol 
                  LEFT JOIN vtiger_modsecuritiescf mcf ON m.modsecuritiesid = mcf.modsecuritiesid
                  SET p.quantity = ?, p.current_value = ? * m.security_price * CASE WHEN mcf.security_price_adjustment = 0 
                                                                                 THEN 1 ELSE mcf.security_price_adjustment END 
                                                                                    * CASE WHEN m.asset_backed_factor > 0 
                                                                                    THEN m.asset_backed_factor ELSE 1 END,
                  p.description = m.security_name, cf.security_type = m.securitytype, cf.base_asset_class = mcf.aclass, cf.custodian = 'TD',
                  p.last_price = m.security_price * CASE WHEN mcf.security_price_adjustment = 0 THEN 1 ELSE mcf.security_price_adjustment END,
                  cf.last_update = ?, cf.custodian_source = ?
                  WHERE account_number = ? AND p.security_symbol = ?";
        $adb->pquery($query, $params, true);
    }
}
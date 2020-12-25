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
        $this->insert_date = $data['date'];
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
    protected $columns;
    /**
     * cTDPortfolios constructor.
     * @param string $custodian_name
     * @param string $database
     * @param string $module
     * @param string $positions_table
     * @param string $table (REFERS TO BALANCE TABLE)
     * @param auto_setup is used to run the GetPositionsData and SetupPositionComparisons function
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
            $group_by_symbol = " GROUP BY account_number, symbol";

        $query = "DROP TABLE IF EXISTS BeforeMapping";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE BeforeMapping
                  SELECT {$fields} FROM {$this->database}.{$this->table}
                  WHERE account_number IN ({$questions}) AND DATE = ? {$group_by_symbol}";
        $adb->pquery($query, $params, true);

        $query = "UPDATE BeforeMapping bm
                  JOIN {$this->database}.td_remap_securities map ON bm.symbol = map.symbol AND bm.security_type = map.security_type 
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
    public function CreateNewPositionsFromPositionData(array $missing_account_data){
        if(!empty($missing_account_data)) {
            foreach ($missing_account_data AS $k => $v) {
                StatusUpdate::UpdateMessage("TDUPDATER", "Creating Positions for {$k}");
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
    public function UpdatePositionsFromPositionsData(array $position_account_data){
        if(!empty($position_account_data)) {
            foreach ($position_account_data AS $k => $v) {
                $this->ResetAccountPositions($k);
                StatusUpdate::UpdateMessage("TDUPDATER", "Updating Positions for {$k}");
                foreach ($v AS $a => $position) {
                    $data = $this->positions_data[$k][$a];
                    if (isset($data)) {
                        $tmp = new cTDPositionsData($data);
                        $this->UpdatePositionsUsingcTDPositionsData($tmp);
                    }
                }
                StatusUpdate::UpdateMessage("SCHWABUPDATER", "Calculating Asset Allocation For {$k}");
                PortfolioInformation_GlobalSummary_Model::CalculateAllAccountAssetAllocationValuesForAccount(array($k));
                StatusUpdate::UpdateMessage("SCHWABUPDATER", "Finished Calculating Asset Allocation For {$k}");
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
              p.description = m.security_name, cf.security_type = m.securitytype, cf.base_asset_class = CASE WHEN mcf.aclass IS NULL OR mcf.aclass = '' THEN 'Other' ELSE mcf.aclass END, cf.custodian = 'TD',
              p.last_price = m.security_price * CASE WHEN mcf.security_price_adjustment > 0 THEN mcf.security_price_adjustment ELSE 1 END,
              cf.last_update = ?, cf.custodian_source = ?, cf.position_closed = 0
              WHERE account_number = ? AND p.security_symbol = ?";
        $adb->pquery($query, $params, true);
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
        $query = "SELECT UPPER(symbol) AS symbol, UPPER(new_symbol) AS new_symbol FROM {$this->database}.td_remap_securities";
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

    public function SetAccountNumbers(array $account_numbers)
    {
        parent::SetAccountNumbers($account_numbers); // TODO: Change the autogenerated stub
        $this->GetPositionsData();
        $this->SetupPositionComparisons();
    }

    static public function UpdateAllCRMPositionsAtOnce(){
        global $adb;
echo "THIS IS NOT READY YET!!!";
return;
        $query = "DROP TABLE IF EXISTS UpdatePositions";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE UpdatePositions LIKE custodian_omniscient.custodian_positions_td";
        $adb->pquery($query, array());

        $query = "INSERT INTO UpdatePositions 
                  SELECT * FROM custodian_omniscient.custodian_positions_td pos
                  WHERE date=(SELECT MAX(date) FROM custodian_omniscient.custodian_positions_td)";
        $adb->pquery($query, array());

        $query = "UPDATE UpdatePositions SET symbol = 'TDCASH' WHERE symbol = 'Cash'";
        $adb->pquery($query, array());

        $query = "SELECT p.positioninformationid, f.quantity + f.amount AS quantity, (f.quantity + f.amount) * m.security_price * CASE WHEN mcf.security_price_adjustment = 0 THEN 1 ELSE mcf.security_price_adjustment END * CASE WHEN m.asset_backed_factor > 0 THEN m.asset_backed_factor ELSE 1 END AS current_value,
                         sec.description, m.securitytype, mcf.aclass, 'TD' AS custodian, 
                         m.security_price * CASE WHEN mcf.security_price_adjustment = 0 THEN 1 ELSE mcf.security_price_adjustment END AS last_price, 
                         f.date, f.filename
                  FROM UpdatePositions f
                  LEFT JOIN custodian_omniscient.custodian_securities_td sec ON f.symbol = sec.symbol 
                  JOIN vtiger_positioninformation p ON f.symbol = p.security_symbol AND f.account_number = p.account_number 
                  JOIN vtiger_positioninformationcf pcf ON pcf.positioninformationid = p.positioninformationid 
                  LEFT JOIN vtiger_modsecurities m ON p.security_symbol = m.security_symbol 
                  LEFT JOIN vtiger_modsecuritiescf mcf ON m.modsecuritiesid = mcf.modsecuritiesid 
                  WHERE f.account_number = p.account_number";
        $result = $adb->pquery($query, array());

        if($adb->num_rows($result) > 0){
            $query = "UPDATE vtiger_positioninformation p 
                      JOIN vtiger_positioninformationcf cf USING (positioninformationid)
                      SET p.quantity = ?, p.current_value = ?, p.description = ?, p.last_price = ?, pcf.last_update = ?, pcf.custodian_source = ?
                      WHERE p.positioninformationid = ?";
            if($adb->num_rows($result) > 0){
                while($v = $adb->fetchByAssoc($result)){
                    $data = array($v['quantity'], $v['current_value'], $v['description'], $v['last_price'], $v['date'],
                        $v['filename'], $v['positioninformationid']);
                    $adb->pquery($query, $data);
                }
            }
        }

/*
        $query = "UPDATE UpdatePositions f
                  LEFT JOIN custodian_omniscient.custodian_securities_td sec ON f.symbol = sec.symbol 
                  JOIN vtiger_positioninformation p ON f.symbol = p.security_symbol AND f.account_number = p.account_number 
                  JOIN vtiger_positioninformationcf pcf ON pcf.positioninformationid = p.positioninformationid 
                  LEFT JOIN vtiger_modsecurities m ON p.security_symbol = m.security_symbol 
                  LEFT JOIN vtiger_modsecuritiescf mcf ON m.modsecuritiesid = mcf.modsecuritiesid 
                  SET p.quantity = f.quantity + f.amount, 
                  p.current_value = (f.quantity + f.amount) * m.security_price * CASE WHEN mcf.security_price_adjustment = 0 THEN 1 ELSE mcf.security_price_adjustment END * CASE WHEN m.asset_backed_factor > 0 THEN m.asset_backed_factor ELSE 1 END, 
                  p.description = sec.description, pcf.security_type = m.securitytype, pcf.base_asset_class =  mcf.aclass, pcf.custodian = 'TD', 
                  p.last_price = m.security_price * CASE WHEN mcf.security_price_adjustment = 0 THEN 1 ELSE mcf.security_price_adjustment END, 
                  pcf.last_update = f.date, 
                  pcf.custodian_source = f.filename 
                  WHERE f.account_number = p.account_number";
        $adb->pquery($query, array());

        $query = "UPDATE UpdatePositions f 
                  JOIN vtiger_positioninformation p ON f.account_number = p.account_number 
                  JOIN vtiger_positioninformationcf pcf ON pcf.positioninformationid = p.positioninformationid 
                  SET p.quantity = 0, p.current_value = 0 
                  WHERE pcf.last_update != f.date";
        $adb->pquery($query, array());
*/
    }

    static public function UpdateAllCRMPositionsAtOnceForAccounts(array $account_number){
        global $adb;
        $questions = generateQuestionMarks($account_number);

        $query = "UPDATE vtiger_positioninformation p 
                  SET p.quantity = 0, p.current_value = 0
                  WHERE account_number IN ({$questions})";
        $adb->pquery($query, array($account_number));


        $query = "SELECT MAX(last_position_date) AS last_position_date
                  FROM vtiger_portfolioinformation por
                  JOIN vtiger_portfolioinformationcf porcf ON por.portfolioinformationid = porcf.portfolioinformationid
                  JOIN custodian_omniscient.latestpositiondates lpd ON lpd.rep_code = porcf.production_number
                  WHERE account_number IN ({$questions}) LIMIT 1";
        $result = $adb->pquery($query, array($account_number));

        if($adb->num_rows($result) > 0) {
            $latest_date = $adb->query_result($result, 0, 'last_position_date');
        }

        $query = "SELECT p.positioninformationid, f.quantity + f.amount AS quantity, (f.quantity + f.amount) * m.security_price * CASE WHEN mcf.security_price_adjustment = 0 THEN 1 ELSE mcf.security_price_adjustment END * CASE WHEN m.asset_backed_factor > 0 THEN m.asset_backed_factor ELSE 1 END AS current_value,
                         sec.description, m.securitytype, mcf.aclass, 'TD' AS custodian, 
                         m.security_price * CASE WHEN mcf.security_price_adjustment = 0 THEN 1 ELSE mcf.security_price_adjustment END AS last_price, 
                         f.date, f.filename
                  FROM custodian_omniscient.custodian_positions_td f
                  LEFT JOIN custodian_omniscient.custodian_securities_td sec ON f.symbol = sec.symbol 
                  JOIN vtiger_positioninformation p ON CASE WHEN f.symbol = 'CASH' THEN 'TDCASH' ELSE f.symbol END = p.security_symbol AND f.account_number = p.account_number 
                  JOIN vtiger_positioninformationcf pcf ON pcf.positioninformationid = p.positioninformationid 
                  LEFT JOIN vtiger_modsecurities m ON p.security_symbol = m.security_symbol 
                  LEFT JOIN vtiger_modsecuritiescf mcf ON m.modsecuritiesid = mcf.modsecuritiesid 
                  WHERE f.account_number IN ({$questions})
                  AND f.date = ?";
        $result = $adb->pquery($query, array($account_number, $latest_date));

        if($adb->num_rows($result) > 0){
            $query = "UPDATE vtiger_positioninformation p 
                      JOIN vtiger_positioninformationcf cf USING (positioninformationid)
                      SET p.quantity = ?, p.current_value = ?, p.description = ?, p.last_price = ?, cf.last_update = ?, cf.custodian_source = ?
                      WHERE p.positioninformationid = ?";

            while($v = $adb->fetchByAssoc($result)){
                $data = array($v['quantity'], $v['current_value'], $v['description'], $v['last_price'], $v['date'],
                              $v['filename'], $v['positioninformationid']);
                $adb->pquery($query, $data);
            }
        }
        PortfolioInformation_GlobalSummary_Model::CalculateAllAccountAssetAllocationValuesForAccount($account_number);
    }

    static public function GetLatestPositionDateForAccounts(array $account_number){
        global $adb;
        $questions = generateQuestionMarks($account_number);

        $query = "SELECT MAX(last_position_date) AS last_position_date
                  FROM vtiger_portfolioinformation por
                  JOIN vtiger_portfolioinformationcf porcf ON por.portfolioinformationid = porcf.portfolioinformationid
                  JOIN custodian_omniscient.latestpositiondates lpd ON lpd.rep_code = porcf.production_number
                  WHERE account_number IN ({$questions})";
        $result = $adb->pquery($query, array($account_number));

        if($adb->num_rows($result) > 0) {
            $latest_date = $adb->query_result($result, 0, 'last_position_date');
            return $latest_date;
        }

        return null;
    }

    static public function CreateNewPositionsForAccounts(array $account_number){
        global $adb;
        $questions = generateQuestionMarks($account_number);
        $latest_date = cTDPositions::GetLatestPositionDateForAccounts($account_number);

        $query = "SELECT pos.symbol, pos.account_number, sec.description, IncreaseAndReturnCrmEntitySequence() AS crmid 
                  FROM custodian_omniscient.custodian_positions_td pos
                  LEFT JOIN custodian_omniscient.custodian_securities_td sec ON pos.symbol = sec.symbol
                  WHERE (account_number, pos.symbol) NOT IN (SELECT account_number, CASE WHEN security_symbol = 'TDCASH' THEN 'Cash' ELSE security_symbol END as security_symbol
                                                             FROM vtiger_positioninformation 
                                                             WHERE security_symbol != '' 
                                                             AND account_number IN ({$questions}))
                  AND pos.date = ?
                  AND pos.symbol != '' 
                  AND pos.account_number IN ({$questions})
                  GROUP BY pos.symbol, account_number";
        $result = $adb->pquery($query, array($account_number, $latest_date, $account_number));

        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
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


        /*        $query = "DROP TABLE IF EXISTS CreatePositions";
                $adb->pquery($query, array(), true);

                $query = "DROP TABLE IF EXISTS LatestPositions";
                $adb->pquery($query, array(), true);

                $query = "CREATE TEMPORARY TABLE LatestPositions
                          SELECT symbol, account_number, date
                          FROM custodian_omniscient.custodian_positions_td
                          WHERE date=(SELECT MAX(date) FROM custodian_omniscient.custodian_positions_td WHERE account_number IN ({$questions}))
                          AND account_number IN ({$questions})
                          GROUP BY account_number, symbol";
                $adb->pquery($query, $params, true);

                $query = "UPDATE LatestPositions
                          SET symbol = 'TDCASH'
                          WHERE symbol = 'Cash'";
                $adb->pquery($query, array(), true);

                $query = "CREATE TEMPORARY TABLE CreatePositions
                          SELECT pos.symbol, pos.account_number, sec.description, 0 AS crmid
                          FROM LatestPositions pos
                          LEFT JOIN custodian_omniscient.custodian_securities_td sec ON pos.symbol = sec.symbol
                          WHERE (account_number, pos.symbol) NOT IN (SELECT account_number, security_symbol FROM vtiger_positioninformation WHERE security_symbol != '')
                          AND pos.date = (SELECT MAX(date) FROM custodian_omniscient.custodian_positions_td WHERE account_number IN ({$questions}))
                          AND pos.symbol != '' GROUP BY pos.symbol, account_number";
                $adb->pquery($query, array($account_number), true);

                $query = "UPDATE CreatePositions
                          SET crmid = IncreaseAndReturnCrmEntitySequence()";
                $adb->pquery($query, array(), true);


        $query = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, createdtime, modifiedtime, label)
                  SELECT crmid, 1, 1, 1, 'PositionInformation', NOW(), NOW(), symbol FROM CreatePositions";
        $adb->pquery($query, array(), true);

        $query = "INSERT INTO vtiger_positioninformation (positioninformationid, security_symbol, description, account_number)
                  SELECT crmid, symbol, description, account_number FROM CreatePositions";
        $adb->pquery($query, array(), true);

        $query = "INSERT INTO vtiger_positioninformationcf (positioninformationid)
                  SELECT crmid FROM CreatePositions";
        $adb->pquery($query, array(), true);
        */
    }

    /**
     * Returns a list of symbols that belong to the passed in accounts
     * @param array $account_numbers
     */
    static public function GetSymbolListFromCustodian(array $account_numbers, $max_only=true){
        global $adb;
        $questions = generateQuestionMarks($account_numbers);

        $query = "SELECT symbol 
                  FROM custodian_omniscient.custodian_positions_td 
                  WHERE account_number IN ({$questions}) 
                  AND date = (SELECT MAX(date) FROM custodian_omniscient.custodian_positions_td WHERE account_number IN ({$questions}))
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
}
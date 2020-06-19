<?php

require_once("libraries/custodians/cCustodian.php");

class cTDTransactionsData{
    /*CUSTODIAN_TRANSACTIONS_TD*/
    public $transaction_id, $advisor_rep_code, $file_date, $account_number, $transaction_code, $cancel_status_flag, $symbol, $security_code;
    public $trade_date, $quantity, $net_amount, $principal, $broker_fee, $other_fee, $settle_date, $from_to_account, $account_type;
    public $accrued_interest, $comment, $closing_method, $filename, $insert_date, $dupe_saver_id;

    /*TDMAPPING*/
    public $id, $transaction_type, $transaction_activity, $omniscient_category, $omniscient_activity, $operation, $stopping_point, $affects_total, $affects_performance;

    /*CUSTOM*/
    public $price;//This gets calculated separately

    public function __construct($data){
        /*CUSTODIAN_TRANSACTIONS_TD*/
        $this->transaction_id = $data['transaction_id'];
        $this->advisor_rep_code = $data['advisor_rep_code'];
        $this->file_date = $data['file_date'];
        $this->account_number = $data['account_number'];
        $this->transaction_code = $data['transaction_code'];
        $this->cancel_status_flag = $data['cancel_status_flag'];
        $this->symbol = $data['symbol'];
        $this->security_code = $data['security_code'];
        $this->trade_date = $data['trade_date'];
        $this->quantity = $data['quantity'];
        $this->net_amount = $data['net_amount'];
        $this->principal = $data['principal'];
        $this->broker_fee = $data['broker_fee'];
        $this->other_fee = $data['other_fee'];
        $this->settle_date = $data['settle_date'];
        $this->from_to_account = $data['from_to_account'];
        $this->account_type = $data['account_type'];
        $this->accrued_interest = $data['accrued_interest'];
        $this->comment = $data['comment'];
        $this->closing_method = $data['closing_method'];
        $this->filename = $data['filename'];
        $this->insert_date = $data['insert_date'];
        $this->dupe_saver_id = $data['dupe_saver_id'];

        /*TDMAPPING*/
        $this->id = $data['id'];
        $this->transaction_type = $data['transaction_type'];
        $this->transaction_activity = $data['transaction_activity'];
        $this->omniscient_category = $data['omniscient_category'];
        $this->omniscient_activity = $data['omniscient_activity'];
        $this->operation = $data['operation'];
        $this->stopping_point = $data['stopping_point'];
        $this->affects_total = $data['affects_total'];
        $this->affects_performance = $data['affects_performance'];

        /*CUSTOM*/
        $this->price = $data['price'];

    }
}

/**
 * Class cTDPortfolios
 * This class allows the pulling of data from the custodian database
 */
class cTDTransactions extends cCustodian
{
    use tTransactions;
    protected $transactions_data;//Holds the pricing information
    protected $columns;

    /**
     * cTDPortfolios constructor.
     * @param string $custodian_name
     * @param string $database
     * @param string $module
     * @param string $transactions_table
     * @param string $table (REFERS TO BALANCE TABLE)
     */
    public function __construct(string $custodian_name, string $database, string $module,
                                string $portfolio_table, string $transactions_table, array $rep_codes, $columns=array("*")){
        $this->name = $custodian_name;
        $this->database = $database;
        $this->module = $module;
        $this->portfolio_table = $portfolio_table;
        $this->table = $transactions_table;
        $this->columns = $columns;
        if(!empty($rep_codes)) {
            $this->SetRepCodes($rep_codes);

        }
    }
    /**
     * Returns an associative array of all requested transactions as of the given date
     * @param null $date
     * @return mixed
     */
    public function GetTransactionsDataForDate($date=null){
        global $adb;
        $params = array();
        $questions = generateQuestionMarks($this->account_numbers);
        $params[] = $this->account_numbers;

        if (empty($this->columns))
            $fields = "*";
        else {
            $fields = implode ( ", ", $this->columns );
        }

        if(!$date)
            $date = $this->GetLatestTransactionsDate("trade_date");
        $params[] = $date;


        $query = "SELECT {$fields} FROM {$this->database}.{$this->table} t
                  WHERE account_number IN ({$questions}) AND trade_date = ?";
        $result = $adb->pquery($query, $params, true);

        if ($adb->num_rows($result) > 0) {
            while ($r = $adb->fetchByAssoc($result)) {
                $this->transactions_data[$r['account_number']] = $r;
            }
        }

        $this->SetupTransactionComparisons();
        return $this->transactions_data;
    }

    /**
     * Returns an associative array of all requested transactions between the given dates
     * @param null start
     * @param null end
     * @return mixed
     */
    public function GetTransactionsDataBetweenDates($start, $end){
        global $adb;
        $params = array();
        $questions = generateQuestionMarks($this->account_numbers);
        $params[] = $this->account_numbers;
        $params[] = $start;
        $params[] = $end;

        if (empty($this->columns))
            $fields = "*";
        else {
            $fields = implode ( ", ", $this->columns );
        }

        $query = "DROP TABLE IF EXISTS BeforeMapping";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE BeforeMapping
                  SELECT {$fields} FROM {$this->database}.{$this->table} t
                  JOIN {$this->database}.tdmapping m ON m.id = t.transaction_code
                  JOIN vtiger_modsecurities ms ON ms.security_symbol = t.symbol
                  JOIN vtiger_modsecuritiescf mscf USING (modsecuritiesid) 
                  WHERE account_number IN ({$questions}) AND trade_date BETWEEN ? AND ?";
        $adb->pquery($query, $params, true);

        $query = "UPDATE BeforeMapping bm
                  SET bm.symbol = 'TDCASH' WHERE bm.symbol = '' OR bm.symbol IS NULL";
        $adb->pquery($query, array());

        $fields = preg_replace('/\b([(t.|ms.|m.|cf.|pr.)]{1,})\b/', '', $fields);

        $query = "SELECT {$fields} FROM BeforeMapping t";
        $result = $adb->pquery($query, array(), true);

        /*
        $query = "SELECT {$fields} FROM {$this->database}.{$this->table} 
                  WHERE account_number IN ({$questions}) AND trade_date BETWEEN ? AND ?";
        $result = $adb->pquery($query, $params, true);
*/
        if($adb->num_rows($result) > 0) {
            while ($r = $adb->fetchByAssoc($result)) {
                if($r['quantity'] == 0 OR empty($r['quantity']))
                    $r['quantity'] = $r['net_amount'];

                if($r['quantity'] != 0 AND !empty($r['quantity']))
                    $r['price'] = $r['net_amount'] / $r['quantity'];//We set the price here so it calculates the buy price
                else
                    $r['price'] = 1;//Set the price to 1 if we can't figure it out (no net amount/quantity)

                //If net amount hasn't been set and the transaction code is REC or DEL
                if((empty($r['net_amount']) OR $r['net_amount'] == 0) AND in_array($r['transaction_code'], array('REC','DEL'))){
                    $query = "INSERT IGNORE INTO vtiger_problem_accounts (account_number, custodian, problem, problem_id)
                              VALUES (?, ?, ?, ?)";
                    $adb->pquery($query, array($r['account_number'], "TD", 'transactions_no_net_amount', $r['symbol']));
                }
                    /*
                    $query = "SELECT security_price_adjustment, pr.price
                              FROM vtiger_modsecurities m
                              JOIN vtiger_modsecuritiescf cf USING (modsecuritiesid)
                              JOIN {$this->database}.custodian_prices_td pr ON m.security_symbol = pr.symbol AND pr.date = (SELECT date FROM {$this->database}.custodian_prices_td WHERE date < ? AND symbol = m.security_symbol ORDER BY date DESC LIMIT 1)
                              WHERE security_symbol = ?";//Get what we know from modsecurities, take the end of day price from custodian database
                    $price_result = $adb->pquery($query, array($r['trade_date'], $r['symbol']), true);

                    if($adb->num_rows($price_result) > 0){
                        $adjustment = $adb->query_result($price_result, 0, 'security_price_adjustment');
                        $price = $adb->query_result($price_result, 0, 'price');
                        $net_amount = $price * $adjustment * $r['quantity'];
                        $r['price'] = $price;
                        $r['net_amount'] = $net_amount;
                    }
                }*/
/*                if(strtoupper($r['symbol']) == 'AAPL') {
                    print_r($r);
                    exit;
                }*/

                $this->transactions_data[$r['account_number']][$r['transaction_id']] = $r;
            }
        }
        $this->SetupTransactionComparisons();
        return $this->transactions_data;
    }

    /**
     * Returns the transactions_data variable that was filled in from the last retrieve
     * @return mixed
     */
    public function GetSavedTransactionsData(){
        return $this->transactions_data;
    }

    /**
     * Using the cTDTransactionsData class, create the portfolios.  Used with a pre-filled in cTDPortfolioData class (done manually)
     * @param cTDPortfolioData $data
     * @throws Exception
     */
    public function CreateNewTransactionUsingcTDTransactionsData(cTDTransactionsData $data){
        if(!$this->DoesTransactionExistInCRM($data->transaction_id)) {//If the transaction doesn't exist yet, create it (uses custodian transaction ID)
            $crmid = $this->UpdateEntitySequence();
            $owner = $this->GetAccountOwnerFromAccountNumber($data->account_number);

            $this->FillEntityTable($crmid, $owner, $data);
            $this->FillTransactionTable($crmid, $data);
            $this->FillTransactionCFTable($crmid, $data);
        }
    }

    /**
     * Auto creates the transaction's based on the data loaded into the $transactions_data member.  If the transaction exists in this data, it will be created
     * @param array $account_numbers
     */
    public function CreateNewTransactionsFromTransactionData(array $missing_account_data){
        if(!empty($missing_account_data)) {
            foreach ($missing_account_data AS $account_number => $v) {
                foreach ($v AS $a => $transaction_id) {
                    $data = $this->transactions_data[$account_number][$transaction_id];
                    if (!empty($data)) {
                        $tmp = new cTDTransactionsData($data);
                        $this->CreateNewTransactionUsingcTDTransactionsData($tmp);
                    }
                }
            }
        }
    }

    /**
     * Auto updates the transaction's based on the data loaded into the $transaction_data member.
     * @param array $account_numbers
     */
    public function UpdateTransactionsFromTransactionsData(array $transaction_account_data){
        if(!empty($transaction_account_data)) {
            foreach ($transaction_account_data AS $k => $v) {
                foreach ($v AS $a => $transaction) {
                    $data = $this->transactions_data[$k][$a];
                    if (!empty($data)) {
                        $tmp = new cTDTransactionsData($data);
                        $this->UpdateTransactionsUsingcTDTransactionsData($tmp);
                    }
                }
            }
        }
    }

    /**
     * Create the new entity in the crmentity table
     * @param $crmid
     * @param $owner
     * @param cTDTransactionsData $data
     */
    protected function FillEntityTable($crmid, $owner, cTDTransactionsData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;
        $params[] = 1;
        $params[] = $owner;
        $params[] = 1;
        $params[] = 'Transactions';
        $params[] = $data->comment;
        $query = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, createdtime, modifiedtime, label)
                  VALUES (?, ?, ?, ?, ?, NOW(), NOW(), ?)";
        $adb->pquery($query, $params, true);
    }

    /**
     * Creates data in the vtiger_transactioninformation table
     * @param $crmid
     * @param cTDTransactionsData $data
     */
    protected function FillTransactionTable($crmid, cTDTransactionsData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;
        $params[] = $data->account_number;
        $params[] = $data->symbol;
        $params[] = $data->price;
        $params[] = $data->quantity;
        $params[] = $data->trade_date;
        $params[] = 'TD';
        $params[] = $data->transaction_id;//cloud transaction id

        $questions = generateQuestionMarks($params);
        $query = "INSERT INTO vtiger_transactions (transactionsid, account_number, security_symbol, security_price, quantity, trade_date, 
                              origination, cloud_transaction_id)
                  VALUES ({$questions})";
        $adb->pquery($query, $params, true);
    }

    /**
     * Creates data in the vtiger_transactionscf table
     * @param $crmid
     * @param cTDTransactionsData $data
     */
    protected function FillTransactionCFTable($crmid, cTDTransactionsData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;
        $params[] = 'TD';
        $params[] = $data->omniscient_category;
        $params[] = $data->advisor_rep_code;
        $params[] = $data->omniscient_activity;
        $params[] = $data->net_amount;
        $params[] = $data->principal;
        $params[] = $data->broker_fee;
        $params[] = $data->other_fee;
        $params[] = $data->comment;
        $params[] = $data->filename;

        $questions = generateQuestionMarks($params);
        $query = "INSERT INTO vtiger_transactionscf (transactionsid, custodian, transaction_type, rep_code, transaction_activity, net_amount, 
                                                     principal, broker_fee, other_fee, description, filename)
                  VALUES ({$questions})";
        $adb->pquery($query, $params, true);
    }

    /**
     * Update the transaction in the CRM using the cTDTransactionsData class
     * @param cTDTransactionsData $data
     */
    public function UpdateTransactionsUsingcTDTransactionsData(cTDTransactionsData $data){
        global $adb;
        $params = array();
        $params[] = $data->quantity_amount_combo;
        $params[] = $data->quantity_amount_combo;
        $params[] = $data->insert_date;
        $params[] = $data->filename;
        $params[] = $data->account_number;
        $params[] = $data->symbol;


/*        $query = "UPDATE vtiger_transactions p
                  JOIN vtiger_positioninformationcf pcf ON pcf.positioninformationid = p.positioninformationid 
                  SET p.quantity = 0, p.current_value = 0 
                  WHERE account_number = ?";
        $adb->pquery($query, array($data->account_number), true);

        $query = "UPDATE vtiger_positioninformation p 
                  JOIN vtiger_positioninformationcf cf USING (positioninformationid)
                  LEFT JOIN vtiger_modsecurities m ON m.security_symbol = p.security_symbol 
                  LEFT JOIN vtiger_modsecuritiescf mcf ON m.modsecuritiesid = mcf.modsecuritiesid
                  SET p.quantity = ?, p.current_value = ? * m.security_price * CASE WHEN mcf.security_price_adjustment > 0 
                                                                                    THEN mcf.security_price_adjustment ELSE 1 END 
                                                                                    * CASE WHEN m.asset_backed_factor > 0 
                                                                                    THEN m.asset_backed_factor ELSE 1 END,
                  p.description = m.security_name, cf.security_type = m.securitytype, cf.base_asset_class = mcf.aclass, cf.custodian = 'TD',
                  p.last_price = m.security_price * CASE WHEN mcf.security_price_adjustment > 0 THEN mcf.security_price_adjustment ELSE 1 END,
                  cf.last_update = ?, cf.custodian_source = ?
                  WHERE account_number = ? AND p.security_symbol = ?";
        $adb->pquery($query, $params, true);*/
    }
}
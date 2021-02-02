<?php
//
require_once("libraries/custodians/cCustodian.php");
require_once("libraries/Reporting/ReportCommonFunctions.php");

spl_autoload_register(function ($className) {
    if (file_exists("libraries/EODHistoricalData/$className.php")) {
        include_once "libraries/EODHistoricalData/$className.php";
    }
});

class cFidelityTransactionsData{
    public $operation, $amount, $production_number, $omniscient_negative_category, $omniscient_category, $buy_sell_indicator,
           $omniscient_negative_activity, $omniscient_activity, $description, $commission, $key_code_description, $service_charge_misc_fee,
           $option_symbol, $account_type_description, $comment, $comment2, $div_payable_date, $div_record_date, $fund_load_override,
           $fund_load_percent, $interest_amount, $postage_fee, $reg_rep1, $reg_rep2, $service_fee, $short_term_redemption_fee,
           $state_tax_amount, $transaction_code_description, $transaction_key_mnemonic, $price, $security_price_adjustment, $quantity,
           $account_number, $symbol, $transaction_type, $transaction_activity, $cusip, $transaction_key_code_description, $trade_date, $transaction_id;

    public function __construct($data){
        $this->account_number = $data['account_number'];
        $this->symbol = $data['symbol'];
        $this->operation = (is_null($data['operation'])) ? "" : $data['operation'];
        $this->amount = ($data['amount'] == 0) ? $data['quantity'] * $data['security_price_adjustment'] * $data['price'] : ABS($data['amount']);
        $this->production_number = $data['production_number'];
        $this->omniscient_negative_category = $data['omniscient_negative_category'];
        $this->omniscient_category = $data['omniscient_category'];
        $this->buy_sell_indicator = $data['buy_sell_indicator'];
        $this->omniscient_negative_activity = $data['omniscient_negative_activity'];
        $this->omniscient_activity = $data['omniscient_activity'];
        $this->description = $data['description'];
        $this->commission = $data['commission'];
        $this->key_code_description = $data['key_code_description'];
        $this->service_charge_misc_fee = $data['service_charge_misc_fee'];
        $this->option_symbol = $data['option_symbol'];
        $this->account_type_description = $data['account_type_description'];
        $this->comment = $data['comment'];
        $this->comment2 = $data['comment2'];
        $this->div_payable_date = $data['div_payable_date'];
        $this->div_record_date = $data['div_record_date'];
        $this->fund_load_override = $data['fund_load_override'];
        $this->fund_load_percent = $data['fund_load_percent'];
        $this->interest_amount = $data['interest_amount'];
        $this->postage_fee = $data['postage_fee'];
        $this->reg_rep1 = $data['reg_rep1'];
        $this->reg_rep2 = $data['reg_rep2'];
        $this->service_fee = $data['service_fee'];
        $this->short_term_redemption_fee = $data['short_term_redemption_fee'];
        $this->state_tax_amount = $data['state_tax_amount'];
        $this->transaction_code_description = $data['transaction_code_description'];
        $this->transaction_key_mnemonic = $data['transaction_key_mnemonic'];
        $this->transaction_key_code_description = $data['transaction_key_code_description'];
        $this->price = $data['price'];
        $this->security_price_adjustment = $data['security_price_adjustment'];
        $this->quantity = $data['quantity'];
        $this->cusip = $data['cusip'];
        $this->trade_date = $data['trade_date'];
        $this->transaction_id = $data['transaction_id'];

        $this->transaction_type = ($data['amount'] < 0) ? $data['omniscient_negative_category'] : $data['omniscient_category'];
        $this->transaction_activity = ($data['amount'] < 0) ? $data['omniscient_negative_activity'] : $data['omniscient_activity'];
        if(strlen($this->transaction_activity) == 0)
            $this->transaction_activity = $data['description'];
    }
}

/**
 * Class cFidelityPortfolios
 * This class allows the pulling of data from the custodian database
 */
class cFidelityTransactions extends cCustodian
{
    use tTransactions;
    protected $transactions_data, $columns;//Holds the pricing information

    /**
     * cFidelityPortfolios constructor.
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
        $this->transactions_data = array();
        if(!empty($rep_codes)) {
            $this->SetRepCodes($rep_codes);
        }
    }
    /**
     * Returns an associative array of all requested transactions as of the given date
     * @param null $date
     * @return mixed
     */
/*    public function GetTransactionsDataForDate($date=null){
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
    }*/

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

        $query = "DROP TABLE IF EXISTS transactions_to_update";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE transactions_to_update 
                  SELECT transaction_id, t.account_number, amount, t.symbol, trade_date, quantity, pos.close_price, pos.pricing_factor, t.quantity * pos.pricing_factor * pos.close_price AS calculated_amount
                  FROM {$this->database}.{$this->table} t 
                  JOIN {$this->database}.custodian_positions_fidelity pos ON t.symbol = pos.symbol AND t.trade_date = pos.as_of_date AND t.account_number = pos.account_number 
                  WHERE amount = 0 AND t.symbol IS NOT NULL AND t.account_number IN ({$questions})";
        $adb->pquery($query, array($this->account_numbers), true);

        $query = "update {$this->database}.{$this->table} t 
                  JOIN transactions_to_update u ON t.transaction_id = u.transaction_id 
                  SET t.amount = u.calculated_amount, t.amount_calculated = 1";
        $adb->pquery($query, array(), true);

        $query = "DROP TABLE IF EXISTS transactions_to_update";
        $adb->pquery($query, array(), true);

        $query = "CREATE TEMPORARY TABLE transactions_to_update 
                  SELECT transaction_id, t.account_number, amount, t.symbol, trade_date, quantity, pr.price, t.quantity * mscf.security_price_adjustment * pr.price AS calculated_amount 
                  FROM {$this->database}.{$this->table} t 
                  JOIN {$this->database}.custodian_prices_fidelity pr ON pr.symbol = t.symbol AND pr.price_id = (SELECT price_id FROM {$this->database}.custodian_prices_fidelity WHERE symbol = t.symbol AND price_date <= t.trade_date ORDER BY price_date DESC LIMIT 1) 
                  JOIN vtiger_modsecurities ms ON ms.security_symbol = t.symbol 
                  JOIN vtiger_modsecuritiescf mscf ON ms.modsecuritiesid = mscf.modsecuritiesid 
                  WHERE amount = 0 AND t.symbol IS NOT NULL AND t.account_number IN ({$questions})";
        $adb->pquery($query,  array($this->account_numbers), true);

        $query = "update {$this->database}.{$this->table} t 
                  JOIN transactions_to_update u ON t.transaction_id = u.transaction_id 
                  SET t.amount = u.calculated_amount, t.price = u.price, t.amount_calculated = 1";
        $adb->pquery($query, array(), true);

        $query = "SELECT {$fields} FROM custodian_omniscient.custodian_transactions_fidelity f 
                  JOIN custodian_omniscient.fidelitymapping m ON m.id = f.transaction_key_mnemonic AND (f.transaction_code_description = m.code_description OR f.transaction_code_description IS NULL AND m.code_description IS NULL)
                  LEFT JOIN vtiger_portfolioinformation p ON p.account_number = f.account_number
                  LEFT JOIN vtiger_portfolioinformationcf pcf ON pcf.portfolioinformationid = p.portfolioinformationid
                  LEFT JOIN custodian_omniscient.custodian_prices_fidelity pr ON pr.symbol = f.symbol AND pr.price_date = f.trade_date
                  LEFT JOIN vtiger_modsecurities ms ON ms.security_symbol = f.symbol
                  LEFT JOIN vtiger_modsecuritiescf mscf ON ms.modsecuritiesid = mscf.modsecuritiesid
                  WHERE f.account_number IN ({$questions}) AND f.trade_date BETWEEN ? AND ? GROUP BY f.transaction_id";
        $result = $adb->pquery($query, array($this->account_numbers, $start, $end), true);

        if ($adb->num_rows($result) > 0) {
            while ($r = $adb->fetchByAssoc($result)) {
                $this->transactions_data[$r['account_number']][$r['transaction_id']] = $r;
            }
        }
        $this->SetupTransactionComparisons($start, $end);
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
     * Using the cFidelityTransactionsData class, create the portfolios.  Used with a pre-filled in cFidelityPortfolioData class (done manually)
     * @param cFidelityPortfolioData $data
     * @throws Exception
     */
    public function CreateNewTransactionUsingcFidelityTransactionsData(cFidelityTransactionsData $data){
        if(!$this->DoesTransactionExistInCRM($data->transaction_id)) {//If the transaction doesn't exist yet, create it (uses custodian transaction ID)
#            $crmid = "73957144";
           $crmid = $this->UpdateEntitySequence();
#            echo "CRMID: {$crmid} <br />";
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
                        $tmp = new cFidelityTransactionsData($data);
                        $this->CreateNewTransactionUsingcFidelityTransactionsData($tmp);
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
                        $tmp = new cFidelityTransactionsData($data);
                        $this->UpdateTransactionsUsingcFidelityTransactionsData($tmp);
                    }
                }
            }
        }
    }

    /**
     * Create the new entity in the crmentity table
     * @param $crmid
     * @param $owner
     * @param cFidelityTransactionsData $data
     */
    protected function FillEntityTable($crmid, $owner, cFidelityTransactionsData $data){
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
     * @param cFidelityTransactionsData $data
     */
    protected function FillTransactionTable($crmid, cFidelityTransactionsData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;
        $params[] = $data->account_number;
        $params[] = $data->symbol;
        $params[] = $data->price;
        $params[] = $data->quantity;
        $params[] = $data->trade_date;
        $params[] = 'FIDELITY';
        $params[] = $data->transaction_id;//cloud transaction id
        $params[] = $data->operation;

        $questions = generateQuestionMarks($params);
        $query = "INSERT INTO vtiger_transactions (transactionsid, account_number, security_symbol, security_price, quantity, trade_date, 
                              origination, cloud_transaction_id, operation)
                  VALUES ({$questions})";
        $adb->pquery($query, $params, true);
    }

    /**
     * Creates data in the vtiger_transactionscf table
     * @param $crmid
     * @param cFidelityTransactionsData $data
     */
    protected function FillTransactionCFTable($crmid, cFidelityTransactionsData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;
        $params[] = 'FIDELITY';
        $params[] = $data->transaction_type;
        $params[] = $data->advisor_rep_code;///FIGURE THIS ONE OUT
        $params[] = $data->transaction_activity;
        $params[] = $data->amount;
        $params[] = $data->principal;
        $params[] = $data->broker_fee;
        $params[] = $data->other_fee;
        $params[] = $data->comment;
        $params[] = $data->cusip;
        $params[] = $data->filename;

        $params[] = $data->buy_sell_indicator;
        $params[] = $data->commission;
        $params[] = $data->key_code_description;
        $params[] = $data->service_charge_misc_fee;
        $params[] = $data->option_symbol;
        $params[] = $data->account_type_description;
        $params[] = $data->comment2;
        $params[] = $data->div_payable_date;
        $params[] = $data->div_record_date;
        $params[] = $data->fund_load_override;
        $params[] = $data->fund_load_percent;
        $params[] = $data->interest_amount;
        $params[] = $data->postage_fee;
        $params[] = $data->reg_rep1;
        $params[] = $data->reg_rep2;
        $params[] = $data->service_fee;
        $params[] = $data->short_term_redemption_fee;
        $params[] = $data->state_tax_amount;
        $params[] = $data->transaction_code_description;
        $params[] = $data->transaction_key_mnemonic;
        $params[] = $data->transaction_key_code_description;

        $questions = generateQuestionMarks($params);
        $query = "INSERT INTO vtiger_transactionscf (transactionsid, custodian, transaction_type, rep_code, transaction_activity, net_amount, 
                                                     principal, broker_fee, other_fee, description, cusip, filename, buy_sell_indicator,
                                                     commission, key_code_description, service_charge_misc_fee, option_symbol, 
                                                     account_type_description, comment2, dividend_payable_date, dividend_record_date, fund_load_override, 
                                                     fund_load_percent, interest_amount, postage_fee, registered_rep1, registered_rep2, service_fee,
                                                     short_term_redemption_fee, state_tax_amount, transaction_code_description, 
                                                     key_mnemonic_description, transaction_key_code_description)
                  VALUES ({$questions})";
        $adb->pquery($query, $params, true);
    }

    /**
     * Update the transaction in the CRM using the cFidelityTransactionsData class
     * @param cFidelityTransactionsData $data
     */
    public function UpdateTransactionsUsingcFidelityTransactionsData(cFidelityTransactionsData $data){
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

    static public function CreateNewTransactionsForAccounts(array $account_number, $sdate=null, $edate=null){
        global $adb;

        $q1 = array();
        $q1[] = $account_number;
        if($sdate && $edate){
            $and = " AND trade_date BETWEEN ? AND ? ";
            $q1[] = $sdate;
            $q1[] = $edate;
        }
        $account_questions = generateQuestionMarks($account_number);
        $query = "SELECT cloud_transaction_id 
                  FROM vtiger_transactions 
                  WHERE origination = 'Fidelity'
                  AND account_number IN ({$account_questions})
                  {$and} ";

        $result = $adb->pquery($query, $q1);
        $params = array();
        $cloud_ids = array();
        $transaction_ids = "";

        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $cloud_ids[] = $v['cloud_transaction_id'];
            }
            $cloud_id_questions = generateQuestionMarks($cloud_ids);
            $transaction_ids = " t.transaction_id NOT IN ({$cloud_id_questions}) ";
            $params[] = $cloud_ids;
        }

        if(strlen($transaction_ids) == 0){
            $transaction_ids = " t.transaction_id != 0 ";
        }

        $params[] = $account_number;

        if($sdate && $edate){
            $params[] = $sdate;
            $params[] = $edate;
        }

        $query = "SELECT IncreaseAndReturnCrmEntitySequence() AS crmid, m.omniscient_category, m.omniscient_activity, t.transaction_id, t.account_number, t.account_source, 
                         t.account_type_code, t.account_type_description, t.amount, t.broker_code, t.buy_sell_indicator, t.certificate_fee, 
                         t.comment, t.comment2, t.commission, t.core_fund_indicator, t.cusip, t.custom_short_name, t.div_payable_date, 
                         t.div_record_date, t.dtc_code, t.entry_date, t.exchange, t.exchange_code, t.fbsi_short_name, t.fee_amount, t.floor_symbol, 
                         t.fprs_txn_code, t.fprs_tsn_code_description, t.fund_load_override, t.fund_load_percent, t.fund_number, t.interest_amount, 
                         t.key_code, t.money_source_id, t.money_source, t.net_amount, t.order_action, t.plan_name, t.plan_number, t.postage_fee, 
                         t.price, t.primary_account_owner, t.principal_amount, t.product_name, t.product_type, t.quantity, t.reference_number, 
                         t.reg_rep1, t.reg_rep2, t.registration, t.sec_fee, t.security_description, t.security_group, t.security_id, t.service_fee, 
                         t.settlement_date, t.short_term_redemption_fee, t.source_destination, t.state_tax_amount, t.symbol, t.trade_date, t.transaction_code, 
                         t.transaction_code_description, t.transaction_key_mnemonic, t.transaction_key_code_description, t.transaction_security_type, 
                         t.transaction_security_type_code, t.trust_income, t.trust_principal, t.file_date, t.filename, t.insert_date,
                         pr.price, m.operation
                  FROM custodian_omniscient.custodian_transactions_fidelity t 
                  JOIN custodian_omniscient.fidelitymapping m ON m.id = t.transaction_key_mnemonic 
                  LEFT JOIN custodian_omniscient.custodian_positions_fidelity pos ON t.symbol = pos.symbol AND t.trade_date = pos.as_of_date AND t.account_number = pos.account_number
                  LEFT JOIN custodian_omniscient.custodian_prices_fidelity pr ON pr.symbol = t.symbol AND pr.price_id = (SELECT price_id FROM custodian_omniscient.custodian_prices_fidelity WHERE symbol = t.symbol AND price_date <= t.trade_date ORDER BY price_date DESC LIMIT 1)
                  LEFT JOIN live_omniscient.vtiger_modsecurities ms ON ms.security_symbol = t.symbol
                  LEFT JOIN live_omniscient.vtiger_modsecuritiescf mscf ON ms.modsecuritiesid = mscf.modsecuritiesid
                  WHERE {$transaction_ids} 
                  AND t.account_number IN ({$account_questions})
                  {$and}
                  GROUP BY transaction_id";
        $result = $adb->pquery($query, $params, true);

        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $v['ownerid'] = PortfolioInformation_Module_Model::GetAccountOwnerFromAccountNumber($v['account_number']);

                /*SPECIAL RULES FOR FIDELITY*/
                $v['calculated_amount'] = $v['quantity'] * $v['pricing_factor'] * $v['close_price'];
                if($v['amount'] == 0) {
                    $v['amount'] = $v['calculated_amount'];
                    $v['amount_calculated'] = 1;
                }

                if($v['amount'] < 0){
                    $v['operation'] = '-';
                    $v['amount'] = ABS($v['amount']);
                }

                if(is_null($v['operation']))
                    $v['operation'] = '';

                /*SPECIAL RULES END*/

                $query = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, createdtime, modifiedtime, label) 
                          VALUES (?, ?, ?, ?, ?, NOW(), NOW(), ?)";
                $adb->pquery($query, array($v['crmid'], 1, $v['ownerid'], 1, 'Transactions', $v['comment']), true);


                $query = "INSERT INTO vtiger_transactions (transactionsid, account_number, security_symbol, security_price, quantity, trade_date, origination, cloud_transaction_id, operation)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $adb->pquery($query, array($v['crmid'], $v['account_number'], $v['symbol'], $v['price'], $v['quantity'], $v['trade_date'],
                                           'Fidelity', $v['transaction_id'], $v['operation']), true);

                $query = "INSERT INTO vtiger_transactionscf (transactionsid, custodian, transaction_type, transaction_activity, 
                                 net_amount, principal, broker_fee, other_fee, description, 
                                 comment, cusip, filename, commission, transaction_key_code_description, 
                                 service_charge_misc_fee, option_symbol, account_type_description, comment2, dividend_payable_date,
                                 dividend_record_date, fund_load_override, fund_load_percent, interest_amount, postage_fee,
                                 registered_rep1, registered_rep2, short_term_redemption_fee, state_tax_amount, transaction_code_description,
                                 key_mnemonic_description)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, 
                                  ?, ?, ?, ?, ?, ?, ?,
                                  ?, ?, ?, ?, ?,
                                  ?, ?, ?, ?, ?,
                                  ?, ?, ?, ?, ?)";
                $adb->pquery($query, array($v['crmid'], 'Fidelity', $v['omniscient_category'], $v['omniscient_activity'], $v['amount'],
                    $v['principal_amount'], $v['service_fee'], $v['fee_amount'], $v['security_description'],
                    $v['comment'], $v['cusip'], $v['filename'], $v['commission'], $v['transaction_key_code_description'],
                    $v['service_charge_misc_fee'], $v['option_symbol'], $v['account_type_description'], $v['comment2'], $v['div_payable_date'],
                    $v['div_record_date'], $v['fund_load_override'], $v['fund_load_percent'], $v['interest_amount'], $v['postage_fee'],
                    $v['reg_rep1'], $v['reg_rep2'], $v['short_term_redemption_fee'], $v['state_tax_amount'], $v['transaction_code_description'],
                    $v['transaction_key_mnemonic']), true);
            }
        }
    }

    private function GetBestReceiptOfSecurityPrice($symbol, $date){
        global $adb;

        $price = cFidelityPrices::GetBestKnownPriceBeforeDate($symbol, $date);
        if($price == false){
            $fix = new CustodianWriter();
            $sdate = GetDateMinusMonthsSpecified($date, 1);
            $edate = $date;
            $fix->WriteEodToCustodian($symbol, $sdate, $edate, "FIDELITY");//Only update if there is no price for previous day
        }else{
            return $price;
        }
        $price = cFidelityPrices::GetBestKnownPriceBeforeDate($date, $symbol);
        return $price;
    }

    static public function UpdateTransactionsForAccounts(array $account_number, $sdate=null, $edate=null){
        global $adb;

        $q1 = array();
        $q1[] = $account_number;
        if($sdate && $edate){
            $and = " AND trade_date BETWEEN ? AND ? ";
            $q1[] = $sdate;
            $q1[] = $edate;
        }
        $account_questions = generateQuestionMarks($account_number);
        $query = "SELECT cloud_transaction_id 
                  FROM vtiger_transactions 
                  WHERE origination = 'Fidelity'
                  AND account_number IN ({$account_questions})
                  {$and} ";

        $result = $adb->pquery($query, $q1);
        $params = array();
        $cloud_ids = array();
        $transaction_ids = "";

        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $cloud_ids[] = $v['cloud_transaction_id'];
            }
            $cloud_id_questions = generateQuestionMarks($cloud_ids);
            $transaction_ids = " t.transaction_id IN ({$cloud_id_questions}) ";
            $params[] = $cloud_ids;
        }

        if(strlen($transaction_ids) == 0){
            $transaction_ids = " t.transaction_id != 0 ";
        }

        $params[] = $account_number;

        if($sdate && $edate){
            $params[] = $sdate;
            $params[] = $edate;
        }

        $query = "SELECT m.omniscient_category, m.omniscient_activity, t.transaction_id, t.account_number, t.account_source, 
                         t.account_type_code, t.account_type_description, t.amount, t.broker_code, t.buy_sell_indicator, t.certificate_fee, 
                         t.comment, t.comment2, t.commission, t.core_fund_indicator, t.cusip, t.custom_short_name, t.div_payable_date, 
                         t.div_record_date, t.dtc_code, t.entry_date, t.exchange, t.exchange_code, t.fbsi_short_name, t.fee_amount, t.floor_symbol, 
                         t.fprs_txn_code, t.fprs_tsn_code_description, t.fund_load_override, t.fund_load_percent, t.fund_number, t.interest_amount, 
                         t.key_code, t.money_source_id, t.money_source, t.net_amount, t.order_action, t.plan_name, t.plan_number, t.postage_fee, 
                         t.price, t.primary_account_owner, t.principal_amount, t.product_name, t.product_type, t.quantity, t.reference_number, 
                         t.reg_rep1, t.reg_rep2, t.registration, t.sec_fee, t.security_description, t.security_group, t.security_id, t.service_fee, 
                         t.settlement_date, t.short_term_redemption_fee, t.source_destination, t.state_tax_amount, t.symbol, t.trade_date, t.transaction_code, 
                         t.transaction_code_description, t.transaction_key_mnemonic, t.transaction_key_code_description, t.transaction_security_type, 
                         t.transaction_security_type_code, t.trust_income, t.trust_principal, t.file_date, t.filename, t.insert_date,
                         pr.price, m.operation, m.omniscient_negative_activity, m.omniscient_negative_category, m.description, pos.pricing_factor
                  FROM custodian_omniscient.custodian_transactions_fidelity t 
                  JOIN custodian_omniscient.fidelitymapping m ON m.id = t.transaction_key_mnemonic AND (t.transaction_code_description = m.code_description OR t.transaction_code_description IS NULL AND m.code_description IS NULL)
                  LEFT JOIN custodian_omniscient.custodian_positions_fidelity pos ON t.symbol = pos.symbol AND t.trade_date = pos.as_of_date AND t.account_number = pos.account_number
                  LEFT JOIN custodian_omniscient.custodian_prices_fidelity pr ON pr.symbol = t.symbol AND pr.price_id = (SELECT price_id FROM custodian_omniscient.custodian_prices_fidelity WHERE symbol = t.symbol AND price_date <= t.trade_date ORDER BY price_date DESC LIMIT 1)
                  JOIN live_omniscient.vtiger_modsecurities ms ON ms.security_symbol = t.symbol
                  LEFT JOIN live_omniscient.vtiger_modsecuritiescf mscf ON ms.modsecuritiesid = mscf.modsecuritiesid
                  WHERE {$transaction_ids} 
                  AND t.account_number IN ({$account_questions})
                  {$and}
                  GROUP BY transaction_id";
        $result = $adb->pquery($query, $params, true);

        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $v['ownerid'] = PortfolioInformation_Module_Model::GetAccountOwnerFromAccountNumber($v['account_number']);
                if($v['pricing_factor'] == 0 || strlen($v['pricing_factor']) == 0)
                    $v['pricing_factor'] = 1;
#                if($v['close_price'] == 0 || strlen($v['close_price']) == 0)
#                    $v['close_price'] = $v['price'];

                /*SPECIAL RULES FOR FIDELITY*/
                if(strtoupper($v['omniscient_activity']) == "RECEIPT OF SECURITIES"){
                    $v['price'] = $v['close_price'] = self::GetBestReceiptOfSecurityPrice($v['symbol'], $v['trade_date']);
                    $v['amount'] = $v['quantity'] * $v['pricing_factor'] * $v['close_price'];
                }

                $v['calculated_amount'] = $v['quantity'] * $v['pricing_factor'] * $v['close_price'];
                if($v['amount'] == 0) {
                    $v['amount'] = $v['calculated_amount'];
                    $v['amount_calculated'] = 1;
                }

                if(is_null($v['omniscient_activity']) || $v['omniscient_activity'] == '')
                    $v['transaction_activity'] = $v['description'];
                else
                    $v['transaction_activity'] = $v['omniscient_activity'];

                if($v['amount'] < 0){
                    $v['operation'] = '-';
                    $v['amount'] = ABS($v['amount']);
                    $v['transaction_type'] = $v['omniscient_negative_category'];
                    if(is_null($v['omniscient_negative_activity']) || $v['omniscient_negative_activity'] == '')
                        $v['transaction_activity'] = $v['omniscient_negative_activity'];
                }else{
                    $v['transaction_type'] = $v['omniscient_category'];
                }

                if($v['amount'] == 0)
                    $v['amount'] = ABS($v['calculated_amount']);
#echo '<br /><br />';
                /*SPECIAL RULES END*/
#print_r($v); echo '<br /><br />';
                $query = "UPDATE vtiger_transactions t
                          JOIN vtiger_transactionscf cf USING (transactionsid)
                          JOIN vtiger_crmentity e ON e.crmid = t.transactionsid
                          JOIN custodian_omniscient.custodian_transactions_fidelity ft ON ft.transaction_id = t.cloud_transaction_id
                          SET t.trade_date = ?, t.operation = ?, cf.transaction_type = ?, cf.transaction_activity = ?, cf.net_amount = ?
                          WHERE t.cloud_transaction_id = ? AND t.account_number = ?";
                $adb->pquery($query, array($v['trade_date'], $v['operation'], $v['transaction_type'], $v['transaction_activity'], $v['amount'],
                                           $v['transaction_id'], $v['account_number']), true);
            }
        }
    }
}


/*
/Array
(
    [omniscient_category] => Flow
    [omniscient_activity] => Withdrawal of funds
[transaction_id] => 62020102229
    [account_number] => 676541830
    [account_source] => B
[account_type_code] => 1
    [account_type_description] => CASH
[amount] => 240.0000000000
    [broker_code] =>
    [buy_sell_indicator] =>
    [certificate_fee] => 0.0000
    [comment] =>
    [comment2] =>
    [commission] => 0.0000000000
    [core_fund_indicator] => N
[cusip] =>
    [custom_short_name] => Colleen Tracy
[div_payable_date] => 0000-00-00
    [div_record_date] => 0000-00-00
    [dtc_code] =>
    [entry_date] => 2020-10-21
    [exchange] =>
    [exchange_code] =>
    [fbsi_short_name] => DDS
[fee_amount] => 0.0000
    [floor_symbol] =>
    [fprs_txn_code] =>
    [fprs_tsn_code_description] =>
    [fund_load_override] => 0.0000
    [fund_load_percent] => 0.0000
    [fund_number] => 0.0000
    [interest_amount] => 0.0000
    [key_code] => 30
    [money_source_id] =>
    [money_source] =>
    [net_amount] => 0.0000
    [order_action] =>
    [plan_name] =>
    [plan_number] =>
    [postage_fee] => 0.0000
    [price] =>
    [primary_account_owner] => COLLEEN A TRACY DDS
[principal_amount] => 0.0000
    [product_name] =>
    [product_type] =>
    [quantity] => 0.0000000000
    [reference_number] =>
    [reg_rep1] =>
    [reg_rep2] =>
    [registration] => Non-Prototype/Corporate Trust
[sec_fee] => 0.0000000000
    [security_description] => CASH
[security_group] =>
    [security_id] => $cash
[service_fee] => 0.0000
    [settlement_date] => 2020-10-21
    [short_term_redemption_fee] => 0.0000
    [source_destination] => client
[state_tax_amount] => 0.0000
    [symbol] => cash
[trade_date] => 2020-10-21
    [transaction_code] => dp
[transaction_code_description] => Deposit
[transaction_key_mnemonic] => ETF
[transaction_key_code_description] => EFT FUNDS RECEIVED
[transaction_security_type] => Cash Account
[transaction_security_type_code] => ca
[trust_income] => 0.0000
    [trust_principal] => 0.0000
    [file_date] => 2020-10-22
    [filename] => gh2transact-(20201022-03-55-59).trn
[insert_date] => 2021-01-26 12:12:52
    [operation] => -
[ownerid] => 33777
    [calculated_amount] => 0
)
*/
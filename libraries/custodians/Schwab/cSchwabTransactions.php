<?php

require_once("libraries/custodians/cCustodian.php");

class cSchwabTransactionsData{
    public  $transaction_id, $account_number, $trade_date, $transaction_code, $security_type, $symbol, $dollar_amount, $account_type, $quantity,
            $brokerage_fee, $unit_cost, $accrued_interest, $broker_code, $filename, $custodian_id, $master_account_number, $master_account_name,
            $business_date, $account_title_line1, $account_title_line2, $account_title_line3, $account_registration, $product_code,
            $product_category_code, $tax_code, $legacy_security_type, $ticker_symbol, $industry_ticker_symbol, $cusip, $schwab_security_number,
            $item_issue_id, $rule_set_suffix_id, $isin, $sedol, $options_display_symbol, $underlying_ticker_symbol,
            $underlying_industry_ticker_symbol, $underlying_cusip, $underlying_schwab_security_number, $underlying_item_issue_id,
            $underlying_rule_set_suffix_id, $underlying_isin, $underlying_sedol, $money_market_code, $transaction_type_code,
            $transaction_subtype_code, $transaction_category, $transaction_source_code, $transaction_source_code_description,
            $transaction_detail_description, $action_code, $transaction_cancel_code, $settlement_date, $transaction_date, $exdividend_date,
            $price, $gross_amount, $debit_credit_indicator, $net_amount, $commission, $exchange_processing_fee, $broker_service_fee,
            $prime_broker_fee, $trade_away_fee, $redemption_fee, $other_fee, $federal_tefra_withholding, $state_tax_withholding,
            $state_receiving_tax, $accounting_rule_code, $order_source_code, $order_number, $trade_order_entry_time_stamp,
            $trade_order_execution_time_stamp, $broker_name, $schwab_from_account, $schwab_to_account, $schwab1_check_number, $sweep_indicator,
            $stock_exchange_code, $interclass_exchange_code, $distribution_rate, $cash_in_lieu_share_quantity, $dividend_interest_share_quantity,
            $cash_in_lieu_rate, $asset_backed_factor, $source_system, $journal_type, $deposit_media, $schwab_cashiering_unique_identifier,
            $recipient_maker_name_line1, $recipient_maker_name_line2, $recipient_maker_name_line3, $frequency, $disbursed_check_number,
            $fed_reference_number, $recipient_maker_account_number, $bank_account_type, $bank_name_part1, $bank_name_part2, $bank_aba_number,
            $intermediary_name, $transaction_check_memo1, $transaction_check_memo2, $retirement_federal_income_tax, $retirement_state_income_tax,
            $retirement_income_tax_state, $publication_time_stamp, $version_marker1, $tips_factor, $closing_price, $version_marker2,
            $transaction_memo, $version_marker3, $closing_price_unfactored, $factor, $factor_date, $file_date, $insert_date, $dupe_flag;

    public  $id, $source_code, $type_code, $subtype_code, $direction, $transaction_activity, $omniscient_category, $omniscient_activity,
            $schwab_category, $operation, $stopping_point, $affects_total, $affects_performance;

    public  $transaction_type;

    public function __construct($data){
        $this->transaction_id = $data['transaction_id'];
        $this->account_number = ltrim($data['account_number'], '0');
        $this->trade_date = $data['trade_date'];
        $this->transaction_code = $data['transaction_code'];
        $this->security_type = $data['security_type'];
        $this->symbol = $data['symbol'];
        $this->dollar_amount = $data['dollar_amount'];
        $this->account_type = $data['account_type'];
        $this->quantity = $data['quantity'];
        $this->brokerage_fee = $data['brokerage_fee'];
        $this->unit_cost = $data['unit_cost'];
        $this->accrued_interest = $data['accrued_interest'];
        $this->broker_code = $data['broker_code'];
        $this->filename = $data['filename'];
        $this->custodian_id = $data['custodian_id'];
        $this->master_account_number = $data['master_account_number'];
        $this->master_account_name = $data['master_account_name'];
        $this->business_date = $data['business_date'];
        $this->account_title_line1 = $data['account_title_line1'];
        $this->account_title_line2 = $data['account_title_line2'];
        $this->account_title_line3 = $data['account_title_line3'];
        $this->account_registration = $data['account_registration'];
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
        $this->underlying_ticker_symbol = $data['underlying_ticker_symbol'];
        $this->underlying_industry_ticker_symbol = $data['underlying_industry_ticker_symbol'];
        $this->underlying_cusip = $data['underlying_cusip'];
        $this->underlying_schwab_security_number = $data['underlying_schwab_security_number'];
        $this->underlying_item_issue_id = $data['underlying_item_issue_id'];
        $this->underlying_rule_set_suffix_id = $data['underlying_rule_set_suffix_id'];
        $this->underlying_isin = $data['underlying_isin'];
        $this->underlying_sedol = $data['underlying_sedol'];
        $this->money_market_code = $data['money_market_code'];
        $this->transaction_type_code = $data['transaction_type_code'];
        $this->transaction_subtype_code = $data['transaction_subtype_code'];
        $this->transaction_category = $data['transaction_category'];
        $this->transaction_source_code = $data['transaction_source_code'];
        $this->transaction_source_code_description = $data['transaction_source_code_description'];
        $this->transaction_detail_description = $data['transaction_detail_description'];
        $this->action_code = $data['action_code'];
        $this->transaction_cancel_code = $data['transaction_cancel_code'];
        $this->settlement_date = $data['settlement_date'];
        $this->transaction_date = $data['transaction_date'];
        $this->exdividend_date = $data['exdividend_date'];
        $this->price = $data['price'];
        $this->gross_amount = $data['gross_amount'];
        $this->debit_credit_indicator = $data['debit_credit_indicator'];
        $this->net_amount = $data['net_amount'];
        $this->commission = $data['commission'];
        $this->exchange_processing_fee = $data['exchange_processing_fee'];
        $this->broker_service_fee = $data['broker_service_fee'];
        $this->prime_broker_fee = $data['prime_broker_fee'];
        $this->trade_away_fee = $data['trade_away_fee'];
        $this->redemption_fee = $data['redemption_fee'];
        $this->other_fee = $data['other_fee'];
        $this->federal_tefra_withholding = $data['federal_tefra_withholding'];
        $this->state_tax_withholding = $data['state_tax_withholding'];
        $this->state_receiving_tax = $data['state_receiving_tax'];
        $this->accounting_rule_code = $data['accounting_rule_code'];
        $this->order_source_code = $data['order_source_code'];
        $this->order_number = $data['order_number'];
        $this->trade_order_entry_time_stamp = $data['trade_order_entry_time_stamp'];
        $this->trade_order_execution_time_stamp = $data['trade_order_execution_time_stamp'];
        $this->broker_name = $data['broker_name'];
        $this->schwab_from_account = $data['schwab_from_account'];
        $this->schwab_to_account = $data['schwab_to_account'];
        $this->schwab1_check_number = $data['schwab1_check_number'];
        $this->sweep_indicator = $data['sweep_indicator'];
        $this->stock_exchange_code = $data['stock_exchange_code'];
        $this->interclass_exchange_code = $data['interclass_exchange_code'];
        $this->distribution_rate = $data['distribution_rate'];
        $this->cash_in_lieu_share_quantity = $data['cash_in_lieu_share_quantity'];
        $this->dividend_interest_share_quantity = $data['dividend_interest_share_quantity'];
        $this->cash_in_lieu_rate = $data['cash_in_lieu_rate'];
        $this->asset_backed_factor = $data['asset_backed_factor'];
        $this->source_system = $data['source_system'];
        $this->journal_type = $data['journal_type'];
        $this->deposit_media = $data['deposit_media'];
        $this->schwab_cashiering_unique_identifier = $data['schwab_cashiering_unique_identifier'];
        $this->recipient_maker_name_line1 = $data['recipient_maker_name_line1'];
        $this->recipient_maker_name_line2 = $data['recipient_maker_name_line2'];
        $this->recipient_maker_name_line3 = $data['recipient_maker_name_line3'];
        $this->frequency = $data['frequency'];
        $this->disbursed_check_number = $data['disbursed_check_number'];
        $this->fed_reference_number = $data['fed_reference_number'];
        $this->recipient_maker_account_number = $data['recipient_maker_account_number'];
        $this->bank_account_type = $data['bank_account_type'];
        $this->bank_name_part1 = $data['bank_name_part1'];
        $this->bank_name_part2 = $data['bank_name_part2'];
        $this->bank_aba_number = $data['bank_aba_number'];
        $this->intermediary_name = $data['intermediary_name'];
        $this->transaction_check_memo1 = $data['transaction_check_memo1'];
        $this->transaction_check_memo2 = $data['transaction_check_memo2'];
        $this->retirement_federal_income_tax = $data['retirement_federal_income_tax'];
        $this->retirement_state_income_tax = $data['retirement_state_income_tax'];
        $this->retirement_income_tax_state = $data['retirement_income_tax_state'];
        $this->publication_time_stamp = $data['publication_time_stamp'];
        $this->version_marker1 = $data['version_marker1'];
        $this->tips_factor = $data['tips_factor'];
        $this->closing_price = $data['closing_price'];
        $this->version_marker2 = $data['version_marker2'];
        $this->transaction_memo = $data['transaction_memo'];
        $this->version_marker3 = $data['version_marker3'];
        $this->closing_price_unfactored = $data['closing_price_unfactored'];
        $this->factor = $data['factor'];
        $this->factor_date = $data['factor_date'];
        $this->file_date = $data['file_date'];
        $this->insert_date = $data['insert_date'];
        $this->dupe_flag = $data['dupe_flag'];

        $this->id = $data['id'];
        $this->source_code = $data['source_code'];
        $this->type_code = $data['type_code'];
        $this->subtype_code = $data['subtype_code'];
        $this->direction = $data['direction'];
        $this->transaction_activity = $data['transaction_activity'];
        $this->omniscient_category = $data['omniscient_category'];
        $this->omniscient_activity = $data['omniscient_activity'];
        $this->schwab_category = $data['schwab_category'];
        $this->operation = (is_null($data['operation'])) ? '' : $data['operation'];
        $this->stopping_point = $data['stopping_point'];
        $this->affects_total = $data['affects_total'];
        $this->affects_performance = $data['affects_performance'];


        if(strlen($data['omniscient_category']) > 2)
            $this->transaction_type = $data['omniscient_category'];
        else
            $this->transaction_type = $data['transaction_category'];

        if(strlen($data['omniscient_activity'] > 2))
            $this->transaction_activity = $data['omniscient_activity'];
        else
            $this->transaction_activity = $data['transaction_activity'];

        if($data['price'] == 0 && $data['closing_price'] != 0)
            $data['price'] = $data['closing_price'];
        else
            $data['price'] = 1;

        $data['quantity'] = abs($data['quantity']);
        $data['net_amount'] = abs($data['net_amount']);
        $data['gross_amount'] = abs($data['gross_amount']);

        if(strlen($data['ticker_symbol']) < 2 && strlen($data['cusip']) > 2)
            $data['ticker_symbol'] = $data['cusip'];

        if(strlen($data['ticker_symbol']) < 2)
            $data['ticker_symbol'] = 'SCASH';

        if($data['gross_amount'] == 0)
            $data['net_amount'] = $data['quantity'] * $data['closing_price'] * ModSecurities_Module_Model::GetSecurityPriceAdjustment($data['symbol']);
        else
            $data['net_amount'] = $data['gross_amount'];
    }
}

/**
 * Class cSchwabPortfolios
 * This class allows the pulling of data from the custodian database
 */
class cSchwabTransactions extends cCustodian
{
    use tTransactions;
    protected $transactions_data;//Holds the pricing information

    protected function FillAccountNumbersFromRepCodes(){
        global $adb;
        $params = array();
        $questions = generateQuestionMarks($this->rep_codes);
        $params[] = $this->rep_codes;

        $query = "SELECT account_number 
                  FROM {$this->database}.{$this->portfolio_table} p 
                  WHERE rep_code IN ({$questions})
                  ORDER BY file_date DESC";

        $result = $adb->pquery($query, array($this->rep_codes), true);
        if($adb->num_rows($result) > 0)
            while($r = $adb->fetchByAssoc($result)){
                $this->account_numbers[] = $r;
            }
    }
    /**
     * cSchwabPortfolios constructor.
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

        return $this->transactions_data;
    }

    protected function AccountsWithLeadingZeros(array $account_numbers){
        $tmp = array();
        foreach($account_numbers AS $k => $v){
            $tmp[] = "00" . $v['account_number'];
        }
        return $tmp;
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
#        $questions = generateQuestionMarks($this->account_numbers);
        $leading_zeros = self::AccountsWithLeadingZeros($this->account_numbers);
        $merged_accounts = array_merge($this->account_numbers, $leading_zeros);
        $questions = generateQuestionMarks($merged_accounts);
        $params[] = $merged_accounts;
        $params[] = $start;
        $params[] = $end;

        if (empty($this->columns))
            $fields = "*";
        else {
            $fields = implode ( ", ", $this->columns );
        }

        $query = "SELECT {$fields} FROM {$this->database}.{$this->table} t
                  JOIN {$this->database}.schwabmapping m ON m.source_code = t.transaction_source_code AND m.type_code = t.transaction_type_code AND m.subtype_code = t.transaction_subtype_code AND m.direction = t.debit_credit_indicator
                  WHERE account_number IN ({$questions}) AND trade_date BETWEEN ? AND ?";
        $result = $adb->pquery($query, $params, true);

        if ($adb->num_rows($result) > 0) {
            while ($r = $adb->fetchByAssoc($result)) {
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
     * Using the cSchwabTransactionsData class, create the portfolios.  Used with a pre-filled in cSchwabPortfolioData class (done manually)
     * @param cSchwabPortfolioData $data
     * @throws Exception
     */
    public function CreateNewTransactionUsingcSchwabTransactionsData(cSchwabTransactionsData $data){
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
/*                    print_r($this->transactions_data[$account_number]); echo " .. ";
                    echo $transaction_id;exit;
                    print_r($this->transactions_data[$account_number][$transaction_id]);exit;*/
                    $data = $this->transactions_data[$account_number][$transaction_id];
                    if (!empty($data)) {
                        $tmp = new cSchwabTransactionsData($data);
                        $this->CreateNewTransactionUsingcSchwabTransactionsData($tmp);
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
                        $tmp = new cSchwabTransactionsData($data);
                        $this->UpdateTransactionsUsingcSchwabTransactionsData($tmp);
                    }
                }
            }
        }
    }

    /**
     * Create the new entity in the crmentity table
     * @param $crmid
     * @param $owner
     * @param cSchwabTransactionsData $data
     */
    protected function FillEntityTable($crmid, $owner, cSchwabTransactionsData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;
        $params[] = 1;
        $params[] = $owner;
        $params[] = 1;
        $params[] = 'Transactions';
        $params[] = $data->transaction_detail_description;
        $query = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, createdtime, modifiedtime, label)
                  VALUES (?, ?, ?, ?, ?, NOW(), NOW(), ?)";
        $adb->pquery($query, $params, true);
    }

    /**
     * Creates data in the vtiger_transactioninformation table
     * @param $crmid
     * @param cSchwabTransactionsData $data
     */
    protected function FillTransactionTable($crmid, cSchwabTransactionsData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;
        $params[] = $data->account_number;
        $params[] = $data->ticker_symbol;
        $params[] = $data->price;
        $params[] = $data->quantity;
        $params[] = $data->trade_date;
        $params[] = 'SCHWAB';
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
     * @param cSchwabTransactionsData $data
     */
    protected function FillTransactionCFTable($crmid, cSchwabTransactionsData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;
        $params[] = 'SCHWAB';
        $params[] = $data->transaction_type;
        $params[] = $data->transaction_activity;
        $params[] = $data->net_amount;
        $params[] = $data->broker_service_fee + $data->prime_broker_fee;
        $params[] = $data->commission + $data->other_fee;
        $params[] = $data->debit_credit_indicator;
        $params[] = $data->transaction_detail_description;
        $params[] = $data->filename;
        $params[] = $data->transaction_source_code;
        $params[] = $data->transaction_type_code;
        $params[] = $data->transaction_subtype_code;

        $questions = generateQuestionMarks($params);
        $query = "INSERT INTO vtiger_transactionscf (transactionsid, custodian, transaction_type, transaction_activity, net_amount, broker_fee, 
                                                     other_fee, schwab_direction, description, filename, key_mnemonic_description, 
                                                     transaction_key_code_description, transaction_code_description)
                  VALUES ({$questions})";
        $adb->pquery($query, $params, true);
    }

    /**
     * Update the transaction in the CRM using the cSchwabTransactionsData class
     * @param cSchwabTransactionsData $data
     */
    public function UpdateTransactionsUsingcSchwabTransactionsData(cSchwabTransactionsData $data){
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

    public function RemoveDupesByZeroingOut(array $account_number){
        global $adb;
        if(empty($account_number))
            return;

        foreach($account_number AS $k => $v){
            $sdate = PortfolioInformation_Module_Model::GetFirstTransactionDate($v);
            $query = "DROP TABLE IF EXISTS TradeDates";
            $adb->pquery($query, array());
            $query = "DROP TABLE IF EXISTS DupeDays";
            $adb->pquery($query, array());
            $query = "DROP TABLE IF EXISTS NumTransactions";
            $adb->pquery($query, array());

            $query = "CREATE TEMPORARY TABLE TradeDates
                      SELECT trade_date, master_account_number, COUNT(*) AS count
                      FROM custodian_omniscient.custodian_transactions_schwab
                      WHERE account_number=?
                      AND trade_date BETWEEN ? AND NOW()
                      GROUP BY master_account_number, trade_date
                      ORDER BY trade_date DESC";
            $adb->pquery($query, array($v, $sdate));

            $query = "CREATE TEMPORARY TABLE DupeDays
                      SELECT trade_date, COUNT(*) AS count
                      FROM TradeDates
                      GROUP BY trade_date
                      ORDER BY trade_date DESC";
            $adb->pquery($query, array());

            $query = "DELETE FROM DupeDays WHERE count <= 1";
            $adb->pquery($query, array());
            $query = "SELECT * FROM DupeDays";
            $adb->pquery($query, array());

            $query = "CREATE TEMPORARY TABLE NumTransactions
                      SELECT master_account_number, COUNT(*) num_transactions
                      FROM custodian_omniscient.custodian_transactions_schwab
                      WHERE account_number = ?
                            AND trade_date = ?
                      GROUP BY master_account_number
                      ORDER BY COUNT(*) DESC";
            $adb->pquery($query, array($v, $sdate));

            $query = "SELECT * FROM custodian_omniscient.custodian_transactions_schwab
                      WHERE master_account_number IN (SELECT master_account_number FROM NumTransactions)
                      AND trade_date = '2020-08-31'
                      AND account_number = '0080256918'";
            $adb->pquery($query, array());
        }
    }
}
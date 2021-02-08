<?php
require_once("libraries/custodians/cCustodian.php");

class cSchwabPortfolioData{
    public  $account_number, $description, $address1, $address2, $address3, $address4, $phone1, $phone2, $inception, $branch_id,
            $tax_id, $mmf_selection, $cash_instructions, $margin_instructions, $restrictions, $approval_level, $record_type, $custodian_id,
            $master_account_number, $account_name, $business_date, $account_title_line1, $account_title_line2, $account_title_line3,
            $account_status, $account_registration, $account_type, $taxpayer_title, $taxpayer_first_name, $taxpayer_middle_name,
            $taxpayer_last_name, $taxpayer_suffix, $tax_withhold_code, $primary_contact, $alias_name, $account_mailing_city,
            $account_mailing_state, $account_mailing_zip, $account_mailing_country_code, $email_address, $phone, $business_phone,
            $date_opened, $schwab_branch_code, $sweep_status_code, $account_cash_instructions_cash_account,
            $account_cash_instructions_margin_account, $margin_account_indicator, $account_restrictions, $additional_mailings,
            $number_of_additional_mailings, $approved_option_level, $beneficiary_on_file, $number_of_beneficiaries, $payment_features_checks,
            $payment_features_debit_card, $payment_features_billpay, $undeliverable_mail_flag, $fa_fee_status, $interest_bearing_feature_code,
            $interest_bearing_features_money_market_code, $interest_bearing_features_ticker_symbol, $interest_bearing_features_item_issue_id,
            $interest_bearing_features_rule_set_suffix, $proxy_voting_authority, $proxy_mailing, $statement_preferences,
            $account_taxable_indicator, $default_lot_selection_method, $cost_method, $cost_basis_method_for_mutual_funds,
            $cost_basis_method_non_mutual_funds, $cost_basis_method_date, $version_marker_number, $customer_type, $organization_primary_name,
            $version_marker_number2, $restriction_reason_code1, $restriction_reason_code2, $restriction_reason_code3,
            $restriction_reason_code4, $restriction_reason_code5, $version_marker_number3, $ssn, $version_marker_number4,
            $prime_broker_enabled_indicator, $managed_account_platform_code, $managed_account_money_manager,
            $managed_account_investment_strategy, $rep_code, $master_rep_code, $omni_code, $omniscient_type;

    public  $account_value, $net_cash, $margin_balance, $available_to_pay, $margin_buying_power, $money_market_funds, $mtd_margin_interest,
            $daily_interest, $market_value_long, $market_value_short, $month_end_div_payout, $market_long_minus_cash, $market_short_minus_cash,
            $market_value, $as_of_date, $custodian,  $master_account_name,
             $net_credit_debit, $mtd_margin_int, $daily_margin_int, $equity_excluding_option, $equity_percentage, $equity_including_option,
            $option_requirements, $month_end_dividend_payment, $maintenance_call, $mvl_cash_excluding_options, $net_mv_positions,
            $net_mv_plus_cash, $cash_balance, $cash_margin, $version_marker, $bank_sweep_ibf, $file_date, $filename, $insert_date;

    public function __construct($data){
        $this->custodian = 'SCHWAB';
        $this->account_number = $data['personal']['account_number'];
        $this->description = $data['personal']['description'];
        $this->address1 = $data['personal']['address1'];
        $this->address2 = $data['personal']['address2'];
        $this->address3 = $data['personal']['address3'];
        $this->address4 = $data['personal']['address4'];
        $this->phone1 = $data['personal']['phone1'];
        $this->phone2 = $data['personal']['phone2'];
        $this->inception = $data['personal']['inception'];
        $this->branch_id = $data['personal']['branch_id'];
        $this->tax_id = $data['personal']['tax_id'];
        $this->mmf_selection = $data['personal']['mmf_selection'];
        $this->cash_instructions = $data['personal']['cash_instructions'];
        $this->margin_instructions = $data['personal']['margin_instructions'];
        $this->restrictions = $data['personal']['restrictions'];
        $this->approval_level = $data['personal']['approval_level'];
        $this->record_type = $data['personal']['record_type'];
        $this->custodian_id = $data['personal']['custodian_id'];
        $this->master_account_number = $data['personal']['master_account_number'];
        $this->account_name = $data['personal']['account_name'];
        $this->business_date = $data['personal']['business_date'];
        $this->account_title_line1 = $data['personal']['account_title_line1'];
        $this->account_title_line2 = $data['personal']['account_title_line2'];
        $this->account_title_line3 = $data['personal']['account_title_line3'];
        $this->account_status = $data['personal']['account_status'];
        $this->account_registration = $data['personal']['account_registration'];
        $this->account_type = $data['personal']['account_type'];
        $this->taxpayer_title = $data['personal']['taxpayer_title'];
        $this->taxpayer_first_name = $data['personal']['taxpayer_first_name'];
        $this->taxpayer_middle_name = $data['personal']['taxpayer_middle_name'];
        $this->taxpayer_last_name = $data['personal']['taxpayer_last_name'];
        $this->taxpayer_suffix = $data['personal']['taxpayer_suffix'];
        $this->tax_withhold_code = $data['personal']['tax_withhold_code'];
        $this->primary_contact = $data['personal']['primary_contact'];
        $this->alias_name = $data['personal']['alias_name'];
        $this->account_mailing_city = $data['personal']['account_mailing_city'];
        $this->account_mailing_state = $data['personal']['account_mailing_state'];
        $this->account_mailing_zip = $data['personal']['account_mailing_zip'];
        $this->account_mailing_country_code = $data['personal']['account_mailing_country_code'];
        $this->email_address = $data['personal']['email_address'];
        $this->email_address = $data['personal']['omniscient_type'];
        $this->phone = $data['personal']['phone'];
        $this->business_phone = $data['personal']['business_phone'];
        $this->date_opened = $data['personal']['date_opened'];
        $this->schwab_branch_code = $data['personal']['schwab_branch_code'];
        $this->sweep_status_code = $data['personal']['sweep_status_code'];
        $this->account_cash_instructions_cash_account = $data['personal']['account_cash_instructions_cash_account'];
        $this->account_cash_instructions_margin_account = $data['personal']['account_cash_instructions_margin_account'];
        $this->margin_account_indicator = $data['personal']['margin_account_indicator'];
        $this->account_restrictions = $data['personal']['account_restrictions'];
        $this->additional_mailings = $data['personal']['additional_mailings'];
        $this->number_of_additional_mailings = $data['personal']['number_of_additional_mailings'];
        $this->approved_option_level = $data['personal']['approved_option_level'];
        $this->beneficiary_on_file = $data['personal']['beneficiary_on_file'];
        $this->number_of_beneficiaries = $data['personal']['number_of_beneficiaries'];
        $this->payment_features_checks = $data['personal']['payment_features_checks'];
        $this->payment_features_debit_card = $data['personal']['payment_features_debit_card'];
        $this->payment_features_billpay = $data['personal']['payment_features_billpay'];
        $this->undeliverable_mail_flag = $data['personal']['undeliverable_mail_flag'];
        $this->fa_fee_status = $data['personal']['fa_fee_status'];
        $this->interest_bearing_feature_code = $data['personal']['interest_bearing_feature_code'];
        $this->interest_bearing_features_money_market_code = $data['personal']['interest_bearing_features_money_market_code'];
        $this->interest_bearing_features_ticker_symbol = $data['personal']['interest_bearing_features_ticker_symbol'];
        $this->interest_bearing_features_item_issue_id = $data['personal']['interest_bearing_features_item_issue_id'];
        $this->interest_bearing_features_rule_set_suffix = $data['personal']['interest_bearing_features_rule_set_suffix'];
        $this->proxy_voting_authority = $data['personal']['proxy_voting_authority'];
        $this->proxy_mailing = $data['personal']['proxy_mailing'];
        $this->statement_preferences = $data['personal']['statement_preferences'];
        $this->account_taxable_indicator = $data['personal']['account_taxable_indicator'];
        $this->default_lot_selection_method = $data['personal']['default_lot_selection_method'];
        $this->cost_method = $data['personal']['cost_method'];
        $this->cost_basis_method_for_mutual_funds = $data['personal']['cost_basis_method_for_mutual_funds'];
        $this->cost_basis_method_non_mutual_funds = $data['personal']['cost_basis_method_non_mutual_funds'];
        $this->cost_basis_method_date = $data['personal']['cost_basis_method_date'];
        $this->version_marker_number = $data['personal']['version_marker_number'];
        $this->customer_type = $data['personal']['customer_type'];
        $this->organization_primary_name = $data['personal']['organization_primary_name'];
        $this->version_marker_number2 = $data['personal']['version_marker_number2'];
        $this->restriction_reason_code1 = $data['personal']['restriction_reason_code1'];
        $this->restriction_reason_code2 = $data['personal']['restriction_reason_code2'];
        $this->restriction_reason_code3 = $data['personal']['restriction_reason_code3'];
        $this->restriction_reason_code4 = $data['personal']['restriction_reason_code4'];
        $this->restriction_reason_code5 = $data['personal']['restriction_reason_code5'];
        $this->version_marker_number3 = $data['personal']['version_marker_number3'];
        $this->ssn = $data['personal']['ssn'];
        $this->version_marker_number4 = $data['personal']['version_marker_number4'];
        $this->prime_broker_enabled_indicator = $data['personal']['prime_broker_enabled_indicator'];
        $this->managed_account_platform_code = $data['personal']['managed_account_platform_code'];
        $this->managed_account_money_manager = $data['personal']['managed_account_money_manager'];
        $this->managed_account_investment_strategy = $data['personal']['managed_account_investment_strategy'];
        $this->rep_code = $data['personal']['rep_code'];
        $this->master_rep_code = $data['personal']['master_rep_code'];
        $this->omni_code = $data['personal']['omni_code'];

        $this->account_value = $data['balance']['account_value'];
        $this->net_cash = $data['balance']['net_cash'];
        $this->margin_balance = $data['balance']['margin_balance'];
        $this->available_to_pay = $data['balance']['available_to_pay'];
        $this->margin_buying_power = $data['balance']['margin_buying_power'];
        $this->money_market_funds = $data['balance']['money_market_funds'];
        $this->mtd_margin_interest = $data['balance']['mtd_margin_interest'];
        $this->daily_interest = $data['balance']['daily_interest'];
        $this->market_value_long = $data['balance']['market_value_long'];
        $this->market_value_short = $data['balance']['market_value_short'];
        $this->month_end_div_payout = $data['balance']['month_end_div_payout'];
        $this->market_long_minus_cash = $data['balance']['market_long_minus_cash'];
        $this->market_short_minus_cash = $data['balance']['market_short_minus_cash'];
        $this->market_value = $data['balance']['market_value'];
        $this->as_of_date = $data['balance']['as_of_date'];
        $this->custodian = $data['balance']['custodian'];
        $this->master_account_name = $data['balance']['master_account_name'];
        $this->net_credit_debit = $data['balance']['net_credit_debit'];
        $this->mtd_margin_int = $data['balance']['mtd_margin_int'];
        $this->daily_margin_int = $data['balance']['daily_margin_int'];
        $this->equity_excluding_option = $data['balance']['equity_excluding_option'];
        $this->equity_percentage = $data['balance']['equity_percentage'];
        $this->equity_including_option = $data['balance']['equity_including_option'];
        $this->option_requirements = $data['balance']['option_requirements'];
        $this->month_end_dividend_payment = $data['balance']['month_end_dividend_payment'];
        $this->maintenance_call = $data['balance']['maintenance_call'];
        $this->mvl_cash_excluding_options = $data['balance']['mvl_cash_excluding_options'];
        $this->net_mv_positions = $data['balance']['net_mv_positions'];
        $this->net_mv_plus_cash = $data['balance']['net_mv_plus_cash'];
        $this->cash_balance = $data['balance']['cash_balance'];
        $this->cash_margin = $data['balance']['cash_margin'];
        $this->version_marker = $data['balance']['version_marker'];
        $this->bank_sweep_ibf = $data['balance']['bank_sweep_ibf'];
        $this->file_date = $data['balance']['file_date'];
        $this->filename = $data['balance']['filename'];
        $this->insert_date = $data['balance']['insert_date'];
    }
}

/**
 * Class cSchwabPortfolios
 * This class allows the pulling of data from the custodian database
 */
class cSchwabPortfolios extends cCustodian {
    use tPortfolios;
    private $portfolio_data;//Holds both personal and balance information

    /**
     * cSchwabPortfolios constructor.
     * @param string $custodian_name
     * @param string $database
     * @param string $module
     * @param string $portfolio_table
     * @param string $table (REFERS TO BALANCE TABLE)
     */
    public function __construct(string $custodian_name, string $database, string $module,
                                string $portfolio_table, string $balance_table, array $rep_codes, $pull_all=true){
        $this->name = $custodian_name;
        $this->database = $database;
        $this->module = $module;
        $this->portfolio_table = $portfolio_table;
        $this->table = $balance_table;
        if(!empty($rep_codes) && $pull_all == true) {
            $this->SetRepCodes($rep_codes);
            $this->GetPortfolioPersonalData();
            $this->GetPortfolioBalanceData();
            $this->SetupPortfolioComparisons();
        }
    }

    public function GetPortfolioPersonalData(){
        global $adb;
        $params = array();
        $questions = generateQuestionMarks($this->account_numbers);
        $params[] = $this->account_numbers;

        if(empty($this->columns))
            $fields = "*";
        else{
            $fields = implode ( ", ", $this->columns );
        }

        $query = "SELECT {$fields} FROM {$this->database}.{$this->portfolio_table}
                  LEFT JOIN {$this->database}.portfolios_mapping_schwab pmap ON pmap.schwab_code = account_registration 
                  WHERE account_number IN ({$questions})";
        $result = $adb->pquery($query, $params, true);

        if($adb->num_rows($result) > 0){
            while($r = $adb->fetchByAssoc($result)){
                $this->portfolio_data[$r['account_number']]['personal'] = $r;
            }
        }
        return $this->portfolio_data;
    }

    public function GetPortfolioBalanceData($date=null){
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
            $date = $this->GetLatestBalanceDate("as_of_date");

        $params[] = $date;
        $query = "SELECT {$fields} FROM {$this->database}.{$this->table} 
                  WHERE account_number IN ({$questions}) AND as_of_date = ?";
        $result = $adb->pquery($query, $params, true);

        if($adb->num_rows($result) > 0){
            while($r = $adb->fetchByAssoc($result)){
                $this->portfolio_data[$r['account_number']]['balance'] = $r;
            }
        }
        return $this->portfolio_data;
    }

    public function GetPortfolioData(){
        return $this->portfolio_data;
    }

    /**
     * Create the new entity in the crmentity table
     * @param $crmid
     * @param $owner
     * @param cSchwabPortfolios $data
     */
    protected function FillEntityTable($crmid, $owner, cSchwabPortfolioData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;
        $params[] = 1;
        $params[] = $owner;
        $params[] = 1;
        $params[] = 'PortfolioInformation';
        $params[] = $data->account_number;
        $query = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, createdtime, modifiedtime, label)
                  VALUES (?, ?, ?, ?, ?, NOW(), NOW(), ?)";
        $adb->pquery($query, $params, true);
    }

    /**
     * Creates data in the vtiger_portfolioinformation table
     * @param $crmid
     * @param cSchwabPortfolios $data
     */
    protected function FillPortfolioTable($crmid, cSchwabPortfolioData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;
        $params[] = $data->account_number;
        $params[] = 'SCHWAB';
        $params[] = $data->taxpayer_first_name;
        $params[] = ($data->taxpayer_last_name = '') ? $data->account_title_line1 : $data->taxpayer_last_name;
        $params[] = $data->account_type;

        $questions = generateQuestionMarks($params);
        $query = "INSERT INTO vtiger_portfolioinformation (portfolioinformationid, account_number, origination, 
                                                           first_name, last_name, account_type)
                  VALUES ({$questions})";
        $adb->pquery($query, $params, true);
    }

    /**
     * Creates data in the vtiger_portfolioinformationcf table
     * @param $crmid
     * @param cSchwabPortfolios $data
     */
    protected function FillPortfolioCFTable($crmid, cSchwabPortfolioData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;
        $params[] = $data->description;
        $params[] = $data->rep_code;
        $params[] = $data->master_rep_code;
        $params[] = $data->address1;
        $params[] = $data->address2;
        $params[] = $data->address3;
        $params[] = $data->address4;
        $params[] = $data->account_title_line1;
        $params[] = $data->account_title_line2;
        $params[] = $data->account_mailing_state;
        $params[] = $data->account_mailing_city;
        $params[] = $data->account_mailing_zip;
        $params[] = $data->account_title_line3;
        $params[] = $data->date_opened;
        $params[] = $data->omniscient_type;
        $params[] = $data->omni_code;
        $params[] = $data->email_address;

        $questions = generateQuestionMarks($params);
        $query = "INSERT INTO vtiger_portfolioinformationcf (portfolioinformationid, description, production_number, master_production_number,
                              address1, address2, address3,address4, account_title1, account_title2, state, city, zip, account_title3, 
                              custodian_inception, cf_2549, omniscient_control_number, email_address)
                  VALUES ({$questions})";
        $adb->pquery($query, $params, true);
    }

    protected function UpdatePortfolios(cSchwabPortfolioData $data){
        global $adb;
        $params[] = $data->taxpayer_first_name;
        $params[] = ($data->taxpayer_last_name = '') ? $data->account_title_line1 : $data->taxpayer_last_name;
        $params[] = $data->description;
        $params[] = $data->address1;
        $params[] = $data->address2;
        $params[] = $data->address3;
        $params[] = $data->address4;
        $params[] = $data->account_title_line1;
        $params[] = $data->account_title_line2;
        $params[] = $data->account_mailing_state;
        $params[] = $data->account_mailing_city;
        $params[] = $data->account_mailing_zip;
        $params[] = $data->account_title_line3;
        $params[] = $data->account_title_line3;
        $params[] = $data->rep_code;
        $params[] = $data->date_opened;
        $params[] = $data->master_rep_code;
        $params[] = $data->omni_code;
        $params[] = $data->email_address;

        $params[] = $data->account_value;
        $params[] = $data->net_mv_positions;
        $params[] = $data->cash_balance;
        $params[] = $data->net_credit_debit;
        $params[] = $data->margin_balance;
        $params[] = $data->available_to_pay;
        $params[] = $data->as_of_date;
        $params[] = $data->filename;
        $params[] = $data->account_number;

        $query = "UPDATE vtiger_portfolioinformation p 
                  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
                  SET p.first_name = ?, p.last_name = ?, cf.description = ?, cf.address1 = ?, cf.address2 = ?, cf.address3 = ?, 
                      cf.address4 = ?, cf.account_title1 = ?, cf.account_title2 = ?, cf.state = ?, cf.city = ?, cf.zip = ?,
                      cf.account_title3 = ?, p.account_type = ?, cf.production_number = ?, cf.custodian_inception = ?,
                      cf.master_production_number = ?, cf.omniscient_control_number = ?, cf.email_address = ?,
                      p.total_value = ?, cf.securities = ?, cf.cash = ?, cf.net_credit_debit = ?, p.margin_balance = ?, p.available_to_pay = ?, 
                      cf.stated_value_date = ?, cf.custodian_source = ?
                      WHERE p.account_number = ?";
        $adb->pquery($query, $params, true);
    }

            /**
             * Using the cSchwabPortfolios class, create the portfolios.  Used with a pre-filled in cSchwabPortfolios class (done manually)
             * @param cSchwabPortfolios $data
             * @throws Exception
             */
    public function CreateNewPortfolioUsingcSchwabPortfolios(cSchwabPortfolioData $data){
        if(!$this->DoesAccountNumberExistInCRM($data->account_number)) {//If the account number doesn't exist yet, create it
#            echo $data->account_number . ' does not exist!';exit;
            $crmid = $this->UpdateEntitySequence();
            $owner = $this->repcode_mapping[strtoupper($data->rep_code)];

            $this->FillEntityTable($crmid, $owner, $data);
            $this->FillPortfolioTable($crmid, $data);
            $this->FillPortfolioCFTable($crmid, $data);
            if($this->DoesAccountNumberExistInCRM($data->account_number))//Confirm the account now exists in the CRM
                $this->existing_accounts[] = $data->account_number;//Add the newly created account to existing accounts because it now exists
        }
    }

    /**
     * Auto creates the portfolio's based on the data loaded into the $portfolio_data member.  If the account number exists in this data, it will be created
     */
    public function CreateNewPortfoliosFromPortfolioData(array $account_numbers){
        if(!empty($account_numbers)) {
            foreach ($account_numbers AS $k => $v) {
                $data = $this->portfolio_data[$v];
                if (!empty($data)) {
                    $tmp = new cSchwabPortfolioData($data);
                    $this->CreateNewPortfolioUsingcSchwabPortfolios($tmp);
                }
            }
        }
    }

    /**
     * Auto creates the portfolio's based on the data loaded into the $portfolio_data member.  If the account number exists in this data, it will be created
     */
    public function UpdatePortfoliosFromPortfolioData(array $account_numbers){
        if(!empty($account_numbers)) {
            foreach ($account_numbers AS $k => $v) {
                $data = $this->portfolio_data[$v];
                if (!empty($data)) {
                    $tmp = new cSchwabPortfolioData($data);
                    $this->UpdatePortfolios($tmp);
                }
            }
        }
    }

    static public function CreateNewPortfoliosForRepCodes($rep_codes){
        global $adb;
        $custodian_accounts = PortfolioInformation_Module_Model::GetAccountNumbersFromCustodianUsingRepCodes("Schwab", $rep_codes);
        $crm_accounts = PortfolioInformation_Module_Model::GetAccountNumbersFromRepCodeOpenAndClosed($rep_codes);

        $new = array_diff($custodian_accounts, $crm_accounts);

        if(!empty($new)) {
            $questions = generateQuestionMarks($new);

            $query = "SELECT p.account_number, 'Schwab' AS custodian, IncreaseAndReturnCrmEntitySequence() AS crmid, p.description, p.rep_code, p.master_rep_code, NOW() AS generated_time
                      FROM custodian_omniscient.custodian_portfolios_schwab p 
                      WHERE p.account_number NOT IN ({$questions})";
            $result = $adb->pquery($query, array($new));

            if($adb->num_rows($result) > 0) {
                while ($v = $adb->fetchByAssoc($result)) {
                    $query = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, createdtime, modifiedtime, label)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    $adb->pquery($query, array($v['crmid'], 1, 1, 1, 'PortfolioInformation', $v['generated_time'], $v['generated_time'], $v['account_number']));

                    $query = "INSERT INTO vtiger_portfolioinformation (portfolioinformationid, account_number, origination)
                              VALUES (?, ?, ?)";
                    $adb->pquery($query, array($v['crmid'], $v['account_number'], $v['custodian']));

                    $query = "INSERT INTO vtiger_portfolioinformationcf (portfolioinformationid, description, production_number, master_production_number)
                              VALUES (?, ?, ?, ?)";
                    $adb->pquery($query, array($v['crmid'], $v['description'], $v['rep_code'], $v['master_rep_code']));
                }
            }
        }
    }

    static public function UpdateAllPortfoliosForAccounts(array $account_number){
        global $adb;
        $questions = generateQuestionMarks($account_number);
        $query = "SELECT f.taxpayer_first_name, CASE WHEN f.taxpayer_last_name = '' THEN f.account_title_line1 ELSE f.taxpayer_last_name END AS taxpayer_last_name, 
                         f.description, f.address1, f.address2, f.address3, f.address4, f.account_title_line1, f.account_title_line2, f.account_mailing_state,
                         f.account_mailing_city, f.account_mailing_zip, f.account_title_line3, f.account_title_line3, f.account_title_line3 AS accounttype, f.rep_code, f.date_opened,
                         CASE WHEN pmap.omniscient_type != '' THEN pmap.omniscient_type ELSE cf.cf_2549 END AS omniscient_type, f.master_rep_code,
                         f.omni_code, f.email_address,
                         bal.account_value, bal.net_mv_positions, bal.cash_balance, bal.net_credit_debit, bal.margin_balance, bal.available_to_pay,
                         bal.as_of_date, bal.filename, 0 AS accountclosed, p.portfolioinformationid
                  FROM vtiger_portfolioinformation p 
                  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid) 
                  JOIN custodian_omniscient.custodian_portfolios_schwab f ON f.account_number = p.account_number
                  JOIN custodian_omniscient.latestpositiondates lpd ON lpd.rep_code = cf.production_number
                  JOIN custodian_omniscient.custodian_balances_schwab bal ON bal.account_number = p.account_number AND bal.as_of_date = lpd.last_position_date
                  LEFT JOIN portfolios_mapping_schwab pmap ON pmap.schwab_code = f.account_registration
                  WHERE p.account_number IN ({$questions})";
        $result = $adb->pquery($query, array($account_number));

        if($adb->num_rows($result) > 0){
            $query = "UPDATE vtiger_portfolioinformation p 
                      JOIN vtiger_portfolioinformationcf cf ON p.portfolioinformationid = cf.portfolioinformationid 
                      SET p.first_name = ?, p.last_name = ?, cf.description = ?, cf.address1 = ?, cf.address2 = ?, cf.address3 = ?,
                          cf.address4 = ?, cf.account_title1 = ?, cf.account_title2 = ?, cf.state = ?, cf.city = ?, cf.zip = ?, 
                          cf.account_title3 = ?, p.account_type = ?, cf.production_number = ?, cf.custodian_inception = ?, 
                          cf.cf_2549 = ?, 
                          cf.master_production_number = ?, cf.omniscient_control_number = ?, cf.email_address = ?,
                          p.total_value = ?, cf.securities = ?, cf.cash = ?, cf.net_credit_debit = ?, 
                          p.margin_balance = ?, p.available_to_pay = ?, cf.stated_value_date = ?, 
                          cf.custodian_source = ?, p.accountclosed = ?
                      WHERE p.portfolioinformationid = ?";
            while($v = $adb->fetchByAssoc($result)){
                $adb->pquery($query, $v);
            }
        }
    }

    static public function GetLatestBalance($account_number){
        global $adb;
        $query = "SELECT * 
                  FROM custodian_omniscient.custodian_balances_schwab 
                  WHERE account_number = ?
                  ORDER BY as_of_date 
                  DESC LIMIT 1";
        $result = $adb->pquery($query, array($account_number));

        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, 'account_value');
        }
        return null;
    }

    /**
     * Returns the earliest date and balance for passed in account numbers
     * @param array $account_numbers
     * @return array
     */
    static public function GetEarliestBalanceAndDate(array $account_numbers){
        global $adb;
        $questions = generateQuestionMarks($account_numbers);
        $params = array();
        $params[] = $account_numbers;

        $query = "SELECT account_number, account_value, MIN(as_of_date) AS as_of_date
                  FROM custodian_omniscient.custodian_balances_schwab 
                  WHERE account_number IN ({$questions}) 
                  GROUP BY account_number";
        $result = $adb->pquery($query, $params);

        $data = array();
        if($adb->num_rows($result) > 0){
            while($r = $adb->fetchByAssoc($result)){
                $data[$r['account_number']] = array("account_value" => $r['account_value'],
                    "as_of_date" => $r['as_of_date']);
            }
        }
        return $data;
    }

    static public function BalanceBetweenDates(array $account_number, $sdate, $edate){
        global $adb;
        $questions = generateQuestionMarks($account_number);
        $params = array();
        $params[] = $account_number;
        $params[] = $sdate;
        $params[] = $edate;

        $query = "SELECT account_number, account_value AS value, as_of_date AS date
                  FROM custodian_omniscient.custodian_balances_schwab 
                  WHERE account_number IN ({$questions}) 
                  AND as_of_date BETWEEN ? AND ?
                  ORDER BY as_of_date";
        $result = $adb->pquery($query, $params);

        $data = array();
        if($adb->num_rows($result) > 0){
            while($r = $adb->fetchByAssoc($result)){
                $data[$r['account_number']][] = $r;
            }
        }
        return $data;
    }
}
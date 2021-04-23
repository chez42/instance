<?php
class cFidelityParsing extends cParsing{
    public function ParseTransactionsUnEnhanced($rep_code, $num_days){
        global $adb;

        $this->LogProcess("FIDELITY", "Starting Transaction Parsing");

        $locations = $this->file_info->GetFileLocations();

        $file_location = $locations[$rep_code]->file_location;
        $rep_id = $locations[$rep_code]->id;

#        $file_location = '/mnt/lanserver2n/TDA_FTP/GOX';//'/home/syncuser/gox';//simulated for now
#        $file_location = '/mnt/lanserver2n/Fidelity/Sowell/BuskirkGH2';//simulated for now
        $account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersFromRepCodeOpenAndClosed(array($rep_code));
        $questions = generateQuestionMarks($account_numbers);

        if($this->ConfirmDirectory($file_location)){
            $files = $this->GetFiles($file_location, "BAK", $num_days);

            foreach ($files as $file) {
                $query = "DROP TABLE IF EXISTS tmp_transactions";
                $adb->pquery($query, array(), true);

                $query = "CREATE TEMPORARY TABLE tmp_transactions LIKE custodian_omniscient.fidelity_unenhanced_transactions";
                $adb->pquery($query, array(), true);

                $query = "CREATE TEMPORARY TABLE tmp_lines (line VARCHAR(5000))";
                $adb->pquery($query, array(), true);

                $parsing_query = "LOAD DATA LOCAL INFILE ? INTO TABLE tmp_lines
                                  LINES TERMINATED BY '\\r\\n'";
                $adb->pquery($parsing_query, array($file->fullFile), true);

/*                while($x = $adb->fetchByAssoc($result)){
#                        echo $query;
#                    print_r($x);#exit;
#                        $adb->pquery($query, array($x['transaction_id'], $x['account_number'], $x['trade_date'], $x['symbol'], $x['amount'],
#                                                   $x['quantity'], $x['commission'], $x['transaction_key_mnemonic'], $x['transaction_type']));
                    echo '<br />';
                }*/

                $insert_query = "INSERT INTO tmp_transactions(account_number, transaction_type, trade_date, symbol, amount, quantity, commission, transaction_key_mnemonic, transaction_code_description)
                                 SELECT TRIM(SUBSTR(line,1,9)),
                                        TRIM(SUBSTR(line, 15, 2)),
                                        STR_TO_DATE(TRIM(SUBSTR(line, 18, 6)), '%m%d%y'),
                                        TRIM(SUBSTR(line, 28, 15)),
                                        TRIM(SUBSTR(line, 41, 12)),
                                        TRIM(SUBSTR(line, 63, 14)),
                                        TRIM(SUBSTR(line, 88, 7)),
                                        TRIM(SUBSTR(line, 96, 5)),
                                        TRIM(SUBSTR(line, 101, 31))
                                 FROM tmp_lines";
                $adb->pquery($insert_query, array(), true);

#                $query = "DELETE FROM tmp_transactions WHERE account_number NOT IN ({$questions})";
#                $adb->pquery($query, array($account_numbers));

                $update_query = "UPDATE tmp_transactions SET uid = CONCAT(?, DATE_FORMAT(trade_date, '%Y%m%d'), uid)";
                $adb->pquery($update_query, array($rep_id), true);

                $query = "SELECT * FROM tmp_transactions tmp 
                          WHERE (account_number, trade_date, symbol, transaction_key_mnemonic) 
                          NOT IN (SELECT account_number, trade_date, symbol, transaction_key_mnemonic 
                                  FROM custodian_omniscient.custodian_transactions_fidelity f 
                                  WHERE f.account_number = tmp.account_number
                                  AND f.trade_date = tmp.trade_date
                                  AND f.symbol = tmp.symbol
                                  AND f.transaction_key_mnemonic = tmp.transaction_key_mnemonic)
                          GROUP BY tmp.uid";
                $result = $adb->pquery($query, array(), true);
                if($adb->num_rows($result) > 0){
#                    $file->filename;
                    $query = "INSERT INTO custodian_omniscient.custodian_transactions_fidelity (transaction_id, account_number, trade_date, 
                                                        symbol, amount, quantity, commission, transaction_key_mnemonic, transaction_type,
                                                        transaction_key_code_description, filename)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    while($x = $adb->fetchByAssoc($result)){
                        print_r($x);echo '<br /><br />';
/*                        $adb->pquery($query, array($x['uid'], $x['account_number'], $x['trade_date'], $x['symbol'], $x['amount'],
                                                   $x['quantity'], $x['commission'], $x['transaction_key_mnemonic'], $x['transaction_type'],
                                                   $x['transaction_code_description'], $file->filename), true);
#print_r($x);
                        echo '<br />';*/
                    }
                }
            }
        }
    }

    protected function WriteUnenhancedResult($result, $filename){
        global $adb;
        if($adb->num_rows($result) > 0){
#                    $file->filename;
            $query = "INSERT INTO custodian_omniscient.custodian_transactions_fidelity (transaction_id, account_number, trade_date, 
                                                        symbol, amount, quantity, commission, transaction_key_mnemonic, transaction_type,
                                                        transaction_key_code_description, filename)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            while($x = $adb->fetchByAssoc($result)){
                $adb->pquery($query, array($x['uid'], $x['account_number'], $x['trade_date'], $x['symbol'], $x['amount'],
                                           $x['quantity'], $x['commission'], $x['transaction_key_mnemonic'], $x['transaction_type'],
                                           $x['transaction_code_description'], $filename), true);
            }
        }
    }

    public function ParseSpecificsUnenhanced($file, array $accountsToInclude){
        $result = $this->GetParsedResultUnEnhanced($file, $accountsToInclude);
        $this->WriteUnenhancedResult($result, $file);
    }

    public function GetParsedResultUnEnhanced($file, array $accountsToInclude){
        global $adb;
        $rep_id = 500;

        if(file_exists($file)) {
            $query = "DROP TABLE IF EXISTS tmp_transactions";
            $adb->pquery($query, array(), true);

            $query = "CREATE TEMPORARY TABLE tmp_transactions LIKE custodian_omniscient.fidelity_unenhanced_transactions";
            $adb->pquery($query, array(), true);

            $query = "CREATE TEMPORARY TABLE tmp_lines (line VARCHAR(5000))";
            $adb->pquery($query, array(), true);

            $parsing_query = "LOAD DATA LOCAL INFILE ? INTO TABLE tmp_lines
                                  LINES TERMINATED BY '\\r\\n'";
            $adb->pquery($parsing_query, array($file), true);

            $insert_query = "INSERT INTO tmp_transactions(account_number, transaction_type, trade_date, symbol, amount, quantity, commission, transaction_key_mnemonic, transaction_code_description)
                                 SELECT TRIM(SUBSTR(line,1,9)),
                                        TRIM(SUBSTR(line, 15, 2)),
                                        STR_TO_DATE(TRIM(SUBSTR(line, 18, 6)), '%m%d%y'),
                                        TRIM(SUBSTR(line, 28, 15)),
                                        TRIM(SUBSTR(line, 41, 12)),
                                        TRIM(SUBSTR(line, 63, 14)),
                                        TRIM(SUBSTR(line, 88, 7)),
                                        TRIM(SUBSTR(line, 96, 5)),
                                        TRIM(SUBSTR(line, 101, 31))
                                 FROM tmp_lines";
            $adb->pquery($insert_query, array(), true);

#                $query = "DELETE FROM tmp_transactions WHERE account_number NOT IN ({$questions})";
#                $adb->pquery($query, array($account_numbers));

            $update_query = "UPDATE tmp_transactions SET uid = CONCAT(?, DATE_FORMAT(trade_date, '%Y%m%d'), uid)";
            $adb->pquery($update_query, array($rep_id), true);

            if(!empty($accountsToInclude)){
                $questions = generateQuestionMarks($accountsToInclude);
                $query = "DELETE FROM tmp_transactions WHERE account_number NOT IN ({$questions})";
                $adb->pquery($query, array($accountsToInclude));
            }

            $query = "SELECT * FROM tmp_transactions tmp 
                          WHERE (account_number, trade_date, symbol, transaction_key_mnemonic) 
                          NOT IN (SELECT account_number, trade_date, symbol, transaction_key_mnemonic 
                                  FROM custodian_omniscient.custodian_transactions_fidelity f 
                                  WHERE f.account_number = tmp.account_number
                                  AND f.trade_date = tmp.trade_date
                                  AND f.symbol = tmp.symbol
                                  AND f.transaction_key_mnemonic = tmp.transaction_key_mnemonic)
                          GROUP BY tmp.uid";
            $result = $adb->pquery($query, array(), true);
            return $result;
        }
        return null;
    }

/*    public function WriteResultType($result){
        if($adb->num_rows($result) > 0){
            while($x = $adb->fetchByAssoc($result)){
                if($x[''])
                    echo '<br /><br />';
            }
        }
    }
*/
    public function GetParsedResultEnhanced($file){
        global $adb;

        if(file_exists($file)) {
            $query = "DROP TABLE IF EXISTS custodian_transactions_fidelity_temp;";
            $adb->pquery($query, array(), true);

            $query = "DROP TABLE IF EXISTS DupeFixer;";
            $adb->pquery($query, array(), true);

            $query = "CREATE TEMPORARY TABLE custodian_transactions_fidelity_temp LIKE custodian_omniscient.fidelity_enhanced_transactions";
            $adb->pquery($query, array(), true);

            $parsing_query = "LOAD DATA LOCAL INFILE ? INTO TABLE custodian_transactions_fidelity_temp
                              FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' 
                              LINES TERMINATED BY '\\r\\n'
                              (account_number, account_source, account_type_code, account_type_description, amount, broker_code, buy_sell_indicator, certificate_fee, comment, comment2, commission, core_fund_indicator, cusip, custom_short_name, div_payable_date, div_record_date, dtc_code, entry_date, exchange, exchange_code, fbsi_short_name, fee_amount, floor_symbol, fprs_txn_code, fprs_tsn_code_description, fund_load_override, fund_load_percent, fund_number, interest_amount, key_code, money_source_id, money_source, net_amount, order_action, plan_name, plan_number, postage_fee, price, primary_account_owner, principal_amount, product_name, product_type, quantity, reference_number, reg_rep1, reg_rep2, registration, sec_fee, security_description, security_group, security_id, service_fee, settlement_date, short_term_redemption_fee, source_destination, state_tax_amount, symbol, trade_date, transaction_code, transaction_code_description, transaction_key_mnemonic, transaction_key_code_description, transaction_security_type, transaction_security_type_code, trust_income, trust_principal)";
            $adb->pquery($parsing_query, array($file), true);

            $select = "SELECT * FROM custodian_transactions_fidelity_temp";
            $result = $adb->pquery($select, array());
            return $result;
        }
    }

    public function ParseTransactionsEnhanced($rep_code, $num_days){
        global $adb;

        $this->LogProcess("FIDELITY", "Starting Transaction Parsing");

        $locations = $this->file_info->GetFileLocations();

        $file_location = $locations[$rep_code]->file_location;
        $rep_id = $locations[$rep_code]->id;

#        $file_location = '/mnt/lanserver2n/TDA_FTP/GOX';//'/home/syncuser/gox';//simulated for now
#        $file_location = '/mnt/lanserver2n/Fidelity/Sowell/BuskirkGH2';//simulated for now
        $account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersFromRepCodeOpenAndClosed(array($rep_code));
        $questions = generateQuestionMarks($account_numbers);
        $file_location = '/mnt/archive/fidelity/sowell/LighthouseGH1/original';

        if($this->ConfirmDirectory($file_location)){
            $files = $this->GetFiles($file_location, "TRN", $num_days);

            foreach ($files as $file) {
                if($file->filename == '021920gh1transact-(20200415-13-39-39).trn')
                    echo 'here';
                else
                    continue;
                $query = "DROP TABLE IF EXISTS custodian_transactions_fidelity_temp;";
                $adb->pquery($query, array(), true);

                $query = "DROP TABLE IF EXISTS DupeFixer;";
                $adb->pquery($query, array(), true);

                $query = "CREATE TEMPORARY TABLE custodian_transactions_fidelity_temp LIKE custodian_omniscient.fidelity_enhanced_transactions";
                $adb->pquery($query, array(), true);

                $parsing_query = "LOAD DATA LOCAL INFILE ? INTO TABLE custodian_transactions_fidelity_temp
                                  FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' 
                                  LINES TERMINATED BY '\\r\\n'
                                  (account_number, account_source, account_type_code, account_type_description, amount, broker_code, buy_sell_indicator, certificate_fee, comment, comment2, commission, core_fund_indicator, cusip, custom_short_name, div_payable_date, div_record_date, dtc_code, entry_date, exchange, exchange_code, fbsi_short_name, fee_amount, floor_symbol, fprs_txn_code, fprs_tsn_code_description, fund_load_override, fund_load_percent, fund_number, interest_amount, key_code, money_source_id, money_source, net_amount, order_action, plan_name, plan_number, postage_fee, price, primary_account_owner, principal_amount, product_name, product_type, quantity, reference_number, reg_rep1, reg_rep2, registration, sec_fee, security_description, security_group, security_id, service_fee, settlement_date, short_term_redemption_fee, source_destination, state_tax_amount, symbol, trade_date, transaction_code, transaction_code_description, transaction_key_mnemonic, transaction_key_code_description, transaction_security_type, transaction_security_type_code, trust_income, trust_principal)";
                $adb->pquery($parsing_query, array($file->fullFile), true);

                $select = "SELECT * FROM custodian_transactions_fidelity_temp";
                $result = $adb->pquery($select, array());

                if($adb->num_rows($result) > 0){
                    $query = "INSERT INTO custodian_omniscient.custodian_transactions_fidelity (transaction_id, account_number, trade_date, 
                                                        symbol, amount, quantity, commission, transaction_key_mnemonic, transaction_type,
                                                        transaction_key_code_description, filename)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    while($x = $adb->fetchByAssoc($result)){
                        print_r($x);
/*                        $adb->pquery($query, array($x['transaction_id'], $x['account_number'], $x['trade_date'], $x['symbol'], $x['amount'],
                            $x['quantity'], $x['commission'], $x['transaction_key_mnemonic'], $x['transaction_type'],
                            $x['transaction_code_description'], $file->filename), true);
#print_r($x);*/
                        echo '<br /><br />';
                    }
                }
/*

                $insert_query = "INSERT INTO tmp_transactions(account_number, transaction_type, trade_date, symbol, amount, quantity, commission, transaction_key_mnemonic, transaction_code_description)
                                 SELECT TRIM(SUBSTR(line,1,9)),
                                        TRIM(SUBSTR(line, 15, 2)),
                                        STR_TO_DATE(TRIM(SUBSTR(line, 18, 6)), '%m%d%y'),
                                        TRIM(SUBSTR(line, 28, 15)),
                                        TRIM(SUBSTR(line, 41, 12)),
                                        TRIM(SUBSTR(line, 63, 14)),
                                        TRIM(SUBSTR(line, 88, 7)),
                                        TRIM(SUBSTR(line, 96, 5)),
                                        TRIM(SUBSTR(line, 101, 31))
                                 FROM tmp_lines";
                $adb->pquery($insert_query, array(), true);

                $update_query = "UPDATE tmp_transactions SET uid = CONCAT(?, DATE_FORMAT(trade_date, '%Y%m%d'), uid)";
                $adb->pquery($update_query, array($rep_id), true);

                $query = "SELECT * FROM tmp_transactions tmp 
                          WHERE (account_number, trade_date, symbol, transaction_key_mnemonic) 
                          NOT IN (SELECT account_number, trade_date, symbol, transaction_key_mnemonic 
                                  FROM custodian_omniscient.custodian_transactions_fidelity f 
                                  WHERE f.account_number = tmp.account_number
                                  AND f.trade_date = tmp.trade_date
                                  AND f.symbol = tmp.symbol
                                  AND f.transaction_key_mnemonic = tmp.transaction_key_mnemonic)
                          GROUP BY tmp.uid";
                $result = $adb->pquery($query, array(), true);
                if($adb->num_rows($result) > 0){
                    $file->filename;
                    $query = "INSERT INTO custodian_omniscient.custodian_transactions_fidelity (transaction_id, account_number, trade_date, 
                                                        symbol, amount, quantity, commission, transaction_key_mnemonic, transaction_type,
                                                        transaction_key_code_description, filename)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    while($x = $adb->fetchByAssoc($result)){
                        $adb->pquery($query, array($x['transaction_id'], $x['account_number'], $x['trade_date'], $x['symbol'], $x['amount'],
                            $x['quantity'], $x['commission'], $x['transaction_key_mnemonic'], $x['transaction_type'],
                            $x['transaction_code_description'], $file->filename), true);
                        echo '<br />';
                    }
                }*/
            }
        }
    }

    public function UpdateSpecificFieldUsingResult($field, $data){
        global $adb;

        if(empty($data))
            return;

        $query = "UPDATE custodian_omniscient.custodian_transactions_fidelity
                  SET {$field} = ?
                  WHERE account_number = ? 
                  AND trade_date = ? 
                  AND symbol = ? 
                  AND transaction_key_mnemonic = ? 
                  AND quantity = ?
                  AND key_code = ?";
        foreach($data AS $k => $v){
            $params = array();
            $params[] = $v[$field];
            $params[] = $v['account_number'];
            $params[] = $v['trade_date'];
            $params[] = $v['symbol'];
            $params[] = $v['transaction_key_mnemonic'];
            $params[] = $v['quantity'];
            $params[] = $v['key_code'];
#            echo $query;
#            print_r($params);
#            echo '<br /><br />';
            $adb->pquery($query, $params);
        }
    }
}
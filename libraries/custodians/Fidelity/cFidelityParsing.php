<?php
class cFidelityParsing extends cParsing{
    public function ParseTransactions($rep_code, $num_days){
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

                $select = "SELECT * FROM tmp_lines";
                $result = $adb->pquery($select, array());

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
                    $file->filename;
                    $query = "INSERT INTO custodian_omniscient.custodian_transactions_fidelity (transaction_id, account_number, trade_date, 
                                                        symbol, amount, quantity, commission, transaction_key_mnemonic, transaction_type,
                                                        transaction_key_code_description, filename)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    while($x = $adb->fetchByAssoc($result)){
                        $adb->pquery($query, array($x['transaction_id'], $x['account_number'], $x['trade_date'], $x['symbol'], $x['amount'],
                                                   $x['quantity'], $x['commission'], $x['transaction_key_mnemonic'], $x['transaction_type'],
                                                   $x['transaction_code_description'], $file->filename), true);
#print_r($x);
                        echo '<br />';
                    }
                }
            }
        }
    }
}
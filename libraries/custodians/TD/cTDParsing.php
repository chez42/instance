<?phpclass cTDParsing extends cParsing{    public function ParseTransactions($rep_code, $num_days){#        $this->MoveBackCataloguedFile(array(4,5,6));#        echo 'done';exit;        global $adb;        $locations = $this->file_info->GetFileLocations();        $file_location = $locations[$rep_code]->file_location;        $file_location = '/mnt/lanserver2n/TDA_FTP/GOX';//'/home/syncuser/gox';//simulated for now        $file_location = '/mnt/lanserver2n/TDA_FTP/GOX';//simulated for now        $this->LogProcess("TD", "Starting Transaction Parsing");        if($this->ConfirmDirectory($file_location)){            $files = $this->GetFiles($file_location, "TRN", $num_days);            $query = "DROP TABLE IF EXISTS custodian_omniscient.custodian_transactions_td_temporary";            $adb->pquery($query, array(), true);            $query = "CREATE TABLE custodian_omniscient.custodian_transactions_td_temporary LIKE custodian_omniscient.td_enhanced_transactions";            $adb->pquery($query, array(), true);            $parsing_query = "LOAD DATA LOCAL INFILE ? INTO TABLE custodian_omniscient.custodian_transactions_td_temporary                          FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'                           LINES TERMINATED BY '\\r\\n'                           (advisor_rep_code, @var_filedate, account_number, transaction_code, cancel_status_flag, symbol, security_code, @var_tradedate,                           quantity, net_amount, principal, broker_fee, other_fee, settle_date, from_to_account, account_type, accrued_interest, closing_method,                           comment, filename)                           set file_date = STR_TO_DATE(@var_filedate, ' % m /%d /%Y'),                           trade_date = STR_TO_DATE(@var_tradedate, ' % m /%d /%Y')";            $insert_query = "INSERT INTO custodian_omniscient.custodian_transactions_td (advisor_rep_code, file_date, account_number, transaction_code, cancel_status_flag, symbol, security_code, trade_date, quantity, net_amount, principal, broker_fee, other_fee, settle_date, from_to_account, account_type, accrued_interest, comment, closing_method, filename, insert_date, dupe_saver_id)                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?,                                     ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,                                     ?, ?)                             ON DUPLICATE KEY UPDATE advisor_rep_code=VALUES(advisor_rep_code)";            foreach ($files as $file) {                $adb->pquery($parsing_query, array($file->fullFile), true);                $query = "UPDATE custodian_omniscient.custodian_transactions_td_temporary SET file_date = ?, filename = ?, insert_date = NOW()";                $adb->pquery($query, array($file->createdDate, $file->filename));                $query = "UPDATE custodian_omniscient.custodian_transactions_td_temporary SET symbol = 'TDCASH' WHERE symbol = ''";                $adb->pquery($query, array());                $query = "UPDATE custodian_omniscient.custodian_transactions_td_temporary                          SET transaction_code = 'MFEERVRSL'                           WHERE transaction_code = 'DEP'                           AND (comment LIKE ('%MGMT FEE RVRSL%') OR comment LIKE ('%MGMT FEE REVERSAL%'))";                $adb->pquery($query, array());                $query = "SELECT advisor_rep_code, file_date, account_number, transaction_code, cancel_status_flag, symbol, security_code, trade_date, quantity, net_amount, principal, broker_fee, other_fee, settle_date, from_to_account, account_type, accrued_interest, comment, closing_method, filename, insert_date, 1 AS dupe_saver_id                          FROM custodian_omniscient.custodian_transactions_td_temporary";                $result = $adb->pquery($query, array());                if($adb->num_rows($result) > 0){                    $query = "SELECT COUNT(*) AS counter                              FROM custodian_omniscient.custodian_transactions_td_temporary";                    $count_result = $adb->pquery($query, array());                    $file->numLines = $adb->query_result($count_result, 0, 'counter');                    while($x = $adb->fetchByAssoc($result)){                        $adb->pquery($insert_query, array($x['advisor_rep_code'], $x['file_date'], $x['account_number'], $x['transaction_code'],                            $x['cancel_status_flag'], $x['symbol'], $x['security_code'], $x['trade_date'], $x['quantity'],                            $x['net_amount'], $x['principal'], $x['broker_fee'], $x['other_fee'], $x['settle_date'],                            $x['from_to_account'], $x['account_type'], $x['accrued_interest'], $x['comment'], $x['closing_method'],                            $x['filename'], $x['insert_date'], $x['dupe_saver_id']), true);                    }                    $this->MoveAndCatalogueFile($file);                }            }        }    }}
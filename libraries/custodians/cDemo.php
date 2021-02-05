<?php

class cDemo{
    private $mapping;

    public function __construct(){
        global $adb;
        $this->mapping = array();

        $query = "SELECT real_number, mapped_number 
                  FROM custodian_omniscient.demo_account_mapping";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $this->mapping[$v['real_number']] = $v['mapped_number'];
            }
        }
    }

    public function GetMappedAccounts(){
        return $this->mapping;
    }

    public function IsAccountMapped($account_number){
        global $adb;
        $query = "SELECT COUNT(*) AS counter FROM custodian_omniscient.demo_account_mapping WHERE real_number = ?";
        $result = $adb->pquery($query, array($account_number));
        if($adb->num_rows($result) > 0) {
            if($adb->query_result($result, 0, 'counter') > 0)
                return true;
            return false;
        }
        return false;
    }

    public function CopyPortfolios(string $custodian, array $account_number, string $replacement_repcode){
        global $adb;
        $params = array();
        $params[] = $account_number;

        $questions = generateQuestionMarks($account_number);

        $query = "SELECT * 
                  FROM custodian_omniscient.custodian_portfolios_{$custodian}
                  WHERE account_number IN ({$questions})";
        $result = $adb->pquery($query, array($account_number));

        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                if($this->IsAccountMapped($v['account_number'])) {
                    $questions = generateQuestionMarks($v);
                    $query = "INSERT INTO custodian_omniscient.custodian_portfolios_{$custodian} VALUES ({$questions}) 
                              ON DUPLICATE KEY UPDATE account_number = VALUES(account_number)";

                    $real_account = $v['account_number'];
                    $mapped_account = $this->mapping[$real_account];
                    $v['account_number'] = $mapped_account;//Replace the account number with the mapped one
                    $v['rep_code'] = $replacement_repcode;//Replace the rep code with the passed in one
                    $adb->pquery($query, array($v));
                }
            }
        }
    }

    public function CopyBalances(string $custodian, array $account_number, $sdate = null, $edate = null, $date_field = "as_of_date"){
        global $adb;
        $params = array();
        $params[] = $account_number;

        if($edate == null)
            $edate = date("Y-m-d");

        if($sdate != null) {
            $and = "AND {$date_field} BETWEEN ? AND ?";
            $params[] = $sdate;
            $params[] = $edate;
        }

        $questions = generateQuestionMarks($account_number);

        $query = "SELECT * FROM custodian_omniscient.custodian_balances_{$custodian}
                  WHERE account_number IN ({$questions})
                  {$and}";
        $result = $adb->pquery($query, $params);

        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                if($this->IsAccountMapped($v['account_number'])) {
                    $questions = generateQuestionMarks($v);
                    $query = "INSERT INTO custodian_omniscient.custodian_balances_{$custodian} VALUES ({$questions})
                              ON DUPLICATE KEY UPDATE account_number = VALUES(account_number)";

                    $real_account = $v['account_number'];
                    $mapped_account = $this->mapping[$real_account];
                    $v['account_number'] = $mapped_account;

                    $adb->pquery($query, array($v));
                }
            }
        }
    }

    public function CopyPositions(string $custodian, array $account_number, $sdate = null, $edate = null){
        global $adb;
        $params = array();
        $params[] = $account_number;

        if(!$edate)
            $edate = date("Y-m-d");

        switch(strtoupper($custodian)){
            case "TD":
            case "SCHWAB":
                $date_field = "date";
                break;
            case "FIDELITY":
                $date_field = "as_of_date";
                break;
            case "PERSHING":
                $date_field = 'position_date';
                break;
        }
        if($sdate != null) {
            $and = "AND {$date_field} BETWEEN ? AND ?";
            $params[] = $sdate;
            $params[] = $edate;
        }

        $questions = generateQuestionMarks($account_number);

        $query = "SELECT * FROM custodian_omniscient.custodian_positions_{$custodian}
                  WHERE account_number IN ({$questions})
                  {$and}";
        $result = $adb->pquery($query, $params);

        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                if($this->IsAccountMapped($v['account_number'])) {
                    $questions = generateQuestionMarks($v);
                    $query = "INSERT INTO custodian_omniscient.custodian_positions_{$custodian} VALUES ({$questions})
                              ON DUPLICATE KEY UPDATE account_number = VALUES(account_number)";

                    $real_account = $v['account_number'];
                    $mapped_account = $this->mapping[$real_account];
                    $v['account_number'] = $mapped_account;

                    $adb->pquery($query, array($v));
                }
            }
        }
    }

    public function CopyTransactions(string $custodian, array $account_number, $sdate = null, $edate = null){
        global $adb;
        $params = array();
        $params[] = $account_number;

        if(!$edate)
            $edate = date("Y-m-d");

        if($sdate != null) {
            $and = "AND trade_date BETWEEN ? AND ?";
            $params[] = $sdate;
            $params[] = $edate;
        }

        $questions = generateQuestionMarks($account_number);

        $query = "SELECT advisor_rep_code, file_date, account_number, transaction_code, cancel_status_flag, symbol, 
                         security_code, trade_date, quantity, net_amount, principal, broker_fee, other_fee, settle_date, from_to_account, 
                         account_type, accrued_interest, comment, closing_method, filename, insert_date, dupe_saver_id 
                  FROM custodian_omniscient.custodian_transactions_{$custodian}
                  WHERE account_number IN ({$questions})
                  {$and}";
        $result = $adb->pquery($query, $params);

        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                if($this->IsAccountMapped($v['account_number'])) {
                    $questions = generateQuestionMarks($v);
                    $query = "INSERT INTO custodian_omniscient.custodian_transactions_{$custodian} (advisor_rep_code, file_date, account_number, transaction_code, cancel_status_flag, symbol, security_code, trade_date, quantity, net_amount, principal, broker_fee, other_fee, settle_date, from_to_account, account_type, accrued_interest, comment, closing_method, filename, insert_date, dupe_saver_id)
                              VALUES ({$questions})
                              ON DUPLICATE KEY UPDATE account_number = VALUES(account_number)";

                    $real_account = $v['account_number'];
                    $mapped_account = $this->mapping[$real_account];
                    $v['account_number'] = $mapped_account;
                    $v['filename'] = "DEMO_".$v['filename'];
                    $adb->pquery($query, array($v));
                }
            }
        }
    }

    public function GetFakeName() {
        global $adb;
        $query = "SELECT first_name, last_name, company_name, address, city, county, state, zip, email, web
                  FROM custodian_omniscient.us_fake_address 
                  ORDER BY RAND() 
                  LIMIT 1";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0) {
            while ($v = $adb->fetchByAssoc($result)) {
                return $v;
            }
        }
        return null;
    }

    public function MapFakeNameToCustodian(string $custodian, array $fake){
        switch($custodian){

        }
    }
    /*Fake Data Example
    Array (
    [first_name] => Chauncey
    [last_name] => Motley
    [company_name] => Affiliated With Travelodge
    [address] => 63 E Aurora Dr
    [city] => Orlando
    [county] => Orange
    [state] => FL
    [zip] => 32804
    [email] => chauncey_motley@aol.com
    [web] => http://www.affiliatedwithtravelodge.com )
     */

    public function UpdateTDWithFakeData($data){
        global $adb;
        $fake = $this->GetFakeName();

        $query = "UPDATE custodian_omniscient.custodian_portfolios_td 
                  SET first_name = ?, last_name = ?, company_name = ?, street = ?, 
                      city = ?, state = ?, zip = ?, rep_code = ?, advisor_id = ?
                  WHERE account_number = ?";

        $adb->pquery($query, array($fake['first_name'], $fake['last_name'], $fake['company_name'], $fake['address'],
            $fake['city'], $fake['state'], $fake['zip'], $data['replace'], $data['replace'], $data['account_number']));

    }

    public function UpdateCustodianWithFakeName(string $custodian, $rep_code_to_replace = 'DEMO_UNEDITED', $new_rep_code = "DEMO"){
        global $adb;

        $query = "SELECT * 
                  FROM custodian_omniscient.custodian_portfolios_{$custodian}
                  WHERE rep_code = ?";
        $result = $adb->pquery($query, array($rep_code_to_replace));

        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $v['replace'] = $new_rep_code;
                switch($custodian){
                    case "TD":
                        $this->UpdateTDWithFakeData($v);
                        break;
                }
            }
        }
    }
}
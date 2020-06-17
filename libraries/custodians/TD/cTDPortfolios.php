<?php
require_once("libraries/custodians/cCustodian.php");

class cTDPortfolioData{
    public $account_number, $custodian, $first_name, $last_name, $account_type,
           $account_value, $money_market;//PortfolioInformation
    public $as_of_date, $street, $address2, $address3, $address4, $address5, $address6, $city, $state, $zip, $rep_code,
           $master_rep_code, $omni_code;//PortfolioInformationCF

    public function __construct($data){
        $this->rep_code = $data['personal']['rep_code'];
        $this->custodian = 'TD';
        $this->account_value = $data['balance']['account_value'];
        $this->money_market = $data['balance']['money_market'];
        $this->as_of_date = $data['balance']['as_of_date'];
        $this->account_number = $data['personal']['account_number'];
        $this->last_name = $data['personal']['last_name'];
        $this->first_name = $data['personal']['first_name'];
        $this->account_type = $data['personal']['account_type'];
        $this->street = $data['personal']['street'];
        $this->address2 = $data['personal']['address2'];
        $this->address3 = $data['personal']['address3'];
        $this->address4 = $data['personal']['address4'];
        $this->address5 = $data['personal']['address5'];
        $this->address6 = $data['personal']['address6'];
        $this->city = $data['personal']['city'];
        $this->state = $data['personal']['state'];
        $this->zip = $data['personal']['zip'];
        $this->omni_code = $data['personal']['omni_code'];
    }
}

/**
 * Class cTDPortfolios
 * This class allows the pulling of data from the custodian database
 */
class cTDPortfolios extends cCustodian {
    use tPortfolios;
    private $portfolio_data;//Holds both personal and balance information

    /**
     * cTDPortfolios constructor.
     * @param string $custodian_name
     * @param string $database
     * @param string $module
     * @param string $portfolio_table
     * @param string $table (REFERS TO BALANCE TABLE)
     */
    public function __construct(string $custodian_name, string $database, string $module,
                                string $portfolio_table, string $balance_table, array $rep_codes, $columns=array('*')){
        $this->name = $custodian_name;
        $this->database = $database;
        $this->module = $module;
        $this->portfolio_table = $portfolio_table;
        $this->table = $balance_table;
        $this->columns = $columns;
        if(!empty($rep_codes)) {
            $this->SetRepCodes($rep_codes);
            $this->GetPortfolioPersonalData();
            $this->GetPortfolioBalanceData();
            $this->SetupPortfolioComparisons();
        }
    }

    protected function GetPortfolioPersonalData(){
        global $adb;
        $params = array();
        $questions = generateQuestionMarks($this->account_numbers);
        $params[] = $this->account_numbers;

        if(empty($this->columns))
            $fields = "*";
        else{
            $fields = implode ( ", ", $this->columns );
        }

        $query = "SELECT {$fields} FROM {$this->database}.{$this->portfolio_table} WHERE account_number IN ({$questions}) AND account_number != ''";
        $result = $adb->pquery($query, $params, true);

        if($adb->num_rows($result) > 0){
            while($r = $adb->fetchByAssoc($result)){
                $this->portfolio_data[$r['account_number']]['personal'] = $r;
            }
        }
        return $this->portfolio_data;
    }

    protected function GetPortfolioBalanceData($date=null){
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
                  WHERE account_number IN ({$questions}) AND as_of_date = ? AND account_number != ''";
        $result = $adb->pquery($query, $params, true);

        if($adb->num_rows($result) > 0){
            while($r = $adb->fetchByAssoc($result)){
                $this->portfolio_data[$r['account_number']]['balance'] = $r;
            }
        }
        return $this->portfolio_data;
    }

    /**
     * Returns both the personal and balance data
     * @return mixed
     */
    public function GetPortfolioData(){
        return $this->portfolio_data;
    }

    /**
     * Create the new entity in the crmentity table
     * @param $crmid
     * @param $owner
     * @param cTDPortfolioData $data
     */
    protected function FillEntityTable($crmid, $owner, cTDPortfolioData $data){
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
     * @param cTDPortfolioData $data
     */
    protected function FillPortfolioTable($crmid, cTDPortfolioData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;
        $params[] = $data->account_number;
        $params[] = 'TD';
        $params[] = $data->account_type;
        $params[] = $data->first_name;
        $params[] = $data->last_name;
        $params[] = $data->account_value;
        $params[] = $data->money_market;

        $questions = generateQuestionMarks($params);
        $query = "INSERT INTO vtiger_portfolioinformation (portfolioinformationid, account_number, origination, account_type, first_name, last_name, total_value, money_market_funds)
                  VALUES ({$questions})";
        $adb->pquery($query, $params, true);
    }

    /**
     * Creates data in the vtiger_portfolioinformationcf table
     * @param $crmid
     * @param cTDPortfolioData $data
     */
    protected function FillPortfolioCFTable($crmid, cTDPortfolioData $data){
        global $adb;
        $params = array();
        $params[] = $crmid;
        $params[] = $data->rep_code;
        $params[] = $data->street;
        $params[] = $data->address2;
        $params[] = $data->address3;
        $params[] = $data->address4;
        $params[] = $data->address5;
        $params[] = $data->address6;
        $params[] = $data->city;
        $params[] = $data->state;
        $params[] = $data->zip;
        $params[] = 1;
        $params[] = $data->as_of_date;

        $questions = generateQuestionMarks($params);
        $query = "INSERT INTO vtiger_portfolioinformationcf (portfolioinformationid, production_number, address1, address2, address3, address4, address5, address6, city, state, zip, system_generated, stated_value_date)
                  VALUES ({$questions})";
        $adb->pquery($query, $params, true);
    }

    protected function UpdatePortfolios(cTDPortfolioData $data){
        global $adb;
        $params[] = 'TD';
        $params[] = $data->account_type;
        $params[] = $data->first_name;
        $params[] = $data->last_name;
        $params[] = $data->account_value;
        $params[] = $data->money_market;
        $params[] = $data->rep_code;
        $params[] = $data->street;
        $params[] = $data->address2;
        $params[] = $data->address3;
        $params[] = $data->address4;
        $params[] = $data->address5;
        $params[] = $data->address6;
        $params[] = $data->city;
        $params[] = $data->state;
        $params[] = $data->zip;
        $params[] = $data->as_of_date;
        $params[] = $data->account_number;

        $query = "UPDATE vtiger_portfolioinformation p 
                  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
                  SET origination=?, account_type=?, first_name=?, last_name=?, total_value=?, money_market_funds=?,
                      production_number=?, address1=?, address2=?, address3=?, address4=?, address5=?, address6=?, city=?, 
                      state=?, zip=?, stated_value_date=? WHERE account_number = ?";
        $adb->pquery($query, $params, true);
    }

    /**
     * Using the cTDPortfolioData class, create the portfolios.  Used with a pre-filled in cTDPortfolioData class (done manually)
     * @param cTDPortfolioData $data
     * @throws Exception
     */
    public function CreateNewPortfolioUsingcTDPortfolioData(cTDPortfolioData $data){
        if(!$this->DoesAccountNumberExistInCRM($data->account_number)) {//If the account number doesn't exist yet, create it
            $crmid = $this->UpdateEntitySequence();
            $owner = $this->repcode_mapping[$data->rep_code];

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
                    $tmp = new cTDPortfolioData($data);
                    $this->CreateNewPortfolioUsingcTDPortfolioData($tmp);
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
                    $tmp = new cTDPortfolioData($data);
                    $this->UpdatePortfolios($tmp);
                }
            }
        }
    }
}
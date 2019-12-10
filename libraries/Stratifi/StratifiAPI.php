<?php

include_once("libraries/Stratifi/StratHouseholds.php");
include_once("libraries/Stratifi/StratContacts.php");
include_once("libraries/Stratifi/StratAdvisors.php");

#curl "https://backend-staging.stratifi.com/api/v1/prism/scores/risks/current/" -H "Authorization: Bearer YVHgHENh8ZFXXFfNraSLVR7U7b9YS3" -H "Content-Type: application/json" -d '{ "positions": [ { "ticker": "CASH", "value": 4.3 }, { "ticker": "ISFCX", "value": 42.22 }, { "ticker": "LCEVX", "value": 46.83 }, { "ticker": "AHHYX", "value": 6.65 } ] }'

class StratifiAPI {
    #private $url  = "https://robo-pm-production.stratifi.com/api/v3/prism/risk_analysis";
    ##protected $url = "https://backend-staging.stratifi.com";
    protected $url = "https://backend-production.stratifi.com";
    protected $options = array();
    protected $apiToken;
    protected $header;

    public function __construct($apiToken = "Bearer YVHgHENh8ZFXXFfNraSLVR7U7b9YS3") {
        $this->apiToken = $apiToken;
        $this->header = array();
        $this->header[] = 'Content-Type: application/json';
        $this->header[] = 'Authorization: ' . $this->apiToken;
    }

    public function getAnalysis($data){
//        $options = $this->options;
//        $options['json'] = $data;
        return $this->execQuery($data);
    }

    public function getCurrentRiskScore($symbol_info){
        $extension = "/api/v1/prism/scores/risks/current/";
        $url = $this->url . $extension;
        $body = $symbol_info;
//        $body = '{ "positions": [ { "ticker": "CASH", "value": 4.3 }, { "ticker": "ISFCX", "value": 42.22 }, { "ticker": "LCEVX", "value": 46.83 }, { "ticker": "AHHYX", "value": 6.65 } ] }';
        $result = $this->execQuery($url, $this->header, $body);
        return $result;
    }

    public function getAccountPrismScore($id){
        $extension = "/api/v1/accounts/{$id}/prism_score/";
        $url = $this->url . $extension;
        $result = $this->execQuery($url, $this->header, null, false);
        return $result;
    }


    public function ConvertPositionsForAssessment($positions){
        $weights = array();
        foreach($positions AS $k => $v){
            $tmp = array();
            $tmp['ticker'] = $v['security_symbol'];
            $tmp['value'] = $v['current_value'];
            $tmp['ticker_name'] = $v['description'];
            if(strlen($tmp['ticker_name']) < 1)
                $tmp['ticker_name'] = $tmp['ticker'];
            $weights[] = $tmp;
        }
        return $weights;
    }

    public function CreateNewStratifiAccount($name){
        $extension = "/api/v1/accounts/";
        $url = $this->url . $extension;
        $body = json_encode(array("name" => $name));
        $result = $this->execQuery($url, $this->header, $body);
        return $result;
    }

    public function getAllAccounts($next = null){
        if($next != null){
            $next = substr($next, strpos($next, "/accounts") + 9);
        }

        $extension = "/api/v1/accounts{$next}";
        $url = $this->url . $extension;
        $result = $this->execQuery($url, $this->header, null, false);
        return $result;
    }

    public function getAccountByID($id){
        $extension = "/api/v1/accounts/{$id}";
        $url = $this->url . $extension;
        $result = $this->execQuery($url, $this->header, null, false);
        return $result;
    }

    public function getAccountByName($name){
        $extension = "/api/v1/accounts/{$name}";
        $url = $this->url . $extension;
        $result = $this->execQuery($url, $this->header, null, false);
        echo $result;
    }

    /**
     * Get the Stratifi Account ID that was assigned from stratifi for this Portfolio
     * @param $account_number
     * @return int|null|string|string[]
     * @throws Exception
     */
    public function GetStratifiAccountID($account_number){
        global $adb;
        $query = "SELECT stratid FROM vtiger_portfolioinformation p 
                  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid) 
                  WHERE account_number = ?";
        $result = $adb->pquery($query, array($account_number));
        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, 'stratid');
        }
        return 0;
    }

    /**
     * Returns the Stratifi Contact ID, Advisor ID, Household ID, and account ID for the passed in account number
     * @param $account_number
     */
    public function GetStratifiLinkingInformation($account_number){
        global $adb;
        $query = "SELECT cf.stratid AS account_stratid, ccf.stratid AS contact_stratid, acf.stratid AS household_stratid, u.stratid AS advisor_stratid, e.smownerid AS omni_advisor_id, p.account_number AS account_number, p.portfolioinformationid AS portfolioinformationid, cf.stratifi_name
                  FROM vtiger_portfolioinformation p 
                  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
                  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
                  JOIN vtiger_users u ON u.id = e.smownerid
                  LEFT JOIN vtiger_contactdetails cd ON p.contact_link = cd.contactid
                  LEFT JOIN vtiger_contactscf ccf ON ccf.contactid = cd.contactid
                  LEFT JOIN vtiger_account a ON a.accountid = p.household_account 
                  LEFT JOIN vtiger_accountscf acf ON a.accountid = acf.accountid 
                  WHERE account_number = ?";
        $result = $adb->pquery($query, array($account_number));
        if($adb->num_rows($result) > 0){
            $row['account_stratid'] = $adb->query_result($result, 0, 'account_stratid');
            $row['contact_stratid'] = $adb->query_result($result, 0, 'contact_stratid');
            $row['household_stratid'] = $adb->query_result($result, 0, 'household_stratid');
            $row['advisor_stratid'] = $adb->query_result($result, 0, 'advisor_stratid');
            $row['omni_advisor_id'] = $adb->query_result($result, 0, 'omni_advisor_id');
            $row['account_number'] = $adb->query_result($result, 0, 'account_number');
            $row['portfolioinformationid'] = $adb->query_result($result, 0, 'portfolioinformationid');
            return $row;
        }
        return 0;
    }

    public function UpdateStratifiAccountLinking($account_number){
        $data = $this->GetStratifiLinkingInformation($account_number);
        $extension = "/api/v1/accounts/" . $data['account_stratid'] . "/";
        $url = $this->url . $extension;
        $params = array();
        $params['name'] = "POR" . $data['portfolioinformationid'];//$data['stratifi_name'];
        if(strlen($data['contact_stratid']) > 0)
            $params['investor'] = $data['contact_stratid'];
        if(strlen($data['advisor_stratid']) > 0)
            $params['advisor'] = $data['advisor_stratid'];
        if(strlen($data['account_number']) > 0)
            $params['number'] = $data['account_number'];

        $body = json_encode($params);
        $result = $this->execPatch($url, $this->header, $body);
        /*        echo "<strong>TRYING FOR: {$url}</strong>" . ' -- {$account_number} -- <br />';
                print_r($data);
                echo "<br />";
                echo $result . '<br /><br />';*/
        return $result;
    }

    public function UpdateStratifiInvestorLinking($account_number){
        $data = $this->GetStratifiLinkingInformation($account_number);
        $extension = "/api/v1/investors/" . $data['contact_stratid'] . "/";
        $url = $this->url . $extension;
        print_r($data);
        echo '<br />' . $url;
        echo '<br /><br />';
        $params = array();
        if($data['advisor_stratid'])
            $params['advisor'] = $data['advisor_stratid'];
        if($data['household_stratid'])
            $params['household'] = $data['household_stratid'];

#        echo $data['contact_stratid'] . ' -- ';
        $body = json_encode($params);
        $result = $this->execPatch($url, $this->header, $body);
        return $result;
    }

    /**
     * Return a list of account that have a stratifi ID
     */
    public function GetAccountsThatHaveStratifiID(){
        global $adb;
        $query = "SELECT account_number FROM vtiger_portfolioinformation p 
                  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid) 
                  WHERE cf.stratid IS NOT NULL AND cf.stratid != ''";
        $result = $adb->pquery($query, array());
        $account_numbers = array();
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $account_numbers[] = $v['account_number'];
            }
            return $account_numbers;
        }
        return $account_numbers;
    }

    public function SendPositionsToStratifi($data){
        $extension = "/api/v1/accounts/" . $data['stratid'] . "/";
        $url = $this->url . $extension;

        $converted_positions = $this->ConvertPositionsForAssessment($data['symbol_data']);
        $body = json_encode(array("name" => $data['stratname'], "positions" => $converted_positions));
        $result = $this->execPut($url, $this->header, $body);
        return $result;
    }

    public function UpdatePositionsToStratifi($data){
        $extension = "/api/v1/accounts/" . $data['stratid'] . "/";
        $url = $this->url . $extension;

        echo "<br /><br />Trying: " . $extension . "<br />";
        $converted_positions = $this->ConvertPositionsForAssessment($data['symbol_data']);
        $body = json_encode(array("name" => $data['stratname'], "positions" => $converted_positions));
        echo "Body: ";
        print_r($body);
        $result = $this->execPatch($url, $this->header, $body);
        return $result;
    }

    protected function execQuery($url, $header, $body, $post=true) {
        $session = curl_init();
        curl_setopt($session, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($session, CURLOPT_HTTPHEADER, $header);
        if($post == true){
            curl_setopt($session, CURLOPT_POST, true);
            curl_setopt($session, CURLOPT_POSTFIELDS, $body);
        }
        curl_setopt($session, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($session, CURLOPT_URL, $url);

        return curl_exec($session);
    }

    protected function execPut($url, $header, $body) {
        $session = curl_init();
        curl_setopt($session, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($session, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($session, CURLOPT_POSTFIELDS,$body);
        curl_setopt($session, CURLOPT_HTTPHEADER, $header);
        curl_setopt($session, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($session, CURLOPT_URL, $url);
        curl_setopt($session, CURLOPT_TIMEOUT, 1);

        return curl_exec($session);
    }

    protected function execPatch($url, $header, $body) {
        $session = curl_init();
        curl_setopt($session, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($session, CURLOPT_CUSTOMREQUEST, "PATCH");
        curl_setopt($session, CURLOPT_POSTFIELDS,$body);
        curl_setopt($session, CURLOPT_HTTPHEADER, $header);
        curl_setopt($session, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($session, CURLOPT_URL, $url);
        curl_setopt($session, CURLOPT_TIMEOUT, 1);

        return curl_exec($session);
    }

    protected function getURL(){
        return $this->url;
    }
}


/*
 { 		"name": "testing account post 1", 		"positions": [ 		{ 			"value": 1000000, 			"ticker": "CUR:US", 			"ticker_name": "U S Dollar" 		}, 		{ 			"value": 1000, 			"ticker": "SPY", 			"ticker_name": "SPDR S&P 500" 		}, 		{ 			"value": 100000000000000, 			"ticker": "IVV", 			"ticker_name": "iShares Core S&P 500" 		} 		] 	}
 */
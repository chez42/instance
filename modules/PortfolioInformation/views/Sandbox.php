<?php

require_once("vendor/autoload.php");

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Psr7\Request;

DEFINE("URI_PRICING","https://eodhistoricaldata.com/api/eod");
DEFINE("URI_REALTIME","https://eodhistoricaldata.com/api/real-time");

class cEodGuzzle{
    public $api_token, $uri_symbol;
    private $guz;
    public function __construct($exchange = "US", $apiToken = "59838effd9cac"){
        $this->api_token = $apiToken;
        $this->exchange = $exchange;
        $this->uri_symbol =

        $this->guz = new Guzzle();
    }

    public function getSymbolPricing($symbol, $start, $end, $exchange = 'US'){
        $options['from'] = $start;
        $options['to'] = $end;
        $options['period'] = "d";
        $options['api_token'] = $this->api_token;
        $options['fmt'] = 'json';

        $headers = ['test' => 'testing'];

        $res = $this->guz->get(URI_PRICING . "/{$symbol}.{$exchange}", ['query' => $options]);
        echo $res->getBody()->getContents();
#        $request = new Request("GET", $this->uri_symbol . "/{$symbol}.{$exchange}");

//        $res = $this->guz->Request("GET", $this->uri_symbol . "/{$symbol}.{$exchange}");//?api_token={$this->api_token}")->getBody()->getContents();
#        echo $request->getUri();
#        $request->
#        $query = $this->guz->getQuery();
#        echo $query;exit;
/*
        $options = $this->options;
        $options['from'] = $start;
        $options['to'] = $end;
        $options['period'] = "d";
        $this->eodUrl = "https://eodhistoricaldata.com/api/eod/{$symbol}." . $this->exchange;
        return $this->execQuery($options);*/
    }

    public function getSymbolRealTimePricing($symbol, $exchange = 'US'){
        $options['api_token'] = $this->api_token;
        $options['fmt'] = 'json';

        $res = $this->guz->get(URI_REALTIME . "/{$symbol}.{$exchange}", ['query' => $options]);
        echo $res->getBody()->getContents();
    }
}



class PortfolioInformation_Sandbox_View extends Vtiger_Index_View{
    public function process(Vtiger_Request $request) {echo 'done';exit;
        $guz = new cEodGuzzle();

        $guz->getSymbolRealTimePricing("AAPL");
#        $symbol = 'aapl';
#        $exchange = 'US';
/*
        $res = $guz->request('GET', 'https://api.github.com/user', [
            'auth' => ['user', 'pass']
        ]);
        echo $res->getStatusCode();
/*
        $res = $guz->request("POST", $guz->uri_symbol . "/{$symbol}/.{$exchange}", ['debug' => true],
                                                                                                ['auth' => ['api_token' => $guz->api_token],
                                                                                                           ['fmt' => 'json']
                                                                                                ])->getBody()->getContents();
        print_r($res);exit;*/

        echo 'sandbox guzzle end';exit;
        PortfolioInformation_TotalBalances_Model::WriteAndUpdateLast7DaysForAllUsers();
        echo 'Done last 7 days for everybody else';exit;
        PortfolioInformation_TotalBalances_Model::WriteAndUpdateAllForUser(1186);
        echo 'ALL DONE<br /><br/><br/>DONE!!';exit;
        $ids = GetAllActiveUserIDs();
        print_r($ids);exit;
        global $adb;
        $query = "SELECT id FROM vtiger_users WHERE id NOT IN (SELECT user_id FROM daily_user_total_balances) ORDER BY id ASC";
        $result = $adb->pquery($query, array());
        $count = 0;
        if($adb->num_rows($result) > 0){
            while($x = $adb->fetchByAssoc($result)){
                $user_id = $x['id'];
                $account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersForSpecificUser($user_id, false);

            $questions = generateQuestionMarks($account_numbers);
            $q = "INSERT INTO daily_user_total_balances
                  SELECT {$user_id}, SUM(account_value) AS total_value, COUNT(account_number) AS num_accounts, as_of_date
                  FROM consolidated_balances
                  WHERE account_number IN({$questions})
                  GROUP BY as_of_date";
            $adb->pquery($q, array($account_numbers));
#                echo "USER: " . $user_id . '<br />';
#                print_r($account_numbers);
#                echo '<br /><br />';
                $count++;
                if($count > 15){
                    echo "<br /><br /><br /><br />FINISHED 15";
                    return;
                }
                /*
                INSERT INTO daily_user_total_balances
                SELECT 1, SUM(account_value) AS total_value, COUNT(account_number) AS num_accounts, as_of_date
                FROM consolidated_balances
                #WHERE account_number IN()
                GROUP BY as_of_date;
                 */
            }
        }
    }
}

?>
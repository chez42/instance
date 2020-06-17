<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2019-02-21
 * Time: 4:22 PM
 */

require_once("vendor/autoload.php");

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Psr7\Request;

DEFINE("URI_PRICING","https://eodhistoricaldata.com/api/eod");
DEFINE("URI_REALTIME","https://eodhistoricaldata.com/api/real-time");
DEFINE("URI_BONDS","https://eodhistoricaldata.com/api/bond-fundamentals");
DEFINE("URI_FUNDAMENTALS","https://eodhistoricaldata.com/api/fundamentals");
DEFINE("URI_DIVIDENDS", "https://eodhistoricaldata.com/api/div");
DEFINE("URI_OPTIONS","https://eodhistoricaldata.com/api/options");
DEFINE("URI_LOGOS","https://eodhistoricaldata.com");

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
        return $res->getBody()->getContents();
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
        return $res->getBody()->getContents();
    }

    public function getFundamentals($symbol, $exchange = 'US'){
        $options['api_token'] = $this->api_token;
        $options['fmt'] = 'json';

        $res = $this->guz->get(URI_FUNDAMENTALS . "/{$symbol}.{$exchange}", ['query' => $options]);
        return $res->getBody()->getContents();
    }

    public function getBonds($symbol){
        $options['api_token'] = $this->api_token;
        $options['fmt'] = 'json';
        $res = $this->guz->get(URI_BONDS . "/{$symbol}", ['query' => $options]);
        return $res->getBody()->getContents();
    }

    public function getOptions($symbol, $exchange = 'US'){
        $options['api_token'] = $this->api_token;
        $options['fmt'] = 'json';
        $res = $this->guz->get(URI_OPTIONS . "/{$symbol}.{$exchange}", ['query' => $options]);
        return $res->getBody()->getContents();
    }

    public function getDividends($symbol, $exchange = 'US', $from, $to){
        $options['api_token'] = $this->api_token;
        $options['fmt'] = 'json';
        $options['from'] = $from;
        $options['to'] = $to;
        $res = $this->guz->get(URI_DIVIDENDS . "/{$symbol}.{$exchange}", ['query' => $options]);
        return $res->getBody()->getContents();
    }


}

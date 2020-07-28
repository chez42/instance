<?php

class FundData{
    public $nav, $morning_star_rating, $morning_star_risk_rating, $inception_date, $yield, $yield_ytd;
}

class FundAllocation{
    public $net_percent, $long_percent, $type, $short_percent, $category_average, $benchmark;
}

class MutualFund{
    public $name, $symbol, $price, $as_of;
    public $code, $type, $exchange, $isin, $cusip, $fund_summary, $fund_family, $fund_category, $fund_style, $marketcap;
    public $fund_allocation, $fund_data;

    public function __construct(){
        $this->fund_allocation = array();
        $this->fund_data = array();
    }
}
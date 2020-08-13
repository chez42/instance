<?php

class Asset_Allocation{
    public $long, $short, $net_assets;

    public function __construct($data){
        $long = "Long_%";
        $short = "Short_%";
        $net = "Net_Assets_%";

        $this->long = $data->$long;
        $this->short = $data->$short;
        $this->net_assets = $data->$net;
    }
}

class MorningStar{
    public $Ration, $Category_Benchmark, $Sustainability_Ratio;
    public function __construct($data){
        $this->Ration = $data->Ratio;
        $this->Category_Benchmark = $data->Category_Benchmark;
        $this->Sustainability_Ratio = $data->Sustainability_Ratio;
    }
}

class World_Regions{
    public $equity, $relative_to_category;

    public function __construct($data){
        $equity = "Equity_%";
        $this->equity = $data->$equity;
        $this->relative_to_category = $data->Relative_to_Category;
    }
}

class Sector_Weights{
    public $equity, $relative_to_category;
    public function __construct($data){
        $equity = "Equity_%";
        $this->equity = $data->$equity;
        $this->relative_to_category = $data->Relative_to_Category;
    }
}

class ETF_Data{
    public $ISIN, $Company_Name, $Company_URL, $ETF_URL, $Yield, $Dividend_Paying_Frequency, $Inception_Date, $Max_Annual_Mgmt_Charge,
           $Ongoing_Charge, $Date_Ongoing_Charge, $NetExpenseRatio, $AnnualHoldingsTurnover, $TotalAssets, $Average_Mkt_Cap_Mil;

    public $asset_allocation, $world_regions, $sector_weights, $morning_star;

    public function __construct($data){
        $this->ISIN = $data->ISIN;
        $this->Company_Name = $data->Company_Name;
        $this->Company_URL = $data->Company_URL;
        $this->ETF_URL = $data->ETF_URL;
        $this->Yield = $data->Yield;
        $this->Dividend_Paying_Frequency = $data->Dividend_Paying_Frequency;
        $this->Inception_Date = $data->Inception_Date;
        $this->Max_Annual_Mgmt_Charge = $data->Max_Annual_Mgmt_Charge;
        $this->Ongoing_Charge = $data->Ongoing_Charge;
        $this->Date_Ongoing_Charge = $data->Date_Ongoing_Charge;
        $this->NetExpenseRatio = $data->NetExpenseRatio;
        $this->AnnualHoldingsTurnover = $data->AnnualHoldingsTurnover;
        $this->TotalAssets = $data->TotalAssets;
        $this->Average_Mkt_Cap_Mil = $data->Average_Mkt_Cap_Mil;

        foreach($data->Asset_Allocation AS $k => $v){
            $type = $k;
            $tmp = new Asset_Allocation($v);
            $this->asset_allocation[$type] = $tmp;
        }

        foreach($data->World_Regions AS $k => $v){
            $type = $k;
            $tmp = new World_Regions($v);
            $this->world_regions[$type] = $tmp;
        }

        foreach($data->Sector_Weights AS $k => $v){
            $type = $k;
            $tmp = new Sector_Weights($v);
            $this->sector_weights[$type] = $tmp;
        }

        $this->morning_star = new MorningStar($data->MorningStar);
    }
}

class TypeETF{
//General
    public $Code, $Type, $Name, $Exchange, $CurrencyCode, $CurrencyName, $CurrencySymbol, $CountryName,
           $CountryISO, $Description, $Category, $UpdatedAt;

    public $ETFData;

    public $asset_class, $stock, $bond, $cash, $unclass, $other;
    public $dividends, $frequency;

    public function __construct($etf_data, $dividend_data = null){
        $this->Code = $etf_data->General->Code;
        $this->Type = $etf_data->General->Type;
        $this->Name = $etf_data->General->Name;
        $this->Exchange = $etf_data->General->Exchange;
        $this->CurrencyCode = $etf_data->General->CurrencyCode;
        $this->CurrencyName = $etf_data->General->CurrencyName;
        $this->CurrencySymbol = $etf_data->General->CurrencySymbol;
        $this->CountryName = $etf_data->General->CountryName;
        $this->CountryISO = $etf_data->General->CountryISO;
        $this->Description = $etf_data->General->Description;
        $this->Category = $etf_data->General->Category;
        $this->UpdatedAt = $etf_data->General->UpdatedAt;

        $this->ETFData = new ETF_Data($etf_data->ETF_Data);

        $this->stock = $this->ETFData->asset_allocation["Stock non-US"]->net_assets + $this->ETFData->asset_allocation["Stock US"]->net_assets;
        $this->bond = $this->ETFData->asset_allocation["Bond"]->net_assets;
        $this->cash = $this->ETFData->asset_allocation["Cash"]->net_assets;
        $this->unclass = $this->ETFData->asset_allocation["NotClassified"]->net_assets;
        $this->other = $this->ETFData->asset_allocation["Other"]->net_assets;
        //Take the asset allocations and put them in an array, then determine which the biggest is to define the asset class
        $combined = array("Stocks" => $this->stock,
                          "Bonds" => $this->bond,
                          "Cash" => $this->cash,
                          "Other" => $this->other,
                          "Unclassified" => $this->unclass);
        $max = array_keys($combined, max($combined));
        $this->asset_class = $max[0];

        $this->dividends = $dividend_data;
        $this->frequency = Dividend::DetermineFrequency($dividend_data);
    }

    public function UpdateIntoOmni(){
        global $adb;
        $query = "UPDATE vtiger_modsecurities m 
                  JOIN vtiger_modsecuritiescf cf USING (modsecuritiesid)
                  JOIN vtiger_crmentity e ON e.crmid = m.modsecuritiesid               
                  SET securitytype = ?, security_name = ?, stock_exchange = ?, country = ?, summary = ?, Morning_Star_Category = ?, isin = ?, 
                      dividend_yield = ?, pay_frequency = ?, us_stock = ?, intl_stock = ?, us_bond = ?, unclassified_net = ?, cash_net = ?, other_net = ?, 
                      us_equity = ?, canada_equity = ?, Latin_America_equity = ?, UK_equity = ?, Europe_ex_euro_equity = ?, 
                      Europe_Emerging_equity = ?, Africa_equity = ?, Middle_East_equity = ?, Japan_equity = ?, Australasia_equity = ?, 
                      Asia_Developed_equity = ?, Asia_Emerging_equity = ?, Basic_Materials_Weight = ?, Consumer_Cyclical_Weight = ?, 
                      Financial_Services_Weight = ?, Real_Estate_Weight = ?, Consumer_Defensive_Weight = ?, Healthcare_Weight = ?, 
                      Utilities_Weight = ?, Energy_Weight = ?, Industrials_Weight = ?, Communication_Services_Weight = ?, technology_weight = ?,
                      aclass = ?, pay_frequency = ?, last_eod = NOW()
                      WHERE security_symbol = ?";

        $params = array();
        $params[] = $this->Type;
        $params[] = $this->Name;
        $params[] = $this->Exchange;
        $params[] = $this->CountryName;
        $params[] = $this->Description;
        $params[] = $this->ETFData->morning_star->Category_Benchmark;

        $params[] = $this->ETFData->ISIN;
        $params[] = $this->ETFData->Yield;
        $params[] = $this->ETFData->Dividend_Paying_Frequency;
        $params[] = $this->ETFData->asset_allocation["Stock US"]->net_assets;
        $params[] = $this->ETFData->asset_allocation["Stock non-US"]->net_assets;
        $params[] = $this->ETFData->asset_allocation["Bond"]->net_assets;
        $params[] = $this->ETFData->asset_allocation["NotClassified"]->net_assets;
        $params[] = $this->ETFData->asset_allocation["Cash"]->net_assets;
        $params[] = $this->ETFData->asset_allocation["Other"]->net_assets;

        $params[] = $this->ETFData->world_regions['North America']->equity;
        $params[] = $this->ETFData->world_regions['North America']->equity;
        $params[] = $this->ETFData->world_regions['Latin America']->equity;
        $params[] = $this->ETFData->world_regions['United Kingdom']->equity;
        $params[] = $this->ETFData->world_regions['Europe Developed']->equity;
        $params[] = $this->ETFData->world_regions['Europe Emerging']->equity;
        $params[] = $this->ETFData->world_regions['Africa\/Middle East']->equity;
        $params[] = $this->ETFData->world_regions['Africa\/Middle East']->equity;
        $params[] = $this->ETFData->world_regions['Japan']->equity;
        $params[] = $this->ETFData->world_regions['Australasia']->equity;
        $params[] = $this->ETFData->world_regions['Asia Developed']->equity;
        $params[] = $this->ETFData->world_regions['Asia Emerging']->equity;

        $params[] = $this->ETFData->sector_weights['Basic Materials']->equity;
        $params[] = $this->ETFData->sector_weights['Consumer Cyclicals']->equity;
        $params[] = $this->ETFData->sector_weights['Financial Services']->equity;
        $params[] = $this->ETFData->sector_weights['Real Estate']->equity;
        $params[] = $this->ETFData->sector_weights['Consumer Defensive']->equity;
        $params[] = $this->ETFData->sector_weights['Healthcare']->equity;
        $params[] = $this->ETFData->sector_weights['Utilities']->equity;
        $params[] = $this->ETFData->sector_weights['Energy']->equity;
        $params[] = $this->ETFData->sector_weights['Industrials']->equity;
        $params[] = $this->ETFData->sector_weights['Communication Services']->equity;
        $params[] = $this->ETFData->sector_weights['Technology']->equity;

        $params[] = $this->asset_class;
        $params[] = $this->frequency;
        $params[] = $this->Code;

foreach($params AS $k => $v){
    if(strlen($v) == 0){
        $params[$k] = 0;
#        echo 'found one ' . $k . '<br />';
    }
#    echo "'" . $v . "'" . '<br />';
}
#exit;
        $adb->pquery($query, $params, true);
    }
}
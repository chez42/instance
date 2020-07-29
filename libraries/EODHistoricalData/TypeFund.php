<?php

class WorldRegionData{
    public $Name, $Category_Average, $Stocks, $Benchmark;
    public function __construct($data){
        $stocks = "Stocks_%";
        $this->Name = $data->Name;
        $this->Category_Average = $data->Category_Average;
        $this->Stocks = $data->$stocks;
        $this->Benchmark = $data->Benchmark;
    }
}

class WorldRegions{
    public $RegionName, $data;
    public function __construct($name, $WorldRegionData){
        $this->RegionName = $name;
        $this->data[] = $WorldRegionData;
    }
}

class SectorData{
    public $Type, $Category_Average, $Amount, $Benchmark;
    public function __construct($data){
        $amount = "Amount_%";
        $this->Type = $data->Type;
        $this->Category_Average = $data->Category_Average;
        $this->Amount = $data->$amount;
        $this->Benchmark = $data->Benchmark;
    }
}

class SectorWeights{
    public $SectorName, $data;

    /**
     * SectorWeight is the Full Sector information under "Sector_Weights"... IE:  Cyclical-><data>
     * SectorWeights constructor.
     * @param $SectorWeight
     */
    public function __construct($name, $SectorWeightData){
        $this->SectorName = $name;
        foreach($SectorWeightData AS $k => $v){
            $this->data[] = new SectorData($v);
        }
    }
}

class Market_Capitalization{
    public $Size, $Category_Average, $Benchmark, $Portfolio;
    public function __construct($data){
        $portfolio = "Portfolio_%";
        $this->Size = $data->Size;
        $this->Category_Average = $data->Category_Average;
        $this->Benchmark = $data->Benchmark;
        $this->Portfolio = $data->$portfolio;
    }
}

class TopHoldings{
    public $Name, $Owned, $Change, $Weight;
    public function __construct($data){
        $this->Name = $data->Name;
        $this->Owned = $data->Owned;
        $this->Change = $data->Change;
        $this->Weight = $data->Weight;
    }
}

class ValueGrowth{
    public $Name, $Category_Average, $Benchmark, $Stock_Portfolio;
    public function __construct($data){
        $this->Name = $data->Name;
        $this->Category_Average = $data->Category_Average;
        $this->Benchmark = $data->Benchmark;
        $this->Stock_Portfolio = $data->Stock_Portfolio;
    }
}

class AssetAllocation{
    public $Net, $Long, $Type, $Short, $Category_Average, $Benchmark;
    public function __construct($data){
        $net = "Net_%";
        $long = "Long_%";
        $short = "Short_%";

        $this->Net = $data->$net;
        $this->Long = $data->$long;
        $this->Type = $data->Type;
        $this->Short = $data->$short;
        $this->Category_Average = $data->Category_Average;
        $this->Benchmark = $data->Benchmark;
    }
}

class TypeFund{
    //General
    public $Code, $Type, $Name, $Exchange, $CurrencyCode, $CurrencyName, $CurrencySymbol, $CountryName, $CountryISO, $ISIN, $CUSIP,
           $Fund_Summary, $Fund_Family, $Fund_Category, $Fund_Style, $Fiscal_Year_End, $MarketCapitalization;

    //MutualFund_Data
    public $Nav, $Prev_Close_Price, $Update_Date, $Portfolio_Net_Assets, $Share_Class_Net_Assets,
           $Morning_Star_Rating, $Morning_Star_Risk_Rating, $Morning_Star_Category, $Inception_Date, $Currency, $Domicile, $Yield, $Yield_YTD,
           $Yield_1Year_YTD, $Yield_3Year_YTD, $Yield_5Year_YTD, $Expense_Ratio, $Expense_Ratio_Date,
           $Asset_Allocation, $Value_Growth,
           $Top_Holdings, $Market_Capitalization, $Sector_Weights, $World_Regions, $Top_Countries, $market_capitalization, $world_regions,
           $sector_weights, $asset_allocation;

    public function __construct($fund_data){
        $this->Code = $fund_data->General->Code;
        $this->Type = $fund_data->General->Type;
        $this->Name = $fund_data->General->Name;
        $this->Exchange = $fund_data->General->Exchange;
        $this->CurrencyCode = $fund_data->General->CurrencyCode;
        $this->CurrencyName = $fund_data->General->CurrencyName;
        $this->CurrencySymbol = $fund_data->General->CurrencySymbol;
        $this->CountryName = $fund_data->General->CountryName;
        $this->CountryISO = $fund_data->General->CountryISO;
        $this->ISIN = $fund_data->General->ISIN;
        $this->CUSIP = $fund_data->General->CUSIP;
        $this->Fund_Summary = $fund_data->General->Fund_Summary;
        $this->Fund_Family = $fund_data->General->Fund_Family;
        $this->Fund_Category = $fund_data->General->Fund_Category;
        $this->Fund_Style = $fund_data->General->Fund_Style;
        $this->Fiscal_Year_End = $fund_data->General->Fiscal_Year_End;
        $this->MarketCapitalization = $fund_data->General->MarketCapitalization;

        $this->Nav = $fund_data->MutualFund_Data->Nav;
        $this->Prev_Close_Price = $fund_data->MutualFund_Data->Prev_Close_Price;
        $this->Update_Date = $fund_data->MutualFund_Data->Update_Date;
        $this->Portfolio_Net_Assets = $fund_data->MutualFund_Data->Portfolio_Net_Assets;
        $this->Share_Class_Net_Assets = $fund_data->MutualFund_Data->Share_Class_Net_Assets;
        $this->Morning_Star_Rating = $fund_data->MutualFund_Data->Morning_Star_Rating;
        $this->Morning_Star_Risk_Rating = $fund_data->MutualFund_Data->Morning_Star_Risk_Rating;
        $this->Morning_Star_Category = $fund_data->MutualFund_Data->Morning_Star_Category;
        $this->Inception_Date = $fund_data->MutualFund_Data->Inception_Date;
        $this->Currency = $fund_data->MutualFund_Data->Currency;
        $this->Domicile = $fund_data->MutualFund_Data->Domicile;
        $this->Yield = $fund_data->MutualFund_Data->Yield;
        $this->Yield_YTD = $fund_data->MutualFund_Data->Yield_YTD;
        $this->Yield_1Year_YTD = $fund_data->MutualFund_Data->Yield_1Year_YTD;
        $this->Yield_3Year_YTD = $fund_data->MutualFund_Data->Yield_3Year_YTD;
        $this->Yield_5Year_YTD = $fund_data->MutualFund_Data->Yield_5Year_YTD;
        $this->Expense_Ratio = $fund_data->MutualFund_Data->Expense_Ratio;
        $this->Expense_Ratio_Date = $fund_data->MutualFund_Data->Expense_Ratio_Date;

        foreach($fund_data->MutualFund_Data->Asset_Allocation AS $k => $allocation){
            $this->Asset_Allocation[] = new AssetAllocation($allocation);
        }

        foreach($fund_data->MutualFund_Data->Value_Growth AS $k => $allocation){
            $this->Value_Growth[] = new ValueGrowth($allocation);
        }

        foreach($fund_data->MutualFund_Data->Top_Holdings AS $k => $allocation){
            $this->Top_Holdings[] = new TopHoldings($allocation);
        }

        foreach($fund_data->MutualFund_Data->Sector_Weights AS $k => $allocation){
            $this->Sector_Weights[] = new SectorWeights($k, $allocation);
        }

        foreach($fund_data->MutualFund_Data->Market_Capitalization AS $k => $allocation){
            $this->Market_Capitalization[] = new Market_Capitalization($allocation);
        }

        foreach($fund_data->MutualFund_Data->World_Regions AS $k => $allocation){
            $this->World_Regions[] = new WorldRegions($k, $allocation);
        }
    }
}
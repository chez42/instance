<?php

class Highlights{
public $MarketCapitalization, $MarketCapitalizationMln, $EBITDA, $PERatio, $PEGRatio, $WallStreetTargetPrice, $BookValue, $DividendShare,
       $DividendYield, $EarningsShare, $EPSEstimateCurrentYear, $EPSEstimateNextYear, $EPSEstimateNextQuarter, $EPSEstimateCurrentQuarter,
       $MostRecentQuarter, $ProfitMargin, $OperatingMarginTTM, $ReturnOnAssetsTTM, $ReturnOnEquityTTM, $RevenueTTM, $RevenuePerShareTTM,
       $QuarterlyRevenueGrowthYOY, $GrossProfitTTM, $DilutedEpsTTM, $QuarterlyEarningsGrowthYOY;

    public function __construct($data){
        $this->MarketCapitalization = $data->MarketCapitalization;
        $this->MarketCapitalizationMln = $data->MarketCapitalizationMln;
        $this->EBITDA = $data->EBITDA;
        $this->PERatio = $data->PERatio;
        $this->PEGRatio = $data->PEGRatio;
        $this->WallStreetTargetPrice = $data->WallStreetTargetPrice;
        $this->BookValue = $data->BookValue;
        $this->DividendShare = $data->DividendShare;
        $this->DividendYield = $data->DividendYield;
        $this->EarningsShare = $data->EarningsShare;
        $this->EPSEstimateCurrentYear = $data->EPSEstimateCurrentYear;
        $this->EPSEstimateNextYear = $data->EPSEstimateNextYear;
        $this->EPSEstimateNextQuarter = $data->EPSEstimateNextQuarter;
        $this->EPSEstimateCurrentQuarter = $data->EPSEstimateCurrentQuarter;
        $this->MostRecentQuarter = $data->MostRecentQuarter;
        $this->ProfitMargin = $data->ProfitMargin;
        $this->OperatingMarginTTM = $data->OperatingMarginTTM;
        $this->ReturnOnAssetsTTM = $data->ReturnOnAssetsTTM;
        $this->ReturnOnEquityTTM = $data->ReturnOnEquityTTM;
        $this->RevenueTTM = $data->RevenueTTM;
        $this->RevenuePerShareTTM = $data->RevenuePerShareTTM;
        $this->QuarterlyRevenueGrowthYOY = $data->QuarterlyRevenueGrowthYOY;
        $this->GrossProfitTTM = $data->GrossProfitTTM;
        $this->DilutedEpsTTM = $data->DilutedEpsTTM;
        $this->QuarterlyEarningsGrowthYOY = $data->QuarterlyEarningsGrowthYOY;
    }
}

class TypeStock{
    //General
    public $Code, $Type, $Name, $Exchange, $CurrencyCode, $CurrencyName, $CurrencySymbol, $CountryName, $CountryISO, $ISIN, $CUSIP, $CIK,
           $EmployerIdNumber, $FiscalYearEnd, $IPODate, $InternationalDomestic, $Sector, $Industry, $GicSector, $GicGroup, $GicIndustry,
           $GicSubIndustry, $HomeCategory, $IsDelisted, $Description, $Address, $Officers, $Phone, $WebURL, $LogoURL, $FullTimeEmployees,
           $UpdatedAt, $asset_class;

    public $highlights;

    public function __construct($stock_data){
        $this->Code = $stock_data->General->Code;
        $this->Type = $stock_data->General->Type;
        $this->Name = $stock_data->General->Name;
        $this->Exchange = $stock_data->General->Exchange;
        $this->CurrencyCode = $stock_data->General->CurrencyCode;
        $this->CurrencyName = $stock_data->General->CurrencyName;
        $this->CurrencySymbol = $stock_data->General->CurrencySymbol;
        $this->CountryName = $stock_data->General->CountryName;
        $this->CountryISO = $stock_data->General->CountryISO;
        $this->ISIN = $stock_data->General->ISIN;
        $this->CUSIP = $stock_data->General->CUSIP;
        $this->CIK = $stock_data->General->CIK;
        $this->EmployerIdNumber = $stock_data->General->EmployerIdNumber;
        $this->FiscalYearEnd = $stock_data->General->FiscalYearEnd;
        $this->IPODate = $stock_data->General->IPODate;
        $this->InternationalDomestic = $stock_data->General->InternationalDomestic;
        $this->Sector = $stock_data->General->Sector;
        $this->Industry = $stock_data->General->Industry;
        $this->GicSector = $stock_data->General->GicSector;
        $this->GicGroup = $stock_data->General->GicGroup;
        $this->GicIndustry = $stock_data->General->GicIndustry;
        $this->GicSubIndustry = $stock_data->General->GicSubIndustry;
        $this->HomeCategory = $stock_data->General->HomeCategory;
        $this->IsDelisted = $stock_data->General->IsDelisted;
        $this->Description = $stock_data->General->Description;
        $this->Address = $stock_data->General->Address;
        $this->Phone = $stock_data->General->Phone;
        $this->WebURL = $stock_data->General->WebURL;
        $this->LogoURL = $stock_data->General->LogoURL;
        $this->FullTimeEmployees = $stock_data->General->FullTimeEmployees;
        $this->UpdatedAt = $stock_data->General->UpdatedAt;

        foreach($stock_data->Highlights AS $k => $v){//Fill in the highlights section
            $this->highlights->$k = $stock_data->Highlights->$k;
        }

    }
}
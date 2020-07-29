<?php

class SplitsDividends{
    public $ForwardAnnualDividendRate, $ForwardAnnualDividendYield, $PayoutRatio, $DividendDate, $ExDividendDate,
           $LastSplitFactor, $LastSplitDate;

    public function __construct($data){
        $this->ForwardAnnualDividendRate = $data->ForwardAnnualDividendRate;
        $this->ForwardAnnualDividendYield = $data->ForwardAnnualDividendYield;
        $this->PayoutRatio = $data->PayoutRatio;
        $this->DividendDate = $data->DividendDate;
        $this->ExDividendDate = $data->ExDividendDate;
        $this->LastSplitFactor = $data->LastSplitFactor;
        $this->LastSplitDate = $data->LastSplitDate;
    }
}

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

    public $highlights, $SplitsDividends;

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

        $this->highlights = new Highlights($stock_data->Highlights);
        $this->SplitsDividends = new SplitsDividends($stock_data->SplitsDividends);
    }

    public function UpdateIntoOmni(){
        global $adb;
        $query = "UPDATE vtiger_modsecurities m 
                  JOIN vtiger_modsecuritiescf cf USING (modsecuritiesid)
                  JOIN vtiger_crmentity e ON e.crmid = m.modsecuritiesid  
                  SET  securitytype = ?, stock_exchange = ?, currency_code = ?, country = ?, isin = ?, cusip = ?, security_sector = ?, industrypl = ?, summary = ?, 
                       market_capitalization = ?, ebitda = ?, peratio = ?, pegratio = ?, one_year_target_price = ?, book_value = ?, dividend_share = ?, 
                       dividend_yield = ?, earnings_share = ?, eps_estimate_current_year = ?, eps_estimate_next_year = ?, eps_estimate_next_quarter = ?";
/*
                  SET securitytype = ?, security_name = ?, stock_exchange = ?, country = ?, summary = ?, Morning_Star_Category = ?, isin = ?, 
                      dividend_yield = ?, pay_frequency = ?, us_stock = ?, intl_stock = ?, us_bond = ?, unclassified_net = ?, cash_net = ?, other_net = ?, 
                      us_equity = ?, canada_equity = ?, Latin_America_equity = ?, UK_equity = ?, Europe_ex_euro_equity = ?, 
                      Europe_Emerging_equity = ?, Africa_equity = ?, Middle_East_equity = ?, Japan_equity = ?, Australasia_equity = ?, 
                      Asia_Developed_equity = ?, Asia_Emerging_equity = ?, Basic_Materials_Weight = ?, Consumer_Cyclical_Weight = ?, 
                      Financial_Services_Weight = ?, Real_Estate_Weight = ?, Consumer_Defensive_Weight = ?, Healthcare_Weight = ?, 
                      Utilities_Weight = ?, Energy_Weight = ?, Industrials_Weight = ?, Communication_Services_Weight = ?, technology_weight = ?,
                      aclass = ?";
        $set .= " securitytype = ?, stock_exchange = ?, currency_code = ?, country = ?, isin = ?, cusip = ?, security_sector = ?, industrypl = ?, summary = ?,
                              market_capitalization = ?, ebitda = ?, peratio = ?, pegratio = ?, one_year_target_price = ?, book_value = ?, dividend_share = ?,
                              dividend_yield = ?, earnings_share = ?, eps_estimate_current_year = ?, eps_estimate_next_year = ?, eps_estimate_next_quarter = ? ";
*/
        $params[] = $this->Type;
        $params[] = $this->Name;
        $params[] = $this->Exchange;
        $params[] = $this->CurrencyCode;
        $params[] = $this->CountryName;
        $params[] = $this->ISIN;
        $params[] = $this->CUSIP;
        $params[] = $this->Sector;
        $params[] = $this->Industry;
        $params[] = $this->Description;

        $params[] = $this->highlights->MarketCapitalization;
        $params[] = $this->highlights->EBITDA;
        $params[] = $this->highlights->PERatio;
        $params[] = $this->highlights->PEGRatio;
        $params[] = $this->highlights->WallStreetTargetPrice;
        $params[] = $this->highlights->BookValue;
        $params[] = $this->highlights->DividendShare;
        $params[] = $this->Highlights->DividendYield;
        $params[] = $this->Highlights->EarningsShare;
        $params[] = $this->Highlights->EPSEstimateCurrentYear;
        $params[] = $this->Highlights->EPSEstimateNextYear;
        $params[] = $this->Highlights->EPSEstimateNextQuarter;

        $set .= " securitytype = ?, stock_exchange = ?, currency_code = ?, country = ?, isin = ?, cusip = ?, security_sector = ?, industrypl = ?, summary = ?, 
                              market_capitalization = ?, ebitda = ?, peratio = ?, pegratio = ?, one_year_target_price = ?, book_value = ?, dividend_share = ?, 
                              dividend_yield = ?, earnings_share = ?, eps_estimate_current_year = ?, eps_estimate_next_year = ?, eps_estimate_next_quarter = ? ";
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2019-02-21
 * Time: 4:15 PM
 */
require_once("libraries/EODHistoricalData/EODGuzzle.php");
include_once("libraries/custodians/OptionsMapping.php");

spl_autoload_register(function ($className) {
    if (file_exists("libraries/EODHistoricalData/$className.php")) {
        include_once "libraries/EODHistoricalData/$className.php";
    }
});

class ModSecurities_EODActions_Action extends Vtiger_BasicAjax_Action {
    static public function WriteEODToCRM($symbol){
        $recordID = ModSecurities_Module_Model::GetSecurityIdBySymbol($symbol);
        $record = ModSecurities_Record_Model::getInstanceById($recordID);

    }

    public function process(Vtiger_Request $request) {
        $record = $request->get('record');
        switch(strtolower($request->get('todo'))){
            case "updateeodsymbol":
                    include_once("libraries/EODHistoricalData/EODGuzzle.php");
                    $guz = new cEodGuzzle();
                    $security_instance = ModSecurities_Record_Model::getInstanceById($record);
                    $symbol = $security_instance->get("security_symbol");
                    $aclass = $security_instance->get('aclass');
                    $security_type = $security_instance->get('securitytype');

                    if($security_type == 'Bond'){
                        $rawData = $guz->getBonds($symbol);
                        ////////WRITE BONDS FUNCTION HERE INTO OMNISCIENT
                    }else{
                        $start = date('Y')  - 1 . "-01-01";
                        $end = date('Y') - 1 . "-12-31";

                        $rawData = $guz->getFundamentals($symbol);
                        $result = json_decode($rawData);
                        $dividendData = json_decode($guz->getDividends($symbol, "US", $start, $end));
                        ModSecurities_ConvertCustodian_Model::UpdateFromEODGuzzleResult($result, $dividendData, $symbol);
                    }

                    ModSecurities_ConvertCustodian_Model::WriteRawEODData($symbol, $rawData);
                    echo 1;
                break;
            case "getdelayedpricing":
                $guz = new cEodGuzzle();
                $security = ModSecurities_Record_Model::getInstanceById($request->get("recordid"));
                if(strlen($security->get("option_root_symbol")) > 0 && trim($security->get("option_root_symbol")) != '') {
                    $symbol = $security->get("option_root_symbol");
                    $eod = json_decode($guz->getSymbolRealTimePricing($symbol));
                    $fund = $guz->getFundamentals($symbol);
                }
                else {
                    $symbol = $security->get("security_symbol");
                    $eod = json_decode($guz->getSymbolRealTimePricing($symbol));
                    $fund = $guz->getFundamentals($symbol);
                }

                date_default_timezone_set('America/Los_Angeles');
                $eod->last_update = date("F d, Y h:i:s a", $eod->timestamp);
                $viewer = new Vtiger_Viewer();
                $viewer->assign("EOD", $eod);
                $output = $viewer->view('DetailViewEODLatestPrice.tpl', "ModSecurities", true);
                echo $output;
                break;
            case "getdelayedpopup":
                $guz = new cEodGuzzle();
                $security = Vtiger_Record_Model::getInstanceById($request->get("recordid"));
                if(strtolower($security->getModuleName()) != "modsecurities"){
                    $sym = $security->get('security_symbol');
                    $crmid = ModSecurities_Module_Model::GetCrmidFromSymbol($sym);
                    $security = Vtiger_Record_Model::getInstanceById($crmid);//Should now be a mod security
                }

                $type  = $security->get('securitytype');
                $symbol = $security->get('security_symbol');

                $viewer = new Vtiger_Viewer();
                $viewer->assign('STYLES', self::getHeaderCss($request));

                switch(strtolower($type)){
                    case "op":
                    case "option":
                        $this->ShowOptionData($symbol, $security);
                        return;
                        break;
                    case "stock":
                    case "stocks":
                    case "common stock":
                    case "eq":
                        $this->ShowStockData($symbol);
                        return;
                        break;
                    case "corporate bond":
                    case "bond":
                    case "bonds":
                        $this->ShowBondData($symbol);
                        return;
                    break;
                    default:
                        $this->ShowOmniData($symbol);
                        return;
                        break;
                }
                /**This is the original logic section before the TD mapping came in.  TD doesn't use an option root symbol*/
                if(!$eod) {
                    if (strlen($security->get("option_root_symbol")) > 0 && trim($security->get("option_root_symbol")) != '') {
                        $symbol = $security->get("option_root_symbol");
                        ###                    $eod = json_decode($guz->getOptions($symbol));//WANTED WHEN WE HAVE REALTIME OPTION PRICING
                        $eod = json_decode($guz->getSymbolRealTimePricing($symbol));
                        $notes = "This security is an option.  Showing information from the root symbol";
                    } else {
                        $symbol = $security->get("security_symbol");
                        $eod = json_decode($guz->getSymbolRealTimePricing($symbol));
                    }
                }
                /**********************************************************************************************************/
                $fund = json_decode($guz->getFundamentals($symbol));
                $data = $security->getData();
                $change = $eod->change;//$eod->close - $data['security_price'];
                $percentage = $change / $eod->close * 100;//$data['security_price'] * 100;
#print_r($eod);exit;
                date_default_timezone_set('America/Los_Angeles');
                $eod->last_update = date("F d, Y h:i:s a", $eod->timestamp);
                $viewer->assign("EOD", $eod);
                $viewer->assign("SECURITY_DATA", $security->getData());
                $viewer->assign("CHANGE", $eod->change);
                $viewer->assign("PERCENTAGE", $percentage);
                $viewer->assign("NOTES", $notes);
                if(strlen($fund->General->LogoURL) > 0)
                    $viewer->assign("LOGO", URI_LOGOS . $fund->General->LogoURL);
                $output = $viewer->view('DetailViewEODLatestPricePopup.tpl', "ModSecurities", true);
                echo $output;
                break;
        }
    }

    public function ShowStockData($symbol){
        $viewer = new Vtiger_Viewer();
        $guz = new cEodGuzzle();

        $stock = new Stock();
        $fund = json_decode($guz->getFundamentals($symbol));
        $price = json_decode($guz->getSymbolRealTimePricing($symbol));
        if(!isset($fund)){
            $this->ShowOmniData($symbol);
            return;
        }
        if(strlen($fund->General->LogoURL) > 0)
            $stock->logo = URI_LOGOS . $fund->General->LogoURL;

        $stock->symbol = strtoupper($symbol);
        $stock->price = $price->close;
        $stock->name = $fund->General->Name;
        $stock->fiscal = $fund->General->FiscalYearEnd;
        $stock->as_of = date("m/d/Y H:i:s", $price->timestamp);
        $stock->change = $price->change;
        $stock->change_percent = $price->change_p;
        $stock->description = $fund->General->Description;

        $viewer->assign("STOCK", $stock);
        $output = $viewer->view('StockPopup.tpl', "ModSecurities", true);
        echo $output;
        return;
    }

    public function ShowBondData($symbol){
        $viewer = new Vtiger_Viewer();
        $guz = new cEodGuzzle();

        $bond = new Bond();
        $b_data = json_decode($guz->getCorporateBond($symbol));
        if(!isset($b_data)){
            $this->ShowOmniData($symbol);
            return;
        }
        $bond->name = $b_data->Name;
        $bond->symbol = $symbol;
        $bond->price = $b_data->Price;
        $bond->maturity = $b_data->Maturity_Date;
        $bond->as_of = $b_data->LastTradeDate;
        $bond->description = $b_data->IssueData->IssuerDescription;
        $bond->issuer = $b_data->IssueData->Issuer;

        $viewer->assign("BOND", $bond);
        $output = $viewer->view('BondPopup.tpl', "ModSecurities", true);
        echo $output;
    }

    public function ShowOptionData($symbol, $security){
        $viewer = new Vtiger_Viewer();
        $guz = new cEodGuzzle();

        $option = new Option();
        if(strtolower($security->get("provider")) == "td")//Td doesn't have a standard mapping
            $mapped = OptionsMapping::MapTDToStandard($symbol);
        $symbol = OptionsMapping::GetSymbolFromStandardizedOption($mapped);
        $fund = json_decode($guz->getFundamentals($symbol));
        if(!isset($fund)){
            $this->ShowOmniData($symbol);
            return;
        }
        $price = json_decode($guz->getSymbolRealTimePricing($symbol));
        $op_data = json_decode($guz->getOptionContract($mapped));

        if(strlen($fund->General->LogoURL) > 0)
            $option->logo = URI_LOGOS . $fund->General->LogoURL;

        $option->symbol = $symbol;
        $option->price = $price->close;
        $option->name = $fund->General->Name;
        $option->as_of = date("m/d/Y H:i:s", $price->timestamp);
        $option->change = $price->change;
        $option->change_percent = $price->change_p;

        if(sizeof($op_data->data) > 0) {
            $option->call = $op_data->data[0]->options->CALL[0];
            $option->put = $op_data->data[0]->options->PUT[0];
        }

        $viewer->assign("OPTION", $option);
        $output = $viewer->view('OptionPopup.tpl', "ModSecurities", true);
        echo $output;
    }

    public function ShowOmniData($symbol){
        $viewer = new Vtiger_Viewer();
        $id = ModSecurities_Module_Model::GetCrmidFromSymbol($symbol);
        $security = ModSecurities_Record_Model::getInstanceById($id);

        $omni = new Omni();
        $omni->symbol = $security->get("security_symbol");
        $omni->as_of = $security->get("last_update");
        $omni->price = $security->get("security_price");
        $omni->name = $security->get("security_name");
        $pr = ModSecurities_Module_Model::GetSecurityPriceForDate($symbol, "2020-07-17");
        if($pr != 0)
            $yesterday = $pr['price'];
        else
            $yesterday = 0;

        if($yesterday != 0) {
            $omni->change = $omni->price - $yesterday;
            $omni->change_percent = $omni->change / $yesterday * 100;
        }else{
            $omni->change = 0;
            $omni->change_percent = 0;
        }

        $viewer->assign("OMNI", $omni);
        $output = $viewer->view('OmniPopup.tpl', "ModSecurities", true);
        echo $output;
        return;
    }
/*
Array
(
    [label] =&gt; MET GOVT NASHVILLE &amp;amp; DAVIDSON REV BDS CALLABLE
    [security_name] =&gt; MET GOVT NASHVILLE &amp;amp; DAVIDSON REV BDS CALLABLE
    [security_symbol] =&gt; 592041RS8
    [security_price] =&gt; 100.0000
    [security_id] =&gt;
    [assigned_user_id] =&gt; 1
    [CreatedTime] =&gt; 2020-07-10 02:14:44
    [ModifiedTime] =&gt; 2020-07-10 02:14:44
    [sectorpl] =&gt;
    [pay_frequency] =&gt;
    [securitytype] =&gt; FI
    [security_price_adjustment] =&gt; 0.01000
    [cusip] =&gt;
    [aclass] =&gt; Bonds
    [industrypl] =&gt;
    [average_daily_volume] =&gt;
    [book_value] =&gt;
    [dividend_share] =&gt; 0.00000
    [earnings_share] =&gt;
    [year_high] =&gt;
    [year_low] =&gt;
    [market_capitalization] =&gt;
    [ebitda] =&gt;
    [fifty_day_moving_average] =&gt;
    [two_hundred_day_moving_average] =&gt;
    [two_hundred_day_change] =&gt;
    [two_hundred_day_percent_change] =&gt;
    [fifty_day_change] =&gt;
    [fifty_day_percent_change] =&gt;
    [price_sales] =&gt;
    [price_book] =&gt;
    [ex_dividend_date] =&gt;
    [peratio] =&gt;
    [dividend_pay_date] =&gt;
    [pegratio] =&gt;
    [price_eps_estimate_current_year] =&gt;
    [price_eps_estimate_next_year] =&gt;
    [short_ratio] =&gt;
    [one_year_target_price] =&gt;
    [year_range] =&gt;
    [stock_exchange] =&gt;
    [dividend_yield] =&gt;
    [summary] =&gt;
    [us_stock] =&gt; 0.00
    [intl_stock] =&gt; 0.00
    [us_bond] =&gt; 0.00
    [intl_bond] =&gt; 0.00
    [preferred_net] =&gt; 0.00
    [convertible_net] =&gt; 0.00
    [cash_net] =&gt; 0.00
    [other_net] =&gt; 0.00
    [unclassified_net] =&gt; 0.00
    [ignore_auto_update] =&gt; 0
    [header] =&gt;
    [prod_code] =&gt; FI
    [options_display_symbol] =&gt;
    [description1] =&gt;
    [last_update] =&gt; 2020-07-17
    [closing_price] =&gt;
    [opt_expr_date] =&gt;
    [c_p] =&gt;
    [strike_price] =&gt;
    [interest_rate] =&gt; 5
    [maturity_date] =&gt; 2039-10-01
    [tips_factor] =&gt;
    [asset_backed_factor] =&gt; 0.00000
    [face_value_amt] =&gt;
    [source] =&gt;
    [benchmark_name] =&gt;
    [provider] =&gt; TD
    [Morning_Star_Category] =&gt;
    [beta] =&gt;
    [first_coupon_date] =&gt; 2009-10-01
    [cf_2559] =&gt;
    [cf_2561] =&gt;
    [last_eod] =&gt;
    [etf] =&gt; 0
    [cf_2612] =&gt; 0
    [cf_2616] =&gt; 0
    [cf_2618] =&gt; 0
    [cf_2620] =&gt; 0
    [cf_2622] =&gt; 0
    [preferred] =&gt; 0
    [cf_2626] =&gt; 0
    [cf_2628] =&gt; 0
    [cf_2630] =&gt; 0
    [cf_2632] =&gt; 0
    [cf_2634] =&gt; 0
    [cf_2636] =&gt; 0
    [cf_2638] =&gt; 0
    [cf_2640] =&gt; 0
    [cf_2642] =&gt; 0
    [cf_2644] =&gt; 0
    [cf_2646] =&gt; 0
    [cf_2648] =&gt; 0
    [cf_2654] =&gt;
    [security_sector] =&gt;
    [yahoo_finance_last_update] =&gt;
    [cf_2715] =&gt;
    [cf_2723] =&gt; 0
    [country] =&gt;
    [fund_family] =&gt;
    [nav] =&gt;
    [net_assets] =&gt;
    [morning_star_rating] =&gt;
    [Morning_Star_Risk_Rating] =&gt;
    [inception_date] =&gt;
    [basic_materials_weight] =&gt;
    [consumer_cyclical_weight] =&gt;
    [financial_services_weight] =&gt;
    [real_estate_weight] =&gt;
    [consumer_defensive_weight] =&gt;
    [healthcare_weight] =&gt;
    [utilities_weight] =&gt;
    [communication_services_weight] =&gt;
    [energy_weight] =&gt;
    [industrials_weight] =&gt;
    [us_equity] =&gt;
    [canada_equity] =&gt;
    [latin_america_equity] =&gt;
    [uk_equity] =&gt;
    [europe_ex_euro_equity] =&gt;
    [europe_emerging_equity] =&gt;
    [africa_equity] =&gt;
    [middle_east_equity] =&gt;
    [japan_equity] =&gt;
    [australasia_equity] =&gt;
    [asia_developed_equity] =&gt;
    [asia_emerging_equity] =&gt;
    [currency_code] =&gt;
    [technology_weight] =&gt;
    [eod_pricing] =&gt;
    [starred] =&gt;
    [call_date] =&gt; 2019-10-08
    [issue_date] =&gt;
    [call_price] =&gt; 100.0000
    [share_per_contract] =&gt; 0
    [cf_3393] =&gt;
    [ignore_gain_loss] =&gt; 0
    [option_call_put] =&gt;
    [option_root_symbol] =&gt;
    [record_id] =&gt; 74029579
    [record_module] =&gt; ModSecurities
    [id] =&gt; 74029579
)

 */
    public function getHeaderCss(Vtiger_Request $request) {
        $cssFileNames = array(
            '~/layouts/v7/modules/ModSecurities/css/EODPopup.css',
        );
//        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        return $cssFileNames;
    }
}
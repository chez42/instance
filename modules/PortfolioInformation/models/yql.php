<?php

require_once("libraries/YahooFinance/YahooFinance.php");

class PortfolioInformation_yql_Model extends Vtiger_Module{
	static public function GetSymbolQuotes($symbols){
		$yql = new YahooFinance();
		return $yql->getQuotes($symbols);
	}

	static public function GetPricingHistory($symbol, $start, $end){
		$yql = new YahooFinance();
		return $yql->getHistoricalData($symbol, $start, $end);
	}

	/**
	 * Using the data pulled from YQL, update the Mod Security Information
	 * @param $security_id
	 * @param $equity_data
	 */
	static public function UpdateModSecurityEquity($security_id, $equity_data){
		global $adb;

		$query = "UPDATE vtiger_modsecurities JOIN vtiger_modsecuritiescf USING (modsecuritiesid) 
														  SET aclass = CASE WHEN aclass = '' THEN 'Equity' ELSE aclass END, securitytype = 'Common Stock', us_stock=100, average_daily_volume = ?, book_value = ?, dividend_share = ?, earnings_share = ?, eps_estimate_current_year = ?,
														  eps_estimate_next_year = ?, eps_estimate_next_quarter = ?, year_high = ?, year_low = ?, market_capitalization = ?, ebitda = ?,
														  fifty_day_moving_average = ?, two_hundred_day_moving_average = ?, two_hundred_day_change = ?, two_hundred_day_percent_change = ?, fifty_day_change = ?,
														  fifty_day_percent_change = ?, price_sales = ?, price_book = ?, ex_dividend_date = ?, peratio = ?, dividend_pay_date = ?, pegratio = ?,
														  price_eps_estimate_current_year = ?, price_eps_estimate_next_year = ?, short_ratio = ?, one_year_target_price = ?, year_range = ?,
														  stock_exchange = ?, dividend_yield = ?, unclassified_net=0, intl_stock=0, us_bond=0, intl_bond=0, preferred_net=0, convertible_net=0, cash_net=0, other_net=0, security_price_adjustment=1
						  WHERE modsecuritiesid = ? AND ignore_auto_update IN (0)";
		$i = $equity_data;
		$exDiv = "";
		$divPay = "";
		if(strlen($i->ExDividendDate) > 0) {
			$date = DateTime::createFromFormat('d/m/Y', $i->ExDividendDate);
			$exDiv = $date->format('Y-m-d');
		}
		if(strlen($i->DividendPayDate) > 0) {
			$date = DateTime::createFromFormat('d/m/Y', $i->DividendPayDate);
			$divPay = $date->format('Y-m-d');
		}

		$adb->pquery($query, array(
			$i->AverageDailyVolume,
			$i->BookValue,
			$i->DividendShare,
			$i->EarningsShare,
			$i->EPSEstimateCurrentYear,
			$i->EPSEstimateNextYear,
			$i->EPSEstimateNextQuarter,
			$i->YearHigh,
			$i->YearLow,
			$i->MarketCapitalization,
			$i->EBITDA,
			$i->FiftydayMovingAverage,
			$i->TwoHundreddayMovingAverage,
			$i->ChangeFromTwoHundreddayMovingAverage,
			$i->PercentChangeFromTwoHundreddayMovingAverage,
			$i->ChangeFromFiftydayMovingAverage,
			$i->PercentChangeFromFiftydayMovingAverage,
			$i->PriceSales,
			$i->PriceBook,
			$exDiv,
			$i->PERatio,
			$divPay,
			$i->PEGRatio,
			$i->PriceEPSEstimateCurrentYear,
			$i->PriceEPSEstimateNextYear,
			$i->ShortRatio,
			$i->OneyrTargetPrice,
			$i->YearRange,
			$i->StockExchange,
			$i->DividendYield,
			$security_id));
	}

	static public function UpdateModSecurityProfile($security_id, $sector, $industry, $summary){
		global $adb;
		$query = "UPDATE vtiger_modsecurities JOIN vtiger_modsecuritiescf USING (modsecuritiesid) 
				  SET sectorpl = ?, industrypl = ?, summary = ?
				  WHERE modsecuritiesid = ? AND ignore_auto_update IN (0)";
		$adb->pquery($query, array($sector, $industry, $summary, $security_id));
	}
}
<?php
include_once("libraries/yql/calls.php");

class ModSecurities_Module_Model extends Vtiger_Module_Model {
	public static function GetSecurityIdBySymbol($symbol){
		global $adb;
		$query = "SELECT security_id FROM vtiger_securities WHERE security_symbol = ? ORDER BY security_data_set_id ASC;";
		$result = $adb->pquery($query, array($symbol));
		if($adb->num_rows($result) > 0){
			return $adb->query_result($result, 0, 'security_id');
		} else
			return 0;
	}

	public static function InsertIndexPrice($symbol, $date, $open, $high, $low, $close, $volume, $adj_close){
		global $adb;
		$query = "INSERT INTO vtiger_prices_index (symbol, date, open, high, low, close, volume, adj_close) VALUES (?, ?, ?, ?, ?, ?, ?, ?) 
				  ON DUPLICATE KEY UPDATE open=VALUES(open), high=VALUES(high), low=VALUES(low), close=VALUES(close), volume=VALUES(volume), adj_close=VALUES(adj_close)";
		$adb->pquery($query, array($symbol, $date, $open, $high, $low, $close, $volume, $adj_close));
	}

	public static function GetCrmidFromSymbol($symbol){
	    global $adb;
	    $query = "SELECT modsecuritiesid FROM vtiger_modsecurities 
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_modsecurities.modsecuritiesid
        WHERE vtiger_crmentity.deleted = 0 AND security_symbol = ?";
	    $result = $adb->pquery($query, array($symbol));
	    if($adb->num_rows($result) > 0)
	        return $adb->query_result($result, 0, 'modsecuritiesid');
	    return 0;
    }

    public static function GetSecurityPrice($symbol){
	    global $adb;
	    $query = "SELECT security_price FROM vtiger_modsecurities WHERE security_symbol = ?";
	    $result = $adb->pquery($query, array($symbol));
	    if($adb->num_rows($result) > 0){
	        return $adb->query_result($result, 0, 'security_price');
        }
        return 0;
    }

    /**
     * Returns an array of security symbols (security_symbol)
     * @param array $aclass
     * @return array|void
     */
    static public function GetAllSecuritySymbolsByAssetClass(array $aclass){
        global $adb;
        if(empty($aclass))
            return;

        $symbols = array();
        $questions = generateQuestionMarks($aclass);
        $query = "SELECT security_symbol 
                  FROM vtiger_modsecurities m 
                  JOIN vtiger_modsecuritiescf cf USING (modsecuritiesid) 
                  WHERE aclass IN ({$questions})";

        $result = $adb->pquery($query, array($aclass));
        if($adb->num_rows($result) > 0){
            while($r = $adb->fetchByAssoc($result)){
                $symbols[] = $r['security_symbol'];
            }
        }
        return $symbols;
    }

    public static function GetSecurityPriceForDate($symbol, $date){
	    global $adb;
	    $query = "SELECT close, date FROM vtiger_prices WHERE symbol = ? AND date <= ? ORDER BY date DESC LIMIT 1";
	    $result = $adb->pquery($query, array($symbol, $date));
	    if($adb->num_rows($result)> 0){
	        $price = $adb->query_result($result, 0, 'close');
	        $date = $adb->query_result($result, 0, 'date');
	        return array("price" => $price, "date" => $date, "symbol" => $symbol, "date" => $date);
        }
        return 0;
    }

	public static function GetSecurityInformationFromSymbols(array $symbols){
		global $adb;
		$questions = generateQuestionMarks($symbols);
		$query = "SELECT * FROM vtiger_modsecurities ms JOIN vtiger_modsecuritiescf USING (modsecuritiesid) WHERE ms.security_symbol IN ({$questions})";
		$result = $adb->pquery($query, array($symbols));
		if($adb->num_rows($result) > 0){
			while($v = $adb->fetchByAssoc($result)){
				$v['equity'] = $v['us_stock'] + $v['intl_stock'];
				$v['fixed'] = $v['us_bond'] + $v['intl_bond'] + $v['preferred_net'];
				$v['cash'] = $v['cash_net'];
				$v['other'] = $v['convertible_net'] + $v['other_net'] + $v['unclassified_net'];
				$symbol_info[$v['security_symbol']] = $v;
			}
			return $symbol_info;
		}
		return 0;
	}

	public static function GetModSecuritiesIdBySymbol($symbol){
		global $adb;
		$query = "SELECT modsecuritiesid FROM vtiger_modsecurities WHERE security_symbol = ?";
		$result = $adb->pquery($query, array($symbol));
		if($adb->num_rows($result) > 0){
			return $adb->query_result($result, 0, 'modsecuritiesid');
		} else
			return 0;
	}

	public static function GetEmptyAssetClassEntityIDs(){
		global $adb;
		$query = "select modsecuritiesid FROM vtiger_modsecurities WHERE asset_class = ''";
	}

	public static function GetSecuritiesForAccounts($account_numbers){
		global $adb;

		if(!is_array($account_numbers))
			$accounts[] = $account_numbers;
		else
			$accounts = $account_numbers;

		PortfolioInformation_HoldingsReport_Model::GenerateReportFromAccounts($accounts);

		$query = "SELECT security_symbol FROM holdings_report_positions";
		$result = $adb->pquery($query, array());
		$symbols = array();
		if($adb->num_rows($result) > 0){
			foreach($result AS $k => $v){
				$symbols[] = $v['security_symbol'];
			}
			return $symbols;
		}
		return 0;
	}

	public static function GetAndSetOmniViewNumbersString(&$params, $us_stock = 0, $intl_stock = 0, $us_bond = 0, $intl_bond = 0, $preferred_net = 0, $convertible_net = 0,
                                                           $cash_net = 0, $other_net = 0, $unclassified_net = 0){
        $params[] = $us_stock;
        $params[] = $intl_stock;
        $params[] = $us_bond;
        $params[] = $intl_bond;
        $params[] = $preferred_net;
        $params[] = $convertible_net;
        $params[] = $cash_net;
        $params[] = $other_net;
        $params[] = $unclassified_net;
        return "us_stock = ?, intl_stock = ?, us_bond = ?, intl_bond = ?, preferred_net = ?, convertible_net = ?, cash_net = ?, other_net = ?, unclassified_net = ?, ";
    }

    public static function UpdateSecurityInformationTD($data){
        global $adb;

        $symbol = $data->symbol;
        $multiplier = 1;

        $params = array();
        $params[] = $data->securityDescription;
        $params[] = $data->cusip;
        $params[] = $data->price;
        $params[] = $data->assetType;
        $params[] = $data->securityDescription;
        $params[] = $data->maturity;
        $params[] = $data->coupon;

        $query = "SELECT etf, preferred 
                  FROM vtiger_modsecurities m 
                  JOIN vtiger_modsecuritiescf USING (modsecuritiesid) 
                  WHERE security_symbol = ?";
        $result = $adb->pquery($query, array($symbol));

        if($adb->num_rows($result) > 0){
            $isetf = $adb->query_result($result, 0, 'etf');
            $ispreferred = $adb->query_result($result, 0, 'preferred');
        }

        if($data->assetType == 'B'){
            $symbol = $data->cusip;
            $security_type = "Bond";
            $omniview = self::GetAndSetOmniViewNumbersString($params, 0, 0, 100);
            $aclass = "aclass = CASE WHEN aclass = '' OR aclass IS NULL THEN 'Bonds' ELSE aclass END, ";
            $multiplier = 0.01;
        }

        if($data->assetType == 'O'){
            $symbol = $data->shortSecurityDescription;
            $multiplier = 100;
            $security_type = "Option";
            $aclass = "aclass = CASE WHEN aclass = '' OR aclass IS NULL THEN 'Other' ELSE aclass END, ";
            $omniview = self::GetAndSetOmniViewNumbersString($params, 0, 0, 0, 0, 0, 0, 0, 100);
        }

        if($data->assetType == 'F'){
            $symbol = $data->symbol;
            $multiplier = 1;
            $security_type = "Mutual Fund";
//            $aclass = "aclass = 'Funds', ";
//            $omniview = self::GetAndSetOmniViewNumbersString($params, 0, 0, 0, 0, 0, 0, 0, 100);
            $aclass = "";
            $omniview = "";
        }

        if($data->assetType == 'E'){
            $symbol = $data->symbol;
            if(strlen($symbol) < 1)
                $symbol = $data->cusip;
            $symbol = str_replace(".", "", $symbol);
#            echo "SYMBOL: {$symbol}";
            $multiplier = 1;
            if (strpos($data->securityDescription, 'ETF') !== false) {
                $security_type = "ETF";
                $aclass = "etf = 1, ";
                $omniview = "";
            }else
            if (strpos($data->securityDescription, 'PFD') !== false) {
                $security_type = "Preferred Stock";
                $aclass = "aclass = CASE WHEN aclass = '' THEN 'Bonds' ELSE aclass END, cf.preferred = 1, ";
                $omniview = self::GetAndSetOmniViewNumbersString($params, 0, 0, 100, 0, 0, 0, 0, 0);
            }else{
                $security_type = "Common Stock";
                $aclass = "aclass = CASE WHEN aclass = '' OR aclass IS NULL THEN 'Stocks' ELSE aclass END, ";
                if($isetf) {
                    $security_type = "ETF";
                    $aclass = "";
                    $omniview = "";
                    $only_if_blank_asset_class = " AND aclass = '' OR aclass IS NULL ";
                }
                elseif($ispreferred) {
                    $security_type = "Preferred Stock";
                    $aclass = "aclass = CASE WHEN aclass = '' OR aclass IS NULL THEN 'Bonds' ELSE aclass END, ";
                    $omniview = self::GetAndSetOmniViewNumbersString($params, 0, 0, 100, 0, 0, 0, 0, 0);
                }
                else {
                    $omniview = self::GetAndSetOmniViewNumbersString($params, 100, 0, 0, 0, 0, 0, 0, 0);
                }
            }
        }

        if($data->assetType == 'M'){
            $symbol = $data->symbol;
            $multiplier = 1;
            $security_type = "Money Market Fund";
            $aclass = "aclass = CASE WHEN aclass = '' OR aclass IS NULL THEN 'Cash' ELSE aclass END, ";
            $omniview = self::GetAndSetOmniViewNumbersString($params, 0, 0, 0, 0, 0, 0, 100, 0);
        }

        $params[] = $multiplier;
        $params[] = $security_type;
        $params[] = "TD";
        $params[] = $symbol;

        $query = "UPDATE vtiger_modsecurities m
                  JOIN vtiger_modsecuritiescf cf USING (modsecuritiesid)
                  SET security_name = ?, cusip = ?, security_price = ?, prod_code = ?, description1 = ?, m.maturity_date = ?, m.interest_rate = ?,
                      {$omniview} {$aclass} security_price_adjustment = ?, securitytype = CASE WHEN etf = 1 THEN 'ETF' ELSE ? END, cf.provider = ?, last_update = NOW()
                  WHERE security_symbol = ? {$only_if_blank_asset_class} ";
        $adb->pquery($query, $params);
    }

    public static function FillSecurityBenchmarks($accounts){
        global $adb;
        $securities = self::GetSecuritiesForAccounts($accounts);
        if(is_array($securities)) {
            $xignite = PortfolioInformation_xignite_Model::GetFundBenchmarkInformation($securities);
            $query = "UPDATE vtiger_modsecurities SET benchmark_name = ? WHERE security_symbol = ?";
            foreach ($xignite AS $k => $v) {
                if ($v->Outcome === "Success") {
                    $adb->pquery($query, array($v->Benchmarks[0]->Name, $v->Fund->Symbol));
                }
            }
        }
    }

	public static function FillSecuritiesWithYQLOrXigniteDataForAccount($accounts){
		$securities = self::GetSecuritiesForAccounts($accounts);
		foreach($securities AS $k => $v){
			self::FillWithYQLOrXigniteData($v);
		}
	}

	public static function FillWithXigniteData($symbols){
	    global $adb;
        $xignite_aa = PortfolioInformation_xignite_Model::GetFundsAssetAllocation($symbols);
        foreach($xignite_aa AS $k => $v){
            if($v->Outcome === "Success"){
                $us_stock = $v->StockAssetAllocation->USStockNetAllocation;
                $intl_stock = $v->StockAssetAllocation->NonUSStockNetAllocation;
                $us_bond = $v->BondAssetAllocation->USBondNetAllocation;
                $intl_bond = $v->BondAssetAllocation->NonUSBondNetAllocation;
                $preferred = $v->OtherAssetAllocation->PreferredNetAllocation;
                $convertible = $v->OtherAssetAllocation->ConvertibleNetAllocation;
                $cash = $v->OtherAssetAllocation->CashNetAllocation;
                $other = $v->OtherAssetAllocation->OtherNetAllocation;

                $query = "UPDATE vtiger_modsecurities m JOIN vtiger_modsecuritiescf USING (modsecuritiesid)
						  SET us_stock=?, intl_stock=?, us_bond=?, intl_bond=?, preferred_net=?, convertible_net=?, cash_net=?, other_net=?, unclassified_net=0, security_price_adjustment=1
						  WHERE security_symbol = ? AND ignore_auto_update = 0";
                $adb->pquery($query, array($us_stock, $intl_stock, $us_bond, $intl_bond, $preferred, $convertible, $cash, $other, $symbols[$k]));
                PortfolioInformation_xignite_Model::UpdateSyncStatus($symbols[$k], "Success");
            }else{
                PortfolioInformation_xignite_Model::UpdateSyncStatus($symbols[$k], "Does not exist");
            }
        }
    }

    public static function AutoUpdateEmptySecurities(){
	    global $adb;
	    $query = "SELECT security_symbol FROM vtiger_modsecurities m
                  JOIN vtiger_modsecuritiescf cf USING (modsecuritiesid)
                  WHERE m.securitytype = ''
                  AND security_symbol REGEXP '^[A-Za-z ]+$'
                  ORDER BY security_symbol ASC";
	    $result = $adb->pquery($query, array());
	    if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                self::FillWithYQLData($v['security_symbol']);
            }
        }

    }

    public static function FillWithYQLData($symbol)
    {
        $security_id = ModSecurities_Module_Model::GetModSecuritiesIdBySymbol($symbol);
        $security_instance = Vtiger_Record_Model::getInstanceById($security_id, "ModSecurities");
        $security_data = $security_instance->getData();

        $yql = json_decode(PortfolioInformation_yql_Model::GetSymbolQuotes($symbol));
        if (is_object($yql))
            $SYMBOL_INFO = $yql->query->results->quote;

        /*NEED TO REVERSE XIGNITE/YAHOO ORDER.... If we get a hit from xignite, do not update percentages from yahoo!... there are times both exist*/
        if (strlen($SYMBOL_INFO->MarketCapitalization) > 2 || $SYMBOL_INFO->Bid > 0 || $SYMBOL_INFO->AverageDailyVolume > 0 || $SYMBOL_INFO->Open > 0 || $SYMBOL_INFO->Beta != 0) {
            $SYMBOL_INFO->symbol_type = 'equity';
            PortfolioInformation_yql_Model::UpdateModSecurityEquity($security_id, $SYMBOL_INFO);
            $summary = new YQLCalls();
            $data = $summary->GetProfile($SYMBOL_INFO->symbol);
            $sector = $data->query->results->td{1}->content;
            $industry = $data->query->results->td{2}->content;
            $summary = $data->query->results->p;
            if (strlen($sector) > 0 && strlen($industry) > 0 && strlen($summary) > 0) {
                $security_data['sectorpl'] = $sector;
                $security_data['industrypl'] = $industry;
                $security_data['summary'] = $summary;
                PortfolioInformation_yql_Model::UpdateModSecurityProfile($security_id, $sector, $industry, $summary);
            }
        }
    }

	public static function FillWithYQLOrXigniteData($symbol){
		global $adb;
		$security_id = ModSecurities_Module_Model::GetModSecuritiesIdBySymbol($symbol);
		$security_instance = Vtiger_Record_Model::getInstanceById($security_id, "ModSecurities");
		$security_data = $security_instance->getData();
        $xigniteResult = 0;

/*
        $xignite_pr = PortfolioInformation_xignite_Model::GetFundProfileInformation($symbol);
        if($xignite_pr->Outcome === "Success"){
            $summary = $xignite_pr->Profile->InvestmentSummary;
            $query = "UPDATE vtiger_modsecurities m JOIN vtiger_modsecuritiescf USING (modsecuritiesid)
						  SET summary = ?
						  WHERE modsecuritiesid = ?";
            $adb->pquery($query, array($summary, $security_id));
        }
        $xignite_aa = PortfolioInformation_xignite_Model::GetFundAssetAllocation($symbol);
        if($xignite_aa->Outcome === "Success") {
            $xigniteResult = 1;
            $security_data['aclass'] = "Fund";
            $us_stock = $xignite_aa->StockAssetAllocation->USStockNetAllocation;
            $intl_stock = $xignite_aa->StockAssetAllocation->NonUSStockNetAllocation;
            $us_bond = $xignite_aa->BondAssetAllocation->USBondNetAllocation;
            $intl_bond = $xignite_aa->BondAssetAllocation->NonUSBondNetAllocation;
            $preferred = $xignite_aa->OtherAssetAllocation->PreferredNetAllocation;
            $convertible = $xignite_aa->OtherAssetAllocation->ConvertibleNetAllocation;
            $cash = $xignite_aa->OtherAssetAllocation->CashNetAllocation;
            $other = $xignite_aa->OtherAssetAllocation->OtherNetAllocation;

            $query = "UPDATE vtiger_modsecurities m JOIN vtiger_modsecuritiescf USING (modsecuritiesid)
						  SET us_stock=?, intl_stock=?, us_bond=?, intl_bond=?, preferred_net=?, convertible_net=?, cash_net=?, other_net=?, unclassified_net=0, security_price_adjustment=1
						  WHERE modsecuritiesid = ? AND ignore_auto_update = 0";
            $adb->pquery($query, array($us_stock, $intl_stock, $us_bond, $intl_bond, $preferred, $convertible, $cash, $other, $security_id));
        }else{*///We have no data for xignite nor do we have it from yahoo
            $yql = json_decode(PortfolioInformation_yql_Model::GetSymbolQuotes($symbol));
            if(is_object($yql))
                $SYMBOL_INFO = $yql->query->results->quote;

            /*NEED TO REVERSE XIGNITE/YAHOO ORDER.... If we get a hit from xignite, do not update percentages from yahoo!... there are times both exist*/
            if(strlen($SYMBOL_INFO->MarketCapitalization) > 2 || $SYMBOL_INFO->Bid > 0 || $SYMBOL_INFO->AverageDailyVolume > 0 || $SYMBOL_INFO->Open > 0 || $SYMBOL_INFO->Beta != 0) {
                $SYMBOL_INFO->symbol_type = 'equity';
                PortfolioInformation_yql_Model::UpdateModSecurityEquity($security_id, $SYMBOL_INFO);
                $summary = new YQLCalls();
                $data = $summary->GetProfile($SYMBOL_INFO->symbol);
                $sector = $data->query->results->td{1}->content;
                $industry = $data->query->results->td{2}->content;
                $summary =$data->query->results->p;
                if(strlen($sector) > 0 && strlen($industry) > 0 && strlen($summary) > 0) {
                    $security_data['sectorpl'] = $sector;
                    $security_data['industrypl'] = $industry;
                    $security_data['summary'] = $summary;
                    PortfolioInformation_yql_Model::UpdateModSecurityProfile($security_id, $sector, $industry, $summary);
                }
/*            }
            /*				$query = "UPDATE vtiger_modsecurities m JOIN vtiger_modsecuritiescf USING (modsecuritiesid) JOIN vtiger_crmentity e ON e.crmid = m.modsecuritiesid
                                      SET us_stock=0, intl_stock=0, us_bond=0, intl_bond=0, preferred_net=0, convertible_net=0, cash_net=0, other_net=0, unclassified_net=100, e.modifiedtime = NOW()
                                      WHERE modsecuritiesid = ? AND ignore_auto_update = 0";//Only update if auto update
                            $adb->pquery($query, array($security_id));*/
        }
	}

	static public function GetSecurityTypeRowForDescriptionMapping($custodian = null, $search_field_name = null, $search_criteria = null, $security_type = null){
        global $adb;
        $where_set = 0;
        $where = "";
        $params = array();

        if($custodian){
            if(!$where_set) {
                $where_set = 1;
                $where .= " WHERE custodian = ? ";
            }
            $params[] = $custodian;
        }

        if($search_field_name){
            if(!$where_set) {
                $where_set = 1;
                $where .= " WHERE search_field_name = ? ";
            }
            else
                $where .= " AND search_field_name = ? ";
            $params[] = $search_field_name;
        }

        if($search_criteria){
            if(!$where_set){
                $where_set = 1;
                $where .= " WHERE search_criteria = ? ";
            }
            else
                $where .= " AND search_criteria = ? ";
            $params[] = $search_criteria;
        }

        if($security_type){
            if(!$where_set){
                $where_set = 1;
                $where .= " WHERE security_type = ? ";
            }
            else
                $where .= " AND security_type = ? ";
            $params[] = $security_type;
        }

        $query = "SELECT * FROM vtiger_security_type_mapping {$where}";
        $result = $adb->pquery($query, $params);
        $rows = array();
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $rows[] = $v;
            }
            return $rows;
        }
        return 0;
    }

    static private function UpdateSecurityTypeFromRow($row){
	    global $adb;
	    $query = "DROP TABLE IF EXISTS SecuritiesToUpdate";
	    $adb->pquery($query, array());

	    $query = "CREATE TEMPORARY TABLE SecuritiesToUpdate
                  SELECT modsecuritiesid FROM vtiger_modsecurities WHERE {$row['search_field_name']} LIKE ('{$row['search_criteria']}')";
	    $adb->pquery($query, array());

	    $params = array();
	    $extra = '';

	    $params[] = $row['security_type'];
	    if(strlen($row['asset_class']) > 1){
	        $extra .= ', aclass = CASE WHEN aclass = "" THEN ? ELSE aclass END ';
	        $params[] = $row['asset_class'];
        }
	    $query = "UPDATE vtiger_modsecurities SET securitytype = ? {$extra} WHERE modsecuritiesid IN (SELECT modsecuritiesid FROM SecuritiesToUpdate)";
	    $adb->pquery($query, $params);
    }

	static public function SetSecurityTypeFromDescriptionMapping($custodian = null, $search_field_name = null, $search_criteria = null, $security_type = null){
        $rows = self::GetSecurityTypeRowForDescriptionMapping($custodian, $search_field_name, $search_criteria, $security_type);
        if(sizeof($rows) > 0) {
            foreach ($rows AS $k => $v) {
                self::UpdateSecurityTypeFromRow($v);
            }
        }
    }

    static public function GetSubCategoryBySymbol($symbol){
	    global $adb;

	    $query = "SELECT securitytype FROM vtiger_modsecurities WHERE security_symbol = ?";
	    $result = $adb->pquery($query, array($symbol));
	    if($adb->num_rows($result) > 0){
	        return $adb->query_result($result, 0, 'securitytype');
        }else
            return 0;
    }

    static public function GetCategoryBySymbol($symbol){
        global $adb;
        $query = "DROP TABLE IF EXISTS CalculateCategory";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE CalculateCategory
                  SELECT us_stock + intl_stock AS 'EquityTotal', 
                         us_bond + intl_bond + preferred_net AS 'BondTotal', 
                         cash_net AS 'CashTotal',
                         convertible_net + other_net + unclassified_net AS 'OtherTotal'
                  FROM  vtiger_modsecurities ms 
                  JOIN vtiger_modsecuritiescf mscf ON mscf.modsecuritiesid = ms.modsecuritiesid
                  WHERE ms.security_symbol IN (?);";
        $adb->pquery($query, array($symbol));

        $query = "SELECT CASE GREATEST(EquityTotal, BondTotal, CashTotal, OtherTotal)
                                WHEN EquityTotal THEN 'Equity'
                                WHEN BondTotal THEN 'Fixed Income'
                                WHEN CashTotal THEN 'Cash and Equivalent'
                                WHEN OtherTotal THEN 'Alternative' END AS estimatedtype
                      FROM CalculateCategory;";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0) {
#            echo $adb->query_result($result, 0, 'estimatedtype') . "<br /><br />";
            return $adb->query_result($result, 0, 'estimatedtype');
        }
        else
            return "Unknown";
    }

    static public function GetYahooFinanceNullSymbols(){
        global $adb;
        $query = "SELECT symbol FROM vtiger_yahoo_finanace_modsecurities_symbol WHERE category is null ORDER BY symbol DESC";
        $result = $adb->pquery($query, array());
        $symbols = array();
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $symbols[] = $v['symbol'];
            }
        }
        return $symbols;
    }

    static public function FillYahooFinanceTableWithSymbolData($symbol_data){
        global $adb;
        $query = "UPDATE vtiger_yahoo_finanace_modsecurities_symbol
                  SET ytd_return = ?, category = ?, beta_3y = ?, yield = ?, cash = ?, stock = ?, bonds = ?, others = ?, preferred = ?,
                  convertable = ?, 5y_avg_return = ?, fund_summary = ?, yahoo_finance_modifiedtime = NOW(), modifiedtime = NOW()
                  WHERE symbol = ?";
        $params = array();
        $params[] = rtrim($symbol_data['YTD Return'], '%');
        $params[] = $symbol_data['Category'];
        $params[] = rtrim($symbol_data['Beta (3y)'], '%');
        $params[] = rtrim($symbol_data['Yield'], '%');
        $params[] = rtrim($symbol_data['Cash'], '%');
        $params[] = rtrim($symbol_data['Stocks'], '%');
        $params[] = rtrim($symbol_data['Bonds'], '%');
        $params[] = rtrim($symbol_data['Others'], '%');
        $params[] = rtrim($symbol_data['Preferred'], '%');
        $params[] = rtrim($symbol_data['Convertable'], '%');
        $params[] = rtrim($symbol_data['5y Average Return'], '%');
        $params[] = rtrim($symbol_data['Fund Summary'], '%');
        $params[] = $symbol_data['Symbol'];

        $adb->pquery($query, $params);
    }

    static public function GetEODDate($symbol){
        global $adb;
        $query = "SELECT last_eod FROM vtiger_modsecurities m JOIN vtiger_modsecuritiescf cf USING (modsecuritiesid) WHERE security_symbol = ?";
        $result = $adb->pquery($query, array($symbol));
        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, 'last_eod');
        }
        return 0;
    }

    static public function GetAllSecuritySymbols()
    {
        global $adb;
        $symbols = array();

        $query = "SELECT security_symbol FROM vtiger_modsecurities";
        $result = $adb->pquery($query, array(), true);
        if($adb->num_rows($result) > 0) {
            while ($v = $adb->fetchByAssoc($result)) {
                $symbols[] = $v['security_symbol'];
            }
        }
        return $symbols;
    }

    static public function GetAllSecuritiesFromType(array $security_type){
        global $adb;
        $questions = generateQuestionMarks($security_type);
        $params = array();
        $params[] = $security_type;

        $query = "SELECT security_symbol AS symbol, us_stock, intl_stock, us_bond, intl_bond, preferred_net, cash_net, convertible_net, other_net, unclassified_net
                  FROM vtiger_modsecurities ms
                  JOIN vtiger_modsecuritiescf cf USING (modsecuritiesid)
                  WHERE securitytype IN ({$questions})";
        $result = $adb->pquery($query, $params);
        $tmp = array();
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $aclass = "Funds";
                $total['Funds'] = 0;
                $total['Stocks'] = $v['us_stock'] + $v['intl_stock'];//Stocks
                $total['Bonds'] = $v['us_bond'] + $v['intl_bond'] + $v['preferred_net'];//Bonds
                $total['Cash'] = $v['cash_net'];//Cash
                $total['Other'] =  $v['convertible_net'] + $v['other_net'];//Other
                $total['Unclassified'] = $v['unclassified_net'];//Unclassified
                $maxs = array_keys($total, max($total));//Determine the greatest between all.  Should default to Funds if all equal 0
                $v['aclass'] = $maxs[0];
                $tmp[] = $v;

/*#                $maxs = array_keys($array, max($array))
                if($v['Stocks'] >= 50)
                    $aclass = "Stocks";
                if($v['Bonds'] >= 50)
                    $aclass = "Bonds";
                if($v['Cash'] >= 50)
                    $aclass = "Cash";
                if($v['Other'] >= 50)
                    $aclass = "Other";
                if($v['Unclassified'] >= 50)
                    $aclass = "Unclassified";
                $v['aclass'] = $aclass;
                $tmp[] = $v;*/
            }
        }
        return $tmp;
    }

    static public function UpdateAllMutualFundAssetClass(){
	    global $adb;
	    $security_types = array("mutual funds", "mutual fund");
	    $securities = self::GetAllSecuritiesFromType($security_types);
	    $query = "UPDATE vtiger_modsecurities ms
	              JOIN vtiger_modsecuritiescf cf USING (modsecuritiesid) 
	              SET aclass = CASE WHEN aclass = '' THEN ? ELSE aclass END WHERE security_symbol = ?";
        foreach($securities AS $k => $v){
            $adb->pquery($query, array($v['aclass'], $v['symbol']));
        }
    }

    /**
     * Creates a security based on positions that dont have a matching symbol to the mod securities table
     */
    static public function CreateSecurityFromPositions(){
        global $adb;

        $query = "DROP TABLE IF EXISTS SecuritiesToCreate";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE SecuritiesToCreate
                  SELECT account_number, security_symbol, base_asset_class, security_type, multiplier, last_price, description, 0 AS crmid
                  FROM vtiger_positioninformation p
                  JOIN vtiger_positioninformationcf cf USING (positioninformationid)
                  WHERE p.security_symbol NOT IN (SELECT security_symbol FROM vtiger_modsecurities)
                  AND security_type IS NOT NULL 
                  AND base_asset_class IS NOT NULL 
                  AND multiplier IS NOT NULL
                  AND last_price IS NOT NULL
                  GROUP BY p.security_symbol";
        $adb->pquery($query, array());

        $crmid = $adb->getUniqueID("vtiger_crmentity");
        $query = "UPDATE SecuritiesToCreate SET crmid = ?";
        $adb->pquery($query, array($crmid));

        $query = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, createdtime, modifiedtime, label) 
                  SELECT crmid, 1, 1, 1, 'ModSecurities', NOW(), NOW(), description FROM SecuritiesToCreate";
        $adb->pquery($query, array());

        $query = "INSERT INTO vtiger_modsecurities (modsecuritiesid, security_symbol, security_name, securitytype, security_price, last_update) 
                  SELECT crmid, security_symbol, description, security_type, last_price, NOW() FROM SecuritiesToCreate";
        $adb->pquery($query, array());

        $query = "INSERT INTO vtiger_modsecuritiescf (modsecuritiesid, aclass, security_price_adjustment) 
                  SELECT crmid, base_asset_class, multiplier FROM SecuritiesToCreate";
        $adb->pquery($query, array());
    }

    public static function UpdateTDSecurityTableBonds($data){
        global $adb;

        $params = array();
        $params[] = $data->securityDescription;
        $params[] = $data->cusip;
        $params[] = $data->price;
        $params[] = $data->assetType;
        $params[] = $data->securityDescription;
        $params[] = $data->maturity;
        $params[] = $data->coupon;
        $params[] = 0;
        $params[] = 0;
        $params[] = 100;
        $params[] = 0;
        $params[] = 0;
        $params[] = 0;
        $params[] = 0;
        $params[] = 0;
        $params[] = 0;
        $params[] = 'Bonds';//aclass
        $params[] = '0.01';//Multiplier (Security Price Adjustment)
        $params[] = 'Bond';//Security type
        $params[] = 'TD';//Provider
        $params[] = $data->cusip;

        $query = "INSERT INTO TD_SECURITIES_TMP
                  (security_name, cusip, security_price, prod_code, description1, maturity_date, interest_rate, us_stock, intl_stock, us_bond, intl_bond, preferred_net, convertible_net, cash_net, other_net, unclassified_net, aclass, security_price_adjustment, securitytype, provider, last_update, symbol)
                  VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)
                  ON DUPLICATE KEY UPDATE security_price = VALUES(security_price), 
                                          maturity_date = VALUES(maturity_date),
                                          interest_rate = VALUES(interest_rate),
                                          last_update = VALUES(last_update)";
        $adb->pquery($query, $params);
    }

    public static function UpdateTDSecurityTableOptions($data){
        global $adb;

        $params = array();
        $params[] = $data->securityDescription;
        $params[] = $data->cusip;
        $params[] = $data->price;
        $params[] = $data->assetType;
        $params[] = $data->securityDescription;
        $params[] = $data->maturity;
        $params[] = $data->coupon;
        $params[] = 0;
        $params[] = 0;
        $params[] = 0;
        $params[] = 0;
        $params[] = 0;
        $params[] = 0;
        $params[] = 0;
        $params[] = 100;
        $params[] = 0;
        $params[] = 'Other';//aclass
        $params[] = '100';//Multiplier (Security Price Adjustment)
        $params[] = 'Option';//Security type
        $params[] = 'TD';//Provider
        $params[] = $data->shortSecurityDescription;

        $query = "INSERT INTO TD_SECURITIES_TMP
                  (security_name, cusip, security_price, prod_code, description1, maturity_date, interest_rate, us_stock, intl_stock, us_bond, intl_bond, preferred_net, convertible_net, cash_net, other_net, unclassified_net, aclass, security_price_adjustment, securitytype, provider, last_update, symbol)
                  VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)
                  ON DUPLICATE KEY UPDATE security_price = VALUES(security_price), 
                                          maturity_date = VALUES(maturity_date),
                                          interest_rate = VALUES(interest_rate),
                                          last_update = VALUES(last_update)";
        $adb->pquery($query, $params);
    }

    public static function UpdateTDSecurityTableMutualFund($data){
        global $adb;
        $params = array();
        $params[] = $data->securityDescription;
        $params[] = $data->cusip;
        $params[] = $data->price;
        $params[] = $data->assetType;
        $params[] = $data->securityDescription;
        $params[] = $data->maturity;
        $params[] = $data->coupon;
        $params[] = 0;
        $params[] = 0;
        $params[] = 0;
        $params[] = 0;
        $params[] = 0;
        $params[] = 0;
        $params[] = 0;
        $params[] = 0;
        $params[] = 0;
        $params[] = '';//aclass
        $params[] = '1';//Multiplier (Security Price Adjustment)
        $params[] = 'Mutual Fund';//Security type
        $params[] = 'TD';//Provider
        $params[] = $data->symbol;

        $query = "INSERT INTO TD_SECURITIES_TMP
                  (security_name, cusip, security_price, prod_code, description1, maturity_date, interest_rate, us_stock, intl_stock, us_bond, intl_bond, preferred_net, convertible_net, cash_net, other_net, unclassified_net, aclass, security_price_adjustment, securitytype, provider, last_update, symbol)
                  VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)
                  ON DUPLICATE KEY UPDATE security_price = VALUES(security_price), 
                                          maturity_date = VALUES(maturity_date),
                                          interest_rate = VALUES(interest_rate),
                                          last_update = VALUES(last_update)";
        $adb->pquery($query, $params);
    }

    public static function UpdateTDSecurityTableMoneyMarketFund($data){
        global $adb;
        $params = array();
        $params[] = $data->securityDescription;
        $params[] = $data->cusip;
        $params[] = $data->price;
        $params[] = $data->assetType;
        $params[] = $data->securityDescription;
        $params[] = $data->maturity;
        $params[] = $data->coupon;
        $params[] = 0;
        $params[] = 0;
        $params[] = 0;
        $params[] = 0;
        $params[] = 0;
        $params[] = 0;
        $params[] = 100;
        $params[] = 0;
        $params[] = 0;
        $params[] = 'Cash';//aclass
        $params[] = '1';//Multiplier (Security Price Adjustment)
        $params[] = 'Money Market Fund';//Security type
        $params[] = 'TD';//Provider
        $params[] = $data->symbol;

        $query = "INSERT INTO TD_SECURITIES_TMP
                  (security_name, cusip, security_price, prod_code, description1, maturity_date, interest_rate, us_stock, intl_stock, us_bond, intl_bond, preferred_net, convertible_net, cash_net, other_net, unclassified_net, aclass, security_price_adjustment, securitytype, provider, last_update, symbol)
                  VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)
                  ON DUPLICATE KEY UPDATE security_price = VALUES(security_price), 
                                          maturity_date = VALUES(maturity_date),
                                          interest_rate = VALUES(interest_rate),
                                          last_update = VALUES(last_update)";
        $adb->pquery($query, $params);
    }

    public static function UpdateTDSecurityTableCommonStock($data){
        global $adb;

        $symbol = $data->symbol;
        if(strlen($symbol) < 1)
            $symbol = $data->cusip;
        $symbol = str_replace(".", "", $symbol);

        $params = array();
        $params[] = $data->securityDescription;
        $params[] = $data->cusip;
        $params[] = $data->price;
        $params[] = $data->assetType;
        $params[] = $data->securityDescription;
        $params[] = $data->maturity;
        $params[] = $data->coupon;

        $multiplier = 1;
        if (strpos($data->securityDescription, 'ETF') !== false) {
            $security_type = "ETF";
            $params[] = 0;
            $params[] = 0;
            $params[] = 0;
            $params[] = 0;
            $params[] = 0;
            $params[] = 0;
            $params[] = 0;
            $params[] = 0;
            $params[] = 0;

            $params[] = '';//aclass
            $params[] = $multiplier;//Multiplier (Security Price Adjustment)
            $params[] = $security_type;//Security type
            $params[] = 'TD';//Provider
            $params[] = $symbol;

            $query = "INSERT INTO TD_SECURITIES_TMP
                  (security_name, cusip, security_price, prod_code, description1, maturity_date, interest_rate, us_stock, intl_stock, us_bond, intl_bond, preferred_net, convertible_net, cash_net, other_net, unclassified_net, aclass, security_price_adjustment, securitytype, provider, last_update, symbol, etf)
                  VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, 1)
                  ON DUPLICATE KEY UPDATE security_price = VALUES(security_price), 
                                          maturity_date = VALUES(maturity_date),
                                          interest_rate = VALUES(interest_rate),
                                          last_update = VALUES(last_update)";
            $adb->pquery($query, $params);

        }else
            if (strpos($data->securityDescription, 'PFD') !== false) {
                $security_type = "Preferred Stock";
                $params[] = 0;
                $params[] = 0;
                $params[] = 100;
                $params[] = 0;
                $params[] = 0;
                $params[] = 0;
                $params[] = 0;
                $params[] = 0;
                $params[] = 0;
                $params[] = 'Bonds';//aclass
                $params[] = $multiplier;//Multiplier (Security Price Adjustment)
                $params[] = $security_type;//Security type
                $params[] = 'TD';//Provider
                $params[] = $symbol;

                $query = "INSERT INTO TD_SECURITIES_TMP
              (security_name, cusip, security_price, prod_code, description1, maturity_date, interest_rate, us_stock, intl_stock, us_bond, intl_bond, preferred_net, convertible_net, cash_net, other_net, unclassified_net, aclass, security_price_adjustment, securitytype, provider, last_update, symbol, preferred)
              VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, 1)
              ON DUPLICATE KEY UPDATE security_price = VALUES(security_price), 
                                      maturity_date = VALUES(maturity_date),
                                      interest_rate = VALUES(interest_rate),
                                      last_update = VALUES(last_update)";
                $adb->pquery($query, $params);
            }else{
                $security_type = "Common Stock";
                $params[] = 100;
                $params[] = 0;
                $params[] = 0;
                $params[] = 0;
                $params[] = 0;
                $params[] = 0;
                $params[] = 0;
                $params[] = 0;
                $params[] = 0;
                $params[] = 'Stocks';//aclass
                $params[] = $multiplier;//Multiplier (Security Price Adjustment)
                $params[] = $security_type;//Security type
                $params[] = 'TD';//Provider
                $params[] = $symbol;

                $query = "INSERT INTO TD_SECURITIES_TMP
              (security_name, cusip, security_price, prod_code, description1, maturity_date, interest_rate, us_stock, intl_stock, us_bond, intl_bond, preferred_net, convertible_net, cash_net, other_net, unclassified_net, aclass, security_price_adjustment, securitytype, provider, last_update, symbol)
              VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)
              ON DUPLICATE KEY UPDATE security_price = VALUES(security_price), 
                                      maturity_date = VALUES(maturity_date),
                                      interest_rate = VALUES(interest_rate),
                                      last_update = VALUES(last_update)";
                $adb->pquery($query, $params);
            }
    }

    public static function CopyTmpTDTableToCRM(){
        global $adb;

        $query = "UPDATE vtiger_modsecurities m
                  JOIN vtiger_modsecuritiescf cf USING (modsecuritiesid)
                  JOIN td_securities_tmp tmp ON tmp.symbol = m.security_symbol
                  SET m.security_name = tmp.security_name, cf.cusip = tmp.cusip, m.security_price = tmp.security_price, 
                  m.prod_code = tmp.prod_code, m.description1 = tmp.description1, 
                  m.maturity_date = tmp.maturity_date, m.interest_rate = tmp.interest_rate,
                  cf.us_stock = tmp.us_stock, 
                  cf.intl_stock = tmp.intl_stock, 
                  cf.us_bond = tmp.us_bond, 
                  cf.intl_bond = tmp.intl_bond, 
                  cf.preferred_net = tmp.preferred_net, 
                  cf.convertible_net = tmp.convertible_net, 
                  cf.cash_net = tmp.cash_net, 
                  cf.other_net = tmp.other_net, 
                  cf.unclassified_net = tmp.unclassified_net, 
                  cf.provider = tmp.provider, 
                  cf.aclass = CASE WHEN cf.aclass = '' OR cf.aclass IS NULL THEN tmp.aclass ELSE cf.aclass END, 
                  cf.security_price_adjustment = tmp.security_price_adjustment, 
                  m.securitytype = CASE WHEN tmp.etf = 1 THEN 'ETF' ELSE tmp.securitytype END, 
                  m.last_update = tmp.last_update";
        $adb->pquery($query, array());
    }

    static public function SetPriceAdjustmentFromTDPrices(){
        global $adb;
        $query = "CALL TD_PRICE_FACTOR()";
        $adb->pquery($query, array());
    }

    static public function GetAllIndexes(){
        global $adb;
        $indexes = array();
        $query = "SELECT symbol FROM vtiger_index_list";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $indexes[] = $v['symbol'];
            }
        }

        return $indexes;
    }

    static public function GetAllIndexData(){
        global $adb;
        $indexes = array();
        $query = "SELECT symbol, description, security_symbol FROM vtiger_index_list";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $tmp = array();
                $tmp['symbol'] = $v['symbol'];
                $tmp['description'] = $v['description'];
                $tmp['security_symbol'] = $v['security_symbol'];
                $indexes[] = $tmp;
            }
        }

        return $indexes;
    }

    static public function UpdateIndexPricesWithLatest(){
        global $adb;
        $query = "DROP TABLE IF EXISTS LatestIndexes";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE LatestIndexes
                  SELECT *
                  FROM vtiger_prices_index 
                  WHERE (symbol,date) IN (SELECT symbol, MAX(date) FROM vtiger_prices_index GROUP BY symbol)";
        $adb->pquery($query, array());

        $query = "UPDATE vtiger_modsecurities m 
                  JOIN vtiger_modsecuritiescf cf USING (modsecuritiesid)
                  JOIN vtiger_index_list il USING(security_symbol) 
                  JOIN LatestIndexes li ON li.symbol = il.symbol
                  SET security_price = li.close, closing_price = li.close
                  WHERE securitytype = 'index'";
        $adb->pquery($query, array());
    }

    /**
     * Returns the security price adjustment if one is available, if not it returns 1 by default
     * @param $symbol
     * @return int|string|string[]|null
     * @throws Exception
     */
    static public function GetSecurityPriceAdjustment($symbol){
        global $adb;
        $query = "SELECT security_price_adjustment 
                  FROM vtiger_modsecurities m
                  JOIN vtiger_modsecuritiescf cf USING (modsecuritiesid)
                  WHERE security_symbol = ?";
        $result = $adb->pquery($query, array($symbol));

        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'security_price_adjustment');
        return 1;
    }

    /**
     * Update the securitytype field based on the EOD full security list
     * @param null $symbol
     */
    static public function UpdateSecurityTypesFromEODTable($symbol = null){
        global $adb;
        $where = "";
        $params = array();

        if(!is_null($symbol)){
            $where = " WHERE m.security_symbol = ?";
            $params[] = $symbol;
        }
        $query = "UPDATE vtiger_modsecurities m
                  JOIN vtiger_modsecuritiescf cf USING (modsecuritiesid)
                  JOIN custodian_omniscient.eod_securities eods ON eods.code = m.security_symbol
                  JOIN custodian_omniscient.eod_type_mapping map ON map.eod_type = eods.type
                  SET m.securitytype = map.omni_type {$where}";
        $adb->pquery($query, $params);
    }

    static public function CopyFromInstance($instance_db="live_omniscient", array $symbols){
        global $adb;
        $where = "";
        $params = array();

        if(sizeof($symbols) > 0){
            $questions = generateQuestionMarks($symbols);
            $where .= " WHERE {$instance_db}.security_symbol IN {$questions}";
        }

        $query = "UPDATE vtiger_modsecurities m 
                  JOIN vtiger_modsecuritiescf cf USING (modsecuritiesid)
                  JOIN {$instance_db}.vtiger_modsecurities m2 
                  JOIN {$instance_db}.vtiger_modsecuritiescf cf2 ON m2.modsecuritiesid = cf2.modsecuritiesid 
                  SET m.security_name = m2.security_name, m.sectorpl = m2.sectorpl, m.pay_frequency = m2.pay_frequency, m.securitytype = m2.securitytype,
                      cf.aclass = cf2.aclass, cf.industrypl = cf2.industrypl, cf.summary = cf2.summary, cf.us_stock = cf2.us_stock,
                      cf.intl_stock = cf2.intl_stock, cf.us_bond = cf2.us_bond, cf.intl_bond = cf2.intl_bond, cf.preferred_net = cf2.preferred_net,
                      cf.convertible_net = cf2.convertible_net, cf.cash_net = cf2.cash_net, cf.other_net = cf2.other_net, 
                      cf.unclassified_net = cf2.unclassified_net, cf.morning_star_category = cf2.morning_star_category, 
                      cf.security_sector = cf2.security_sector
                      {$where}";
        $adb->pquery($query, $params);
    }

    /**
     * Return list of securities that should exist but don't
     * @param $symbols
     * @return array|null
     */
    static public function GetMissingSymbolsFromList(array $symbols){
        global $adb;
        $query = "DROP TABLE IF EXISTS SymbolList";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE SymbolList(symbol VARCHAR(250) PRIMARY KEY)";
        $adb->pquery($query, array());

        foreach($symbols AS $k => $v){
            $query = "INSERT INTO SymbolList VALUES(?)";
            $adb->pquery($query, array($v));
        }
        $query = "SELECT symbol FROM SymbolList WHERE symbol NOT IN (SELECT security_symbol FROM vtiger_modsecurities)";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            $symbols = array();
            while($v = $adb->fetchByAssoc($result)){
                $symbols[] = $v['symbol'];
            }
            return $symbols;
        }
        return null;
    }

    static public function GetSecuritySymbolsForAccounts(array $account_numbers){
        global $adb;
        $questions = generateQuestionMarks($account_numbers);
        $params = array();
        $params[] = $account_numbers;
        $params[] = $account_numbers;

        $query = "SELECT security_symbol 
                  FROM vtiger_modsecurities 
                  WHERE security_symbol IN (SELECT security_symbol 
                                            FROM vtiger_positioninformation 
                                            WHERE account_number IN ({$questions}))";
        $adb->pquery($query, $params, true);

        $query = "SELECT symbol FROM SymbolList WHERE symbol NOT IN (SELECT security_symbol FROM vtiger_modsecurities)";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            $symbols = array();
            while($v = $adb->fetchByAssoc($result)){
                $symbols[] = $v['symbol'];
            }
            return $symbols;
        }
        return null;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: rsandnes
 * Date: 2016-07-06
 * Time: 3:04 PM
 */

class cHoldingsReport{
	static public function GenerateTablesByAccounts(array $accounts, $group_primary='aclass', $group_secondary='securitytype', $order_primary='aclass', $order_secondary='securitytype'){
		global $adb;
		include_once("include/utils/omniscientCustom.php");

		$query = "DROP TABLE IF EXISTS holdings_report_positions";
		$adb->pquery($query, array());

		$query = "DROP TABLE IF EXISTS holdings_report_grouped";
		$adb->pquery($query, array());

		$accounts = RemoveDashes($accounts);
		$questions = generateQuestionMarks($accounts);
		$query = "CREATE TEMPORARY TABLE holdings_report_positions
				  SELECT p.*, s.sectorpl, s.securitytype, mcf.* FROM vtiger_positioninformation p
				  JOIN vtiger_positioninformationcf cf USING (positioninformationid)
				  LEFT JOIN vtiger_modsecurities s ON s.security_symbol = p.security_symbol
				  LEFT JOIN vtiger_modsecuritiescf mcf ON s.modsecuritiesid = mcf.modsecuritiesid
				  WHERE REPLACE(account_number, '-', '') IN ({$questions}) AND p.quantity != 0";
		$adb->pquery($query, array($accounts));

		$query = "UPDATE holdings_report_positions SET security_symbol = TRIM(security_symbol)";
		$adb->pquery($query, array());

		$query = "CREATE TEMPORARY TABLE holdings_report_grouped
				  SELECT SUM(p.quantity) AS group_quantity, SUM(p.current_value) AS group_current_value, p.*, s.sectorpl, s.securitytype, mcf.* FROM vtiger_positioninformation p
				  JOIN vtiger_positioninformationcf cf USING (positioninformationid)
				  LEFT JOIN vtiger_modsecurities s ON s.security_symbol = p.security_symbol
				  LEFT JOIN vtiger_modsecuritiescf mcf ON s.modsecuritiesid = mcf.modsecuritiesid
				  WHERE REPLACE(account_number, '-', '') IN ({$questions}) AND p.quantity != 0 GROUP BY security_symbol";
		$adb->pquery($query, array($accounts));

		$query = "UPDATE holdings_report_grouped SET security_symbol = TRIM(security_symbol)";
		$adb->pquery($query, array());
/*
		$query = "DELETE FROM holdings_report_positions WHERE account_number LIKE ('%-%')";
		$adb->pquery($query, array());

		$query = "DELETE FROM holdings_report_grouped WHERE account_number LIKE ('%-%')";
		$adb->pquery($query, array());
*/
		self::GenerateHoldingsTables($group_primary, $group_secondary, $order_primary, $order_secondary);
	}

	private static function GenerateHoldingsTables($group_primary='aclass', $group_secondary='securitytype'){
		global $adb;

		$query = "SELECT 1 FROM holdings_report_positions LIMIT 1";
		$result = $adb->pquery($query, array());
		if($result !== FALSE) {
			$query = "DROP TABLE IF EXISTS holdings_global_totals";
			$adb->pquery($query, array());
			$query = "DROP TABLE IF EXISTS holdings_grouped_totals";
			$adb->pquery($query, array());
			$query = "DROP TABLE IF EXISTS holdings_report_individual";
			$adb->pquery($query, array());
			$query = "DROP TABLE IF EXISTS holdings_grouped_primary";
			$adb->pquery($query, array());
			$query = "DROP TABLE IF EXISTS holdings_grouped_secondary";
			$adb->pquery($query, array());
			$query = "DROP TABLE IF EXISTS positions_tmp";
			$adb->pquery($query, array());
			$query = "DROP TABLE IF EXISTS weighted_positions";
			$adb->pquery($query, array());
			$query = "DROP TABLE IF EXISTS weighted_positions_grouped";
			$adb->pquery($query, array());

			$query = "CREATE TEMPORARY TABLE holdings_global_totals
					  SELECT SUM(current_value) AS global_total FROM holdings_report_positions";
			$adb->pquery($query, array());

			$query = "UPDATE holdings_report_positions
					  SET weight = current_value/(SELECT global_total FROM holdings_global_totals)*100";
			$adb->pquery($query, array());

			$query = "UPDATE holdings_report_positions SET securitytype = type_override WHERE type_override != ''";
			$adb->pquery($query, array());

			$query = "UPDATE holdings_report_grouped
					  SET weight = group_current_value/(SELECT global_total FROM holdings_global_totals)*100, current_value = group_current_value, quantity = group_quantity";
			$adb->pquery($query, array());

			$query = "UPDATE holdings_report_grouped SET securitytype = type_override WHERE type_override != ''";
			$adb->pquery($query, array());

			#Group Primary
			$query = "CREATE TEMPORARY TABLE holdings_grouped_primary
					  SELECT SUM(current_value) AS group_total, SUM(weight) AS group_weight, s.securitytype, cf.aclass
					  FROM holdings_report_positions r
					  LEFT JOIN vtiger_modsecurities s ON s.security_symbol = r.security_symbol
					  LEFT JOIN vtiger_modsecuritiescf cf ON s.modsecuritiesid = cf.modsecuritiesid
					  GROUP BY {$group_primary}";
			$adb->pquery($query, array());

			#Group Secondary
			$query = "CREATE TEMPORARY TABLE holdings_grouped_secondary
					  SELECT SUM(current_value) AS group_total, SUM(weight) AS group_weight, s.securitytype, cf.aclass
					  FROM holdings_report_positions r
					  LEFT JOIN vtiger_modsecurities s ON s.security_symbol = r.security_symbol
					  LEFT JOIN vtiger_modsecuritiescf cf ON s.modsecuritiesid = cf.modsecuritiesid
					  GROUP BY {$group_primary}, {$group_secondary}";
			$adb->pquery($query, array());

			$query = "CREATE TEMPORARY TABLE weighted_positions
						SELECT pos.*, 
							   PercentageWeight(us_stock, current_value, global_total) AS us_stock_weight,
							PercentageWeight(intl_stock, current_value, global_total) AS intl_stock_weight,
							PercentageWeight(us_bond, current_value, global_total) AS us_bond_weight,
							PercentageWeight(intl_bond, current_value, global_total) AS intl_bond_weight,
							PercentageWeight(preferred_net, current_value, global_total) AS preferred_net_weight,
							PercentageWeight(convertible_net, current_value, global_total) AS convertible_net_weight,
							PercentageWeight(cash_net, current_value, global_total) AS cash_net_weight,
							PercentageWeight(other_net, current_value, global_total) AS other_net_weight,
							PercentageWeight(unclassified_net, current_value, global_total) AS unclassified_net_weight							
							FROM 
							(SELECT (SELECT * FROM holdings_global_totals) AS global_total, p.*, 
							PercentageValue(us_stock, current_value) AS us_stock_value,
							PercentageValue(intl_stock, current_value) AS intl_stock_value,
							PercentageValue(us_bond, current_value) AS us_bond_value,
							PercentageValue(intl_bond, current_value) AS intl_bond_value,
							PercentageValue(preferred_net, current_value) AS preferred_net_value,
							PercentageValue(convertible_net, current_value) AS convertible_net_value,
							PercentageValue(cash_net, current_value) AS cash_net_value,
							PercentageValue(other_net, current_value) AS other_net_value,
							PercentageValue(unclassified_net, current_value) AS unclassified_net_value
							FROM holdings_report_positions p) AS pos;";
			$adb->pquery($query, array());

			$query = "CREATE TEMPORARY TABLE weighted_positions_grouped
						SELECT pos.*, 
							   PercentageWeight(us_stock, group_current_value, global_total) AS us_stock_weight,
							PercentageWeight(intl_stock, group_current_value, global_total) AS intl_stock_weight,
							PercentageWeight(us_bond, group_current_value, global_total) AS us_bond_weight,
							PercentageWeight(intl_bond, group_current_value, global_total) AS intl_bond_weight,
							PercentageWeight(preferred_net, group_current_value, global_total) AS preferred_net_weight,
							PercentageWeight(convertible_net, group_current_value, global_total) AS convertible_net_weight,
							PercentageWeight(cash_net, group_current_value, global_total) AS cash_net_weight,
							PercentageWeight(other_net, group_current_value, global_total) AS other_net_weight,
							PercentageWeight(unclassified_net, group_current_value, global_total) AS unclassified_net_weight							
							FROM 
							(SELECT (SELECT * FROM holdings_global_totals) AS global_total, p.*, 
							PercentageValue(us_stock, group_current_value) AS us_stock_value,
							PercentageValue(intl_stock, group_current_value) AS intl_stock_value,
							PercentageValue(us_bond, group_current_value) AS us_bond_value,
							PercentageValue(intl_bond, group_current_value) AS intl_bond_value,
							PercentageValue(preferred_net, group_current_value) AS preferred_net_value,
							PercentageValue(convertible_net, group_current_value) AS convertible_net_value,
							PercentageValue(cash_net, group_current_value) AS cash_net_value,
							PercentageValue(other_net, group_current_value) AS other_net_value,
							PercentageValue(unclassified_net, group_current_value) AS unclassified_net_value
							FROM holdings_report_grouped p) AS pos;";
			$adb->pquery($query, array());
		}else
			return 0;
	}
	
	public static function GetGlobalTotal(){
		global $adb;
		$query = "SELECT * FROM holdings_global_totals";
		$result = $adb->pquery($query, array());
		if($adb->num_rows($result) > 0){
			foreach($result AS $k => $v){
				$p = $v;
			}
			return $p;
		}
		return 0;		
	}
	
	public static function GetAllPositions($order_primary='aclass', $order_secondary='securitytype'){
		global $adb;

		#Individual Positions
		$query = "SELECT * FROM holdings_report_positions r
					  ORDER BY {$order_primary}, {$order_secondary}";
		$result = $adb->pquery($query, array());
		if($adb->num_rows($result) > 0){
			foreach($result AS $k => $v){
				$p[] = $v;
			}
			return $p;
		}
		return 0;
	}
	
	public static function GetGroupedPrimary(){
		global $adb;
		$query = "SELECT * FROM holdings_grouped_primary";
		$result = $adb->pquery($query, array());
		if($adb->num_rows($result) > 0){
			foreach($result AS $k => $v){
				$p[] = $v;
			}
			return $p;
		}
		return 0;
	}
	
	public static function GetGroupedSecondary(){
		global $adb;
		$query = "SELECT * FROM holdings_grouped_secondary";
		$result = $adb->pquery($query, array());
		if($adb->num_rows($result) > 0){
			foreach($result AS $k => $v){
				$p[] = $v;
			}
			return $p;
		}
		return 0;
	}

	public static function GetWeightedPositions($grouped=false){
		global $adb;
		if($grouped)
			$g = "_grouped";
		$query = "SELECT * FROM weighted_positions{$g}";
		$result = $adb->pquery($query, array());
		if($adb->num_rows($result) > 0){
			foreach($result AS $k => $v){
				$p[] = $v;
			}
			return $p;
		}
		return 0;
	}

	public static function CategorizePositions($positions){
	    foreach($positions AS $k => $v){
	        if($v['us_stock'] + $v['intl_stock'] == 100)
	            $positions[$k]['category'] = "Equity";
            else if($v['us_bond'] + $v['intl_bond'] + $v['preferred_net'] == 100)
                $positions[$k]['category'] = "Fixed Income";
            else if($v['cash_net'] == 100)
                $positions[$k]['category'] = "Cash";
            else if($v['convertible_net'] + $v['other_net'] == 100)
                $positions[$k]['category'] = 'Other';
            else if($v['unclassified_net'] == 100)
                $positions[$k]['category'] = 'Unclassified';
            else
                $positions[$k]['category'] = 'Blended';
        }
        return $positions;
    }

    public static function TotalCategories($positions, &$total_weight){
        $r = self::GetGlobalTotal();
        $global_total = $r['global_total'];
        $category = array();

        foreach($positions AS $k => $v){
            $category[$v['category']]['total'] += $v['current_value'];
        }
        foreach($category AS $k => $v){
            $weight = $v['total'] / $global_total * 100;
            $category[$k]['weight'] = $weight;
            $total_weight+=$weight;
        }
        return $category;
    }

    public static function TotalAssetClass($positions){
        $tmp = array();
        foreach($positions AS $k => $v){
            $tmp['equities'] += $v['us_stock_value'] + $v['intl_stock_value'];
            $tmp['fixed'] += $v['us_bond_value'] + $v['intl_bond_value'] + $v['preferred_net_value'];
            $tmp['cash'] += $v['cash_net_value'];
            $tmp['other'] +=  $v['convertible_net_value'] + $v['other_net_value'];
            $tmp['unclassified'] += $v['unclassified_net_value'];
        }
        return $tmp;
    }

	/**
	 * Takes the individual asset classes and totals them up such as us_stock_value, intl_stock_value, etc
	 * @param $positions
	 */
    public static function TotalIndividualizedAssetClass($positions){
    	$tmp = array();
		foreach($positions AS $k => $v){
			$tmp['us_stock_value'] += $v['us_stock_value'];
			$tmp['intl_stock_value'] += $v['intl_stock_value'];
			$tmp['us_bond_value'] += $v['us_bond_value'];
			$tmp['intl_bond_value'] += $v['intl_bond_value'];
			$tmp['preferred_net_value'] += $v['preferred_net_value'];
			$tmp['cash_net_value'] += $v['cash_net_value'];
			$tmp['convertible_net_value'] += $v['convertible_net_value'];
			$tmp['other_net_value'] += $v['other_net_value'];
			$tmp['unclassified_net_value'] += $v['unclassified_net_value'];
		}
		return $tmp;
	}

	/**
	 * Takes the a key/value pairing and the total to calculate it against.  Returns a new array of weights with the keys the same as the passed in array
	 * @param $ac
	 * @param $global_total
	 * @return array
	 */
	public static function GetACWeights($ac, $global_total){
		$tmp = array();
		foreach($ac AS $k => $v){
			$tmp[$k] = $v/$global_total['global_total'] * 100;
		}
		return $tmp;
	}

	public static function CreatePHPGeneratorCompatiblePieFromPositions($positions){
		$pie = array();
		$ac = self::TotalAssetClass($positions);

		if($ac['equities'] > 0)
			$pie['Equity'] = $ac['equities'];
		if($ac['fixed'] > 0)
			$pie['Fixed Income'] = $ac['fixed'];
		if($ac['cash'] > 0)
			$pie['Cash and Equivalent'] = $ac['cash'];
		if($ac['other'] > 0)
			$pie['Other'] = $ac['other'];
		if($ac['unclassified'] > 0)
			$pie['Unclassified'] = $ac['unclassified'];

		return $pie;
	}

	public static function CreatePieFromAssetClassGrouped(array $account_numbers){
	    global $adb;

        $questions = generateQuestionMarks($account_numbers);
	    $query = "CALL POSITIONS_BY_ASSET_CLASS(\"{$questions}\");";
	    $adb->pquery($query, array($account_numbers));

	    $query = "SELECT * FROM positions_by_asset_class";
	    $result = $adb->pquery($query, array());

	    if($adb->num_rows($result) > 0){
	        $pie = array();
            while($v = $adb->fetchByAssoc($result)){
                if($v['current_value'] != 0) {
                    $pie[] = array("title"=>$v['base_asset_class'],
                                   "value"=>number_format($v['current_value'], 2, '.', ''),
                                   "weight"=>$v['weight'],
                                   "color"=>PortfolioInformation_Module_Model::GetChartColorForTitle($v['base_asset_class']));
                }
            }
            return $pie;
        }
        return 0;
    }

	public static function CreatePieFromPositions($positions){
		$pie = array();
		$tmp = self::TotalAssetClass($positions);

		$pie[] = array("title"=>"Equity",
			"value"=>number_format($tmp['equities'], 2, '.', ''),
			"color"=>PortfolioInformation_Module_Model::GetChartColorForTitle("equities"));
		$pie[] = array("title"=>"Fixed Income",
			"value"=>number_format($tmp['fixed'], 2, '.', ''),
			"color"=>PortfolioInformation_Module_Model::GetChartColorForTitle("fixed income"));
		$pie[] = array("title"=>"Cash",
			"value"=>number_format($tmp['cash'], 2, '.', ''),
		"color"=>PortfolioInformation_Module_Model::GetChartColorForTitle("cash"));
		$pie[] = array("title"=>"Other",
			"value"=>number_format($tmp['other'], 2, '.', ''),
		"color"=>PortfolioInformation_Module_Model::GetChartColorForTitle("other"));
		$pie[] = array("title"=>"Unclassified",
			"value"=>number_format($tmp['unclassified'], 2, '.', ''),
		"color"=>PortfolioInformation_Module_Model::GetChartColorForTitle("unclassified"));

		return $pie;
	}
}
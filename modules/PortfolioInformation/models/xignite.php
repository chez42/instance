<?php
/**
 * Created by PhpStorm.
 * User: rsandnes
 * Date: 2016-05-09
 * Time: 3:51 PM
 */

require_once("libraries/xignite/companies.php");

class PortfolioInformation_xignite_Model extends Vtiger_Module{
	static public function GetAndWriteSectorToMappingTable(){
		global $adb;

		$xignite =new XigniteCompanies();
		$data = $xignite->GetAllSectors();

		$query = "INSERT INTO sector_mapping (sector_code, sector_name, industry_code, industry_name) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE sector_name = VALUES(sector_name), industry_name = VALUES(industry_name)";
		foreach($data->Sectors AS $sk => $sv){
			$sector_code = $sv->Code;
			$sector_name = $sv->Name;
			foreach($sv->Industries AS $ik => $iv) {
				$industry_code = $iv->Code;
				$industry_name = $iv->Name;
#				echo $query;
				$adb->pquery($query, array($sector_code, $sector_name, $industry_code, $industry_name));
			}
		}
		echo 'done';
	}

	static public function MapSectorInformationIntoCRM(){
		global $adb;
		$query = "UPDATE vtiger_modsecurities p
				  JOIN vtiger_modsecuritiescf cf USING (modsecuritiesid)
				  LEFT JOIN xignite_nasdaq nas ON nas.`Companies Symbol` = p.security_symbol
				  JOIN sector_mapping sm ON sm.sector_code = nas.`Companies Sector` AND sm.industry_code = nas.`Companies Industry`
				  SET p.sectorpl = sm.sector_name, cf.industrypl = sm.industry_name, cf.cusip = nas.`Companies CUSIP`";
		$adb->pquery($query, array());
		$query = "UPDATE vtiger_modsecurities p
				  JOIN vtiger_modsecuritiescf cf USING (modsecuritiesid)
				  LEFT JOIN xignite_nyse nse ON nse.`Companies Symbol` = p.security_symbol
				  JOIN sector_mapping sm ON sm.sector_code = nse.`Companies Sector` AND sm.industry_code = nse.`Companies Industry`
				  SET p.sectorpl = sm.sector_name, cf.industrypl = sm.industry_name, cf.cusip = nse.`Companies CUSIP`";
		$adb->pquery($query, array());
	}

	static public function PopulateUnclassified($limit = null){
		global $adb;
		if($limit)
			$limit = " LIMIT {$limit}";
		$query = "SELECT security_symbol FROM vtiger_modsecurities s
			 	  JOIN vtiger_modsecuritiescf cf USING (modsecuritiesid)
			 	  JOIN vtiger_crmentity e ON e.crmid = s.modsecuritiesid
				  WHERE ignore_auto_update IN (0) 
				  AND (us_stock + intl_stock + intl_bond + us_bond + preferred_net + convertible_net + cash_net + other_net = 0)
				  AND security_symbol REGEXP '^[A-Za-z ]+$' 
				  AND e.modifiedtime < CURRENT_DATE()
				  AND e.deleted = 0
				  ORDER BY modsecuritiesid ASC {$limit}";
		$result = $adb->pquery($query, array());
		if($adb->num_rows($result) > 0){
			while($v = $adb->fetchByAssoc($result)){
				ModSecurities_Module_Model::FillWithYQLOrXigniteData($v['security_symbol']);
			}
		}
	}

	static public function GetSymbolInformation($symbol){
		$xignite = new XigniteCompanies();
		$result = $xignite->GetFundamentals("Symbol", $symbol);
		if($result[0]->Outcome === "Success")
			return $result[0];

		$result = $xignite->GetFundProfile("Symbol", $symbol);
		return $result;
	}

	static public function GetFundProfileInformation($symbol){
		$xignite = new XigniteCompanies();
		$result = $xignite->GetFundProfile("Symbol", $symbol);
		return $result;
	}

	static public function GetFundBenchmarkInformation(array $symbols){
	    $s = implode(",", $symbols);
	    $xignite = new XigniteCompanies();
	    $result = $xignite->GetFundBenchmarkInformation("Symbol", $s);
	    return $result;
    }

	static public function GetFundAssetAllocation($symbol){
		$xignite = new XigniteCompanies();
		$result = $xignite->GetFundAssetAllocation("Symbol", $symbol);
		return $result;
	}

    static public function GetFundsAssetAllocation($symbol){
        $xignite = new XigniteCompanies();
        $result = $xignite->GetFundsAssetAllocation("Symbol", $symbol);
        return $result;
    }


    static public function UpdateSyncStatus($symbol, $status){
        global $adb;
        $query = "UPDATE vtiger_xignite_sync SET status = ?, last_check = NOW() WHERE symbol = ?";
        $adb->pquery($query, array($status, $symbol));
    }

	static public function UpdateSyncTableWithLatestSecurities(){
	    global $adb;

	    $query = "insert into vtiger_xignite_sync (symbol)
                  SELECT security_symbol FROM vtiger_modsecurities WHERE security_symbol NOT IN (SELECT symbol from vtiger_xignite_sync)
                  ON DUPLICATE KEY UPDATE id=id";
	    $adb->pquery($query, array());
    }

    /**
     * Get the first <x> number of symbols that haven't been updated since the given date.  If no gate, it only cares about null values
     * @param int $limit
     * @param null $date
     */
	static public function GetXigniteSyncSymbols($limit=5, $date=null){
	    global $adb;
	    if(!$date)
	        $and = " AND last_check IS NULL ";
        else {
            $and = " AND last_check < ?";
            $params[] = $date;
        }

	    $query = "SELECT symbol
                  FROM vtiger_xignite_sync 
                  WHERE symbol != '' 
                  {$and}
                  LIMIT {$limit}";

	    $result = $adb->pquery($query, $params);
	    if($adb->num_rows($result) > 0){
	        while($v = $adb->fetchByAssoc($result)){
	            $symbols[] = $v['symbol'];
            }
            return $symbols;
        }
        return 0;
    }
}
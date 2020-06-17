<?php
/**
 * Created by PhpStorm.
 * User: rsandnes
 * Date: 2016-05-09
 * Time: 3:51 PM
 */

class PortfolioInformation_CloudInteractions_Model extends Vtiger_Module{
	static $tenant = "custodian_omniscient";

	static public function GetLatestDates(){
		global $adb;
		$query = "SELECT max(trade_date) AS date, custodian
				  FROM vtiger_transactions t
				  JOIN vtiger_transactionscf cf USING (transactionsid)
				  WHERE custodian NOT IN ('omniscient', '')";

		$result = $adb->pquery($query, array());
		if($adb->num_rows($result) > 0){
			foreach($result AS $k => $v) {
				$tmp[] = array("custodian" => $v['custodian'],
					 		   "date" => $v['date']);
			}
			return $tmp;
		}
	}

	static public function GetFilesRunByCustodian($number_of_files = 25){
		global $adb;

		$tenant = self::$tenant;
		$query = "SELECT * FROM {$tenant}.files_run ORDER BY id DESC LIMIT {$number_of_files}";
		$result = $adb->pquery($query);
		if($adb->num_rows($result) > 0) {
			$r = array();
			foreach ($result AS $k => $v) {
				$r[] = $v;
			}
			return $r;
		}
			return 0;
	}
}
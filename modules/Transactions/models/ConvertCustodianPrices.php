<?php
/**
 * Created by PhpStorm.
 * User: rsandnes
 * Date: 2016-03-29
 * Time: 1:48 PM
 */
class Transactions_ConvertCustodianPrices_Model extends Vtiger_Module_Model{
	static $tenant = "custodian_omniscient";

	static public function GetSymbolMapping($custodian_symbol, $custodian){
		global $adb;
		$query = "SELECT cusip FROM vtiger_custodian_symbol_mapping WHERE custodian_symbol = ? AND custodian = ?";
		$result = $adb->pquery($query, array($custodian_symbol, $custodian));
		if($adb->num_rows($result) > 0)
			return $adb->query_result($result, 0, "cusip");
		return 0;
	}

	/**
	 * Comparitor is used with the $date variable.  >= will select all transactions >= the given date.  = is the default pulling only for the date provided
	 * @param $custodian
	 * @param $date
	 * @param $comparitor
	 * @return array|int
	 */
	static public function GetPrices($custodian, $date, $comparitor="="){
		global $adb;

		$tenant = self::$tenant;
		$comparitor = str_replace("&gt;", ">", $comparitor);
		$query = "SELECT * FROM {$tenant}.custodian_prices_{$custodian} WHERE date {$comparitor} ?";
		$result = $adb->pquery($query, array($date));
		if($adb->num_rows($result) > 0){
			foreach($result AS $k => $v){
				$tmp[] = $v;
			}
			return $tmp;
		}
		return 0;
	}

	/**
	 * Each custodian may use different field names so we need to map it to be compatible with our setup
	 * @param $custodian
	 * @param $transaction_type_map
	 * @param $cloudData
	 * @param $data
	 */
	static private function MapCloudToModuleData(&$custodian, &$cloudData, &$data){
		switch($custodian){
			case "td":
			case "millenium":
				$data['symbol'] = $cloudData['symbol'];
				$data['price'] = $cloudData['price'];
				$data['date'] =$cloudData['date'];
				break;
		}
	}

	static public function ConvertCustodian($custodian, $date, $comparitor){
		self::CloudToModuleConversion($custodian, $date, $comparitor);
	}

	/**
	 * Adds the symbol to the price table when it is empty
	 */
	static public function AddSymbolToPricingTable(){
		global $adb;
		$query = "UPDATE vtiger_pc_security_prices pr
				  JOIN vtiger_securities s ON s.security_id = pr.security_id
		   		  SET pr.symbol = s.security_symbol
				  WHERE pr.symbol IS null";
		$adb->pquery($query, array());
	}

	/**
	 * Write mapped data into our pricing table
	 * @param $data
	 */
	static private function WriteCustodianDataToDatabase($data){
		global $adb;
		$query = "INSERT INTO vtiger_custodian_prices (symbol, trade_date, price) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE price=VALUES(price)";
		$adb->pquery($query, array($data['symbol'], $data['date'], $data['price']));
	}

	static private function CloudToModuleConversion($custodian, $date, $comparitor){
		$prices = self::GetPrices($custodian, $date, $comparitor);
		$count = 1;
		$data = array();
		if($prices){
			set_time_limit ( 0 );
			foreach($prices AS $k => $v){
				echo "START OF LOOP: " . memory_get_peak_usage() . " - Count: " . $count . "<br />";
				if(strlen($v['symbol']) == 0) {
					echo "SIZE: " . strlen($v['symbol']);
					echo " -- SYMBOL: " . $v['symbol'];exit;
					$v['symbol'] = self::GetSymbolMapping($v['symbol'], $custodian);
				}
				if(strlen($v['symbol']) > 0){
					self::MapCloudToModuleData($custodian, $v, $data);
					self::WriteCustodianDataToDatabase($data);
					$count++;
				}else{
					echo "<span style='text-color:red;'>Error converting the following: ";
					print_r($v);
					echo "</span>" . PHP_EOL;
				}
			}
		}
		echo "<strong>Conversion finished</strong>" . PHP_EOL;
	}
}
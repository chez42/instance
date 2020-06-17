<?php
include_once("include/utils/cron/cPortfolioCenter.php");

class cPricingAccess{
	private $pc;

	public function __construct() {
		$this->pc = new cPortfolioCenter();
	}

	static public function CreateCustomPrice($security_id, $data_set_id, $price, $date){
		global $adb;
		$query = "INSERT INTO vtiger_pc_custom_prices (data_set_id, security_id, price_date, price, factor, entry_method_id, last_modified_date, origination_id, last_modified_user_id)
                                               VALUES (?, ?, ?, ?, 0, 20, NOW(), 33, 1)
                  ON DUPLICATE KEY UPDATE price = VALUES(price), last_modified_date = VALUES(last_modified_date)";
		$adb->pquery($query, array($data_set_id, $security_id, $date, $price));
	}

	static public function GetLatestPriceBySymbol($symbol){
		global $adb;
		if(strlen($symbol) < 3)
			return 0;
		$query = "SELECT price FROM vtiger_pc_security_prices pr
                  JOIN vtiger_securities s ON s.security_id = pr.security_id
                  WHERE price_date <= NOW()
                  AND s.security_symbol = '{$symbol}'
                  ORDER BY price_date DESC LIMIT 1";
		$result = $adb->pquery($query, array());
		if($adb->num_rows($result) > 0)
			return $adb->query_result($result, 0, 'price');
		return 0;
	}

	/**Convert the sql date to a proper format*/
	public function ConvertDate($date)
	{
		$time = strtotime($date);
		$time = date('Y-m-d 00:00:00', $time);
		return $time;
	}

	/**
	 * Get the latest prices  ~45 second query
	 * @global type $adb
	 * @return type
	 */
	public function GetLatestPrices(){
		global $adb;
		$query = "SELECT price, security_id FROM vtiger_pc_security_prices 
                  WHERE price_date <= NOW()
                  GROUP BY security_id
                  ORDER BY price_date, security_id DESC";
		$result = $adb->pquery($query);

		return $result;
	}

	public function UpdateSecurityPriceBySecurityID($security_id){
		global $adb;
		$database = CPortfolioCenter::GetDatabase();//"PortfolioCenter";
		$query = "SELECT * FROM [{$database}].[dbo].SecurityPrices WHERE SecurityID IN({$security_id})";

		$results = mssql_query($query);
		$row_count = mssql_num_rows($results);
		$count = 0;
		if($results){
			$query = "INSERT INTO vtiger_pc_security_prices (security_price_id, data_set_id, security_id, price_date, price, factor, entry_method_id, last_modified_date, origination_id, last_modified_user_id)
                      VALUES ";
			while($row = mssql_fetch_array($results))
			{
				$price_id = $row['SecurityPriceID'];
				$dataset_id = $row['DataSetID'];
				$security_id = $row['SecurityID'];
				$price_date = $this->ConvertDate($row['PriceDate']);
				$price = $row['Price'];
				$factor = $row['Factor'];
				$entry_method = $row['EntryMethodID'];
				$last_modified_date = $this->ConvertDate($row['LastModifiedDate']);
				$origination_id = $row['OriginationID'];
				$last_modified_user = $row['LastModifiedUserID'];

				$count++;
				$query .= "('{$price_id}',
                            '{$dataset_id}',
                            '{$security_id}',
                            '{$price_date}',
                            '{$price}',
                            '{$factor}',
                            '{$entry_method}',
                            '{$last_modified_date}',
                            '{$origination_id}',
                            '{$last_modified_user}')";
				if($count < mssql_num_rows($results))
					$query .= ", ";
			}
			$query .= " ON DUPLICATE KEY UPDATE security_price_id=VALUES(security_price_id), price=VALUES(price), data_set_id=VALUES(data_set_id), price_date=VALUES(price_date), last_modified_date=VALUES(last_modified_date)";
			$adb->pquery($query, array());
		}
	}

	public function PullSecurityPrice($security_name=null){
		global $adb;

		if(strlen($security_name) < 2)
			return "Invalid Security Name";
		if(!$this->pc->connect())
			return "Error Connecting to PC";
		include_once("include/utils/cron/cSecuritiesAccess.php");
		$sec = new cSecuritiesAccess();
		$info = $sec->GetSecuritiesFromPCBySymbol($security_name);
        $ids = '';

		foreach($info AS $k => $v){
			$ids .= $v['SecurityID'] . ',';
		}
		$ids = rtrim($ids, ',');

		$database = CPortfolioCenter::GetDatabase();//"PortfolioCenter";
		$query = "SELECT * FROM [{$database}].[dbo].SecurityPrices WHERE SecurityID IN({$ids})";

		$results = mssql_query($query);
		$row_count = mssql_num_rows($results);
		if($results){
			$query = "INSERT INTO vtiger_pc_security_prices (security_price_id, data_set_id, security_id, price_date, price, factor, entry_method_id, last_modified_date, origination_id, last_modified_user_id)
                      VALUES ";
			while($row = mssql_fetch_array($results))
			{
				//            echo '.';
				$price_id = $row['SecurityPriceID'];
				$dataset_id = $row['DataSetID'];
				$security_id = $row['SecurityID'];
				$price_date = $this->ConvertDate($row['PriceDate']);
				$price = $row['Price'];
				$factor = $row['Factor'];
				$entry_method = $row['EntryMethodID'];
				$last_modified_date = $this->ConvertDate($row['LastModifiedDate']);
				$origination_id = $row['OriginationID'];
				$last_modified_user = $row['LastModifiedUserID'];

				$count++;
				$query .= "('{$price_id}',
                            '{$dataset_id}',
                            '{$security_id}',
                            '{$price_date}',
                            '{$price}',
                            '{$factor}',
                            '{$entry_method}',
                            '{$last_modified_date}',
                            '{$origination_id}',
                            '{$last_modified_user}')";
				if($count < mssql_num_rows($results))
					$query .= ", ";
			}
			$query .= " ON DUPLICATE KEY UPDATE security_price_id=VALUES(security_price_id), price=VALUES(price), data_set_id=VALUES(data_set_id), price_date=VALUES(price_date), last_modified_date=VALUES(last_modified_date)";
			$adb->pquery($query, array());
		}

		return("Finished Writing {$row_count} Rows To Prices Table<br />\r\n");
	}

	/**
	 * Returns the count for the number of prices in the pricing table for the given date
	 * @param null $date
	 * @return mixed|string
	 * @throws Exception
	 */
	public function GetNumberOfPricesForDate($date=null){
		global $adb;

		if(strlen($date) < 2)
			$date = date('Y-m-d', strtotime("today -1 Weekday"));

		$query = "SELECT COUNT(*) as count FROM vtiger_pc_security_prices WHERE price_date = ?";
		$result = $adb->pquery($query, array($date));
		return $adb->query_result($result, 0, 'count');
	}

	/**
	 * Update prices starting at the given date.  If none entered, it is today -2 weeks
	 * @param type $date
	 */
	public function UpdatePrices($date=null){
		global $adb;

		if(strlen($date) < 2)
			$date = date('Y-m-d', strtotime("-1 weeks"));
		if(!$this->pc->connect())
			return "Error Connecting to PC";

		$database = CPortfolioCenter::GetDatabase();//"PortfolioCenter";
		$query = "SELECT * FROM [{$database}].[dbo].SecurityPrices WHERE PriceDate >= '{$date}'";

		$results = mssql_query($query);
		if($results){
			$query = "INSERT INTO vtiger_pc_security_prices (security_price_id, data_set_id, security_id, price_date, price, factor, entry_method_id, last_modified_date, origination_id, last_modified_user_id)
                      VALUES ";
			while($row = mssql_fetch_array($results))
			{
				//            echo '.';
				$price_id = $row['SecurityPriceID'];
				$dataset_id = $row['DataSetID'];
				$security_id = $row['SecurityID'];
				$price_date = $this->ConvertDate($row['PriceDate']);
				$price = $row['Price'];
				$factor = $row['Factor'];
				$entry_method = $row['EntryMethodID'];
				$last_modified_date = $this->ConvertDate($row['LastModifiedDate']);
				$origination_id = $row['OriginationID'];
				$last_modified_user = $row['LastModifiedUserID'];

				$count++;
				$query .= "('{$price_id}',
                            '{$dataset_id}',
                            '{$security_id}',
                            '{$price_date}',
                            '{$price}',
                            '{$factor}',
                            '{$entry_method}',
                            '{$last_modified_date}',
                            '{$origination_id}',
                            '{$last_modified_user}')";
				if($count < mssql_num_rows($results))
					$query .= ", ";
				//        echo "SECURITY: {$security_id} -- PRICE: {$price} -- DATE: {$price_date}<br />";
				//$adb->pquery($query, array($advisor_id, $advisor_name, $description, $advisor_id, $advisor_name, $description));
				/*                $query = "INSERT INTO vtiger_pc_security_prices (security_price_id, data_set_id, security_id, price_date, price, factor, entry_method_id, last_modified_date, origination_id, last_modified_user_id)
										  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
										  ON DUPLICATE KEY UPDATE price_date=VALUES(price_date), last_modified_date=VALUES(last_modified_date)";
				//                $adb->pquery($query, array($price_id, $dataset_id, $security_id, $price_date, $price, $factor, $entry_method, $last_modified_date, $origination_id, $last_modified_user));*/

			}
			$query .= " ON DUPLICATE KEY UPDATE security_price_id=VALUES(security_price_id), data_set_id=VALUES(data_set_id), price_date=VALUES(price_date), last_modified_date=VALUES(last_modified_date)";
			$adb->pquery($query, array());
		}

		return("Finished Writing To Prices Table<br />\r\n");
	}
}
/*
        
require_once('include/DatabaseUtil.php');
require_once("modules/Portfolios/classes/cTransactions.php");
require_once("modules/Portfolios/classes/Portfolios.php");
    
$myServer = "lanserver2n";
$myUser = "syncuser";
$myPass = "Consec11";
$myDB = "PortfolioCenter";
set_time_limit(0);
//Convert the sql date to a proper format
function ConvertDate($date)
{   
    $time = strtotime($date);
    $time = date('Y-m-d 00:00:00', $time);
    return $time;
}   
    
global $myServer; 
global $myUser;
global $myPass;
global $myDB;
global $adb;
        
        
        //connection to the database
$dbhandle = mssql_connect($myServer, $myUser, $myPass);// or die("Couldn't connect to SQL Server on $myServer");
if(!$dbhandle)
{
    echo "NO HANDLE TO PORTFOLIO CENTER!<br />";
}       
else    
{
    $date = date('Y-m-d', strtotime("-2 weeks"));
    $query = "SELECT * FROM SecurityPrices WHERE PriceDate > '{$date}'";
echo $query;
    $results = mssql_query($query);
    if($results)
    while($row = mssql_fetch_array($results))
    {
        $price_id = $row['SecurityPriceID'];
        $dataset_id = $row['DataSetID'];
        $security_id = $row['SecurityID'];
        $price_date = ConvertDate($row['PriceDate']);
        $price = $row['Price'];
        $factor = $row['Factor'];
        $entry_method = $row['EntryMethodID'];
        $last_modified_date = ConvertDate($row['LastModifiedDate']);
        $origination_id = $row['OriginationID'];
        $last_modified_user = $row['LastModifiedUserID'];
        
//        echo "SECURITY: {$security_id} -- PRICE: {$price} -- DATE: {$price_date}<br />";
        //$adb->pquery($query, array($advisor_id, $advisor_name, $description, $advisor_id, $advisor_name, $description));
        $query = "INSERT INTO vtiger_pc_security_prices (security_price_id, data_set_id, security_id, price_date, price, factor, entry_method_id, last_modified_date, origination_id, last_modified_user_id)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE price_date=?, last_modified_date=?";
        $adb->pquery($query, array($price_id, $dataset_id, $security_id, $price_date, $price, $factor, $entry_method, $last_modified_date, $origination_id, $last_modified_user,
                                   $price_date, $last_modified_date));

    }
    echo "Finished Writing To Prices Table<br />";

}

echo "MAIN CRON FINISHED\n\r";
set_time_limit(120);

*/

?>
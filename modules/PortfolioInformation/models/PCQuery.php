<?php
require_once("include/utils/cron/cPortfolioCenter.php");

class PortfolioInformation_PCQuery_Model extends Vtiger_Module {
    private $pc;
    private $datasets;

    public function __construct() {
        $this->pc = new cPortfolioCenter();
        $this->datasets = cPortfolioCenter::GetDatasets();//"1, 28";
    }
    
    public function GetDataSets(){
        if(!$this->pc->connect())//Try connecting
            return "Error Connecting to PC";

        $query = "SELECT DataSetID, DataSetName
                  FROM Datasets ORDER BY DataSetID";
        $result = mssql_query($query);
        $info = array();//Holds all row info
        if(mssql_num_rows($result) > 0)
            while($row = mssql_fetch_array($result))
                $info[] = $row;
        return $info;
    }
    
    public function GetPortfolioList(){
        if(!$this->pc->connect())//Try connecting
            return "Error Connecting to PC";

        $query = "SELECT PortfolioID, AccountNumber
                  FROM Portfolios p 
                  WHERE p.DataSetID IN ({$this->datasets}) 
                  AND PortfolioTypeID = 16 
                  AND ClosedAccountFlag=0";
        $result = mssql_query($query);
        $info = array();//Holds all row info
        if(mssql_num_rows($result) > 0)
            while($row = mssql_fetch_array($result))
                $info[] = $row;
        return $info;
    }
    
    public function GetTransactions($portfolio_id){
        if(!$this->pc->connect())//Try connecting
            return "Error Connecting to PC";

        $query = "SELECT *
                  FROM Transactions t 
                  WHERE t.portfolioid IN ($portfolio_id)";
        $result = mssql_query($query);
        $info = array();//Holds all row info
        if(mssql_num_rows($result) > 0)
            while($row = mssql_fetch_array($result))
                $info[] = $row;
        return $info;
        
    }

    public function DoesAccountExistInPC($account_number){
		$query = "SELECT PortfolioID FROM Portfolios WHERE AccountNumber = '{$account_number}'";
		$pc = self::CustomQuery($query);
		if($pc !== 0)
			return 1;
		return 0;
	}
    
    public function CustomQuery($query){
        if(!$this->pc->connect())//Try connecting
            return "Error Connecting to PC";
        $result = mssql_query($query);
        $info = array();//Holds all row info
        if(mssql_num_rows($result) > 0) {
			while ($row = mssql_fetch_array($result))
				$info[] = $row;
			return $info;
		}
		return 0;
    }

    public function QueryStraightResult($query){
        if(!$this->pc->connect())//Try connecting
            return "Error Connecting to PC";
        $result = mssql_query($query);
        return $result;
    }
}
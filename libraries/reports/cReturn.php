<?php

require_once("libraries/reports/cTransactions.php");

//$pids = $_REQUEST['pids'];
class cReturn extends cTransactions
{    
    /**
     * Adds the shorts passed in.  Returns the shorts total value
     * @param type $contributions
     * @return type
     */
    public function AddShorts($shorts)
    {
        if($shorts)
            foreach($shorts AS $k => $v)
            {
                $shorts_total += $v['value'];
            }
        return $shorts_total;
    }
    
    /**
     * Adds the shorts passed in.  Returns the shorts total value
     * @param type $contributions
     * @return type
     */
    public function AddCovers($covers)
    {
        if($covers)
            foreach($covers AS $k => $v)
            {
                $covers_total += $v['value'];
            }
        return $covers_total;
    }
    
    /**
     * Adds the shorts passed in.  Returns the shorts total value
     * @param type $contributions
     * @return type
     */
    public function AddBuys($buys)
    {
        if($buys)
            foreach($buys AS $k => $v)
            {
                $buys_total += $v['value'];
            }
        return $buys_total;
    }
    
    /**
     * Adds the shorts passed in.  Returns the shorts total value
     * @param type $contributions
     * @return type
     */
    public function AddSells($sells)
    {
        if($sells)
            foreach($sells AS $k => $v)
            {
                $sells_total += $v['value'];
            }
        return $sells_total;
    }
    
    /**
     * Adds the contributions passed in.  Returns the contributions total value
     * @param type $contributions
     * @return type
     */
    public function AddContributions($contributions)
    {
        if($contributions)
            foreach($contributions AS $k => $v)
                $contributions_total += $v['value'];
        
        return $contributions_total;
    }
    
    /**
     * Adds the withdrawals passed in.  Returns the withdrawals total value
     * @param type $withdrawals
     * @return type
     */
    public function AddWithdrawals($withdrawals)
    {
        if($withdrawals)
            foreach($withdrawals AS $k => $v)
                $withdrawals_total += $v['value'];
        
        return $withdrawals_total;
    }

    /**
     * Adds the Dividends passed in.  Returns the dividends total value
     * @param type $dividends
     * @return type
     */
    public function AddDividends($dividends)
    {
        if($dividends)
            foreach($dividends AS $k => $v)
                $dividends_total += $v['value'];
        
        return $dividends_total;
    }
    
    /**
     * Adds the Interest passed in.  Returns the interest total value
     * @param type $interest
     * @return type
     */
    public function AddInterest($interest)
    {
        if($interest)
            foreach($interest AS $k => $v)
                $interest_total += $v['value'];
        
        return $interest_total;
    }
    
    /**
     * Adds the Expenses passed in.  Returns the expenses total value
     * @param type $expenses
     * @return type
     */
    public function AddExpenses($expenses)
    {
        if($expenses)
            foreach($expenses AS $k => $v)
                $expenses_total += $v['value'];
        
        return $expenses_total;
    }

    /**
     * Adds the Income passed in.  Returns the income total value
     * @param type $interest
     * @return type
     */
    public function AddIncome($income)
    {
        if($income)
            foreach($income AS $k => $v)
                $income_total += $v['value'];
        
        return $income_total;
    }
    
    /**
     * Calculates net contributions and returns the result
     * @param type $contributions
     * @param type $withdrawals
     * @return type
     */
    public function GetNetContributions($contributions, $withdrawals)
    {
        $net = $contributions + $withdrawals;
        return $net;
    }
    
    /**
     * Calculates the net total -- Beginning value + Net contributions
     * @param type $beginning_value
     * @param type $net_contributions
     * @return type
     */
    public function GetNetTotal($beginning_value, $net_contributions)
    {
        $net_total = $beginning_value + $net_contributions;
        return $net_total;
    }
    
    public function GetGainLoss($beginning_value, $end_value, $net_contributions)
    {
        $gl = $end_value - $beginning_value + $net_contributions;
    }
    
    /**
     * Takes the beginning, end, and income values to calculate total investment return percentage
     * @param type $end_value
     * @param type $net_total
     * @return type
     */
    public function GetInvestmentReturnPercentage($beginning_value, $end_value, $income)
    {   
        if(!$beginning_value)
            return 0;
        $return_total = (($beginning_value - $end_value) + $income)/$beginning_value;
        return $return_total;
    }
    
    /**
     * Calculate the investment return
     * @param type $beginning_value
     * @param type $end_value
     * @param type $net_contributions
     * @param type $income
     */
    public function GetInvestmentReturn($beginning_value, $end_value, $net_contributions)
    {
        $return_total = ($end_value - $net_contributions - $beginning_value);
        return $return_total;
    }
  
    /**
     * Calculates the net income from the given income and expenses
     * @param type $income
     * @param type $expenses
     */
    public function GetNetIncome($income, $expenses)
    {
        $net_income = $income + $expenses;
        return $net_income;
    }
    
    /**
     * Calculate the capital appreciation
     * @param type $return
     * @param type $expenses
     */
    public function GetCapitalAppreciation($end_value, $beginning_value, $net_contributions, $net_income)
    {
        $capital_appreciation = ($end_value - $beginning_value) - ($net_contributions + $net_income);
        return $capital_appreciation;
    }

    public function SetupPriceMergeTable($startDate, $symbol){
        global $adb;
        require_once("include/utils/cron/cSecuritiesAccess.php");
        $info = cSecuritiesAccess::GetSecurityIDsBySymbol($symbol);
        $security_id = $info[0]['security_id'];
        $query = "drop table if exists pricing_tmp";
        $adb->pquery($query, array());

        $query = "create temporary table pricing_tmp
        (SELECT pr.*
        from vtiger_securities 
        join vtiger_pc_security_prices pr using (security_id) 
        where price_date >= ?
        AND security_id = ?)";
        $adb->pquery($query, array($startDate, $security_id));

        $query = "INSERT INTO pricing_tmp
        (SELECT pr.*
        from vtiger_securities 
        join vtiger_pc_custom_prices pr using (security_id) 
        where price_date >= ?
        AND security_id = ?)";
        $adb->pquery($query, array($startDate, $security_id));
    }
    /**
     * This is all based on John's original stuff... Just noting this may need to be changed.  Hopefully doesn't taint this entire class
     * @global type $adb
     * @param type $symbol
     * @param type $startDate
     * @param type $endDate
     * @param type $feePct
     * @return int
     */
    
    /* ====  START : Felipe 2016-07-25 MyChanges ===== */
     
    public function getReferenceReturn($symbol,$startDate,$endDate,$feePct = 0) {
    	
    	global $adb;
    	
    	$end = $start = array();
    	
//        $this->SetupPriceMergeTable($startDate, $symbol);
        
        $result = $adb->pquery("SELECT to_days(date) as to_days, date AS price_date, close AS price from 
                               vtiger_prices_index where date <= ?
                               AND symbol = ? 
                               order by date DESC limit 1",array($startDate,$symbol));
        
        if($adb->num_rows($result) <= 0)
            return 0;
        
        while($v = $adb->fetchByAssoc($result))
        	$start = $v;

/*        $query = "SELECT to_days(pr.price_date), pr.price_date, pr.price FROM vtiger_securities 
                  join vtiger_pc_security_prices pr USING (security_id) 
                  where price_date <= ? AND security_id = (SELECT security_id FROM vtiger_securities WHERE security_symbol = ? LIMIT 1)
                  order by pr.price_date desc limit 1";
//        $query = "SELECT to_days(price_date), price_date, price from vtiger_securities join vtiger_pc_security_prices using (security_id) where price_date = ? AND security_symbol = ? order by price_date desc limit 1";
*/      $query = "SELECT to_days(date) as to_days, date AS price_date, close AS price 
				  FROM vtiger_prices_index WHERE date <= ?
                  AND symbol = ?
                  order by price_date desc limit 1";
        $end_result = $adb->pquery($query,array($endDate,$symbol));

        if($adb->num_rows($end_result) <= 0)
            return 0;
        
        while($v = $adb->fetchByAssoc($end_result))
			$end = $v;
			
		/*Changes 14June,2016 
	     * $end 0 => to_days, 1 => price_date, 2 => price
	     */
    
    	$intervalDays = $end['to_days'] - $start['to_days'];

        $guess = $end['price'] / $start['price'] - 1;

        if ($intervalDays >= 365)
            $irr = pow((1+$guess),(365/$intervalDays)) - 1;
        else
            $irr = $guess;
#echo "IRR IS: {$irr}, because: " . "{$end['price']} / {$start['price']} -1 gets us there... with interval days at {$intervalDays}" . "<br />";
        return $irr;
    }    
}

?>
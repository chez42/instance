<?php
include_once("include/utils/omniscientCustom.php");

class cPortfolioDetails
{
    /**
     * Calculate the grand totals for all accounts
     * @param type $account_info
     * @return type
     */
    public function CalculateGrandTotal($account_info)
    {
        foreach($account_info AS $k => $v)
        {
            $market_value += $v['market_value'];
            $total += $v['total'];
            $cash_value += $v['cash_value'];
        }
        
        return(array("market_value" => $market_value,
                     "total_value" => $total,
                     "cash_value" => $cash_value));
    }
    
    /**
     * Calculates the totals for the given account
     * @global type $adb
     * @return string
     */
    private function GetAccountTotals($account_number)
    {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();
        $query = "SELECT *, SUM(latest_value) AS total_value
                  FROM t_summary_table_{$current_user->get('id')} 
                  WHERE account_number = ?
                  GROUP BY account_number, security_type_id, activity_id";
        
        $result = $adb->pquery($query, array($account_number));

        foreach($result AS $k => $v)
        {
            if($v['security_type_id'] == 11)
            {
                $tmp = "cash_value";
            }
            else
            if($v['report_as_type_id'] == 60)
            {
                $tmp = "annual_management_fee";
            }
            else
            if($v['activity_id'] == 80)
            {
                $tmp = "shorts";
            }
            else
            {
                $tmp = "market_value";
            }
            $summary_info[$v['account_number']][$tmp] += $v['total_value'];
            $summary_info[$v['account_number']]['value'] += $v['total_value'];
            $summary_totals["value"] += $v['total_value'];
            $summary_totals[$tmp] += $v['total_value'];
            if(!$summary_info[$v['account_number']]['account_name'])
            {
                $portfolio_info = new Portfolios();
                $info = $portfolio_info->GetPortfolioInfoFromAccountNumber($v['account_number']);
                $summary_info[$v['account_number']]['account_name'] = $adb->query_result($info, 0,'portfolio_account_name');
                $summary_info[$v['account_number']]['firstname'] = $adb->query_result($info, 0,'portfolio_first_name');
                $summary_info[$v['account_number']]['lastname'] = $adb->query_result($info, 0,'portfolio_last_name');
                $summary_info[$v['account_number']]['nickname'] = $adb->query_result($info, 0,'nickname');
                $summary_info[$v['account_number']]['master_account'] = $adb->query_result($info, 0,'master_account');
                $type = $adb->query_result($info, 0,'portfolio_account_type');
                if(!$type)
                    $type = "Undefined";
                $summary_info[$v['account_number']]['type'] = $type;
            }
        }
		
        // My Changes For Print Report Task 22-08-2016 Felipe Reports
		return $summary_info;
		
        foreach($summary_info AS $k => $v)
        {
            $shorts = cPholdingsInfo::CalculateShorts($k);
            $summary_info[$k]['market_value'];// += $shorts['total'];// + $shorts['difference'];//$summary_info[$k]['shorts'];
            $summary_info[$k]['value'];// -= $shorts['total'] - $shorts['difference'];//$summary_info[$k]['shorts'];
            $summary_info[$k]['cash_value'];// -= $shorts['total'] - $shorts['difference'];//$summary_info[$k]['shorts'];


//            $summary_info[$k]['market_value'] += $summary_info[$k]['shorts'];
//            $summary_info[$k]['value'] -= $summary_info[$k]['shorts'];
//            $summary_info[$k]['cash_value'] += $summary_info[$k]['shorts'];
        }
        
        return $summary_info;
    }
    /**
     * Get the account details for the given portfolio account number
     * @global type $adb
     * @param type $account_number
     * @return type
     */
    public function GetAccountDetails($account_number)
    {
        global $adb;

        $result = GetPortfolioInfoFromPortfolioAccountNumber($account_number);//This function is found in omniscientCustom.php
        if($adb->query_result($result, 0, "portfolio_id"))//Assuming we have a result
        {
            $accounts = $this->GetAccountTotals($account_number);
            $acct_name = $adb->query_result($result, 0, "portfolio_account_name");
            $management_fee = $adb->query_result($result, 0, "advisor_fee");
            $acct_type = $adb->query_result($result, 0, "portfolio_account_type");
            if(strlen($acct_type) <= 1)
                $acct_type = "";
            if(!$acct_type)
                $acct_type = "Undefined";

            if($annual_fee <= 0)
                $annual_fee *= -1;

            $query = "SELECT pi.origination, pic.master_account, pi.portfolioinformationid
                      FROM vtiger_portfolioinformation pi
                      LEFT JOIN vtiger_portfolioinformationcf pic ON pic.portfolioinformationid = pi.portfolioinformationid
                      WHERE pi.account_number = ?";
            $r = $adb->pquery($query, array($account_number));
            if($r)
            {
                $origination = $adb->query_result($r, 0, "origination");
                $master_account = $adb->query_result($r, 0, "master_account");
                $portfolio_information_id = $adb->query_result($r, 0, "portfolioinformationid");
            }

            $account_info = array("name"=>$acct_name,
                                  "number"=>$account_number,
                                  "type"=>$acct_type,
                                  "total"=>$accounts[$account_number]['value'],
                                  "market_value"=>$accounts[$account_number]['market_value'],
                                  "cash_value"=>$accounts[$account_number]['cash_value'],
                                  "shorts"=>$accounts[$account_number]['shorts'],
                                  "management_fee"=>$management_fee,
                                  "annual_fee"=>$accounts[$account_number]['annual_fee'],
                                  "custodian"=>$origination,
				  "master_account"=>$master_account,
                                  "portfolio_information_id"=>$portfolio_information_id
                                  );
        }
        return $account_info;
    }
}

?>

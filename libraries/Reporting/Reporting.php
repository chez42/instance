<?php
spl_autoload_register(function ($className) {
    if (file_exists("libraries/Reporting/$className.php")) {
        include_once "libraries/Reporting/$className.php";
    }
});

/**
 * This returns the transactions for the passed in account number
 * @param $account_number
 * @param $sdate
 * @param $edate
 * @return array
 */
function GetTransactionsPerformanceData($account_number, $sdate, $edate, $applyrules=true){
    global $adb;

    $query = "SELECT SUM(CONCAT(operation, net_amount)) AS amount, transaction_type, transaction_activity, trade_date, operation, 
                         buy_sell_indicator, SUM(commission) AS commission
                  FROM vtiger_transactions t
                  JOIN vtiger_transactionscf cf USING (transactionsid)
                  JOIN vtiger_crmentity e ON e.crmid = t.transactionsid
                  JOIN vtiger_portfolioinformation p ON p.account_number = t.account_number
                  JOIN vtiger_portfolioinformationcf pcf ON p.portfolioinformationid = pcf.portfolioinformationid
                  WHERE t.account_number = ?
                  AND trade_date >= ?
                  AND trade_date <= ?
                  AND e.deleted = 0
                  AND pcf.disable_performance != 1
                  AND transaction_type NOT LIKE ('%DUPE%')
                  GROUP BY transaction_type, transaction_activity, buy_sell_indicator";

    $result = $adb->pquery($query, array($account_number, $sdate, $edate));
    $data = array();
    if($adb->num_rows($result) > 0){
        while($v = $adb->fetchByAssoc($result)){
            $tmp = new cTransactionInfo();
            $tmp->amount = $v['amount'];
            $tmp->transaction_activity = $v['transaction_activity'];
            $tmp->trade_date = $v['trade_date'];
            $tmp->operation = $v['operation'];
            $tmp->commission = $v['commission'];
            $tmp->buy_sell_indicator = $v['buy_sell_indicator'];
            $tmp->transaction_type = $v['transaction_type'];
            if($applyrules == true)
                ApplyPerformanceRules($tmp);//Set the rules for performance
            $data[] = $tmp;
        }
    }

    return $data;
}

function ApplyPerformanceRules(&$data){
    switch(strtoupper($data->transaction_activity)){
#        case "MANAGEMENT FEE":
#            $data->transaction_type = "Reversal";
#            break;
        case "":
            $data->transaction_type = "Unknown";
        break;
    }

    switch(strtoupper($data->transaction_type)){
        case "":
            $data->transaction_type = "Unknown";
            break;
    }
}
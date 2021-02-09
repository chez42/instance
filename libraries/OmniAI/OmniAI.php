<?php
spl_autoload_register(function ($className) {
    if (file_exists("libraries/OmniAI/$className.php")) {
        include_once "libraries/OmniAI/$className.php";
    }
});
include_once "libraries/Reporting/ReportCommonFunctions.php";


class OmniAI{
    public function __construct(){
    }

    public function AuditTransactions($account_number){
        global $adb;

    }

    public function FixReceiptOfSecuritiesWithNoAmount($date){
        global $adb;

        $query = "SELECT t.account_number, t.security_symbol, quantity, net_amount, p.origination, trade_date, DATE_ADD(trade_date, INTERVAL 30 DAY) AS edate, aclass
                  FROM vtiger_transactions t 
                  JOIN vtiger_transactionscf cf USING (transactionsid)
                  JOIN vtiger_modsecurities m ON m.security_symbol = t.security_symbol
                  JOIN vtiger_modsecuritiescf mcf USING (modsecuritiesid)
                  JOIN vtiger_portfolioinformation p ON p.account_number = t.account_number
                  JOIN vtiger_portfolioinformationcf pcf USING(portfolioinformationid)
                  WHERE transaction_activity = 'Receipt of Securities' 
                  AND net_amount = 0
                  AND trade_date >= ?
                  GROUP BY security_symbol, trade_date";
        $result = $adb->pquery($query, array($date));

        if($adb->num_rows($result) > 0){
            while($x = $adb->fetchByAssoc($result)){
                $price = cFidelityPrices::GetBestKnownPriceBeforeDate($x['security_symbol'], $x['trade_date']);
                if($price == false){
                    $fix = new CustodianWriter();
                    $sdate = GetDateMinusMonthsSpecified($x['trade_date'], 1);
                    $edate = $x['trade_date'];
                    $fix->WriteEodToCustodian($x['security_symbol'], $sdate, $edate, $x['origination']);//Only update if there is no price for previous day
                }
            }
        }
    }
}
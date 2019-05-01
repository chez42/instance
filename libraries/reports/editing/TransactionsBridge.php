<?php
/**
 * The idea behind the security bridge is that it handles interactions between the module and the portfolio center tables
 */

class cTransactionsBridge{
    static protected $datasets = '1,28';
    
    public function __construct() {
    }
    
    public static function UpdateTransaction($transaction_id, $quantity, $detail){
        global $adb;
        $query = "UPDATE vtiger_pc_transactions SET quantity = ?, notes = ? WHERE transaction_id = ?";
        $adb->pquery($query, array($quantity, $detail, $transaction_id));
    }
    
    public static function CreateTransactionsEntitiesFromPCTransactions($trade_date = null){
        global $adb;
        if($trade_date)
            $and = " AND t.trade_date >= '{$trade_date}' ";
        $query = "DROP TABLE IF EXISTS transactions_transfer";
        $adb->pquery($query, array());
        $query = "CREATE TEMPORARY TABLE transactions_transfer(
                  transaction_id INT(10) UNSIGNED PRIMARY KEY,
                  portfolio_id INT(10) UNSIGNED,
                  symbol_id INT(10) UNSIGNED,
                  activity_id INT(10) UNSIGNED,
                  report_as_type_id INT(10) UNSIGNED,
                  quantity DECIMAL(10,2),
                  trade_date VARCHAR(50),
                  origination_id INT(5),
                  cost_basis_adjustment DECIMAL(10,2),
                  security_symbol VARCHAR(10),
                  account_number VARCHAR(20),
                  activity_name VARCHAR(50),
                  origination VARCHAR(50),
                  activity_type VARCHAR(50),
                  entityID INT(10));";
        $adb->pquery($query, array());
        $datasets = self::$datasets;
        $query = "INSERT INTO transactions_transfer
                  (SELECT t.transaction_id, t.portfolio_id, t.symbol_id, t.activity_id, t.report_as_type_id, t.quantity, t.trade_date,
                          t.origination_id, t.cost_basis_adjustment, s.security_symbol, p.portfolio_account_number AS account_number, 
                          a.activity_name, ori.interface_name AS origination, r.report_as_type_name AS activity_type, (SELECT IncreaseAndReturnCrmEntitySequence() AS entityID)
                  FROM vtiger_pc_transactions t
                  JOIN vtiger_securities s 	ON t.symbol_id = s.security_id
                  JOIN vtiger_portfolios p 	ON p.portfolio_id = t.portfolio_id
                  JOIN vtiger_pc_activities a ON a.activity_id = t.activity_id
                  JOIN vtiger_pc_interface_originations ori ON ori.origination_id = t.origination_id
                  JOIN vtiger_pc_report_as_types r ON r.report_as_type_id = t.report_as_type_id
                  WHERE t.transaction_id NOT IN (SELECT transaction_id FROM vtiger_transactions)
                  {$and}
                  AND s.security_data_set_id IN ({$datasets}))";
        $adb->pquery($query, array());

        $query = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, createdtime, modifiedtime, label)
                  SELECT entityID, 1, 1, 1, 'Transactions', NOW(), NOW(), account_number FROM transactions_transfer";
        $adb->pquery($query, array());
        
        $query = "INSERT INTO vtiger_transactions (transactionsid, portfolio_id, security_id, trade_date, quantity, cost_basis_adjustment, 
                                                   security_symbol, account_number, activity, activity_id, origination, report_as_type_id, activity_type, transaction_id)
                  SELECT entityID, portfolio_id, symbol_id, trade_date, quantity, cost_basis_adjustment, security_symbol, account_number, 
                         activity_name, activity_id, origination, report_as_type_id, activity_type, transaction_id
                  FROM transactions_transfer";
        $adb->pquery($query, array());
        
        $query = "INSERT INTO vtiger_transactionscf (transactionsid)
                  SELECT entityID FROM transactions_transfer";
        $adb->pquery($query, array());
    }
    
    public static function UpdateTransactionsEntityPricesFromPCPricing($trade_date = null){
        global $adb;
        $datasets = self::$datasets;
        
        $query = "DROP TABLE IF EXISTS temp_transaction_pricing";
        $adb->pquery($query, array());

        if($trade_date)
            $and = " AND t.trade_date >= '{$trade_date}' ";
        $query = "CREATE TEMPORARY TABLE temp_transaction_pricing
        (SELECT t.transaction_id, pr.price, t.security_id
        FROM vtiger_transactions t
        JOIN vtiger_securities s ON s.security_id = t.security_id
        LEFT JOIN vtiger_pc_security_prices pr ON pr.security_price_id = 
          (SELECT MAX(security_price_id) AS security_price_id 
                        FROM vtiger_pc_security_prices 
                        WHERE security_id = t.security_id
                        AND trade_date <= t.trade_date)
        WHERE t.security_id != 1
        AND s.security_data_set_id IN ({$datasets})
        {$and})";
        $adb->pquery($query, array());

        $query = "UPDATE vtiger_transactions t
        JOIN temp_transaction_pricing pr ON t.transaction_id = pr.transaction_id
        SET t.security_price = pr.price";
        $adb->pquery($query, array());
    }
}
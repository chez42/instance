<?php

class ModSecurities_HistoricalData_Model extends Vtiger_Module_Model {
    /**
     * Get all historical pricing information for the given security
     * @global type $adb
     * @param type $security_id
     * @return int
     */
    public static function GetAllHistoricalPricesWithVolume($security_id, $direction = "DESC"){
        global $adb;
        $query = "SELECT security_price_id, price, DATE_FORMAT(price_date,'%d-%m-%Y') AS price_date, 
        DATE_FORMAT(price_date, '%a %b %d %Y') AS stockFormat, SUM(t.quantity) AS volume
        FROM vtiger_pc_security_prices p
        JOIN vtiger_pc_transactions t ON p.security_id = t.symbol_id AND p.price_date = t.trade_date
        WHERE security_id = ? GROUP BY security_price_id, price_date ORDER BY UNIX_TIMESTAMP(price_date) {$direction}";
        
        $result = $adb->pquery($query, array($security_id));
        $pricing = array();
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $pricing[] = $v;
            }
            return $pricing;
        }else{
            return 0;
        }
    }

    public static function GetHistoricalPricesForSymbol($symbol, $start=null, $end=null, $table=null){
        global $adb;

        $params = array();
        $params[] = $symbol;
        if($start) {
            $params[] = $start;
            $and = " AND date >= ? ";
        }
        if($end) {
            $params[] = $end;
            $and = " AND date <= ? ";
        }

        if($table == 'vtiger_prices')
            $query = "SELECT symbol, date, open, high, low, close, adjusted_close, volume 
                      FROM {$table} 
                      WHERE symbol = ? {$and} ";
        else{
            $query = "SELECT il.symbol, date, open, high, low, close, adj_close, volume
                      FROM {$table}
                      JOIN vtiger_index_list il ON il.security_symbol = ?
                      WHERE {$table}.symbol = il.symbol";
        }
        $result = $adb->pquery($query, $params);
        if($adb->num_rows($result) > 0){
            $prices = array();
            while($v = $adb->fetchByAssoc($result)) {
                $prices[] = $v;
            }
            return $prices;
        }
        return 0;
    }
    
    public static function GetHistoricalPricesWithVolumeAdvisor($security_id, $direction = "DESC"){
        global $adb;
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        if($currentUserModel->isAdminUser())
            return self::GetAllHistoricalPricesWithVolume ($security_id, $direction);

        require('user_privileges/sharing_privileges_'.$currentUserModel->get('id').'.php');
        foreach($PortfolioInformation_share_read_permission['GROUP'] AS $groups => $users){
            foreach($users AS $k => $v)
                $related_ids[] = $v;
            $related_ids[] = $groups;
        }
        $related_ids[] = $currentUserModel->get('id');//Always at least give the current user ID
        $questions = generateQuestionMarks($related_ids);

        $query = "SELECT security_price_id, price, DATE_FORMAT(price_date,'%d-%m-%Y') AS price_date, 
                  DATE_FORMAT(price_date, '%a %b %d %Y') AS stockFormat, SUM(t.quantity) AS volume
                  FROM vtiger_pc_security_prices p
                  JOIN vtiger_pc_transactions t ON p.security_id = t.symbol_id AND p.price_date = t.trade_date
                  JOIN vtiger_portfolios vp ON vp.portfolio_id = t.portfolio_id
                  JOIN vtiger_portfolioinformation vportinf ON vportinf.account_number = vp.portfolio_account_number
                  JOIN vtiger_crmentity e ON e.crmid = vportinf.portfolioinformationid
                  WHERE security_id = ? 
                  AND e.smownerid IN ({$questions})
                  GROUP BY security_price_id, price_date 
                  ORDER BY UNIX_TIMESTAMP(price_date) {$direction}";
        
        $result = $adb->pquery($query, array($security_id, $related_ids));
        $pricing = array();
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $pricing[] = $v;
            }
            return $pricing;
        }else{
            return 0;
        }
    }
}

?>
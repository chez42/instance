<?php
/**
 * The idea behind the security bridge is that it handles interactions between the module and the portfolio center tables
 */

class ModSecurities_SecurityBridge_Model extends Vtiger_Module{
    static protected $datasets = '1,28';
    
    public function __construct() {
    }
    
    /**
     * Pulls the individual security
     * @param type $symbol
     * @return type
     */
    public static function PullSecurity($symbol){
        self::CreateTemporarySecurityList($symbol);
        return self::GetTemporarySecurityList();
    }
    
    /**
     * Pulls all security information we need for inserting into the ModSecurities module
     * @return type
     */
    public static function PullAllSecurities(){
        self::CreateTemporarySecurityList();
        return self::GetTemporarySecurityList();
    }
    
    private function GetTemporarySecurityList(){
        global $adb;
        $query = "SELECT s2.security_id, s2.security_symbol, s2.security_description
                  FROM SecurityFinalList s1
                  INNER JOIN vtiger_securities s2 ON (s1.sid = s2.security_id)";
        $list = array();
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $list[] = $v;
            }
        }
        return $list;
    }
    /**
     * This creates the temporary tables that pull from the securities table based on data set and determine which is the "proper" one to pull.  Symbol is completely optional.
     * If symbol is entered then it will only pull for that symbol in particular rather than all.
     * @param type $symbol
     */
    private function CreateTemporarySecurityList($symbol=null){
        global $adb;
        if(strlen($symbol) > 2){
            $and = "AND security_symbol = ?";
        }
        $query = "DROP TABLE IF EXISTS SecurityIDList;";
        $adb->pquery($query, array());
        $query = "DROP TABLE IF EXISTS SecurityFinalList;";
        $adb->pquery($query, array());
        $datsets = self::$datasets;
        $query = "CREATE TEMPORARY TABLE SecurityIDList 
                        (SELECT MAX(security_id) sid, security_data_set_id, security_symbol, security_description
                        FROM vtiger_securities s
                        WHERE security_data_set_id IN ({$datasets})
                        {$and}
                        AND s.security_symbol NOT IN (SELECT security_symbol FROM vtiger_modsecurities)
                        GROUP BY security_symbol, security_data_set_id
                        ORDER BY security_id DESC);";
        if(strlen($symbol) > 2)
            $adb->pquery($query, array($symbol));
        else
            $adb->pquery($query, array());
        $query = "CREATE TEMPORARY TABLE SecurityFinalList 
                  (SELECT * FROM(
                        SELECT *
                        FROM SecurityIDList
                        ORDER BY security_data_set_id, sid ASC) AS bla
                  GROUP BY security_symbol
                  ORDER BY security_data_set_id ASC);";
        $adb->pquery($query, array());
    }
    
    public static function WriteListToModSecurities($list){
        print_r($list);exit;
        if(sizeof($list) > 0){
            foreach($list AS $k => $v){
                $t = Vtiger_Record_Model::getCleanInstance("ModSecurities");
                $data = $t->getData();
                $data['security_name'] = $v['security_description'];
                $data['security_symbol'] = $v['security_symbol'];
                $data['security_id'] = $v['security_id'];
                $t->setData($data);
                $t->save();
                $t = null;
            }
        }
    }
    
    /**
     * Update all security prices in the ModSecurities Module.  This takes awhile to run and uses the latest date price from vtiger_pc_security_prices
     * @global type $adb
     */
    public static function UpdateAllModSecuritiesPrices(){
        global $adb;
        $query = "UPDATE vtiger_modsecurities ms
                  JOIN vtiger_securities s ON ms.security_id = s.security_id
                  SET ms.security_price = (SELECT price FROM vtiger_pc_security_prices 
                                                    WHERE price_date = (SELECT max(price_date) FROM vtiger_pc_security_prices WHERE security_id=ms.security_id AND price > 0) 
                                                    AND security_id=ms.security_id) 
                                                    * CASE WHEN (s.security_factor > 0) THEN s.security_price_adjustment * s.security_factor
                                                    ELSE s.security_price_adjustment END
                  WHERE ms.security_id != 0";
        $adb->pquery($query, array());
    }
    
    /**
     * This updates an individual security in the ModSecurities Module.  It uses the latest data price for that security from vtiger_pc_security_prices
     * @global type $adb
     * @param type $security_id
     */
    public static function UpdateIndividualModSecurityPrice($security_id){
        global $adb;
        $query = "UPDATE vtiger_modsecurities ms
                  JOIN vtiger_securities s ON ms.security_id = s.security_id
                  SET ms.security_price = (SELECT price FROM vtiger_pc_security_prices 
                                                    WHERE price_date = (SELECT max(price_date) FROM vtiger_pc_security_prices WHERE security_id=ms.security_id AND price > 0) 
                                                    AND security_id=ms.security_id) 
                                                    * CASE WHEN (s.security_factor > 0) THEN s.security_price_adjustment * s.security_factor
                                                    ELSE s.security_price_adjustment END
                  WHERE ms.security_id = ?";
        $adb->pquery($query, array($security_id));
    }
    
    /**
     * Gets the latest security_price_id from the pricing table.  This is what will be used to physically alter the table
     * @global type $adb
     * @param type $security_id
     * @return int
     */
    public static function GetLatestPriceIDForSecurity($security_id){
        global $adb;
        $query = "SELECT security_price_id
                  FROM vtiger_pc_security_prices WHERE price_date = (SELECT MAX(price_date) AS price_date
                                                                     FROM vtiger_pc_security_prices p
                                                                     WHERE p.security_id = ?)
                  AND security_id = ?";
        $result = $adb->pquery($query, array($security_id, $security_id));
        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, 'security_price_id');
        } else
            return 0;
    }
    
    /**
     * Updates the pricing table price
     * @global type $adb
     * @param type $price_id
     * @param type $price
     */
    public static function UpdatePricingTablePrice($price_id, $price, $update_pc = 0){
        global $adb;
        global $current_user;
        $old_price = self::GetPriceBySecurityPriceID($price_id);
        $query = "UPDATE vtiger_pc_security_prices SET price = ? WHERE security_price_id = ?";
        $adb->pquery($query, array($price, $price_id));
        if(mysql_affected_rows() != 1){
            if($update_pc){
                self::LogPriceModifications($price_id, $old_price, $price, $current_user->id);
                self::UpdatePCPrice($price_id, $price);
            }
            return 0;
        } else{
            self::LogPriceModifications($price_id, $old_price, $price, $current_user->id);
            if($update_pc){
                self::UpdatePCPrice($price_id, $price);
            }
            return 1;
        }
    }
    
    /**
     * Update portfolio center with the given price information
     * @param type $price_id
     * @param type $price
     */
    public static function UpdatePCPrice($price_id, $price){
        include_once('include/utils/cron/cPortfolioCenter.php');
        $pc = new cPortfolioCenter("pctestserver", "syncuser", "Concert222");
        $query = "UPDATE SecurityPrices SET Price = {$price} WHERE SecurityPriceID = {$price_id}";
//        $query = "SELECT * FROM SecurityPrices WHERE SecurityPriceID = {$price_id}";
        $r = $pc->CustomQuery($query);
        return $r;
    }

    /**
     * Update portfolio center code information
     * @global type $adb
     * @param type $security_id
     * @param type $type_id
     * @param type $code_id
     */
    public static function UpdatePCSecurityCodeID($security_id, $type_id, $code_id){
        if($code_id != 0 && $type_id != 0 && $security_id != 0){
            include_once('include/utils/cron/cPortfolioCenter.php');
            $pc = new cPortfolioCenter("pctestserver", "syncuser", "Concert222");        
            $query = "UPDATE SecurityCodes SET CodeID = {$code_id} WHERE SecurityID = {$security_id} AND CodeTypeID = {$type_id}";
            $r = $pc->CustomQuery($query);
            return $r;
        }
    }


    /**
     * Get all historical pricing information for the given security
     * @global type $adb
     * @param type $security_id
     * @return int
     */
    public static function GetAllHistoricalPrices($security_id, $direction = "DESC"){
        global $adb;
        $query = "SELECT security_price_id, price, DATE_FORMAT(price_date,'%d-%m-%Y') AS price_date, DATE_FORMAT(price_date, '%a %b %d %Y') AS stockFormat, DATE_FORMAT(price_date,'%m-%d-%Y') AS american_format
                  FROM vtiger_pc_security_prices WHERE security_id = ? ORDER BY UNIX_TIMESTAMP(price_date) {$direction}";
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
    
    /**
     * Log who modified a price
     * @global type $adb
     * @param type $price_id
     * @param type $old_price
     * @param type $new_price
     * @param type $modified_by
     */
    public static function LogPriceModifications($price_id, $old_price, $new_price, $modified_by){
        global $adb;
        $query = "INSERT INTO vtiger_price_modifications (price_id, old_price, new_price, modified_by, modified_time)
                  VALUES (?, ?, ?, ?, NOW())";
        $adb->pquery($query, array($price_id, $old_price, $new_price, $modified_by));
    }
    
    /**
     * Gets the price for the security price ID
     * @global type $adb
     * @param type $price_id
     * @return int
     */
    public static function GetPriceBySecurityPriceID($price_id){
        global $adb;
        $query = "SELECT price FROM vtiger_pc_security_prices WHERE security_price_id = ?";
        $result = $adb->pquery($query, array($price_id));
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'price');
        return 0;
    }
    
    public static function GetAssetClassBySecurityID($security_id){
        global $adb;
        $query = "SELECT code_id FROM vtiger_pc_security_codes WHERE security_id = ? AND code_type_id = 20";
        $result = $adb->pquery($query, array($security_id));
    }
    
    public static function GetCodeIDByAssetClassName($asset_class_name){
        global $adb;
        $datasets = self::$datasets;
        $query = "SELECT code_id FROM vtiger_pc_codes WHERE code_description = ? AND code_type_id = 20 AND data_set_id IN ({$datasets}) ORDER BY data_set_id, code_order ASC";
        $result = $adb->pquery($query, array($asset_class_name));
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'code_id');
        return 0;
    }
    
    public static function GetCodeIDBySectorClassName($sector_name){
        global $adb;
        $datasets = self::$datasets;
        $query = "SELECT code_id FROM vtiger_pc_codes WHERE code_description = ? AND code_type_id = 10 AND data_set_id IN ({$datasets}) ORDER BY data_set_id, code_order ASC";
        $result = $adb->pquery($query, array($sector_name));
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'code_id');
        return 0;
    }
    
    public static function GetPayFrequencyIDByFrequencyName($frequency_name){
        global $adb;
        $query = "SELECT frequency_type_id FROM vtiger_pc_frequency_types WHERE frequency_type_name = ?";
        $result = $adb->pquery($query, array($frequency_name));
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'frequency_type_id');
        return 0;
    }
    
    public static function UpdateSecurityCodeID($security_id, $type_id, $code_id, $update_pc = 0){
        global $adb;
        if($code_id != 0 && $type_id != 0 && $security_id != 0){
            $query = "UPDATE vtiger_pc_security_codes SET code_id = ? WHERE security_id = ? AND code_type_id = ?";
            $adb->pquery($query, array($code_id, $security_id, $type_id));
            if($update_pc){
                self::UpdatePCSecurityCodeID($security_id, $type_id, $code_id);
            }
        }
    }
    
    public static function UpdatePayFrequencyCodeID($security_id, $frequency_id, $update_pc = 0){
        global $adb;
        if($security_id != 0 && $frequency_id != 0){
            $query = "UPDATE vtiger_securities SET security_income_frequency_id = ? WHERE security_id = ?";
            $adb->pquery($query, array($frequency_id, $security_id));
            if($update_pc){
                self::UpdatePC($security_id, $type_id, $code_id);
            }
        }
    }
    
    public static function UpdatePCPayFrequencyCodeID($security_id, $frequency_id){
        if($frequency_id != 0 && $security_id != 0){
            include_once('include/utils/cron/cPortfolioCenter.php');
            $pc = new cPortfolioCenter("pctestserver", "syncuser", "Concert222");        
            $query = "UPDATE Securities SET IncomeFrequencyID = {$frequency_id} WHERE SecurityID = {$security_id}";
            $r = $pc->CustomQuery($query);
            return $r;
        }
    }


}
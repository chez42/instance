<?php


class ModSecurities_OmniToOmniTransfer_Model extends Vtiger_Module_Model{
    public function __construct(){

    }

    static public function CopyFromInstance($instance_db="live_omniscient", array $symbols){
        global $adb;
        $where = "";
        $params = array();

        if(sizeof($symbols) > 0){
            $questions = generateQuestionMarks($symbols);
            $where .= " WHERE m.security_symbol IN ({$questions}) ";
            $params[] = $symbols;
        }

        $query = "UPDATE vtiger_modsecurities m 
                  JOIN vtiger_modsecuritiescf cf USING (modsecuritiesid)
                  JOIN {$instance_db}.vtiger_modsecurities m2 ON m2.security_symbol = m.security_symbol
                  JOIN {$instance_db}.vtiger_modsecuritiescf cf2 ON m2.modsecuritiesid = cf2.modsecuritiesid 
                  SET m.security_name = m2.security_name, m.sectorpl = m2.sectorpl, m.pay_frequency = m2.pay_frequency, m.securitytype = m2.securitytype,
                      cf.aclass = cf2.aclass, cf.industrypl = cf2.industrypl, cf.summary = cf2.summary, cf.us_stock = cf2.us_stock,
                      cf.intl_stock = cf2.intl_stock, cf.us_bond = cf2.us_bond, cf.intl_bond = cf2.intl_bond, cf.preferred_net = cf2.preferred_net,
                      cf.convertible_net = cf2.convertible_net, cf.cash_net = cf2.cash_net, cf.other_net = cf2.other_net, 
                      cf.unclassified_net = cf2.unclassified_net, cf.morning_star_category = cf2.morning_star_category, 
                      cf.security_sector = cf2.security_sector
                      {$where}";
        $adb->pquery($query, $params, true);
    }
}
<?php

class cReportGlobals{
    public function __construct() {
        ;
    }
    
    public static function GetClientName($account_number, $calling_module){
        global $adb;
        $account_number = str_replace('combined_', '', $account_number);//In case it is a combined report
        switch($calling_module){
            case 'Accounts':
                $query = "SELECT accountname, accountid 
                          FROM vtiger_account 
                          WHERE accountid = (SELECT accountid 
                                             FROM vtiger_contactdetails cd
                                             JOIN vtiger_contactscf cf ON cd.contactid = cf.contactid
                                             WHERE cf.ssn = (SELECT portfolio_tax_id
                                             FROM vtiger_portfolios
                                             WHERE portfolio_account_number = ?) AND accountid != 0 LIMIT 1)";
                $result = $adb->pquery($query, array($account_number));
                if($adb->num_rows($result) > 0){
                    $query = "SELECT cf_722 FROM vtiger_accountscf WHERE accountid=?";
                    $account_id = $adb->query_result($result, 0, 'accountid');
                    $portfolio_name_result = $adb->pquery($query, array($account_id));
                    if($adb->num_rows($portfolio_name_result) > 0){
                        $portfolio_name = $adb->query_result($portfolio_name_result, 0, 'cf_722');
                    }
                    if(strlen($portfolio_name) > 0)
                        return $portfolio_name;
                    else
                        return $adb->query_result($result, 0, 'accountname');
                }
                break;
            case 'Contacts':
                $query = "SELECT firstname, lastname, contactid
                          FROM vtiger_contactdetails
                          WHERE contactid = (SELECT cd.contactid 
                                             FROM vtiger_contactscf cf
                                             JOIN vtiger_contactdetails cd ON cd.contactid = cf.contactid
                                             WHERE cf.ssn = (SELECT portfolio_tax_id
                                             FROM vtiger_portfolios
                                             WHERE portfolio_account_number = ?) AND accountid != 0 LIMIT 1)";
                $result = $adb->pquery($query, array($account_number));
                if($adb->num_rows($result) > 0){
                    $query = "SELECT cf_721 FROM vtiger_contactscf WHERE contactid=?";
                    $contact_id = $adb->query_result($result, 0, 'contactid');
                    $portfolio_name_result = $adb->pquery($query, array($contact_id));
                    if($adb->num_rows($portfolio_name_result) > 0){
                        $portfolio_name = $adb->query_result($portfolio_name_result, 0, 'cf_721');
                    }
                    if(strlen($portfolio_name) > 0)
                        return $portfolio_name;
                    else{
                        $fname = $adb->query_result($result, 0, 'firstname');
                        $lname = $adb->query_result($result, 0, 'lastname');
                        return $fname . " " . $lname;
                    }
                }
                break;
        }
    }
    
    public static function GetAccountNickname($account_number){
        global $adb;
        $query = "SELECT nickname FROM vtiger_pc_account_custom WHERE account_number = ?";
        $result = $adb->pquery($query, array($account_number));
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'nickname');
        else
            return '';
    }

}

?>
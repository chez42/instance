<?php

class Omniscient_TransferHouseholds_Action extends Omniscient_Transfer_Action{
    /**
     * Transfer households from v2 to v1
     * @global type $adb
     */
    public function TransferToCRM100(Vtiger_Request $request){
        global $adb;
        $date = $request->get('contact_date');
        $type = 'Accounts';
        $copied_ids = $this->GetCopiedIds();
        $copied_ids = SeparateArrayWithCommas($copied_ids);

        $query = "SELECT * FROM vtiger_crmentity e " 
               . "LEFT JOIN vtiger_account a ON a.accountid = e.crmid "
               . "LEFT JOIN vtiger_accountscf acf ON acf.accountid = a.accountid "
               . "LEFT JOIN vtiger_accountbillads aba ON aba.accountaddressid = a.accountid "
               . "LEFT JOIN vtiger_accountshipads asa ON asa.accountaddressid = a.accountid "
               . "WHERE e.createdtime >= ? AND setype=? AND e.crmid NOT IN ({$copied_ids})";

        $result = $adb->pquery($query, array($date, $type));
        if($adb->num_rows($result) > 0)
            foreach($result AS $k => $v){
                $info[] = $v;
            }

        foreach($info AS $k => $v){
            $new_id = $this->UpdateEntitySequence();
            $this->InsertIntoEntityTable($v, $new_id);
            $this->InsertAccounts($v, $new_id);
        }
        $this->ConnectContacts();
        return "Households Inserted";
    }
    
    /**
     * Insert household accounts into CRM100
     * @global type $adb
     * @param type $info
     * @param type $new_id
     */
    public function InsertAccounts($info, $new_id){
        global $adb;
        $accounts = "INSERT INTO advisorviewcrm100.vtiger_account (accountid,account_no,accountname,parentid,account_type,industry,annualrevenue,rating,ownership,siccode,tickersymbol,phone,otherphone,email1,email2,website,fax,employees,emailoptout,notify_owner)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $adb->pquery($accounts, array($new_id,$info['account_no'],$info['accountname'],$info['parentid'],$info['account_type'],$info['industry'],$info['annualrevenue'],$info['rating'],$info['ownership'],$info['siccode'],$info['tickersymbol'],$info['phone'],$info['otherphone'],$info['email1'],$info['email2'],$info['website'],$info['fax'],$info['employees'],$info['emailoptout'],$info['notify_owner']));
        $cf = "INSERT INTO advisorviewcrm100.vtiger_accountscf (accountid,ssn,contact_partner_id,contact_shared,contact_use_account_address,contact_last_touched_action,old_ucrm_account_id,stickynote,cf_671,cf_672,cf_673,cf_674,account_total_value,account_market_value,account_cash_value,account_annual_revenue,account_bond_value,cf_722,cf_726,cf_729,cf_761,cf_762,cf_763,v2_id)
               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $adb->pquery($cf, array($new_id,$info['ssn'],$info['contact_partner_id'],$info['contact_shared'],$info['contact_use_account_address'],$info['contact_last_touched_action'],$info['old_ucrm_account_id'],$info['stickynote'],$info['cf_671'],$info['cf_672'],$info['cf_673'],$info['cf_674'],$info['account_total_value'],$info['account_market_value'],$info['account_cash_value'],$info['account_annual_revenue'],$info['account_bond_value'],$info['cf_722'],$info['cf_726'],$info['cf_729'],$info['cf_761'],$info['cf_762'],$info['cf_763'],$info['crmid']));
        $bill = "INSERT INTO advisorviewcrm100.vtiger_accountbillads (accountaddressid,bill_city,bill_code,bill_country,bill_state,bill_street,bill_pobox)
                 VALUES (?, ?, ?, ?, ?, ?, ?)";
        $adb->pquery($bill, array($new_id,$info['bill_city'],$info['bill_code'],$info['bill_country'],$info['bill_state'],$info['bill_street'],$info['bill_pobox']));
        $ship = "INSERT INTO advisorviewcrm100.vtiger_accountshipads (accountaddressid,ship_city,ship_code,ship_country,ship_state,ship_pobox,ship_street)
                 VALUES (?, ?, ?, ?, ?, ?, ?)";
        $adb->pquery($ship, array($new_id,$info['ship_city'],$info['ship_code'],$info['ship_country'],$info['ship_state'],$info['ship_pobox'],$info['ship_street']));
        
        $touched = "INSERT INTO copied_ids (crmid) VALUES (?)";
        $adb->pquery($touched, array($info['crmid']));
    }
    
    /**
     * Connect contacts to households
     */
    public function ConnectContacts(){
        global $adb;
        $copied_ids = $this->GetCopiedIds();
        $copied_ids = SeparateArrayWithCommas($copied_ids);
        $query = "SELECT accountid, v2_id FROM advisorviewcrm100.vtiger_accountscf cf "
               . "WHERE cf.v2_id IN ({$copied_ids})";//Get list of all copied accounts

        $result = $adb->pquery($query, array());
        foreach($result AS $k => $v){
            $query = "UPDATE advisorviewcrm100.vtiger_contactdetails cd "
                   . "JOIN advisorviewcrm100.vtiger_contactscf cf ON cf.contactid = cd.contactid "
                   . "SET cd.accountid = ? WHERE cd.accountid = ? AND cf.v2_id IN ({$copied_ids})";
            $adb->pquery($query, array($v['accountid'], $v['v2_id']));
        }
    }
}

?>
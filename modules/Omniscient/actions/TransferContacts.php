<?php

class Omniscient_TransferContacts_Action extends Omniscient_Transfer_Action{
    /**
     * Transfer contacts from v2 to v1
     * @global type $adb
     */
    public function TransferToCRM100(Vtiger_Request $request){
        global $adb;
        $date = $request->get('contact_date');
        $type = 'Contacts';
        $copied_ids = $this->GetCopiedIds();
        $copied_ids = SeparateArrayWithCommas($copied_ids);
//        $info = $this->GetEntityInfo($date, $type);
        $query = "SELECT * FROM vtiger_crmentity e " 
               . "LEFT JOIN vtiger_contactdetails cd ON cd.contactid = e.crmid "
               . "LEFT JOIN vtiger_contactscf cf ON cf.contactid = cd.contactid "
               . "LEFT JOIN vtiger_contactaddress ca ON ca.contactaddressid = cd.contactid "
               . "LEFT JOIN vtiger_contactsubdetails csd ON csd.contactsubscriptionid = cd.contactid "
               . "WHERE e.createdtime >= ? AND setype=? AND e.crmid NOT IN ({$copied_ids})";

        $result = $adb->pquery($query, array($date, $type));
        if($adb->num_rows($result) > 0)
            foreach($result AS $k => $v){
                $info[] = $v;
            }

        foreach($info AS $k => $v){
            $new_id = $this->UpdateEntitySequence();
            $this->InsertIntoEntityTable($v, $new_id);
            $this->InsertContacts($v, $new_id);            
        }
//        $this->FixSubDetails();
        return "Contacts Inserted";
    }
    
    /**
     * Insert contacts into CRM100
     * @global type $adb
     * @param type $info
     * @param type $new_id
     */
    public function InsertContacts($info, $new_id){
        global $adb;
        $details = "INSERT INTO advisorviewcrm100.vtiger_contactdetails (contactid,contact_no, accountid,salutation,firstname,lastname,email,phone,mobile,title,department,fax,reportsto,training,usertype,contacttype,otheremail,secondaryemail,donotcall,emailoptout,imagename,reference,notify_owner)
                    VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $adb->pquery($details, array($new_id, $info['contact_no'],$info['accountid'],$info['salutation'],$info['firstname'],$info['lastname'],$info['email'],$info['phone'],$info['mobile'],$info['title'],$info['department'],$info['fax'],$info['reportsto'],$info['training'],$info['usertype'],$info['contacttype'],$info['otheremail'],$info['secondaryemail'],$info['donotcall'],$info['emailoptout'],$info['imagename'],$info['reference'],$info['notify_owner']));
        $cf = "INSERT INTO advisorviewcrm100.vtiger_contactscf (contactid,ssn,contact_partner_id,contact_shared,contact_use_account_address,contact_last_touched_action,old_ucrm_id,old_creator,old_owner,stickynote,cf_634,cf_635,cf_636,cf_637,cf_638,cf_639,cf_640,cf_641,cf_642,cf_644,contact_exchange_item_id,contact_exchange_change_key,cf_659,cf_660,cf_661,cf_662,cf_663,cf_664,cf_665,cf_666,cf_667,cf_668,cf_675,cf_676,cf_677,cf_678,cf_683,cf_697,cf_698,cf_712,cf_721,cf_724,cf_725,cf_727,cf_732,cf_736,cf_737,cf_764,cf_765,cf_784,cf_785,cf_786,cf_805,cf_806,cf_807,cf_808,cf_809,cf_810,v2_id) "
            . "VALUES(?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?) ";
        $adb->pquery($cf, array($new_id,$info['ssn'],$info['contact_partner_id'],$info['contact_shared'],$info['contact_use_account_address'],$info['contact_last_touched_action'],$info['old_ucrm_id'],$info['old_creator'],$info['old_owner'],$info['stickynote'],$info['cf_634'],$info['cf_635'],$info['cf_636'],$info['cf_637'],$info['cf_638'],$info['cf_639'],$info['cf_640'],$info['cf_641'],$info['cf_642'],$info['cf_644'],$info['contact_exchange_item_id'],$info['contact_exchange_change_key'],$info['cf_659'],$info['cf_660'],$info['cf_661'],$info['cf_662'],$info['cf_663'],$info['cf_664'],$info['cf_665'],$info['cf_666'],$info['cf_667'],$info['cf_668'],$info['cf_675'],$info['cf_676'],$info['cf_677'],$info['cf_678'],$info['cf_683'],$info['cf_697'],$info['cf_698'],$info['cf_712'],$info['cf_721'],$info['cf_724'],$info['cf_725'],$info['cf_727'],$info['cf_732'],$info['cf_736'],$info['cf_737'],$info['cf_764'],$info['cf_765'],$info['cf_784'],$info['cf_785'],$info['cf_786'],$info['cf_805'],$info['cf_806'],$info['cf_807'],$info['cf_808'],$info['cf_809'],$info['cf_810'],$info['crmid']));
        $address = "INSERT INTO advisorviewcrm100.vtiger_contactaddress (contactaddressid,mailingcity,mailingstreet,mailingcountry,othercountry,mailingstate,mailingpobox,othercity,otherstate,mailingzip,otherzip,otherstreet,otherpobox) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $adb->pquery($address, array($new_id,$info['mailingcity'],$info['mailingstreet'],$info['mailingcountry'],$info['othercountry'],$info['mailingstate'],$info['mailingpobox'],$info['othercity'],$info['otherstate'],$info['mailingzip'],$info['otherzip'],$info['otherstreet'],$info['otherpobox']));
        $sub = "INSERT INTO advisorviewcrm100.vtiger_contactsubdetails (contactsubscriptionid,homephone,otherphone,assistant,assistantphone,birthday,laststayintouchrequest,laststayintouchsavedate,leadsource) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $adb->pquery($sub, array($new_id,$info['homephone'],$info['otherphone'],$info['assistant'],$info['assistantphone'],$info['birthday'],$info['laststayintouchrequest'],$info['laststayintouchsavedate'],$info['leadsource']));
        $touched = "INSERT INTO copied_ids (crmid) VALUES (?)";
        $adb->pquery($touched, array($info['crmid']));
    }
    
    public function FixSubDetails(){
        global $adb;
        $copied_ids = $this->GetCopiedIds();
        $copied_ids = SeparateArrayWithCommas($copied_ids);
        $query = "SELECT contactid, v2_id FROM advisorviewcrm100.vtiger_contactscf cf "
               . "WHERE cf.v2_id IN ({$copied_ids})";
        $result = $adb->pquery($query, array());
        foreach($result AS $k => $v){
            $query = "SELECT * FROM vtiger_contactsubdetails WHERE contactsubscriptionid=?";
            $result = $adb->pquery($query, array($v['v2_id']));
            foreach($result AS $a => $info){
                $sub = "INSERT INTO advisorviewcrm100.vtiger_contactsubdetails (contactsubscriptionid,homephone,otherphone,assistant,assistantphone,birthday,laststayintouchrequest,laststayintouchsavedate,leadsource) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $adb->pquery($sub, array($v['contactid'],$info['homephone'],$info['otherphone'],$info['assistant'],$info['assistantphone'],$info['birthday'],$info['laststayintouchrequest'],$info['laststayintouchsavedate'],$info['leadsource']));
            }
        }        
    }
}

?>
<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class OmniCal_ConvertEws_Model extends Vtiger_Module_Model{
    
    public function __construct() {
    }
    
    /**
     * Get the old ID's from the CRM
     * @global type $adb
     * @param type $user_id
     * @return type
     */
    public static function GetOldIDs($user_id){
        global $adb;
        $query = "SELECT cf.activityid, task_exchange_item_id, task_exchange_change_key
                  FROM vtiger_activitycf cf 
                  JOIN vtiger_crmentity e ON e.crmid = cf.activityid
                  WHERE e.smownerid = ?
                  AND cf.task_exchange_item_id != '' AND cf.task_exchange_item_id IS NOT NULL";
        $result = $adb->pquery($query, array($user_id));
        $ids = array();
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $tmp = array("crmid"=>$v['activityid'],
                             "exchange_id"=>$v['task_exchange_item_id'],
                             "ck"=>$v['task_exchange_change_key']);
                $ids[] = $tmp;
            }
        }
        return $ids;
    }
    
    /**
     * Convert ID's for the given user email
     * @param array $ids
     * @param type $impersonation_name
     * @param type $email
     */
    public static function ConvertIDs(array $old_ids, $impersonation_name, $email){
        $converter = new OmniCal_ExchangeEws_Model();
        $converter->SetImpersonation($impersonation_name);
        
        $id_info = new EWSType_ConvertIdType();
        $id_info->SourceIds = new EWSType_NonEmptyArrayOfAlternateIdsType();
        $id_info->DestinationFormat = "EwsId";
        
        $count = 0;
        foreach($old_ids AS $k => $v){
            $id = new EWSType_AlternateIdType();
            $id->Format = 'EwsLegacyId';
            $id->Id = $v['exchange_id'];
            $id->Mailbox = $email;
            $id_info->SourceIds->AlternateId[] = $id;
        }

        echo "<br /><br /><strong>CONVERTING IDS FOR {$impersonation_name}</strong><br /><br />";
        
        $count = 0;
        try{
            $response = $converter->ews->ConvertId($id_info);
        } catch (Exception $ex){
            echo "<strong>FAILED CONVERSION FOR {$impersonation_name}</strong><br />";
            return;
        }
        try{
            $conversion_values = array();
            if(is_array($response->ResponseMessages->ConvertIdResponseMessage)){
                foreach($response->ResponseMessages->ConvertIdResponseMessage AS $k => $v){
                    if($v->ResponseCode == "NoError"){
                        $convert = array('crmid'=>$old_ids[$count]['crmid'],
                                         'old_key'=>$old_ids[$count]['exchange_id'],
                                         'new_key'=>$v->AlternateId->Id);
                        echo $old_ids[$count]['crmid'] . ": " . $old_ids[$count]['exchange_id'] . " - Converts to - <br />";
                        echo $v->AlternateId->Id . "<br /><br />";
                        $conversion_values[] = $convert;
                    }
                    $count++;
                }
                self::RunConversion($conversion_values);
            } else{
                    if($response->ResponseMessages->ConvertIdResponseMessage->ResponseCode == "NoError"){
                        $convert = array('crmid'=>$old_ids[$count]['crmid'],
                                         'old_key'=>$old_ids[$count]['exchange_id'],
                                         'new_key'=>$v->AlternateId->Id);
                        echo $old_ids[$count]['crmid'] . ": " . $old_ids[$count]['exchange_id'] . " - Converts to - <br />";
                        echo $response->ResponseMessages->ConvertIdResponseMessage->AlternateId->Id . "<br /><br />";
                        $conversion_values[] = $convert;
                    }
                    self::RunConversion($conversion_values);
            }
        } catch (Exception $ex) {
            echo "<br /><br /><strong>EXCEPTION ERROR FOR {$impersonation_name}</strong><br /><br />";
        }
        return $response;
    }
    
    private function RunConversion(array $conversion_values){
        global $adb;
        if(sizeof($conversion_values) > 0){
            $query_start = " INSERT INTO exchange_ews_keys (crmid, old_key, new_key) VALUES ";
            $query_continue = ' ';
            $query_end = " ON DUPLICATE KEY UPDATE crmid=VALUES(crmid), old_key=VALUES(old_key), new_key=VALUES(new_key) ";
            $count = 0;
            foreach($conversion_values AS $k => $v){
                $count++;
                if($count < sizeof($conversion_values))
                    $query_continue .= " ('{$v['crmid']}', '{$v['old_key']}', '{$v['new_key']}'), ";
                else
                    $query_continue .= " ('{$v['crmid']}', '{$v['old_key']}', '{$v['new_key']}') ";
            }
            $full_query = $query_start . $query_continue . $query_end;
            $adb->pquery($full_query, array());
            
            $query = " UPDATE vtiger_activitycf cf
                       JOIN exchange_ews_keys k ON cf.activityid = k.crmid
                       SET cf.task_exchange_item_id = k.new_key";
            $adb->pquery($query, array());
        }
    }
    
    /**
     * Returns the required info from the user table (id, name) to use with ldap.  This is needed to get the user's email address used in exchange
     * @global type $adb
     * @return type
     */
    public static function GetUserConversionRequirementsAll(){
        global $adb;
        $query = "SELECT id, user_name 
                  FROM vtiger_users
                  WHERE exchange_enabled = 1";
        $result = $adb->pquery($query, array());
        $user_info = array();
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $user = array("user_id" => $v['id'],
                              "user_name" => $v['user_name']);
                $user_info[] = $user;
            }
        }
        return $user_info;
    }

    /**
     * Get the conversion requirements for an individual user
     * @global type $adb
     * @param type $user_name
     * @return type
     */
    public static function GetUserConversionRequirementsForSingleUser($user_name){
        global $adb;
        $query = "SELECT id, user_name 
                  FROM vtiger_users
                  WHERE exchange_enabled = 1
                  AND user_name = ?";
        $result = $adb->pquery($query, array($user_name));
        $user_info = array();
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $user = array("user_id" => $v['id'],
                              "user_name" => $v['user_name']);
                $user_info[] = $user;
            }
        }
        return $user_info;
    }
}

?>
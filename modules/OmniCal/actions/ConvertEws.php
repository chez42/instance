<?php

require_once 'include/ldap/config.ldap.php';
require_once 'include/ldap/Ldap.php';
require_once('include/ldap/adLdap.php');

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class OmniCal_ConvertEws_Action extends Vtiger_BasicAjax_Action{
    
    public function __construct() {
    }

    public function ConvertEws(array $user_info){
        global $AUTHCFG;
        $adldap = new adLDAP($AUTHCFG);

        foreach($user_info AS $k => $v){
            $user_info = $adldap->user_info($v['user_name']);
            $email = $user_info[0]['mail'][0];            
            $old_ids = OmniCal_ConvertEws_Model::GetOldIDs($v['user_id']);
            $new_ids = OmniCal_ConvertEws_Model::ConvertIDs($old_ids, $v['user_name'], $email);
        }
    }
    
    public function process(\Vtiger_Request $request) {

    }
}

?>
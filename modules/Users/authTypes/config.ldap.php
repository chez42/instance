<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * @Contributor - Elmue 2008
 ************************************************************************************/

// Login Type may be: 'LDAP' or 'AD' or 'SQL'
// Use 'SQL' to login using the passwords stored in the vTiger Sql database
$AUTHCFG['authType']      = 'AD';

// ----------- Configuration LDAP -------------
$AUTHCFG['ldap_host']     = 'vmserver6.concertglobal.com';	//system where ldap is running (e.g. ldap://localhost)
$AUTHCFG['ldap_port']     = '389';				//port of the ldap service

// The LDAP branch which stores the User Information
// This branch may have subfolders. PHP will search in all subfolders.
$AUTHCFG['ldap_basedn']   = 'dc=concertglobal,dc=com';

// The account on the LDAP server which has permissions to read the branch specified in ldap_basedn
//$AUTHCFG['ldap_username'] = 'cn=admin,dc=localhost,dc=localdomain';   // set = NULL if not required
//$AUTHCFG['ldap_username'] = 'cn=concertadmin,dc=vmserver6,dc=concertglobal,dc=com';   // set = NULL if not required
$AUTHCFG['ldap_username'] = 'cn=concertadmin,cn=users,dc=concertglobal,dc=com';   // set = NULL if not required

$AUTHCFG['ldap_pass']     = 'Consec1'; // set = NULL if not required

// Predefined LDAP fields (these settings work on Win 2003 Domain Controler)
$AUTHCFG['ldap_objclass']    = 'objectClass';
# $AUTHCFG['ldap_account']     = 'cn';
$AUTHCFG['ldap_account']     = 'sAMAccountName';
$AUTHCFG['ldap_forename']    = 'givenName';
$AUTHCFG['ldap_lastname']    = 'sn';
$AUTHCFG['ldap_fullname']    = 'cn'; // or "name" or "displayName"
$AUTHCFG['ldap_email']       = 'mail';
$AUTHCFG['ldap_tel_work']    = 'telephoneNumber';
$AUTHCFG['ldap_tel_mobile']    = 'mobile';
$AUTHCFG['ldap_department']  = 'physicalDeliveryOfficeName';
$AUTHCFG['ldap_department']  = 'department';
$AUTHCFG['ldap_description'] = 'description';
$AUTHCFG['ldap_manager'] = 'manager';
$AUTHCFG['sql_accounts'] 	 = array("admin");	//the users whose authentication will be from database instead of from ldap

// Required to search users: the array defined in ldap_objclass must contain at least one of the following values
//$AUTHCFG['ldap_userfilter']  = 'user|person|organizationalPerson|account';
$AUTHCFG['ldap_userfilter']  = 'user';

//SEAN TSANG PATCH START - Users can change their own password or not.
$AUTHCFG['ldap_allowpasswordchange'] = true;
//$AUTHCFG['ldap_admin'] = 'cn=admin,dc=example,dc=com';  //Change to NULL if admin do not need to change own password through vTiger
$AUTHCFG['ldap_admin'] = 'cn=concertadmin,dc=concertglobal,dc=com';  //Change to NULL if admin do not need to change own password through vTiger
$AUTHCFG['ldap_adminpwd'] = 'Consec1';  //Change to NULL if admin do not need to change own password through vTiger
//SEAN TSANG PATCH END----------------------------------------------------------

// ------------ Configuration AD (Active Directory) --------------

$AUTHCFG['ad_accountSuffix'] = '@concertglobal.com';
$AUTHCFG['ad_basedn']        = 'dc=concertglobal,dc=com';
$AUTHCFG['ad_dc']            = 'vmserver6.concertglobal.com'; //array of domain controllers
//$AUTHCFG['ad_dc']            = 'vmserver6.concertglobal.com,lanserver1.concertglobal.com'; //array of domain controllers
$AUTHCFG['ad_username']      = 'concertadmin'; //optional user/pass for searching
$AUTHCFG['ad_password']          = 'Consec1';
$AUTHCFG['ad_realgroup']     = true; //AD does not return the primary group.  Setting this to false will fudge "Domain Users" and is much faster.  True will resolve the real primary group, but may be resource intensive.

$AUTHCFG['account_suffix'] = "@concertglobal.com";
$AUTHCFG['base_dn'] = 'dc=concertglobal,dc=com';
$AUTHCFG['domain_controllers'] = array('vmserver6.concertglobal.com','vmserver7.concertglobal.com');
//$AUTHCFG['domain_controllers'] = array('vmserver6.concertglobal.com','lanserver1.concertglobal.com');
//$AUTHCFG['ad_username'] = 'concertadmin';
//$AUTHCFG['ad_password'] = 'Consec1';
$AUTHCFG['use_ssl'] = false;

// #########################################################################
?>


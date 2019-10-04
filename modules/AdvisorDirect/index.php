<?php

global $currentModule;
global $current_user;

require_once('modules/Trading/Trading.php');
require_once('Smarty_setup.php');
require_once("data/Tracker.php");
require_once('include/logging.php');
require_once('include/ListView/ListView.php');
require_once('include/utils/utils.php');
require_once('modules/CustomView/CustomView.php');
require_once('include/database/Postgres8.php');
require_once('modules/AdvisorDirect/classes/cAdvisorDirect.php');
include_once('modules/Users/Users.php');

$attachment_id = $_REQUEST['attachments'];

//$result = mysql_query('SELECT * FROM vtiger_users ORDER BY last_name ASC')
//or die(mysql_error());  

$ad = new cAdvisorDirect();
$name = getUserFullNameFirstLast($current_user->id);
$custodians = $ad->GetCustodianList();
$email = getUserEmail($current_user->id);
$smarty = new vtigerCRM_Smarty;

$smarty->assign("ATTACHMENT_ID", $attachment_id);
$smarty->assign('AUTHORIZED',1);
$smarty->assign('USERS',$users);
$smarty->assign('APPROVED_USERS',$approved_users);

$smarty->assign('CUSTODIANS', $custodians);
$smarty->assign('EMAIL', $email);
$smarty->assign('USER_NAME', $name);
$smarty->display('AdvisorDirect.tpl');


?>



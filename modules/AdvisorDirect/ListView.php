<?php

require_once('modules/Trading/Trading.php');
require_once('Smarty_setup.php');
require_once("data/Tracker.php");
require_once('include/logging.php');
require_once('include/ListView/ListView.php');
require_once('include/utils/utils.php');
require_once('modules/CustomView/CustomView.php');
require_once('include/database/Postgres8.php');

$focus = new cTrading();
$smarty = new vtigerCRM_Smarty;

$users = $focus->GetUnSubscribedList();
$approved_users = $focus->GetSubscribedList();

$smarty->assign("AUTHORIZED",1);
$smarty->assign("USERS",$users);
$smarty->assign("APPROVED_USERS",$approved_users);
$smarty->display("trading.tpl");

?>

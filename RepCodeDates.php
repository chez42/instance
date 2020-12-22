<?php
$Vtiger_Utils_Log = true;
define('VTIGER6_REL_DIR', '');
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once 'includes/main/WebUI.php';
include_once 'libraries/custodians/cCustodian.php';

$adb = PearDatabase::getInstance();

$custodian = $_GET['custodian'];
$dateField = $_GET['date_field'];

cCustodian::UpdateLatestPositionsTable($custodian, $dateField);
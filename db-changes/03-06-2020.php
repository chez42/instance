<?php
$Vtiger_Utils_Log = true;

chdir('../');

include_once 'includes/main/WebUI.php';

$moduleIns = Vtiger_Module_Model::getInstance('Notifications');
$fieldIns = Vtiger_Field_Model::getInstance('related_to', $moduleIns);
$fieldIns->setrelatedmodules(array('Calendar'));
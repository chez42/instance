<?php
$Vtiger_Utils_Log = true;

chdir('../');

include_once 'includes/main/WebUI.php';

$adb = PearDatabase::getInstance();

$handlers = array("cron/modules/Custodian/TDPull.service",
                  "cron/modules/Custodian/FidelityPull.service",
                  "cron/modules/Custodian/SchwabPull.service",
                  "cron/modules/Custodian/PershingPull.service",
                  "cron/modules/InstanceOnly/HomepageWidgets.service",
                  "cron/modules/PortfolioInformation/WeightCalculations.service");
$questions = generateQuestionMarks($handlers);
$params = array();
$params[] = $handlers;
$query = "UPDATE vtiger_cron_task 
          SET laststart = 0, lastend = 0
          WHERE handler_file IN ({$questions})";
$adb->pquery($query, $params);
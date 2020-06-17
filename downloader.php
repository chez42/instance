<?php
include("includes/main/WebUI.php");

$custodian = $_REQUEST['custodian'];
$rep_code = $_REQUEST['repcode'];
$filename = $_REQUEST['filename'];

PortfolioInformation_Administration_Action::WriteDownloaderData($custodian, $rep_code, $filename);
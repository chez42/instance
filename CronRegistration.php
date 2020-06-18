<?php

include_once "vtlib/Vtiger/Cron.php";
Vtiger_Cron::register("Monthly Revenue", "modules/PortfolioInformation/cron/MonthlyRevenue.service", 86400, "PortfolioInformation", 1, 23, "Monthly intervals for all accounts");

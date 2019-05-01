<?php
if (ob_get_level() == 0) ob_start();

require_once("libraries/reports/new/nAuditing.php");

/*
require_once("libraries/reports/cTransactions.php");
require_once("libraries/reports/Portfolios.php");
require_once("libraries/reports/editing/TransactionsBridge.php");
require_once("include/utils/cron/cPortfolioAccess.php");
require_once("include/utils/cron/cPricingAccess.php");
require_once("include/utils/cron/cSecuritiesAccess.php");
require_once("include/utils/cron/cAdvisorAccess.php");
*/

class PortfolioInformation_SandBox_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        global $adb;
        set_time_limit (0);
        ini_set('memory_limit', '2048M');

        $asset_allocation = new PortfolioInformation_AssetAllocation_Action();
$box = array('Q3T000000', 
'Q3T001003',
'Q3T001011',
'Q3T001029',
'Q3T001037',
'Q3T001052',
'Q3T001060',
'Q3T001078',
'Q3T001086',
'Q3T001094',
'Q3T001102',
'Q3T001110',
'Q3T001128',
'Q3T001144',
'Q3T001151',
'Q3T001169',
'Q3T001177',
'Q3T001185',
'Q3T001193',
'Q3T001201',
'Q3T008016',
'Q3T897772',
'Q3T999982');
foreach($box AS $k => $v){
$crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($v);
$asset_allocation->UpdateIndividualAccount($crmid);
}
        echo "All done<br />";
    }
}
?>

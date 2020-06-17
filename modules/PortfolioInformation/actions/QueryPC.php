<?php

require_once("include/utils/cron/cPortfolioCenter.php");
require_once("include/utils/cron/cPortfolioAccess.php");
class PortfolioInformation_QueryPC_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
/*        $pc = new cPortfolioCenter();
        $result = $pc->QueryPC("SELECT * FROM Portfolios WHERE AccountNumber = '675-856118'");
        foreach($result AS $k => $v){
            echo $v['ClosedAccountFlag'];
        }*/
        $a = new cPortfolioAccess();
        $info = $a->CloseAccounts();
        print_r($info);
    }
}
/* 
SELECT *
  FROM [PortfolioCenter].[dbo].[Portfolios]
  WHERE AccountNumber = '675-856118'
*/

?>
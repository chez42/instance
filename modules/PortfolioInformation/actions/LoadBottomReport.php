<?php

class PortfolioInformation_LoadBottomReport_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
		$account_number = $request->get('account_number');
		$t = new PortfolioInformation_PCQuery_Model();
		$pc = $t->DoesAccountExistInPC($account_number);
        $report_display = $request->get("report_display");
        switch($report_display){
            case "holdings":
            	if($pc)
	                $info = new PortfolioInformation_Positions_View();
				else
					$info = new PortfolioInformation_HoldingsReport_View();
                break;
            case "monthly_income":
                $info = new PortfolioInformation_MonthlyIncome_View();
                break;
            case "performance":
                $info = new PortfolioInformation_Performance_View();
                break;
            case "transactions":
                $info = new PortfolioInformation_Transactions_View();
                break;
            case "overview":
                $info = new PortfolioInformation_Overview_View();
                break;
            default:
                echo "Generation Error";
                break;
        }
        echo $info->process($request);
    }
}
?>
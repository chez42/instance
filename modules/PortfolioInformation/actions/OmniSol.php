<?php

class PortfolioInformation_OmniSol_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $omniSol = new PortfolioInformation_OmniSol_Model();
        switch (strtolower($request->get('todo'))) {
            case 'compare_status':
                $sdate = $request->get('start');
                $edate = $request->get('end');
                $td = $omniSol->AccountCompareCount("custodian_omniscient.custodian_balances_td", "as_of_date", "as_of_date", $sdate, $edate);
                $schwab = $omniSol->AccountCompareCount("custodian_omniscient.custodian_balances_schwab", "as_of_date", "as_of_date", $sdate, $edate);
                $pershing = $omniSol->AccountCompareCount("custodian_omniscient.custodian_balances_pershing", "date", "date", $sdate, $edate);
                $fidelity = $omniSol->AccountCompareCount("custodian_omniscient.custodian_balances_fidelity", "as_of_date", "as_of_date", $sdate, $edate);
                $results = ["td" => $td,
                            "pershing" => $pershing,
                            "schwab" => $schwab,
                            "fidelity" => $fidelity];
                echo json_encode($results);
                break;
        }
    }
}



/*
    $omnisol = new PortfolioInformation_OmniSol_Model();
    $omnisol->AccountCompareCount(custodian_omniscient.custodian_balances_td, "as_of_date", "as_of_date", );
 */
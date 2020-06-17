<?php

class PortfolioInformation_LoadTopReport_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        $info = new PortfolioInformation_ReportTop_View();
        echo $info->process($request);
    }
}
?>

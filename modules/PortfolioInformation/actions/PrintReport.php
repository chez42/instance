<?php
class PortfolioInformation_PrintReport_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        $pdf_report = new PortfolioInformation_PrintReport_View();
        $pdf = $pdf_report->process($request);
        $pdf->DownloadPDF($pdf->pdf_name);
    }
}
?>
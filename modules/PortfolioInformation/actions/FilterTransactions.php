<?php

class PortfolioInformation_FilterTransactions_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        $transactions_model = new PortfolioInformation_TransactionsNavigation_Model();
        $transactions = $transactions_model->GetFilteredTransactions($request);

        $viewer = new Vtiger_Viewer();
        $viewer->assign("TRANSACTIONS", $transactions);
        $output = $viewer->view('TransactionsList.tpl', "PortfolioInformation", true);
        echo $output;
    }
}
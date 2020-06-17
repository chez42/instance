<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2017-06-22
 * Time: 3:05 PM
 */

class PortfolioInformation_PortfoliosReset_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        switch($request->get('todo')){
            case 'ResetAccounts':
                require_once("include/utils/cron/cTransactionsAccess.php");
                $results = array();
                $failed = array();
                $success = array();
                $interaction = new PortfolioInformation_ManualInteractions_Model();
                $transaction_count = 0;
                $accounts = $request->get('account_numbers');
                $accounts = array_unique($accounts);
                foreach($accounts AS $k => $v){
                    $r = $interaction->ResetAccountTransactions($v);
                    PortfolioInformation_Module_Model::SetPCTransactionsTransferredToNo(str_replace("-", '', $v));
                    if(is_numeric($r)){
                        $transaction_count += $r;
                        $custodian = PortfolioInformation_Module_Model::GetCustodianFromAccountNumber($v);
                        PortfolioInformation_Module_Model::CreateTransactionsFromPCCloud($custodian, $v);
                        $success[$v] = "Account #{$v}: {$r} transactions pulled";
                    }
                    else{
                        $failed[] = "{$v} FAILED WITH MESSAGE: {$r}... This is an indication of not existing in an external source and not necessarily a true failure\r\n";
                    }
                }
                $results = array('transactions_count' => $transaction_count,
                    'success' => $success,
                    'failed' => $failed);
                echo json_encode($results);
                break;
        }
    }
}

?>
<?php
include("includes/main/WebUI.php");

$rep_codes = PortfolioInformation_Module_Model::GetRepCodeListFromUsersTable();
$account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersFromRepCodeOpenAndClosed($rep_codes);
$count = 1;
foreach($account_numbers AS $k => $v){
#            if($count < 10) {
    $account_number = array($v);
    $tmp = new CustodianClassMapping($account_number);
    $tmp->portfolios::UpdateAllPortfoliosForAccounts($account_number);
    $tmp->positions::UpdateAllCRMPositionsAtOnceForAccounts($account_number);
    $tmp->transactions::CreateNewTransactionsForAccounts($account_number);
    if (PortfolioInformation_Module_Model::getInstanceSetting("update_transactions", 1) == 1)
        $tmp->transactions::UpdateTransactionsForAccounts($account_number);
    $count++;
    echo "Finished " . $v . '<br />';
#            }
}
echo 'done' . $count;
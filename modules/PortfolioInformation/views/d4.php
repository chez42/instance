<?php
echo "<strong>START TIME: " . date("m-d-Y H:i:s") . "</strong><br />";
ob_flush();
flush();

$url = "https://veoapi.advisorservices.com/InstitutionalAPIv2/api";

$users = Trading_Ameritrade_Model::GetAmeritradeUsersInformation();
if($users) {
    foreach ($users AS $a => $b) {
        $td = new Trading_Ameritrade_Model($b['userid'], $b['password']);

        $data = $td->GetPositions($url, null, 'E');
        foreach ($data->model->getPositionsJson->position AS $k => $v) {
            ModSecurities_Module_Model::UpdateSecurityInformationTD($v);
        }
    }
}

echo "<strong>FINISHED EQUITY TIME: " . date("m-d-Y H:i:s") . "</strong><br />";
ob_flush();
flush();

exit;


$trade = new Trading_Ameritrade_Model();
$accounts = PortfolioInformation_ConvertCustodian_Model::GetMissingTDAccountsFromBalances();
$max = 9000;
$interval = 3000;
$x = 1;
$tmp = $trade->GetAllAccounts("https://veoapi.advisorservices.com/InstitutionalAPIv2/api", $accounts, 1, 300);
foreach ($tmp['model']['getAccountsJson']['account'] AS $k => $v) {
    if (PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($v['accountNumber']) == 0) {
        $recordModel = PortfolioInformation_Record_Model::getCleanInstance("PortfolioInformation");
        $data = $recordModel->getData();
        $data['account_number'] = $v['accountNumber'];
        $data['description'] = $v['accountDescription'];
        $data['account_title1'] = $v['accountTitle'];
        $data['account_type'] = $v['accountType'];
        $data['production_number'] = $v['repCode'];
        $data['first_name'] = $v['firstName'];
        $data['last_name'] = $v['lastName'];
        $data['address1'] = $v['address1'];
        $data['address2'] = $v['address2'];
        $data['city'] = $v['city'];
        $data['state'] = $v['state'];
        $data['zip'] = $v['zip'];
        $data['origination'] = 'td';

        $recordModel->setData($data);
        $recordModel->set('mode', 'create');
        $recordModel->save();
    }
}

global $adb;
$query = "CALL COPY_TD_PORTFOLIOS_FROM_CRM_TO_CLOUD()";
$adb->pquery($query, array());


echo "<strong>COPY SECTION COMPLETE: " . date("m-d-Y H:i:s") . "</strong><br />";
ob_flush();
flush();


$url = "https://veoapi.advisorservices.com/InstitutionalAPIv2/api";

$users = Trading_Ameritrade_Model::GetAmeritradeUsersInformation();
if($users) {
    foreach ($users AS $a => $b) {
        $td = new Trading_Ameritrade_Model($b['userid'], $b['password']);
        $data = $td->GetPositions($url, null, 'B');
        foreach ($data->model->getPositionsJson->position AS $k => $v) {
            ModSecurities_Module_Model::UpdateSecurityInformationTD($v);
        }

        $data = $td->GetPositions($url, null, 'O');
        foreach ($data->model->getPositionsJson->position AS $k => $v) {
            ModSecurities_Module_Model::UpdateSecurityInformationTD($v);
        }

        $data = $td->GetPositions($url, null, 'F');
        foreach ($data->model->getPositionsJson->position AS $k => $v) {
            ModSecurities_Module_Model::UpdateSecurityInformationTD($v);
        }

        $data = $td->GetPositions($url, null, 'E');
        foreach ($data->model->getPositionsJson->position AS $k => $v) {
            ModSecurities_Module_Model::UpdateSecurityInformationTD($v);
        }

        $data = $td->GetPositions($url, null, 'M');
        foreach ($data->model->getPositionsJson->position AS $k => $v) {
            ModSecurities_Module_Model::UpdateSecurityInformationTD($v);
        }
    }
}

echo "<strong>AMERITRADE SECTION COMPLETE: " . date("m-d-Y H:i:s") . "</strong><br />";
ob_flush();
flush();


####ModSecurities_Module_Model::AutoUpdateEmptySecurities();//Any empty security type in ModSecurities will hit Yahoo to try and get filled in
Transactions_ConvertCustodian_Model::ReassignTransactions();//Assign transactions that currently belong to admin to the owner of the portfolio they are associated with
Transactions_ConvertCustodian_Model::UpdateRepCodes();
##WHY ARE WE DELETING POSITIONS WHEN WE CLOSE THEM NEXT##PositionInformation_ConvertCustodian_Model::SetPositionsDeletedForClosedAccounts();//Mark positions as deleted for accounts that are closed
PositionInformation_Module_Model::ClosePositions();//Close positions for accounts that are closed

echo "<strong>REST OF IT COMPLETE: " . date("m-d-Y H:i:s") . "</strong><br />";
ob_flush();
flush();

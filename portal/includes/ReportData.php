<?php
include_once('config.php');
if(isset($_REQUEST['todo']) && $_REQUEST['todo'] != ''){
    $todo = $_REQUEST['todo'];
    $accounts = explode(',', $_REQUEST['account_numbers']);
    switch(strtolower($todo)){
        case "endvalues":
            $result = PortfolioInformation_Module_Model::GetEndValuesForAccounts($accounts);
            echo json_encode($result);
            break;
        case "endvaluesdaily":
            $result = PortfolioInformation_Module_Model::GetEndValuesForAccounts($accounts, null, null, "Daily");
            echo json_encode($result);
            break;
    }
}
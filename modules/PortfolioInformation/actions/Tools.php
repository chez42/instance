<?php
require_once("libraries/CloudAccess/ParsingGuzzle.php");
require_once("libraries/CloudAccess/PushGuzzle.php");

class PortfolioInformation_Tools_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        $todo = strtolower($request->get('todo'));
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $accounts = $request->get('account_numbers');
        $rep_codes = $request->get('rep_codes');
        $rep_code = $request->get('rep_code');
        $inception = $request->get('inception');
        $custodian = $request->get('custodian');
        $parse_type = $request->get('parse_type');
        $push_type = $request->get("push_type");
        $num_days = $request->get('num_days');
        $file_type = $request->get('file_type');
        $file_info_date = $request->get('file_info_date');
        $file_sdate = date("Y-m-d", strtotime($request->get('file_sdate')));
        $file_edate = date("Y-m-d", strtotime($request->get('file_edate')));

        switch($todo){
            case "daily_intervals":
                $account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersFromRepCode($rep_codes);
                if($account_numbers != 0) {
                    foreach($account_numbers AS $k => $v){
                        $inception_date = PortfolioInformation_Module_Model::GetInceptionBalanceDateForAccountNumber($v);
                        PortfolioInformation_Module_Model::CalculateDailyIntervalsForAccounts(array($v), $inception_date);
//                            echo $v . ' - ' . $inception_date . '<br />';
                    }
                }
            break;
            case "parse_files":
                $guz = new cParseGuzzle($custodian, $parse_type, $num_days, 0);
                $guz->parseFiles();
                echo "Finished Parse";
            break;
            case "push_files":
                $guz = new cPushGuzzle($custodian, $push_type);
                $guz->pushFiles();
                echo "Finished Push";
            break;
            case "find_missing":
                $extensions = PortfolioInformation_Tools_Model::GetExtensionsFromType($file_type);
                $missing = PortfolioInformation_Tools_Model::GetMissingFiles($extensions, $file_sdate, $file_edate);
                echo json_encode($missing);
            break;
            case "get_rep_codes_for_date":
                $date = PortfolioInformation_Tools_Model::GetRepCodesWithFilesFromDate($file_info_date);
//                $file_info = PortfolioInformation_Tools_Model::GetFileInfoFromTypeAndDates("positions", '2020-02-01', '2020-02-31');
            break;
            case "get_rep_codes_for_dates"://Gets the rep codes from the parsing_directory_structure table that were parsed within passed in dates
                $rep_codes = PortfolioInformation_Tools_Model::GetRepCodesWithFilesFromDates($start_date, $end_date);
                echo json_encode($rep_codes);
            break;
            case "get_active_rep_codes":
                $rep_codes = PortfolioInformation_Tools_Model::GetActiveRepCodes();
                echo json_encode($rep_codes);
            break;
            case "get_file_info":
                $file_info = PortfolioInformation_Tools_Model::GetFileInfoFromTypeAndDates("positions", $start_date, $end_date);
                echo json_encode($file_info);
            break;
            case "get_rep_code_files":
                $file_info = PortfolioInformation_Tools_Model::GetRepCodeFileInfo($rep_code, $start_date, $end_date);
                echo json_encode($file_info);
            break;
            case "remove_intervals":
                $accounts = explode(",", $request->get("account_numbers"));
                $accounts = array_unique($accounts);
                PortfolioInformation_Module_Model::RemoveIntervals($accounts);
#                print_r($accounts);
                echo json_encode("Intervals Removed.... ");
        }
    }
}
/*
SELECT *
  FROM [PortfolioCenter].[dbo].[Portfolios]
  WHERE AccountNumber = '675-856118'
*/

?>
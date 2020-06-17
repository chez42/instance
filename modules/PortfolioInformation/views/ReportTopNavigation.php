<?php
require_once("include/utils/omniscientCustom.php");
require_once("libraries/reports/cReports.php");
require_once("libraries/reports/pdf/cPDFDBAccess.php");
require_once("libraries/reports/new/nExpense.php");

class PortfolioInformation_ReportTopNavigation_View extends Vtiger_BasicAjax_View{
    
    function process(Vtiger_Request $request, &$return_accounts=null) {
        global $current_user, $adb;
        $viewer = $this->getViewer($request);
        $viewer->assign("user_id", $current_user->id);
        $viewer->assign("URL", $url);

        $instance = $request->get('instance');
        $acct = $request->get('acct');

        $report = new cReports($instance, $acct);
        $account_numbers_pid = GetPortfolioAccountNumbersFromPids($report->pids);
        $account_numbers_ssn = GetPortfolioAccountNumbersFromSSN($report->ssn);
        $account_numbers_contact = GetPortfolioAccountNumbersFromContactID($acct);

        $focus = CRMEntity::getInstance('Accounts');
        $entityIds = $focus->getRelatedContactsIds($acct);

        $account_numbers_household = GetPortfolioAccountNumbersFromContactID($entityIds);

        $to_loop = array();
        $to_loop[] = $account_numbers_pid;
        $to_loop[] = $account_numbers_ssn;
        $to_loop[] = $account_numbers_contact;
        $to_loop[] = $account_numbers_household;

        $account_numbers = array();
        foreach($to_loop AS $arr){
            if(is_array($arr)){
                $account_numbers = array_merge($account_numbers, $arr);
            }
        }

        $account_numbers = array_unique($account_numbers);

/*        if(is_array($account_numbers))
            $account_numbers = array_merge($account_numbers, $account_numbers_ssn, $account_numbers_contact);
       else
            $account_numbers = $account_numbers_ssn;
*/

        $questions = generateQuestionMarks($account_numbers);
        $totals = array();
        $live_accounts = array();
        if(!empty($account_numbers)) {
            $query = "SELECT * FROM vtiger_portfolioinformation p
					  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
                      JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
                      LEFT JOIN vtiger_contactdetails cd ON cd.contactid = p.contact_link
                      WHERE account_number IN ({$questions})
                      AND e.deleted = 0 AND (p.accountclosed = 0 OR p.accountclosed IS NULL)";

            $result = $adb->pquery($query, array($account_numbers));

            if($adb->num_rows($result) > 0)
                foreach($result AS $k => $v){
                    $v['record'] = $v['crmid'];
                    $tmp = new nExpense($v['account_number']);
//                    $v['management_fee'] = abs($tmp->CalculateAmount('DATE_SUB(NOW(),INTERVAL 1 YEAR)', 'NOW()'));
                    $summary_info[] = $v;

                    $totals["total_value"] += $v['total_value'];
                    $totals["market_value"] += $v['securities'];
                    $totals["cash_value"] += $v['cash'];
                    $totals['management_fee'] += $v['management_fee'];
                    $totals['money_market_value'] += $v['money_market_funds'];
                    $live_accounts[] = $v['account_number'];
            }
        }
/*////////THIS WAY DOES NOT SHOW $0 accounts
        $pdfAccess = new cPDFDBAccess();
        $accts = $pdfAccess->ReadOtherAccountsWithTotals($account_numbers, "AND t1.account_number = t1.primary_account");
        $comparison = array();
        if(!empty($accts))
        foreach($accts AS $k => $v){
            $query = "SELECT portfolioinformationid FROM vtiger_portfolioinformation p
                      JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
                      WHERE account_number=?
                      AND e.deleted = 0 AND p.accountclosed = 0";
            $result = $adb->pquery($query, array($v['account_number']));
            if($adb->num_rows($result) > 0) {
                $v['record'] = $adb->query_result($result, 0, "portfolioinformationid");
                $tmp = new nExpense($v['account_number']);
                $v['management_fee'] = abs($tmp->CalculateAmount('DATE_SUB(NOW(),INTERVAL 1 YEAR)', 'NOW()'));
                $summary_info[] = $v;

                $totals["total_value"] = $v['total_value_sum'];
                $totals["market_value"] = $v['market_value_sum'];
                $totals["cash_value"] = $v['cash_value_sum'];
                $totals['management_fee'] += $v['management_fee'];
                $comparison[] = $v['account_number'];
            }
        }
*/////////////////////
/*        foreach($accts AS $k => $v){
            $query = "SELECT portfolioinformationid FROM vtiger_portfolioinformation WHERE account_number=?";
            $result = $adb->pquery($query, array($v['account_number']));
            $v['record'] = $adb->query_result($result, 0, "portfolioinformationid");
            $tmp = new nExpense($v['account_number']);
            $v['management_fee'] = abs($tmp->CalculateAmount('DATE_SUB(NOW(),INTERVAL 1 YEAR)', 'NOW()'));
            $summary_info[] = $v;

            $totals["total_value"] = $v['total_value_sum'];
            $totals["market_value"] = $v['market_value_sum'];
            $totals["cash_value"] = $v['cash_value_sum'];
            $totals['management_fee'] += $v['management_fee'];
            $comparison[] = $v['account_number'];
        }*/
$return_accounts = $live_accounts;#$account_numbers;
        if(sizeof($return_accounts) == 0)
            $viewer->assign("HIDE_WIDGET", 1);
        else
            $viewer->assign("HIDE_WIDGET", 0);

        $viewer->assign("ACCOUNT_NUMBER", $acct);
        $viewer->assign("HOUSEHOLD_ACCOUNT", $id);
        $viewer->assign("MODULE", "Portfolios");
        $viewer->assign("ACTION", "household_summary");
        $viewer->assign("TOTALS", $categories);
        $viewer->assign("GRANDTOTALS", $totals);
        $viewer->assign("USER_ID", $current_user->id);
        $viewer->assign("SUMMARY_INFO", $summary_info);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $viewer->assign("HIDE_LINKS", $request->get('hide_links'));
        $viewer->assign("CALLING_MODULE", $request->get('calling_module') ? $request->get('calling_module') : "Accounts");
        $viewer->assign("RECORD", $request->get('calling_record'));

        $output = $viewer->view('ReportTopNavigation.tpl', "PortfolioInformation", true);
        return $output;
    }

    public function getHeaderScripts(Vtiger_Request $request) {
            $moduleName = $request->getModule();
            $jsFileNames = array(
				"~libraries/jquery/handsontable/jQuery-contextMenu/jquery.contextMenu.js",
				"~libraries/jquery/handsontable/jQuery-contextMenu/jquery.ui.position.js",
				"modules.$moduleName.resources.PortfolioList", // . = delimiter
				"modules.PortfolioInformation.resources.topReportNavigation", // . = delimiter
            );
            $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
            return $jsScriptInstances;
    }    
}

?>
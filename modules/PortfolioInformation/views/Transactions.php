<?php

class PortfolioInformation_Transactions_View extends Vtiger_BasicAjax_View{
    
    function process(Vtiger_Request $request) {
        if(strlen($request->get("account_number")) > 0){
            $transactions = new PortfolioInformation_Transactions_Model();

            $transactions->GenerateReport($request);
            
            $viewer = $this->getViewer($request);
            
            $viewer->assign("TRANSACTIONINFO", $transactions->transaction_info);
            $viewer->assign("DATE", $transactions->date);
            $viewer->assign("ACCOUNT_NUMBER", $transactions->account_number);
            $viewer->assign("ACCT_DETAILS", $transactions->account_info);

            $viewer->assign("ORDER", $transactions->orderby);
            $viewer->assign("DIRECTION", $transactions->direction);
            $viewer->assign("NUMPAGES", $transactions->num_pages);
            $viewer->assign("NEXTPAGE", $transactions->next_page);
            $viewer->assign("PREVPAGE", $transactions->prev_page);
            $viewer->assign("CURRENTPAGE", $transactions->current_page);
            $viewer->assign("SEARCHCONTENT", $transactions->searchcontent);
            $viewer->assign("SEARCHTYPE", $transactions->searchtype);

            $viewer->assign("SEARCHLIST", $transactions->search_list);
            $viewer->assign("NUMRESULTS", $transactions->pagination);

            $viewer->assign("SYMBOLS", $transactions->symbols);
            $viewer->assign("SECURITY_TYPES", $transactions->security_types);
            $viewer->assign("DESCRIPTIONS", $transactions->descriptions);
            $viewer->assign("ACTIONS", $transactions->actions);
            $viewer->assign("FILTER", $transactions->filter);

            $viewer->assign("ACTIONS_CHECKED", $transactions->actions_checked);
            $viewer->assign("SYMBOLS_CHECKED", $transactions->symbols_checked);
            $viewer->assign("DESCRIPTIONS_CHECKED", $transactions->descriptions_checked);
            $viewer->assign("SECURITY_TYPES_CHECKED", $transactions->security_types_checked);
            $viewer->assign("DATE_RANGE_CHECKED", $transactions->date_range_checked);

            $viewer->assign("transaction_filter_actions_value_carried", $transactions->transaction_filter_actions_value_carried);
            $viewer->assign("transaction_filter_symbols_value_carried", $transactions->transaction_filter_symbols_value_carried);
            $viewer->assign("transaction_filter_descriptions_value_carried", $transactions->transaction_filter_descriptions_value_carried);
            $viewer->assign("transaction_filter_security_types_value_carried", $transactions->transaction_filter_security_types_value_carried);
            $viewer->assign("transaction_filter_symbols_value", $transactions->symbol_values);
            $viewer->assign("transaction_filter_date_range_start_carried", $transactions->transaction_filter_date_range_start_carried);
            $viewer->assign("transaction_filter_date_range_end_carried", $transactions->transaction_filter_date_range_end_carried);
            
            $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
            
            $output = $viewer->view('Transactions.tpl', "PortfolioInformation", true);
            return $output;
        } else
            return "<div class='ReportBottom'></div>";
    }
    
    public function getHeaderScripts(Vtiger_Request $request) {
            $moduleName = $request->getModule();
            $jsFileNames = array(
                "modules.PortfolioInformation.resources.transactions",
            );
            $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
            return $jsScriptInstances;
    }
    
}

?>
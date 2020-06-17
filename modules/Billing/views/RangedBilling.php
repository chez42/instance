<?php

require_once("modules/Billing/models/Billing.php");
require_once("modules/Billing/models/CapitalFlows.php");
require_once("libraries/Reporting/ReportCommonFunctions.php");

class Billing_RangedBilling_View extends Vtiger_Index_View {
    // We are overriding the default SideBar UI to list our feeds.
    public function preProcess(Vtiger_Request $request, $display = true) {
        return parent::preProcess($request, $display);
    }

    public function Process(Vtiger_Request $request){
        /**
         * MAKING A BILLING REPORT:
         * 1) Create a new model for the billing type
         * 2) Create a "GENERATE" function of some sort that takes in the account numbers, determined title of the report
         * 3) Module type can be used for logical purposes in template files.  If in accounts, show like this, if in portfolios, show like this
         * 4) The Billing class is the container that holds the arrays of portfolios, contacts, households, and list of accounts
         * 5) The Billing parent class holds the global information such as the report title, who's generating it, start period, end period, start value, end value, etc
         * 6) If you want INDIVIDUAL values, that must be done in the individual array elements
         * 7) See the RangedBilling.php model for how to use individual portfolios vs combined
         */
        $account_numbers = $request->get('account_numbers');
        $account_numbers = explode(",", $account_numbers);
        $start = GetDateMinusMonths(3);
#        $end = "2020-02-14";
        $rangedBilling = new Billing_RangedBilling_Model();
        $rangedBilling->GenerateIndividualData($account_numbers, "RANGED REPORTING", "Ryan Testing Client Name", "Portfolios", $start, $end);
        $rangedBilling->GenerateCombinedData($account_numbers, "RANGED REPORT", "Ryan Testing Client Name", "Portfolios", $start, $end);
        $billing = $rangedBilling->GetBillingObject();
        $combined = $rangedBilling->GetCombinedObject();

#        $capitalFlow = new Billing_CapitalFlows_Model($account_numbers);

        $viewer = $this->getViewer($request);
#        $viewer->assign("TITLE", "RANGED REPORT TITLE");
#        $viewer->assign("CLIENT_NAME", "RYAN TESTING CLIENT NAME");
        $viewer->assign("MODULE_NAME", "Portfolios");
        $viewer->assign("BILLING", $billing);
        $viewer->assign("COMBINED", $combined);
        $viewer->assign("INCEPTION_DATE", "02/03/2014");
        $viewer->assign("CSS", $this->getHeaderCss($request));
        $viewer->view('RangedBilling.tpl', 'Billing');
    }

    // Injecting custom javascript resources
    public function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();
        $jsFileNames = array(
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
            '~/layouts/v7/modules/Billing/css/Billing.css',
            '~/layouts/v7/modules/Billing/css/RangedBilling.css',
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);
        return $headerCssInstances;
    }
}


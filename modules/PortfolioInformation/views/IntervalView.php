<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2017-06-22
 * Time: 3:40 PM
 */

class PortfolioInformation_IntervalView_View extends Vtiger_BasicAjax_View{

    function process(Vtiger_Request $request) {
        $module = $request->get('calling_module');
        $calling_record = $request->get('calling_record');
        $account_numbers = $request->get('account_numbers');
//        if($module == "PortfolioInformation") {
            $accounts = explode(",", $account_numbers);
            $accounts = PortfolioInformation_Module_Model::ReturnValidAccountsFromArray($accounts);

            PortfolioInformation_Module_Model::CalculateMonthlyIntervalsForAccounts($accounts);
            $intervals = PortfolioInformation_Module_Model::GetIntervalsForAccounts($accounts);
            $viewer = $this->getViewer($request);

            $viewer->assign('INTERVALS', $intervals);
            $viewer->assign("ACCOUNT_NUMBERS", implode(",", $accounts));
            $viewer->assign('SCRIPTS', self::getHeaderScripts($request));
            $viewer->assign('STYLES', self::getHeaderCss($request));
            $viewer->assign("SOURCE_RECORD", $calling_record);
            $viewer->assign("SOURCE_MODULE", $module);
            $viewer->view('IntervalView.tpl', "PortfolioInformation");
//        }
/*            if($module == "Contacts" || $module == "Accounts"){
                $i = Vtiger_Record_Model::getInstanceById($calling_record);
                if($module == "Accounts") {
                    include_once("include/utils/omniscientCustom.php");
                    $ssn = GetSSNsForHousehold($calling_record);
                }
                else
                    $ssn[] = str_replace('-', '', $i->get('ssn'));
                if(!$ssn)
                    return;

                $accounts = PortfolioInformation_Module_Model::GetAccountNumbersFromSSN($ssn);
                PositionInformation_Module_Model::UndeleteAllPositionsForAccounts($accounts);
                $positions = PositionInformation_Module_Model::GetPositionsAndCalculateDynamic($accounts);
                $viewer = $this->getViewer($request);

                $viewer->assign('POSITIONS', $positions);
                $viewer->assign('SCRIPTS', self::getHeaderScripts($request));
                $viewer->assign('STYLES', self::getHeaderCss($request));
                $viewer->assign("CALLING_RECORD", $calling_record);
                $viewer->assign("ACCOUNT", json_encode($accounts));
                $viewer->assign("SOURCE_MODULE", $module);
                $viewer->assign("SOURCE_RECORD", $calling_record);
                $viewer->view('PositionsWidget.tpl', "PortfolioInformation");
            }*/
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            /* "~/libraries/amcharts/amstockchart_3/amcharts/amcharts.js",
            "~/libraries/amcharts/amstockchart_3/amcharts/serial.js",
            "~/libraries/amcharts/amstockchart_3/amcharts/themes/light.js",
            "~/libraries/amcharts/amstockchart_3/amcharts/amstock.js",
            "~/libraries/amcharts/amstockchart_3/amcharts/plugins/dataloader/dataloader.js",
            "~/libraries/amcharts/amstockchart_3/amcharts/plugins/export/export.min.js", */
            "~/libraries/amcharts/amstockchart/amcharts.js",
            "~/libraries/amcharts/amstockchart/serial.js",
            "~/libraries/amcharts/amstockchart/themes/light.js",
            "~/libraries/amcharts/amstockchart/amstock.js",
            "~/libraries/amcharts/amstockchart/plugins/dataloader/dataloader.js",
            "~/libraries/amcharts/amstockchart/plugins/export/export.min.js",
            "modules.PortfolioInformation.resources.Intervals", // . = delimiter
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
		     "~/libraries/amcharts/amstockchart/plugins/export/export.css",
#            "~/libraries/amcharts/amstockchart_3/amcharts/plugins/export/export.css",
#            '~/layouts/vlayout/modules/PortfolioInformation/css/PositionsWidget.css',
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        return $cssInstances;
    }

}

?>
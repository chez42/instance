<?php
/*+***********************************************************************************
 * The Index settings page for users to select which indexes they want to show up in their reports
 *************************************************************************************/

include_once "libraries/Reporting/ReportCommonFunctions.php";

class PortfolioInformation_Downloader_View extends Vtiger_Index_View {
    function preProcessTplName(Vtiger_Request $request) {
        return 'PortfolioReportsPerProcess.tpl';
    }

    public function postProcess(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $viewer = $this->getViewer($request);
        $viewer->view('PortfolioReportsPostProcess.tpl', $moduleName);

        parent::postProcess($request);
    }

    public function process(Vtiger_Request $request) {
        $downloader = new PortfolioInformation_Downloader_Model();
        $sdate = GetDateMinusDays(30);
        $edate = date("Y-m-d");

        $dates = $downloader->GetDatePeriods($sdate, $edate);
        $rep_codes = $downloader->GetAllRepCodes();
        $history = $downloader->GetRepCodeHistory('all', $sdate, $edate);

        $viewer = $this->getViewer($request);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $viewer->assign('STYLES', self::getHeaderCss($request));
        $viewer->assign("DATES", $dates);
        $viewer->assign("REP_CODES", $rep_codes);
        $viewer->assign("HISTORY", $history);
        $screen_content = $viewer->fetch('layouts/v7/modules/PortfolioInformation/Downloader.tpl', $request->getModule());
        echo $screen_content;
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();
        $moduleDetailFile = 'modules.'.$moduleName.'.resources.PreferenceDetail';
        unset($headerScriptInstances[$moduleDetailFile]);

        $jsFileNames = array(
//            '~libraries/jquery/Drop-Down-Combo-Tree/comboTreePlugin.js',
            '~layouts/v7/modules/PortfolioInformation/resources/Downloader.js',
//            '~layouts/v7/modules/PortfolioInformation/resources/icontains.js',
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
            '~/layouts/v7/modules/PortfolioInformation/css/Downloader.css',
//            '~libraries/jquery/Drop-Down-Combo-Tree/style.css',
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);
        return $headerCssInstances;
    }


}

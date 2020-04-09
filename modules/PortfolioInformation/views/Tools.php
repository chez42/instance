<?php
/*+***********************************************************************************
 * The Index settings page for users to select which indexes they want to show up in their reports
 *************************************************************************************/

include_once "libraries/Reporting/ReportCommonFunctions.php";

class PortfolioInformation_Tools_View extends Vtiger_Index_View {
    /*    function preProcessTplName(Vtiger_Request $request) {
            return 'PortfolioReportsPerProcess.tpl';
        }*/

    public function postProcess(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $viewer = $this->getViewer($request);
        $viewer->view('PortfolioReportsPostProcess.tpl', $moduleName);

        parent::postProcess($request);
    }

    public function process(Vtiger_Request $request) {
/*        $downloader = new PortfolioInformation_Downloader_Model();
        $sdate = GetDateMinusDays(30);
        $edate = date("Y-m-d");

        $dates = $downloader->GetDatePeriods($sdate, $edate);
        $rep_codes = $downloader->GetAllRepCodes();
        $history = $downloader->GetRepCodeHistory('all', $sdate, $edate);
*/

#        $extensions = PortfolioInformation_Tools_Model::GetExtensionsFromType("Portfolios");
#        $missing = PortfolioInformation_Tools_Model::GetMissingFiles($extensions, '2020-02-21', '2020-02-22');
#        print_r($missing);exit;

#        $file_info = PortfolioInformation_Tools_Model::GetFileInfoFromTypeAndDates("positions", '2020-02-01', '2020-02-31');
#        print_r($file_info);exit;
#print_r($files);
#exit;
        $rep_codes = PortfolioInformation_Module_Model::GetRepCodeList();
//        $calendar_rep_codes = PortfolioInformation_Tools_Model::GetCalendarRepCodesFromDates('2020-02-01', '2020-03-31');

        $viewer = $this->getViewer($request);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $viewer->assign('STYLES', self::getHeaderCss($request));
        $viewer->assign("REP_CODES", $rep_codes);
//        $viewer->assign("CALENDAR_REP_CODES", json_encode($calendar_rep_codes));
//        $viewer->assign("FILE_INFO", json_encode($file_info));
        $viewer->assign("LOADER", "layouts/v7/modules/PortfolioInformation/images/Loader.gif");
        $screen_content = $viewer->fetch('layouts/v7/modules/PortfolioInformation/Tools.tpl', $request->getModule());
        echo $screen_content;
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();
        $moduleDetailFile = 'modules.'.$moduleName.'.resources.PreferenceDetail';
        unset($headerScriptInstances[$moduleDetailFile]);

        $jsFileNames = array(
//            '~libraries/jquery/Drop-Down-Combo-Tree/comboTreePlugin.js',
            "~layouts/v7/lib/jquery/fullcalendar/fullcalendar.js",
            '~layouts/v7/modules/PortfolioInformation/resources/Tools.js',
            "~/libraries/shield/shieldui-all.min.js",
//            '~layouts/v7/modules/PortfolioInformation/resources/icontains.js',
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
            '~layouts/'.Vtiger_Viewer::getDefaultLayoutName().'/lib/jquery/fullcalendar/fullcalendar.css',
            '~layouts/'.Vtiger_Viewer::getDefaultLayoutName().'/lib/jquery/fullcalendar/fullcalendar-bootstrap.css',
            '~/layouts/v7/modules/PortfolioInformation/css/Tools.css',
//            '~libraries/jquery/Drop-Down-Combo-Tree/style.css',
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);
        return $headerCssInstances;
    }


}
<?php
/*+***********************************************************************************
 * The Index settings page for users to select which indexes they want to show up in their reports
 *************************************************************************************/
class PortfolioInformation_Indexes_View extends Vtiger_Index_View {
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
#        $list = PortfolioInformation_Indexes_Model::GetIndexList();
        $list = PortfolioInformation_Indexes_Model::GetIndexListFiltered("base_asset_class");
        $capitalization = PortfolioInformation_Indexes_Model::GetCapitalizationList();
        $style = PortfolioInformation_Indexes_Model::GetStyleList();
        $international = PortfolioInformation_Indexes_Model::GetInternationalList();
        $sector = PortfolioInformation_Indexes_Model::GetSectorList();
        $aclass = PortfolioInformation_Indexes_Model::GetBaseAssetClassList();
        $preferences = PortfolioInformation_Indexes_Model::GetIndexPreferences();

        $viewer = $this->getViewer($request);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $viewer->assign('STYLES', self::getHeaderCss($request));
        $viewer->assign("LIST", $list);
        $viewer->assign("CAPITALIZATION", json_encode($capitalization));
        $viewer->assign("STYLE", json_encode($style));
        $viewer->assign("INTERNATIONAL", json_encode($international));
        $viewer->assign("SECTOR", json_encode($sector));
        $viewer->assign("ACLASS", json_encode($aclass));
        $viewer->assign("PREFERENCES", json_encode($preferences));
//        $viewer->fetch('layouts/v7/modules/Users/views/Indexes.tpl', $request->getModule());
        $screen_content = $viewer->fetch('layouts/v7/modules/PortfolioInformation/Indexes.tpl', $request->getModule());
        echo $screen_content;
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();
        $moduleDetailFile = 'modules.'.$moduleName.'.resources.PreferenceDetail';
        unset($headerScriptInstances[$moduleDetailFile]);

        $jsFileNames = array(
            '~libraries/jquery/Drop-Down-Combo-Tree/comboTreePlugin.js',
            '~layouts/v7/modules/PortfolioInformation/resources/Indexes.js',
//            '~layouts/v7/modules/PortfolioInformation/resources/icontains.js',
            "~/libraries/jquery/DataTables/datatables.js",
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
            '~/layouts/v7/modules/PortfolioInformation/css/Indexes.css',
            '~libraries/jquery/Drop-Down-Combo-Tree/style.css',
            "~/libraries/jquery/DataTables/datatables.css",
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);
        return $headerCssInstances;
    }


}

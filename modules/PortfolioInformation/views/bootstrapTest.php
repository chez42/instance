<?php
if (ob_get_level() == 0) ob_start();
ob_implicit_flush(true);
ob_end_flush();

/**
 * Created by PhpStorm.
 * User: rsandnes
 * Date: 2016-07-06
 * Time: 3:55 PM
 */

class PortfolioInformation_bootstrapTest_View extends Vtiger_BasicAjax_View
{

    function process(Vtiger_Request $request){
        $viewer = $this->getViewer($request);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $viewer->assign('STYLES', self::getHeaderCss($request));
        $viewer->display('layouts/v7/modules/PortfolioInformation/bootstrapTest.tpl', "PortfolioInformation");
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "~/libraries/bootstrap5/js/bootstrap.bundle.js",
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
            '~/libraries/bootstrap5/css/bootstrap.css',
            '~/layouts/v7/modules/PortfolioInformation/css/bootstrapTest.css',
            '~/layouts/v7/modules/Omni/commonCSS/common.css',
            '~/layouts/v7/modules/Omni/commonCSS/bootstrapCustom.css'
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);
        return $headerCssInstances;
    }
}
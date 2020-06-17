<?php


/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2017-05-24
 * Time: 3:30 PM
 */

class PortfolioInformation_TableEditor_View extends Vtiger_Index_View
{
    function process(Vtiger_Request $request)
    {
        $viewer = $this->getViewer($request);
        $viewer->assign("LOCATIONS", $locations);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));

        $viewer->view('Sandbox.tpl', "PortfolioInformation", false);
    }

    public function getHeaderScripts(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "~/libraries/handsontable/dist/handsontable.full.js",
            "modules.PortfolioInformation.resources.TableEditor", // . = delimiter
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
            '~/libraries/handsontable/dist/handsontable.full.css'
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);
        return $headerCssInstances;
    }

}
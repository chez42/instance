<?php

class ReportSettings_Settings_View extends Vtiger_BasicAjax_View{
    function process(Vtiger_Request $request) {
        $settings_model = new ReportSettings_Settings_Model();
        $settings = $settings_model->settings;
        $logo_list = $settings_model->logo_list;
        
        $viewer = $this->getViewer($request);
        $viewer->assign("SETTINGS", $settings);
        $viewer->assign("ACCOUNT_NUMBER", $request->get('account_number'));
        $viewer->assign("LOGOS", $logo_list);
        $viewer->assign("STYLES", $this->getHeaderCss($request));
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        echo $viewer->view('Settings.tpl', 'ReportSettings', true);
    }
    
    // Injecting custom javascript resources
    public function getHeaderScripts(Vtiger_Request $request) {
            $headerScriptInstances = parent::getHeaderScripts($request);
            $moduleName = $request->getModule();
            $jsFileNames = array(
                "modules.ReportSettings.resources.Settings", // . = delimiter
                "~/libraries/uploader/js/vendor/jquery.ui.widget.js",
                "~/libraries/uploader/js/load-image.min.js",
                "~/libraries/uploader/js/jquery.iframe-transport.js",
                "~/libraries/uploader/js/jquery.fileupload.js",
                "~/libraries/uploader/js/jquery.fileupload-process.js",
                "~/libraries/uploader/js/jquery.fileupload-image.js",
                "~/libraries/uploader/js/jquery.fileupload-audio.js",
                "~/libraries/uploader/js/jquery.fileupload-video.js",
                "~/libraries/uploader/js/jquery.fileupload-validate.js",
            );
            $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
            return $jsScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request) {
            $headerCssInstances = parent::getHeaderCss($request);
            $cssFileNames = array(
                '~/libraries/uploader/css/jquery.fileupload.css',
            );
            $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
            return $cssInstances;
    }
}

?>

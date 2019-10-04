<?php

class PortfolioInformation_TransactionsNavigation_View extends Vtiger_BasicAjax_View{
    public function process(Vtiger_Request $request) {
        $transactions_model = new PortfolioInformation_TransactionsNavigation_Model();
        
        $viewer = $this->getViewer($request);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $viewer->assign("STYLES", $this->getHeaderCss($request));
        $viewer->assign("ACCOUNTINFO", $request->get("account_numbers"));
        $viewer->assign("ACTIVITIES", $transactions_model->GetAllActivities($request));
        $viewer->assign("SECURITY_TYPES", $transactions_model->GetAllSecurityTypes($request));
        $viewer->assign("DATES", $transactions_model->GetTradeDates($request));
        $viewer->assign("SYMBOLS", $transactions_model->GetAllSymbols($request));
         
        $viewer->assign("USER_MODEL", Users_Record_Model::getCurrentUserModel());
         
//        $viewer->assign("STYLES", $this->getHeaderCss($request));
        $output = $viewer->view('TransactionsNavigation.tpl', "PortfolioInformation", true);
        echo $output;
    }
    
    public function getHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "modules.$moduleName.resources.TransactionsNavigation",
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }
    
    public function getHeaderCss(Vtiger_Request $request) {
            $headerCssInstances = parent::getHeaderCss($request);
            $cssFileNames = array(
                '~/layouts/vlayout/modules/PortfolioInformation/css/TransactionsNavigation.css',
            );
            $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
            return $cssInstances;
    }
}
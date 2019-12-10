<?php
/*+***********************************************************************************
 * The Index settings page for users to select which indexes they want to show up in their reports
 *************************************************************************************/
class PortfolioInformation_Statements_View extends Vtiger_Index_View {
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
        $user = Users_Record_Model::getCurrentUserModel();

#        $groups = $user->getRelatedGroupsInformation();//Array consisting of Name, ID, Description.  IE: $groups[]=array("name" => "Ryan", "ID" => "2233", "Description" => "Hello")
        $groups = $user->getAccessibleGroups();
        $users = $user->getAccessibleUsers();//key value of id/name

        $statement = new PortfolioInformation_Statements_Model();

        $prepared_by = $statement->GetPreparedByData($user->get('id'));
        $formatted_prepared = htmlspecialchars_decode($prepared_by);

        $viewer = $this->getViewer($request);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $viewer->assign('STYLES', self::getHeaderCss($request));
        $viewer->assign('GROUPS', $groups);
        $viewer->assign('USERS', $users);
        $viewer->assign('USER', $user);
        $viewer->assign("PREPARED_BY", $prepared_by);
        $viewer->assign("FORMATTED_PREPARED_BY", $formatted_prepared);

        $screen_content = $viewer->fetch('layouts/v7/modules/PortfolioInformation/Statements.tpl', $request->getModule());
        echo $screen_content;
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();
        $moduleDetailFile = 'modules.'.$moduleName.'.resources.PreferenceDetail';
        unset($headerScriptInstances[$moduleDetailFile]);

        $jsFileNames = array(
            '~layouts/v7/modules/PortfolioInformation/resources/Statements.js',
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
            '~/layouts/v7/modules/PortfolioInformation/css/Statements.css',
            '~libraries/jquery/Drop-Down-Combo-Tree/style.css',
            "~/libraries/jquery/DataTables/datatables.css",
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);
        return $headerCssInstances;
    }


}

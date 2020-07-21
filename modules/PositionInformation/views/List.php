<?php
class PositionInformation_List_View extends Vtiger_List_View {
	/**
	 * Calculates the global summary for the list view
	 * @global type $current_user
	 * @param Vtiger_Request $request
	 * @param type $display
	 * @return type
	 */
	public function preProcess(Vtiger_Request $request, $display = true) {
		$currentUserModel = Users_Record_Model::getCurrentUserModel();

		return parent::preProcess($request, $display);
	}

	public function initializeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer) {
		parent::initializeListViewContents($request, $viewer);
	}
	
	public function process(Vtiger_Request $request) {

		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		
		$viewer = $this->getViewer($request);

		$this->viewName = $request->get('viewname');

		$this->initializeListViewContents($request, $viewer);
		
		$headers = $this->listViewHeaders;
		
		$entries = $this->listViewEntries;
		
		$this->assignCustomViews($request,$viewer);//change  For Show List View 11-Jul-2018
		
		$moduleName = $request->getModule();
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);

		$viewer->assign('VIEW', $request->get('view'));
		$viewer->assign('MODULE_MODEL', $moduleModel);
		$viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->view('ListViewContents.tpl', $moduleName);
	}

	public function postProcess(Vtiger_Request $request) {

		parent::postProcess($request);

	}

    /**
     * Function to get the list of Script models to be included
     * @param Vtiger_Request $request
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();

        $jsFileNames = array(
            "~layouts/v7/modules/ModSecurities/resources/ListViewRightClickPricing.js"
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
}
?>
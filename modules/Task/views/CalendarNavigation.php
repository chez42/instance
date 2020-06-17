<?php
class Task_CalendarNavigation_View extends Vtiger_Index_View {

	public function process(Vtiger_Request $request) {
		$viewer = $this->getViewer($request);

		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$viewer->assign('CURRENT_USER_MODEL', $currentUserModel);

		$viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $viewer->view('CalendarNavigation.tpl', $request->getModule());
	}
	public function getHeaderScripts(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$jsFileNames = array(
			"modules.$moduleName.resources.CalendarNavigation",
		);
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		return $jsScriptInstances;
    }
}
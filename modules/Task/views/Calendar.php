<?php

class Task_Calendar_View extends Vtiger_Index_View {

	public function preProcess(Vtiger_Request $request, $display = true) {
		
		$viewer = $this->getViewer($request);
		
		$viewer->assign('MODULE_NAME', $request->getModule());

		if(isset($_SESSION['task_user_login'])){
			$viewer->assign('USER_LOGIN_COUNT', $_SESSION['task_user_login']);
			unset($_SESSION['task_user_login']);		
		}
		parent::preProcess($request, false);
		
		if($display) {
			$this->preProcessDisplay($request);
		}
		
	}

	protected function preProcessTplName(Vtiger_Request $request) {
		return 'CalendarViewPreProcess.tpl';
	}

	public function getHeaderScripts(Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$jsFileNames = array(
			"modules.Task.resources.CalendarView",
			"~/libraries/fullcalendar/fullcalendar.js",
			"~/libraries/jquery/colorpicker/js/colorpicker.js"
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

	public function getHeaderCss(Vtiger_Request $request) {
		$headerCssInstances = parent::getHeaderCss($request);


		$cssFileNames = array(
			'~/libraries/fullcalendar/fullcalendar.css',
			'~/libraries/fullcalendar/fullcalendar-bootstrap.css',
			'~/libraries/jquery/colorpicker/css/colorpicker.css'
		);
		$cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
		$headerCssInstances = array_merge($headerCssInstances, $cssInstances);

		return $headerCssInstances;
	}

	public function process(Vtiger_Request $request) {
		
		$viewer = $this->getViewer($request);
		
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		
		$viewer->assign('CURRENT_USER', $currentUserModel);

		$viewer->view('CalendarView.tpl', $request->getModule());
	}
}
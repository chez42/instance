<?php

class PositionInformation_SecurityClasses_Dashboard extends Vtiger_IndexAjax_View {

	public function process(Vtiger_Request $request) {
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();

		$linkId = $request->get('linkid');
		$page = $request->get('page');
		if(empty($page)) {
			$page = 1;
		}
		$pagingModel = new Vtiger_Paging_Model();
		$pagingModel->set('page', $page);

		$chartModel = new PositionInformation_Chart_Model();
		
		$data = $chartModel->getSecurityClassesChartData();

		$widget = Vtiger_Widget_Model::getInstance($linkId, $currentUser->getId());

		$viewer->assign('WIDGET', $widget);
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign("GRAND_TOTAL", $data['grand_total']);
        $viewer->assign('DATA', $data['chartData']);
        
		$viewer->assign("CHART_TYPE", 'position_security_classes');
		
		$content = $request->get('content');
		if(!empty($content)) {
			$viewer->view('dashboards/SecurityClassesContent.tpl', $moduleName);
		} else {
			$viewer->view('dashboards/SecurityClasses.tpl', $moduleName);
		}
	}
}

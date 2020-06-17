<?php
/************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L.  --  This file is a part of vtiger CRM TimeControl extension.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 * *********************************************************************************** */
global $root_directory;
require_once($root_directory."/modules/Timecontrol/autoload_wf.php");


class Timecontrol_Detail_View extends Vtiger_Detail_View {

    function preProcess(Vtiger_Request $request, $display=true) {
		 $moduleModel = Vtiger_Module_Model::getInstance("Timecontrol");

           $className = "\\TimeControl\\S"."WE"."xt"."ension\\cd10"."7ad732d2304"."f9118bc9f6892f"."1110643e5469";
           $as2df = new $className("Timecontrol", $moduleModel->version);

           if(!$as2df->g814b18adf5aa857e83e72e05669060e9b72dd078()) {
               header('Location:index.php?module=Timecontrol&view=LicenseManager&parent=Settings');
               exit();
           }

   		parent::preProcess($request, $display);
   	}

	public function process(Vtiger_Request $request) {
		global $current_user;
		$viewer = $this->getViewer($request);

		$recordId = $request->get('record');
		$moduleName = $request->getModule();

		if(!$this->record){
			$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}
		
        /**
         * @var $recordModel \Vtiger_Record_Model
         */
        $recordModel = $this->record->getRecord();
//
//        $datefield = new DateTimeField(null);
//        $date = $datefield->convertToUserTimeZone($recordModel->get('date_start').' '.$recordModel->get('time_start'));
//
//        $recordModel->set('date_start', $date->format('Y-m-d'));
//        $recordModel->set('time_start', $date->format('H:i:s'));

		if ($recordModel->get('timecontrolstatus')=='run') {
		  $date = $recordModel->get('date_start');
		  $time = $recordModel->get('time_start');
		  list($year, $month, $day) = explode('-', $date);
		  list($hour, $minute) = explode(':', $time);

		  $starttime = mktime($hour, $minute, 0, $month, $day, $year);
		  // las sgtes líneas deberían bastar para calcular el tiempo en función de la zona horario del usuario
//
//            $nowtime = time();
//            $now = date('Y-m-d H:i:s');

            $datetimefield = new DateTimeField('');
            $nowDate = $datetimefield->convertToUserTimeZone(date('Y-m-d H:i:s'));

		  $counter = strtotime($nowDate->format('Y-m-d H:i:s'))-$starttime;
		  $viewer->assign('SHOW_WATCH', 'started');
		  $viewer->assign('WATCH_COUNTER', $counter);
		} else {
		  $viewer->assign('SHOW_WATCH', 'halted');
		  $viewer->assign('WATCH_DISPLAY', $recordModel->get('totaltime'));
		}
		parent::process($request);
	}

    function getHeaderCss(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderCss($request);
        $moduleName = $request->getModule();

        $cssFileNames = array(
            "http://fonts.googleapis.com/css?family=Source+Code+Pro"
        );

        $cssScriptInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerStyleInstances = array_merge($headerScriptInstances, $cssScriptInstances);
        return $headerStyleInstances;
    }

}

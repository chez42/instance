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

class Timecontrol_Save_Action extends Vtiger_Save_Action {

	public function process(Vtiger_Request $request) {

		if ($request->get('date_end')=='' || $request->get('time_end')=='') {
			$request->set('date_end','');
			$request->set('time_end','');
		}

//        $datefield = new DateTimeField(null);
//        $date = $datefield->convertToDBTimeZone($request->get('date_start').' '.$request->get('time_start'));
//
//        $request->set('date_start', $date->format('Y-m-d'));
//        $request->set('time_start', $date->format('H:i:s'));

		parent::process($request);
	}
}

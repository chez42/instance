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

class Timecontrol_Finish_Action extends Vtiger_Action_Controller {

    public function checkPermission(Vtiger_Request $request) {
   		$moduleName = $request->getModule();
   		$record = $request->get('record');

   		if(!Users_Privileges_Model::isPermitted($moduleName, 'Save', $record)) {
   			throw new AppException('LBL_PERMISSION_DENIED');
   		}
   	}

	public function process(Vtiger_Request $request) {
        $current_user = Users_Record_Model::getCurrentUserModel();

        $recordId = intval($request->get('record'));

        $record = Vtiger_Record_Model::getInstanceById($recordId, 'Timecontrol');

        $datetimefield = new DateTimeField('');
        $nowDate = $datetimefield->convertToUserTimeZone(date('Y-m-d H:i:s'));
        $finishDateTS = strtotime($nowDate->format('Y-m-d H:i:s'));

        $record->set('date_end', date('Y-m-d', $finishDateTS));
        $record->set('time_end', date('H:i:s', $finishDateTS));
        $record->set('mode', 'edit');

        $record->set('timecontrolstatus', 'finish');
        $record->save();
        
        $response = new Vtiger_Response();
        $response->setResult(true);
        $response->emit();

        //header('Location:index.php?module=Timecontrol&view=Detail&record='. $recordId);
        //header('Location:index.php?module=Timecontrol&view=Edit&record='. $recordId);
       // exit();

	}

    public function validateRequest(Vtiger_Request $request) {
        $request->validateReadAccess();
    }

}
